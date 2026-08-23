@extends('layouts.app')

@section('content')
<style>
    .admin-avatar-circle {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.15rem;
    }
</style>

<div class="row g-4">
    <!-- Kolom Kiri: Profil & Ganti Password Saya Sendiri -->
    <div class="col-lg-5">
        <div class="card card-custom p-4 shadow-sm border-0 rounded-4 h-100">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                <div class="p-2.5 bg-primary bg-opacity-10 text-primary rounded-3">
                    <i class="fa-solid fa-user-gear fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Profil Akun Saya</h5>
                    <small class="text-muted">Perbarui identitas &amp; kata sandi akun Anda</small>
                </div>
            </div>

            <form action="{{ route('admin.user.profile') }}" method="POST">
                @csrf
                <!-- Nama Lengkap -->
                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Nama Lengkap Admin</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="name" class="form-control border-start-0" value="{{ old('name', $currentUser->name) }}" required>
                    </div>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Alamat Email Login</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control border-start-0" value="{{ old('email', $currentUser->email) }}" required>
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-key text-warning"></i> Ganti Kata Sandi (Opsional)
                </h6>

                <!-- Password Saat Ini -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" class="form-control" placeholder="Masukkan jika ingin mengganti password">
                </div>

                <!-- Password Baru -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Kata Sandi Baru</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter">
                </div>

                <!-- Konfirmasi Password Baru -->
                <div class="mb-4">
                    <label class="form-label fw-semibold small text-muted">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="new_password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru">
                </div>

                <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2.5 shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Profil
                </button>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Daftar Seluruh Admin TU & Tambah Admin Baru -->
    <div class="col-lg-7">
        <div class="card card-custom p-4 shadow-sm border-0 rounded-4 h-100">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2.5 bg-success bg-opacity-10 text-success rounded-3">
                        <i class="fa-solid fa-users-gear fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Daftar Admin TU</h5>
                        <small class="text-muted">Kelola akun staf TU yang memiliki akses admin</small>
                    </div>
                </div>
                <button type="button" class="btn btn-primary rounded-pill px-3 py-2 btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin">
                    <i class="fa-solid fa-user-plus"></i> Tambah Admin
                </button>
            </div>

            <!-- Tabel Daftar Admin -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Staf Admin</th>
                            <th>Email Login</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $index => $admin)
                        <tr>
                            <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="admin-avatar-circle bg-primary bg-opacity-10 text-primary">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $admin->name }}</div>
                                        @if($admin->id === Auth::id())
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-0.5" style="font-size: 0.7rem;">
                                                <i class="fa-solid fa-circle-check me-1"></i> Akun Anda
                                            </span>
                                        @else
                                            <small class="text-muted">Staf TU</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-dark fw-semibold">{{ $admin->email }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-outline-primary rounded-pill px-2.5 py-1 me-1" title="Edit Data Admin" onclick="openEditModal({{ $admin->id }}, '{{ addslashes($admin->name) }}', '{{ $admin->email }}')">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Delete Button (Hanya jika bukan diri sendiri) -->
                                    @if($admin->id !== Auth::id())
                                    <form action="{{ route('admin.user.destroy', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun admin [{{ addslashes($admin->name) }}]?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger rounded-pill px-2.5 py-1" title="Hapus Admin">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Admin Baru -->
<div class="modal fade" id="modalTambahAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-user-plus text-primary"></i> Tambah Akun Admin TU
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.user.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Daftarkan staf TU baru untuk memiliki hak akses mengelola sistem presensi sekolah.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Nama Lengkap Staf TU</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Siti Rahma, S.Pd" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Alamat Email Login</label>
                        <input type="email" name="email" class="form-control" placeholder="Contoh: sitirahma@almutaqin.sch.id" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Kata Sandi Awal</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-plus me-1"></i> Daftarkan Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Admin -->
<div class="modal fade" id="modalEditAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-primary"></i> Edit Data Admin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditAdmin" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Nama Lengkap Staf TU</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Alamat Email Login</label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Ganti Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter baru">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(id, name, email) {
        const form = document.getElementById('formEditAdmin');
        form.action = `/admin/user/${id}`;
        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;

        const modal = new bootstrap.Modal(document.getElementById('modalEditAdmin'));
        modal.show();
    }
</script>
@endsection
