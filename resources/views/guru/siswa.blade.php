@extends('layouts.app')

@section('content')
<style>
    .stat-card-custom {
        background: #ffffff;
        border-radius: 14px;
        padding: 1rem 1.25rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .stat-card-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    .stat-icon-custom {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .wa-chat-pill {
        background-color: #f0fdf4;
        color: #16a34a !important;
        border: 1px solid #bbf7d0;
        border-radius: 50rem;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 0.35rem 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .wa-chat-pill:hover {
        background-color: #22c55e;
        color: #ffffff !important;
        border-color: #22c55e;
        box-shadow: 0 2px 8px rgba(34, 197, 94, 0.25);
    }
</style>

<!-- Stat Cards Section -->
<div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
        <div class="stat-card-custom">
            <div class="stat-icon-custom bg-primary bg-opacity-10 text-primary">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <small class="text-muted fw-semibold d-block" style="font-size: 0.78rem;">Total Siswa Binaan</small>
                <h5 class="fw-bold text-dark mb-0">{{ $totalSiswa }} <span class="fs-6 text-muted fw-normal">Siswa</span></h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card-custom">
            <div class="stat-icon-custom bg-info bg-opacity-10 text-info">
                <i class="fa-solid fa-mars"></i>
            </div>
            <div>
                <small class="text-muted fw-semibold d-block" style="font-size: 0.78rem;">Laki-Laki (L)</small>
                <h5 class="fw-bold text-dark mb-0">{{ $totalL }} <span class="fs-6 text-muted fw-normal">Siswa</span></h5>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card-custom">
            <div class="stat-icon-custom bg-danger bg-opacity-10 text-danger">
                <i class="fa-solid fa-venus"></i>
            </div>
            <div>
                <small class="text-muted fw-semibold d-block" style="font-size: 0.78rem;">Perempuan (P)</small>
                <h5 class="fw-bold text-dark mb-0">{{ $totalP }} <span class="fs-6 text-muted fw-normal">Siswi</span></h5>
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

    <!-- Search & Sort Bar (Selaras dengan Halaman Orang Tua Admin) -->
    <form action="{{ route('guru.siswa.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
        <div class="col-md-7">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari Nama Siswa, NISN, Ayah/Ibu, No WA, atau Alamat..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-primary px-4 fw-semibold">Cari</button>
            </div>
        </div>

        <div class="col-md-5">
            <div class="d-flex align-items-center justify-content-md-end gap-2">
                <label class="form-label fw-bold text-nowrap mb-0 text-dark small">
                    <i class="fa-solid fa-arrow-down-up-across-line text-primary me-1"></i> Urutkan:
                </label>
                <select name="sort_by" class="form-select shadow-sm form-select-sm" style="max-width: 220px;" onchange="this.form.submit()">
                    <option value="nama_asc" {{ ($sortBy ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama Siswa (A-Z)</option>
                    <option value="nama_desc" {{ ($sortBy ?? '') === 'nama_desc' ? 'selected' : '' }}>Nama Siswa (Z-A)</option>
                    <option value="nisn" {{ ($sortBy ?? '') === 'nisn' ? 'selected' : '' }}>NISN Siswa</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Table of Students (Selaras dengan Format Halaman Orang Tua Admin) -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 45px;" class="text-dark">No</th>
                    <th style="width: 120px;" class="text-dark">NISN / NIS</th>
                    <th class="text-dark text-start" style="min-width: 190px;">Peserta Didik</th>
                    <th class="text-dark text-start" style="width: 170px;">Data Orang Tua</th>
                    <th class="text-dark text-start" style="width: 140px;">Wali Siswa</th>
                    <th class="text-dark text-center" style="width: 155px;">No. WhatsApp</th>
                    <th class="text-dark text-start">Alamat Domisili</th>
                    <th style="width: 85px;" class="text-dark text-center">Aksi</th>
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
                            <div class="rounded-circle {{ $s->jenis_kelamin === 'L' ? 'bg-primary' : 'bg-danger' }} bg-opacity-10 {{ $s->jenis_kelamin === 'L' ? 'text-primary' : 'text-danger' }} d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 34px; height: 34px; font-size: 0.82rem;">
                                {{ strtoupper(substr($s->nama, 0, 1)) }}
                            </div>
                            <div>
                                <span class="fw-bold text-dark d-block">{{ $s->nama }}</span>
                                <span class="badge {{ $s->jenis_kelamin === 'L' ? 'bg-primary' : 'bg-danger' }} bg-opacity-10 {{ $s->jenis_kelamin === 'L' ? 'text-primary border-primary' : 'text-danger border-danger' }} border px-1.5 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                                    {{ $s->jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan' }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($s->orangTua && ($s->orangTua->nama_ayah || $s->orangTua->nama_ibu))
                            @if($s->orangTua->nama_ayah)
                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-user-tie text-primary fs-6"></i>
                                    <span>{{ $s->orangTua->nama_ayah }}</span>
                                </div>
                            @endif
                            @if($s->orangTua->nama_ibu)
                                <div class="fw-bold text-dark d-flex align-items-center gap-2 {{ $s->orangTua->nama_ayah ? 'mt-1.5' : '' }}">
                                    <i class="fa-solid fa-person-dress text-danger fs-6"></i>
                                    <span>{{ $s->orangTua->nama_ibu }}</span>
                                </div>
                            @endif
                        @else
                            <span class="text-muted fst-italic small">- Data Belum Diisi -</span>
                        @endif
                    </td>
                    <td>
                        @if($s->orangTua && $s->orangTua->nama_wali)
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="fa-solid fa-hands-holding-child text-warning fs-6"></i>
                                <span>{{ $s->orangTua->nama_wali }}</span>
                            </div>
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-2 py-0.5 rounded-pill mt-1" style="font-size: 0.72rem;">
                                {{ $s->orangTua->hubungan_wali ?: 'Wali Siswa' }}
                            </span>
                        @else
                            <span class="text-muted small">-</span>
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
                                <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode('Assalamu\'alaikum Wr. Wb. Bapak/Ibu wali dari ananda ' . $s->nama . ' (Kelas ' . ($s->kelas->nama_kelas ?? '') . ')...') }}" target="_blank" class="wa-chat-pill shadow-sm" title="Klik untuk Chat WhatsApp">
                                    <i class="fa-brands fa-whatsapp fs-6 text-success"></i>
                                    <span>{{ $s->orangTua->no_wa }}</span>
                                </a>
                            </div>
                        @else
                            <span class="badge bg-light text-muted border px-2 py-1 small">Tidak Ada WA</span>
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
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 fw-semibold shadow-sm" onclick="showDetailModal({{ json_encode($s) }})">
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
    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <small class="text-muted">Menampilkan {{ $siswas->firstItem() ?? 0 }} - {{ $siswas->lastItem() ?? 0 }} dari total {{ $siswas->total() }} siswa binaan</small>
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
                        <strong class="text-dark small" id="detNisn">-</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">NIS</span>
                        <strong class="text-dark small" id="detNis">-</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">Nama Ayah</span>
                        <strong class="text-dark small" id="detAyah">-</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">Nama Ibu</span>
                        <strong class="text-dark small" id="detIbu">-</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">Wali Siswa</span>
                        <strong class="text-dark small" id="detWali">-</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">No. WhatsApp</span>
                        <div id="detWaBox">
                            <strong class="text-dark small" id="detWa">-</strong>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                        <span class="text-muted small">Alamat</span>
                        <small class="text-dark text-end ms-3" id="detAlamat">-</small>
                    </li>
                </ul>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function showDetailModal(siswa) {
    document.getElementById('detInisial').innerText = siswa.nama.charAt(0).toUpperCase();
    document.getElementById('detNama').innerText = siswa.nama;
    document.getElementById('detKelas').innerText = 'Kelas ' + (siswa.kelas ? siswa.kelas.nama_kelas : '-');
    document.getElementById('detJK').innerText = siswa.jenis_kelamin === 'L' ? 'Laki-Laki (L)' : 'Perempuan (P)';
    document.getElementById('detNisn').innerText = siswa.nisn || '-';
    document.getElementById('detNis').innerText = siswa.nis || '-';
    
    if (siswa.orang_tua) {
        document.getElementById('detAyah').innerText = siswa.orang_tua.nama_ayah || '-';
        document.getElementById('detIbu').innerText = siswa.orang_tua.nama_ibu || '-';
        document.getElementById('detWali').innerText = siswa.orang_tua.nama_wali ? (siswa.orang_tua.nama_wali + ' (' + (siswa.orang_tua.hubungan_wali || 'Wali') + ')') : '-';
        
        if (siswa.orang_tua.no_wa) {
            let cleanWa = siswa.orang_tua.no_wa.replace(/[^0-9]/g, '');
            if (cleanWa.startsWith('0')) {
                cleanWa = '62' + cleanWa.substring(1);
            }
            const waUrl = `https://wa.me/${cleanWa}?text=${encodeURIComponent('Assalamu\'alaikum Wr. Wb. Bapak/Ibu wali dari ananda ' + siswa.nama + '...')}`;
            document.getElementById('detWaBox').innerHTML = `
                <a href="${waUrl}" target="_blank" class="badge bg-success text-white px-2.5 py-1.5 rounded-pill text-decoration-none shadow-sm">
                    <i class="fa-brands fa-whatsapp me-1"></i> ${siswa.orang_tua.no_wa}
                </a>
            `;
        } else {
            document.getElementById('detWaBox').innerHTML = '<span class="text-muted small">-</span>';
        }

        document.getElementById('detAlamat').innerText = siswa.orang_tua.alamat || '-';
    } else {
        document.getElementById('detAyah').innerText = '-';
        document.getElementById('detIbu').innerText = '-';
        document.getElementById('detWali').innerText = '-';
        document.getElementById('detWaBox').innerHTML = '<span class="text-muted small">-</span>';
        document.getElementById('detAlamat').innerText = '-';
    }

    const modal = new bootstrap.Modal(document.getElementById('modalDetailSiswa'));
    modal.show();
}
</script>
@endsection
