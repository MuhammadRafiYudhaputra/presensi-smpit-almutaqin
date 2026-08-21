@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <!-- Banner Header Generate Laporan (Sesuai Gaya Desain Website) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
        <div class="card-body p-4 text-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-file-invoice"></i> Generate Laporan
                    </h4>
                    <p class="mb-0 text-white-50 small">Cetak & Export Laporan Rekapitulasi Presensi Siswa SMP IT Al-Muttaqin (Format PDF / Cetak Resmi & Dokumen)</p>
                </div>
                <div class="badge bg-white bg-opacity-20 text-white border border-white border-opacity-25 px-3 py-2 rounded-pill fs-6">
                    <i class="fa-solid fa-school me-1"></i> SMP IT AL-MUTTAQIN
                </div>
            </div>
        </div>
    </div>

    <!-- Dual Form Cards (Bulanan & Semester Khusus Siswa) -->
    <div class="row g-4">
        <!-- 1. KARTU LAPORAN ABSEN SISWA (BULANAN) -->
        <div class="col-lg-6">
            <div class="card card-custom h-100 p-4 shadow-sm border-0 rounded-4">
                <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <i class="fa-solid fa-calendar-days text-primary me-2 fs-5"></i> Laporan Absen Siswa
                        </h5>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2.5 py-0.5 rounded-pill mt-1" style="font-size: 0.75rem;">
                            Periode Bulanan
                        </span>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block" style="font-size: 0.78rem;">Total Siswa Aktif</small>
                        <span class="fw-bold text-dark fs-6">{{ $totalSiswa }} Siswa</span>
                    </div>
                </div>

                <form action="{{ route('admin.rekap.cetak') }}" method="GET" target="_blank">
                    <input type="hidden" name="mode" value="bulanan">

                    <!-- Pilih Bulan & Tahun -->
                    <div class="row g-2 mb-3">
                        <div class="col-sm-7">
                            <label class="form-label fw-bold text-dark mb-1 small">
                                <i class="fa-regular fa-calendar text-primary me-1"></i> Pilih Bulan:
                            </label>
                            <select name="bulan" class="form-select shadow-sm">
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-sm-5">
                            <label class="form-label fw-bold text-dark mb-1 small">
                                <i class="fa-regular fa-calendar-check text-primary me-1"></i> Tahun:
                            </label>
                            <select name="tahun" class="form-select shadow-sm">
                                @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                                    <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Pilih Kelas -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-1 small">
                            <i class="fa-solid fa-chalkboard-user text-primary me-1"></i> Pilih Kelas Siswa:
                        </label>
                        <select name="kelas_id" class="form-select shadow-sm">
                            <option value="">Semua Kelas (Total {{ $totalSiswa }} Siswa)</option>
                            @foreach($kelases as $k)
                                <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }} - {{ $k->siswas_count }} siswa</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Hari Efektif Sekolah -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-1 small">
                            <i class="fa-solid fa-business-time text-primary me-1"></i> Hari Efektif Masuk Sekolah:
                        </label>
                        <div class="input-group shadow-sm">
                            <input type="number" name="hari_efektif" class="form-control" value="21" min="1" max="31" required>
                            <span class="input-group-text bg-light text-muted small">Hari Masuk</span>
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">
                            *Dasar perhitungan persentase kehadiran siswa dalam sebulan.
                        </small>
                    </div>

                    <!-- Tombol Action (Generate PDF & Generate DOC) -->
                    <div class="pt-2">
                        <button type="submit" name="format" value="pdf" class="btn btn-danger text-uppercase fw-bold rounded-3 py-2.5 w-100 shadow-sm d-flex align-items-center justify-content-center gap-2 mb-2.5">
                            <i class="fa-solid fa-print fs-5"></i> GENERATE PDF / CETAK
                        </button>
                        <button type="submit" name="format" value="doc" class="btn btn-primary text-uppercase fw-bold rounded-3 py-2.5 w-100 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="fa-solid fa-file-word fs-5"></i> GENERATE DOC
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. KARTU LAPORAN ABSEN SISWA (SEMESTER) -->
        <div class="col-lg-6">
            <div class="card card-custom h-100 p-4 shadow-sm border-0 rounded-4">
                <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <i class="fa-solid fa-graduation-cap text-success me-2 fs-5"></i> Laporan Absen Siswa
                        </h5>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-2.5 py-0.5 rounded-pill mt-1" style="font-size: 0.75rem;">
                            Periode Semester
                        </span>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block" style="font-size: 0.78rem;">Total Siswa Aktif</small>
                        <span class="fw-bold text-dark fs-6">{{ $totalSiswa }} Siswa</span>
                    </div>
                </div>

                <form action="{{ route('admin.rekap.cetak') }}" method="GET" target="_blank">
                    <input type="hidden" name="mode" value="semester">

                    <!-- Pilih Semester & Tahun Ajaran -->
                    <div class="row g-2 mb-3">
                        <div class="col-sm-7">
                            <label class="form-label fw-bold text-dark mb-1 small">
                                <i class="fa-solid fa-calendar-check text-success me-1"></i> Pilih Semester:
                            </label>
                            <select name="semester" class="form-select shadow-sm">
                                <option value="ganjil" selected>Semester Ganjil (Jul - Des)</option>
                                <option value="genap">Semester Genap (Jan - Jun)</option>
                            </select>
                        </div>
                        <div class="col-sm-5">
                            <label class="form-label fw-bold text-dark mb-1 small">
                                <i class="fa-regular fa-calendar-days text-success me-1"></i> Tahun Ajaran:
                            </label>
                            <select name="tahun" class="form-select shadow-sm">
                                @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                                    <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}/{{ $y+1 }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Pilih Kelas -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark mb-1 small">
                            <i class="fa-solid fa-chalkboard-user text-success me-1"></i> Pilih Kelas Siswa:
                        </label>
                        <select name="kelas_id" class="form-select shadow-sm">
                            <option value="">Semua Kelas (Total {{ $totalSiswa }} Siswa)</option>
                            @foreach($kelases as $k)
                                <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }} - {{ $k->siswas_count }} siswa</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Hari Efektif Semester -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-1 small">
                            <i class="fa-solid fa-business-time text-success me-1"></i> Hari Efektif Semester:
                        </label>
                        <div class="input-group shadow-sm">
                            <input type="number" name="hari_efektif" class="form-control" value="108" min="1" max="180" required>
                            <span class="input-group-text bg-light text-muted small">Hari Masuk</span>
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">
                            *Dasar perhitungan persentase kehadiran siswa dalam 1 semester.
                        </small>
                    </div>

                    <!-- Tombol Action (Generate PDF & Generate DOC) -->
                    <div class="pt-2">
                        <button type="submit" name="format" value="pdf" class="btn btn-danger text-uppercase fw-bold rounded-3 py-2.5 w-100 shadow-sm d-flex align-items-center justify-content-center gap-2 mb-2.5">
                            <i class="fa-solid fa-print fs-5"></i> GENERATE PDF / CETAK
                        </button>
                        <button type="submit" name="format" value="doc" class="btn btn-primary text-uppercase fw-bold rounded-3 py-2.5 w-100 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="fa-solid fa-file-word fs-5"></i> GENERATE DOC
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
