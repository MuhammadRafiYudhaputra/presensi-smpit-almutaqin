@extends('layouts.app')

@section('content')
<style>
    .template-textarea {
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.8rem;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        color: #1e293b;
        line-height: 1.45;
        resize: vertical;
    }
    .template-textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
    }
</style>

<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center">
                <i class="fa-brands fa-whatsapp text-success fs-4 me-2"></i> Pengaturan WhatsApp Gateway & Template
            </h5>
            <small class="text-muted" style="font-size: 0.8rem;">Konfigurasi Fonnte API Token dan kustomisasi format pesan notifikasi presensi otomatis</small>
        </div>
        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1.5 rounded-pill fw-semibold" style="font-size: 0.8rem;">
            <i class="fa-solid fa-bolt me-1"></i> Fonnte API
        </span>
    </div>

    <form action="{{ route('admin.fonnte.update') }}" method="POST">
        @csrf
        
        <!-- Fonnte API Token (Compact Form) -->
        <div class="mb-4 p-3 bg-light rounded-3 border">
            <div class="row align-items-center g-2">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark mb-0 small d-flex align-items-center">
                        <i class="fa-solid fa-key text-warning me-1.5"></i> Fonnte API Token:
                    </label>
                </div>
                <div class="col-md-9">
                    <div class="input-group input-group-sm shadow-sm" style="max-width: 500px;">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-shield-halved"></i></span>
                        <input type="password" name="api_token" class="form-control border-start-0" value="{{ old('api_token', $setting->api_token ?? '') }}" placeholder="Masukkan API Token Fonnte akun Anda..." required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Title: Kustomisasi Template -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-primary mb-0 d-flex align-items-center small">
                <i class="fa-solid fa-comments me-1.5"></i> Kustomisasi Format Pesan Notifikasi WhatsApp
            </h6>
            <small class="text-muted" style="font-size: 0.75rem;">
                Variabel tersedia: <code>{nama}</code>, <code>{nisn}</code>, <code>{kelas}</code>, <code>{tanggal}</code>, <code>{waktu}</code>, <code>{status}</code>
            </small>
        </div>

        <!-- 3 Template Columns Side-by-Side (Compact & Tidy) -->
        <div class="row g-3 mb-4">
            <!-- Template 1: Masuk (Tepat Waktu) -->
            <div class="col-lg-4">
                <div class="card h-100 border rounded-3 p-3 bg-light bg-opacity-30 shadow-none">
                    <label class="form-label fw-bold text-dark d-flex align-items-center mb-2 small">
                        <span class="badge bg-success me-1.5 px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">1</span>
                        Presensi MASUK
                    </label>
                    <textarea name="template_masuk" class="form-control form-control-sm template-textarea shadow-sm mb-2" rows="6" required>{{ old('template_masuk', $setting->template_masuk ?? "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi MASUK SMP IT Al-Muttaqin:\nNama: {nama}\nNISN: {nisn}\nKelas: {kelas}\nStatus: {status}\n\nTelah melakukan presensi MASUK pada tanggal {tanggal} pukul {waktu}.\n\nTerima kasih.") }}</textarea>
                    <small class="text-muted d-block" style="font-size: 0.72rem;">Dikirim saat siswa melakukan scan masuk sekolah.</small>
                </div>
            </div>

            <!-- Template 2: Terlambat -->
            <div class="col-lg-4">
                <div class="card h-100 border rounded-3 p-3 bg-light bg-opacity-30 shadow-none">
                    <label class="form-label fw-bold text-dark d-flex align-items-center mb-2 small">
                        <span class="badge bg-warning text-dark me-1.5 px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">2</span>
                        Presensi TERLAMBAT
                    </label>
                    <textarea name="template_terlambat" class="form-control form-control-sm template-textarea shadow-sm mb-2" rows="6" required>{{ old('template_terlambat', $setting->template_terlambat ?? "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi TERLAMBAT SMP IT Al-Muttaqin:\nNama: {nama}\nNISN: {nisn}\nKelas: {kelas}\nStatus: {status}\n\nTelah melakukan presensi MASUK TERLAMBAT pada tanggal {tanggal} pukul {waktu}. Mohon perhatian dari Bapak/Ibu Wali Murid.\n\nTerima kasih.") }}</textarea>
                    <small class="text-muted d-block" style="font-size: 0.72rem;">Dikirim saat siswa scan masuk melebihi batas jam masuk.</small>
                </div>
            </div>

            <!-- Template 3: Pulang -->
            <div class="col-lg-4">
                <div class="card h-100 border rounded-3 p-3 bg-light bg-opacity-30 shadow-none">
                    <label class="form-label fw-bold text-dark d-flex align-items-center mb-2 small">
                        <span class="badge bg-primary me-1.5 px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">3</span>
                        Presensi PULANG
                    </label>
                    <textarea name="template_pulang" class="form-control form-control-sm template-textarea shadow-sm mb-2" rows="6" required>{{ old('template_pulang', $setting->template_pulang ?? "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi PULANG SMP IT Al-Muttaqin:\nNama: {nama}\nNISN: {nisn}\nKelas: {kelas}\n\nTelah menyelesaikan kegiatan belajar dan melakukan presensi PULANG pada tanggal {tanggal} pukul {waktu}.\n\nTerima kasih.") }}</textarea>
                    <small class="text-muted d-block" style="font-size: 0.72rem;">Dikirim saat siswa melakukan scan kepulangan sekolah.</small>
                </div>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="d-flex justify-content-end pt-2 border-top">
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-1.5 btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-1">
                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pengaturan Fonnte
            </button>
        </div>
    </form>
</div>
@endsection
