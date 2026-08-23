@extends('layouts.app')

@section('content')
<style>
    .time-card {
        border-radius: 20px;
        padding: 1.75rem 1.5rem;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .time-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06);
    }
    .time-card-blue {
        background: linear-gradient(180deg, #f0f7ff 0%, #ffffff 100%);
        border: 1.5px solid #bfdbfe;
    }
    .time-card-yellow {
        background: linear-gradient(180deg, #fefce8 0%, #ffffff 100%);
        border: 1.5px solid #fde047;
    }
    .time-card-teal {
        background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
        border: 1.5px solid #bbf7d0;
    }
    .time-icon-badge {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .time-input-box {
        background-color: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        font-size: 1.5rem;
        font-weight: 800;
        text-align: center;
        padding: 0.75rem 1rem;
        color: #0f172a;
        letter-spacing: 2px;
        transition: all 0.2s ease;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }
    .time-input-box:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        outline: none;
    }
</style>

<div class="card card-custom p-4 p-md-5 shadow-sm border-0 rounded-4">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4">
                <i class="fa-solid fa-clock-rotate-left fs-3 text-warning"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-1 text-dark">Pengaturan Jam Operasional Presensi</h5>
                <p class="text-muted small mb-0">Atur jam batas masuk, batas toleransi terlambat, dan jam kepulangan resmi siswa SMP IT Al-Muttaqin</p>
            </div>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.85rem;">
            <i class="fa-solid fa-school me-1"></i> Jadwal Reguler
        </span>
    </div>

    <form action="{{ route('admin.jampresensi.update') }}" method="POST">
        @csrf
        <input type="hidden" name="nama_jadwal" value="{{ $jamPresensi->nama_jadwal ?? 'Jadwal Reguler Sekolah' }}">

        <!-- 3 Time Cards Row -->
        <div class="row g-4 mb-4">
            <!-- Card 1: Jam Masuk -->
            <div class="col-lg-4 col-md-6">
                <div class="time-card time-card-blue h-100 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Header Card -->
                        <div class="d-flex align-items-center gap-2.5 mb-3">
                            <div class="time-icon-badge bg-primary bg-opacity-10 text-primary">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-primary mb-0">Jam Masuk</h6>
                                <small class="text-muted" style="font-size: 0.78rem;">Awal waktu presensi pagi</small>
                            </div>
                        </div>

                        <!-- Input Time Box -->
                        <div class="my-3">
                            <input type="time" name="jam_masuk" class="form-control time-input-box w-100" value="{{ old('jam_masuk', substr($jamPresensi->jam_masuk ?? '07:00:00', 0, 5)) }}" required>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="p-2.5 bg-white bg-opacity-80 rounded-3 border border-primary border-opacity-15 mt-2">
                        <small class="text-muted d-flex align-items-start gap-1.5" style="font-size: 0.8rem; line-height: 1.4;">
                            <i class="fa-solid fa-circle-info text-primary mt-1"></i>
                            <span>Waktu awal siswa diperbolehkan melakukan scan presensi pagi ketika tiba di sekolah.</span>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Card 2: Batas Terlambat -->
            <div class="col-lg-4 col-md-6">
                <div class="time-card time-card-yellow h-100 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Header Card -->
                        <div class="d-flex align-items-center gap-2.5 mb-3">
                            <div class="time-icon-badge bg-warning bg-opacity-20 text-warning text-dark">
                                <i class="fa-solid fa-user-clock text-warning"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Batas Terlambat</h6>
                                <small class="text-muted" style="font-size: 0.78rem;">Toleransi keterlambatan</small>
                            </div>
                        </div>

                        <!-- Input Time Box -->
                        <div class="my-3">
                            <input type="time" name="jam_terlambat" class="form-control time-input-box w-100" value="{{ old('jam_terlambat', substr($jamPresensi->jam_terlambat ?? '07:15:00', 0, 5)) }}" required>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="p-2.5 bg-white bg-opacity-80 rounded-3 border border-warning border-opacity-25 mt-2">
                        <small class="text-muted d-flex align-items-start gap-1.5" style="font-size: 0.8rem; line-height: 1.4;">
                            <i class="fa-solid fa-triangle-exclamation text-warning mt-1"></i>
                            <span>Siswa yang scan setelah jam ini otomatis tercatat dengan status <strong>TERLAMBAT</strong>.</span>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Card 3: Jam Pulang -->
            <div class="col-lg-4 col-md-12">
                <div class="time-card time-card-teal h-100 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Header Card -->
                        <div class="d-flex align-items-center gap-2.5 mb-3">
                            <div class="time-icon-badge bg-success bg-opacity-10 text-success">
                                <i class="fa-solid fa-door-open"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-success mb-0">Jam Pulang</h6>
                                <small class="text-muted" style="font-size: 0.78rem;">Waktu kepulangan resmi</small>
                            </div>
                        </div>

                        <!-- Input Time Box -->
                        <div class="my-3">
                            <input type="time" name="jam_pulang" class="form-control time-input-box w-100" value="{{ old('jam_pulang', substr($jamPresensi->jam_pulang ?? '14:30:00', 0, 5)) }}" required>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="p-2.5 bg-white bg-opacity-80 rounded-3 border border-success border-opacity-15 mt-2">
                        <small class="text-muted d-flex align-items-start gap-1.5" style="font-size: 0.8rem; line-height: 1.4;">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <span>Waktu resmi selesai KBM. Siswa dapat melakukan scan kepulangan sore hari.</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Alur Presensi Otomatis -->
        <div class="alert bg-light border rounded-3 p-3 mb-4 d-flex align-items-start gap-3">
            <i class="fa-solid fa-circle-info text-primary fs-5 mt-0.5"></i>
            <div class="small text-muted">
                <strong class="text-dark d-block mb-1">Informasi Sinkronisasi Otomatis:</strong>
                Sistem kios presensi QR Code akan langsung menggunakan jam yang Anda tentukan di atas secara *real-time*. Pesan notifikasi WhatsApp otomatis ke orang tua juga akan menyertakan status tepat waktu atau terlambat sesuai konfigurasi ini.
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="d-flex justify-content-end pt-2">
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan Jam
            </button>
        </div>
    </form>
</div>
@endsection
