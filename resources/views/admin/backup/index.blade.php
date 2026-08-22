@extends('layouts.app')

@section('content')
<style>
    .backup-panel-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .backup-header-purple {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        color: #ffffff;
        padding: 1.25rem 1.5rem;
    }

    .backup-header-teal {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        padding: 1.25rem 1.5rem;
    }

    .backup-body {
        padding: 1.75rem 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .backup-section-title {
        font-weight: 800;
        font-size: 1rem;
        color: #1e293b;
        margin-bottom: 0.35rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .backup-section-desc {
        font-size: 0.84rem;
        color: #64748b;
        margin-bottom: 1rem;
        line-height: 1.45;
    }

    .backup-divider {
        border-top: 1px dashed #cbd5e1;
        margin: 1.5rem 0;
    }

    .warning-alert-box {
        background-color: #fffbeb;
        border: 1px solid #fef3c7;
        border-left: 4px solid #f59e0b;
        border-radius: 8px;
        padding: 0.65rem 0.85rem;
        font-size: 0.8rem;
        color: #92400e;
        margin-bottom: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }
</style>

<!-- Top Title Card -->
<div class="card card-custom p-4 shadow-sm border-0 rounded-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center">
                <i class="fa-solid fa-cloud-arrow-down text-primary me-2 fs-4"></i> Backup &amp; Restore Data Sistem
            </h5>
            <small class="text-muted">Kelola pencadangan salinan data database dan penyimpanan berkas QR Code untuk menjaga keamanan data sekolah.</small>
        </div>
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                <i class="fa-solid fa-shield-halved me-1"></i> Data Security Center
            </span>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center gap-2">
        <i class="fa-solid fa-circle-check fs-5"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation fs-5"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3">
        <ul class="mb-0 small ps-3">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4 mb-4">
    <!-- 1. Card Database Backup & Restore -->
    <div class="col-12 col-lg-6">
        <div class="backup-panel-card">
            <div class="backup-header-purple">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="fa-solid fa-database fs-4"></i>
                    <h5 class="fw-bold mb-0 text-white">Database</h5>
                </div>
                <small class="text-white-50">Backup &amp; Restore Database Sistem (Format SQL)</small>
            </div>

            <div class="backup-body">
                <!-- Section 1: Backup Database -->
                <div>
                    <div class="backup-section-title">
                        <i class="fa-solid fa-download text-primary"></i> Backup Database
                    </div>
                    <div class="backup-section-desc">
                        Unduh seluruh file cadangan <code>.sql</code> berisi seluruh tabel (siswa, presensi, kelas, akun, dll).
                        <div class="mt-2 small text-dark d-flex gap-3">
                            <span><i class="fa-solid fa-server text-muted me-1"></i> Driver: <strong>{{ strtoupper($driver) }}</strong></span>
                            <span><i class="fa-solid fa-hard-drive text-muted me-1"></i> Ukuran: <strong>{{ $dbSize }}</strong></span>
                        </div>
                    </div>
                    <a href="{{ route('admin.backup.database') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2 text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.3px;">
                        <i class="fa-solid fa-cloud-arrow-down fs-6"></i> Download Backup Database
                    </a>
                </div>

                <div class="backup-divider"></div>

                <!-- Section 2: Restore Database -->
                <div>
                    <div class="backup-section-title">
                        <i class="fa-solid fa-upload text-warning"></i> Restore Database
                    </div>
                    
                    <div class="warning-alert-box">
                        <i class="fa-solid fa-triangle-exclamation fs-5 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <strong>Peringatan Penting:</strong> Tindakan ini akan menimpa seluruh database saat ini dengan data dari berkas yang diunggah.
                        </div>
                    </div>

                    <form action="{{ route('admin.backup.database.restore') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Apakah Anda yakin ingin melakukan restore database? Seluruh data saat ini akan digantikan oleh isi file backup.');">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Pilih Berkas Cadangan (.SQL):</label>
                            <input type="file" name="sql_file" class="form-control form-control-sm rounded-3 shadow-none" accept=".sql, .txt" required>
                        </div>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm d-inline-flex align-items-center gap-2 text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.3px;">
                            <i class="fa-solid fa-rotate-left fs-6"></i> Restore Database
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
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="fa-solid fa-qrcode fs-4"></i>
                    <h5 class="fw-bold mb-0 text-white">Foto &amp; QR Code</h5>
                </div>
                <small class="text-white-50">Backup &amp; Restore Folder Uploads &amp; QR Siswa (Format ZIP)</small>
            </div>

            <div class="backup-body">
                <!-- Section 1: Backup Foto -->
                <div>
                    <div class="backup-section-title">
                        <i class="fa-solid fa-download text-info"></i> Backup Foto (QR Code)
                    </div>
                    <div class="backup-section-desc">
                        Unduh seluruh file berkas gambar QR Code kartu siswa dalam bentuk arsip <code>.zip</code> terkompresi.
                        <div class="mt-2 small text-dark d-flex gap-3">
                            <span><i class="fa-solid fa-image text-muted me-1"></i> Total Gambar: <strong>{{ $totalQrFiles }} Berkas</strong></span>
                            <span><i class="fa-solid fa-hard-drive text-muted me-1"></i> Ukuran: <strong>{{ $qrFolderSize }}</strong></span>
                        </div>
                    </div>
                    <a href="{{ route('admin.backup.storage') }}" class="btn btn-info text-white rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2 text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.3px;">
                        <i class="fa-solid fa-cloud-arrow-down fs-6"></i> Download Backup Foto (ZIP)
                    </a>
                </div>

                <div class="backup-divider"></div>

                <!-- Section 2: Restore Foto -->
                <div>
                    <div class="backup-section-title">
                        <i class="fa-solid fa-upload text-warning"></i> Restore Foto / Asset
                    </div>
                    
                    <div class="warning-alert-box">
                        <i class="fa-solid fa-circle-info fs-5 flex-shrink-0 mt-0.5 text-warning"></i>
                        <div>
                            <strong>Peringatan:</strong> Berkas foto/QR code yang ada akan diperbarui atau ditimpa jika memiliki nama berkas yang sama.
                        </div>
                    </div>

                    <form action="{{ route('admin.backup.storage.restore') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Apakah Anda yakin ingin melakukan restore foto & QR Code ke folder sistem?');">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Pilih Berkas Arsip (.ZIP):</label>
                            <input type="file" name="zip_file" class="form-control form-control-sm rounded-3 shadow-none" accept=".zip" required>
                        </div>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm d-inline-flex align-items-center gap-2 text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.3px;">
                            <i class="fa-solid fa-rotate-left fs-6"></i> Restore Foto / Asset
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
