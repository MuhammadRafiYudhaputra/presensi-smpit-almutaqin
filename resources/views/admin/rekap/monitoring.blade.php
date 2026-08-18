@extends('layouts.app')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Live Monitoring Kehadiran</h5>
            <small class="text-muted">Pantau aktivitas scan presensi siswa secara real-time</small>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.rekap.monitoring') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label fw-semibold">Pilih Tanggal</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control" onchange="this.form.submit()">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Filter Kelas</label>
            <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelases as $k)
                    <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>Kelas {{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Waktu Scan</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status Presensi</th>
                    <th>Status Notifikasi WA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kehadirans as $kh)
                <tr>
                    <td>{{ $kh->created_at->format('H:i:s') }}</td>
                    <td class="fw-semibold">{{ $kh->siswa->nama ?? '-' }}</td>
                    <td><span class="badge bg-secondary">{{ $kh->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                    <td><span class="text-success fw-bold">{{ $kh->jam_masuk ?? '-' }}</span></td>
                    <td><span class="text-primary fw-bold">{{ $kh->jam_pulang ?? '-' }}</span></td>
                    <td>
                        @if($kh->status == 'HADIR')
                            <span class="badge bg-success">HADIR</span>
                        @elseif($kh->status == 'TERLAMBAT')
                            <span class="badge bg-warning text-dark">TERLAMBAT</span>
                        @else
                            <span class="badge bg-danger">{{ $kh->status }}</span>
                        @endif
                    </td>
                    <td>
                        @if($kh->wa_masuk_sent)
                            <span class="badge bg-success-subtle text-success"><i class="fa-brands fa-whatsapp me-1"></i> WA Masuk Sent</span>
                        @else
                            <span class="badge bg-light text-muted">Waiting Queue</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Tidak ada data presensi pada tanggal/kelas terpilih.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $kehadirans->appends(request()->query())->links() }}
    </div>
</div>
@endsection
