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

    public function index()
    {
        $siswas = Siswa::with(['kelas', 'orangTua'])->latest()->paginate(15);
        $kelases = Kelas::all();
        $orangTuas = OrangTua::all();
        return view('admin.siswa.index', compact('siswas', 'kelases', 'orangTuas'));
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

        Siswa::create([
            'nisn' => $request->nisn,
            'nis' => $request->nis,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'kelas_id' => $request->kelas_id,
            'orang_tua_id' => $request->orang_tua_id,
            'qr_code_token' => $token,
        ]);

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

        $siswa->update($request->only(['nisn', 'nis', 'nama', 'jenis_kelamin', 'kelas_id', 'orang_tua_id']));

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return redirect()->back()->with('success', 'Data siswa berhasil dihapus!');
    }

    /**
     * Tampilan Cetak Kartu Pelajar dengan QR Code
     */
    public function printCard($id)
    {
        $siswa = Siswa::with(['kelas', 'orangTua'])->findOrFail($id);
        $qrSvg = $this->qrCodeService->renderSvg($siswa->qr_code_token, 180);

        return view('admin.siswa.card', compact('siswa', 'qrSvg'));
    }
}
