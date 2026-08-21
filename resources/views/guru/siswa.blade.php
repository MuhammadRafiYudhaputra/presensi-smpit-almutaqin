@extends('layouts.app')

@section('content')
<style>
    .stat-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.07);
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .wa-btn {
        background-color: #22c55e;
        color: #ffffff;
        border-radius: 50rem;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 0.35rem 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: background-color 0.2s ease;
    }
    .wa-btn:hover {
        background-color: #16a34a;
        color: #ffffff;
    }
</style>

<!-- Stat Cards Section -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <small class="text-muted fw-semibold">Total Siswa Binaan</small>
                <h4 class="fw-bold text-dark mb-0">{{ $totalSiswa }} <span class="fs-6 text-muted fw-normal">Siswa</span></h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-info bg-opacity-10 text-info">
                <i class="fa-solid fa-mars"></i>
            </div>
            <div>
                <small class="text-muted fw-semibold">Laki-Laki (L)</small>
                <h4 class="fw-bold text-dark mb-0">{{ $totalL }} <span class="fs-6 text-muted fw-normal">Siswa</span></h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                <i class="fa-solid fa-venus"></i>
            </div>
            <div>
                <small class="text-muted fw-semibold">Perempuan (P)</small>
                <h4 class="fw-bold text-dark mb-0">{{ $totalP }} <span class="fs-6 text-muted fw-normal">Siswi</span></h4>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center flex-wrap">
                <i class="fa-solid fa-address-book text-primary me-2 fs-4"></i> Biodata Siswa Binaan
                @if($kelas)
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-1 rounded-pill fs-6 ms-2">
                        <i class="fa-solid fa-chalkboard-user me-1"></i> Kelas {{ $kelas->nama_kelas }}
                    </span>
                @endif
            </h5>
            <small class="text-muted">Informasi lengkap biodata siswa, nama orang tua (ayah/ibu), kontak WhatsApp, dan alamat domisili</small>
        </div>
    </div>

    <!-- Search & Sort Bar (Tanpa Filter Kelas) -->
    <form action="{{ route('guru.siswa.index') }}" method="GET" class="row g-3 mb-4 align-items-end">
        <div class="col-md-7">
            <label class="form-label fw-bold text-dark mb-1">Cari Data Siswa</label>
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Nama siswa, NISN, ayah/ibu, WA, alamat..." value="{{ $search }}">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold text-dark mb-1">Urutkan Data</label>
            <select name="sort_by" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="nama_asc" {{ ($sortBy ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama Siswa (A-Z)</option>
                <option value="nama_desc" {{ ($sortBy ?? '') === 'nama_desc' ? 'selected' : '' }}>Nama Siswa (Z-A)</option>
                <option value="nisn" {{ ($sortBy ?? '') === 'nisn' ? 'selected' : '' }}>NISN Siswa</option>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary rounded-pill fw-bold shadow-sm">
                <i class="fa-solid fa-filter me-1"></i> Cari Data
            </button>
        </div>
    </form>

    <!-- Table of Students -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 50px;" class="text-dark">No</th>
                    <th style="width: 130px;" class="text-dark">NISN / NIS</th>
                    <th class="text-dark text-start">Nama Peserta Didik</th>
                    <th class="text-dark text-start" style="width: 200px;">Nama Orang Tua</th>
                    <th class="text-dark text-center" style="width: 180px;">Kontak WhatsApp</th>
                    <th class="text-dark text-start">Alamat Domisili</th>
                    <th style="width: 90px;" class="text-dark text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $idx => $s)
                <tr>
                    <td class="text-center fw-bold">{{ $siswas->firstItem() + $idx }}</td>
                    <td class="text-center">
                        <span class="fw-bold text-dark d-block">{{ $s->nisn }}</span>
                        @if($s->nis)
                            <small class="text-muted">NIS: {{ $s->nis }}</small>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                {{ strtoupper(substr($s->nama, 0, 1)) }}
                            </div>
                            <div>
                                <span class="fw-bold text-dark d-block">{{ $s->nama }}</span>
                                <small class="text-muted">Kelas {{ $s->kelas->nama_kelas ?? '-' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($s->orangTua)
                            <div>
                                <span class="d-block fw-semibold text-dark"><i class="fa-solid fa-user-tie text-muted me-1 small"></i>{{ $s->orangTua->nama_ayah ?: '-' }}</span>
                                <span class="d-block text-muted small"><i class="fa-solid fa-person-dress text-muted me-1 small"></i>{{ $s->orangTua->nama_ibu ?: '-' }}</span>
                            </div>
                        @else
                            <span class="text-muted fst-italic">- Belum terdata -</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($s->orangTua && $s->orangTua->no_wa)
                            @php
                                $cleanWa = preg_replace('/[^0-9]/', '', $s->orangTua->no_wa);
                                if (substr($cleanWa, 0, 1) === '0') {
                                    $cleanWa = '62' . substr($cleanWa, 1);
                                }
                            @endphp
                            <div class="d-flex flex-column align-items-center gap-1">
                                <span class="fw-semibold text-dark small">{{ $s->orangTua->no_wa }}</span>
                                <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode('Assalamu\'alaikum Wr. Wb. Bapak/Ibu wali dari ananda ' . $s->nama . ' (Kelas ' . ($s->kelas->nama_kelas ?? '') . ')...') }}" target="_blank" class="wa-btn shadow-sm" title="Kirim Pesan WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i> Chat WA
                                </a>
                            </div>
                        @else
                            <span class="badge bg-light text-muted border px-2 py-1">Tidak Ada WA</span>
                        @endif
                    </td>
                    <td>
                        @if($s->orangTua && $s->orangTua->alamat)
                            <small class="text-secondary d-flex align-items-start gap-1">
                                <i class="fa-solid fa-location-dot text-danger mt-1"></i>
                                <span>{{ $s->orangTua->alamat }}</span>
                            </small>
                        @else
                            <span class="text-muted fst-italic small">- Alamat belum diisi -</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold shadow-sm" onclick="showDetailModal({{ json_encode($s) }})">
                            <i class="fa-solid fa-id-card me-1"></i> Detail
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="fa-solid fa-user-slash fs-2 d-block mb-2 text-muted"></i>
                        Data siswa tidak ditemukan untuk kelas ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-end">
        {{ $siswas->links() }}
    </div>
</div>

<!-- Modal Detail Biodata Siswa -->
<div class="modal fade" id="modalDetailSiswa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-id-card me-2 text-primary"></i>Profil & Biodata Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Profile Header Card -->
                <div class="p-3 bg-light rounded-3 mb-3 border text-center">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold fs-3 mb-2 shadow-sm" style="width: 60px; height: 60px;" id="detInisial">
                        S
                    </div>
                    <h5 class="fw-bold text-dark mb-1" id="detNama">-</h5>
                    <span class="badge bg-primary px-3 py-1 rounded-pill" id="detKelas">Kelas -</span>
                    <span class="badge bg-secondary px-2 py-1 rounded-pill ms-1" id="detJK">JK: -</span>
                </div>

                <ul class="list-group list-group-flush rounded-3 border">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">NISN</span>
                        <span class="fw-bold text-dark" id="detNisn">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">NIS</span>
                        <span class="fw-semibold text-dark" id="detNis">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">Nama Ayah</span>
                        <span class="fw-semibold text-dark" id="detAyah">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">Nama Ibu</span>
                        <span class="fw-semibold text-dark" id="detIbu">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">No. WhatsApp Orang Tua</span>
                        <span class="fw-bold text-success" id="detWa">-</span>
                    </li>
                    <li class="list-group-item py-2">
                        <span class="text-muted small d-block mb-1">Alamat Tempat Tinggal / Domisili:</span>
                        <span class="fw-semibold text-dark small d-block" id="detAlamat">-</span>
                    </li>
                </ul>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function showDetailModal(siswa) {
    document.getElementById('detInisial').innerText = (siswa.nama || 'S').substring(0, 1).toUpperCase();
    document.getElementById('detNama').innerText = siswa.nama || '-';
    document.getElementById('detKelas').innerText = 'Kelas ' + (siswa.kelas ? siswa.kelas.nama_kelas : '-');
    document.getElementById('detJK').innerText = (siswa.jenis_kelamin === 'L' ? 'Laki-Laki (L)' : 'Perempuan (P)');
    document.getElementById('detNisn').innerText = siswa.nisn || '-';
    document.getElementById('detNis').innerText = siswa.nis || '-';

    if (siswa.orang_tua) {
        document.getElementById('detAyah').innerText = siswa.orang_tua.nama_ayah || '-';
        document.getElementById('detIbu').innerText = siswa.orang_tua.nama_ibu || '-';
        document.getElementById('detWa').innerText = siswa.orang_tua.no_wa || '-';
        document.getElementById('detAlamat').innerText = siswa.orang_tua.alamat || '-';
    } else {
        document.getElementById('detAyah').innerText = '-';
        document.getElementById('detIbu').innerText = '-';
        document.getElementById('detWa').innerText = '-';
        document.getElementById('detAlamat').innerText = '-';
    }

    const modal = new bootstrap.Modal(document.getElementById('modalDetailSiswa'));
    modal.show();
}
</script>
@endsection
