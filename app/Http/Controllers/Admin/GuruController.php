<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'nama_asc');

        $query = Guru::with(['user', 'kelas']);

        switch ($sortBy) {
            case 'nama_desc':
                $query->orderBy('nama', 'desc');
                break;
            case 'nip':
                $query->orderBy('nip', 'asc');
                break;
            case 'nama_asc':
            default:
                $query->orderBy('nama', 'asc');
                break;
        }

        $gurus = $query->paginate(15)->withQueryString();
        $kelases = Kelas::all();

        return view('admin.guru.index', compact('gurus', 'kelases', 'sortBy'));
    }

    public function store(Request $request)
    {
        if ($request->hasFile('file_guru')) {
            return $this->importGuru($request);
        }

        $request->validate([
            'nip' => 'nullable|string|unique:gurus,nip',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guru',
        ]);

        $guru = Guru::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        if (!empty($request->kelas_id)) {
            Kelas::where('id', $request->kelas_id)->update(['guru_id' => $guru->id]);
        }

        return redirect()->back()->with('success', 'Data Guru Wali Kelas berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::with('user')->findOrFail($id);

        $request->validate([
            'nip' => 'nullable|string|unique:gurus,nip,' . $guru->id,
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($guru->user ? $guru->user->id : 0),
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $guru->update([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        if ($guru->user) {
            $guru->user->update([
                'name' => $request->nama,
                'email' => $request->email,
            ]);
        }

        // Reset previous class assignment and assign new one if provided
        Kelas::where('guru_id', $guru->id)->update(['guru_id' => null]);
        if (!empty($request->kelas_id)) {
            Kelas::where('id', $request->kelas_id)->update(['guru_id' => $guru->id]);
        }

        return redirect()->back()->with('success', 'Data Wali Kelas berhasil diperbarui!');
    }

    public function resetPassword(Request $request, $id)
    {
        $guru = Guru::with('user')->findOrFail($id);
        
        if (!$guru->user) {
            return redirect()->back()->with('error', 'Akun login portal guru tidak ditemukan!');
        }

        $newPassword = $request->input('password', '12345678');

        $guru->user->update([
            'password' => Hash::make($newPassword),
        ]);

        return redirect()->back()->with('success', "Password akun Guru [{$guru->nama}] berhasil diubah menjadi: {$newPassword}");
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        
        // Unassign from class
        Kelas::where('guru_id', $guru->id)->update(['guru_id' => null]);

        if ($guru->user) {
            $guru->user->delete();
        } else {
            $guru->delete();
        }

        return redirect()->back()->with('success', 'Data Guru Wali Kelas berhasil dihapus!');
    }

    /**
     * Import Akun Guru dari file CSV atau Excel
     */
    public function importGuru(Request $request)
    {
        $request->validate([
            'file_guru' => 'required|file|max:10240',
        ], [
            'file_guru.required' => 'Silakan pilih file data guru terlebih dahulu.',
        ]);

        $file = $request->file('file_guru');
        $ext = strtolower($file->getClientOriginalExtension());
        $rows = [];

        try {
            if (in_array($ext, ['xlsx', 'xls']) && class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $rows = $spreadsheet->getActiveSheet()->toArray();
            } else {
                $content = file_get_contents($file->getRealPath());
                if (substr($content, 0, 3) === "\xEF\xBB\xBF") $content = substr($content, 3);
                $lines = preg_split('/\r\n|\r|\n/', trim($content));
                $delim = str_contains($lines[0] ?? '', ';') ? ';' : ',';
                foreach ($lines as $l) {
                    if (trim($l) !== '') $rows[] = str_getcsv($l, $delim);
                }
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }

        if (count($rows) < 2) {
            return redirect()->back()->with('error', 'File tidak memiliki baris data guru yang valid.');
        }

        $headers = array_shift($rows);
        $headerMap = [];
        foreach ($headers as $idx => $h) {
            $clean = strtolower(str_replace([' ', '_', '-'], '', trim((string)$h)));
            if (in_array($clean, ['nama', 'namaguru', 'namalengkap'])) $headerMap['nama'] = $idx;
            elseif (in_array($clean, ['nip', 'nomornip'])) $headerMap['nip'] = $idx;
            elseif (in_array($clean, ['email', 'surel'])) $headerMap['email'] = $idx;
            elseif (in_array($clean, ['nohp', 'hp', 'telepon', 'nowa'])) $headerMap['no_hp'] = $idx;
            elseif (in_array($clean, ['alamat'])) $headerMap['alamat'] = $idx;
            elseif (in_array($clean, ['kelas', 'walikelas', 'rombel'])) $headerMap['kelas'] = $idx;
        }

        $imported = 0;
        $kelases = Kelas::all();

        foreach ($rows as $row) {
            $nama = isset($headerMap['nama']) ? trim((string)($row[$headerMap['nama']] ?? '')) : '';
            if (empty($nama)) continue;

            $email = isset($headerMap['email']) ? trim((string)($row[$headerMap['email']] ?? '')) : '';
            if (empty($email)) {
                $email = 'guru.' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nama)) . rand(10, 99) . '@almutaqin.sch.id';
            }

            $nip = isset($headerMap['nip']) ? trim((string)($row[$headerMap['nip']] ?? '')) : null;
            $noHp = isset($headerMap['no_hp']) ? trim((string)($row[$headerMap['no_hp']] ?? '')) : null;
            $alamat = isset($headerMap['alamat']) ? trim((string)($row[$headerMap['alamat']] ?? '')) : null;
            $kelasVal = isset($headerMap['kelas']) ? trim((string)($row[$headerMap['kelas']] ?? '')) : null;

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $nama,
                    'password' => Hash::make('12345678'),
                    'role' => 'guru'
                ]
            );

            $guru = Guru::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $nama,
                    'nip' => $nip,
                    'no_hp' => $noHp,
                    'alamat' => $alamat
                ]
            );

            // Hubungkan ke kelas jika ada
            if (!empty($kelasVal)) {
                $matchKelas = $kelases->first(function($k) use ($kelasVal) {
                    return strcasecmp($k->nama_kelas, $kelasVal) === 0 || str_contains(strtolower($k->nama_kelas), strtolower($kelasVal));
                });
                if ($matchKelas) {
                    $matchKelas->update(['guru_id' => $guru->id]);
                }
            }

            $imported++;
        }

        return redirect()->back()->with('success', "Berhasil mengimpor {$imported} data Guru / Wali Kelas.");
    }

    /**
     * Download format template CSV Guru
     */
    public function downloadTemplate()
    {
        $filename = 'template_import_wali_kelas.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['NIP', 'Nama', 'Email', 'No HP', 'Alamat', 'Wali Kelas'];
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);
            fputcsv($file, ['198501012010011001', 'Ustadz Zulkifli, S.Pd', 'zulkifli@almutaqin.sch.id', '081234567801', 'Tasikmalaya', '9A']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
