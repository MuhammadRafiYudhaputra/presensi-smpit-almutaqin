<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'nama_asc');

        $query = Guru::with(['user', 'kelas']);

        switch ($sortBy) {
            case 'nama_desc':
                $query->orderBy('nama', 'desc');
                break;
            case 'nip':
                $query->orderBy('nip', 'asc');
                break;
            case 'nama_asc':
            default:
                $query->orderBy('nama', 'asc');
                break;
        }

        $gurus = $query->paginate(15)->withQueryString();
        $kelases = Kelas::all();

        return view('admin.guru.index', compact('gurus', 'kelases', 'sortBy'));
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
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guru',
        ]);

        $guru = Guru::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        if (!empty($request->kelas_id)) {
            Kelas::where('id', $request->kelas_id)->update(['guru_id' => $guru->id]);
        }

        return redirect()->back()->with('success', 'Data Guru Wali Kelas berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::with('user')->findOrFail($id);

        $request->validate([
            'nip' => 'nullable|string|unique:gurus,nip,' . $guru->id,
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($guru->user ? $guru->user->id : 0),
            'no_hp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $guru->update([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        if ($guru->user) {
            $guru->user->update([
                'name' => $request->nama,
                'email' => $request->email,
            ]);
        }

        // Reset previous class assignment and assign new one if provided
        Kelas::where('guru_id', $guru->id)->update(['guru_id' => null]);
        if (!empty($request->kelas_id)) {
            Kelas::where('id', $request->kelas_id)->update(['guru_id' => $guru->id]);
        }

        return redirect()->back()->with('success', 'Data Wali Kelas berhasil diperbarui!');
    }

    public function resetPassword(Request $request, $id)
    {
        $guru = Guru::with('user')->findOrFail($id);
        
        if (!$guru->user) {
            return redirect()->back()->with('error', 'Akun login portal guru tidak ditemukan!');
        }

        $newPassword = $request->input('password', '12345678');

        $guru->user->update([
            'password' => Hash::make($newPassword),
        ]);

        return redirect()->back()->with('success', "Password akun Guru [{$guru->nama}] berhasil diubah menjadi: {$newPassword}");
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        
        // Unassign from class
        Kelas::where('guru_id', $guru->id)->update(['guru_id' => null]);

        if ($guru->user) {
            $guru->user->delete();
        } else {
            $guru->delete();
        }

        return redirect()->back()->with('success', 'Data Guru Wali Kelas berhasil dihapus!');
    }
}
