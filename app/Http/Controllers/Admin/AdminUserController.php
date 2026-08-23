<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Tampilkan Daftar Admin TU & Pengaturan Profil
     */
    public function index(Request $request)
    {
        $admins = User::where('role', 'admin')->orderBy('id', 'asc')->get();
        $currentUser = Auth::user();

        return view('admin.user.index', compact('admins', 'currentUser'));
    }

    /**
     * Daftarkan Admin TU Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => 'Nama lengkap staf admin wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email ini sudah terdaftar pada sistem.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return redirect()->back()->with('success', "Akun Admin TU baru [{$request->name}] berhasil didaftarkan!");
    }

    /**
     * Update Data Admin Tertentu
     */
    public function update(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($admin->id)],
            'password' => 'nullable|string|min:6',
        ], [
            'name.required' => 'Nama lengkap staf admin wajib diisi.',
            'email.unique' => 'Alamat email ini sudah digunakan oleh akun lain.',
            'password.min' => 'Kata sandi minimal 6 karakter jika diubah.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->back()->with('success', "Data akun admin [{$admin->name}] berhasil diperbarui!");
    }

    /**
     * Hapus Akun Admin TU
     */
    public function destroy($id)
    {
        if ((int)$id === (int)Auth::id()) {
            return redirect()->back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan!']);
        }

        $adminCount = User::where('role', 'admin')->count();
        if ($adminCount <= 1) {
            return redirect()->back()->withErrors(['error' => 'Sistem membutuhkan minimal 1 akun Admin aktif. Anda tidak dapat menghapus admin terakhir!']);
        }

        $admin = User::where('role', 'admin')->findOrFail($id);
        $name = $admin->name;
        $admin->delete();

        return redirect()->back()->with('success', "Akun Admin [{$name}] berhasil dihapus dari sistem.");
    }

    /**
     * Update Profil & Password Saya Sendiri
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama profil wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (!empty($request->new_password)) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Kata sandi saat ini yang Anda masukkan salah.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', 'Profil dan kata sandi Anda berhasil diperbarui!');
    }
}
