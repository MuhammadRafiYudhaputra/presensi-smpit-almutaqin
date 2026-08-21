<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMP IT Al-Muttaqin - Presensi Siswa</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Pro / Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --sidebar-bg: #ffffff;
            --sidebar-border: #e2e8f0;
            --sidebar-hover: #f1f5f9;
            --primary-accent: #00c0ef;
            --primary-accent-dark: #0891b2;
            --primary-color: #0284c7;
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

        /* Sidebar Minimalist Styling */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow-y: auto;
            border-right: 1px solid var(--sidebar-border);
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.02);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem 1.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-bottom: 1px solid var(--sidebar-border);
            position: relative;
        }

        .sidebar-brand-top {
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .sidebar-brand-title {
            font-size: 0.95rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0284c7;
            margin-bottom: 4px;
        }

        .sidebar-brand-sub {
            font-size: 0.72rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sidebar-heading {
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            padding: 1.25rem 1.25rem 0.4rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0.75rem 0.85rem;
            margin: 0;
        }

        .sidebar-menu .nav-item {
            margin-bottom: 4px;
        }

        .sidebar-menu .nav-link {
            color: #475569;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 0.7rem 1rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .sidebar-menu .nav-link i {
            font-size: 1.05rem;
            width: 22px;
            text-align: center;
            color: #64748b;
            transition: color 0.2s ease;
        }

        .sidebar-menu .nav-link:hover {
            color: #0f172a;
            background: var(--sidebar-hover);
        }
        .sidebar-menu .nav-link:hover i {
            color: var(--primary-accent-dark);
        }

        /* Active Menu Button (Minimalist Vibrant Accent) */
        .sidebar-menu .nav-link.active {
            color: #ffffff;
            background: linear-gradient(135deg, #00c0ef 0%, #0891b2 100%);
            box-shadow: 0 4px 14px rgba(0, 192, 239, 0.35);
            font-weight: 700;
        }
        .sidebar-menu .nav-link.active i {
            color: #ffffff;
        }

        .sidebar-footer {
            padding: 1rem 0.85rem 1.5rem;
            border-top: 1px solid var(--sidebar-border);
        }

        .sidebar-footer .logout-btn {
            color: #ef4444;
            font-weight: 600;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.65rem 0.9rem;
            text-decoration: none;
            border-radius: 10px;
            transition: background 0.2s ease;
            width: 100%;
            background: transparent;
            border: none;
        }
        .sidebar-footer .logout-btn:hover {
            background: rgba(239, 68, 68, 0.08);
        }

        /* Main Content Container */
        .main-content {
            margin-left: 260px;
            padding: 1.5rem 2rem;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Top Minimalist Header / Navbar */
        .top-header-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 1rem 1.75rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .top-header-title {
            font-weight: 800;
            font-size: 1.25rem;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .user-role-badge {
            background: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            font-size: 0.82rem;
            padding: 0.45rem 1rem;
            border-radius: 50rem;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .date-badge {
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.82rem;
            padding: 0.45rem 0.9rem;
            border-radius: 50rem;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Floating Accent Badge Stat Cards (from reference UI) */
        .stat-card-floating {
            background: #ffffff;
            border-radius: 16px;
            padding: 1.5rem 1.25rem 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            position: relative;
            margin-top: 20px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card-floating:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        }

        .stat-floating-icon {
            position: absolute;
            top: -18px;
            left: 20px;
            width: 58px;
            height: 58px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.5rem;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
        }

        .stat-card-floating .stat-content {
            text-align: right;
            margin-bottom: 0.75rem;
        }

        .stat-card-floating .stat-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 2px;
            display: block;
        }

        .stat-card-floating .stat-value {
            font-size: 1.85rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin: 0;
        }

        .stat-card-floating .stat-footer {
            border-top: 1px solid #f1f5f9;
            padding-top: 0.65rem;
            font-size: 0.78rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Mobile Layout Handling */
        .mobile-navbar {
            display: none;
            background: #ffffff;
            padding: 0.85rem 1.25rem;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            margin-bottom: 1rem;
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
            z-index: 1040;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-backdrop.show {
                display: block;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem 0.85rem 2rem;
            }

            .mobile-navbar {
                display: flex;
            }

            .top-header-card {
                padding: 1rem 1.25rem;
            }

            .top-header-title {
                font-size: 1.15rem;
            }
        }
    </style>
</head>
<body>

    <!-- Mobile Overlay Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <!-- Sidebar Drawer -->
    <div class="sidebar" id="sidebarDrawer">
        <div>
            <!-- Branding Header (Matching Reference UI) -->
            <div class="sidebar-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SMP IT" style="width: 44px; height: 44px; object-fit: contain; margin-bottom: 8px;">
                <span class="sidebar-brand-top">OPERATOR PETUGAS ABSENSI</span>
                <span class="sidebar-brand-title">SMP IT AL-MUTTAQIN</span>
                <span class="sidebar-brand-sub">TAROGONG KALER - GARUT</span>
                <!-- Close Button on Mobile -->
                <button type="button" class="btn btn-sm text-secondary d-lg-none p-1 position-absolute top-0 end-0 m-2" onclick="toggleSidebar()" aria-label="Tutup Menu">
                    <i class="fa-solid fa-xmark fs-4"></i>
                </button>
            </div>

            <ul class="sidebar-menu">
                @if(Auth::check() && Auth::user()->role === 'guru')
                    <!-- MENU WALI KELAS -->
                    <div class="sidebar-heading">MENU WALI KELAS</div>
                    <li class="nav-item">
                        <a href="{{ route('guru.monitoring') }}" class="nav-link {{ request()->routeIs('guru.monitoring') ? 'active' : '' }}">
                            <i class="fa-solid fa-rotate-left"></i> Monitoring Kehadiran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('guru.rekap') }}" class="nav-link {{ request()->routeIs('guru.rekap') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-lines"></i> Rekap Kehadiran Siswa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('guru.siswa.index') }}" class="nav-link {{ request()->routeIs('guru.siswa.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-address-book"></i> Biodata Siswa Binaan
                        </a>
                    </li>
                @else
                    <!-- MENU OPERATOR / ADMIN TU -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-table-columns"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('presensi.scan') }}" class="nav-link {{ request()->routeIs('presensi.scan') ? 'active' : '' }}">
                            <i class="fa-solid fa-qrcode"></i> Scanner QR Code
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.rekap.monitoring') }}" class="nav-link {{ request()->routeIs('admin.rekap.monitoring') ? 'active' : '' }}">
                            <i class="fa-solid fa-list-check"></i> Absensi Siswa (Live)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.rekap.index') }}" class="nav-link {{ request()->routeIs('admin.rekap.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-simple"></i> Rekap Kehadiran Siswa
                        </a>
                    </li>

                    <!-- MASTER DATA -->
                    <div class="sidebar-heading">DATA MASTER</div>
                    <li class="nav-item">
                        <a href="{{ route('admin.siswa.index') }}" class="nav-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-graduate"></i> Data Siswa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.kelas.index') }}" class="nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-school"></i> Data Kelas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.guru.index') }}" class="nav-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-chalkboard-user"></i> Data Guru & Wali
                        </a>
                    </li>

                    <!-- LAPORAN & PENGATURAN -->
                    <div class="sidebar-heading">LAPORAN & SISTEM</div>
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
                @endif
            </ul>
        </div>

        <!-- Sidebar Footer / Direct Instant Logout -->
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST" id="logoutFormSidebar" class="m-0">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar / Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Mobile Top Navbar -->
        <div class="mobile-navbar rounded-3">
            <button class="btn btn-light border p-2 rounded-3 text-dark" onclick="toggleSidebar()" aria-label="Buka Menu Sidebar">
                <i class="fa-solid fa-bars-staggered fs-5"></i>
            </button>
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 28px; height: 28px; object-fit: contain;">
                <span class="fw-bold text-dark fs-6">SMP IT Al-Muttaqin</span>
            </div>
            <div class="user-role-badge py-1 px-2" style="font-size: 0.72rem;">
                {{ Auth::user()->role === 'guru' ? 'GURU' : 'ADMIN' }}
            </div>
        </div>

        <!-- Desktop Top Header Navbar (Minimalist Style) -->
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
                <div class="date-badge">
                    <i class="fa-regular fa-calendar text-muted"></i>
                    <span>{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}</span>
                </div>

                <div class="user-role-badge">
                    <i class="fa-solid fa-circle-user text-danger"></i>
                    <span>USER : {{ strtoupper(Auth::user()->role === 'guru' ? (Auth::user()->name) : 'SUPERADMIN (ADMIN TU)') }}</span>
                </div>
            </div>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-2" role="alert">
                <i class="fa-solid fa-circle-check fs-5 text-success"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-2" role="alert">
                <i class="fa-solid fa-triangle-exclamation fs-5 text-danger"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
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
