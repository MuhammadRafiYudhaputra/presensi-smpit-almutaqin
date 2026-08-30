@extends('layouts.app')

@section('content')
<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header & Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0.5 text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="fa-solid fa-school text-primary me-3 fs-5"></i>
                <span>Kelola Data Kelas</span>
            </h5>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold shadow-sm btn-sm d-inline-flex align-items-center gap-1.5 text-nowrap" data-bs-toggle="modal" data-bs-target="#modalKenaikanKelas" style="font-size: 0.82rem;">
                <i class="fa-solid fa-graduation-cap me-1"></i>
                <span>Kenaikan Kelas</span>
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-3.5 py-1.5 fw-semibold shadow-sm btn-sm d-inline-flex align-items-center gap-1.5 text-nowrap" data-bs-toggle="modal" data-bs-target="#modalAddKelas" style="font-size: 0.82rem;">
                <i class="fa-solid fa-plus me-1"></i>
                <span>Tambah Kelas</span>
            </button>
        </div>
    </div>

    <!-- Sorting Filter -->
    <form action="{{ route('admin.kelas.index') }}" method="GET" class="d-flex align-items-center gap-2 mb-4">
        <label class="form-label fw-bold text-nowrap mb-0 text-dark small">
            <i class="fa-solid fa-arrow-down-up-across-line text-primary me-1.5"></i> Urutkan:
        </label>
        <select name="sort_by" class="form-select form-select-sm shadow-sm" style="max-width: 230px;" onchange="this.form.submit()">
            <option value="nama_asc" {{ ($sortBy ?? '') === 'nama_asc' ? 'selected' : '' }}>Nama Kelas (A-Z)</option>
            <option value="nama_desc" {{ ($sortBy ?? '') === 'nama_desc' ? 'selected' : '' }}>Nama Kelas (Z-A)</option>
        </select>
    </form>

    <!-- Tabel Data Kelas -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 50px;" class="text-dark">No</th>
                    <th class="text-dark" style="width: 200px;">Nama Kelas</th>
                    <th class="text-dark text-start">Wali Kelas Penanggung Jawab</th>
                    <th class="text-dark" style="width: 220px;">Jumlah Peserta Didik</th>
                    <th class="text-center text-dark" style="width: 110px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelases as $idx => $k)
                <tr>
                    <td class="text-center fw-bold">{{ $kelases->firstItem() + $idx }}</td>
                    <td class="fw-bold text-dark">
                        <i class="fa-solid fa-graduation-cap text-primary me-2"></i> Kelas {{ $k->nama_kelas }}
                    </td>
                    <td>
                        @if($k->waliKelas)
                            <div class="fw-semibold text-dark">
                                <i class="fa-solid fa-user-tie text-success me-1"></i> {{ $k->waliKelas->nama }}
                            </div>
                            <small class="text-muted"><i class="fa-solid fa-id-card-clip me-1"></i> NIP: {{ $k->waliKelas->nip ?? '-' }}</small>
                        @else
                            <span class="badge bg-warning text-dark px-2 py-1 rounded-2">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum ditentukan
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill fw-bold">
                            <i class="fa-solid fa-users me-1"></i> {{ $k->siswas ? $k->siswas->where('status', '!=', 'alumni')->count() : 0 }} Siswa Terdaftar
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1 justify-content-center">
                            <button type="button" class="btn btn-primary btn-sm rounded-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit Data Kelas" onclick="openEditKelasModal({{ json_encode($k) }})">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <form action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kelas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm rounded-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Hapus Kelas">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="fa-solid fa-school fs-2 d-block mb-2 text-muted"></i>
                        Belum ada data kelas terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($kelases->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <small class="text-muted">Menampilkan {{ $kelases->firstItem() ?? 0 }} - {{ $kelases->lastItem() ?? 0 }} dari total {{ $kelases->total() }} kelas</small>
        {{ $kelases->links() }}
    </div>
    @endif
</div>

<!-- Modal Tambah Kelas Baru -->
<div class="modal fade" id="modalAddKelas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-plus-circle me-2 text-primary"></i>Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Nama Kelas<span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas" class="form-control" placeholder="" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Wali Kelas Penanggung Jawab</label>
                        <select name="guru_id" class="form-select">
                            <option value="">-- Belum Ditentukan --</option>
                            @foreach($gurus as $g)
                                <option value="{{ $g->id }}">{{ $g->nama }} (NIP: {{ $g->nip ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kelas -->
<div class="modal fade" id="modalEditKelas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit Data Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditKelas" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas" id="edit_nama_kelas" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Wali Kelas Penanggung Jawab</label>
                        <select name="guru_id" id="edit_guru_id" class="form-select">
                            <option value="">-- Belum Ditentukan --</option>
                            <option value="">-- Belum Ditentukan --</option>
                            @foreach($gurus as $g)
                                <option value="{{ $g->id }}">{{ $g->nama }} (NIP: {{ $g->nip ?? '-' }})</option>
                            @endforeach
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

<!-- Modal Kenaikan Kelas Otomatis & Pengecualian Tinggal Kelas -->
<div class="modal fade" id="modalKenaikanKelas" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-success bg-opacity-10 border-0 pb-2">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-graduation-cap me-2 text-success"></i>Proses Kenaikan Kelas (Tahun Ajaran Baru)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.kenaikan.proses') }}" method="POST" onsubmit="return confirm('PERHATIAN: Tindakan ini akan memproses kenaikan kelas seluruh siswa aktif secara otomatis (Kelas 7 -> 8, Kelas 8 -> 9, dan Kelas 9 -> Alumni/Lulus). Siswa yang dicentang di bawah akan DIKECUALIKAN (Tinggal Kelas). Lanjutkan?')">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info border-info d-flex align-items-center mb-3 py-2">
                        <i class="fa-solid fa-circle-info fs-4 me-3 text-info"></i>
                        <small class="text-dark">
                            <strong>Alur Kenaikan Otomatis:</strong><br>
                            - Siswa <strong>Kelas 7</strong> otomatis naik ke <strong>Kelas 8</strong>.<br>
                            - Siswa <strong>Kelas 8</strong> otomatis naik ke <strong>Kelas 9</strong>.<br>
                            - Siswa <strong>Kelas 9</strong> otomatis dialihkan statusnya menjadi <strong>Arsip Alumni (Lulus)</strong>.
                        </small>
                    </div>

                    <h6 class="fw-bold text-danger mb-2"><i class="fa-solid fa-user-xmark me-1"></i> Pengecualian Siswa (Tinggal Kelas):</h6>
                    <p class="small text-muted mb-2">Centang nama siswa di bawah jika siswa tersebut <strong>TIDAK NAIK KELAS</strong> agar tetap dipertahankan pada kelas saat ini:</p>

                    <!-- Live Filter Search Box di dalam Modal -->
                    <div class="mb-3">
                        <input type="text" id="searchSiswaTinggal" class="form-control form-control-sm" placeholder="Cari nama siswa tinggal kelas..." onkeyup="filterSiswaList()">
                    </div>

                    <div class="border rounded-3 p-3 bg-light" style="max-height: 250px; overflow-y: auto;" id="listSiswaContainer">
                        @if(isset($allActiveSiswa) && count($allActiveSiswa) > 0)
                            @foreach($allActiveSiswa as $s)
                            <div class="form-check mb-2 siswa-item" data-nama="{{ strtolower($s->nama) }} {{ strtolower($s->nisn) }}">
                                <input class="form-check-input" type="checkbox" name="except_siswa_ids[]" value="{{ $s->id }}" id="except_{{ $s->id }}">
                                <label class="form-check-label d-flex justify-content-between align-items-center cursor-pointer" for="except_{{ $s->id }}">
                                    <span class="text-dark fw-semibold">{{ $s->nama }} (NISN: {{ $s->nisn }})</span>
                                    <span class="badge bg-secondary">Kelas {{ $s->kelas->nama_kelas ?? '-' }}</span>
                                </label>
                            </div>
                            @endforeach
                        @else
                            <span class="text-muted small">Tidak ada data siswa aktif yang ditemukan.</span>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-check-double me-1"></i> Eksekusi Kenaikan Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditKelasModal(kelas) {
    document.getElementById('edit_nama_kelas').value = kelas.nama_kelas;
    document.getElementById('edit_guru_id').value = kelas.guru_id || '';
    document.getElementById('formEditKelas').action = `/admin/kelas/${kelas.id}`;
    const modal = new bootstrap.Modal(document.getElementById('modalEditKelas'));
    modal.show();
}

function filterSiswaList() {
    const input = document.getElementById('searchSiswaTinggal').value.toLowerCase();
    const items = document.querySelectorAll('.siswa-item');
    items.forEach(item => {
        const text = item.getAttribute('data-nama');
        if (text.includes(input)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
@endsection
