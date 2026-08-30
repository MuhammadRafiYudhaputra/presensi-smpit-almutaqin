@extends('layouts.app')

@section('content')
<style>
    .table-badge-icon {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
</style>

<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header & Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0.5 text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="fa-solid fa-chalkboard-user text-primary me-3 fs-5"></i>
                <span>Kelola Data Wali Kelas</span>
            </h5>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold shadow-sm btn-sm d-inline-flex align-items-center gap-1.5 text-nowrap" data-bs-toggle="modal" data-bs-target="#modalImportGuru" style="font-size: 0.82rem;">
                <i class="fa-solid fa-file-import me-1"></i>
                <span>Import Wali Kelas</span>
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-3.5 py-1.5 fw-semibold shadow-sm btn-sm d-inline-flex align-items-center gap-1.5 text-nowrap" data-bs-toggle="modal" data-bs-target="#modalAddGuru" style="font-size: 0.82rem;">
                <i class="fa-solid fa-plus me-1"></i>
                <span>Tambah Wali Kelas</span>
            </button>
        </div>
    </div>

    <!-- Sorting Filter -->
    <form action="{{ route('admin.guru.index') }}" method="GET" class="d-flex align-items-center gap-2 mb-4">
        <label class="form-label fw-bold text-nowrap mb-0 text-dark small">
            <i class="fa-solid fa-arrow-down-up-across-line text-primary me-1.5"></i> Urutkan:
        </label>
        <select name="sort_by" class="form-select form-select-sm shadow-sm" style="max-width: 230px;" onchange="this.form.submit()">
            <option value="nama_asc" {{ ($sortBy ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama Wali Kelas (A-Z)</option>
            <option value="nama_desc" {{ ($sortBy ?? '') === 'nama_desc' ? 'selected' : '' }}>Nama Wali Kelas (Z-A)</option>
            <option value="nip" {{ ($sortBy ?? '') === 'nip' ? 'selected' : '' }}>NIP Pegawai</option>
        </select>
    </form>

    <!-- Tabel Data Wali Kelas (Fit 100% Tanpa Scroll Samping) -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.85rem; width: 100%;">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 45px;" class="text-dark">No</th>
                    <th class="text-dark" style="width: 140px;">NIP Pegawai</th>
                    <th class="text-dark text-start">Nama Wali Kelas</th>
                    <th class="text-dark">Email Login Portal</th>
                    <th class="text-dark">Penugasan Kelas</th>
                    <th class="text-dark">No. HP / WA</th>
                    <th class="text-center text-dark" style="width: 110px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gurus as $idx => $guru)
                <tr>
                    <td class="text-center fw-bold text-muted">{{ $gurus->firstItem() + $idx }}</td>
                    <td class="text-center fw-semibold text-dark">{{ $guru->nip ?? '-' }}</td>
                    <td>
                        <span class="fw-bold text-dark d-block" style="font-size: 0.88rem;">{{ $guru->nama }}</span>
                        <small class="text-muted d-inline-flex align-items-center gap-1.5" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-location-dot text-secondary"></i>
                            <span>{{ $guru->alamat ?? 'Tasikmalaya' }}</span>
                        </small>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-1.5 rounded-pill fw-semibold table-badge-icon" style="font-size: 0.78rem;">
                            <i class="fa-solid fa-envelope text-primary"></i>
                            <span>{{ $guru->user->email ?? '-' }}</span>
                        </span>
                    </td>
                    <td class="text-center">
                        @if($guru->kelas)
                            <span class="badge bg-success px-3 py-1.5 rounded-pill shadow-sm fw-semibold table-badge-icon" style="font-size: 0.78rem;">
                                <i class="fa-solid fa-school"></i>
                                <span>Wali Kelas {{ $guru->kelas->nama_kelas }}</span>
                            </span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-muted border px-3 py-1.5 rounded-pill fw-semibold table-badge-icon" style="font-size: 0.78rem;">
                                <span>Belum Ditugaskan</span>
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($guru->no_hp)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1.5 rounded-pill fw-semibold table-badge-icon" style="font-size: 0.78rem;">
                                <i class="fa-brands fa-whatsapp text-success"></i>
                                <span>{{ $guru->no_hp }}</span>
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1.5 justify-content-center align-items-center">
                            <button type="button" class="btn btn-warning text-dark btn-sm rounded-2 d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 30px; height: 30px;" title="Ubah Password Login" onclick="openResetPasswordModal({{ $guru->id }}, '{{ addslashes($guru->nama) }}')">
                                <i class="fa-solid fa-key" style="font-size: 0.78rem;"></i>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm rounded-2 d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" title="Edit Data Wali Kelas" onclick="openEditGuruModal({{ json_encode($guru) }}, {{ $guru->kelas ? $guru->kelas->id : 'null' }})">
                                <i class="fa-solid fa-pen" style="font-size: 0.78rem;"></i>
                            </button>
                            <form action="{{ route('admin.guru.destroy', $guru->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data guru ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm rounded-2 d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" title="Hapus Guru">
                                    <i class="fa-solid fa-trash" style="font-size: 0.78rem;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="fa-solid fa-chalkboard-user fs-2 d-block mb-2 text-muted"></i>
                        Belum ada data guru wali kelas terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <small class="text-muted">Menampilkan {{ $gurus->firstItem() ?? 0 }} - {{ $gurus->lastItem() ?? 0 }} dari total {{ $gurus->total() }} wali kelas</small>
        {{ $gurus->links() }}
    </div>
</div>

<!-- Modal Tambah Guru -->
<div class="modal fade" id="modalAddGuru" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark d-flex align-items-center">
                    <i class="fa-solid fa-user-plus me-2.5 text-primary fs-5"></i>
                    <span>Tambah Wali Kelas Baru</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.guru.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Nomor Induk Pegawai</label>
                            <input type="text" name="nip" class="form-control" placeholder="">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Nama Lengkap & Gelar</label>
                            <input type="text" name="nama" class="form-control" placeholder="" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Email Login Portal</label>
                            <input type="email" name="email" class="form-control" placeholder="" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Password Login</label>
                            <input type="password" name="password" class="form-control" value="" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Penugasan Kelas Binaan</label>
                            <select name="kelas_id" class="form-select">
                                <option value="">-- Belum Ditugaskan --</option>
                                @foreach($kelases as $k)
                                    <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control" placeholder="">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">Alamat / Asal Kota</label>
                            <textarea name="alamat" class="form-control" rows="2" placeholder=""></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-end gap-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Wali Kelas</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Guru -->
<div class="modal fade" id="modalEditGuru" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark d-flex align-items-center">
                    <i class="fa-solid fa-user-pen me-2.5 text-primary fs-5"></i>
                    <span>Edit Data Wali Kelas</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditGuru" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">NIP Pegawai</label>
                            <input type="text" name="nip" id="edit_guru_nip" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_guru_nama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Email Login Portal <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_guru_email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Penugasan Kelas Binaan</label>
                            <select name="kelas_id" id="edit_guru_kelas_id" class="form-select">
                                <option value="">-- Belum Ditugaskan --</option>
                                @foreach($kelases as $k)
                                    <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" id="edit_guru_no_hp" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Alamat / Asal Kota</label>
                            <input type="text" name="alamat" id="edit_guru_alamat" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-end gap-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ubah Password -->
<div class="modal fade" id="modalResetPassword" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark d-flex align-items-center">
                    <i class="fa-solid fa-key me-3 text-warning fs-5"></i>
                    <span>Ubah Password Akun Guru</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formResetPassword" method="POST">
                @csrf
                <div class="modal-body py-3">
                    <p class="text-dark mb-3">Ubah kata sandi login untuk akun guru: <strong id="reset_guru_nama">-</strong></p>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-dark mb-1">Password Baru:</label>
                        <input type="text" name="password" class="form-control px-3" value="12345678" placeholder="Masukkan kata sandi baru" required>
                        <small class="text-muted mt-1 d-block" style="font-size: 0.78rem;">Password default cepat: <code>12345678</code></small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-end gap-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        <span>Simpan Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Guru -->
<div class="modal fade" id="modalImportGuru" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-file-import me-2 text-success"></i>Import Data Wali Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info border-info small mb-3">
                        <i class="fa-solid fa-circle-info me-1"></i> Unggah file data guru format Excel (.xlsx / .csv) untuk menambahkan akun guru secara massal.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Pilih File Excel / CSV Guru:</label>
                        <input type="file" name="file_guru" class="form-control" accept=".csv, .xlsx, .xls">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">Unggah & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditGuruModal(guru, kelasId) {
    document.getElementById('edit_guru_nip').value = guru.nip || '';
    document.getElementById('edit_guru_nama').value = guru.nama;
    document.getElementById('edit_guru_email').value = guru.user ? guru.user.email : '';
    document.getElementById('edit_guru_no_hp').value = guru.no_hp || '';
    document.getElementById('edit_guru_alamat').value = guru.alamat || '';
    document.getElementById('edit_guru_kelas_id').value = kelasId || '';
    document.getElementById('formEditGuru').action = `/admin/guru/${guru.id}`;
    const modal = new bootstrap.Modal(document.getElementById('modalEditGuru'));
    modal.show();
}

function openResetPasswordModal(guruId, guruNama) {
    document.getElementById('reset_guru_nama').innerText = guruNama;
    document.getElementById('formResetPassword').action = `/admin/guru/${guruId}/reset-password`;
    const modal = new bootstrap.Modal(document.getElementById('modalResetPassword'));
    modal.show();
}
</script>
@endsection
