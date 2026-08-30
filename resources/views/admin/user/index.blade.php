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
    .compact-input {
        font-size: 0.88rem;
        padding-top: 0.55rem;
        padding-bottom: 0.55rem;
    }
</style>

<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header & Tombol Tambah Admin -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0.5 text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="fa-solid fa-users-gear text-primary me-3 fs-5"></i> Kelola Akun Administrator TU
            </h5>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-3.5 py-1.5 btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-2 text-nowrap" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin" style="font-size: 0.82rem;">
            <i class="fa-solid fa-user-plus me-1.5"></i>
            <span>Tambah Admin TU</span>
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;" class="text-center text-dark">No</th>
                    <th class="text-dark">Nama Administrator TU</th>
                    <th class="text-dark">Email Login</th>
                    <th class="text-dark text-center" style="width: 180px;">Hak Akses</th>
                    <th class="text-center text-dark" style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $index => $admin)
                <tr>
                    <td class="fw-bold text-muted text-center">{{ $index + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="admin-avatar-circle bg-primary bg-opacity-10 text-primary flex-shrink-0 me-3 border border-primary border-opacity-25">
                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark mb-0.5" style="font-size: 0.9rem;">{{ $admin->name }}</div>
                                @if($admin->id === Auth::id())
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2.5 py-0.5 rounded-pill fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.7rem;">
                                        <i class="fa-solid fa-circle-check text-success"></i>
                                        <span>Akun Anda (Aktif)</span>
                                    </span>
                                @else
                                    <small class="text-muted" style="font-size: 0.74rem;">Staf Tata Usaha</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="text-dark fw-semibold d-inline-flex align-items-center" style="font-size: 0.86rem;">
                            <i class="fa-regular fa-envelope text-muted me-2"></i>{{ $admin->email }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-1 rounded-pill fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-shield text-primary"></i>
                            <span>Administrator TU</span>
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-2 align-items-center">
                            <!-- Edit Button -->
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-none d-inline-flex align-items-center gap-1.5" title="Edit Profil & Password" onclick="openEditModal({{ $admin->id }}, '{{ addslashes($admin->name) }}', '{{ $admin->email }}', {{ $admin->id === Auth::id() ? 'true' : 'false' }})">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span>Edit</span>
                            </button>

                            <!-- Delete Button -->
                            @if($admin->id !== Auth::id())
                            <form action="{{ route('admin.user.destroy', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun admin [{{ addslashes($admin->name) }}]?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 shadow-none d-inline-flex align-items-center" title="Hapus Admin Ini">
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

<!-- Modal Tambah Admin Baru -->
<div class="modal fade" id="modalTambahAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="fa-solid fa-user-plus text-primary me-2.5 fs-5"></i>
                    <span>Tambah Akun Admin TU</span>
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
                        <label class="form-label fw-bold small text-dark mb-1">Nama Lengkap Staf TU <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control compact-input px-3" placeholder="Contoh: Siti Rahma, S.Pd" required>
                    </div>

                    <div class="mb-2.5">
                        <label class="form-label fw-bold small text-dark mb-1">Alamat Email Login <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control compact-input px-3" placeholder="Contoh: sitirahma@almutaqin.sch.id" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark mb-1">Kata Sandi Awal <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control compact-input px-3" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-plus me-1"></i>
                        <span>Daftarkan Admin</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Data & Kata Sandi Admin -->
<div class="modal fade" id="modalEditAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="fa-solid fa-pen-to-square text-primary me-2.5 fs-5"></i>
                    <span>Edit Profil &amp; Kata Sandi Admin</span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditAdmin" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body py-3">
                    <div class="mb-2.5">
                        <label class="form-label fw-bold small text-dark mb-1">Nama Lengkap Staf TU <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editName" class="form-control compact-input px-3" required>
                    </div>

                    <div class="mb-2.5">
                        <label class="form-label fw-bold small text-dark mb-1">Alamat Email Login <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="editEmail" class="form-control compact-input px-3" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark mb-1">Ganti Kata Sandi (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control compact-input px-3" placeholder="Minimal 6 karakter baru jika ingin ganti password">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(id, name, email, isSelf) {
        const form = document.getElementById('formEditAdmin');
        form.action = `/admin/user/${id}`;
        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;

        const modal = new bootstrap.Modal(document.getElementById('modalEditAdmin'));
        modal.show();
    }
</script>
@endsection
