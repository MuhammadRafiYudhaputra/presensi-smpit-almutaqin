<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\OrangTua;
use App\Services\QrCodeService;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    protected QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
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
