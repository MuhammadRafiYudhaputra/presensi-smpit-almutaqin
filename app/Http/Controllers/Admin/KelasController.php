<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelases = Kelas::with(['waliKelas', 'siswas'])->latest()->paginate(15);
        $gurus = Guru::all();
        
        // Ambil semua siswa aktif untuk daftar pengecualian tinggal kelas di modal
        $allActiveSiswa = Siswa::with('kelas')
            ->where('status', '!=', 'alumni')
            ->orWhereNull('status')
            ->orderBy('nama', 'asc')
            ->get();

        return view('admin.kelas.index', compact('kelases', 'gurus', 'allActiveSiswa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'guru_id' => 'nullable|exists:gurus,id',
        ]);

        Kelas::create($request->all());

        return redirect()->back()->with('success', 'Data Kelas berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return redirect()->back()->with('success', 'Data Kelas berhasil dihapus!');
    }
}
