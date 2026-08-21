@extends('layouts.app')

@section('content')
<style>
    .table-siswa-minimal {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table-siswa-minimal thead th {
        color: #334155;
        font-weight: 700;
        font-size: 0.88rem;
        border-bottom: 2px solid #e2e8f0;
        padding: 0.9rem 1rem;
        background: #f8fafc;
        letter-spacing: 0.3px;
    }
    .table-siswa-minimal tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.88rem;
    }
    .table-siswa-minimal tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Action buttons square/rounded */
    .btn-action-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 0.82rem;
        transition: transform 0.15s ease, opacity 0.15s ease;
    }
    .btn-action-icon:hover {
        transform: translateY(-1px);
        opacity: 0.9;
    }
</style>

<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header & Action Buttons (Sesuai Referensi Gambar) -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center">
                <i class="fa-solid fa-users text-primary me-2 fs-4"></i> Data Siswa
            </h5>
            <small class="text-muted">Kelola data seluruh siswa, kelas, kontak orang tua, dan cetak kartu QR presensi</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-primary px-3 py-2 fw-semibold shadow-sm rounded-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalAddSiswa">
                <i class="fa-solid fa-plus"></i> TAMBAH DATA SISWA
            </button>
            <button type="button" class="btn btn-outline-primary px-3 py-2 fw-semibold shadow-sm rounded-3 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalImportDapodik">
                <i class="fa-solid fa-file-import"></i> IMPORT CSV / EXCEL
            </button>
        </div>
    </div>

    <!-- Status Filter Tabs (Siswa Aktif vs Arsip Alumni vs Semua Data) -->
    <div class="d-flex align-items-center mb-4 flex-wrap gap-2">
        <div class="btn-group p-1 bg-light rounded-pill border" role="group">
            <a href="{{ route('admin.siswa.index', ['status' => 'aktif', 'search' => $search, 'kelas_id' => $kelasId, 'sort_by' => $sortBy]) }}" class="btn btn-sm rounded-pill {{ ($status ?? 'aktif') === 'aktif' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                <i class="fa-solid fa-user me-1"></i> Siswa Aktif
                <span class="badge {{ ($status ?? 'aktif') === 'aktif' ? 'bg-white text-primary' : 'bg-secondary text-white' }} rounded-circle ms-1">{{ $countAktif }}</span>
            </a>
            <a href="{{ route('admin.siswa.index', ['status' => 'alumni', 'search' => $search, 'kelas_id' => $kelasId, 'sort_by' => $sortBy]) }}" class="btn btn-sm rounded-pill {{ ($status ?? '') === 'alumni' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                <i class="fa-solid fa-graduation-cap text-warning me-1"></i> Arsip Alumni
                <span class="badge bg-light text-dark rounded-circle ms-1">{{ $countAlumni }}</span>
            </a>
            <a href="{{ route('admin.siswa.index', ['status' => 'semua', 'search' => $search, 'kelas_id' => $kelasId, 'sort_by' => $sortBy]) }}" class="btn btn-sm rounded-pill {{ ($status ?? '') === 'semua' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                <i class="fa-solid fa-users text-secondary me-1"></i> Semua Data
                <span class="badge bg-secondary text-white rounded-circle ms-1">{{ $countSemua }}</span>
            </a>
        </div>
    </div>

    <!-- Search, Filter & Sorting Bar -->
    <form action="{{ route('admin.siswa.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
        <input type="hidden" name="status" value="{{ $status ?? 'aktif' }}">
        
        <!-- Search Input -->
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari NISN, Nama Siswa, atau Kontak WA..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-primary px-4 fw-semibold">Cari</button>
            </div>
        </div>

        <!-- Filter Kelas -->
        <div class="col-md-3">
            <div class="d-flex align-items-center gap-2">
                <label class="form-label fw-bold text-nowrap mb-0 text-dark"><i class="fa-solid fa-filter text-primary me-1"></i> Kelas:</label>
                <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($kelases as $k)
                        <option value="{{ $k->id }}" {{ ($kelasId ?? '') == $k->id ? 'selected' : '' }}>Kelas {{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Sorting -->
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-2">
                <label class="form-label fw-bold text-nowrap mb-0 text-dark"><i class="fa-solid fa-arrow-down-up-across-line text-primary me-1"></i> Urutkan:</label>
                <select name="sort_by" class="form-select" onchange="this.form.submit()">
                    <option value="nama_asc" {{ ($sortBy ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama Siswa (A-Z)</option>
                    <option value="nama_desc" {{ ($sortBy ?? '') === 'nama_desc' ? 'selected' : '' }}>Nama Siswa (Z-A)</option>
                    <option value="nisn" {{ ($sortBy ?? '') === 'nisn' ? 'selected' : '' }}>NISN Siswa</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Tabel Data Siswa (Format Bersih Sesuai Referensi) -->
    <div class="table-responsive">
        <table class="table-siswa-minimal align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 130px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 140px;">Kelas</th>
                    <th style="width: 180px;">No HP</th>
                    <th style="width: 130px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $idx => $siswa)
                <tr>
                    <td class="text-muted fw-semibold">{{ $siswas->firstItem() + $idx }}</td>
                    <td class="text-dark fw-bold">{{ $siswa->nisn }}</td>
                    <td>
                        <span class="fw-bold text-dark d-block">{{ $siswa->nama }}</span>
                        @if($siswa->nis)
                            <small class="text-muted">NIS: {{ $siswa->nis }}</small>
                        @endif
                    </td>
                    <td>
                        @if(($siswa->status ?? '') === 'alumni')
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-2 py-1">
                                <i class="fa-solid fa-graduation-cap me-1"></i> Alumni
                            </span>
                        @else
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1">
                                Kelas {{ $siswa->kelas->nama_kelas ?? '-' }}
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($siswa->orangTua && $siswa->orangTua->no_wa)
                            <span class="text-dark fw-semibold d-block">{{ $siswa->orangTua->no_wa }}</span>
                            <small class="text-muted">({{ $siswa->orangTua->nama_ayah ?? $siswa->orangTua->nama_ibu ?? 'Wali' }})</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex align-items-center gap-1">
                            <!-- Tombol Edit Siswa -->
                            <button type="button" class="btn btn-primary btn-action-icon" title="Edit Data Siswa" onclick="openEditSiswaModal({{ json_encode($siswa) }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            
                            <!-- Tombol Hapus Siswa -->
                            <form action="{{ route('admin.siswa.destroy', $siswa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-action-icon" title="Hapus Siswa">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                            <!-- Tombol Cetak Kartu QR -->
                            <a href="{{ route('admin.siswa.card', $siswa->id) }}" target="_blank" class="btn btn-success btn-action-icon" title="Cetak Kartu QR Siswa">
                                <i class="fa-solid fa-qrcode"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="fa-solid fa-user-slash fs-2 d-block mb-2 text-muted"></i>
                        Tidak ada data siswa yang sesuai dengan filter pencarian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <small class="text-muted">Menampilkan {{ $siswas->firstItem() ?? 0 }} - {{ $siswas->lastItem() ?? 0 }} dari total {{ $siswas->total() }} siswa</small>
        {{ $siswas->links() }}
    </div>
</div>

<!-- Modal Tambah Siswa Baru -->
<div class="modal fade" id="modalAddSiswa" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Tambah Siswa Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.siswa.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">NISN <span class="text-danger">*</span></label>
                            <input type="text" name="nisn" class="form-control" placeholder="10 Digit NISN" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">NIS (Nomor Induk)</label>
                            <input type="text" name="nis" class="form-control" placeholder="Nomor Induk Sekolah">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-dark">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap Siswa" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Kelas / Rombel <span class="text-danger">*</span></label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelases as $k)
                                    <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Orang Tua / Wali <span class="text-danger">*</span></label>
                            <select name="orang_tua_id" class="form-select" required>
                                <option value="">-- Pilih Data Orang Tua --</option>
                                @foreach($orangTuas as $ot)
                                    <option value="{{ $ot->id }}">{{ $ot->nama_ayah ?? $ot->nama_ibu }} (WA: {{ $ot->no_wa }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Simpan Data Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Siswa -->
<div class="modal fade" id="modalEditSiswa" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-user-pen me-2 text-primary"></i>Edit Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditSiswa" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">NISN <span class="text-danger">*</span></label>
                            <input type="text" name="nisn" id="edit_nisn" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">NIS</label>
                            <input type="text" name="nis" id="edit_nis" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-dark">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" id="edit_jenis_kelamin" class="form-select" required>
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Kelas / Rombel <span class="text-danger">*</span></label>
                            <select name="kelas_id" id="edit_kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelases as $k)
                                    <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Orang Tua / Wali <span class="text-danger">*</span></label>
                            <select name="orang_tua_id" id="edit_orang_tua_id" class="form-select" required>
                                <option value="">-- Pilih Data Orang Tua --</option>
                                @foreach($orangTuas as $ot)
                                    <option value="{{ $ot->id }}">{{ $ot->nama_ayah ?? $ot->nama_ibu }} (WA: {{ $ot->no_wa }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Data Dapodik -->
<div class="modal fade" id="modalImportDapodik" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-file-import me-2 text-primary"></i>Import Data Siswa dari Dapodik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.siswa.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info border-info small mb-3">
                        <i class="fa-solid fa-circle-info me-1"></i> Unggah file data siswa format Excel (.xlsx / .csv). Sistem akan otomatis membuat akun siswa & generate token QR Code presensi.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Pilih File Excel / CSV Dapodik:</label>
                        <input type="file" name="file_dapodik" class="form-control" accept=".csv, .xlsx, .xls">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Unggah & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditSiswaModal(siswa) {
    document.getElementById('edit_nisn').value = siswa.nisn;
    document.getElementById('edit_nis').value = siswa.nis || '';
    document.getElementById('edit_nama').value = siswa.nama;
    document.getElementById('edit_jenis_kelamin').value = siswa.jenis_kelamin;
    document.getElementById('edit_kelas_id').value = siswa.kelas_id || '';
    document.getElementById('edit_orang_tua_id').value = siswa.orang_tua_id || '';
    document.getElementById('formEditSiswa').action = `/admin/siswa/${siswa.id}`;
    const modal = new bootstrap.Modal(document.getElementById('modalEditSiswa'));
    modal.show();
}
</script>
@endsection
