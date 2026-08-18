<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrangTua;
use Illuminate\Http\Request;

class OrangTuaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'ayah_asc');

        $query = OrangTua::with(['siswas.kelas']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_ayah', 'like', "%{$search}%")
                  ->orWhere('nama_ibu', 'like', "%{$search}%")
                  ->orWhere('no_wa', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        switch ($sortBy) {
            case 'ayah_desc':
                $query->orderBy('nama_ayah', 'desc');
                break;
            case 'ibu_asc':
                $query->orderBy('nama_ibu', 'asc');
                break;
            case 'no_wa':
                $query->orderBy('no_wa', 'asc');
                break;
            case 'ayah_asc':
            default:
                $query->orderBy('nama_ayah', 'asc');
                break;
        }

        $orangTuas = $query->paginate(15)->withQueryString();

        return view('admin.orangtua.index', compact('orangTuas', 'search', 'sortBy'));
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

        return redirect()->back()->with('success', 'Data Orang Tua / Wali berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $orangTua = OrangTua::findOrFail($id);

        $request->validate([
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'no_wa' => 'required|string|max:30',
            'alamat' => 'nullable|string',
        ]);

        $orangTua->update($request->all());

        return redirect()->back()->with('success', 'Data Orang Tua / Wali berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $orangTua = OrangTua::findOrFail($id);
        $orangTua->delete();

        return redirect()->back()->with('success', 'Data Orang Tua / Wali berhasil dihapus!');
    }
}
