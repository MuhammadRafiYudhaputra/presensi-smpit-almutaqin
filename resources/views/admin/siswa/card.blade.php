<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Presensi Siswa - {{ $siswa->nama }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .card-container {
            width: 320px;
            background: linear-gradient(180deg, #0b57d0 0%, #003e9e 100%);
            border-radius: 26px;
            padding: 1.75rem 1.25rem 1.25rem;
            color: #ffffff;
            text-align: center;
            border: 3.5px solid #ffffff;
            box-shadow: 0 20px 45px rgba(11, 87, 208, 0.35);
            position: relative;
            user-select: none;
        }

        /* Top Header */
        .card-title {
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .card-subtitle {
            font-size: 0.72rem;
            font-weight: 800;
            color: #facc15;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 12px;
            display: block;
        }

        .header-divider {
            height: 1px;
            background-color: rgba(255, 255, 255, 0.25);
            margin: 0 auto 16px;
            width: 95%;
        }

        /* QR Code Box */
        .qr-wrapper {
            background-color: #ffffff;
            padding: 14px;
            border-radius: 22px;
            display: inline-block;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            margin-bottom: 8px;
            line-height: 0;
        }

        .qr-wrapper svg, .qr-wrapper img {
            width: 175px !important;
            height: 175px !important;
            display: block;
        }

        .token-text {
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.65);
            margin-bottom: 14px;
            letter-spacing: 0.3px;
        }

        /* Student Info Box */
        .student-info-box {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 18px;
            padding: 14px 12px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            margin-bottom: 14px;
        }

        .student-name {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .class-badge {
            background-color: #facc15;
            color: #000000;
            font-size: 0.68rem;
            font-weight: 800;
            padding: 3px 14px;
            border-radius: 8px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .info-divider {
            height: 1px;
            background-color: rgba(255, 255, 255, 0.2);
            margin: 0 auto 10px;
            width: 90%;
        }

        .nisn-label {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            margin-right: 4px;
        }

        .nisn-value {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        /* Footer */
        .footer-divider {
            height: 1px;
            background-color: rgba(255, 255, 255, 0.2);
            margin: 0 auto 10px;
            width: 95%;
        }

        .footer-text {
            font-size: 0.68rem;
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.4;
        }

        /* Floating Action Buttons */
        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        .btn-action {
            background-color: #0b57d0;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            background-color: #003e9e;
            transform: translateY(-2px);
        }

        @media print {
            .action-buttons {
                display: none !important;
            }
            body {
                background: none !important;
                padding: 0 !important;
            }
            .card-container {
                box-shadow: none !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Print Button -->
    <div class="action-buttons">
        <button onclick="window.print()" class="btn-action">
            <i class="fa-solid fa-print"></i> Cetak Kartu
        </button>
    </div>

    <!-- The Exact ID Card -->
    <div class="card-container">
        <!-- Top Header -->
        <div class="card-title">SMP IT AL-MUTTAQIN</div>
        <div class="card-subtitle">KARTU PRESENSI SISWA</div>
        <div class="header-divider"></div>

        <!-- Center QR Code Box -->
        <div class="qr-wrapper">
            @if(isset($qrSvg) && !empty($qrSvg))
                {!! $qrSvg !!}
            @else
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=175x175&data={{ urlencode($siswa->qr_code_token) }}" alt="QR Code" />
            @endif
        </div>
        <div class="token-text">Token: {{ $siswa->qr_code_token }}</div>

        <!-- Bottom Student Info Box -->
        <div class="student-info-box">
            <div class="student-name">{{ $siswa->nama }}</div>
            <div class="class-badge">KELAS {{ $siswa->kelas->nama_kelas ?? '9B' }}</div>
            <div class="info-divider"></div>
            <div>
                <span class="nisn-label">NISN:</span>
                <span class="nisn-value">{{ $siswa->nisn }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-divider"></div>
        <div class="footer-text">
            SMP IT Al-Muttaqin • Kartu Wajib Dibawa Setiap Hari
        </div>
    </div>

</body>
</html>
