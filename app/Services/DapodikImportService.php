<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\OrangTua;
use App\Models\RiwayatKelas;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DapodikImportService
{
    protected QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Import siswa dari file Excel (.xlsx / .xls) atau CSV (Dapodik / Template)
     *
     * @param UploadedFile $file
     * @param int|null $defaultKelasId
     * @return array
     */
    public function import(UploadedFile $file, ?int $defaultKelasId = null): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $rows = [];

        try {
            if (in_array($extension, ['csv', 'txt'])) {
                $rows = $this->readCsv($file->getRealPath());
            } elseif (in_array($extension, ['xlsx', 'xls'])) {
                $rows = $this->readExcel($file->getRealPath());
            } else {
                return [
                    'success' => false,
                    'message' => 'Format file tidak didukung! Harap unggah file berformat .csv, .xlsx, atau .xls.',
                    'imported' => 0,
                    'updated' => 0,
                ];
            }
        } catch (\Throwable $e) {
            Log::error("File Read Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal membaca file: ' . $e->getMessage(),
                'imported' => 0,
                'updated' => 0,
            ];
        }

        if (empty($rows) || count($rows) < 2) {
            return [
                'success' => false,
                'message' => 'File kosong atau tidak memiliki baris data siswa yang valid.',
                'imported' => 0,
                'updated' => 0,
            ];
        }

        // Header normalization
        $rawHeaders = array_shift($rows);
        $headerMap = $this->mapHeaders($rawHeaders);

        if (!isset($headerMap['nama']) && !isset($headerMap['nisn'])) {
            return [
                'success' => false,
                'message' => 'Kolom data tidak dikenali. Pastikan terdapat kolom "Nama" atau "NISN" pada baris judul file.',
                'imported' => 0,
                'updated' => 0,
            ];
        }

        $kelases = Kelas::all();
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('n');
        $currentTahunAjaran = ($currentMonth >= 7) 
            ? ($currentYear . '/' . ($currentYear + 1)) 
            : (($currentYear - 1) . '/' . $currentYear);

        $importedCount = 0;
        $updatedCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                // Lewati baris kosong
                if (empty(array_filter($row, fn($v) => trim((string)$v) !== ''))) {
                    continue;
                }

                $getValue = function($key) use ($headerMap, $row) {
                    if (isset($headerMap[$key]) && isset($row[$headerMap[$key]])) {
                        return trim((string)$row[$headerMap[$key]]);
                    }
                    return null;
                };

                $nama = $getValue('nama');
                $nisn = $getValue('nisn');
                $nis = $getValue('nis'); // NIPD atau NIS
                $jkRaw = $getValue('jk');
                $kelasRaw = $getValue('kelas');
                $namaAyah = $getValue('nama_ayah');
                $namaIbu = $getValue('nama_ibu');
                $namaWali = $getValue('nama_wali');
                $hpRaw = $getValue('hp');
                $alamat = $getValue('alamat');

                if (empty($nama) && empty($nisn)) {
                    continue;
                }

                // Normalisasi NISN (hanya angka)
                $nisn = preg_replace('/[^0-9]/', '', (string)$nisn);
                if (empty($nisn)) {
                    $nisn = !empty($nis) ? preg_replace('/[^0-9]/', '', (string)$nis) : '00' . rand(10000000, 99999999);
                }

                // Normalisasi Jenis Kelamin (L atau P)
                $jk = 'L';
                if (!empty($jkRaw)) {
                    $upperJk = strtoupper($jkRaw);
                    if (str_starts_with($upperJk, 'P') || str_contains($upperJk, 'PEREMPUAN') || str_contains($upperJk, 'WANITA')) {
                        $jk = 'P';
                    }
                }

                // Pencocokan Kelas (Rombel)
                $kelasId = $this->resolveKelasId($kelasRaw, $kelases, $defaultKelasId);

                // Normalisasi No WhatsApp (HP)
                $noWa = $this->cleanPhoneNumber($hpRaw);

                // Buat atau Cari Akun Orang Tua
                $orangTua = null;
                if (!empty($noWa)) {
                    $orangTua = OrangTua::where('no_wa', $noWa)->first();
                }

                if (!$orangTua) {
                    $namaOrtu = $namaAyah ?: ($namaIbu ?: ($namaWali ?: 'Orang Tua ' . $nama));
                    $orangTua = OrangTua::create([
                        'nama_ayah' => $namaAyah ?: $namaOrtu,
                        'nama_ibu' => $namaIbu,
                        'nama_wali' => $namaWali,
                        'hubungan_wali' => $namaWali ? 'Wali' : 'Orang Tua Kandung',
                        'no_wa' => $noWa ?: '08' . rand(1000000000, 9999999999),
                        'alamat' => $alamat ?: 'Siswa SMP IT Al-Muttaqin',
                    ]);
                } else {
                    if (!empty($namaAyah) && empty($orangTua->nama_ayah)) $orangTua->nama_ayah = $namaAyah;
                    if (!empty($namaIbu) && empty($orangTua->nama_ibu)) $orangTua->nama_ibu = $namaIbu;
                    if (!empty($alamat) && empty($orangTua->alamat)) $orangTua->alamat = $alamat;
                    $orangTua->save();
                }

                // Cek data siswa sebelumnya
                $existingSiswa = Siswa::where('nisn', $nisn)->first();
                $token = $existingSiswa ? $existingSiswa->qr_code_token : $this->qrCodeService->generateToken($nisn);

                $siswa = Siswa::updateOrCreate(
                    ['nisn' => $nisn],
                    [
                        'nis' => $nis ?: ($existingSiswa ? $existingSiswa->nis : null),
                        'nama' => $nama,
                        'jenis_kelamin' => $jk,
                        'kelas_id' => $kelasId,
                        'orang_tua_id' => $orangTua->id,
                        'qr_code_token' => $token,
                        'status' => 'aktif',
                    ]
                );

                // Simpan Riwayat Kelas
                RiwayatKelas::updateOrCreate(
                    ['siswa_id' => $siswa->id, 'tahun_ajaran' => $currentTahunAjaran],
                    ['kelas_id' => $kelasId, 'status' => 'aktif']
                );

                if ($existingSiswa) {
                    $updatedCount++;
                } else {
                    $importedCount++;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Berhasil memproses import: {$importedCount} data siswa baru ditambahkan, {$updatedCount} data siswa diperbarui.",
                'imported' => $importedCount,
                'updated' => $updatedCount,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Import Process Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
                'imported' => 0,
                'updated' => 0,
            ];
        }
    }

    /**
     * Membaca file CSV dengan auto-detect delimiter dan pembersihan UTF-8 BOM
     */
    protected function readCsv(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false) return [];

        // Hapus UTF-8 BOM jika ada
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (empty($lines)) return [];

        // Deteksi delimiter (; , atau \t)
        $firstLine = $lines[0];
        $semicolonCount = substr_count($firstLine, ';');
        $commaCount = substr_count($firstLine, ',');
        $tabCount = substr_count($firstLine, "\t");

        $delimiter = ',';
        if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
            $delimiter = ';';
        } elseif ($tabCount > $commaCount && $tabCount > $semicolonCount) {
            $delimiter = "\t";
        }

        $rows = [];
        foreach ($lines as $line) {
            if (trim($line) === '') continue;
            $rows[] = str_getcsv($line, $delimiter);
        }

        return $rows;
    }

    /**
     * Membaca file Excel (.xlsx / .xls) menggunakan PhpSpreadsheet
     */
    protected function readExcel(string $filePath): array
    {
        if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            throw new \Exception('Library PhpSpreadsheet belum siap. Silakan gunakan format file .CSV terlebih dahulu.');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        return $worksheet->toArray();
    }

    /**
     * Memetakan nama kolom ke atribut model secara fleksibel
     */
    protected function mapHeaders(array $headers): array
    {
        $map = [];

        foreach ($headers as $colIndex => $header) {
            // Normalisasi: lowercase, hapus spasi & tanda baca
            $clean = strtolower(trim((string)$header));
            $clean = str_replace(["\xEF\xBB\xBF", ' ' , '-', '_', '.', '/', ':', '(', ')'], '', $clean);

            if (in_array($clean, ['nama', 'namasiswa', 'namalengkap', 'namapesertadidik'])) {
                $map['nama'] = $colIndex;
            } elseif (in_array($clean, ['nisn', 'nomornisn', 'nonisn'])) {
                $map['nisn'] = $colIndex;
            } elseif (in_array($clean, ['nipd', 'nis', 'noinduk', 'nomorinduk', 'nik'])) {
                if (!isset($map['nis'])) $map['nis'] = $colIndex;
            } elseif (in_array($clean, ['jk', 'jeniskelamin', 'gender', 'lp'])) {
                $map['jk'] = $colIndex;
            } elseif (in_array($clean, ['rombelsaatini', 'rombonganbelajar', 'rombel', 'kelas', 'tingkat'])) {
                $map['kelas'] = $colIndex;
            } elseif (in_array($clean, ['hp', 'nohp', 'nowa', 'telepon', 'nomortelepon', 'nomorteleponseluler', 'nohandphone', 'whatsapp'])) {
                $map['hp'] = $colIndex;
            } elseif (in_array($clean, ['dataayahnama', 'namaayah', 'ayah', 'orangtua'])) {
                $map['nama_ayah'] = $colIndex;
            } elseif (in_array($clean, ['dataibunama', 'namaibu', 'ibu'])) {
                $map['nama_ibu'] = $colIndex;
            } elseif (in_array($clean, ['datawalinama', 'namawali', 'wali'])) {
                $map['nama_wali'] = $colIndex;
            } elseif (in_array($clean, ['alamat', 'alamattempattinggal', 'dusun', 'kelurahan'])) {
                if (!isset($map['alamat'])) $map['alamat'] = $colIndex;
            }
        }

        return $map;
    }

    /**
     * Mencocokkan nilai nama rombel dengan ID kelas yang ada di database
     */
    protected function resolveKelasId(?string $kelasRaw, $kelases, ?int $defaultKelasId): int
    {
        if (!empty($kelasRaw)) {
            $clean = trim($kelasRaw);

            // Cari exact match
            $found = $kelases->first(function($k) use ($clean) {
                return strcasecmp($k->nama_kelas, $clean) === 0;
            });
            if ($found) return $found->id;

            // Cari partial match (misal di file 'Kelas 7' di db '7' atau sebaliknya)
            $found = $kelases->first(function($k) use ($clean) {
                $kName = strtolower(str_replace(['kelas', ' '], '', $k->nama_kelas));
                $cName = strtolower(str_replace(['kelas', ' '], '', $clean));
                return $kName === $cName || str_contains($cName, $kName) || str_contains($kName, $cName);
            });
            if ($found) return $found->id;
        }

        // Gunakan default dari form jika tersedia
        if ($defaultKelasId && $kelases->where('id', $defaultKelasId)->isNotEmpty()) {
            return $defaultKelasId;
        }

        // Fallback kelas pertama
        return $kelases->first() ? $kelases->first()->id : 1;
    }

    /**
     * Membersihkan nomor telepon agar standar format WA
     */
    protected function cleanPhoneNumber(?string $phone): string
    {
        if (empty($phone)) return '';

        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (empty($clean)) return '';

        if (str_starts_with($clean, '62')) {
            $clean = '0' . substr($clean, 2);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '0' . $clean;
        }

        return $clean;
    }
}
