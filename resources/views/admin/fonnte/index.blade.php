@extends('layouts.app')

@section('content')
<style>
    .template-textarea {
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.88rem;
        background-color: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        color: #1e293b;
        line-height: 1.5;
    }
    .toggle-card {
        background-color: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 0.85rem 1.25rem;
    }
</style>

<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-2">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center">
            <i class="fa-brands fa-whatsapp text-success fs-3 me-2"></i> Pengaturan WhatsApp Gateway & Template
        </h5>
        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill fw-semibold">
            <i class="fa-solid fa-bolt me-1"></i> Fonnte API
        </span>
    </div>

    <form action="{{ route('admin.fonnte.update') }}" method="POST">
        @csrf
        
        <!-- Fonnte API Token -->
        <div class="mb-4">
            <label class="form-label fw-bold text-dark mb-1">Fonnte API Token</label>
            <div class="input-group shadow-sm" style="max-width: 600px;">
                <span class="input-group-text bg-white border-end-0 text-warning"><i class="fa-solid fa-key"></i></span>
                <input type="password" name="api_token" class="form-control border-start-0 fw-bold" value="{{ old('api_token', $setting->api_token ?? '') }}" placeholder="Masukkan API Token Fonnte..." required>
            </div>
            <small class="text-muted d-block mt-1">Dapatkan API Token dari dashboard akun Fonnte Anda di <a href="https://fonnte.com" target="_blank" class="text-primary fw-semibold">Fonnte.com</a></small>
        </div>

        <!-- Toggle Switch Card -->
        <div class="toggle-card d-flex align-items-center mb-4" style="max-width: 600px;">
            <div class="form-check form-switch mb-0 d-flex align-items-center gap-3">
                <input class="form-check-input fs-5 cursor-pointer" type="checkbox" name="is_active" id="is_active" {{ ($setting->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold text-dark cursor-pointer" for="is_active">
                    Aktifkan Pengiriman Notifikasi WhatsApp Otomatis ke Orang Tua
                </label>
            </div>
        </div>

        <!-- Section Title: Kustomisasi Template -->
        <h6 class="fw-bold text-primary mb-3 d-flex align-items-center">
            <i class="fa-solid fa-comments me-2"></i> Kustomisasi Template Pesan WhatsApp
        </h6>

        <!-- Template 1: Masuk (Tepat Waktu) -->
        <div class="mb-4">
            <label class="form-label fw-bold text-dark d-flex align-items-center mb-2">
                <span class="badge bg-success me-2 px-2 py-1">1</span> Template Pesan Presensi MASUK (Tepat Waktu)
            </label>
            <textarea name="template_masuk" class="form-control template-textarea shadow-sm mb-1" rows="6" required>{{ old('template_masuk', $setting->template_masuk ?? "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi MASUK SMP IT Al-Muttaqin:\nNama: {nama}\nNISN: {nisn}\nKelas: {kelas}\nStatus: {status}\n\nTelah melakukan presensi MASUK pada tanggal {tanggal} pukul {waktu}.\n\nTerima kasih.") }}</textarea>
            <small class="text-muted d-block" style="font-size: 0.82rem;">Dikirim saat siswa melakukan scan pertama dan tiba sebelum batas keterlambatan.</small>
        </div>

        <!-- Template 2: Terlambat -->
        <div class="mb-4">
            <label class="form-label fw-bold text-dark d-flex align-items-center mb-2">
                <span class="badge bg-warning text-dark me-2 px-2 py-1">2</span> Template Pesan Presensi TERLAMBAT
            </label>
            <textarea name="template_terlambat" class="form-control template-textarea shadow-sm mb-1" rows="6" required>{{ old('template_terlambat', $setting->template_terlambat ?? "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi TERLAMBAT SMP IT Al-Muttaqin:\nNama: {nama}\nNISN: {nisn}\nKelas: {kelas}\nStatus: {status}\n\nTelah melakukan presensi MASUK TERLAMBAT pada tanggal {tanggal} pukul {waktu}. Mohon perhatian dari Bapak/Ibu Wali Murid.\n\nTerima kasih.") }}</textarea>
            <small class="text-muted d-block" style="font-size: 0.82rem;">Dikirim saat siswa melakukan scan masuk melebihi batas jam keterlambatan.</small>
        </div>

        <!-- Template 3: Pulang -->
        <div class="mb-4">
            <label class="form-label fw-bold text-dark d-flex align-items-center mb-2">
                <span class="badge bg-info text-dark me-2 px-2 py-1">3</span> Template Pesan Presensi PULANG (Kepulangan Siswa)
            </label>
            <textarea name="template_pulang" class="form-control template-textarea shadow-sm mb-1" rows="6" required>{{ old('template_pulang', $setting->template_pulang ?? "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi PULANG SMP IT Al-Muttaqin:\nNama: {nama}\nNISN: {nisn}\nKelas: {kelas}\n\nTelah menyelesaikan kegiatan belajar dan melakukan presensi PULANG pada tanggal {tanggal} pukul {waktu}.\n\nTerima kasih.") }}</textarea>
            <small class="text-muted d-block" style="font-size: 0.82rem;">Dikirim saat siswa melakukan scan kepulangan sore hari.</small>
        </div>

        <!-- Guide Variabel -->
        <div class="alert alert-info border-info small mb-4">
            <i class="fa-solid fa-code me-1"></i> <strong>Variabel Dinamis Yang Tersedia:</strong><br>
            <code>{nama}</code> : Nama Lengkap Siswa | <code>{nisn}</code> : NISN Siswa | <code>{kelas}</code> : Nama Kelas<br>
            <code>{tanggal}</code> : Tanggal Presensi | <code>{waktu}</code> : Jam Scan | <code>{status}</code> : Status Kehadiran
        </div>

        <!-- Tombol Simpan -->
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Pengaturan Fonnte
            </button>
        </div>
    </form>
</div>
@endsection
