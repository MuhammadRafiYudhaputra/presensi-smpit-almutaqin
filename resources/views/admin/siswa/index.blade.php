@extends('layouts.app')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-user-graduate me-2 text-primary"></i>Kelola Data Siswa</h5>
            <small class="text-muted">Total siswa terdaftar dan pembuatan token QR Code</small>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddSiswa">
            <i class="fa-solid fa-plus me-1"></i> Tambah Siswa Baru
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>NISN / NIS</th>
                    <th>Nama Lengkap</th>
                    <th>L/P</th>
                    <th>Kelas</th>
                    <th>Orang Tua & WA</th>
                    <th>Token QR</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $siswa)
                <tr>
                    <td>
                        <span class="fw-bold d-block">{{ $siswa->nisn }}</span>
                        <small class="text-muted">NIS: {{ $siswa->nis ?? '-' }}</small>
                    </td>
                    <td class="fw-semibold">{{ $siswa->nama }}</td>
                    <td>{{ $siswa->jenis_kelamin }}</td>
                    <td><span class="badge bg-info text-dark">{{ $siswa->kelas->nama_kelas ?? '-' }}</span></td>
                    <td>
                        <span class="d-block">{{ $siswa->orangTua->nama_ayah ?? $siswa->orangTua->nama_ibu ?? '-' }}</span>
                        <small class="text-success"><i class="fa-brands fa-whatsapp"></i> {{ $siswa->orangTua->no_wa ?? '-' }}</small>
                    </td>
                    <td><code>{{ $siswa->qr_code_token }}</code></td>
                    <td class="text-center">
                        <a href="{{ route('admin.siswa.card', $siswa->id) }}" target="_blank" class="btn btn-sm btn-success rounded-pill me-1" title="Cetak Kartu QR">
                            <i class="fa-solid fa-id-card"></i> Cetak Kartu
                        </a>
                        <form action="{{ route('admin.siswa.destroy', $siswa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data siswa ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada data siswa. Silakan tambahkan siswa baru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $siswas->links() }}
    </div>
</div>

<!-- Modal Tambah Siswa -->
<div class="modal fade" id="modalAddSiswa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.siswa.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NISN</label>
                        <input type="text" name="nisn" class="form-control" required placeholder="Contoh: 0081234567">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIS</label>
                        <input type="text" name="nis" class="form-control" placeholder="Contoh: 23241001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Siswa</label>
                        <input type="text" name="nama" class="form-control" required placeholder="Nama Siswa">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L">Laki-laki (L)</option>
                            <option value="P">Perempuan (P)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kelas</label>
                        <select name="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelases as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Orang Tua / Wali (Kontak WA)</label>
                        <select name="orang_tua_id" class="form-select" required>
                            <option value="">-- Pilih Orang Tua --</option>
                            @foreach($orangTuas as $ot)
                                <option value="{{ $ot->id }}">{{ $ot->nama_ayah ?? $ot->nama_ibu }} (WA: {{ $ot->no_wa }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
