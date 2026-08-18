@extends('layouts.app')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-file-invoice me-2 text-primary"></i>Rekapitulasi Kehadiran Bulanan</h5>
            <small class="text-muted">Ringkasan total Hadir, Terlambat, Izin, Sakit, dan Alpa</small>
        </div>
        <a href="{{ route('admin.rekap.cetak', ['bulan' => $bulan, 'tahun' => $tahun, 'kelas_id' => $kelasId]) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-4">
            <i class="fa-solid fa-print me-1"></i> Cetak / Export Laporan
        </a>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.rekap.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label fw-semibold">Pilih Bulan</label>
            <select name="bulan" class="form-select" onchange="this.form.submit()">
                @for($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Pilih Tahun</label>
            <select name="tahun" class="form-select" onchange="this.form.submit()">
                @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
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
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th rowspan="2" class="align-middle">No</th>
                    <th rowspan="2" class="align-middle text-start">Nama Siswa</th>
                    <th rowspan="2" class="align-middle">Kelas</th>
                    <th colspan="5">Akumulasi Kehadiran (Bulan {{ $bulan }}/{{ $tahun }})</th>
                </tr>
                <tr>
                    <th class="text-success">Hadir</th>
                    <th class="text-warning">Terlambat</th>
                    <th class="text-info">Izin</th>
                    <th class="text-primary">Sakit</th>
                    <th class="text-danger">Alpa</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapData as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-semibold">{{ $row['siswa']->nama }}</td>
                    <td class="text-center"><span class="badge bg-secondary">{{ $row['siswa']->kelas->nama_kelas ?? '-' }}</span></td>
                    <td class="text-center fw-bold text-success">{{ $row['hadir'] }}</td>
                    <td class="text-center fw-bold text-warning">{{ $row['terlambat'] }}</td>
                    <td class="text-center fw-bold text-info">{{ $row['izin'] }}</td>
                    <td class="text-center fw-bold text-primary">{{ $row['sakit'] }}</td>
                    <td class="text-center fw-bold text-danger">{{ $row['alpa'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Data siswa tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
