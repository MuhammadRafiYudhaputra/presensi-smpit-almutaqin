@extends('layouts.app')

@section('content')
<style>
    /* Table Styling Selaras dengan Tema Website */
    .table-presensi-minimal {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table-presensi-minimal thead th {
        color: #334155;
        font-weight: 700;
        font-size: 0.88rem;
        border-bottom: 2px solid #e2e8f0;
        padding: 0.9rem 1rem;
        background: #f8fafc;
        letter-spacing: 0.3px;
    }
    .table-presensi-minimal tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.88rem;
    }
    .table-presensi-minimal tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Metric Summary Styles (Selaras dengan Dashboard) */
    .stat-metric-box {
        text-align: center;
        padding: 0.4rem 0.25rem;
    }
    .stat-metric-label {
        font-size: 0.78rem;
        font-weight: 700;
        margin-bottom: 2px;
        display: block;
    }
    .stat-metric-val {
        font-size: 1.4rem;
        font-weight: 800;
        line-height: 1;
        margin: 0;
    }
    .text-hadir { color: #16a34a !important; }
    .text-terlambat { color: #d97706 !important; }
    .text-sakit { color: #475569 !important; }
    .text-izin { color: #0284c7 !important; }
    .text-alpa { color: #dc2626 !important; }
    .text-total { color: #7c3aed !important; }
    .text-belum { color: #64748b !important; }
</style>

<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header Title & Cetak Absensi Harian -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center flex-wrap">
                <i class="fa-solid fa-list-check text-primary me-2 fs-4"></i> Absensi Siswa
                @if($kelas)
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-1 rounded-pill fs-6 ms-2">
                        <i class="fa-solid fa-chalkboard-user me-1"></i> Kelas {{ $kelas->nama_kelas }}
                    </span>
                @endif
            </h5>
            <small class="text-muted">Daftar presensi harian seluruh siswa kelas binaan, jam masuk/pulang, dan keterangan kehadiran</small>
        </div>

        <div class="d-flex gap-2 align-items-center flex-nowrap flex-shrink-0">
            <!-- Tombol Cetak Absensi Harian Sesuai Tanggal Terpilih -->
            <a href="{{ route('admin.rekap.cetak', ['mode' => 'harian', 'tanggal' => $tanggal, 'kelas_id' => $kelas ? $kelas->id : null]) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold shadow-sm btn-sm text-nowrap d-inline-flex align-items-center gap-1.5">
                <i class="fa-solid fa-print me-1"></i> Cetak Absensi Harian
            </a>
        </div>
    </div>

    <!-- Form Filter Tanggal & Sorting (Tanpa Filter Kelas) -->
    <form action="{{ route('guru.monitoring') }}" method="GET" class="row g-3 mb-4 align-items-end">
        <div class="col-md-7">
            <label class="form-label fw-bold text-dark mb-1">Pilih Tanggal Presensi</label>
            <input type="date" name="tanggal" class="form-control shadow-sm" value="{{ $tanggal }}" onchange="this.form.submit()">
        </div>
        <div class="col-md-5">
            <label class="form-label fw-bold text-dark mb-1">Urutkan Data (Sorting)</label>
            <select name="sort_by" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="nama_asc" {{ ($sortBy ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama Siswa (A-Z)</option>
                <option value="nama_desc" {{ ($sortBy ?? '') === 'nama_desc' ? 'selected' : '' }}>Nama Siswa (Z-A)</option>
                <option value="nisn" {{ ($sortBy ?? '') === 'nisn' ? 'selected' : '' }}>NISN Siswa</option>
            </select>
        </div>
    </form>

    <!-- Ringkasan Cepat Hari Ini (Selaras dengan Halaman Dashboard) -->
    <div class="p-3 bg-light rounded-3 mb-4 border">
        <div class="row g-2 justify-content-around align-items-center text-center">
            <div class="col-6 col-sm-3 col-md-auto stat-metric-box px-3">
                <span class="stat-metric-label text-total">Total Siswa</span>
                <h5 class="stat-metric-val text-total">{{ $summary['total'] }}</h5>
            </div>
            <div class="col-6 col-sm-3 col-md-auto stat-metric-box px-3">
                <span class="stat-metric-label text-hadir">Hadir</span>
                <h5 class="stat-metric-val text-hadir">{{ $summary['hadir'] }}</h5>
            </div>
            <div class="col-6 col-sm-3 col-md-auto stat-metric-box px-3">
                <span class="stat-metric-label text-terlambat">Terlambat</span>
                <h5 class="stat-metric-val text-terlambat">{{ $summary['terlambat'] }}</h5>
            </div>
            <div class="col-6 col-sm-3 col-md-auto stat-metric-box px-3">
                <span class="stat-metric-label text-sakit">Sakit</span>
                <h5 class="stat-metric-val text-sakit">{{ $summary['sakit'] }}</h5>
            </div>
            <div class="col-6 col-sm-3 col-md-auto stat-metric-box px-3">
                <span class="stat-metric-label text-izin">Izin</span>
                <h5 class="stat-metric-val text-izin">{{ $summary['izin'] }}</h5>
            </div>
            <div class="col-6 col-sm-3 col-md-auto stat-metric-box px-3">
                <span class="stat-metric-label text-alpa">Alfa</span>
                <h5 class="stat-metric-val text-alpa">{{ $summary['alpa'] }}</h5>
            </div>
            <div class="col-6 col-sm-3 col-md-auto stat-metric-box px-3">
                <span class="stat-metric-label text-belum">Belum Absen</span>
                <h5 class="stat-metric-val text-belum">{{ $summary['belum'] }}</h5>
            </div>
        </div>
    </div>

    <!-- Table Absensi Harian (Format Bersih Sesuai Tema Website Tanpa Kolom Aksi) -->
    <div class="table-responsive">
        <table class="table-presensi-minimal align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th style="width: 140px;">NISN / NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 160px; text-align: center;">Kehadiran</th>
                    <th style="width: 140px; text-align: center;">Jam masuk</th>
                    <th style="width: 140px; text-align: center;">Jam pulang</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($harianData as $idx => $row)
                <tr>
                    <td class="text-muted fw-semibold">{{ $idx + 1 }}</td>
                    <td class="text-dark fw-bold">{{ $row->siswa->nisn }}</td>
                    <td class="fw-bold text-dark">{{ $row->siswa->nama }}</td>
                    <td class="text-center">
                        @if($row->status === 'HADIR')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill fw-bold">
                                <i class="fa-solid fa-circle-check me-1"></i> Hadir
                            </span>
                        @elseif($row->status === 'TERLAMBAT')
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-1 rounded-pill fw-bold" style="color: #92400e !important;">
                                <i class="fa-solid fa-clock me-1 text-warning"></i> Terlambat
                            </span>
                        @elseif($row->status === 'IZIN')
                            <span class="badge bg-info bg-opacity-10 text-primary border border-info px-3 py-1 rounded-pill fw-bold">
                                <i class="fa-solid fa-envelope-open me-1"></i> Izin
                            </span>
                        @elseif($row->status === 'SAKIT')
                            <span class="badge bg-secondary bg-opacity-10 text-dark border border-secondary px-3 py-1 rounded-pill fw-bold">
                                <i class="fa-solid fa-notes-medical me-1"></i> Sakit
                            </span>
                        @elseif($row->status === 'ALPA')
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 rounded-pill fw-bold">
                                <i class="fa-solid fa-xmark me-1"></i> Alpa
                            </span>
                        @else
                            <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill fw-semibold">
                                <i class="fa-regular fa-circle me-1"></i> Belum
                            </span>
                        @endif
                    </td>
                    <td class="text-center text-muted font-monospace">{{ $row->jam_masuk ?? '-' }}</td>
                    <td class="text-center text-muted font-monospace">{{ $row->jam_pulang ?? '-' }}</td>
                    <td class="text-muted">{{ $row->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="fa-solid fa-folder-open fs-2 d-block mb-2 text-muted"></i>
                        Tidak ada data siswa ditemukan untuk kelas ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
