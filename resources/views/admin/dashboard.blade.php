@extends('layouts.app')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat Cards -->
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-primary border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted fw-semibold">Total Siswa</small>
                    <h3 class="fw-bold m-0 text-primary">{{ $totalSiswa }}</h3>
                </div>
                <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                    <i class="fa-solid fa-user-graduate fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-success border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted fw-semibold">Hadir Hari Ini</small>
                    <h3 class="fw-bold m-0 text-success">{{ $totalHadir }}</h3>
                </div>
                <div class="bg-success-subtle p-3 rounded-circle text-success">
                    <i class="fa-solid fa-user-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-warning border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted fw-semibold">Terlambat Hari Ini</small>
                    <h3 class="fw-bold m-0 text-warning">{{ $totalTerlambat }}</h3>
                </div>
                <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                    <i class="fa-solid fa-user-clock fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-danger border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted fw-semibold">Belum Presensi (Alpa)</small>
                    <h3 class="fw-bold m-0 text-danger">{{ $totalAlpa }}</h3>
                </div>
                <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                    <i class="fa-solid fa-user-xmark fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-stream text-primary me-2"></i>Aktivitas Presensi Terakhir Hari Ini</h6>
        <a href="{{ route('admin.rekap.monitoring') }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                    <th>Notifikasi WA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPresensi as $item)
                <tr>
                    <td class="fw-semibold">{{ $item->siswa->nama ?? '-' }}</td>
                    <td><span class="badge bg-secondary">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                    <td>{{ $item->jam_masuk ?? '-' }}</td>
                    <td>{{ $item->jam_pulang ?? '-' }}</td>
                    <td>
                        @if($item->status == 'HADIR')
                            <span class="badge bg-success">HADIR</span>
                        @elseif($item->status == 'TERLAMBAT')
                            <span class="badge bg-warning text-dark">TERLAMBAT</span>
                        @else
                            <span class="badge bg-danger">{{ $item->status }}</span>
                        @endif
                    </td>
                    <td>
                        @if($item->wa_masuk_sent)
                            <span class="text-success"><i class="fa-brands fa-whatsapp"></i> Terkirim</span>
                        @else
                            <span class="text-muted"><i class="fa-regular fa-clock"></i> Pending / Queue</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada aktivitas presensi hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
