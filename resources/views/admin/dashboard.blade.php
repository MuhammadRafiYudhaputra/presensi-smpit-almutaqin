@extends('layouts.app')

@section('content')
<!-- Metric Cards Row 1: Kehadiran Hari Ini -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-success shadow-sm rounded-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Hadir Tepat Waktu</span>
                    <h3 class="fw-bold mb-0 text-success">{{ $totalHadir }}</h3>
                    <small class="text-muted">Presensi Hari Ini</small>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                    <i class="fa-solid fa-circle-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-warning shadow-sm rounded-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Hadir Terlambat</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ $totalTerlambat }}</h3>
                    <small class="text-muted">Presensi Hari Ini</small>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                    <i class="fa-solid fa-clock-rotate-left fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-info shadow-sm rounded-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Izin & Sakit</span>
                    <h3 class="fw-bold mb-0 text-info">{{ $totalIzinSakit }}</h3>
                    <small class="text-muted">Presensi Hari Ini</small>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                    <i class="fa-solid fa-envelope-open-text fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-danger shadow-sm rounded-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold">Belum / Alpa</span>
                    <h3 class="fw-bold mb-0 text-danger">{{ $totalAlpa }}</h3>
                    <small class="text-muted">Presensi Hari Ini</small>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger">
                    <i class="fa-solid fa-circle-xmark fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Metric Cards Row 2: Master Data Counts -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 shadow-sm rounded-4 border-0">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary me-3">
                    <i class="fa-solid fa-user-graduate fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold">Siswa Aktif</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $totalSiswa }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 shadow-sm rounded-4 border-0">
            <div class="d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning me-3">
                    <i class="fa-solid fa-graduation-cap fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold">Arsip Alumni</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $totalAlumni ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 shadow-sm rounded-4 border-0">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success me-3">
                    <i class="fa-solid fa-chalkboard-user fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold">Guru / Wali Kelas</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $totalGuru }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 shadow-sm rounded-4 border-0">
            <div class="d-flex align-items-center">
                <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info me-3">
                    <i class="fa-solid fa-school fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold">Rombel Kelas</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $totalKelas }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Live Table Presensi Terbaru -->
<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Aktivitas Presensi Terkini</h5>
        <a href="{{ route('admin.rekap.monitoring') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
            Lihat Monitoring Lengkap <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Waktu Scan</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPresensi as $p)
                <tr>
                    <td class="text-muted small">{{ $p->updated_at ? $p->updated_at->format('H:i:s') : '-' }}</td>
                    <td class="fw-semibold text-dark">{{ $p->siswa->nama ?? 'Siswa tidak ditemukan' }}</td>
                    <td><span class="badge bg-light text-dark border">Kelas {{ $p->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                    <td><span class="badge bg-light text-dark border">{{ $p->jam_masuk ?? '-' }}</span></td>
                    <td><span class="badge bg-light text-dark border">{{ $p->jam_pulang ?? '-' }}</span></td>
                    <td class="text-center">
                        @if($p->status === 'HADIR')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill">HADIR</span>
                        @elseif($p->status === 'TERLAMBAT')
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-1 rounded-pill">TERLAMBAT</span>
                        @elseif($p->status === 'IZIN')
                            <span class="badge bg-info bg-opacity-10 text-dark border border-info px-3 py-1 rounded-pill">IZIN</span>
                        @elseif($p->status === 'SAKIT')
                            <span class="badge bg-secondary bg-opacity-10 text-dark border border-secondary px-3 py-1 rounded-pill">SAKIT</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 rounded-pill">ALPA</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada aktivitas presensi pada hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
