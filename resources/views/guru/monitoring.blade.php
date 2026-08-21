@extends('layouts.app')

@section('content')
<style>
    /* Styling Radio Pilihan Kehadiran Sesuai Gambar Referensi */
    .status-radio-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .status-radio-option:hover {
        background: #f8fafc;
    }
    .status-radio-option input[type="radio"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    .radio-label-hadir { color: #16a34a; font-weight: 700; font-size: 0.95rem; }
    .radio-label-terlambat { color: #d97706; font-weight: 700; font-size: 0.95rem; }
    .radio-label-sakit { color: #475569; font-weight: 700; font-size: 0.95rem; }
    .radio-label-izin { color: #0284c7; font-weight: 700; font-size: 0.95rem; }
    .radio-label-alpa { color: #dc2626; font-weight: 700; font-size: 0.95rem; }

    .btn-ubah-modal {
        background: #00c0ef;
        border: 1px solid #00c0ef;
        color: #ffffff;
        font-weight: 700;
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
        transition: all 0.2s ease;
    }
    .btn-ubah-modal:hover {
        background: #0891b2;
        border-color: #0891b2;
        color: #ffffff;
    }
</style>

<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center flex-wrap">
                <i class="fa-solid fa-list-check text-primary me-2 fs-4"></i> Absensi Siswa (Harian)
                @if($kelas)
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-1 rounded-pill fs-6 ms-2">
                        <i class="fa-solid fa-chalkboard-user me-1"></i> Kelas {{ $kelas->nama_kelas }}
                    </span>
                @endif
            </h5>
            <small class="text-muted">Daftar presensi harian seluruh siswa kelas binaan, jam masuk/pulang, dan set status</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.rekap.cetak', ['mode' => 'harian', 'tanggal' => $tanggal, 'kelas_id' => $kelas ? $kelas->id : null]) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-semibold shadow-sm">
                <i class="fa-solid fa-print me-1"></i> Cetak Absensi Harian
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('guru.monitoring') }}" class="row g-3 mb-4 align-items-end">
        <div class="col-md-6">
            <label class="form-label fw-bold text-dark mb-1">Pilih Tanggal Presensi</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control shadow-sm" onchange="this.form.submit()">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold text-dark mb-1">Urutkan Data (Sorting)</label>
            <select name="sort_by" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="nama_asc" {{ ($sortBy ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama Siswa (A-Z)</option>
                <option value="nama_desc" {{ ($sortBy ?? '') === 'nama_desc' ? 'selected' : '' }}>Nama Siswa (Z-A)</option>
                <option value="nisn" {{ ($sortBy ?? '') === 'nisn' ? 'selected' : '' }}>NISN Siswa</option>
            </select>
        </div>
    </form>

    <!-- Ringkasan Cepat Hari Ini -->
    <div class="p-3 bg-light rounded-3 mb-4 border d-flex flex-wrap gap-3 justify-content-around text-center" style="font-size: 0.85rem;">
        <div>
            <span class="text-muted d-block fw-semibold">Total Siswa:</span>
            <strong class="fs-6 text-dark">{{ $summary['total'] }}</strong>
        </div>
        <div>
            <span class="text-success d-block fw-semibold">Hadir:</span>
            <strong class="fs-6 text-success">{{ $summary['hadir'] }}</strong>
        </div>
        <div>
            <span class="text-warning d-block fw-semibold" style="color: #d97706 !important;">Terlambat:</span>
            <strong class="fs-6" style="color: #d97706 !important;">{{ $summary['terlambat'] }}</strong>
        </div>
        <div>
            <span class="text-primary d-block fw-semibold">Izin:</span>
            <strong class="fs-6 text-primary">{{ $summary['izin'] }}</strong>
        </div>
        <div>
            <span class="text-secondary d-block fw-semibold">Sakit:</span>
            <strong class="fs-6 text-secondary">{{ $summary['sakit'] }}</strong>
        </div>
        <div>
            <span class="text-danger d-block fw-semibold">Alpa:</span>
            <strong class="fs-6 text-danger">{{ $summary['alpa'] }}</strong>
        </div>
        <div>
            <span class="text-muted d-block fw-semibold">Belum Absen:</span>
            <strong class="fs-6 text-dark">{{ $summary['belum'] }}</strong>
        </div>
    </div>

    <!-- Table -->
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
                    <th class="text-dark" style="width: 180px;">Status Kehadiran</th>
                    <th class="text-dark" style="width: 150px;">Notifikasi WA</th>
                    <th class="text-center text-dark" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($harianData as $idx => $row)
                <tr>
                    <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                    <td class="text-center fw-bold text-dark">{{ $row->siswa->nisn }}</td>
                    <td class="fw-bold text-dark">
                        {{ $row->siswa->nama }}
                        @if($row->keterangan)
                            <small class="d-block text-muted fw-normal" style="font-size: 0.78rem;">
                                <i class="fa-solid fa-note-sticky text-warning me-1"></i>{{ $row->keterangan }}
                            </small>
                        @endif
                    </td>
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
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-xmark me-1"></i> TANPA KETERANGAN</span>
                        @else
                            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fw-semibold"><i class="fa-regular fa-circle me-1"></i> BELUM ABSEN</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($row->wa_sent)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-brands fa-whatsapp me-1"></i> Terkirim</span>
                        @elseif(in_array($row->status, ['HADIR', 'TERLAMBAT']))
                            <span class="badge bg-light text-muted border px-2 py-1"><i class="fa-solid fa-hourglass-half me-1"></i> Antrian</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold shadow-sm" onclick="openSetStatusModal({{ $row->siswa->id }}, '{{ addslashes($row->siswa->nama) }}', '{{ $row->status }}', '{{ addslashes($row->keterangan ?? '') }}')">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Set Status
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-5">
                        <i class="fa-solid fa-folder-open fs-2 d-block mb-2 text-muted"></i>
                        Tidak ada data siswa ditemukan untuk kelas ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ubah Kehadiran (Sesuai Desain Gambar Referensi) -->
<div class="modal fade" id="modalSetStatus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg" style="overflow: hidden;">
            <div class="modal-header border-0 pb-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0">Ubah kehadiran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.rekap.updateStatus') }}" method="POST">
                @csrf
                <input type="hidden" name="siswa_id" id="modal_siswa_id">
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                
                <div class="modal-body px-4 py-3">
                    <!-- Info Nama Siswa -->
                    <div class="mb-3">
                        <small class="text-muted d-block fw-semibold mb-1">Nama Siswa:</small>
                        <span class="fw-bold text-dark fs-6" id="modal_siswa_nama">-</span>
                    </div>

                    <hr class="my-2" style="opacity: 0.15;">

                    <!-- Pilihan Kehadiran Radio List (Sesuai Gambar) -->
                    <label class="form-label fw-bold text-muted small text-uppercase mb-2" style="letter-spacing: 0.5px;">Kehadiran</label>
                    <div class="d-flex flex-column gap-1 mb-3">
                        <label class="status-radio-option">
                            <input type="radio" name="status" id="status_hadir" value="HADIR">
                            <span class="radio-label-hadir">HADIR</span>
                        </label>

                        <label class="status-radio-option">
                            <input type="radio" name="status" id="status_terlambat" value="TERLAMBAT">
                            <span class="radio-label-terlambat">TERLAMBAT</span>
                        </label>

                        <label class="status-radio-option">
                            <input type="radio" name="status" id="status_sakit" value="SAKIT">
                            <span class="radio-label-sakit">SAKIT</span>
                        </label>

                        <label class="status-radio-option">
                            <input type="radio" name="status" id="status_izin" value="IZIN">
                            <span class="radio-label-izin">IZIN</span>
                        </label>

                        <label class="status-radio-option">
                            <input type="radio" name="status" id="status_alpa" value="ALPA">
                            <span class="radio-label-alpa">TANPA KETERANGAN</span>
                        </label>
                    </div>

                    <!-- Keterangan (Sesuai Gambar) -->
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-1" style="letter-spacing: 0.5px;">Keterangan</label>
                        <input type="text" name="keterangan" id="modal_keterangan" class="form-control rounded-3" placeholder="Contoh: Sakit demam, Izin acara keluarga, dll." style="padding: 0.6rem 0.85rem; border-color: #cbd5e1;">
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-3 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-4 rounded-3 text-secondary fw-semibold" data-bs-dismiss="modal">TUTUP</button>
                    <button type="submit" class="btn btn-ubah-modal">UBAH</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openSetStatusModal(siswaId, siswaNama, currentStatus, keterangan) {
    document.getElementById('modal_siswa_id').value = siswaId;
    document.getElementById('modal_siswa_nama').innerText = siswaNama;
    document.getElementById('modal_keterangan').value = keterangan || '';

    const statusVal = (currentStatus && currentStatus !== 'BELUM ABSEN') ? currentStatus : 'HADIR';
    
    // Set checked radio button
    const radioHadir = document.getElementById('status_hadir');
    const radioTerlambat = document.getElementById('status_terlambat');
    const radioSakit = document.getElementById('status_sakit');
    const radioIzin = document.getElementById('status_izin');
    const radioAlpa = document.getElementById('status_alpa');

    if (radioHadir) radioHadir.checked = (statusVal === 'HADIR');
    if (radioTerlambat) radioTerlambat.checked = (statusVal === 'TERLAMBAT');
    if (radioSakit) radioSakit.checked = (statusVal === 'SAKIT');
    if (radioIzin) radioIzin.checked = (statusVal === 'IZIN');
    if (radioAlpa) radioAlpa.checked = (statusVal === 'ALPA');

    const modal = new bootstrap.Modal(document.getElementById('modalSetStatus'));
    modal.show();
}
</script>
@endsection
