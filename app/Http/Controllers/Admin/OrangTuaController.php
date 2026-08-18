<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrangTua;
use Illuminate\Http\Request;

class OrangTuaController extends Controller
{
    public function index()
    {
        $orangTuas = OrangTua::with('siswas')->latest()->paginate(15);
        return view('admin.orangtua.index', compact('orangTuas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'no_wa' => 'required|string|max:30',
            'alamat' => 'nullable|string',
        ]);

        OrangTua::create($request->all());

        return redirect()->back()->with('success', 'Data Orang Tua berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $orangTua = OrangTua::findOrFail($id);
        $orangTua->delete();

        return redirect()->back()->with('success', 'Data Orang Tua berhasil dihapus!');
    }
}
