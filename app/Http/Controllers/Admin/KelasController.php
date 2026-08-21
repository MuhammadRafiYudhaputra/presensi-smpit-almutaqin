<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'nama_asc');

        $query = Kelas::with(['waliKelas', 'siswas']);

        switch ($sortBy) {
            case 'nama_desc':
                $query->orderBy('nama_kelas', 'desc');
                break;
            case 'nama_asc':
            default:
                $query->orderBy('nama_kelas', 'asc');
                break;
        }

        $kelases = $query->paginate(15)->withQueryString();
        $gurus = Guru::all();

        // Ambil semua siswa aktif untuk pengecualian tinggal kelas di modal kenaikan
        $allActiveSiswa = Siswa::with('kelas')
            ->where(function ($q) {
                $q->where('status', '!=', 'alumni')->orWhereNull('status');
            })
            ->orderBy('nama', 'asc')
            ->get();

        return view('admin.kelas.index', compact('kelases', 'gurus', 'allActiveSiswa', 'sortBy'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:100|unique:kelas,nama_kelas',
            'guru_id' => 'nullable|exists:gurus,id',
        ]);

        Kelas::create($request->all());

        return redirect()->back()->with('success', 'Data Kelas baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'nama_kelas' => 'required|string|max:100|unique:kelas,nama_kelas,' . $kelas->id,
            'guru_id' => 'nullable|exists:gurus,id',
        ]);

        $kelas->update($request->all());

        return redirect()->back()->with('success', 'Data Kelas berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return redirect()->back()->with('success', 'Data Kelas berhasil dihapus!');
    }
}
