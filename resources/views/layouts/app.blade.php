<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SMP IT Al-Mutaqin - Presensi Siswa & Notifikasi WA</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0F52BA;
            --primary-hover: #083885;
            --accent-color: #00D26A;
            --sidebar-width: 260px;
            --bg-light: #F4F7FE;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: #2D3748;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, #1E293B 0%, #0F172A 100%);
            color: #fff;
            padding-top: 1.5rem;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            list-style: none;
            padding: 1rem 0.75rem;
            margin: 0;
        }

        .sidebar-menu .nav-item {
            margin-bottom: 0.35rem;
        }

        .sidebar-menu .nav-link {
            color: #94A3B8;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .sidebar-menu .nav-link:hover, .sidebar-menu .nav-link.active {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu .nav-link.active {
            background: var(--primary-color);
            box-shadow: 0 4px 12px rgba(15, 82, 186, 0.3);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
        }

        .top-header {
            background: #FFFFFF;
            padding: 1rem 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-custom {
            background: #FFFFFF;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h5 class="fw-bold m-0 text-white"><i class="fa-solid fa-qrcode text-warning me-2"></i>SMP IT Al-Mutaqin</h5>
            <small class="text-secondary">Monitoring & WA Fonnte</small>
        </div>
        <ul class="sidebar-menu">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('presensi.scan') }}" target="_blank" class="nav-link text-warning">
                    <i class="fa-solid fa-expand"></i> Kios Scanner QR
                </a>
            </li>
            
            <li class="px-3 text-uppercase fs-7 text-secondary mt-3 mb-1 fw-bold">Master Data</li>
            <li class="nav-item">
                <a href="{{ route('admin.siswa.index') }}" class="nav-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-graduate"></i> Data Siswa & QR
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.guru.index') }}" class="nav-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chalkboard-user"></i> Data Guru
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.kelas.index') }}" class="nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-school"></i> Data Kelas
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.orangtua.index') }}" class="nav-link {{ request()->routeIs('admin.orangtua.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> Data Orang Tua
                </a>
            </li>

            <li class="px-3 text-uppercase fs-7 text-secondary mt-3 mb-1 fw-bold">Monitoring & WA</li>
            <li class="nav-item">
                <a href="{{ route('admin.rekap.monitoring') }}" class="nav-link {{ request()->routeIs('admin.rekap.monitoring') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i> Live Monitoring
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.rekap.index') }}" class="nav-link {{ request()->routeIs('admin.rekap.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice"></i> Rekap Bulanan
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.fonnte.index') }}" class="nav-link {{ request()->routeIs('admin.fonnte.*') ? 'active' : '' }}">
                    <i class="fa-brands fa-whatsapp text-success"></i> Setting Fonnte WA
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <div class="top-header">
            <div>
                <h5 class="fw-bold mb-0">Sistem Monitoring Presensi Siswa</h5>
                <small class="text-muted">SMP IT Al-Mutaqin - Notifikasi WA Fonnte Otomatis</small>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-light text-dark p-2 border">
                    <i class="fa-regular fa-calendar-check text-primary me-1"></i> {{ date('d M Y') }}
                </span>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Scripts -->
    <script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
