@extends('layouts.app')

@section('content')
<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header & Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h5 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-school me-2 text-primary"></i>Kelola Data Kelas & Rombel</h5>
            <small class="text-muted">Manajemen kelas, wali kelas penanggung jawab, dan kenaikan kelas tahun ajaran baru</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-warning rounded-pill px-4 shadow-sm fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalKenaikanKelas">
                <i class="fa-solid fa-angles-up me-1"></i> Proses Kenaikan Kelas
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAddKelas">
                <i class="fa-solid fa-plus me-1"></i> Tambah Kelas Baru
            </button>
        </div>
    </div>

    <!-- Tabel Daftar Kelas -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;" class="text-center text-dark">ID</th>
                    <th class="text-dark">Nama Kelas / Rombel</th>
                    <th class="text-dark">Wali Kelas</th>
                    <th class="text-center text-dark">Jumlah Siswa</th>
                    <th class="text-center text-dark" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelases as $k)
                <tr>
                    <td class="text-center text-muted fw-bold">#{{ $k->id }}</td>
                    <td class="fw-bold text-primary fs-6">Kelas {{ $k->nama_kelas }}</td>
                    <td>
                        @if($k->waliKelas)
                            <span class="fw-semibold text-dark">{{ $k->waliKelas->nama }}</span>
                            <small class="text-muted d-block">NIP: {{ $k->waliKelas->nip ?? '-' }}</small>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-muted border">Belum Ditugaskan</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary rounded-pill px-3 py-2">
                            {{ $k->siswas ? $k->siswas->where('status', '!=', 'alumni')->count() : 0 }} Siswa Aktif
                        </span>
                    </td>
                    <td class="text-center">
                        <form action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rombel kelas ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Hapus Kelas">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">Belum ada data rombel kelas terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $kelases->links() }}
    </div>
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
                        <label class="form-label fw-semibold text-dark">Nama Kelas / Rombel <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: 7A, 8B, 9C" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Wali Kelas Penanggung Jawab</label>
                        <select name="guru_id" class="form-select">
                            <option value="">-- Pilih Guru Wali Kelas --</option>
                            @foreach($gurus as $g)
                                <option value="{{ $g->id }}">{{ $g->nama }} (NIP: {{ $g->nip ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kenaikan Kelas Otomatis & Pengecualian Tinggal Kelas -->
<div class="modal fade" id="modalKenaikanKelas" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-warning bg-opacity-10 border-0 pb-2">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-graduation-cap me-2 text-warning"></i>Proses Kenaikan Kelas Tahun Ajaran Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.kenaikan.proses') }}" method="POST" onsubmit="return confirm('PERHATIAN: Tindakan ini akan menaikkan tingkat seluruh rombel siswa aktif (Kelas 7 -> 8, Kelas 8 -> 9, dan Kelas 9 -> Alumni/Lulus). Siswa yang dicentang di bawah akan DIKECUALIKAN (Tinggal Kelas). Lanjutkan proses?')">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning border-warning d-flex align-items-center mb-3 py-2">
                        <i class="fa-solid fa-triangle-exclamation fs-4 me-3 text-warning"></i>
                        <small class="text-dark">
                            <strong>Aturan Kenaikan Otomatis:</strong><br>
                            - Siswa <strong>Kelas 7</strong> otomatis naik ke <strong>Kelas 8</strong>.<br>
                            - Siswa <strong>Kelas 8</strong> otomatis naik ke <strong>Kelas 9</strong>.<br>
                            - Siswa <strong>Kelas 9</strong> otomatis dialihkan statusnya menjadi <strong>Alumni (Lulus)</strong>.
                        </small>
                    </div>

                    <h6 class="fw-bold text-danger mb-2"><i class="fa-solid fa-user-xmark me-1"></i> Pengecualian Siswa (Tinggal Kelas):</h6>
                    <p class="small text-muted mb-2">Centang nama siswa di bawah ini jika siswa tersebut <strong>TIDAK NAIK KELAS</strong> agar tetap dipertahankan pada kelas saat ini:</p>

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
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark shadow-sm">
                        <i class="fa-solid fa-check-double me-1"></i> Eksekusi Kenaikan Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
