@extends('layouts.app')

@section('content')
<style>
    /* Gradient Header Widgets */
    .panel-widget {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        margin-bottom: 0.85rem;
    }

    .panel-header-purple {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        color: #ffffff;
        padding: 0.85rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .panel-header-teal {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        padding: 0.85rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .stat-metric-box {
        text-align: center;
        padding: 0.65rem 0.35rem;
    }

    .stat-metric-label {
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 4px;
        display: block;
    }

    .stat-metric-val {
        font-size: 1.55rem;
        font-weight: 800;
        line-height: 1;
        margin: 0;
    }

    .text-hadir { color: #16a34a; }
    .text-terlambat { color: #d97706; }
    .text-sakit { color: #475569; }
    .text-izin { color: #0284c7; }
    .text-alpa { color: #dc2626; }
    .text-total { color: #7c3aed; }
</style>

<!-- 1. Top 4 Metric Cards -->
<div class="row g-3 mb-3">
    <!-- Card 1: Jumlah Siswa -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card-floating">
            <div class="stat-floating-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Jumlah Siswa</span>
                <h3 class="stat-value">{{ $totalSiswa }}</h3>
            </div>
            <div class="stat-footer">
                <i class="fa-solid fa-check text-success"></i>
                <span>Terdaftar (Kelas 7, 8, 9)</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Jumlah Guru -->
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
                <i class="fa-solid fa-check text-success"></i>
                <span>Wali Kelas</span>
            </div>
        </div>
    </div>

    <!-- Card 3: Kelas Aktif -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card-floating">
            <div class="stat-floating-icon" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                <i class="fa-solid fa-school"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Total Kelas</span>
                <h3 class="stat-value">{{ $totalKelas }} <span class="fs-6 text-muted fw-normal">/ 3 Tingkat</span></h3>
            </div>
            <div class="stat-footer">
                <i class="fa-solid fa-building-columns text-primary"></i>
                <span>SMP IT Al-Muttaqin</span>
            </div>
        </div>
    </div>

    <!-- Card 4: Petugas Admin -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card-floating">
            <div class="stat-floating-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div class="stat-content">
                <span class="stat-label">Jumlah Petugas</span>
                <h3 class="stat-value">1</h3>
            </div>
            <div class="stat-footer">
                <i class="fa-solid fa-shield text-danger"></i>
                <span>Petugas dan Administrator</span>
            </div>
        </div>
    </div>
</div>

<!-- 2. Middle Row: Widget Absensi Siswa Hari Ini & Grafik 7 Hari Terakhir -->
<div class="row g-3 mb-2">
    <!-- Absensi Siswa Hari Ini Panel -->
    <div class="col-12 col-lg-6">
        <div class="panel-widget h-100">
            <div class="panel-header-purple">
                <div>
                    <h6 class="fw-bold mb-0 text-white" style="font-size: 0.95rem;">Absensi Siswa Hari Ini</h6>
                    <small class="text-white-50" style="font-size: 0.75rem;">{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}</small>
                </div>
            </div>

            <div class="p-3.5 py-4">
                <div class="row g-1 justify-content-around">
                    <div class="col-4 col-sm-2 stat-metric-box">
                        <span class="stat-metric-label text-hadir">Hadir</span>
                        <h4 class="stat-metric-val text-hadir">{{ $totalHadir }}</h4>
                    </div>
                    <div class="col-4 col-sm-2 stat-metric-box">
                        <span class="stat-metric-label text-terlambat">Terlambat</span>
                        <h4 class="stat-metric-val text-terlambat">{{ $totalTerlambat }}</h4>
                    </div>
                    <div class="col-4 col-sm-2 stat-metric-box">
                        <span class="stat-metric-label text-sakit">Sakit</span>
                        <h4 class="stat-metric-val text-sakit">{{ $totalSakit }}</h4>
                    </div>
                    <div class="col-4 col-sm-2 stat-metric-box">
                        <span class="stat-metric-label text-izin">Izin</span>
                        <h4 class="stat-metric-val text-izin">{{ $totalIzin }}</h4>
                    </div>
                    <div class="col-4 col-sm-2 stat-metric-box">
                        <span class="stat-metric-label text-alpa">Alfa</span>
                        <h4 class="stat-metric-val text-alpa">{{ $totalAlpa }}</h4>
                    </div>
                    <div class="col-4 col-sm-2 stat-metric-box">
                        <span class="stat-metric-label text-total">Total</span>
                        <h4 class="stat-metric-val text-total">{{ $totalSiswaFiltered }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tingkat Kehadiran Siswa (Grafik 7 Hari Terakhir) -->
    <div class="col-12 col-lg-6">
        <div class="panel-widget h-100">
            <div class="panel-header-teal">
                <div>
                    <h6 class="fw-bold mb-0 text-white" style="font-size: 0.95rem;">Tingkat Kehadiran Siswa</h6>
                    <small class="text-white-50" style="font-size: 0.75rem;">Statistik kehadiran 7 hari terakhir | {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}</small>
                </div>
            </div>

            <div class="p-3">
                <div style="height: 145px; position: relative;">
                    <canvas id="chartKehadiran"></canvas>
                </div>
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
                            borderRadius: 3,
                            barPercentage: 0.7,
                            categoryPercentage: 0.7
                        },
                        {
                            label: 'Sakit',
                            data: {!! json_encode($chartSakit) !!},
                            backgroundColor: '#f59e0b',
                            borderRadius: 3,
                            barPercentage: 0.7,
                            categoryPercentage: 0.7
                        },
                        {
                            label: 'Izin',
                            data: {!! json_encode($chartIzin) !!},
                            backgroundColor: '#06b6d4',
                            borderRadius: 3,
                            barPercentage: 0.7,
                            categoryPercentage: 0.7
                        },
                        {
                            label: 'Alfa',
                            data: {!! json_encode($chartAlfa) !!},
                            backgroundColor: '#ef4444',
                            borderRadius: 3,
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
