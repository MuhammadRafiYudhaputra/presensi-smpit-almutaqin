@extends('layouts.app')

@section('content')
<style>
    .col-header-hadir { color: #15803d !important; font-weight: 700; }
    .col-header-terlambat { color: #b45309 !important; font-weight: 700; }
    .col-header-izin { color: #0369a1 !important; font-weight: 700; }
    .col-header-sakit { color: #334155 !important; font-weight: 700; }
    .col-header-alpa { color: #b91c1c !important; font-weight: 700; }

    .val-hadir { color: #15803d; font-weight: 800; font-size: 1rem; }
    .val-terlambat { color: #b45309; font-weight: 800; font-size: 1rem; }
    .val-izin { color: #0369a1; font-weight: 800; font-size: 1rem; }
    .val-sakit { color: #334155; font-weight: 800; font-size: 1rem; }
    .val-alpa { color: #b91c1c; font-weight: 800; font-size: 1rem; }
</style>

<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header & Mode Tabs -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center">
                <i class="fa-solid fa-file-invoice text-primary me-2 fs-4"></i> Rekapitulasi Kehadiran Siswa
            </h5>
            <small class="text-muted">Laporan presensi harian, rekapitulasi bulanan, dan semester berbasis Hari Efektif Sekolah</small>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <!-- Mode Switcher Tabs -->
            <div class="btn-group p-1 bg-light rounded-pill border" role="group">
                <a href="{{ route('admin.rekap.index', ['mode' => 'harian', 'tanggal' => $tanggal, 'kelas_id' => $kelasId, 'sort_by' => $sortBy]) }}" class="btn btn-sm rounded-pill px-3 fw-semibold {{ $mode === 'harian' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                    <i class="fa-solid fa-calendar-day me-1"></i> Harian
                </a>
                <a href="{{ route('admin.rekap.index', ['mode' => 'bulanan', 'bulan' => $bulan, 'tahun' => $tahun, 'hari_efektif' => $hariEfektif, 'kelas_id' => $kelasId, 'sort_by' => $sortBy]) }}" class="btn btn-sm rounded-pill px-3 fw-semibold {{ $mode === 'bulanan' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                    <i class="fa-solid fa-chart-simple me-1"></i> Bulanan
                </a>
                <a href="{{ route('admin.rekap.index', ['mode' => 'semester', 'semester' => $semester, 'tahun' => $tahun, 'hari_efektif' => $hariEfektif, 'kelas_id' => $kelasId, 'sort_by' => $sortBy]) }}" class="btn btn-sm rounded-pill px-3 fw-semibold {{ $mode === 'semester' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                    <i class="fa-solid fa-graduation-cap me-1"></i> Semester
                </a>
            </div>

            <!-- Tombol Cetak Laporan -->
            <a href="{{ route('admin.rekap.cetak', ['mode' => $mode, 'tanggal' => $tanggal, 'bulan' => $bulan, 'tahun' => $tahun, 'semester' => $semester, 'kelas_id' => $kelasId, 'hari_efektif' => $hariEfektif]) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-semibold shadow-sm">
                <i class="fa-solid fa-print me-1"></i> Cetak Laporan
            </a>
        </div>
    </div>

    <!-- Filter & Parameter Form -->
    <form action="{{ route('admin.rekap.index') }}" method="GET" class="row g-3 mb-4 align-items-end">
        <input type="hidden" name="mode" value="{{ $mode }}">

        @if($mode === 'harian')
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark mb-1">Pilih Tanggal Presensi</label>
                <input type="date" name="tanggal" class="form-control shadow-sm" value="{{ $tanggal }}" onchange="this.form.submit()">
            </div>
        @elseif($mode === 'bulanan')
            <div class="col-md-2">
                <label class="form-label fw-bold text-dark mb-1">Pilih Bulan</label>
                <select name="bulan" class="form-select shadow-sm" onchange="this.form.submit()">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold text-dark mb-1">Pilih Tahun</label>
                <select name="tahun" class="form-select shadow-sm" onchange="this.form.submit()">
                    @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold text-dark mb-1">Hari Efektif (Masuk)</label>
                <div class="input-group shadow-sm">
                    <input type="number" name="hari_efektif" class="form-control" value="{{ $hariEfektif }}" min="1" max="31" title="Jumlah hari efektif/masuk sekolah dalam bulan ini">
                    <span class="input-group-text bg-light text-muted small">Hari</span>
                </div>
            </div>
        @elseif($mode === 'semester')
            <div class="col-md-2">
                <label class="form-label fw-bold text-dark mb-1">Pilih Semester</label>
                <select name="semester" class="form-select shadow-sm" onchange="this.form.submit()">
                    <option value="ganjil" {{ $semester === 'ganjil' ? 'selected' : '' }}>Semester Ganjil (Jul - Des)</option>
                    <option value="genap" {{ $semester === 'genap' ? 'selected' : '' }}>Semester Genap (Jan - Jun)</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold text-dark mb-1">Tahun Ajaran</label>
                <select name="tahun" class="form-select shadow-sm" onchange="this.form.submit()">
                    @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}/{{ $y+1 }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold text-dark mb-1">Hari Efektif Semester</label>
                <div class="input-group shadow-sm">
                    <input type="number" name="hari_efektif" class="form-control" value="{{ $hariEfektif }}" min="1" max="180" title="Jumlah hari efektif sekolah semester ini">
                    <span class="input-group-text bg-light text-muted small">Hari</span>
                </div>
            </div>
        @endif

        <div class="col-md-3">
            <label class="form-label fw-bold text-dark mb-1">Filter Kelas</label>
            <select name="kelas_id" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelases as $k)
                    <option value="{{ $k->id }}" {{ ($kelasId ?? '') == $k->id ? 'selected' : '' }}>Kelas {{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold text-dark mb-1">Urutkan Data (Sorting)</label>
            <select name="sort_by" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="nama_asc" {{ ($sortBy ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama Siswa (A-Z)</option>
                <option value="nama_desc" {{ ($sortBy ?? '') === 'nama_desc' ? 'selected' : '' }}>Nama Siswa (Z-A)</option>
                <option value="nisn" {{ ($sortBy ?? '') === 'nisn' ? 'selected' : '' }}>NISN Siswa</option>
            </select>
        </div>

        @if($mode !== 'harian')
            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                    <i class="fa-solid fa-arrows-rotate me-1"></i> Terapkan Hari Efektif & Filter
                </button>
                <small class="text-muted ms-2">
                    <i class="fa-solid fa-circle-info text-primary me-1"></i>
                    Dasar persentase kehadiran dihitung dari <strong>{{ $hariEfektif }} Hari Efektif</strong>. Keterlambatan dicatat terpisah untuk catatan BK.
                </small>
            </div>
        @endif
    </form>

    <!-- 1. TAMPILAN TABEL MODE HARIAN -->
    @if($mode === 'harian')
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 50px;" class="text-dark">No</th>
                    <th class="text-dark" style="width: 120px;">NISN</th>
                    <th class="text-dark text-start">Nama Peserta Didik</th>
                    <th style="width: 50px;" class="text-dark">JK</th>
                    <th class="text-dark" style="width: 110px;">Kelas</th>
                    <th class="text-dark" style="width: 130px;">Jam Masuk (Pagi)</th>
                    <th class="text-dark" style="width: 130px;">Jam Pulang (Sore)</th>
                    <th class="text-dark" style="width: 180px;">Status Kehadiran Harian</th>
                    <th class="text-center text-dark" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($harianData as $idx => $row)
                <tr>
                    <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                    <td class="text-center fw-bold text-dark">{{ $row->siswa->nisn }}</td>
                    <td class="fw-bold text-dark">{{ $row->siswa->nama }}</td>
                    <td class="text-center">
                        <span class="badge {{ $row->siswa->jenis_kelamin === 'L' ? 'bg-primary bg-opacity-10 text-primary border border-primary' : 'bg-danger bg-opacity-10 text-danger border border-danger' }} px-2 fw-bold">{{ $row->siswa->jenis_kelamin }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 rounded-2">
                            Kelas {{ $row->siswa->kelas->nama_kelas ?? '-' }}
                        </span>
                    </td>
                    <td class="text-center text-muted">
                        @if($row->jam_masuk)
                            <span class="badge bg-light text-dark border px-2 py-1"><i class="fa-regular fa-clock text-primary me-1"></i>{{ $row->jam_masuk }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center text-muted">
                        @if($row->jam_pulang)
                            <span class="badge bg-light text-dark border px-2 py-1"><i class="fa-solid fa-door-open text-success me-1"></i>{{ $row->jam_pulang }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($row->status === 'HADIR')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-circle-check me-1"></i> HADIR</span>
                        @elseif($row->status === 'TERLAMBAT')
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-2 rounded-pill fw-bold" style="color: #92400e !important;"><i class="fa-solid fa-clock me-1 text-warning"></i> TERLAMBAT</span>
                        @elseif($row->status === 'IZIN')
                            <span class="badge bg-info bg-opacity-10 text-primary border border-info px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-envelope-open-text me-1"></i> IZIN</span>
                        @elseif($row->status === 'SAKIT')
                            <span class="badge bg-secondary bg-opacity-10 text-dark border border-secondary px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-notes-medical me-1"></i> SAKIT</span>
                        @elseif($row->status === 'ALPA')
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-xmark me-1"></i> ALPA</span>
                        @else
                            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fw-semibold"><i class="fa-regular fa-circle me-1"></i> BELUM ABSEN</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold shadow-sm" onclick="openSetStatusModal({{ $row->siswa->id }}, '{{ addslashes($row->siswa->nama) }}', '{{ $row->status }}')">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Set Status
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="fa-solid fa-folder-open fs-2 d-block mb-2 text-muted"></i>
                        Tidak ada data peserta didik ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 2. TAMPILAN TABEL MODE BULANAN -->
    @elseif($mode === 'bulanan')
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light text-center">
                <tr>
                    <th rowspan="2" style="width: 50px;" class="align-middle text-dark">No</th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 120px;">NISN</th>
                    <th rowspan="2" class="align-middle text-dark text-start">Nama Peserta Didik</th>
                    <th rowspan="2" style="width: 50px;" class="align-middle text-dark">JK</th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 110px;">Kelas</th>
                    <th colspan="5" class="text-dark bg-light">
                        Akumulasi Kehadiran (Bulan {{ $bulan }}/{{ $tahun }} &bull; Dasar: {{ $hariEfektif }} Hari Efektif)
                    </th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 110px;">Persentase</th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 140px;">Catatan BK</th>
                </tr>
                <tr>
                    <th class="col-header-hadir text-center py-2" style="width: 85px;" title="Total siswa masuk sekolah">
                        <i class="fa-solid fa-circle-check d-block mb-1 fs-6"></i>
                        <span>Hadir</span>
                    </th>
                    <th class="col-header-terlambat text-center py-2" style="width: 95px;" title="Dicatat terpisah untuk tindak lanjut BK">
                        <i class="fa-solid fa-clock d-block mb-1 fs-6"></i>
                        <span>Terlambat</span>
                    </th>
                    <th class="col-header-izin text-center py-2" style="width: 85px;">
                        <i class="fa-solid fa-envelope-open d-block mb-1 fs-6"></i>
                        <span>Izin</span>
                    </th>
                    <th class="col-header-sakit text-center py-2" style="width: 85px;">
                        <i class="fa-solid fa-notes-medical d-block mb-1 fs-6"></i>
                        <span>Sakit</span>
                    </th>
                    <th class="col-header-alpa text-center py-2" style="width: 85px;">
                        <i class="fa-solid fa-circle-xmark d-block mb-1 fs-6"></i>
                        <span>Alpa</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($bulananData as $idx => $row)
                <tr>
                    <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                    <td class="text-center fw-bold text-dark">{{ $row->siswa->nisn }}</td>
                    <td class="fw-bold text-dark">{{ $row->siswa->nama }}</td>
                    <td class="text-center">
                        <span class="badge {{ $row->siswa->jenis_kelamin === 'L' ? 'bg-primary bg-opacity-10 text-primary border border-primary' : 'bg-danger bg-opacity-10 text-danger border border-danger' }} px-2 fw-bold">{{ $row->siswa->jenis_kelamin }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 rounded-2">
                            Kelas {{ $row->siswa->kelas->nama_kelas ?? '-' }}
                        </span>
                    </td>
                    <td class="text-center val-hadir" title="{{ $row->hadir }} hari hadir">{{ $row->hadir }}</td>
                    <td class="text-center val-terlambat">
                        {{ $row->terlambat }}
                        @if($row->terlambat >= 3)
                            <span class="badge bg-warning bg-opacity-25 text-dark border border-warning ms-1" style="font-size: 0.7rem;" title="Perlu tindak lanjut BK">
                                <i class="fa-solid fa-triangle-exclamation"></i> BK
                            </span>
                        @endif
                    </td>
                    <td class="text-center val-izin">{{ $row->izin }}</td>
                    <td class="text-center val-sakit">{{ $row->sakit }}</td>
                    <td class="text-center val-alpa">{{ $row->alpa }}</td>
                    <td class="text-center">
                        <span class="badge {{ $row->persentase >= 85 ? 'bg-success' : ($row->persentase >= 75 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3 py-2 fw-bold">
                            {{ $row->persentase }}%
                        </span>
                    </td>
                    <td class="text-center">
                        @if($row->terlambat >= 3)
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-user-shield me-1"></i> Perlu Tindak Lanjut
                            </span>
                        @elseif($row->terlambat > 0)
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-2 py-1" style="font-size: 0.75rem; color: #92400e !important;">
                                <i class="fa-regular fa-clock me-1"></i> Catatan ({{ $row->terlambat }}x)
                            </span>
                        @else
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-check me-1"></i> Tertib
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center text-muted py-5">Tidak ada data rekapitulasi bulanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 3. TAMPILAN TABEL MODE SEMESTER -->
    @elseif($mode === 'semester')
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light text-center">
                <tr>
                    <th rowspan="2" style="width: 50px;" class="align-middle text-dark">No</th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 120px;">NISN</th>
                    <th rowspan="2" class="align-middle text-dark text-start">Nama Peserta Didik</th>
                    <th rowspan="2" style="width: 50px;" class="align-middle text-dark">JK</th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 110px;">Kelas</th>
                    <th colspan="5" class="text-dark bg-light">
                        Akumulasi Kehadiran (Semester {{ ucfirst($semester) }} {{ $tahun }} &bull; Dasar: {{ $hariEfektif }} Hari Efektif)
                    </th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 110px;">Persentase</th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 140px;">Catatan BK</th>
                </tr>
                <tr>
                    <th class="col-header-hadir text-center py-2" style="width: 85px;" title="Total siswa masuk sekolah">
                        <i class="fa-solid fa-circle-check d-block mb-1 fs-6"></i>
                        <span>Hadir</span>
                    </th>
                    <th class="col-header-terlambat text-center py-2" style="width: 95px;" title="Dicatat terpisah untuk tindak lanjut BK">
                        <i class="fa-solid fa-clock d-block mb-1 fs-6"></i>
                        <span>Terlambat</span>
                    </th>
                    <th class="col-header-izin text-center py-2" style="width: 85px;">
                        <i class="fa-solid fa-envelope-open d-block mb-1 fs-6"></i>
                        <span>Izin</span>
                    </th>
                    <th class="col-header-sakit text-center py-2" style="width: 85px;">
                        <i class="fa-solid fa-notes-medical d-block mb-1 fs-6"></i>
                        <span>Sakit</span>
                    </th>
                    <th class="col-header-alpa text-center py-2" style="width: 85px;">
                        <i class="fa-solid fa-circle-xmark d-block mb-1 fs-6"></i>
                        <span>Alpa</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($semesterData as $idx => $row)
                <tr>
                    <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                    <td class="text-center fw-bold text-dark">{{ $row->siswa->nisn }}</td>
                    <td class="fw-bold text-dark">{{ $row->siswa->nama }}</td>
                    <td class="text-center">
                        <span class="badge {{ $row->siswa->jenis_kelamin === 'L' ? 'bg-primary bg-opacity-10 text-primary border border-primary' : 'bg-danger bg-opacity-10 text-danger border border-danger' }} px-2 fw-bold">{{ $row->siswa->jenis_kelamin }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 rounded-2">
                            Kelas {{ $row->siswa->kelas->nama_kelas ?? '-' }}
                        </span>
                    </td>
                    <td class="text-center val-hadir" title="{{ $row->hadir }} hari hadir">{{ $row->hadir }}</td>
                    <td class="text-center val-terlambat">
                        {{ $row->terlambat }}
                        @if($row->terlambat >= 3)
                            <span class="badge bg-warning bg-opacity-25 text-dark border border-warning ms-1" style="font-size: 0.7rem;" title="Perlu tindak lanjut BK">
                                <i class="fa-solid fa-triangle-exclamation"></i> BK
                            </span>
                        @endif
                    </td>
                    <td class="text-center val-izin">{{ $row->izin }}</td>
                    <td class="text-center val-sakit">{{ $row->sakit }}</td>
                    <td class="text-center val-alpa">{{ $row->alpa }}</td>
                    <td class="text-center">
                        <span class="badge {{ $row->persentase >= 85 ? 'bg-success' : ($row->persentase >= 75 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3 py-2 fw-bold">
                            {{ $row->persentase }}%
                        </span>
                    </td>
                    <td class="text-center">
                        @if($row->terlambat >= 3)
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-user-shield me-1"></i> Perlu Tindak Lanjut
                            </span>
                        @elseif($row->terlambat > 0)
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-2 py-1" style="font-size: 0.75rem; color: #92400e !important;">
                                <i class="fa-regular fa-clock me-1"></i> Catatan ({{ $row->terlambat }}x)
                            </span>
                        @else
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-check me-1"></i> Tertib
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center text-muted py-5">Tidak ada data rekapitulasi semester.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- Modal Set Status Kehadiran Manual -->
<div class="modal fade" id="modalSetStatus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-user-pen me-2 text-primary"></i>Ubah Status Kehadiran Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.rekap.updateStatus') }}" method="POST">
                @csrf
                <input type="hidden" name="siswa_id" id="modal_siswa_id">
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                <div class="modal-body">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <small class="text-muted d-block">Nama Siswa:</small>
                        <span class="fw-bold text-dark fs-6" id="modal_siswa_nama">-</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Pilih Status Baru:</label>
                        <select name="status" id="modal_status" class="form-select" required>
                            <option value="HADIR">HADIR (Hadir Tepat Waktu)</option>
                            <option value="TERLAMBAT">TERLAMBAT (Hadir Melebihi Jam Masuk)</option>
                            <option value="IZIN">IZIN (Surat / Keterangan Izin)</option>
                            <option value="SAKIT">SAKIT (Surat Dokter / Sakit)</option>
                            <option value="ALPA">ALPA (Tanpa Keterangan)</option>
                        </select>
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

<script>
function openSetStatusModal(siswaId, siswaNama, currentStatus) {
    document.getElementById('modal_siswa_id').value = siswaId;
    document.getElementById('modal_siswa_nama').innerText = siswaNama;
    document.getElementById('modal_status').value = currentStatus || 'HADIR';
    const modal = new bootstrap.Modal(document.getElementById('modalSetStatus'));
    modal.show();
}
</script>
@endsection
