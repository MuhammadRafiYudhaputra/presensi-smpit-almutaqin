<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMP IT Al-Muttaqin - Presensi Siswa</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Pro / Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --sidebar-bg: #ffffff;
            --sidebar-border: #e2e8f0;
            --primary-cyan: #00c0ef;
            --body-bg: #f4f6f9;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--body-bg);
            color: var(--text-main);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        /* Layout Container */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Sidebar Styling (Compact & Natural) */
        .sidebar {
            width: 250px;
            min-width: 250px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            padding-bottom: 1.5rem;
            z-index: 1040;
            transition: transform 0.3s ease;
        }

        /* Sidebar Brand Header (Sesuai Referensi Gambar) */
        .sidebar-brand {
            padding: 1.25rem 1rem 1rem;
            text-align: center;
        }

        .sidebar-brand-title {
            font-size: 0.9rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e293b;
            line-height: 1.3;
            margin-bottom: 3px;
        }

        .sidebar-brand-sub {
            font-size: 0.7rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
        }

        .sidebar-divider {
            border-top: 1px solid #e2e8f0;
            margin: 0 1rem 0.65rem;
            opacity: 0.8;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 0.85rem;
            margin: 0;
        }

        .sidebar-menu .nav-item {
            margin-bottom: 3px;
        }

        .sidebar-menu .nav-link {
            color: #334155;
            font-weight: 600;
            font-size: 0.84rem;
            padding: 0.55rem 0.85rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .sidebar-menu .nav-link i {
            font-size: 0.95rem;
            width: 18px;
            text-align: center;
            color: #8c9ba5;
            transition: color 0.15s ease;
        }

        .sidebar-menu .nav-link:hover {
            color: #0f172a;
            background: #f8fafc;
        }
        .sidebar-menu .nav-link:hover i {
            color: #00c0ef;
        }

        /* Active Menu Button (Cyan Flat Minimalist Sesuai Gambar) */
        .sidebar-menu .nav-link.active {
            color: #ffffff !important;
            background: #00c0ef;
            box-shadow: 0 4px 10px rgba(0, 192, 239, 0.3);
            font-weight: 700;
        }
        .sidebar-menu .nav-link.active i {
            color: #ffffff !important;
        }

        /* Logout button style inside sidebar menu */
        .sidebar-menu .logout-btn-link {
            color: #ef4444 !important;
            font-weight: 600;
            font-size: 0.84rem;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.55rem 0.85rem;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.15s ease;
            width: 100%;
            background: transparent;
            border: none;
        }
        .sidebar-menu .logout-btn-link i {
            color: #ef4444 !important;
            font-size: 0.95rem;
            width: 18px;
            text-align: center;
        }
        .sidebar-menu .logout-btn-link:hover {
            background: rgba(239, 68, 68, 0.08);
            color: #dc2626 !important;
        }

        /* Main Content Container */
        .main-content {
            flex: 1;
            min-width: 0;
            padding: 1.15rem 1.5rem;
            background-color: var(--body-bg);
        }

        /* Top Minimalist Header / Navbar */
        .top-header-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 0.65rem 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .top-header-title {
            font-weight: 800;
            font-size: 1.15rem;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .user-role-badge {
            background: #ffffff;
            color: #0f172a;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 0.35rem 0.85rem;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Floating Accent Badge Stat Cards */
        .stat-card-floating {
            background: #ffffff;
            border-radius: 10px;
            padding: 1.25rem 1rem 1rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            position: relative;
            margin-top: 14px;
            transition: transform 0.2s ease;
        }
        .stat-card-floating:hover {
            transform: translateY(-2px);
        }

        .stat-floating-icon {
            position: absolute;
            top: -14px;
            left: 14px;
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.25rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        }

        .stat-card-floating .stat-content {
            text-align: right;
            margin-bottom: 0.5rem;
        }

        .stat-card-floating .stat-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 2px;
            display: block;
        }

        .stat-card-floating .stat-value {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.15;
            margin: 0;
        }

        .stat-card-floating .stat-footer {
            border-top: 1px solid #f1f5f9;
            padding-top: 0.5rem;
            font-size: 0.74rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Mobile Layout Handling */
        .mobile-navbar {
            display: none;
            background: #ffffff;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            margin-bottom: 0.85rem;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(3px);
            z-index: 1030;
        }

        @media (max-width: 991.98px) {
            .app-wrapper {
                flex-direction: column;
            }

            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                transform: translateX(-100%);
                width: 260px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-backdrop.show {
                display: block;
            }

            .main-content {
                padding: 0.85rem 0.75rem 1.5rem;
            }

            .mobile-navbar {
                display: flex;
            }
        }
    </style>
</head>
<body>

    <!-- Mobile Overlay Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <div class="app-wrapper">
        <!-- Sidebar Navigation (Natural Flow, Seluruh Menu & Logout Terlihat Langsung) -->
        <div class="sidebar" id="sidebarDrawer">
            <!-- Branding Header (Sesuai Referensi Gambar) -->
            <div class="sidebar-brand">
                <div class="sidebar-brand-title">
                    OPERATOR<br>PETUGAS ABSENSI
                </div>
                <span class="sidebar-brand-sub">SMP IT AL-MUTTAQIN</span>
                <!-- Close Button on Mobile -->
                <button type="button" class="btn btn-sm text-secondary d-lg-none p-1 position-absolute top-0 end-0 m-2" onclick="toggleSidebar()" aria-label="Tutup Menu">
                    <i class="fa-solid fa-xmark fs-4"></i>
                </button>
            </div>

            <div class="sidebar-divider"></div>

            <ul class="sidebar-menu">
                @if(Auth::check() && Auth::user()->role === 'guru')
                    <!-- MENU WALI KELAS -->
                    <li class="nav-item">
                        <a href="{{ route('guru.monitoring') }}" class="nav-link {{ request()->routeIs('guru.monitoring') ? 'active' : '' }}">
                            <i class="fa-solid fa-rotate-left"></i> Monitoring Kehadiran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('guru.rekap') }}" class="nav-link {{ request()->routeIs('guru.rekap') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-lines"></i> Rekap Kehadiran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('guru.siswa.index') }}" class="nav-link {{ request()->routeIs('guru.siswa.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-address-book"></i> Biodata Siswa Binaan
                        </a>
                    </li>
                    <li class="nav-item mt-2 pt-2 border-top">
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="logout-btn-link">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar / Logout
                            </button>
                        </form>
                    </li>
                @else
                    <!-- MENU OPERATOR / ADMIN TU (Sesuai Struktur Gambar) -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-table-cells-large"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('presensi.scan') }}" class="nav-link {{ request()->routeIs('presensi.scan') ? 'active' : '' }}">
                            <i class="fa-solid fa-qrcode"></i> Scan Presensi QR
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.rekap.monitoring') }}" class="nav-link {{ request()->routeIs('admin.rekap.monitoring') ? 'active' : '' }}">
                            <i class="fa-solid fa-list-check"></i> Absensi Siswa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.siswa.index') }}" class="nav-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-user"></i> Data Siswa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.kelas.index') }}" class="nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-graduation-cap"></i> Data Kelas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.guru.index') }}" class="nav-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-chalkboard-user"></i> Data Guru
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.rekap.index') }}" class="nav-link {{ request()->routeIs('admin.rekap.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-simple"></i> Rekapitulasi Presensi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.rekap.cetak') }}" target="_blank" class="nav-link">
                            <i class="fa-solid fa-print"></i> Generate Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.fonnte.index') }}" class="nav-link {{ request()->routeIs('admin.fonnte.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-gear"></i> Pengaturan WA
                        </a>
                    </li>
                    <li class="nav-item mt-2 pt-2 border-top">
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="logout-btn-link">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar / Logout
                            </button>
                        </form>
                    </li>
                @endif
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Mobile Top Navbar -->
            <div class="mobile-navbar rounded-3">
                <button class="btn btn-light border p-2 rounded-3 text-dark" onclick="toggleSidebar()" aria-label="Buka Menu Sidebar">
                    <i class="fa-solid fa-bars-staggered fs-5"></i>
                </button>
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 26px; height: 26px; object-fit: contain;">
                    <span class="fw-bold text-dark fs-6">SMP IT Al-Muttaqin</span>
                </div>
                <div class="user-role-badge py-1 px-2" style="font-size: 0.72rem;">
                    {{ Auth::user()->role === 'guru' ? 'GURU' : 'ADMIN' }}
                </div>
            </div>

            <!-- Desktop Top Header Navbar (Minimalist Style Sesuai Gambar) -->
            <div class="top-header-card d-none d-lg-flex">
                <div>
                    <h5 class="top-header-title">
                        @if(request()->routeIs('admin.dashboard'))
                            Dashboard
                        @elseif(request()->routeIs('admin.siswa.*'))
                            Data Siswa
                        @elseif(request()->routeIs('admin.kelas.*'))
                            Data Kelas
                        @elseif(request()->routeIs('admin.guru.*'))
                            Data Guru & Wali Kelas
                        @elseif(request()->routeIs('admin.rekap.monitoring') || request()->routeIs('guru.monitoring'))
                            Absensi Siswa (Monitoring Live)
                        @elseif(request()->routeIs('admin.rekap.index') || request()->routeIs('guru.rekap'))
                            Rekapitulasi Kehadiran Siswa
                        @elseif(request()->routeIs('guru.siswa.*'))
                            Biodata Siswa Binaan
                        @elseif(request()->routeIs('presensi.scan'))
                            Scanner QR Code
                        @elseif(request()->routeIs('admin.fonnte.*'))
                            Pengaturan Gateway WhatsApp
                        @else
                            Sistem Presensi Siswa
                        @endif
                    </h5>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="user-role-badge">
                        <i class="fa-solid fa-circle-user text-danger"></i>
                        <span>USER : {{ strtoupper(Auth::user()->role === 'guru' ? (Auth::user()->name) : 'SUPERADMIN') }}</span>
                    </div>
                </div>
            </div>

            <!-- Alert Notifications -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center gap-2" role="alert">
                    <i class="fa-solid fa-circle-check fs-5 text-success"></i>
                    <div class="fw-semibold">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center gap-2" role="alert">
                    <i class="fa-solid fa-triangle-exclamation fs-5 text-danger"></i>
                    <div class="fw-semibold">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3" role="alert">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fa-solid fa-circle-xmark fs-5 text-danger"></i>
                        <strong class="fw-bold">Perhatian:</strong>
                    </div>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Main Page Content Slot -->
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js for Minimalist Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarDrawer');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('show');
            }
        }
    </script>
</body>
</html>
