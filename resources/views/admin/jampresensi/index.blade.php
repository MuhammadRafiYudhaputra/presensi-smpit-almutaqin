@extends('layouts.app')

@section('content')
<style>
    .time-setting-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem 1.25rem;
        transition: all 0.25s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        text-align: center;
    }
    .time-setting-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.06);
    }
    .time-setting-card.card-blue {
        border-top: 3.5px solid #2563eb;
    }
    .time-setting-card.card-yellow {
        border-top: 3.5px solid #f59e0b;
    }
    .time-setting-card.card-green {
        border-top: 3.5px solid #10b981;
    }
    .time-icon-circle {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 0.85rem;
    }
    .time-input-styled {
        background-color: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        font-size: 1.35rem;
        font-weight: 800;
        text-align: center;
        padding: 0.45rem 0.75rem;
        color: #0f172a;
        letter-spacing: 1.5px;
        transition: all 0.2s ease;
        width: 100%;
        max-width: 175px;
        margin: 0 auto 0.85rem;
    }
    .time-input-styled:focus {
        background-color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.12);
        outline: none;
    }
</style>

<div class="card card-custom p-4 p-md-4 shadow-sm border-0 rounded-4">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <div class="p-2.5 bg-primary bg-opacity-10 text-primary rounded-3">
                <i class="fa-solid fa-clock fs-4"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-1 text-dark">Pengaturan Jam Operasional Presensi</h5>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.jampresensi.update') }}" method="POST">
        @csrf
        <input type="hidden" name="nama_jadwal" value="{{ $jamPresensi->nama_jadwal ?? 'Jadwal Reguler Sekolah' }}">

        <!-- 3 Time Cards Row (Lebih Ringkas & Rapi) -->
        <div class="row g-3 mb-4">
            <!-- Card 1: Jam Masuk -->
            <div class="col-lg-4 col-md-6">
                <div class="time-setting-card card-blue h-100 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Icon Circle -->
                        <div class="time-icon-circle" style="background-color: #eff6ff;">
                            <i class="fa-solid fa-bell text-primary"></i>
                        </div>
                        
                        <!-- Title & Subtitle -->
                        <h6 class="fw-bold text-dark mb-0.5">Jam Masuk</h6>
                        <p class="text-muted small mb-2.5" style="font-size: 0.78rem;">Awal waktu presensi pagi siswa</p>

                        <!-- Input Time Box -->
                        <div>
                            <input type="time" name="jam_masuk" class="form-control time-input-styled shadow-sm" value="{{ old('jam_masuk', substr($jamPresensi->jam_masuk ?? '07:00:00', 0, 5)) }}" required>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <p class="text-muted small mb-0 px-1" style="font-size: 0.76rem; line-height: 1.4;">
                        Waktu awal siswa dapat melakukan scan presensi pagi ketika tiba di sekolah.
                    </p>
                </div>
            </div>

            <!-- Card 2: Batas Terlambat -->
            <div class="col-lg-4 col-md-6">
                <div class="time-setting-card card-yellow h-100 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Icon Circle -->
                        <div class="time-icon-circle" style="background-color: #fef3c7;">
                            <i class="fa-solid fa-user-clock" style="color: #b45309;"></i>
                        </div>
                        
                        <!-- Title & Subtitle -->
                        <h6 class="fw-bold text-dark mb-0.5">Batas Terlambat</h6>
                        <p class="text-muted small mb-2.5" style="font-size: 0.78rem;">Toleransi batas keterlambatan</p>

                        <!-- Input Time Box -->
                        <div>
                            <input type="time" name="jam_terlambat" class="form-control time-input-styled shadow-sm" value="{{ old('jam_terlambat', substr($jamPresensi->jam_terlambat ?? '07:15:00', 0, 5)) }}" required>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <p class="text-muted small mb-0 px-1" style="font-size: 0.76rem; line-height: 1.4;">
                        Siswa yang scan setelah jam ini otomatis tercatat status <strong class="text-danger">TERLAMBAT</strong>.
                    </p>
                </div>
            </div>

            <!-- Card 3: Jam Pulang -->
            <div class="col-lg-4 col-md-12">
                <div class="time-setting-card card-green h-100 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Icon Circle -->
                        <div class="time-icon-circle" style="background-color: #ecfdf5;">
                            <i class="fa-solid fa-door-open text-success"></i>
                        </div>
                        
                        <!-- Title & Subtitle -->
                        <h6 class="fw-bold text-dark mb-0.5">Jam Pulang</h6>
                        <p class="text-muted small mb-2.5" style="font-size: 0.78rem;">Waktu kepulangan resmi sekolah</p>

                        <!-- Input Time Box -->
                        <div>
                            <input type="time" name="jam_pulang" class="form-control time-input-styled shadow-sm" value="{{ old('jam_pulang', substr($jamPresensi->jam_pulang ?? '14:30:00', 0, 5)) }}" required>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <p class="text-muted small mb-0 px-1" style="font-size: 0.76rem; line-height: 1.4;">
                        Waktu Siswa dapat melakukan scan kepulangan sore hari.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="d-flex justify-content-end pt-2">
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan Jam
            </button>
        </div>
    </form>
</div>
@endsection
