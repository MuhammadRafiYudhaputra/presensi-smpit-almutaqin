@extends('layouts.app')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-users me-2 text-primary"></i>Kelola Data Orang Tua / Wali</h5>
            <small class="text-muted">Kontak WhatsApp untuk penerima notifikasi otomatis</small>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddOrangTua">
            <i class="fa-solid fa-plus me-1"></i> Tambah Orang Tua Baru
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nama Ayah / Ibu</th>
                    <th>Nomor WhatsApp Fonnte</th>
                    <th>Anak (Siswa)</th>
                    <th>Alamat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orangTuas as $ot)
                <tr>
                    <td>
                        <span class="fw-semibold d-block">{{ $ot->nama_ayah ?? '-' }} (Ayah)</span>
                        <small class="text-muted">{{ $ot->nama_ibu ?? '-' }} (Ibu)</small>
                    </td>
                    <td>
                        <span class="badge bg-success fs-7"><i class="fa-brands fa-whatsapp"></i> {{ $ot->no_wa }}</span>
                    </td>
                    <td>
                        @foreach($ot->siswas as $s)
                            <span class="badge bg-light text-dark border">{{ $s->nama }} ({{ $s->kelas->nama_kelas ?? '-' }})</span>
                        @endforeach
                    </td>
                    <td>{{ $ot->alamat ?? '-' }}</td>
                    <td class="text-center">
                        <form action="{{ route('admin.orangtua.destroy', $ot->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data orang tua ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada data orang tua.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $orangTuas->links() }}
    </div>
</div>

<!-- Modal Tambah Orang Tua -->
<div class="modal fade" id="modalAddOrangTua" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Data Orang Tua / Wali</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.orangtua.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Ayah</label>
                        <input type="text" name="nama_ayah" class="form-control" placeholder="Nama Ayah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Ibu</label>
                        <input type="text" name="nama_ibu" class="form-control" placeholder="Nama Ibu">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor WhatsApp (Aktif Fonnte)</label>
                        <input type="text" name="no_wa" class="form-control" required placeholder="Contoh: 08123456789">
                        <small class="text-muted">Nomor ini akan menerima pesan notifikasi presensi otomatis.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
