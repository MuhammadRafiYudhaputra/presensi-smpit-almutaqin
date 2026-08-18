@extends('layouts.app')

@section('content')
<!-- Metric Cards Row (4 Cards) -->
<div class="row g-3 mb-4">
    <!-- Siswa Aktif -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-primary rounded-4 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">Siswa Aktif (7, 8, 9)</span>
                    <h2 class="fw-bold mb-1 text-primary">{{ $totalSiswa }}</h2>
                    <small class="text-muted"><i class="fa-solid fa-graduation-cap me-1"></i>Arsip Alumni: {{ $totalAlumni ?? 0 }}</small>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="fa-solid fa-user-graduate fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Hadir Hari Ini -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-success rounded-4 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">Hadir Hari Ini</span>
                    <h2 class="fw-bold mb-1 text-success">{{ $totalHadir }}</h2>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="fa-solid fa-user-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Terlambat Hari Ini -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-warning rounded-4 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">Terlambat Hari Ini</span>
                    <h2 class="fw-bold mb-1 text-warning">{{ $totalTerlambat }}</h2>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="fa-solid fa-user-clock fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Belum Presensi (Alpa) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-danger rounded-4 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">Belum Presensi (Alpa)</span>
                    <h2 class="fw-bold mb-1 text-danger">{{ $totalAlpa }}</h2>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="fa-solid fa-user-xmark fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Card: Aktivitas Presensi Terakhir Hari Ini -->
<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
            <i class="fa-solid fa-bars-staggered text-primary me-2"></i> Aktivitas Presensi Terakhir Hari Ini
        </h6>
        <a href="{{ route('admin.rekap.monitoring') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
            Lihat Semua
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-dark fw-bold">Siswa</th>
                    <th class="text-dark fw-bold">Kelas</th>
                    <th class="text-dark fw-bold">Jam Absensi</th>
                    <th class="text-dark fw-bold">Status</th>
                    <th class="text-dark fw-bold">Notifikasi WA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPresensi as $p)
                <tr>
                    <td class="fw-semibold text-dark">{{ $p->siswa->nama ?? '-' }}</td>
                    <td><span class="badge bg-light text-dark border">Kelas {{ $p->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                    <td><span class="badge bg-light text-dark border"><i class="fa-regular fa-clock text-primary me-1"></i>{{ $p->jam_masuk ?? '-' }}</span></td>
                    <td>
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
                    <td>
                        @if($p->wa_masuk_sent)
                            <span class="badge bg-success-subtle text-success"><i class="fa-brands fa-whatsapp me-1"></i> Terkirim</span>
                        @else
                            <span class="badge bg-light text-muted">Antrian</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        Belum ada aktivitas presensi hari ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
