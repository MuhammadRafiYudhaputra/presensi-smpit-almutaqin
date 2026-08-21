@extends('layouts.app')

@section('content')
<div class="card card-custom p-4 shadow-sm border-0 rounded-4">
    <!-- Header Halaman Generate Laporan -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4 pb-3 border-bottom">
        <div>
            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center">
                <i class="fa-solid fa-file-invoice text-primary me-2 fs-4"></i> Generate Laporan Presensi Siswa
            </h5>
            <small class="text-muted">Cetak dan unduh laporan rekapitulasi presensi siswa (Bulanan & Semester) berformat PDF resmi</small>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-1.5 rounded-pill fw-semibold" style="font-size: 0.85rem;">
            <i class="fa-solid fa-users me-1"></i> Total: {{ $totalSiswa }} Siswa
        </span>
    </div>

    <!-- Dual Form Cards (Bulanan & Semester Khusus Siswa) -->
    <div class="row g-4">
        <!-- 1. KARTU LAPORAN ABSEN SISWA (BULANAN) -->
        <div class="col-lg-6">
            <div class="card border rounded-4 p-4 h-100 bg-light bg-opacity-40 shadow-none d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                        <div>
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                                <i class="fa-solid fa-calendar-days text-primary me-2 fs-5"></i> Laporan Absen Siswa
                            </h6>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2.5 py-1 rounded-pill" style="font-size: 0.75rem;">
                            Periode Bulanan
                        </span>
                    </div>

                    <form action="{{ route('admin.rekap.cetak') }}" method="GET" target="_blank" id="formBulanan">
                        <input type="hidden" name="mode" value="bulanan">

                        <!-- Pilih Bulan & Tahun -->
                        <div class="row g-2 mb-3">
                            <div class="col-sm-7">
                                <label class="form-label fw-bold text-dark mb-1 small">Bulan :</label>
                                <select name="bulan" class="form-select form-select-sm shadow-sm">
                                    @for($m=1; $m<=12; $m++)
                                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-sm-5">
                                <label class="form-label fw-bold text-dark mb-1 small">Tahun :</label>
                                <select name="tahun" class="form-select form-select-sm shadow-sm">
                                    @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                                        <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Pilih Kelas -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-1 small">Kelas :</label>
                            <select name="kelas_id" class="form-select form-select-sm shadow-sm">
                                <option value="">Semua Kelas</option>
                                @foreach($kelases as $k)
                                    <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }} - {{ $k->siswas_count }} siswa</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Tombol Action (Generate PDF Saja) -->
                <div class="pt-2">
                    <button type="submit" form="formBulanan" name="format" value="pdf" class="btn btn-danger text-uppercase fw-bold rounded-pill py-2.5 w-100 shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="fa-solid fa-print fs-6"></i> GENERATE PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. KARTU LAPORAN ABSEN SISWA (SEMESTER) -->
        <div class="col-lg-6">
            <div class="card border rounded-4 p-4 h-100 bg-light bg-opacity-40 shadow-none d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                        <div>
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                                <i class="fa-solid fa-graduation-cap text-success me-2 fs-5"></i> Laporan Absen Siswa
                            </h6>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-2.5 py-1 rounded-pill" style="font-size: 0.75rem;">
                            Periode Semester
                        </span>
                    </div>

                    <form action="{{ route('admin.rekap.cetak') }}" method="GET" target="_blank" id="formSemester">
                        <input type="hidden" name="mode" value="semester">

                        <!-- Pilih Semester & Tahun Ajaran -->
                        <div class="row g-2 mb-3">
                            <div class="col-sm-7">
                                <label class="form-label fw-bold text-dark mb-1 small">Semester :</label>
                                <select name="semester" class="form-select form-select-sm shadow-sm">
                                    <option value="ganjil" selected>Semester Ganjil (Jul - Des)</option>
                                    <option value="genap">Semester Genap (Jan - Jun)</option>
                                </select>
                            </div>
                            <div class="col-sm-5">
                                <label class="form-label fw-bold text-dark mb-1 small">Tahun Ajaran :</label>
                                <select name="tahun" class="form-select form-select-sm shadow-sm">
                                    @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                                        <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}/{{ $y+1 }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Pilih Kelas -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-1 small">Kelas :</label>
                            <select name="kelas_id" class="form-select form-select-sm shadow-sm">
                                <option value="">Semua Kelas</option>
                                @foreach($kelases as $k)
                                    <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }} - {{ $k->siswas_count }} siswa</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Tombol Action (Generate PDF Saja) -->
                <div class="pt-2">
                    <button type="submit" form="formSemester" name="format" value="pdf" class="btn btn-danger text-uppercase fw-bold rounded-pill py-2.5 w-100 shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="fa-solid fa-print fs-6"></i> GENERATE PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
