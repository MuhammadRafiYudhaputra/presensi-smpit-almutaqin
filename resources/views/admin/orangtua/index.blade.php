@extends('layouts.app')

@section('content')
<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header & Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0.5 text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="fa-solid fa-user-group text-primary me-3 fs-5"></i>
                <span>Kelola Data Orang Tua / Wali</span>
            </h5>
            <small class="text-muted ms-4 ps-2 d-block" style="font-size: 0.78rem;">Data orang tua, wali murid siswa, nomor WhatsApp, dan anak yang terhubung</small>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap flex-sm-nowrap flex-shrink-0">
            <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold shadow-sm btn-sm text-nowrap d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalImportOrangTua" style="font-size: 0.82rem;">
                <i class="fa-solid fa-file-import me-1"></i>
                <span>Import Orang Tua</span>
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-3.5 py-1.5 fw-semibold shadow-sm btn-sm text-nowrap d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalAddOrangTua" style="font-size: 0.82rem;">
                <i class="fa-solid fa-plus me-1"></i>
                <span>Tambah Orang Tua</span>
            </button>
        </div>
    </div>

    <!-- Search Bar (Tanpa Urutkan Redundan) -->
    <form action="{{ route('admin.orangtua.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-2" placeholder="Cari Nama Ayah, Nama Ibu, Nama Wali, No WA, atau Alamat..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-primary px-4 fw-semibold">Cari</button>
            </div>
        </div>
    </form>

    <!-- Tabel Data Orang Tua / Wali -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 45px;" class="text-dark">No</th>
                    <th class="text-dark text-start" style="width: 170px;">Data Orang Tua</th>
                    <th class="text-dark text-start" style="width: 140px;">Wali Siswa</th>
                    <th class="text-dark text-center" style="width: 135px;">No. WhatsApp</th>
                    <th class="text-dark text-start" style="width: 160px;">Peserta Didik</th>
                    <th class="text-dark text-start">Alamat Domisili</th>
                    <th class="text-center text-dark" style="width: 90px;">Aksi</th>
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
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-2.5 py-1.5 rounded-pill shadow-sm text-nowrap" style="font-size: 0.82rem;">
                            <i class="fa-brands fa-whatsapp me-1.5"></i> {{ $ot->no_wa }}
                        </span>
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
                        <div class="d-inline-flex gap-1 justify-content-center">
                            <button type="button" class="btn btn-primary btn-sm rounded-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit Data Orang Tua / Wali" onclick="openEditOrangTuaModal({{ json_encode($ot) }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <form action="{{ route('admin.orangtua.destroy', $ot->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data orang tua/wali ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm rounded-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="fa-solid fa-users-slash fs-2 d-block mb-2 text-muted"></i>
                        Tidak ada data orang tua / wali yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <small class="text-muted">Menampilkan {{ $orangTuas->firstItem() ?? 0 }} - {{ $orangTuas->lastItem() ?? 0 }} dari total {{ $orangTuas->total() }} orang tua / wali</small>
        {{ $orangTuas->links() }}
    </div>
</div>

<!-- Modal Tambah Orang Tua Baru -->
<div class="modal fade" id="modalAddOrangTua" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Tambah Data Orang Tua / Wali</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.orangtua.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Nama Lengkap Ayah</label>
                            <input type="text" name="nama_ayah" class="form-control" placeholder="">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Nama Lengkap Ibu</label>
                            <input type="text" name="nama_ibu" class="form-control" placeholder="">
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-dark">Nama Lengkap Wali Siswa</label>
                            <input type="text" name="nama_wali" class="form-control" placeholder="">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-dark">Hubungan Wali</label>
                            <input type="text" name="hubungan_wali" class="form-control" placeholder="">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark">No. WhatsApp</label>
                            <input type="text" name="no_wa" class="form-control" placeholder="" required>
                            <small class="text-muted">Nomor ini akan menerima notifikasi otomatis WhatsApp setiap kali anak melakukan presensi scan QR.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">Alamat Tempat Tinggal</label>
                            <textarea name="alamat" class="form-control" rows="2" placeholder=""></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Orang Tua -->
<div class="modal fade" id="modalEditOrangTua" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-user-pen me-2 text-primary"></i>Edit Data Orang Tua / Wali</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditOrangTua" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Nama Lengkap Ayah</label>
                            <input type="text" name="nama_ayah" id="edit_nama_ayah" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Nama Lengkap Ibu</label>
                            <input type="text" name="nama_ibu" id="edit_nama_ibu" class="form-control">
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-dark">Nama Lengkap Wali Siswa</label>
                            <input type="text" name="nama_wali" id="edit_nama_wali" class="form-control">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-dark">Hubungan Wali</label>
                            <input type="text" name="hubungan_wali" id="edit_hubungan_wali" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark">No. WhatsApp Notifikasi <span class="text-danger">*</span></label>
                            <input type="text" name="no_wa" id="edit_no_wa" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">Alamat Tempat Tinggal</label>
                            <textarea name="alamat" id="edit_alamat" class="form-control" rows="2"></textarea>
                        </div>
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

<!-- Modal Import Orang Tua -->
<div class="modal fade" id="modalImportOrangTua" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-file-import me-2 text-primary"></i>Import Data Orang Tua / Wali</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.orangtua.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info border-info small mb-3">
                        <i class="fa-solid fa-circle-info me-1"></i> Unggah file data orang tua/wali format Excel (.xlsx / .csv) untuk menambahkan data kontak WA secara massal.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Pilih File Excel / CSV:</label>
                        <input type="file" name="file_orangtua" class="form-control" accept=".csv, .xlsx, .xls">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Unggah & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditOrangTuaModal(ot) {
    document.getElementById('edit_nama_ayah').value = ot.nama_ayah || '';
    document.getElementById('edit_nama_ibu').value = ot.nama_ibu || '';
    document.getElementById('edit_nama_wali').value = ot.nama_wali || '';
    document.getElementById('edit_hubungan_wali').value = ot.hubungan_wali || '';
    document.getElementById('edit_no_wa').value = ot.no_wa || '';
    document.getElementById('edit_alamat').value = ot.alamat || '';
    document.getElementById('formEditOrangTua').action = `/admin/orangtua/${ot.id}`;
    const modal = new bootstrap.Modal(document.getElementById('modalEditOrangTua'));
    modal.show();
}
</script>
@endsection
