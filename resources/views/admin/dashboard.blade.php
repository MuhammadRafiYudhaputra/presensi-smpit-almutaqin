@extends('layouts.app')

@section('content')
<style>
    /* Metric Stat Item Styles */
    .metric-pill-box {
        text-align: center;
        padding: 0.85rem 0.5rem;
        border-radius: 12px;
        transition: transform 0.15s ease;
    }
    .metric-pill-box:hover {
        transform: translateY(-2px);
    }
    .metric-pill-box .stat-label {
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 4px;
        display: block;
    }
    .metric-pill-box .stat-val {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1;
        margin: 0;
    }
</style>

<!-- 1. Top 4 Metric Cards (Clean & Balanced Floating Cards) -->
<div class="row g-3 mb-4">
    <!-- Card 1: Jumlah Siswa (Royal Blue Accent) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card-floating">
            <div class="stat-floating-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Jumlah Siswa</span>
                <h3 class="stat-value">{{ $totalSiswa }}</h3>
            </div>
            <div class="stat-footer">
                <i class="fa-solid fa-circle-check text-primary"></i>
                <span>Terdaftar (Kelas 7, 8, 9)</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Jumlah Guru (Emerald Green Accent) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card-floating">
            <div class="stat-floating-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Jumlah Guru</span>
                <h3 class="stat-value">{{ $totalGuru }}</h3>
            </div>
            <div class="stat-footer">
                <i class="fa-solid fa-circle-check text-success"></i>
                <span>Wali Kelas</span>
            </div>
        </div>
    </div>

    <!-- Card 3: Kelas Aktif (Cyan / Teal Accent) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card-floating">
            <div class="stat-floating-icon" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                <i class="fa-solid fa-school"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Kelas / Rombel</span>
                <h3 class="stat-value">{{ $totalKelas }} <span class="fs-6 text-muted fw-normal">/ 3 Tingkat</span></h3>
            </div>
            <div class="stat-footer">
                <i class="fa-solid fa-building-columns text-info"></i>
                <span>SMP IT Al-Muttaqin</span>
            </div>
        </div>
    </div>

    <!-- Card 4: Petugas Admin (Warm Amber Accent) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card-floating">
            <div class="stat-floating-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Jumlah Petugas</span>
                <h3 class="stat-value">1</h3>
            </div>
            <div class="stat-footer">
                <i class="fa-solid fa-shield text-warning"></i>
                <span>Petugas dan Administrator</span>
            </div>
        </div>
    </div>
</div>

<!-- 2. Middle Row: Widget Absensi Siswa Hari Ini & Grafik 7 Hari Terakhir -->
<div class="row g-3 mb-2">
    <!-- Absensi Siswa Hari Ini Panel (Clean Card Header) -->
    <div class="col-12 col-lg-6">
        <div class="card card-custom h-100 p-4 shadow-sm border-0 rounded-4 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                            <i class="fa-solid fa-clipboard-user text-primary fs-5 me-2"></i> Absensi Siswa Hari Ini
                        </h6>
                        <small class="text-muted" style="font-size: 0.78rem;">{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</small>
                    </div>
                    <!-- Filter Dropdown Kelas -->
                    <form action="{{ route('admin.dashboard') }}" method="GET" class="m-0">
                        <select name="kelas_id" class="form-select form-select-sm shadow-sm" style="min-width: 175px; font-size: 0.78rem;" onchange="this.form.submit()">
                            <option value="">-- Semua Kelas ({{ $totalSiswa }} siswa) --</option>
                            @foreach($kelases as $k)
                                <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                    Kelas {{ $k->nama_kelas }} ({{ $k->siswas_count }} siswa)
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <!-- 6 Metric Item Pills -->
                <div class="row g-2 mb-3">
                    <div class="col-4 col-sm-2">
                        <div class="metric-pill-box bg-success bg-opacity-10 border border-success border-opacity-25 text-success">
                            <span class="stat-label">Hadir</span>
                            <h4 class="stat-val text-success">{{ $totalHadir }}</h4>
                        </div>
                    </div>
                    <div class="col-4 col-sm-2">
                        <div class="metric-pill-box bg-warning bg-opacity-10 border border-warning border-opacity-25 text-warning text-dark">
                            <span class="stat-label text-warning text-dark">Terlambat</span>
                            <h4 class="stat-val text-warning text-dark">{{ $totalTerlambat }}</h4>
                        </div>
                    </div>
                    <div class="col-4 col-sm-2">
                        <div class="metric-pill-box bg-secondary bg-opacity-10 border border-secondary border-opacity-25 text-secondary">
                            <span class="stat-label">Sakit</span>
                            <h4 class="stat-val text-secondary">{{ $totalSakit }}</h4>
                        </div>
                    </div>
                    <div class="col-4 col-sm-2">
                        <div class="metric-pill-box bg-info bg-opacity-10 border border-info border-opacity-25 text-info">
                            <span class="stat-label">Izin</span>
                            <h4 class="stat-val text-info">{{ $totalIzin }}</h4>
                        </div>
                    </div>
                    <div class="col-4 col-sm-2">
                        <div class="metric-pill-box bg-danger bg-opacity-10 border border-danger border-opacity-25 text-danger">
                            <span class="stat-label">Alfa</span>
                            <h4 class="stat-val text-danger">{{ $totalAlpa }}</h4>
                        </div>
                    </div>
                    <div class="col-4 col-sm-2">
                        <div class="metric-pill-box bg-primary bg-opacity-10 border border-primary border-opacity-25 text-primary">
                            <span class="stat-label">Total</span>
                            <h4 class="stat-val text-primary">{{ $totalSiswaFiltered }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top pt-2.5 mt-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted" style="font-size: 0.78rem;">
                    <i class="fa-solid fa-circle-info text-primary me-1"></i> Total hadir fisik: <strong>{{ $totalMasuk }} Siswa</strong>
                </small>
                <a href="{{ route('admin.rekap.monitoring') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 0.78rem;">
                    <i class="fa-solid fa-list-check me-1"></i> Buka Absensi Siswa
                </a>
            </div>
        </div>
    </div>

    <!-- Tingkat Kehadiran Siswa (Grafik 7 Hari Terakhir) -->
    <div class="col-12 col-lg-6">
        <div class="card card-custom h-100 p-4 shadow-sm border-0 rounded-4 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                            <i class="fa-solid fa-chart-column text-primary fs-5 me-2"></i> Tingkat Kehadiran Siswa
                        </h6>
                        <small class="text-muted" style="font-size: 0.78rem;">Statistik kehadiran 7 hari terakhir</small>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2.5 py-1 rounded-pill" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-chart-simple me-1"></i> Grafik Mingguan
                    </span>
                </div>

                <div style="height: 165px; position: relative;">
                    <canvas id="chartKehadiran"></canvas>
                </div>
            </div>

            <div class="border-top pt-2.5 mt-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted" style="font-size: 0.78rem;">Data sinkron otomatis</small>
                <a href="{{ route('admin.rekap.index') }}" class="text-decoration-none fw-bold text-primary small" style="font-size: 0.78rem;">
                    <i class="fa-solid fa-chart-column me-1"></i> Lihat Rekapitulasi Lengkap &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Initialization -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('chartKehadiran');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: 'Hadir',
                            data: {!! json_encode($chartHadir) !!},
                            backgroundColor: '#22c55e',
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.7
                        },
                        {
                            label: 'Sakit',
                            data: {!! json_encode($chartSakit) !!},
                            backgroundColor: '#f59e0b',
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.7
                        },
                        {
                            label: 'Izin',
                            data: {!! json_encode($chartIzin) !!},
                            backgroundColor: '#06b6d4',
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.7
                        },
                        {
                            label: 'Alfa',
                            data: {!! json_encode($chartAlfa) !!},
                            backgroundColor: '#ef4444',
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.7
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                font: {
                                    size: 10,
                                    weight: '600'
                                },
                                padding: 8
                            }
                        },
                        tooltip: {
                            padding: 8,
                            cornerRadius: 6
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { size: 10 }
                            },
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: { size: 10 }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
