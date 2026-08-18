<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal - SMP IT Al-Muttaqin</title>
    <!-- Favicon & Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #1d4ed8;
            --dark-sidebar: #0e1726;
            --accent-yellow: #facc15;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0e1726 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #334155;
        }

        .login-card {
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-header {
            background: #0e1726;
            padding: 2.5rem 2rem 2rem;
            text-align: center;
            color: #ffffff;
            position: relative;
        }

        .login-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #1d4ed8, #facc15, #10b981);
        }

        .brand-logo-icon {
            font-size: 2.2rem;
            color: #facc15;
            margin-bottom: 0.75rem;
            display: inline-block;
        }

        .form-control {
            border-radius: 12px;
            padding: 0.8rem 1rem;
            border: 1.5px solid #e2e8f0;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 4px rgba(29, 78, 216, 0.12);
        }

        .btn-login {
            background: #1d4ed8;
            border: none;
            border-radius: 14px;
            padding: 0.85rem 1.5rem;
            font-weight: 700;
            font-size: 1rem;
            color: #ffffff;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            background: #1e40af;
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(29, 78, 216, 0.3);
            color: #ffffff;
        }

        .quick-role-btn {
            font-size: 0.8rem;
            border-radius: 10px;
            padding: 0.45rem 0.75rem;
            font-weight: 600;
            transition: all 0.15s ease;
        }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Header -->
    <div class="login-header">
        <i class="fa-solid fa-table-cells-large brand-logo-icon"></i>
        <h4 class="fw-bold mb-1 text-white">SMP IT Al-Muttaqin</h4>
        <small class="text-secondary d-block" style="font-size: 0.82rem;">Sistem Presensi Siswa & WhatsApp Gateway</small>
    </div>

    <!-- Body Form -->
    <div class="p-4 p-md-5">
        @if(session('success'))
            <div class="alert alert-success border-0 rounded-3 small mb-3">
                <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 small mb-3">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label fw-bold text-dark small mb-1">Email Pengguna</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" name="email" id="inputEmail" class="form-control border-start-0" placeholder="admin@almutaqin.sch.id" value="{{ old('email', 'admin@almutaqin.sch.id') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-dark small mb-1">Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="password" id="inputPassword" class="form-control border-start-0 border-end-0" placeholder="••••••••" value="admin123" required>
                    <button class="btn btn-outline-secondary border-start-0 bg-white" type="button" onclick="togglePasswordVisibility()">
                        <i class="fa-regular fa-eye text-muted" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label small text-muted cursor-pointer" for="remember">
                        Ingat Saya
                    </label>
                </div>
                <a href="{{ route('presensi.scan') }}" class="small text-decoration-none fw-semibold text-primary">
                    <i class="fa-solid fa-qrcode me-1"></i> Buka Scanner
                </a>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-4 shadow-sm">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk ke Portal
            </button>

            <!-- Quick Auto-Fill Demo Credentials -->
            <div class="bg-light p-3 rounded-4 border">
                <small class="text-muted fw-bold d-block mb-2 text-center" style="font-size: 0.75rem;">AKSES CEPAT (DEMO):</small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary quick-role-btn flex-fill" onclick="fillCreds('admin@almutaqin.sch.id', 'admin123')">
                        <i class="fa-solid fa-user-shield me-1"></i> Admin TU
                    </button>
                    <button type="button" class="btn btn-outline-success quick-role-btn flex-fill" onclick="fillCreds('guru@almutaqin.sch.id', '12345678')">
                        <i class="fa-solid fa-chalkboard-user me-1"></i> Wali Kelas
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    const input = document.getElementById('inputPassword');
    const icon = document.getElementById('toggleIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function fillCreds(email, password) {
    document.getElementById('inputEmail').value = email;
    document.getElementById('inputPassword').value = password;
}
</script>
</body>
</html>
