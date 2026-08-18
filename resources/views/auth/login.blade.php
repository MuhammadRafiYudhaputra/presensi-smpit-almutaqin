<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Monitoring Presensi Siswa SMP IT Al-Muttaqin</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at center, #1e293b 0%, #0f172a 60%, #090d16 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-wrapper {
            width: 100%;
            max-width: 960px;
            background: #2563eb;
            border-radius: 28px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        @media (min-width: 768px) {
            .login-wrapper {
                flex-direction: row;
                min-height: 580px;
            }
        }

        /* Left Branding Panel */
        .brand-panel {
            flex: 1;
            padding: 3.5rem 3rem;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(145deg, #1d4ed8 0%, #2563eb 100%);
            position: relative;
        }

        .brand-header-icon {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #ffffff;
        }

        .brand-title {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1.25;
            color: #ffffff;
            margin-top: 2.5rem;
            margin-bottom: 2rem;
        }

        .brand-footer {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Right Form Card */
        .form-card {
            flex: 1.15;
            background: #ffffff;
            border-radius: 28px;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.05);
        }

        .form-title {
            font-size: 1.7rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .form-subtitle {
            font-size: 0.88rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        .custom-alert {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            color: #334155;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label {
            font-size: 0.88rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .input-group-custom {
            background-color: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            display: flex;
            align-items: center;
            padding: 0.2rem 0.85rem;
            transition: all 0.2s ease;
        }

        .input-group-custom:focus-within {
            border-color: #2563eb;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .input-group-custom input {
            border: none;
            background: transparent;
            width: 100%;
            padding: 0.65rem 0.5rem;
            font-size: 0.95rem;
            color: #1e293b;
            outline: none;
        }

        .input-group-custom i {
            color: #64748b;
            font-size: 0.95rem;
        }

        .btn-submit {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 14px;
            padding: 0.85rem;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-submit:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
        }

        /* Demo Accounts Buttons */
        .demo-box {
            margin-top: 1.5rem;
        }

        .demo-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-demo-admin {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 0.6rem 0.75rem;
            text-align: center;
            flex: 1;
            transition: all 0.15s ease;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-demo-admin:hover {
            background-color: #1d4ed8;
            color: #ffffff;
        }

        .btn-demo-guru {
            background-color: #065f46;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 0.6rem 0.75rem;
            text-align: center;
            flex: 1;
            transition: all 0.15s ease;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-demo-guru:hover {
            background-color: #047857;
            color: #ffffff;
        }

        .demo-badge-title {
            font-weight: 800;
            font-size: 0.82rem;
            display: block;
            margin-bottom: 2px;
        }

        .demo-badge-sub {
            font-size: 0.72rem;
            opacity: 0.9;
            display: block;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <!-- Left Branding Panel -->
    <div class="brand-panel">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SMP IT Al-Muttaqin" style="width: 48px; height: 48px; object-fit: contain; filter: drop-shadow(0 2px 8px rgba(0,0,0,0.2));">
            <div>
                <h5 class="fw-bold mb-0 text-white">SMP IT Al-Muttaqin</h5>
                <small style="color: rgba(255, 255, 255, 0.8);">Tarogong Kaler</small>
            </div>
        </div>

        <div class="brand-title">
            Sistem Monitoring<br>Presensi Siswa
        </div>

        <div class="brand-footer">
            <i class="fa-solid fa-location-dot me-1"></i> Tarogong Kaler - Garut &copy; 2026
        </div>
    </div>

    <!-- Right Login Card -->
    <div class="form-card">
        <h2 class="form-title">Selamat Datang 👋</h2>
        <p class="form-subtitle">Silakan masuk dengan email & password terdaftar Anda</p>

        @if(session('success'))
            <div class="custom-alert">
                <i class="fa-solid fa-circle-check text-success"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="custom-alert border-danger bg-danger bg-opacity-10 text-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf

            <!-- Email Input -->
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <div class="input-group-custom">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="email" id="email" placeholder="email@almuttaqin.sch.id" value="{{ old('email', 'admin@almutaqin.sch.id') }}" required autofocus>
                </div>
            </div>

            <!-- Password Input -->
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group-custom">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="••••••••" value="admin123" required>
                </div>
            </div>

            <!-- Remember Me & Forgot Password Link -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label text-muted small cursor-pointer" for="remember">
                        Ingat Saya
                    </label>
                </div>
                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalForgotPassword" class="text-decoration-none small text-primary fw-bold">
                    <i class="fa-solid fa-key me-1"></i> Lupa Password?
                </a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Sistem
            </button>

            <!-- Quick Demo Login -->
            <div class="demo-box">
                <div class="demo-title">
                    <i class="fa-solid fa-key text-warning"></i> Demo Akun Quick Login:
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn-demo-admin" onclick="fillCreds('admin@almutaqin.sch.id', 'admin123')">
                        <span class="demo-badge-title">Admin TU</span>
                        <span class="demo-badge-sub">admin@almutaqin.sch.id</span>
                    </button>
                    <button type="button" class="btn-demo-guru" onclick="fillCreds('guru@almutaqin.sch.id', '12345678')">
                        <span class="demo-badge-title">Wali Kelas</span>
                        <span class="demo-badge-sub">guru@almutaqin.sch.id</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Lupa Password -->
<div class="modal fade" id="modalForgotPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg p-2">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark d-flex align-items-center">
                    <i class="fa-solid fa-key text-warning me-2"></i> Pemulihan Password Akun
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('forgot.password') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Masukkan alamat email Anda yang terdaftar pada sistem. Password Anda akan di-reset ke kata sandi standar sistem secara otomatis.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Email Akun Terdaftar</label>
                        <div class="input-group-custom">
                            <i class="fa-regular fa-envelope"></i>
                            <input type="email" name="email" placeholder="admin@almutaqin.sch.id" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 flex-column gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2 shadow-sm">
                        <i class="fa-solid fa-rotate-right me-1"></i> Reset Password Saya
                    </button>
                    <a href="https://wa.me/6281234567890?text=Assalamu'alaikum%20Admin%20SMP%20IT%20Al-Muttaqin,%20saya%20membutuhkan%20bantuan%20reset%20password%20akun%20portal%20presensi." target="_blank" class="btn btn-outline-success rounded-pill w-100 fw-semibold py-2">
                        <i class="fa-brands fa-whatsapp me-1"></i> Hubungi Admin TU via WhatsApp
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function fillCreds(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>

</body>
</html>
