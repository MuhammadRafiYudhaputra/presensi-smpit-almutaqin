@extends('layouts.app')

@section('content')
<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header -->
    <div class="mb-4">
        <h5 class="fw-bold mb-1 text-dark d-flex align-items-center flex-wrap">
            <i class="fa-solid fa-rotate-left text-primary me-2 fs-4"></i> Live Monitoring Kehadiran
            @if($kelas)
                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill fs-6 ms-2">
                    <i class="fa-solid fa-chalkboard-user me-1"></i> Kelas {{ $kelas->nama_kelas }}
                </span>
            @endif
        </h5>
        <small class="text-muted">Pantau aktivitas scan presensi siswa secara real-time</small>
    </div>

    <!-- Filter Form (Hanya Pilih Tanggal) -->
    <form method="GET" action="{{ route('guru.monitoring') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark mb-1">Pilih Tanggal</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control shadow-sm" onchange="this.form.submit()">
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light">
                <tr>
                    <th class="text-dark fw-bold">Waktu Scan</th>
                    <th class="text-dark fw-bold">Nama Siswa</th>
                    <th class="text-dark fw-bold">Kelas</th>
                    <th class="text-dark fw-bold">Jam Absensi</th>
                    <th class="text-dark fw-bold">Status Presensi</th>
                    <th class="text-dark fw-bold">Status Notifikasi WA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kehadirans as $kh)
                <tr>
                    <td class="text-muted"><i class="fa-regular fa-clock me-1 text-primary"></i>{{ $kh->created_at ? $kh->created_at->format('H:i:s') : '-' }}</td>
                    <td class="fw-bold text-dark">{{ $kh->siswa->nama ?? '-' }}</td>
                    <td><span class="badge bg-light text-dark border">Kelas {{ $kh->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                    <td><span class="badge bg-light text-dark border">{{ $kh->jam_masuk ?? '-' }}</span></td>
                    <td>
                        @if($kh->status == 'HADIR')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill fw-bold">HADIR</span>
                        @elseif($kh->status == 'TERLAMBAT')
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-1 rounded-pill fw-bold" style="color: #92400e !important;">TERLAMBAT</span>
                        @elseif($kh->status == 'IZIN')
                            <span class="badge bg-info bg-opacity-10 text-primary border border-info px-3 py-1 rounded-pill fw-bold">IZIN</span>
                        @elseif($kh->status == 'SAKIT')
                            <span class="badge bg-secondary bg-opacity-10 text-dark border border-secondary px-3 py-1 rounded-pill fw-bold">SAKIT</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 rounded-pill fw-bold">ALPA</span>
                        @endif
                    </td>
                    <td>
                        @if($kh->wa_masuk_sent)
                            <span class="badge bg-success-subtle text-success"><i class="fa-brands fa-whatsapp me-1"></i> Terkirim</span>
                        @else
                            <span class="badge bg-light text-muted">Antrian</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        Tidak ada data presensi pada tanggal terpilih.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $kehadirans->appends(request()->query())->links() }}
    </div>
</div>
@endsection
