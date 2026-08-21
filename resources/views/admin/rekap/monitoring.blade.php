@extends('layouts.app')

@section('content')
<style>
    /* Table Styling Sesuai Gambar Referensi */
    .table-presensi-minimal {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table-presensi-minimal thead th {
        color: #7e22ce;
        font-weight: 700;
        font-size: 0.92rem;
        border-bottom: 2px solid #f1f5f9;
        padding: 0.85rem 1rem;
        background: #ffffff;
    }
    .table-presensi-minimal tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f8fafc;
        font-size: 0.88rem;
    }
    .table-presensi-minimal tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Solid Status Badges Sesuai Gambar Referensi */
    .badge-status-solid {
        padding: 0.4rem 1rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.82rem;
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }
    .badge-hadir-solid { background-color: #22c55e; color: #ffffff; }
    .badge-terlambat-solid { background-color: #f59e0b; color: #ffffff; }
    .badge-sakit-solid { background-color: #64748b; color: #ffffff; }
    .badge-izin-solid { background-color: #00c0ef; color: #ffffff; }
    .badge-alpa-solid { background-color: #ef4444; color: #ffffff; }
    .badge-belum-solid { background-color: #e2e8f0; color: #64748b; }

    /* Button Edit Aksi Cyan Sesuai Gambar */
    .btn-edit-action {
        background-color: #00c0ef;
        border: 1px solid #00c0ef;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 0.35rem 0.9rem;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }
    .btn-edit-action:hover {
        background-color: #0891b2;
        border-color: #0891b2;
        color: #ffffff;
    }

    /* Radio Ubah Kehadiran */
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
    <!-- Header & Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center">
                <i class="fa-solid fa-list-check text-primary me-2 fs-4"></i> Absensi Siswa
            </h5>
            <small class="text-muted">Daftar presensi harian seluruh peserta didik, jam masuk & pulang, serta keterangan kehadiran</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.rekap.cetak', ['mode' => 'harian', 'tanggal' => $tanggal, 'kelas_id' => $kelasId]) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-semibold shadow-sm">
                <i class="fa-solid fa-print me-1"></i> Cetak Absensi Harian
            </a>
        </div>
    </div>

    <!-- Filter & Parameter Form -->
    <form method="GET" action="{{ route('admin.rekap.monitoring') }}" class="row g-3 mb-4 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-bold text-dark mb-1">Pilih Tanggal Presensi</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control shadow-sm" onchange="this.form.submit()">
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold text-dark mb-1">Filter Kelas</label>
            <select name="kelas_id" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelases as $k)
                    <option value="{{ $k->id }}" {{ ($kelasId ?? '') == $k->id ? 'selected' : '' }}>Kelas {{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
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

    <!-- Table Absensi Harian (Format Bersih Sesuai Contoh Gambar) -->
    <div class="table-responsive">
        <table class="table-presensi-minimal align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th style="width: 130px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 140px; text-align: center;">Kehadiran</th>
                    <th style="width: 130px; text-align: center;">Jam masuk</th>
                    <th style="width: 130px; text-align: center;">Jam pulang</th>
                    <th>Keterangan</th>
                    <th style="width: 100px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($harianData as $idx => $row)
                <tr>
                    <td class="text-muted fw-semibold">{{ $idx + 1 }}</td>
                    <td class="text-dark fw-bold">{{ $row->siswa->nisn }}</td>
                    <td class="fw-bold text-dark">{{ $row->siswa->nama }}</td>
                    <td class="text-center">
                        @if($row->status === 'HADIR')
                            <span class="badge-status-solid badge-hadir-solid">Hadir</span>
                        @elseif($row->status === 'TERLAMBAT')
                            <span class="badge-status-solid badge-terlambat-solid">Terlambat</span>
                        @elseif($row->status === 'IZIN')
                            <span class="badge-status-solid badge-izin-solid">Izin</span>
                        @elseif($row->status === 'SAKIT')
                            <span class="badge-status-solid badge-sakit-solid">Sakit</span>
                        @elseif($row->status === 'ALPA')
                            <span class="badge-status-solid badge-alpa-solid">Alpa</span>
                        @else
                            <span class="badge-status-solid badge-belum-solid">Belum</span>
                        @endif
                    </td>
                    <td class="text-center text-muted font-monospace">{{ $row->jam_masuk ?? '-' }}</td>
                    <td class="text-center text-muted font-monospace">{{ $row->jam_pulang ?? '-' }}</td>
                    <td class="text-muted">{{ $row->keterangan ?? '-' }}</td>
                    <td class="text-center">
                        <button type="button" class="btn-edit-action shadow-sm" onclick="openSetStatusModal({{ $row->siswa->id }}, '{{ addslashes($row->siswa->nama) }}', '{{ $row->status }}', '{{ addslashes($row->keterangan ?? '') }}')">
                            <i class="fa-solid fa-pen"></i> EDIT
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="fa-solid fa-folder-open fs-2 d-block mb-2 text-muted"></i>
                        Tidak ada data siswa ditemukan.
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
