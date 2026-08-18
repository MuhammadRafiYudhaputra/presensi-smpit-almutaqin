@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-md-7">
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-1"><i class="fa-brands fa-whatsapp text-success me-2"></i>Pengaturan API Fonnte & Template Pesan WA</h5>
            <p class="text-muted small">Konfigurasikan token Fonnte dan format pesan notifikasi otomatis untuk orang tua.</p>

            <form action="{{ route('admin.fonnte.update') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Fonnte API Token</label>
                    <input type="password" name="api_token" class="form-control" value="{{ $setting->api_token }}" required placeholder="Token Fonnte dari fonnte.com">
                    <small class="text-muted">Dapatkan API Token dari dashboard akun Anda di <a href="https://fonnte.com" target="_blank">Fonnte.com</a></small>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ $setting->is_active ? 'checked' : '' }}>
                    <label class="form-check-input-label fw-semibold" for="is_active">Aktifkan Notifikasi WhatsApp Otomatis</label>
                </div>

                <hr class="my-3">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Template Pesan Presensi MASUK (Tepat Waktu)</label>
                    <textarea name="template_masuk" class="form-control" rows="3" required>{{ $setting->template_masuk }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Template Pesan Presensi TERLAMBAT</label>
                    <textarea name="template_terlambat" class="form-control" rows="3" required>{{ $setting->template_terlambat }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Template Pesan Presensi PULANG</label>
                    <textarea name="template_pulang" class="form-control" rows="3" required>{{ $setting->template_pulang }}</textarea>
                </div>

                <div class="alert alert-info rounded-3 small">
                    <strong><i class="fa-solid fa-code me-1"></i> Variabel Dinamis Yang Tersedia:</strong><br>
                    <code>{nama}</code> : Nama Siswa | <code>{nisn}</code> : NISN Siswa | <code>{kelas}</code> : Nama Kelas<br>
                    <code>{tanggal}</code> : Tanggal Presensi | <code>{waktu}</code> : Jam Scan | <code>{status}</code> : HADIR / TERLAMBAT
                </div>

                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pengaturan
                </button>
            </form>
        </div>
    </div>

    <!-- Test Send Form -->
    <div class="col-md-5">
        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-2"><i class="fa-solid fa-paper-plane text-primary me-2"></i>Uji Coba Pengiriman Pesan WA</h6>
            <p class="text-muted small">Kirim pesan tes ke nomor WhatsApp untuk memastikan koneksi Fonnte berfungsi dengan baik.</p>

            <form action="{{ route('admin.fonnte.test') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor WhatsApp Tujuan</label>
                    <input type="text" name="target_no_wa" class="form-control" placeholder="08123456789" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Isi Pesan Tes</label>
                    <textarea name="message" class="form-control" rows="3" required>Assalamu'alaikum. Ini adalah pesan uji coba sistem presensi SMP IT Al-Mutaqin via Fonnte API.</textarea>
                </div>

                <button type="submit" class="btn btn-success rounded-pill w-100">
                    <i class="fa-brands fa-whatsapp me-1"></i> Kirim Pesan Uji Coba
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
