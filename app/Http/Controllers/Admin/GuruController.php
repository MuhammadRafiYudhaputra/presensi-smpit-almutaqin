<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::with('user')->latest()->paginate(15);
        return view('admin.guru.index', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'nullable|string|unique:gurus,nip',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'guru',
        ]);

        Guru::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return redirect()->back()->with('success', 'Data Guru berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        if ($guru->user) {
            $guru->user->delete();
        } else {
            $guru->delete();
        }

        return redirect()->back()->with('success', 'Data Guru berhasil dihapus!');
    }
}
