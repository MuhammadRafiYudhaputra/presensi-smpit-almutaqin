@extends('layouts.app')

@section('content')
<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header & Mode Tabs -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-file-invoice me-2 text-primary"></i>Rekapitulasi Kehadiran Siswa</h5>
            <small class="text-muted">Laporan presensi harian, rekapitulasi bulanan, dan semester</small>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <!-- Mode Switcher Tabs -->
            <div class="btn-group p-1 bg-light rounded-pill border" role="group">
                <a href="{{ route('admin.rekap.index', ['mode' => 'harian', 'tanggal' => $tanggal, 'kelas_id' => $kelasId, 'sort_by' => $sortBy]) }}" class="btn btn-sm rounded-pill {{ $mode === 'harian' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                    <i class="fa-solid fa-calendar-day me-1"></i> Harian
                </a>
                <a href="{{ route('admin.rekap.index', ['mode' => 'bulanan', 'bulan' => $bulan, 'tahun' => $tahun, 'kelas_id' => $kelasId, 'sort_by' => $sortBy]) }}" class="btn btn-sm rounded-pill {{ $mode === 'bulanan' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                    <i class="fa-solid fa-calendar-days me-1"></i> Bulanan
                </a>
                <a href="{{ route('admin.rekap.index', ['mode' => 'semester', 'semester' => $semester, 'tahun' => $tahun, 'kelas_id' => $kelasId, 'sort_by' => $sortBy]) }}" class="btn btn-sm rounded-pill {{ $mode === 'semester' ? 'btn-primary shadow-sm' : 'btn-light text-muted' }}">
                    <i class="fa-solid fa-graduation-cap me-1"></i> Semester
                </a>
            </div>

            <!-- Tombol Cetak Laporan -->
            <a href="{{ route('admin.rekap.cetak', ['mode' => $mode, 'tanggal' => $tanggal, 'bulan' => $bulan, 'tahun' => $tahun, 'semester' => $semester, 'kelas_id' => $kelasId]) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 shadow-sm">
                <i class="fa-solid fa-print me-1"></i> Cetak Laporan
            </a>
        </div>
    </div>

    <!-- Filter & Parameter Form -->
    <form action="{{ route('admin.rekap.index') }}" method="GET" class="row g-3 mb-4 align-items-end">
        <input type="hidden" name="mode" value="{{ $mode }}">

        @if($mode === 'harian')
            <div class="col-md-4">
                <label class="form-label fw-semibold text-dark">Pilih Tanggal Presensi</label>
                <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}" onchange="this.form.submit()">
            </div>
        @elseif($mode === 'bulanan')
            <div class="col-md-2">
                <label class="form-label fw-semibold text-dark">Pilih Bulan</label>
                <select name="bulan" class="form-select" onchange="this.form.submit()">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold text-dark">Pilih Tahun</label>
                <select name="tahun" class="form-select" onchange="this.form.submit()">
                    @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        @elseif($mode === 'semester')
            <div class="col-md-2">
                <label class="form-label fw-semibold text-dark">Pilih Semester</label>
                <select name="semester" class="form-select" onchange="this.form.submit()">
                    <option value="ganjil" {{ $semester === 'ganjil' ? 'selected' : '' }}>Semester Ganjil (Jul - Des)</option>
                    <option value="genap" {{ $semester === 'genap' ? 'selected' : '' }}>Semester Genap (Jan - Jun)</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold text-dark">Tahun Ajaran</label>
                <select name="tahun" class="form-select" onchange="this.form.submit()">
                    @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}/{{ $y+1 }}</option>
                    @endfor
                </select>
            </div>
        @endif

        <div class="col-md-4">
            <label class="form-label fw-semibold text-dark">Filter Kelas</label>
            <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelases as $k)
                    <option value="{{ $k->id }}" {{ ($kelasId ?? '') == $k->id ? 'selected' : '' }}>Kelas {{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold text-dark">Urutkan Data</label>
            <select name="sort_by" class="form-select" onchange="this.form.submit()">
                <option value="nama_asc" {{ ($sortBy ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama Siswa (A-Z)</option>
                <option value="nama_desc" {{ ($sortBy ?? '') === 'nama_desc' ? 'selected' : '' }}>Nama Siswa (Z-A)</option>
                <option value="nisn" {{ ($sortBy ?? '') === 'nisn' ? 'selected' : '' }}>NISN Siswa</option>
            </select>
        </div>
    </form>

    <!-- TAMPILAN TABEL BERDASARKAN MODE -->

    <!-- 1. MODE HARIAN -->
    @if($mode === 'harian')
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 45px;" class="text-dark">No</th>
                    <th class="text-dark text-nowrap">NISN</th>
                    <th class="text-dark text-start text-nowrap">Nama Peserta Didik</th>
                    <th style="width: 55px;" class="text-dark">JK</th>
                    <th class="text-dark text-nowrap">Kelas</th>
                    <th class="text-dark text-nowrap">Jam Masuk (Pagi)</th>
                    <th class="text-dark text-nowrap">Jam Pulang (Sore)</th>
                    <th class="text-dark text-nowrap">Status Kehadiran Harian</th>
                    <th style="width: 100px;" class="text-dark">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($harianData as $idx => $row)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="fw-bold">{{ $row->siswa->nisn }}</td>
                    <td class="fw-semibold text-dark">{{ $row->siswa->nama }}</td>
                    <td class="text-center">
                        <span class="badge {{ $row->siswa->jenis_kelamin === 'L' ? 'bg-primary bg-opacity-10 text-primary border border-primary' : 'bg-danger bg-opacity-10 text-danger border border-danger' }} px-2">
                            {{ $row->siswa->jenis_kelamin }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info bg-opacity-10 text-dark border border-info">
                            Kelas {{ $row->siswa->kelas->nama_kelas ?? '-' }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($row->jam_masuk)
                            <span class="badge bg-light text-dark border px-2 py-1"><i class="fa-regular fa-clock text-primary me-1"></i>{{ $row->jam_masuk }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($row->jam_pulang)
                            <span class="badge bg-light text-dark border px-2 py-1"><i class="fa-solid fa-door-open text-success me-1"></i>{{ $row->jam_pulang }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($row->status === 'HADIR')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill"><i class="fa-solid fa-check me-1"></i> HADIR</span>
                        @elseif($row->status === 'TERLAMBAT')
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-2 rounded-pill"><i class="fa-solid fa-clock me-1 text-warning"></i> TERLAMBAT</span>
                        @elseif($row->status === 'IZIN')
                            <span class="badge bg-info bg-opacity-10 text-dark border border-info px-3 py-2 rounded-pill"><i class="fa-solid fa-envelope-open-text me-1"></i> IZIN</span>
                        @elseif($row->status === 'SAKIT')
                            <span class="badge bg-secondary bg-opacity-10 text-dark border border-secondary px-3 py-2 rounded-pill"><i class="fa-solid fa-notes-medical me-1"></i> SAKIT</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill"><i class="fa-solid fa-xmark me-1"></i> ALPA</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" onclick="openSetStatusModal({{ $row->siswa->id }}, '{{ addslashes($row->siswa->nama) }}', '{{ $row->status }}')">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Set Status
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">Tidak ada data siswa ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 2. MODE BULANAN -->
    @elseif($mode === 'bulanan')
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light text-center">
                <tr>
                    <th rowspan="2" style="width: 45px;" class="align-middle text-dark">No</th>
                    <th rowspan="2" class="align-middle text-dark text-nowrap">NISN</th>
                    <th rowspan="2" class="align-middle text-dark text-start text-nowrap">Nama Peserta Didik</th>
                    <th rowspan="2" style="width: 55px;" class="align-middle text-dark">JK</th>
                    <th rowspan="2" class="align-middle text-dark text-nowrap">Kelas</th>
                    <th colspan="5" class="text-dark bg-light text-nowrap">Akumulasi Kehadiran (Bulan {{ $bulan }}/{{ $tahun }})</th>
                    <th rowspan="2" class="align-middle text-dark text-nowrap">Persentase (%)</th>
                </tr>
                <tr>
                    <th class="text-success text-nowrap px-3 py-2"><i class="fa-solid fa-circle-check me-1"></i> Hadir</th>
                    <th class="text-warning text-nowrap px-3 py-2"><i class="fa-solid fa-clock me-1"></i> Terlambat</th>
                    <th class="text-info text-nowrap px-3 py-2"><i class="fa-solid fa-envelope-open me-1"></i> Izin</th>
                    <th class="text-secondary text-nowrap px-3 py-2"><i class="fa-solid fa-notes-medical me-1"></i> Sakit</th>
                    <th class="text-danger text-nowrap px-3 py-2"><i class="fa-solid fa-circle-xmark me-1"></i> Alpa</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bulananData as $idx => $row)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="fw-bold">{{ $row->siswa->nisn }}</td>
                    <td class="fw-semibold text-dark">{{ $row->siswa->nama }}</td>
                    <td class="text-center">
                        <span class="badge {{ $row->siswa->jenis_kelamin === 'L' ? 'bg-primary bg-opacity-10 text-primary border border-primary' : 'bg-danger bg-opacity-10 text-danger border border-danger' }} px-2">
                            {{ $row->siswa->jenis_kelamin }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info bg-opacity-10 text-dark border border-info">
                            Kelas {{ $row->siswa->kelas->nama_kelas ?? '-' }}
                        </span>
                    </td>
                    <td class="text-center fw-bold text-success">{{ $row->hadir }}</td>
                    <td class="text-center fw-bold text-warning">{{ $row->terlambat }}</td>
                    <td class="text-center fw-bold text-info">{{ $row->izin }}</td>
                    <td class="text-center fw-bold text-secondary">{{ $row->sakit }}</td>
                    <td class="text-center fw-bold text-danger">{{ $row->alpa }}</td>
                    <td class="text-center">
                        <span class="badge {{ $row->persentase >= 85 ? 'bg-success' : ($row->persentase >= 75 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3 py-2">
                            {{ $row->persentase }}%
                        </span>
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

    <!-- 3. MODE SEMESTER -->
    @elseif($mode === 'semester')
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light text-center">
                <tr>
                    <th rowspan="2" style="width: 45px;" class="align-middle text-dark">No</th>
                    <th rowspan="2" class="align-middle text-dark text-nowrap">NISN</th>
                    <th rowspan="2" class="align-middle text-dark text-start text-nowrap">Nama Peserta Didik</th>
                    <th rowspan="2" style="width: 55px;" class="align-middle text-dark">JK</th>
                    <th rowspan="2" class="align-middle text-dark text-nowrap">Kelas</th>
                    <th colspan="5" class="text-dark bg-light text-nowrap">Akumulasi Kehadiran Semester ({{ ucfirst($semester) }} {{ $tahun }})</th>
                    <th rowspan="2" class="align-middle text-dark text-nowrap">Persentase Akhir (%)</th>
                </tr>
                <tr>
                    <th class="text-success text-nowrap px-3 py-2"><i class="fa-solid fa-circle-check me-1"></i> Hadir</th>
                    <th class="text-warning text-nowrap px-3 py-2"><i class="fa-solid fa-clock me-1"></i> Terlambat</th>
                    <th class="text-info text-nowrap px-3 py-2"><i class="fa-solid fa-envelope-open me-1"></i> Izin</th>
                    <th class="text-secondary text-nowrap px-3 py-2"><i class="fa-solid fa-notes-medical me-1"></i> Sakit</th>
                    <th class="text-danger text-nowrap px-3 py-2"><i class="fa-solid fa-circle-xmark me-1"></i> Alpa</th>
                </tr>
            </thead>
            <tbody>
                @forelse($semesterData as $idx => $row)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="fw-bold">{{ $row->siswa->nisn }}</td>
                    <td class="fw-semibold text-dark">{{ $row->siswa->nama }}</td>
                    <td class="text-center">
                        <span class="badge {{ $row->siswa->jenis_kelamin === 'L' ? 'bg-primary bg-opacity-10 text-primary border border-primary' : 'bg-danger bg-opacity-10 text-danger border border-danger' }} px-2">
                            {{ $row->siswa->jenis_kelamin }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info bg-opacity-10 text-dark border border-info">
                            Kelas {{ $row->siswa->kelas->nama_kelas ?? '-' }}
                        </span>
                    </td>
                    <td class="text-center fw-bold text-success">{{ $row->hadir }}</td>
                    <td class="text-center fw-bold text-warning">{{ $row->terlambat }}</td>
                    <td class="text-center fw-bold text-info">{{ $row->izin }}</td>
                    <td class="text-center fw-bold text-secondary">{{ $row->sakit }}</td>
                    <td class="text-center fw-bold text-danger">{{ $row->alpa }}</td>
                    <td class="text-center">
                        <span class="badge {{ $row->persentase >= 85 ? 'bg-success' : ($row->persentase >= 75 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3 py-2">
                            {{ $row->persentase }}%
                        </span>
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
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Perubahan</button>
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
