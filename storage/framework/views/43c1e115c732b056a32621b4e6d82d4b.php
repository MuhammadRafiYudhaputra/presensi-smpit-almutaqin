<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>SMP IT Al-Muttaqin - Presensi Siswa</title>
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
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overscroll-behavior: auto;
            -webkit-overflow-scrolling: touch;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .sidebar-brand {
            padding: 0 1.25rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0.75rem 0.75rem;
            margin: 0;
            flex-grow: 1;
        }

        .sidebar-menu .nav-item {
            margin-bottom: 0.25rem;
        }

        .sidebar-menu .nav-link {
            color: #94A3B8;
            padding: 0.65rem 0.9rem;
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
            transition: box-shadow 0.2s ease;
        }

        .card-custom:hover {
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
            <h5 class="fw-bold m-0 text-white"><i class="fa-solid fa-qrcode text-warning me-2"></i>SMP IT Al-Muttaqin</h5>
        </div>
        <ul class="sidebar-menu">
            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->user()->role === 'admin'): ?>
                    <!-- ADMIN MENU -->
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                            <i class="fa-solid fa-chart-pie"></i> Dashboard Admin
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('presensi.scan')); ?>" class="nav-link text-warning <?php echo e(request()->routeIs('presensi.scan') ? 'active' : ''); ?>">
                            <i class="fa-solid fa-qrcode"></i> Scanner QR Code
                        </a>
                    </li>
                    
                    <li class="px-3 text-uppercase fs-7 text-secondary mt-3 mb-1 fw-bold">Master Data</li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.siswa.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.siswa.*') ? 'active' : ''); ?>">
                            <i class="fa-solid fa-user-graduate"></i> Data Siswa & QR
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.guru.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.guru.*') ? 'active' : ''); ?>">
                            <i class="fa-solid fa-chalkboard-user"></i> Data Wali Kelas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.kelas.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.kelas.*') ? 'active' : ''); ?>">
                            <i class="fa-solid fa-school"></i> Data Kelas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.orangtua.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.orangtua.*') ? 'active' : ''); ?>">
                            <i class="fa-solid fa-users"></i> Data Orang Tua
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.jampresensi.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.jampresensi.*') ? 'active' : ''); ?>">
                            <i class="fa-solid fa-clock text-warning"></i> Jam Presensi
                        </a>
                    </li>

                    <li class="px-3 text-uppercase fs-7 text-secondary mt-3 mb-1 fw-bold">Monitoring & WA</li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.rekap.monitoring')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.rekap.monitoring') ? 'active' : ''); ?>">
                            <i class="fa-solid fa-clock-rotate-left"></i> Live Monitoring
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.rekap.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.rekap.index') ? 'active' : ''); ?>">
                            <i class="fa-solid fa-clipboard-user"></i> Rekap Kehadiran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.fonnte.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.fonnte.*') ? 'active' : ''); ?>">
                            <i class="fa-brands fa-whatsapp text-success"></i> Fonnte WA API
                        </a>
                    </li>
                <?php else: ?>
                    <!-- WALI KELAS MENU (2 FITUR UTAMA: MONITORING & REKAP) -->
                    <li class="px-3 text-uppercase fs-7 text-secondary mt-2 mb-1 fw-bold">Menu Wali Kelas</li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('guru.monitoring')); ?>" class="nav-link <?php echo e(request()->routeIs('guru.monitoring') ? 'active' : ''); ?>">
                            <i class="fa-solid fa-clock-rotate-left"></i> Monitoring Kehadiran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('guru.rekap')); ?>" class="nav-link <?php echo e(request()->routeIs('guru.rekap') ? 'active' : ''); ?>">
                            <i class="fa-solid fa-clipboard-user"></i> Rekap Kehadiran Siswa
                        </a>
                    </li>
                <?php endif; ?>

                <!-- LOGOUT BUTTON DI SIDEBAR MENU -->
                <li class="nav-item mt-4 pt-3 border-top border-secondary border-opacity-25">
                    <form action="<?php echo e(route('logout')); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="nav-link text-danger bg-transparent border-0 w-100 text-start d-flex align-items-center gap-2">
                            <i class="fa-solid fa-right-from-bracket text-danger"></i> Logout / Keluar
                        </button>
                    </form>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <div class="top-header">
            <div>
                <h5 class="fw-bold mb-0">Sistem Presensi Siswa</h5>
                <small class="text-muted">SMP IT Al-Muttaqin Tarogong Kaler</small>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-light text-dark p-2 border">
                    <i class="fa-regular fa-calendar-check text-primary me-1"></i> <?php echo e(date('d M Y')); ?>

                </span>

                <?php if(auth()->guard()->check()): ?>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary border p-2">
                            <i class="fa-solid fa-user me-1"></i> <?php echo e(auth()->user()->name); ?> (<?php echo e(auth()->user()->role === 'guru' ? 'WALI KELAS' : 'ADMIN TU'); ?>)
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Flash Messages Global -->
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> <?php echo nl2br(e(session('success'))); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo nl2br(e(session('error'))); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Auto-dismiss Flash Alerts setelah 4 detik secara otomatis -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    // Animasi fade out halus dan naik ke atas
                    alert.style.transition = 'opacity 0.6s ease, transform 0.6s ease, margin 0.6s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(function() {
                        try {
                            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                            if (bsAlert) bsAlert.close();
                        } catch (e) {
                            alert.remove();
                        }
                    }, 600);
                }, 4000); // Durasi 4 detik sebelum otomatis menghilang
            });
        });
    </script>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/layouts/app.blade.php ENDPATH**/ ?>