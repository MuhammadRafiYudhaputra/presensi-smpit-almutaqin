@extends('layouts.app')

@section('content')
<style>
    .time-setting-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 2.25rem 1.75rem;
        transition: all 0.25s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        text-align: center;
    }
    .time-setting-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.06);
    }
    .time-setting-card.card-blue {
        border-top: 4px solid #2563eb;
    }
    .time-setting-card.card-yellow {
        border-top: 4px solid #f59e0b;
    }
    .time-setting-card.card-green {
        border-top: 4px solid #10b981;
    }
    .time-icon-circle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.25rem;
    }
    .time-input-styled {
        background-color: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        font-size: 1.6rem;
        font-weight: 800;
        text-align: center;
        padding: 0.75rem 1rem;
        color: #0f172a;
        letter-spacing: 2px;
        transition: all 0.2s ease;
        width: 100%;
        max-width: 220px;
        margin: 0 auto 1.25rem;
    }
    .time-input-styled:focus {
        background-color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        outline: none;
    }
</style>

<div class="card card-custom p-4 p-md-5 shadow-sm border-0 rounded-4">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4">
                <i class="fa-solid fa-clock fs-3"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-1 text-dark">Pengaturan Jam Operasional Presensi</h5>
                <p class="text-muted small mb-0">Atur jam batas masuk, batas toleransi terlambat, dan jam kepulangan resmi siswa</p>
            </div>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.85rem;">
            <i class="fa-solid fa-school me-1"></i> Jadwal Reguler Sekolah
        </span>
    </div>

    <form action="{{ route('admin.jampresensi.update') }}" method="POST">
        @csrf
        <input type="hidden" name="nama_jadwal" value="{{ $jamPresensi->nama_jadwal ?? 'Jadwal Reguler Sekolah' }}">

        <!-- 3 Time Cards Row -->
        <div class="row g-4 mb-4">
            <!-- Card 1: Jam Masuk -->
            <div class="col-lg-4 col-md-6">
                <div class="time-setting-card card-blue h-100 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Icon Circle -->
                        <div class="time-icon-circle bg-primary bg-opacity-10 text-primary">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        
                        <!-- Title & Subtitle -->
                        <h5 class="fw-bold text-dark mb-1">Jam Masuk</h5>
                        <p class="text-muted small mb-3">Waktu awal presensi pagi siswa</p>

                        <!-- Input Time Box -->
                        <div>
                            <input type="time" name="jam_masuk" class="form-control time-input-styled shadow-sm" value="{{ old('jam_masuk', substr($jamPresensi->jam_masuk ?? '07:00:00', 0, 5)) }}" required>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <p class="text-muted small mb-0 px-2" style="font-size: 0.82rem; line-height: 1.5;">
                        Waktu awal siswa dapat melakukan scan presensi pagi ketika tiba di sekolah.
                    </p>
                </div>
            </div>

            <!-- Card 2: Batas Terlambat -->
            <div class="col-lg-4 col-md-6">
                <div class="time-setting-card card-yellow h-100 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Icon Circle -->
                        <div class="time-icon-circle bg-warning bg-opacity-15 text-warning">
                            <i class="fa-solid fa-user-clock"></i>
                        </div>
                        
                        <!-- Title & Subtitle -->
                        <h5 class="fw-bold text-dark mb-1">Batas Terlambat</h5>
                        <p class="text-muted small mb-3">Toleransi batas keterlambatan</p>

                        <!-- Input Time Box -->
                        <div>
                            <input type="time" name="jam_terlambat" class="form-control time-input-styled shadow-sm" value="{{ old('jam_terlambat', substr($jamPresensi->jam_terlambat ?? '07:15:00', 0, 5)) }}" required>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <p class="text-muted small mb-0 px-2" style="font-size: 0.82rem; line-height: 1.5;">
                        Siswa yang scan setelah jam ini otomatis tercatat dengan status <strong class="text-danger">TERLAMBAT</strong>.
                    </p>
                </div>
            </div>

            <!-- Card 3: Jam Pulang -->
            <div class="col-lg-4 col-md-12">
                <div class="time-setting-card card-green h-100 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Icon Circle -->
                        <div class="time-icon-circle bg-success bg-opacity-10 text-success">
                            <i class="fa-solid fa-door-open"></i>
                        </div>
                        
                        <!-- Title & Subtitle -->
                        <h5 class="fw-bold text-dark mb-1">Jam Pulang</h5>
                        <p class="text-muted small mb-3">Waktu kepulangan resmi sekolah</p>

                        <!-- Input Time Box -->
                        <div>
                            <input type="time" name="jam_pulang" class="form-control time-input-styled shadow-sm" value="{{ old('jam_pulang', substr($jamPresensi->jam_pulang ?? '14:30:00', 0, 5)) }}" required>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <p class="text-muted small mb-0 px-2" style="font-size: 0.82rem; line-height: 1.5;">
                        Waktu resmi selesai KBM. Siswa dapat melakukan scan kepulangan sore hari.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="d-flex justify-content-end pt-3">
            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan Jam
            </button>
        </div>
    </form>
</div>
@endsection
