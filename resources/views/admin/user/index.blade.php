@extends('layouts.app')

@section('content')
<style>
    .admin-avatar-circle {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.95rem;
    }
    .profile-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.5rem 1.35rem;
    }
    .compact-input {
        font-size: 0.88rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    .compact-addon {
        padding-left: 0.9rem;
        padding-right: 0.9rem;
        font-size: 0.9rem;
    }
</style>

<div class="row g-3">
    <!-- Kolom Kiri: Profil & Ganti Password Saya Sendiri -->
    <div class="col-lg-4">
        <div class="card card-custom profile-card shadow-sm border-0 h-100">
            <div class="d-flex align-items-center mb-3 pb-2.5 border-bottom">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                    <i class="fa-solid fa-user-gear fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Profil Akun Saya</h6>
                    <small class="text-muted" style="font-size: 0.76rem;">Perbarui nama, email &amp; kata sandi Anda</small>
                </div>
            </div>

            <form action="{{ route('admin.user.profile') }}" method="POST">
                @csrf
                <!-- Nama Lengkap -->
                <div class="mb-2.5">
                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.8rem;">Nama Lengkap Admin</label>
                    <div class="input-group">
                        <span class="input-group-text compact-addon bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="name" class="form-control compact-input border-start-0 ps-2" value="{{ old('name', $currentUser->name) }}" required>
                    </div>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.8rem;">Alamat Email Login</label>
                    <div class="input-group">
                        <span class="input-group-text compact-addon bg-light border-end-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control compact-input border-start-0 ps-2" value="{{ old('email', $currentUser->email) }}" required>
                    </div>
                </div>

                <hr class="my-3 text-muted opacity-25">

                <h6 class="fw-bold text-dark mb-2.5 d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-key text-warning me-1"></i> Ganti Kata Sandi
                </h6>

                <!-- Password Saat Ini -->
                <div class="mb-2.5">
                    <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.76rem;">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" class="form-control compact-input px-3" placeholder="Masukkan jika ingin ubah password">
                </div>

                <!-- Password Baru -->
                <div class="mb-2.5">
                    <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.76rem;">Kata Sandi Baru</label>
                    <input type="password" name="new_password" class="form-control compact-input px-3" placeholder="Minimal 6 karakter">
                </div>

                <!-- Konfirmasi Password Baru -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted mb-1" style="font-size: 0.76rem;">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="new_password_confirmation" class="form-control compact-input px-3" placeholder="Ulangi kata sandi baru">
                </div>

                <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2 btn-sm shadow-sm d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Perubahan Profil
                </button>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Daftar Seluruh Admin TU & Tambah Admin Baru -->
    <div class="col-lg-8">
        <div class="card card-custom profile-card shadow-sm border-0 h-100">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3 pb-2.5 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                        <i class="fa-solid fa-users-gear fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Daftar Admin TU</h6>
                        <small class="text-muted" style="font-size: 0.76rem;">Kelola seluruh akun staf TU dengan hak akses admin</small>
                    </div>
                </div>
                <button type="button" class="btn btn-primary rounded-pill px-3 py-1.5 btn-sm fw-bold shadow-sm d-inline-flex align-items-center text-nowrap" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin" style="font-size: 0.82rem;">
                    <i class="fa-solid fa-user-plus me-2"></i> Tambah Admin
                </button>
            </div>

            <!-- Tabel Daftar Admin (Lebar Lega, Tanpa Scroll Samping) -->
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem; min-width: 100%;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 45px;" class="text-center">No</th>
                            <th>Staf Admin</th>
                            <th>Email Login</th>
                            <th class="text-center" style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $index => $admin)
                        <tr>
                            <td class="fw-bold text-muted text-center">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="admin-avatar-circle bg-primary bg-opacity-10 text-primary flex-shrink-0 me-3">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0.5" style="font-size: 0.86rem;">{{ $admin->name }}</div>
                                        @if($admin->id === Auth::id())
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-0.5 rounded-pill fw-semibold" style="font-size: 0.68rem;">
                                                <i class="fa-solid fa-circle-check me-1"></i> Akun Anda
                                            </span>
                                        @else
                                            <small class="text-muted" style="font-size: 0.72rem;">Staf TU</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-dark fw-semibold" style="font-size: 0.83rem;">{{ $admin->email }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-outline-primary rounded-pill px-2.5 py-1 shadow-none {{ $admin->id !== Auth::id() ? 'me-1.5' : '' }}" title="Edit Data Admin" onclick="openEditModal({{ $admin->id }}, '{{ addslashes($admin->name) }}', '{{ $admin->email }}', {{ $admin->id === Auth::id() ? 'true' : 'false' }})">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Delete Button (Hanya jika admin lain) -->
                                    @if($admin->id !== Auth::id())
                                    <form action="{{ route('admin.user.destroy', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun admin [{{ addslashes($admin->name) }}]?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger rounded-pill px-2.5 py-1 shadow-none" title="Hapus Admin Ini">
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
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="fa-solid fa-user-plus text-primary me-2"></i> Tambah Akun Admin TU
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.user.store') }}" method="POST">
                @csrf
                <div class="modal-body py-3">
                    <p class="text-muted small mb-3" style="font-size: 0.8rem;">
                        Daftarkan staf TU baru untuk memiliki hak akses mengelola sistem presensi sekolah.
                    </p>

                    <div class="mb-2.5">
                        <label class="form-label fw-bold small text-dark mb-1">Nama Lengkap Staf TU</label>
                        <input type="text" name="name" class="form-control compact-input px-3" placeholder="Contoh: Siti Rahma, S.Pd" required>
                    </div>

                    <div class="mb-2.5">
                        <label class="form-label fw-bold small text-dark mb-1">Alamat Email Login</label>
                        <input type="email" name="email" class="form-control compact-input px-3" placeholder="Contoh: sitirahma@almutaqin.sch.id" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark mb-1">Kata Sandi Awal</label>
                        <input type="password" name="password" class="form-control compact-input px-3" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold">
                        <i class="fa-solid fa-plus me-1.5"></i> Daftarkan Admin
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
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Data Admin
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditAdmin" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body py-3">
                    <div class="mb-2.5">
                        <label class="form-label fw-bold small text-dark mb-1">Nama Lengkap Staf TU</label>
                        <input type="text" name="name" id="editName" class="form-control compact-input px-3" required>
                    </div>

                    <div class="mb-2.5">
                        <label class="form-label fw-bold small text-dark mb-1">Alamat Email Login</label>
                        <input type="email" name="email" id="editEmail" class="form-control compact-input px-3" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark mb-1">Ganti Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control compact-input px-3" placeholder="Minimal 6 karakter baru">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
                    <div>
                        <button type="button" id="btnDeleteInModal" class="btn btn-outline-danger rounded-pill px-3.5 btn-sm fw-semibold" onclick="deleteAdminFromModal()">
                            <i class="fa-solid fa-trash-can me-1.5"></i> Hapus Admin
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-3.5 btn-sm fw-bold">
                            <i class="fa-solid fa-floppy-disk me-1.5"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
            <form id="formDeleteAdminModal" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

<script>
    let currentEditAdminId = null;
    let currentEditAdminName = '';

    function openEditModal(id, name, email, isSelf) {
        currentEditAdminId = id;
        currentEditAdminName = name;

        const form = document.getElementById('formEditAdmin');
        form.action = `/admin/user/${id}`;
        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;

        const deleteBtn = document.getElementById('btnDeleteInModal');
        if (isSelf) {
            deleteBtn.style.display = 'none';
        } else {
            deleteBtn.style.display = 'inline-flex';
        }

        const modal = new bootstrap.Modal(document.getElementById('modalEditAdmin'));
        modal.show();
    }

    function deleteAdminFromModal() {
        if (!currentEditAdminId) return;
        if (confirm(`Apakah Anda yakin ingin menghapus akun admin [${currentEditAdminName}] dari sistem?`)) {
            const delForm = document.getElementById('formDeleteAdminModal');
            delForm.action = `/admin/user/${currentEditAdminId}`;
            delForm.submit();
        }
    }
</script>
@endsection
