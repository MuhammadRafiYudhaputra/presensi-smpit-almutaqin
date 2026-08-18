@extends('layouts.app')

@section('content')
<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-chalkboard-user me-2 text-primary"></i>Kelola Master Data Guru</h5>
            <small class="text-muted">Data Tenaga Pendidik, pembina wali kelas, dan manajemen akun login guru</small>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAddGuru">
            <i class="fa-solid fa-plus me-1"></i> Tambah Guru Baru
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-dark">NIP</th>
                    <th class="text-dark">Nama Guru</th>
                    <th class="text-dark">Email Login</th>
                    <th class="text-dark">No. HP / WA</th>
                    <th class="text-dark">Alamat</th>
                    <th class="text-center text-dark" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gurus as $guru)
                <tr>
                    <td><span class="fw-bold text-dark">{{ $guru->nip ?? '-' }}</span></td>
                    <td class="fw-semibold text-dark">{{ $guru->nama }}</td>
                    <td><code>{{ $guru->user->email ?? '-' }}</code></td>
                    <td>
                        @if($guru->no_hp)
                            <small class="text-success"><i class="fa-brands fa-whatsapp me-1"></i>{{ $guru->no_hp }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $guru->alamat ?? '-' }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-2 me-1" title="Reset Password" onclick="openResetPasswordModal({{ $guru->id }}, '{{ addslashes($guru->nama) }}')">
                            <i class="fa-solid fa-key"></i> Reset
                        </button>
                        <form action="{{ route('admin.guru.destroy', $guru->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data guru ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Hapus Guru">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">Belum ada data guru terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $gurus->links() }}
    </div>
</div>

<!-- Modal Tambah Guru -->
<div class="modal fade" id="modalAddGuru" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Tambah Data Guru Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.guru.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">NIP (Opsional)</label>
                        <input type="text" name="nip" class="form-control" placeholder="Nomor Induk Pegawai">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Nama Lengkap Guru <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required placeholder="Nama Lengkap & Gelar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Email Login <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="guru@almutaqin.sch.id">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Password Login <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter" value="12345678">
                        <small class="text-muted">Default: <code>12345678</code></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="08123456789">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Alamat Tempat Tinggal</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat guru..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Guru</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="modalResetPassword" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-key me-2 text-warning"></i>Reset Password Akun Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formResetPassword" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-dark">Reset password untuk akun guru: <strong id="reset_guru_nama">-</strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Password Baru:</label>
                        <input type="text" name="password" class="form-control" value="12345678" required>
                        <small class="text-muted">Password default: <code>12345678</code></small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openResetPasswordModal(guruId, guruNama) {
    document.getElementById('reset_guru_nama').innerText = guruNama;
    document.getElementById('formResetPassword').action = `/admin/guru/${guruId}/reset-password`;
    const modal = new bootstrap.Modal(document.getElementById('modalResetPassword'));
    modal.show();
}
</script>
@endsection
