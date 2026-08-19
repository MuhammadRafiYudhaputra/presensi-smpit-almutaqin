@extends('layouts.app')

@section('content')
<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header -->
    <div class="mb-4">
        <h5 class="fw-bold mb-1 text-dark d-flex align-items-center flex-wrap">
            <i class="fa-solid fa-rotate-left text-primary me-2 fs-4"></i> Live Monitoring Kehadiran
            @if($kelas)
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-1 rounded-pill fs-6 ms-2">
                    <i class="fa-solid fa-chalkboard-user me-1"></i> Kelas {{ $kelas->nama_kelas }}
                </span>
            @endif
        </h5>
        <small class="text-muted">Pantau aktivitas scan presensi siswa kelas binaan secara real-time</small>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('guru.monitoring') }}" class="row g-3 mb-4 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark mb-1">Pilih Tanggal Presensi</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control shadow-sm" onchange="this.form.submit()">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark mb-1">Pilih Kelas Binaan</label>
            <select name="kelas_id" class="form-select shadow-sm" onchange="this.form.submit()">
                @foreach($allKelases as $k)
                    <option value="{{ $k->id }}" {{ ($kelas && $kelas->id == $k->id) ? 'selected' : '' }}>
                        Kelas {{ $k->nama_kelas }} {{ ($k->waliKelas ? '('.$k->waliKelas->nama.')' : '') }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light">
                <tr>
                    <th class="text-dark fw-bold" style="width: 130px;">Waktu Scan</th>
                    <th class="text-dark fw-bold">Nama Siswa</th>
                    <th class="text-dark fw-bold" style="width: 110px;">Kelas</th>
                    <th class="text-dark fw-bold" style="width: 130px;">Jam Absensi</th>
                    <th class="text-dark fw-bold" style="width: 160px;">Status Presensi</th>
                    <th class="text-dark fw-bold" style="width: 180px;">Status Notifikasi WA</th>
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
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-circle-check me-1"></i> HADIR</span>
                        @elseif($kh->status == 'TERLAMBAT')
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-2 rounded-pill fw-bold" style="color: #92400e !important;"><i class="fa-solid fa-clock me-1 text-warning"></i> TERLAMBAT</span>
                        @elseif($kh->status == 'IZIN')
                            <span class="badge bg-info bg-opacity-10 text-primary border border-info px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-envelope-open me-1"></i> IZIN</span>
                        @elseif($kh->status == 'SAKIT')
                            <span class="badge bg-secondary bg-opacity-10 text-dark border border-secondary px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-notes-medical me-1"></i> SAKIT</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-circle-xmark me-1"></i> ALPA</span>
                        @endif
                    </td>
                    <td>
                        @if($kh->wa_masuk_sent)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1"><i class="fa-brands fa-whatsapp me-1"></i> Terkirim ke Ortu</span>
                        @else
                            <span class="badge bg-light text-muted border px-2 py-1"><i class="fa-solid fa-hourglass-half me-1"></i> Antrian</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="fa-solid fa-folder-open fs-2 d-block mb-2 text-muted"></i>
                        Tidak ada aktivitas presensi untuk kelas ini pada tanggal terpilih.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-end">
        {{ $kehadirans->appends(request()->query())->links() }}
    </div>
</div>
@endsection
