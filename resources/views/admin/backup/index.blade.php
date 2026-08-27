@extends('layouts.app')

@section('content')
<style>
    .backup-panel-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .backup-header-purple {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        color: #ffffff;
        padding: 0.75rem 1.15rem;
    }

    .backup-header-teal {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        padding: 0.75rem 1.15rem;
    }

    .backup-body {
        padding: 1.15rem 1.25rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .backup-section-title {
        font-weight: 800;
        font-size: 0.88rem;
        color: #1e293b;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .backup-section-desc {
        font-size: 0.78rem;
        color: #64748b;
        margin-bottom: 0.75rem;
        line-height: 1.4;
    }

    .backup-divider {
        border-top: 1px dashed #e2e8f0;
        margin: 1rem 0;
    }

    .warning-alert-box {
        background-color: #fffbeb;
        border: 1px solid #fef3c7;
        border-left: 3px solid #f59e0b;
        border-radius: 6px;
        padding: 0.45rem 0.65rem;
        font-size: 0.75rem;
        color: #92400e;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 6px;
        line-height: 1.35;
    }
</style>

<!-- Compact Top Title Card -->
<div class="card card-custom p-3 shadow-sm border-0 rounded-3 mb-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h6 class="fw-bold mb-0.5 text-dark d-flex align-items-center" style="font-size: 0.95rem;">
                <i class="fa-solid fa-cloud-arrow-down text-primary me-2 fs-5"></i> Backup &amp; Restore Data Sistem
            </h6>
            <small class="text-muted" style="font-size: 0.78rem;">Kelola pencadangan salinan data database &amp; penyimpanan berkas QR Code untuk keamanan data sekolah.</small>
        </div>
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                <i class="fa-solid fa-shield-halved me-1"></i> Data Security Center
            </span>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3 p-2.5 d-flex align-items-center gap-2" style="font-size: 0.82rem;">
        <i class="fa-solid fa-circle-check fs-6 text-success"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3 p-2.5 d-flex align-items-center gap-2" style="font-size: 0.82rem;">
        <i class="fa-solid fa-triangle-exclamation fs-6 text-danger"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3 p-2.5" style="font-size: 0.82rem;">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3 mb-3">
    <!-- 1. Card Database Backup & Restore -->
    <div class="col-12 col-lg-6">
        <div class="backup-panel-card">
            <div class="backup-header-purple">
                <div class="d-flex align-items-center gap-2 mb-0.5">
                    <i class="fa-solid fa-database fs-5"></i>
                    <h6 class="fw-bold mb-0 text-white" style="font-size: 0.95rem;">Database</h6>
                </div>
                <small class="text-white-50" style="font-size: 0.73rem;">Backup &amp; Restore Database Sistem (Format SQL)</small>
            </div>

            <div class="backup-body">
                <!-- Section 1: Backup Database -->
                <div>
                    <div class="backup-section-title mb-1.5">
                        <i class="fa-solid fa-download text-primary me-1.5"></i> Backup Database
                    </div>
                    <div class="backup-section-desc">
                        Unduh seluruh file cadangan <code>.sql</code> berisi seluruh tabel sistem.
                        <div class="mt-2 small text-dark d-flex gap-3" style="font-size: 0.75rem;">
                            <span><i class="fa-solid fa-server text-muted me-1"></i> Driver: <strong>{{ strtoupper($driver) }}</strong></span>
                            <span><i class="fa-solid fa-hard-drive text-muted me-1"></i> Ukuran: <strong>{{ $dbSize }}</strong></span>
                        </div>
                    </div>
                    <a href="{{ route('admin.backup.database') }}" class="btn btn-sm btn-primary rounded-pill px-3.5 py-2 fw-bold shadow-sm d-inline-flex align-items-center text-uppercase" style="font-size: 0.78rem;">
                        <i class="fa-solid fa-cloud-arrow-down fs-6 me-2"></i>
                        <span>Download Backup Database</span>
                    </a>
                </div>

                <div class="backup-divider"></div>

                <!-- Section 2: Restore Database -->
                <div>
                    <div class="backup-section-title mb-1.5">
                        <i class="fa-solid fa-upload text-warning me-1.5"></i> Restore Database
                    </div>
                    
                    <div class="warning-alert-box">
                        <i class="fa-solid fa-triangle-exclamation flex-shrink-0 text-warning fs-6 me-1.5"></i>
                        <div>
                            <strong>Peringatan:</strong> Tindakan ini akan menimpa seluruh database saat ini dengan berkas yang diunggah.
                        </div>
                    </div>

                    <form action="{{ route('admin.backup.database.restore') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Apakah Anda yakin ingin melakukan restore database? Seluruh data saat ini akan digantikan oleh isi file backup.');">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1.5" style="font-size: 0.78rem;">Pilih Berkas Cadangan (.SQL):</label>
                            <input type="file" name="sql_file" class="form-control form-control-sm rounded-2 shadow-none" style="font-size: 0.78rem;" accept=".sql, .txt" required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-warning rounded-pill px-3.5 py-2 fw-bold text-dark shadow-sm d-inline-flex align-items-center text-uppercase" style="font-size: 0.78rem;">
                            <i class="fa-solid fa-rotate-left fs-6 me-2"></i>
                            <span>Restore Database</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Card Foto & QR Code Backup & Restore -->
    <div class="col-12 col-lg-6">
        <div class="backup-panel-card">
            <div class="backup-header-teal">
                <div class="d-flex align-items-center gap-2 mb-0.5">
                    <i class="fa-solid fa-qrcode fs-5"></i>
                    <h6 class="fw-bold mb-0 text-white" style="font-size: 0.95rem;">Foto &amp; QR Code</h6>
                </div>
                <small class="text-white-50" style="font-size: 0.73rem;">Backup &amp; Restore Folder Uploads &amp; QR Siswa (Format ZIP)</small>
            </div>

            <div class="backup-body">
                <!-- Section 1: Backup Foto -->
                <div>
                    <div class="backup-section-title mb-1.5">
                        <i class="fa-solid fa-download text-info me-1.5"></i> Backup Foto (QR Code)
                    </div>
                    <div class="backup-section-desc">
                        Unduh seluruh file berkas gambar QR Code kartu siswa dalam bentuk arsip <code>.zip</code>.
                        <div class="mt-2 small text-dark d-flex gap-3" style="font-size: 0.75rem;">
                            <span><i class="fa-solid fa-image text-muted me-1"></i> Total Gambar: <strong>{{ $totalQrFiles }} Berkas</strong></span>
                            <span><i class="fa-solid fa-hard-drive text-muted me-1"></i> Ukuran: <strong>{{ $qrFolderSize }}</strong></span>
                        </div>
                    </div>
                    <a href="{{ route('admin.backup.storage') }}" class="btn btn-sm btn-info text-white rounded-pill px-3.5 py-2 fw-bold shadow-sm d-inline-flex align-items-center text-uppercase" style="font-size: 0.78rem;">
                        <i class="fa-solid fa-cloud-arrow-down fs-6 me-2"></i>
                        <span>Download Backup Foto (ZIP)</span>
                    </a>
                </div>

                <div class="backup-divider"></div>

                <!-- Section 2: Restore Foto -->
                <div>
                    <div class="backup-section-title mb-1.5">
                        <i class="fa-solid fa-upload text-warning me-1.5"></i> Restore Foto / Asset
                    </div>
                    
                    <div class="warning-alert-box">
                        <i class="fa-solid fa-circle-info flex-shrink-0 text-warning fs-6 me-1.5"></i>
                        <div>
                            <strong>Peringatan:</strong> Berkas foto/QR code yang ada akan diperbarui jika memiliki nama berkas yang sama.
                        </div>
                    </div>

                    <form action="{{ route('admin.backup.storage.restore') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Apakah Anda yakin ingin melakukan restore foto & QR Code ke folder sistem?');">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1.5" style="font-size: 0.78rem;">Pilih Berkas Arsip (.ZIP):</label>
                            <input type="file" name="zip_file" class="form-control form-control-sm rounded-2 shadow-none" style="font-size: 0.78rem;" accept=".zip" required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-warning rounded-pill px-3.5 py-2 fw-bold text-dark shadow-sm d-inline-flex align-items-center text-uppercase" style="font-size: 0.78rem;">
                            <i class="fa-solid fa-rotate-left fs-6 me-2"></i>
                            <span>Restore Foto / Asset</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bar Sinkronisasi Data Dummy Sample 112 Siswa -->
<div class="card card-custom p-3.5 shadow-sm border-0 rounded-3 mt-3 bg-light border">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 p-2">
        <div>
            <h6 class="fw-bold mb-1 text-dark d-flex align-items-center" style="font-size: 0.92rem;">
                <i class="fa-solid fa-arrows-rotate text-success me-2 fs-5"></i> Sinkronisasi Dataset Lengkap (112 Siswa Dummy)
            </h6>
            <small class="text-muted" style="font-size: 0.78rem;">
                Muat atau samakan dataset lengkap 112 siswa (32 Kelas 7, 40 Kelas 8, 20 Kelas 9A, 20 Kelas 9B), data wali murid, riwayat kenaikan kelas, hari efektif, dan sample kehadiran agar 100% identik dengan local.
            </small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form action="{{ route('admin.backup.migrate') }}" method="POST" onsubmit="return confirm('Jalankan migrasi struktur database terbaru?');">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3.5 py-2 fw-bold shadow-sm d-inline-flex align-items-center text-nowrap gap-1.5" style="font-size: 0.8rem;" title="Membuat tabel baru jika ada perubahan skema database">
                    <i class="fa-solid fa-table"></i>
                    <span>Update Struktur Database</span>
                </button>
            </form>
            <form action="{{ route('admin.backup.seedDummy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memuat/menyinkronkan dataset lengkap 112 siswa dummy ke database?');">
                @csrf
                <button type="submit" class="btn btn-sm btn-success rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center text-nowrap gap-2" style="font-size: 0.8rem;">
                    <i class="fa-solid fa-rotate"></i>
                    <span>Sinkronkan Dataset Siswa</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
