<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SMP IT Al-Muttaqin Presensi</title>
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

        .forgot-card {
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            padding: 2.5rem;
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
    </style>
</head>
<body>

    <div class="forgot-card">
        <div class="text-center mb-4">
            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-inline-flex mb-3">
                <i class="fa-solid fa-key fs-3"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">Lupa Password Akun?</h4>
            <p class="text-muted small">Masukkan email terdaftar Anda (Admin/Wali Kelas) untuk melakukan pemulihan password</p>
        </div>

        <?php if(session('error')): ?>
            <div class="alert alert-danger rounded-3 border-0 small mb-4">
                <i class="fa-solid fa-circle-exclamation me-1"></i> <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('forgot-password.process')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <label class="form-label fw-semibold small">Email Terdaftar</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
                        <i class="fa-regular fa-envelope"></i>
                    </span>
                    <input type="email" name="email" class="form-control bg-light border-start-0 rounded-end-3" placeholder="contoh: guru@almuttaqin.sch.id" value="<?php echo e(old('email')); ?>" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn btn-primary-custom text-white w-100 mb-3">
                <i class="fa-solid fa-paper-plane me-2"></i> Verifikasi Email & Lanjut
            </button>

            <div class="text-center">
                <a href="<?php echo e(route('login')); ?>" class="text-muted small text-decoration-none fw-semibold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Halaman Login
                </a>
            </div>
        </form>
    </div>

</body>
</html>
<?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/auth/forgot-password.blade.php ENDPATH**/ ?>