<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelases = Kelas::with('waliKelas')->latest()->paginate(15);
        $gurus = Guru::all();
        return view('admin.kelas.index', compact('kelases', 'gurus'));
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
