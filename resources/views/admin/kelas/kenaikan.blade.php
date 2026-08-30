@extends('layouts.app')

@section('content')
<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center">
                <i class="fa-solid fa-graduation-cap text-success me-2 fs-3"></i> Kenaikan Kelas (Tahun Ajaran Baru)
            </h5>
            <small class="text-muted">Proses kenaikan jenjang otomatis untuk seluruh siswa dan pengalihan lulusan Kelas 9 ke Alumni</small>
        </div>
        <a href="{{ route('admin.kelas.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Data Kelas
        </a>
    </div>

    <!-- 1. Alur Visual Kenaikan Kelas -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="p-3 rounded-4 bg-primary bg-opacity-10 border border-primary text-center">
                <span class="badge bg-primary px-3 py-2 rounded-pill mb-2 fs-6">Kelas 7 &rarr; Naik ke Kelas 8</span>
                <h4 class="fw-bold text-primary mb-1">{{ $siswaKelas7->count() }} Siswa</h4>
                <small class="text-muted">Akan dinaikkan ke jenjang Kelas 8 secara otomatis</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-3 rounded-4 bg-warning bg-opacity-10 border border-warning text-center">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill mb-2 fs-6">Kelas 8 &rarr; Naik ke Kelas 9</span>
                <h4 class="fw-bold text-dark mb-1">{{ $siswaKelas8->count() }} Siswa</h4>
                <small class="text-muted">Akan dinaikkan ke jenjang Kelas 9 secara otomatis</small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-3 rounded-4 bg-success bg-opacity-10 border border-success text-center">
                <span class="badge bg-success px-3 py-2 rounded-pill mb-2 fs-6">Kelas 9 &rarr; Lulus ke Alumni</span>
                <h4 class="fw-bold text-success mb-1">{{ $siswaKelas9->count() }} Siswa</h4>
                <small class="text-muted">Akan dialihkan statusnya menjadi Alumni / Lulus</small>
            </div>
        </div>
    </div>

    <!-- Form Eksekusi Kenaikan Otomatis -->
    <form action="{{ route('admin.kenaikan.proses') }}" method="POST" onsubmit="return confirm('PERHATIAN: Apakah Anda yakin ingin mengeksekusi kenaikan kelas seluruh siswa aktif sekarang? Tindakan ini akan memindahkan siswa ke jenjang berikutnya.')">
        @csrf
        
        <div class="card p-4 border rounded-4 bg-light mb-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold text-danger mb-0"><i class="fa-solid fa-user-xmark me-1"></i> Pengecualian Siswa (Tinggal Kelas):</h6>
                    <small class="text-muted">Centang siswa di bawah jika <strong>TIDAK NAIK KELAS</strong> (akan tetap di kelas saat ini):</small>
                </div>
                <div style="min-width: 250px;">
                    <input type="text" id="searchKenaikan" class="form-control form-control-sm shadow-sm" placeholder="Cari nama siswa atau NISN..." onkeyup="filterKenaikanList()">
                </div>
            </div>

            <!-- List Siswa Checkboxes -->
            <div class="bg-white border rounded-3 p-3" style="max-height: 320px; overflow-y: auto;" id="listKenaikanContainer">
                <div class="row g-2">
                    @forelse($siswaKelas7->merge($siswaKelas8)->merge($siswaKelas9) as $s)
                    <div class="col-md-6 item-siswa-kenaikan" data-nama="{{ strtolower($s->nama) }} {{ strtolower($s->nisn) }}">
                        <div class="form-check p-2 border rounded-3 hover-bg">
                            <input class="form-check-input ms-1" type="checkbox" name="except_siswa_ids[]" value="{{ $s->id }}" id="exc_{{ $s->id }}">
                            <label class="form-check-label ps-2 w-100 d-flex justify-content-between align-items-center cursor-pointer" for="exc_{{ $s->id }}">
                                <div>
                                    <strong class="text-dark d-block">{{ $s->nama }}</strong>
                                    <small class="text-muted">NISN: {{ $s->nisn }}</small>
                                </div>
                                <span class="badge bg-secondary">Kelas {{ $s->kelas->nama_kelas ?? '-' }}</span>
                            </label>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted py-4">
                        Tidak ada siswa aktif terdaftar.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm">
                <i class="fa-solid fa-check-double me-2"></i> Proses Kenaikan Kelas Tahun Ajaran Baru
            </button>
        </div>
    </form>
</div>

<script>
function filterKenaikanList() {
    const input = document.getElementById('searchKenaikan').value.toLowerCase();
    const items = document.querySelectorAll('.item-siswa-kenaikan');
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
