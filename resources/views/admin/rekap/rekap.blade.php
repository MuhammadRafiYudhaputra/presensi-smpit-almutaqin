@extends('layouts.app')

@section('content')
<style>
    .col-header-hadir { color: #16a34a !important; font-weight: 700; }
    .col-header-terlambat { color: #d97706 !important; font-weight: 700; }
    .col-header-sakit { color: #475569 !important; font-weight: 700; }
    .col-header-izin { color: #0284c7 !important; font-weight: 700; }
    .col-header-alpa { color: #dc2626 !important; font-weight: 700; }

    .val-hadir { color: #16a34a; font-weight: 800; font-size: 1rem; }
    .val-terlambat { color: #d97706; font-weight: 800; font-size: 1rem; cursor: pointer; }
    .val-sakit { color: #475569; font-weight: 800; font-size: 1rem; }
    .val-izin { color: #0284c7; font-weight: 800; font-size: 1rem; }
    .val-alpa { color: #dc2626; font-weight: 800; font-size: 1rem; }

    .bk-badge-btn {
        cursor: pointer;
        transition: transform 0.15s ease;
    }
    .bk-badge-btn:hover {
        transform: scale(1.05);
    }
</style>

<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header & Mode Tabs (Bulanan & Semester Saja) -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3 pb-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0.5 text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="fa-solid fa-chart-simple text-primary me-3 fs-5"></i> Rekapitulasi Presensi Siswa
            </h5>
            <small class="text-muted ms-4 ps-2 d-block" style="font-size: 0.76rem;">Akumulasi kehadiran berkala (Bulanan &amp; Semester)</small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap flex-sm-nowrap">
            <!-- Mode Switcher Tabs (Bulanan & Semester) -->
            <div class="btn-group p-0.5 bg-light rounded-pill border" role="group">
                <a href="{{ route('admin.rekap.index', ['mode' => 'bulanan', 'bulan' => $bulan, 'tahun' => $tahun, 'kelas_id' => $kelasId, 'sort_by' => $sortBy]) }}" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-2 {{ $mode === 'bulanan' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}" style="font-size: 0.8rem;">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span>Bulanan</span>
                </a>
                <a href="{{ route('admin.rekap.index', ['mode' => 'semester', 'semester' => $semester, 'tahun' => $tahun, 'kelas_id' => $kelasId, 'sort_by' => $sortBy]) }}" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-2 {{ $mode === 'semester' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}" style="font-size: 0.8rem;">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>Semester</span>
                </a>
            </div>

            <!-- Tombol Pengaturan Semester & Tahun Ajaran Aktif (Modal Trigger) -->
            @if(isset($settingAkademik))
            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-semibold shadow-xs text-nowrap d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalSettingAkademik" style="font-size: 0.8rem;" title="Klik untuk mengubah Semester atau Tahun Ajaran resmi aktif">
                <i class="fa-solid fa-calendar-check text-success"></i>
                <span>Semester {{ ucfirst($settingAkademik->semester) }} ({{ $settingAkademik->tahun_ajaran }})</span>
            </button>
            @endif

            <!-- Tombol Cetak Laporan -->
            <a href="{{ route('admin.rekap.cetak', ['mode' => $mode, 'bulan' => $bulan, 'tahun' => $tahun, 'semester' => $semester, 'kelas_id' => $kelasId]) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 py-1 fw-semibold shadow-sm btn-sm text-nowrap d-inline-flex align-items-center gap-2" style="font-size: 0.8rem;">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Laporan</span>
            </a>
        </div>
    </div>

    <!-- Filter & Parameter Form -->
    <form action="{{ route('admin.rekap.index') }}" method="GET" class="row g-3 mb-3 align-items-end">
        <input type="hidden" name="mode" value="{{ $mode }}">

        @if($mode === 'bulanan')
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark mb-1">Pilih Bulan</label>
                <select name="bulan" class="form-select shadow-sm" onchange="this.form.submit()">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark mb-1">Pilih Tahun</label>
                <select name="tahun" class="form-select shadow-sm" onchange="this.form.submit()">
                    @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        @elseif($mode === 'semester')
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark mb-1">Pilih Semester</label>
                <select name="semester" class="form-select shadow-sm" onchange="this.form.submit()">
                    <option value="ganjil" {{ $semester === 'ganjil' ? 'selected' : '' }}>Semester Ganjil (Jul - Des)</option>
                    <option value="genap" {{ $semester === 'genap' ? 'selected' : '' }}>Semester Genap (Jan - Jun)</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark mb-1">Tahun Ajaran</label>
                <select name="tahun" class="form-select shadow-sm" onchange="this.form.submit()">
                    @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}/{{ $y+1 }}</option>
                    @endfor
                </select>
            </div>
        @endif

        <div class="col-md-4">
            <label class="form-label fw-bold text-dark mb-1">Filter Kelas</label>
            <select name="kelas_id" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelases as $k)
                    <option value="{{ $k->id }}" {{ ($kelasId ?? '') == $k->id ? 'selected' : '' }}>Kelas {{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        @if($mode === 'semester')
        <!-- Bar Informasi Dasar Hari Efektif Masing-Masing Kelas (Hanya Tampil di Mode Semester) -->
        <div class="col-12 mt-1">
            <div class="d-flex align-items-center justify-content-between p-2 px-3 bg-light rounded-3 border">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-bold text-dark d-flex align-items-center me-2" style="font-size: 0.82rem;">
                        <i class="fa-solid fa-calendar-days text-primary me-2 fs-6"></i> Dasar Hari Efektif (100%):
                    </span>
                    @foreach($kelases as $k)
                        <span class="badge bg-white text-dark border px-2.5 py-1 rounded-2 shadow-xs d-inline-flex align-items-center" style="font-size: 0.78rem;">
                            <span class="text-secondary">Kelas {{ $k->nama_kelas }}:&nbsp;</span>
                            <strong class="text-primary">{{ $hariEfektifMap[$k->id] ?? 20 }} Hari</strong>
                        </span>
                    @endforeach
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center gap-1.5 shadow-none" style="font-size: 0.78rem;" data-bs-toggle="modal" data-bs-target="#modalAturHariEfektif">
                    <i class="fa-solid fa-sliders text-primary"></i>
                    <span>Atur Hari Efektif</span>
                </button>
            </div>
        </div>
        @endif
    </form>

    <!-- 1. TAMPILAN TABEL MODE BULANAN -->
    @if($mode === 'bulanan')
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light text-center">
                <tr>
                    <th rowspan="2" style="width: 45px;" class="align-middle text-dark">No</th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 110px;">NISN</th>
                    <th rowspan="2" class="align-middle text-dark text-start" style="min-width: 200px;">Nama Peserta Didik</th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 100px;">Kelas</th>
                    <th colspan="5" class="text-dark bg-light">
                        Kehadiran Bulan {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }} (T.A. {{ $tahunAjaran ?? '' }})
                    </th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 95px;">Persentase</th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 140px;">Catatan BK</th>
                </tr>
                <tr>
                    <th class="col-header-hadir text-center py-2" style="width: 80px;" title="Total siswa masuk sekolah">
                        <i class="fa-solid fa-circle-check d-block mb-1 fs-6"></i>
                        <span>Hadir</span>
                    </th>
                    <th class="col-header-terlambat text-center py-2" style="width: 95px;" title="Klik untuk melihat tanggal & jam keterlambatan siswa">
                        <i class="fa-solid fa-clock d-block mb-1 fs-6"></i>
                        <span>Terlambat</span>
                    </th>
                    <th class="col-header-izin text-center py-2" style="width: 80px;">
                        <i class="fa-solid fa-envelope-open d-block mb-1 fs-6"></i>
                        <span>Izin</span>
                    </th>
                    <th class="col-header-sakit text-center py-2" style="width: 80px;">
                        <i class="fa-solid fa-notes-medical d-block mb-1 fs-6"></i>
                        <span>Sakit</span>
                    </th>
                    <th class="col-header-alpa text-center py-2" style="width: 80px;">
                        <i class="fa-solid fa-circle-xmark d-block mb-1 fs-6"></i>
                        <span>Alpa</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($bulananData as $idx => $row)
                <tr>
                    <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                    <td class="text-center fw-bold text-dark">{{ $row->siswa->nisn }}</td>
                    <td class="fw-bold text-dark text-nowrap">{{ $row->siswa->nama }}</td>
                    <td class="text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 rounded-2">
                            Kelas {{ $row->kelas_historis->nama_kelas ?? ($row->siswa->kelas->nama_kelas ?? '-') }}
                        </span>
                    </td>
                    <td class="text-center val-hadir" title="{{ $row->hadir }} hari hadir">{{ $row->hadir }}</td>
                    <td class="text-center val-terlambat" onclick="openRiwayatTerlambatModal('{{ addslashes($row->siswa->nama) }}', '{{ $row->siswa->nisn }}', '{{ $row->kelas_historis->nama_kelas ?? ($row->siswa->kelas->nama_kelas ?? '-') }}', {{ json_encode($row->riwayat_terlambat) }}, '{{ $row->siswa->orangTua->no_wa ?? '' }}')">
                        <span class="text-decoration-underline" title="Klik untuk rincian tanggal">{{ $row->terlambat }}x</span>
                        @if($row->terlambat > 4)
                            <span class="badge bg-warning bg-opacity-25 text-dark border border-warning ms-1" style="font-size: 0.7rem;" title="Perlu tindak lanjut BK (Lebih dari 4x terlambat)">
                                <i class="fa-solid fa-triangle-exclamation"></i> BK
                            </span>
                        @endif
                    </td>
                    <td class="text-center val-izin">{{ $row->izin }}</td>
                    <td class="text-center val-sakit">{{ $row->sakit }}</td>
                    <td class="text-center val-alpa">{{ $row->alpa }}</td>
                    <td class="text-center">
                        <span class="badge {{ $row->persentase >= 85 ? 'bg-success' : ($row->persentase >= 75 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3 py-1.5 fw-bold">
                            {{ $row->persentase }}%
                        </span>
                        <div class="small text-muted mt-1" style="font-size: 0.72rem;" title="Dasar Hitungan: Hadir {{ $row->hadir }} hari dari {{ $row->hari_efektif_siswa }} hari efektif kelas {{ $row->kelas_historis->nama_kelas ?? ($row->siswa->kelas->nama_kelas ?? '-') }}">
                            {{ $row->hadir }}/{{ $row->hari_efektif_siswa }} hr
                        </div>
                    </td>
                    <td class="text-center">
                        @if($row->terlambat > 4)
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 bk-badge-btn" style="font-size: 0.75rem;" onclick="openRiwayatTerlambatModal('{{ addslashes($row->siswa->nama) }}', '{{ $row->siswa->nisn }}', '{{ $row->siswa->kelas->nama_kelas ?? '-' }}', {{ json_encode($row->riwayat_terlambat) }}, '{{ $row->siswa->orangTua->no_wa ?? '' }}')">
                                <i class="fa-solid fa-user-shield me-1"></i> Perlu Tindak Lanjut BK
                            </button>
                        @elseif($row->terlambat > 0)
                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1 bk-badge-btn text-dark" style="font-size: 0.75rem;" onclick="openRiwayatTerlambatModal('{{ addslashes($row->siswa->nama) }}', '{{ $row->siswa->nisn }}', '{{ $row->siswa->kelas->nama_kelas ?? '-' }}', {{ json_encode($row->riwayat_terlambat) }}, '{{ $row->siswa->orangTua->no_wa ?? '' }}')">
                                <i class="fa-regular fa-clock me-1 text-warning"></i> Catatan ({{ $row->terlambat }}x)
                            </button>
                        @else
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-check me-1"></i> Tertib
                            </span>
                        @endif
                    </td>
                </tr>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-5">Tidak ada data rekapitulasi bulanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 2. TAMPILAN TABEL MODE SEMESTER -->
    @elseif($mode === 'semester')
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light text-center">
                <tr>
                    <th rowspan="2" style="width: 45px;" class="align-middle text-dark">No</th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 110px;">NISN</th>
                    <th rowspan="2" class="align-middle text-dark text-start" style="min-width: 200px;">Nama Peserta Didik</th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 100px;">Kelas</th>
                    <th colspan="5" class="text-dark bg-light">
                        Kehadiran Semester {{ ucfirst($semester) }} {{ $tahun }}/{{ $tahun + 1 }}
                    </th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 95px;">Persentase</th>
                    <th rowspan="2" class="align-middle text-dark" style="width: 140px;">Catatan BK</th>
                </tr>
                <tr>
                    <th class="col-header-hadir text-center py-2" style="width: 80px;" title="Total siswa masuk sekolah">
                        <i class="fa-solid fa-circle-check d-block mb-1 fs-6"></i>
                        <span>Hadir</span>
                    </th>
                    <th class="col-header-terlambat text-center py-2" style="width: 95px;" title="Klik untuk melihat tanggal & jam keterlambatan siswa">
                        <i class="fa-solid fa-clock d-block mb-1 fs-6"></i>
                        <span>Terlambat</span>
                    </th>
                    <th class="col-header-izin text-center py-2" style="width: 80px;">
                        <i class="fa-solid fa-envelope-open d-block mb-1 fs-6"></i>
                        <span>Izin</span>
                    </th>
                    <th class="col-header-sakit text-center py-2" style="width: 80px;">
                        <i class="fa-solid fa-notes-medical d-block mb-1 fs-6"></i>
                        <span>Sakit</span>
                    </th>
                    <th class="col-header-alpa text-center py-2" style="width: 80px;">
                        <i class="fa-solid fa-circle-xmark d-block mb-1 fs-6"></i>
                        <span>Alpa</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($semesterData as $idx => $row)
                <tr>
                    <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                    <td class="text-center fw-bold text-dark">{{ $row->siswa->nisn }}</td>
                    <td class="fw-bold text-dark text-nowrap">{{ $row->siswa->nama }}</td>
                    <td class="text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 rounded-2">
                            Kelas {{ $row->kelas_historis->nama_kelas ?? ($row->siswa->kelas->nama_kelas ?? '-') }}
                        </span>
                    </td>
                    <td class="text-center val-hadir" title="{{ $row->hadir }} hari hadir">{{ $row->hadir }}</td>
                    <td class="text-center val-terlambat" onclick="openRiwayatTerlambatModal('{{ addslashes($row->siswa->nama) }}', '{{ $row->siswa->nisn }}', '{{ $row->kelas_historis->nama_kelas ?? ($row->siswa->kelas->nama_kelas ?? '-') }}', {{ json_encode($row->riwayat_terlambat) }}, '{{ $row->siswa->orangTua->no_wa ?? '' }}')">
                        <span class="text-decoration-underline" title="Klik untuk rincian tanggal">{{ $row->terlambat }}x</span>
                        @if($row->terlambat > 4)
                            <span class="badge bg-warning bg-opacity-25 text-dark border border-warning ms-1" style="font-size: 0.7rem;" title="Perlu tindak lanjut BK">
                                <i class="fa-solid fa-triangle-exclamation"></i> BK
                            </span>
                        @endif
                    </td>
                    <td class="text-center val-izin">{{ $row->izin }}</td>
                    <td class="text-center val-sakit">{{ $row->sakit }}</td>
                    <td class="text-center val-alpa">{{ $row->alpa }}</td>
                    <td class="text-center">
                        <span class="badge {{ $row->persentase >= 85 ? 'bg-success' : ($row->persentase >= 75 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3 py-1.5 fw-bold">
                            {{ $row->persentase }}%
                        </span>
                        <div class="small text-muted mt-1" style="font-size: 0.72rem;" title="Dasar Hitungan: Hadir {{ $row->hadir }} hari dari {{ $row->hari_efektif_siswa }} hari efektif kelas {{ $row->kelas_historis->nama_kelas ?? ($row->siswa->kelas->nama_kelas ?? '-') }}">
                            {{ $row->hadir }}/{{ $row->hari_efektif_siswa }} hr
                        </div>
                    </td>
                    <td class="text-center">
                        @if($row->terlambat > 4)
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 bk-badge-btn" style="font-size: 0.75rem;" onclick="openRiwayatTerlambatModal('{{ addslashes($row->siswa->nama) }}', '{{ $row->siswa->nisn }}', '{{ $row->siswa->kelas->nama_kelas ?? '-' }}', {{ json_encode($row->riwayat_terlambat) }}, '{{ $row->siswa->orangTua->no_wa ?? '' }}')">
                                <i class="fa-solid fa-user-shield me-1"></i> Perlu Tindak Lanjut BK
                            </button>
                        @elseif($row->terlambat > 0)
                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1 bk-badge-btn text-dark" style="font-size: 0.75rem;" onclick="openRiwayatTerlambatModal('{{ addslashes($row->siswa->nama) }}', '{{ $row->siswa->nisn }}', '{{ $row->siswa->kelas->nama_kelas ?? '-' }}', {{ json_encode($row->riwayat_terlambat) }}, '{{ $row->siswa->orangTua->no_wa ?? '' }}')">
                                <i class="fa-regular fa-clock me-1 text-warning"></i> Catatan ({{ $row->terlambat }}x)
                            </button>
                        @else
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-check me-1"></i> Tertib
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-5">Tidak ada data rekapitulasi semester.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- Modal Pengaturan Hari Efektif per Kelas -->
<div class="modal fade" id="modalAturHariEfektif" tabindex="-1" aria-labelledby="modalAturHariEfektifLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <div>
                    <h6 class="modal-title fw-bold" id="modalAturHariEfektifLabel">
                        <i class="fa-solid fa-sliders me-2"></i> Atur Hari Efektif per Kelas
                    </h6>
                    <small class="text-white text-opacity-75">
                        Periode: {{ $mode === 'semester' ? 'Semester ' . ucfirst($semester) . ' (T.A. ' . $tahunAjaran . ')' : 'Bulan ' . \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') . ' ' . $tahun . ' (T.A. ' . $tahunAjaran . ')' }}
                    </small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.rekap.saveHariEfektif') }}" method="POST">
                @csrf
                <input type="hidden" name="mode" value="{{ $mode }}">
                <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">
                <input type="hidden" name="semester" value="{{ $semester }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <input type="hidden" name="bulan" value="{{ $bulan }}">

                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 mb-3 d-flex align-items-start gap-2.5 small">
                        <i class="fa-solid fa-circle-info fs-5 text-primary mt-0.5"></i>
                        <div>
                            <strong>Dasar Perhitungan Persentase:</strong><br>
                            Kehadiran dihitung berdasarkan jumlah hari efektif masuk sekolah. 
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th>Nama Kelas</th>
                                    <th>Wali Kelas</th>
                                    <th style="width: 200px;" class="text-center">Hari Efektif (Hari)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kelases as $idx => $k)
                                @php
                                    $currentDays = $hariEfektifMap[$k->id] ?? 20;
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                                    <td class="fw-bold text-dark">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2.5 py-1.5 rounded-2">
                                            Kelas {{ $k->nama_kelas }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $k->waliKelas->nama ?? 'Belum ditentukan' }}
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="hari_efektif_kelas[{{ $k->id }}]" class="form-control text-center fw-bold text-primary" value="{{ $currentDays }}" min="1" max="365" required>
                                            <span class="input-group-text bg-light text-muted small">Hari</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 border-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Hari Efektif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Riwayat Keterlambatan & Catatan BK -->
<div class="modal fade" id="modalRiwayatTerlambat" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-clipboard-user me-2 text-warning"></i>Catatan Keterlambatan Siswa (BK)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark fs-6" id="bk_siswa_nama">-</span>
                        <span class="badge bg-primary px-2 py-1" id="bk_siswa_kelas">Kelas -</span>
                    </div>
                    <small class="text-muted d-block">NISN: <span id="bk_siswa_nisn" class="fw-semibold text-dark">-</span></small>
                </div>

                <div id="bk_rekomendasi_box" class="p-3 rounded-3 mb-3"></div>

                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-clock-rotate-left me-1 text-primary"></i> Rincian Tanggal & Jam Terlambat:</h6>
                <div class="table-responsive border rounded-3 mb-3" style="max-height: 200px; overflow-y: auto;">
                    <table class="table table-sm table-striped mb-0 text-center" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jam Datang</th>
                            </tr>
                        </thead>
                        <tbody id="bk_tabel_body">
                        </tbody>
                    </table>
                </div>

                <div id="bk_wa_container" class="text-center"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Setting Master Semester & Tahun Ajaran Aktif -->
@if(isset($settingAkademik))
<div class="modal fade" id="modalSettingAkademik" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-calendar-days me-2 text-primary"></i>Pengaturan Periode Akademik Aktif</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.setting.akademik.update') }}" method="POST">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Tahun Ajaran Aktif <span class="text-danger">*</span></label>
                        <select name="tahun_ajaran" class="form-select shadow-sm" required>
                            @php
                                $historyTAs = \App\Models\RiwayatKelas::distinct()->pluck('tahun_ajaran')->toArray();
                                $taList = [];
                                // Rentang tahun rolling: 5 tahun ke belakang hingga 5 tahun ke depan
                                for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++) {
                                    $taList[] = $y . '/' . ($y + 1);
                                }
                                // Gabungkan seluruh histori tahun ajaran yang pernah tercatat di database agar tidak pernah hilang
                                foreach ($historyTAs as $hta) {
                                    if (!empty($hta) && !in_array($hta, $taList)) {
                                        $taList[] = $hta;
                                    }
                                }
                                if (!in_array($settingAkademik->tahun_ajaran, $taList)) {
                                    $taList[] = $settingAkademik->tahun_ajaran;
                                }
                                usort($taList, function($a, $b) {
                                    return strcmp($a, $b);
                                });
                            @endphp
                            @foreach($taList as $taOption)
                                <option value="{{ $taOption }}" {{ $settingAkademik->tahun_ajaran === $taOption ? 'selected' : '' }}>
                                    Tahun Ajaran {{ $taOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Semester Berjalan <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check p-2 border rounded-3 flex-fill">
                                <input class="form-check-input ms-1" type="radio" name="semester" id="sem_ganjil" value="ganjil" {{ $settingAkademik->semester === 'ganjil' ? 'checked' : '' }} required>
                                <label class="form-check-label ps-2 cursor-pointer w-100" for="sem_ganjil">
                                    <strong class="d-block text-dark">Semester Ganjil</strong>
                                    <small class="text-muted">Bulan Juli - Desember</small>
                                </label>
                            </div>
                            <div class="form-check p-2 border rounded-3 flex-fill">
                                <input class="form-check-input ms-1" type="radio" name="semester" id="sem_genap" value="genap" {{ $settingAkademik->semester === 'genap' ? 'checked' : '' }} required>
                                <label class="form-check-label ps-2 cursor-pointer w-100" for="sem_genap">
                                    <strong class="d-block text-dark">Semester Genap</strong>
                                    <small class="text-muted">Bulan Januari - Juni</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-check me-1"></i> Simpan & Aktifkan Periode
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
function openRiwayatTerlambatModal(nama, nisn, kelas, riwayat, noWa) {
    document.getElementById('bk_siswa_nama').innerText = nama;
    document.getElementById('bk_siswa_nisn').innerText = nisn;
    document.getElementById('bk_siswa_kelas').innerText = 'Kelas ' + kelas;

    const tbody = document.getElementById('bk_tabel_body');
    tbody.innerHTML = '';

    const count = riwayat ? riwayat.length : 0;
    const rekBox = document.getElementById('bk_rekomendasi_box');

    if (count > 4) {
        rekBox.className = 'p-3 rounded-3 mb-3 bg-danger bg-opacity-10 border border-danger text-danger';
        rekBox.innerHTML = `
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                <strong class="fs-6">Perhatian: Perlu Tindak Lanjut Pihak BK</strong>
            </div>
            <small class="text-dark d-block">Siswa tercatat terlambat sebanyak <strong>${count} kali</strong> pada periode ini. Diperlukan koordinasi pemanggilan atau konseling kedisiplinan bersama pihak BK dan orang tua.</small>
        `;
    } else if (count > 0) {
        rekBox.className = 'p-3 rounded-3 mb-3 bg-warning bg-opacity-10 border border-warning text-dark';
        rekBox.innerHTML = `
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="fa-solid fa-clock fs-5 text-warning"></i>
                <strong class="fs-6">Catatan Kedisiplinan: ${count} Kali Terlambat</strong>
            </div>
            <small class="text-muted d-block">Siswa memiliki catatan keterlambatan ringan. Tetap pantau kehadiran harian siswa.</small>
        `;
    } else {
        rekBox.className = 'p-3 rounded-3 mb-3 bg-success bg-opacity-10 border border-success text-success';
        rekBox.innerHTML = `
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="fa-solid fa-circle-check fs-5"></i>
                <strong class="fs-6">Siswa Tertib</strong>
            </div>
            <small class="text-dark d-block">Tidak ada catatan keterlambatan untuk siswa ini pada periode terpilih.</small>
        `;
    }

    if (count > 0) {
        riwayat.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="fw-bold">${index + 1}</td>
                <td>${item.tanggal}</td>
                <td><span class="badge bg-warning bg-opacity-10 text-dark border border-warning">${item.jam_masuk || '-'}</span></td>
            `;
            tbody.appendChild(tr);
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="3" class="text-muted py-3">Tidak ada riwayat keterlambatan</td></tr>';
    }

    const waContainer = document.getElementById('bk_wa_container');
    if (noWa && count > 0) {
        let cleanWa = noWa.replace(/[^0-9]/g, '');
        if (cleanWa.startsWith('0')) cleanWa = '62' + cleanWa.substring(1);
        const msg = encodeURIComponent(`Assalamu'alaikum Wr. Wb. Bapak/Ibu wali dari ananda ${nama} (Kelas ${kelas}). Kami ingin menginformasikan terkait catatan kehadiran ananda yang telah tercatat terlambat sebanyak ${count} kali...`);
        waContainer.innerHTML = `
            <a href="https://wa.me/${cleanWa}?text=${msg}" target="_blank" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                <i class="fa-brands fa-whatsapp me-1"></i> Hubungi Orang Tua via WhatsApp
            </a>
        `;
    } else {
        waContainer.innerHTML = '';
    }

    const modal = new bootstrap.Modal(document.getElementById('modalRiwayatTerlambat'));
    modal.show();
}
</script>
@endsection
