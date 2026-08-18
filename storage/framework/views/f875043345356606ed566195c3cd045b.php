<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SMP IT Al-Muttaqin Presensi</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0F52BA;
            --primary-hover: #083885;
            --accent-color: #00D26A;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #0F52BA 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1.5rem;
        }

        .login-card {
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            width: 100%;
            max-width: 950px;
        }

        .brand-section {
            background: linear-gradient(180deg, #0F52BA 0%, #083885 100%);
            color: #FFFFFF;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .form-section {
            padding: 3rem;
        }

        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 82, 186, 0.4);
        }

        .demo-badge {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px dashed rgba(15, 82, 186, 0.4);
        }

        .demo-badge:hover {
            background-color: #EBF3FF !important;
            transform: scale(1.02);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="row g-0">
            <!-- Left Branding -->
            <div class="col-lg-5 brand-section d-none d-lg-flex">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="bg-white text-primary p-2 rounded-3 shadow-sm">
                            <i class="fa-solid fa-qrcode fs-3"></i>
                        </div>
                        <h4 class="fw-bold m-0 text-white">SMP IT Al-Muttaqin Tarogong Kaler</h4>
                    </div>
                    <h2 class="fw-bold text-white mb-3">Sistem Monitoring Presensi Siswa</h2>
                </div>
                <div class="mt-auto">
                    <small class="text-white-50"><i class="fa-solid fa-shield-halved me-1"></i> Tarogong Kaler - Garut &copy; <?php echo e(date('Y')); ?></small>
                </div>
            </div>

            <!-- Right Form -->
            <div class="col-lg-7 form-section">
                <div class="mb-4">
                    <h3 class="fw-bold mb-1 text-dark">Selamat Datang 👋</h3>
                    <p class="text-muted small">Silakan masuk dengan email & password terdaftar Anda</p>
                </div>

                <?php if(session('error')): ?>
                    <div class="alert alert-danger rounded-3 border-0 small mb-4">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> <?php echo e(session('error')); ?>

                    </div>
                <?php endif; ?>

                <?php if(session('success')): ?>
                    <div class="alert alert-success rounded-3 border-0 small mb-4">
                        <i class="fa-solid fa-circle-check me-1"></i> <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('login')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                            <input type="email" id="emailInput" name="email" class="form-control bg-light border-start-0 rounded-end-3" placeholder="email@almuttaqin.sch.id" value="<?php echo e(old('email')); ?>" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" id="passwordInput" name="password" class="form-control bg-light border-start-0 rounded-end-3" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
                        </div>
                        <a href="<?php echo e(route('forgot-password')); ?>" class="small text-primary text-decoration-none fw-semibold">
                            <i class="fa-solid fa-key me-1"></i> Lupa Password?
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary-custom text-white w-100 mb-4">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk ke Sistem
                    </button>
                </form>

                <!-- Quick Login Demo Buttons -->
                <div class="p-3 bg-light rounded-4 border">
                    <small class="fw-bold text-muted d-block mb-2"><i class="fa-solid fa-key text-warning me-1"></i> Demo Akun Quick Login:</small>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-2 bg-white rounded-3 demo-badge text-center" onclick="fillCredentials('admin@almuttaqin.sch.id', 'password')">
                                <span class="badge bg-primary d-block mb-1">Admin TU</span>
                                <small class="text-dark d-block fw-semibold" style="font-size: 11px;">admin@almuttaqin.sch.id</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-white rounded-3 demo-badge text-center" onclick="fillCredentials('guru@almuttaqin.sch.id', 'password')">
                                <span class="badge bg-success d-block mb-1">Wali Kelas</span>
                                <small class="text-dark d-block fw-semibold" style="font-size: 11px;">guru@almuttaqin.sch.id</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillCredentials(email, password) {
            document.getElementById('emailInput').value = email;
            document.getElementById('passwordInput').value = password;
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/auth/login.blade.php ENDPATH**/ ?>