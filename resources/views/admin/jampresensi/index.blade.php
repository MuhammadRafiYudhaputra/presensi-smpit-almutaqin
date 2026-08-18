@extends('layouts.app')

@section('content')
<style>
    .time-card-blue {
        background-color: #eff6ff;
        border: 1.5px solid #bfdbfe;
        border-radius: 18px;
        padding: 1.5rem;
    }
    .time-card-yellow {
        background-color: #fefce8;
        border: 1.5px solid #fef08a;
        border-radius: 18px;
        padding: 1.5rem;
    }
    .time-card-teal {
        background-color: #f0fdfa;
        border: 1.5px solid #ccfbf1;
        border-radius: 18px;
        padding: 1.5rem;
    }
    .time-input-box {
        background-color: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1.25rem;
        font-weight: 800;
        text-align: center;
        padding: 0.6rem 1rem;
        color: #0f172a;
    }
</style>

<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header -->
    <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
        <i class="fa-solid fa-clock text-warning fs-3 me-3"></i>
        <div>
            <h5 class="fw-bold mb-1 text-primary">Pengaturan Jam Presensi & Keterlambatan</h5>
            <p class="text-muted small mb-0">Atur jam presensi masuk, batas toleransi terlambat, dan jam pulang sesuai jadwal operasional sekolah</p>
        </div>
    </div>

    <form action="{{ route('admin.jampresensi.update') }}" method="POST">
        @csrf
        <input type="hidden" name="nama_jadwal" value="{{ $jamPresensi->nama_jadwal ?? 'Jadwal Reguler Sekolah' }}">

        <!-- 3 Time Cards Row -->
        <div class="row g-4 mb-4">
            <!-- Card 1: Jam Masuk -->
            <div class="col-md-4">
                <div class="time-card-blue h-100 d-flex flex-direction-column justify-content-between">
                    <div>
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fa-solid fa-bell me-1"></i> Jam Masuk
                        </h6>
                        <div class="mb-3">
                            <input type="time" name="jam_masuk" class="form-control time-input-box shadow-sm" value="{{ old('jam_masuk', substr($jamPresensi->jam_masuk ?? '07:00:00', 0, 5)) }}" required>
                        </div>
                    </div>
                    <small class="text-muted d-block" style="font-size: 0.82rem;">
                        Jam awal mulainya presensi pagi siswa tiba di sekolah.
                    </small>
                </div>
            </div>

            <!-- Card 2: Batas Terlambat -->
            <div class="col-md-4">
                <div class="time-card-yellow h-100 d-flex flex-direction-column justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="fa-solid fa-user-clock text-warning me-1"></i> Batas Terlambat
                        </h6>
                        <div class="mb-3">
                            <input type="time" name="jam_terlambat" class="form-control time-input-box shadow-sm" value="{{ old('jam_terlambat', substr($jamPresensi->jam_terlambat ?? '07:10:00', 0, 5)) }}" required>
                        </div>
                    </div>
                    <small class="text-muted d-block" style="font-size: 0.82rem;">
                        Siswa yang scan setelah jam ini otomatis tercatat <strong>TERLAMBAT</strong>.
                    </small>
                </div>
            </div>

            <!-- Card 3: Jam Pulang -->
            <div class="col-md-4">
                <div class="time-card-teal h-100 d-flex flex-direction-column justify-content-between">
                    <div>
                        <h6 class="fw-bold text-success mb-3">
                            <i class="fa-solid fa-door-open me-1"></i> Jam Pulang
                        </h6>
                        <div class="mb-3">
                            <input type="time" name="jam_pulang" class="form-control time-input-box shadow-sm" value="{{ old('jam_pulang', substr($jamPresensi->jam_pulang ?? '14:30:00', 0, 5)) }}" required>
                        </div>
                    </div>
                    <small class="text-muted d-block" style="font-size: 0.82rem;">
                        Waktu resmi kepulangan sekolah untuk scan pulang sore.
                    </small>
                </div>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Pengaturan Jam
            </button>
        </div>
    </form>
</div>
@endsection
