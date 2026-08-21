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
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            background-image: 
                radial-gradient(at 15% 15%, rgba(37, 99, 235, 0.1) 0px, transparent 50%),
                radial-gradient(at 85% 85%, rgba(37, 99, 235, 0.08) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #1e293b;
        }

        .login-wrapper {
            width: 100%;
            max-width: 940px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1px solid #e2e8f0;
        }

        @media (min-width: 768px) {
            .login-wrapper {
                flex-direction: row;
                min-height: 560px;
            }
        }

        /* Left Branding Panel */
        .brand-panel {
            flex: 1;
            padding: 3rem 2.5rem;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            position: relative;
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            bottom: -30px;
            right: -30px;
            width: 160px;
            height: 160px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
            pointer-events: none;
        }

        .brand-logo-box {
            width: 54px;
            height: 54px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .brand-title {
            font-size: 1.85rem;
            font-weight: 800;
            line-height: 1.25;
            color: #ffffff;
            margin-top: 1.75rem;
            margin-bottom: 0.75rem;
            letter-spacing: -0.5px;
        }

        .brand-desc {
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.5;
            margin-bottom: 1.75rem;
        }

        .brand-features {
            list-style: none;
            padding: 0;
            margin: 0 0 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }

        .brand-features li {
            font-size: 0.82rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .brand-features li i {
            width: 22px;
            height: 22px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }

        .brand-footer {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.75);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* Right Form Card */
        .form-card {
            flex: 1.15;
            background: #ffffff;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
            letter-spacing: -0.3px;
        }

        .form-subtitle {
            font-size: 0.84rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        .custom-alert {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 0.65rem 0.85rem;
            font-size: 0.82rem;
            color: #1e40af;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .input-group-custom {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            display: flex;
            align-items: center;
            padding: 0.2rem 0.85rem;
            transition: all 0.2s ease;
        }

        .input-group-custom:focus-within {
            border-color: #2563eb;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .input-group-custom input {
            border: none;
            background: transparent;
            width: 100%;
            padding: 0.55rem 0.5rem;
            font-size: 0.88rem;
            color: #1e293b;
            outline: none;
        }

        .input-group-custom i {
            color: #64748b;
            font-size: 0.9rem;
        }

        .btn-submit {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 700;
            font-size: 0.92rem;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
        }

        .btn-submit:hover {
            background-color: #1d4ed8;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
            transform: translateY(-1px);
        }

        /* Demo Accounts Buttons */
        .demo-box {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f1f5f9;
        }

        .demo-title {
            font-size: 0.78rem;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 0.65rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-demo {
            border-radius: 10px;
            padding: 0.55rem 0.75rem;
            text-align: left;
            flex: 1;
            transition: all 0.15s ease;
            cursor: pointer;
            border: 1px solid transparent;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .btn-demo-admin {
            background-color: #eff6ff;
            border-color: #bfdbfe;
            color: #1e40af;
        }

        .btn-demo-admin:hover {
            background-color: #dbeafe;
            border-color: #93c5fd;
        }

        .btn-demo-guru {
            background-color: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }

        .btn-demo-guru:hover {
            background-color: #dcfce7;
            border-color: #86efac;
        }

        .demo-badge-title {
            font-weight: 800;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .demo-badge-sub {
            font-size: 0.72rem;
            opacity: 0.85;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <!-- Left Branding Panel -->
    <div class="brand-panel">
        <div>
            <div class="d-flex align-items-center gap-3">
                <div class="brand-logo-box">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SMP IT Al-Muttaqin" style="width: 36px; height: 36px; object-fit: contain;">
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-white" style="letter-spacing: 0.5px;">SMP IT AL-MUTTAQIN</h6>
                    <small style="color: rgba(255, 255, 255, 0.8); font-size: 0.75rem;">Tarogong Kaler - Garut</small>
                </div>
            </div>

            <div class="brand-title">
                Sistem Monitoring Presensi Siswa
            </div>
        </div>

        <div class="brand-footer">
            <i class="fa-solid fa-shield-halved me-1"></i> Sistem Informasi Sekolah &copy; 2026
        </div>
    </div>

    <!-- Right Login Card -->
    <div class="form-card">
        <h2 class="form-title">Selamat Datang 👋</h2>
        <p class="form-subtitle">Silakan masuk dengan email &amp; kata sandi akun Anda</p>

        @if(session('success'))
            <div class="custom-alert">
                <i class="fa-solid fa-circle-check text-success fs-6"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="custom-alert border-danger bg-danger bg-opacity-10 text-danger">
                <i class="fa-solid fa-triangle-exclamation fs-6"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf

            <!-- Email Input -->
            <div class="mb-3">
                <label class="form-label">Alamat Email</label>
                <div class="input-group-custom">
                    <i class="fa-regular fa-envelope text-primary me-1"></i>
                    <input type="email" name="email" id="email" placeholder="nama@almuttaqin.sch.id" value="{{ old('email', 'admin@almutaqin.sch.id') }}" required autofocus>
                </div>
            </div>

            <!-- Password Input with Toggle Eye -->
            <div class="mb-3">
                <label class="form-label">Kata Sandi</label>
                <div class="input-group-custom">
                    <i class="fa-solid fa-lock text-primary me-1"></i>
                    <input type="password" name="password" id="password" placeholder="••••••••" value="admin123" required>
                    <i class="fa-regular fa-eye text-muted" id="togglePasswordIcon" style="cursor: pointer;" onclick="togglePasswordVisibility()" title="Lihat/Sembunyikan Kata Sandi"></i>
                </div>
            </div>

            <!-- Remember Me & Forgot Password Link -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalForgotPassword" class="text-decoration-none small text-primary fw-bold" style="font-size: 0.8rem;">
                    <i class="fa-solid fa-key me-1"></i> Lupa Password?
                </a>
            </div>

            <!-- Submit Button (Sesuai Gaya Desain Website) -->
            <button type="submit" class="btn btn-primary text-uppercase fw-bold rounded-pill py-2.5 w-100 shadow-sm d-flex align-items-center justify-content-center gap-2 mb-3">
                <i class="fa-solid fa-right-to-bracket fs-6"></i> Masuk ke Sistem
            </button>

            <!-- Quick Demo Login -->
            <div class="demo-box">
                <div class="demo-title">
                    <i class="fa-solid fa-wand-magic-sparkles text-primary"></i> Akses Demo Cepat:
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-3 p-2.5 flex-fill text-start shadow-none" onclick="fillCreds('admin@almutaqin.sch.id', 'admin123')">
                        <div class="fw-bold small d-flex align-items-center gap-1.5 mb-0.5">
                            <i class="fa-solid fa-user-shield"></i> Admin TU
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.72rem;">admin@almutaqin.sch.id</small>
                    </button>
                    <button type="button" class="btn btn-success bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-3 p-2.5 flex-fill text-start shadow-none" onclick="fillCreds('guru@almutaqin.sch.id', '12345678')">
                        <div class="fw-bold small d-flex align-items-center gap-1.5 mb-0.5">
                            <i class="fa-solid fa-chalkboard-user"></i> Wali Kelas
                        </div>
                        <small class="text-muted d-block" style="font-size: 0.72rem;">guru@almutaqin.sch.id</small>
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
                    <i class="fa-solid fa-key text-primary me-2"></i> Pemulihan Password Akun
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
                            <i class="fa-regular fa-envelope text-primary me-1"></i>
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

function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePasswordIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>
