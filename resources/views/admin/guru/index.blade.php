@extends('layouts.app')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-chalkboard-user me-2 text-primary"></i>Kelola Data Guru</h5>
            <small class="text-muted">Data Tenaga Pendidik & Akun Login Guru</small>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddGuru">
            <i class="fa-solid fa-plus me-1"></i> Tambah Guru Baru
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>NIP</th>
                    <th>Nama Guru</th>
                    <th>Email Login</th>
                    <th>No. HP</th>
                    <th>Alamat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gurus as $guru)
                <tr>
                    <td>{{ $guru->nip ?? '-' }}</td>
                    <td class="fw-semibold">{{ $guru->nama }}</td>
                    <td>{{ $guru->user->email ?? '-' }}</td>
                    <td>{{ $guru->no_hp ?? '-' }}</td>
                    <td>{{ $guru->alamat ?? '-' }}</td>
                    <td class="text-center">
                        <form action="{{ route('admin.guru.destroy', $guru->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data guru ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada data guru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $gurus->links() }}
    </div>
</div>

<!-- Modal Tambah Guru -->
<div class="modal fade" id="modalAddGuru" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Data Guru Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.guru.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIP (Opsional)</label>
                        <input type="text" name="nip" class="form-control" placeholder="Nomor Induk Pegawai">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Guru</label>
                        <input type="text" name="nama" class="form-control" required placeholder="Nama & Gelar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email (Untuk Login)</label>
                        <input type="email" name="email" class="form-control" required placeholder="guru@almutaqin.sch.id">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Login</label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="08123456789">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Guru</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
