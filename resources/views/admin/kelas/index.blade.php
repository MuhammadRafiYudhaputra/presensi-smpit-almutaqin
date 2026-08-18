@extends('layouts.app')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-school me-2 text-primary"></i>Kelola Data Kelas</h5>
            <small class="text-muted">Daftar kelas dan Wali Kelas penanggung jawab</small>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddKelas">
            <i class="fa-solid fa-plus me-1"></i> Tambah Kelas Baru
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nama Kelas</th>
                    <th>Wali Kelas</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelases as $k)
                <tr>
                    <td>#{{ $k->id }}</td>
                    <td class="fw-bold text-primary">{{ $k->nama_kelas }}</td>
                    <td>{{ $k->waliKelas->nama ?? 'Belum ditentukan' }}</td>
                    <td class="text-center">
                        <form action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kelas ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Belum ada data kelas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="modalAddKelas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" required placeholder="Contoh: 7A, 8B, 9C">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Wali Kelas (Opsional)</label>
                        <select name="guru_id" class="form-select">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach($gurus as $g)
                                <option value="{{ $g->id }}">{{ $g->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
