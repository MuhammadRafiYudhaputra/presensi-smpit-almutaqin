<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\OrangTua;
use App\Services\QrCodeService;
use App\Services\DapodikImportService;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    protected QrCodeService $qrCodeService;
    protected DapodikImportService $dapodikImportService;

    public function __construct(QrCodeService $qrCodeService, DapodikImportService $dapodikImportService)
    {
        $this->qrCodeService = $qrCodeService;
        $this->dapodikImportService = $dapodikImportService;
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'aktif');
        $search = $request->get('search');
        $kelasId = $request->get('kelas_id');
        $sortBy = $request->get('sort_by', 'nama_asc');

        $query = Siswa::with(['kelas', 'orangTua']);

        // Filter Status Siswa (Aktif vs Alumni vs Semua)
        if ($status === 'aktif') {
            $query->whereNotNull('kelas_id')->where(function ($q) {
                $q->where('status', '!=', 'alumni')->orWhereNull('status');
            });
        } elseif ($status === 'alumni') {
            $query->where(function ($q) {
                $q->where('status', 'alumni')->orWhereNull('kelas_id');
            });
        }

        // Pencarian NISN, Nama, atau NIK/NIS
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Filter Rombel / Kelas
        if (!empty($kelasId)) {
            $query->where('kelas_id', $kelasId);
        }

        // Sorting Default Berdasarkan Nama Siswa (A-Z) dan NISN
        switch ($sortBy) {
            case 'nama_desc':
                $query->orderBy('nama', 'desc')->orderBy('nisn', 'asc');
                break;
            case 'nisn':
                $query->orderBy('nisn', 'asc')->orderBy('nama', 'asc');
                break;
            case 'nama_asc':
            default:
                $query->orderBy('nama', 'asc')->orderBy('nisn', 'asc');
                break;
        }

        $siswas = $query->paginate(15)->withQueryString();
        $kelases = Kelas::all();
        $orangTuas = OrangTua::all();

        // Hitung statistik untuk badge status
        $countAktif = Siswa::whereNotNull('kelas_id')->where(function ($q) {
            $q->where('status', '!=', 'alumni')->orWhereNull('status');
        })->count();
        $countAlumni = Siswa::where(function ($q) {
            $q->where('status', 'alumni')->orWhereNull('kelas_id');
        })->count();
        $countSemua = Siswa::count();

        return view('admin.siswa.index', compact(
            'siswas',
            'kelases',
            'orangTuas',
            'status',
            'search',
            'kelasId',
            'sortBy',
            'countAktif',
            'countAlumni',
            'countSemua'
        ));
    }

    public function store(Request $request)
    {
        // Jika form mengirimkan file import dapodik
        if ($request->hasFile('file_dapodik')) {
            return $this->importDapodik($request);
        }

        $request->validate([
            'nisn' => 'required|string|unique:siswas,nisn',
            'nis' => 'nullable|string|unique:siswas,nis',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
            'orang_tua_id' => 'required|exists:orang_tuas,id',
        ]);

        $token = $this->qrCodeService->generateToken($request->nisn);

        $siswa = Siswa::create([
            'nisn' => $request->nisn,
            'nis' => $request->nis,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'kelas_id' => $request->kelas_id,
            'orang_tua_id' => $request->orang_tua_id,
            'qr_code_token' => $token,
            'status' => 'aktif',
        ]);

        $currentYear = (int)date('Y');
        $currentMonth = (int)date('n');
        $currentTahunAjaran = ($currentMonth >= 7) ? ($currentYear . '/' . ($currentYear + 1)) : (($currentYear - 1) . '/' . $currentYear);

        \App\Models\RiwayatKelas::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tahun_ajaran' => $currentTahunAjaran],
            ['kelas_id' => $request->kelas_id, 'status' => 'aktif']
        );

        return redirect()->back()->with('success', 'Data siswa berhasil ditambahkan & QR Code di-generate!');
    }

    /**
     * Memproses import file Excel/CSV Dapodik
     */
    public function importDapodik(Request $request)
    {
        $request->validate([
            'file_dapodik' => 'required|file|max:15360',
            'default_kelas_id' => 'nullable|exists:kelas,id',
        ], [
            'file_dapodik.required' => 'Silakan pilih file Excel / CSV data siswa terlebih dahulu.',
            'file_dapodik.max' => 'Ukuran file tidak boleh melebihi 15 MB.',
        ]);

        $result = $this->dapodikImportService->import(
            $request->file('file_dapodik'),
            $request->input('default_kelas_id') ? (int)$request->input('default_kelas_id') : null
        );

        if ($result['success']) {
            return redirect()->route('admin.siswa.index')->with('success', $result['message']);
        } else {
            return redirect()->route('admin.siswa.index')->with('error', $result['message']);
        }
    }

    /**
     * Download format template CSV Dapodik untuk contoh
     */
    public function downloadTemplate()
    {
        $filename = 'template_import_siswa_dapodik.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = [
            'No', 'Nama', 'NIPD', 'JK', 'NISN', 'Tempat Lahir', 'Tanggal Lahir', 'NIK', 'Agama',
            'Alamat', 'RT', 'RW', 'Dusun', 'Kelurahan', 'Kecamatan', 'Kode Pos',
            'Jenis Tinggal', 'Alat Transportasi', 'HP', 'E-Mail', 'Penerima KPS',
            'Data Ayah - Nama', 'Data Ibu - Nama', 'Data Wali - Nama', 'Rombel Saat Ini'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM untuk Microsoft Excel
            fputcsv($file, $columns);

            fputcsv($file, [
                '1', 'Ahmad Fadillah', '2425001', 'L', '0081234501', 'Tasikmalaya', '2011-05-12', '3206012345670001', 'Islam',
                'Jl. Al-Muttaqin No. 10', '01', '02', 'Dusun I', 'Mangkubumi', 'Mangkubumi', '46181',
                'Bersama orang tua', 'Sepeda Motor', '081234567891', 'ahmad@example.com', 'Tidak',
                'Bapak Fadil', 'Ibu Fadilah', '', '7'
            ]);
            fputcsv($file, [
                '2', 'Siti Aisyah', '2425002', 'P', '0081234502', 'Tasikmalaya', '2011-08-20', '3206012345670002', 'Islam',
                'Jl. Sukalaya No. 25', '03', '04', 'Dusun II', 'Cihideung', 'Cihideung', '46122',
                'Bersama orang tua', 'Jalan Kaki', '081234567892', 'siti@example.com', 'Tidak',
                'Bapak Aisy', 'Ibu Aisyah', '', '8'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'nisn' => 'required|string|unique:siswas,nisn,' . $siswa->id,
            'nis' => 'nullable|string|unique:siswas,nis,' . $siswa->id,
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
            'orang_tua_id' => 'required|exists:orang_tuas,id',
        ]);

        $siswa->update([
            'nisn' => $request->nisn,
            'nis' => $request->nis,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'kelas_id' => $request->kelas_id,
            'orang_tua_id' => $request->orang_tua_id,
        ]);

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return redirect()->back()->with('success', 'Data siswa berhasil dihapus!');
    }

    public function printCard($id)
    {
        $siswa = Siswa::with(['kelas', 'orangTua'])->findOrFail($id);
        
        try {
            $qrSvg = $this->qrCodeService->renderSvg($siswa->qr_code_token, 160);
        } catch (\Throwable $e) {
            $qrSvg = '<img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' . urlencode($siswa->qr_code_token) . '" alt="QR Code" width="160" height="160" style="display:block; margin:0 auto;" />';
        }

        return view('admin.siswa.card', compact('siswa', 'qrSvg'));
    }
}
