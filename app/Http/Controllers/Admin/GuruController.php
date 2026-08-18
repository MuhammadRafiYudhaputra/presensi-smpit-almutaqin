<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::with(['user', 'kelasBinaan'])->latest()->paginate(15);
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
            'password' => Hash::make($request->password),
            'role' => 'guru',
        ]);

        Guru::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return redirect()->back()->with('success', 'Data Guru Wali Kelas berhasil ditambahkan!');
    }

    public function resetPassword(Request $request, $id)
    {
        $guru = Guru::with('user')->findOrFail($id);
        
        if (!$guru->user) {
            return redirect()->back()->with('error', 'Akun login guru tidak ditemukan!');
        }

        $newPassword = $request->input('password', '12345678');

        $guru->user->update([
            'password' => Hash::make($newPassword),
        ]);

        return redirect()->back()->with('success', "Password akun Guru [{$guru->nama}] berhasil di-reset menjadi: {$newPassword}");
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
