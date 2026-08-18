<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>SMP IT Al-Muttaqin - Presensi Siswa</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Pro / Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --sidebar-bg: #0b1329;
            --sidebar-hover: rgba(255, 255, 255, 0.06);
            --sidebar-active: #1d4ed8;
            --primary-color: #1a56db;
            --body-bg: #f3f6fa;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--body-bg);
            color: #1e293b;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
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
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar-brand-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-brand-icon {
            color: #facc15;
            font-size: 1.5rem;
        }

        .sidebar-brand-text {
            color: #ffffff;
            font-weight: 800;
            font-size: 1.15rem;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .sidebar-heading {
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            padding: 1.25rem 1.25rem 0.5rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0.5rem 0.75rem;
            margin: 0;
        }

        .sidebar-menu .nav-item {
            margin-bottom: 4px;
        }

        .sidebar-menu .nav-link {
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 0.65rem 0.9rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .sidebar-menu .nav-link i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-menu .nav-link:hover {
            color: #ffffff;
            background: var(--sidebar-hover);
        }

        .sidebar-menu .nav-link.active {
            color: #ffffff;
            background: var(--primary-color);
            box-shadow: 0 4px 12px rgba(26, 86, 219, 0.35);
        }

        .sidebar-menu .nav-link.scanner-link {
            color: #facc15;
        }
        .sidebar-menu .nav-link.scanner-link:hover {
            background: rgba(250, 204, 21, 0.1);
        }

        .sidebar-footer {
            padding: 1rem 0.85rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar-footer .logout-btn {
            color: #f87171;
            font-weight: 600;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.65rem 0.9rem;
            text-decoration: none;
            border-radius: 12px;
            transition: background 0.2s ease;
        }
        .sidebar-footer .logout-btn:hover {
            background: rgba(248, 113, 113, 0.1);
        }

        /* Main Content Container */
        .main-content {
            margin-left: 260px;
            padding: 1.5rem 2rem;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .top-header-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 1.25rem 1.75rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
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
            margin-bottom: 2px;
        }

        .top-header-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0;
        }

        .user-role-badge {
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 50rem;
            padding: 0.45rem 1rem;
            font-weight: 700;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .date-badge {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
            border-radius: 50rem;
            padding: 0.45rem 1rem;
            font-weight: 600;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .card-custom {
            background: #ffffff;
            border-radius: 18px;
            border: none;
            box-shadow: 0 2px 14px rgba(0, 0, 0, 0.03);
        }

        /* Mobile Overlay Backdrop */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-backdrop.show {
            display: block;
            opacity: 1;
        }

        /* Mobile Header Bar */
        .mobile-navbar {
            display: none;
            background: #ffffff;
            border-radius: 16px;
            padding: 0.85rem 1.25rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            margin-bottom: 1rem;
            align-items: center;
            justify-content: space-between;
        }

        /* Responsive Breakpoints for Mobile / Tablets */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            }

            .sidebar.show {
                transform: translateX(0);
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

            .user-role-badge, .date-badge {
                font-size: 0.75rem;
                padding: 0.35rem 0.75rem;
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
            <div class="sidebar-brand">
                <div class="sidebar-brand-left">
                    <i class="fa-solid fa-table-cells-large sidebar-brand-icon"></i>
                    <h5 class="sidebar-brand-text">SMP IT Al-Muttaqin</h5>
                </div>
                <!-- Close Button on Mobile -->
                <button type="button" class="btn btn-sm text-white-50 d-lg-none p-1" onclick="toggleSidebar()" aria-label="Tutup Menu">
                    <i class="fa-solid fa-xmark fs-4"></i>
                </button>
            </div>

            <ul class="sidebar-menu">
                @if(Auth::check() && Auth::user()->role === 'guru')
                    <!-- MENU WALI KELAS (HANYA 2 FITUR) -->
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
                @else
                    <!-- MENU ADMIN TU -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-pie"></i> Dashboard Admin
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('presensi.scan') }}" class="nav-link {{ request()->routeIs('presensi.scan') ? 'active' : 'scanner-link' }}">
                            <i class="fa-solid fa-table-cells-large"></i> Scanner QR Code
                        </a>
                    </li>

                    <!-- MASTER DATA -->
                    <div class="sidebar-heading">MASTER DATA</div>
                    <li class="nav-item">
                        <a href="{{ route('admin.siswa.index') }}" class="nav-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-graduate"></i> Data Siswa & QR
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.guru.index') }}" class="nav-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-plus"></i> Data Wali Kelas
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
                    <li class="nav-item">
                        <a href="{{ route('admin.jampresensi.index') }}" class="nav-link {{ request()->routeIs('admin.jampresensi.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-clock text-warning"></i> Jam Presensi
                        </a>
                    </li>

                    <!-- MONITORING & WA -->
                    <div class="sidebar-heading">MONITORING & WA</div>
                    <li class="nav-item">
                        <a href="{{ route('admin.rekap.monitoring') }}" class="nav-link {{ request()->routeIs('admin.rekap.monitoring') ? 'active' : '' }}">
                            <i class="fa-solid fa-rotate-left"></i> Live Monitoring
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.rekap.index') }}" class="nav-link {{ request()->routeIs('admin.rekap.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-lines"></i> Rekap Kehadiran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.fonnte.index') }}" class="nav-link {{ request()->routeIs('admin.fonnte.*') ? 'active' : '' }}">
                            <i class="fa-brands fa-whatsapp text-success"></i> Fonnte WA API
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="sidebar-footer">
            <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
            <a href="javascript:void(0)" class="logout-btn" onclick="document.getElementById('logoutForm').submit();">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout / Keluar
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Mobile Navbar (Only Visible on Mobile & Tablet) -->
        <div class="mobile-navbar">
            <button type="button" class="btn btn-light border rounded-pill px-3 py-2 shadow-sm d-flex align-items-center gap-2" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars text-primary fs-5"></i>
                <span class="fw-bold small text-dark">Menu Navigasi</span>
            </button>
            <div class="d-flex align-items-center gap-2">
                <span class="user-role-badge">
                    <i class="fa-solid fa-user"></i> {{ Auth::user()->name ?? 'User' }}
                </span>
            </div>
        </div>

        <!-- Top Header Card -->
        <div class="top-header-card">
            <div>
                <h4 class="top-header-title">Sistem Presensi Siswa</h4>
                <p class="top-header-subtitle">SMP IT Al-Muttaqin Tarogong Kaler</p>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="date-badge">
                    <i class="fa-regular fa-calendar text-primary"></i> {{ date('d M Y') }}
                </span>
                <span class="user-role-badge">
                    <i class="fa-solid fa-user"></i> {{ Auth::user()->name ?? 'Admin TU SMP IT Al-Muttaqin' }} ({{ strtoupper(Auth::user()->role ?? 'ADMIN TU') }})
                </span>
            </div>
        </div>

        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm auto-dismiss-alert mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm auto-dismiss-alert mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarDrawer');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('show');
            }
        }

        // Auto dismiss flash alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.auto-dismiss-alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 4000);
    </script>
    @yield('scripts')
</body>
</html>
