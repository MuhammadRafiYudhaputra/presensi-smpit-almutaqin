@extends('layouts.app')

@section('content')
<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header Page (Tanpa Tombol Tambah/Import Master) -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center flex-wrap">
                <i class="fa-solid fa-user-group text-primary me-2 fs-4"></i> Data Orang Tua / Wali Siswa
                @if($kelas)
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-1 rounded-pill fs-6 ms-2">
                        <i class="fa-solid fa-chalkboard-user me-1"></i> Kelas {{ $kelas->nama_kelas }}
                    </span>
                @endif
            </h5>
            <small class="text-muted">Data orang tua, wali murid siswa, nomor WhatsApp, dan anak yang terhubung di kelas Anda</small>
        </div>
    </div>

    <!-- Search & Sorting Row (Persis seperti Halaman Admin) -->
    <form action="{{ route('guru.siswa.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
        <div class="col-md-7">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari Nama Ayah, Nama Ibu, Nama Wali, Siswa, No WA, atau Alamat..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-primary px-4 fw-semibold">Cari</button>
            </div>
        </div>

        <div class="col-md-5">
            <div class="d-flex align-items-center justify-content-md-end gap-2">
                <label class="form-label fw-bold text-nowrap mb-0 text-dark">
                    <i class="fa-solid fa-arrow-down-up-across-line text-primary me-1"></i> Urutkan:
                </label>
                <select name="sort_by" class="form-select shadow-sm" style="max-width: 250px;" onchange="this.form.submit()">
                    <option value="ayah_asc" {{ ($sortBy ?? '') === 'ayah_asc' ? 'selected' : '' }}>Nama Ayah (A-Z)</option>
                    <option value="ayah_desc" {{ ($sortBy ?? '') === 'ayah_desc' ? 'selected' : '' }}>Nama Ayah (Z-A)</option>
                    <option value="ibu_asc" {{ ($sortBy ?? '') === 'ibu_asc' ? 'selected' : '' }}>Nama Ibu (A-Z)</option>
                    <option value="wali_asc" {{ ($sortBy ?? '') === 'wali_asc' ? 'selected' : '' }}>Nama Wali (A-Z)</option>
                    <option value="no_wa" {{ ($sortBy ?? '') === 'no_wa' ? 'selected' : '' }}>No. WhatsApp</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Tabel Data Orang Tua / Wali (Persis seperti Halaman Admin) -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 45px;" class="text-dark">No</th>
                    <th class="text-dark text-start" style="width: 170px;">Data Orang Tua</th>
                    <th class="text-dark text-start" style="width: 140px;">Wali Siswa</th>
                    <th class="text-dark text-center" style="width: 145px;">No. WhatsApp</th>
                    <th class="text-dark text-start" style="width: 160px;">Peserta Didik</th>
                    <th class="text-dark text-start">Alamat Domisili</th>
                    <th class="text-center text-dark" style="width: 80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orangTuas as $idx => $ot)
                <tr>
                    <td class="text-center fw-bold">{{ $orangTuas->firstItem() + $idx }}</td>
                    <td>
                        @if($ot->nama_ayah || $ot->nama_ibu)
                            @if($ot->nama_ayah)
                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-user-tie text-primary fs-6"></i>
                                    <span>{{ $ot->nama_ayah }}</span>
                                </div>
                            @endif
                            @if($ot->nama_ibu)
                                <div class="fw-bold text-dark d-flex align-items-center gap-2 {{ $ot->nama_ayah ? 'mt-1.5' : '' }}">
                                    <i class="fa-solid fa-person-dress text-danger fs-6"></i>
                                    <span>{{ $ot->nama_ibu }}</span>
                                </div>
                            @endif
                        @else
                            <span class="text-muted fst-italic">- Data Orang Tua Kosong -</span>
                        @endif
                    </td>
                    <td>
                        @if($ot->nama_wali)
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="fa-solid fa-hands-holding-child text-warning fs-6"></i>
                                <span>{{ $ot->nama_wali }}</span>
                            </div>
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-2 py-0.5 rounded-pill mt-1" style="font-size: 0.75rem;">
                                {{ $ot->hubungan_wali ?: 'Wali Siswa' }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($ot->no_wa)
                            @php
                                $cleanWa = preg_replace('/[^0-9]/', '', $ot->no_wa);
                                if (substr($cleanWa, 0, 1) === '0') {
                                    $cleanWa = '62' . substr($cleanWa, 1);
                                }
                            @endphp
                            <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode('Assalamu\'alaikum Wr. Wb. Bapak/Ibu wali murid. Kami dari Wali Kelas ' . ($kelas->nama_kelas ?? '') . '...') }}" target="_blank" class="badge bg-success bg-opacity-10 text-success border border-success px-2.5 py-1.5 rounded-pill shadow-sm text-nowrap text-decoration-none" style="font-size: 0.82rem;" title="Klik untuk Chat WhatsApp">
                                <i class="fa-brands fa-whatsapp me-1.5"></i> {{ $ot->no_wa }}
                            </a>
                        @else
                            <span class="badge bg-light text-muted border px-2 py-1">Tidak Ada WA</span>
                        @endif
                    </td>
                    <td>
                        @if($ot->siswas && count($ot->siswas) > 0)
                            @foreach($ot->siswas as $anak)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 mb-1 d-inline-block rounded-2">
                                    {{ $anak->nama }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-muted small">- Belum terhubung -</span>
                        @endif
                    </td>
                    <td>
                        <span class="small text-dark">{{ $ot->alamat ?? '-' }}</span>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2.5 py-1 fw-semibold shadow-sm d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;" title="Lihat Rincian Kontak" onclick="openDetailOrangTuaModal({{ json_encode($ot) }})">
                            <i class="fa-solid fa-id-card"></i> Detail
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="fa-solid fa-users-slash fs-2 d-block mb-2 text-muted"></i>
                        Tidak ada data orang tua / wali yang ditemukan di kelas ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <small class="text-muted">Menampilkan {{ $orangTuas->firstItem() ?? 0 }} - {{ $orangTuas->lastItem() ?? 0 }} dari total {{ $orangTuas->total() }} orang tua / wali siswa</small>
        {{ $orangTuas->links() }}
    </div>
</div>

<!-- Modal Detail Kontak Orang Tua / Wali -->
<div class="modal fade" id="modalDetailOrangTua" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-id-card text-primary me-2"></i>Rincian Kontak Orang Tua / Wali</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="p-3 bg-light rounded-3 mb-3 border text-center">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold fs-3 mb-2 shadow-sm" style="width: 55px; height: 55px;">
                        <i class="fa-solid fa-user-group fs-4"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0" id="detTitleNama">-</h6>
                    <small class="text-muted" id="detAnakList">-</small>
                </div>

                <ul class="list-group list-group-flush rounded-3 border">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">Nama Ayah</span>
                        <strong class="text-dark small" id="detNamaAyah">-</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">Nama Ibu</span>
                        <strong class="text-dark small" id="detNamaIbu">-</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">Wali Siswa</span>
                        <strong class="text-dark small" id="detNamaWali">-</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">No. WhatsApp</span>
                        <div id="detWaLink">
                            <strong class="text-dark small" id="detNoWa">-</strong>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                        <span class="text-muted small">Alamat Domisili</span>
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
function openDetailOrangTuaModal(ot) {
    const namaUtama = ot.nama_ayah || ot.nama_ibu || ot.nama_wali || 'Orang Tua Siswa';
    document.getElementById('detTitleNama').innerText = namaUtama;

    let anakNames = [];
    if (ot.siswas && ot.siswas.length > 0) {
        ot.siswas.forEach(s => anakNames.push(s.nama));
    }
    document.getElementById('detAnakList').innerText = anakNames.length > 0 ? 'Orang tua dari: ' + anakNames.join(', ') : '-';

    document.getElementById('detNamaAyah').innerText = ot.nama_ayah || '-';
    document.getElementById('detNamaIbu').innerText = ot.nama_ibu || '-';
    document.getElementById('detNamaWali').innerText = ot.nama_wali ? (ot.nama_wali + ' (' + (ot.hubungan_wali || 'Wali') + ')') : '-';
    
    if (ot.no_wa) {
        let cleanWa = ot.no_wa.replace(/[^0-9]/g, '');
        if (cleanWa.startsWith('0')) {
            cleanWa = '62' + cleanWa.substring(1);
        }
        const waUrl = `https://wa.me/${cleanWa}?text=${encodeURIComponent('Assalamu\'alaikum Wr. Wb. Bapak/Ibu wali murid...')}`;
        document.getElementById('detWaLink').innerHTML = `
            <a href="${waUrl}" target="_blank" class="badge bg-success text-white px-2.5 py-1.5 rounded-pill text-decoration-none shadow-sm">
                <i class="fa-brands fa-whatsapp me-1"></i> ${ot.no_wa}
            </a>
        `;
    } else {
        document.getElementById('detWaLink').innerHTML = '<span class="text-muted small">-</span>';
    }

    document.getElementById('detAlamat').innerText = ot.alamat || '-';

    const modal = new bootstrap.Modal(document.getElementById('modalDetailOrangTua'));
    modal.show();
}
</script>
@endsection
