@extends('layouts.app')

@section('content')
<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header & Action Button -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-user-graduate me-2 text-primary"></i>Kelola Master Data Siswa</h5>
            <small class="text-muted">Manajemen data peserta didik, kartu presensi QR Code, dan arsip alumni</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAddSiswa">
                <i class="fa-solid fa-plus me-1"></i> Tambah Siswa Baru
            </button>
        </div>
    </div>

    <!-- Status Tabs Navigation (Aktif vs Alumni vs Semua) -->
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
        <div class="btn-group p-1 bg-light rounded-pill border" role="group">
            <a href="{{ route('admin.siswa.index', ['status' => 'aktif', 'search' => $search, 'kelas_id' => $kelasId, 'sort_by' => $sortBy]) }}" class="btn btn-sm rounded-pill {{ ($status ?? 'aktif') === 'aktif' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                <i class="fa-solid fa-user-check me-1"></i> Siswa Aktif
                <span class="badge bg-white text-primary ms-1 rounded-pill">{{ $countAktif }}</span>
            </a>
            <a href="{{ route('admin.siswa.index', ['status' => 'alumni', 'search' => $search, 'kelas_id' => $kelasId, 'sort_by' => $sortBy]) }}" class="btn btn-sm rounded-pill {{ ($status ?? '') === 'alumni' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                <i class="fa-solid fa-graduation-cap me-1 text-warning"></i> Arsip Alumni
                <span class="badge bg-white text-dark ms-1 rounded-pill">{{ $countAlumni }}</span>
            </a>
            <a href="{{ route('admin.siswa.index', ['status' => 'semua', 'search' => $search, 'kelas_id' => $kelasId, 'sort_by' => $sortBy]) }}" class="btn btn-sm rounded-pill {{ ($status ?? '') === 'semua' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                <i class="fa-solid fa-users me-1"></i> Semua Data
                <span class="badge bg-secondary text-white ms-1 rounded-pill">{{ $countSemua }}</span>
            </a>
        </div>
    </div>

    <!-- Filter, Search, & Sorting Bar -->
    <form action="{{ route('admin.siswa.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
        <input type="hidden" name="status" value="{{ $status ?? 'aktif' }}">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari NISN, Nama Siswa, atau NIK/NIS..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-primary px-3">Cari</button>
                @if(!empty($search) || !empty($kelasId))
                    <a href="{{ route('admin.siswa.index', ['status' => $status ?? 'aktif']) }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-flex align-items-center gap-2">
                <label class="form-label fw-semibold text-nowrap mb-0 text-dark"><i class="fa-solid fa-filter text-primary me-1"></i> Kelas:</label>
                <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($kelases as $k)
                        <option value="{{ $k->id }}" {{ ($kelasId ?? '') == $k->id ? 'selected' : '' }}>Kelas {{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-2">
                <label class="form-label fw-semibold text-nowrap mb-0 text-dark"><i class="fa-solid fa-arrow-down-a-z text-primary me-1"></i> Urutkan:</label>
                <select name="sort_by" class="form-select" onchange="this.form.submit()">
                    <option value="nama_asc" {{ ($sortBy ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama Siswa (A-Z)</option>
                    <option value="nama_desc" {{ ($sortBy ?? '') === 'nama_desc' ? 'selected' : '' }}>Nama Siswa (Z-A)</option>
                    <option value="nisn" {{ ($sortBy ?? '') === 'nisn' ? 'selected' : '' }}>NISN Siswa</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Tabel Data Siswa -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 140px;" class="text-dark">NISN / NIS</th>
                    <th class="text-dark">Nama Lengkap</th>
                    <th style="width: 50px;" class="text-center text-dark">JK</th>
                    <th class="text-dark">Kelas</th>
                    <th class="text-dark">Orang Tua & WA</th>
                    <th class="text-dark">Status</th>
                    <th class="text-center text-dark" style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $siswa)
                <tr>
                    <td>
                        <span class="fw-bold d-block text-dark">{{ $siswa->nisn }}</span>
                        <small class="text-muted">NIS: {{ $siswa->nis ?? '-' }}</small>
                    </td>
                    <td>
                        <span class="fw-bold text-dark">{{ $siswa->nama }}</span>
                    </td>
                    <td class="text-center">
                        @if($siswa->jenis_kelamin === 'L')
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2">L</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2">P</span>
                        @endif
                    </td>
                    <td>
                        @if(($siswa->status ?? '') === 'alumni')
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning">
                                <i class="fa-solid fa-graduation-cap me-1"></i> Alumni
                            </span>
                        @else
                            <span class="badge bg-info bg-opacity-10 text-dark border border-info">
                                Kelas {{ $siswa->kelas->nama_kelas ?? '-' }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="d-block text-dark fw-semibold">{{ $siswa->orangTua->nama_ayah ?? $siswa->orangTua->nama_ibu ?? '-' }}</span>
                        <small class="text-success"><i class="fa-brands fa-whatsapp me-1"></i>{{ $siswa->orangTua->no_wa ?? '-' }}</small>
                    </td>
                    <td>
                        @if(($siswa->status ?? '') === 'alumni')
                            <span class="badge bg-secondary">Alumni (Lulus)</span>
                        @else
                            <span class="badge bg-success">Aktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.siswa.card', $siswa->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2 me-1" title="Cetak Kartu QR">
                            <i class="fa-solid fa-id-card"></i> Kartu
                        </a>
                        <form action="{{ route('admin.siswa.destroy', $siswa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Hapus Siswa">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="fa-solid fa-folder-open fs-2 d-block mb-2 text-muted"></i>
                        Tidak ada data siswa yang sesuai dengan filter pencarian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-between align-items-center">
        <small class="text-muted">Menampilkan {{ $siswas->firstItem() ?? 0 }} - {{ $siswas->lastItem() ?? 0 }} dari total {{ $siswas->total() }} siswa</small>
        {{ $siswas->links() }}
    </div>
</div>

<!-- Modal Tambah Siswa -->
<div class="modal fade" id="modalAddSiswa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Tambah Siswa Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.siswa.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">NISN <span class="text-danger">*</span></label>
                        <input type="text" name="nisn" class="form-control" placeholder="10 Digit NISN" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">NIS</label>
                        <input type="text" name="nis" class="form-control" placeholder="Nomor Induk Sekolah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap Sesuai Akta" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L">Laki-laki (L)</option>
                            <option value="P">Perempuan (P)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Kelas / Rombel <span class="text-danger">*</span></label>
                        <select name="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelases as $k)
                                <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Orang Tua / Wali <span class="text-danger">*</span></label>
                        <select name="orang_tua_id" class="form-select" required>
                            <option value="">-- Pilih Data Orang Tua --</option>
                            @foreach($orangTuas as $ot)
                                <option value="{{ $ot->id }}">{{ $ot->nama_ayah ?? $ot->nama_ibu }} (WA: {{ $ot->no_wa }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Data Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
