<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Pelajar - {{ $siswa->nama }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .id-card {
            width: 380px;
            height: 580px;
            background: linear-gradient(180deg, #0F52BA 0%, #083885 100%);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            color: #fff;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 4px solid #FFFFFF;
        }

        .id-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .header-school {
            text-align: center;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 0.75rem;
        }

        .qr-box {
            background: #FFFFFF;
            padding: 12px;
            border-radius: 16px;
            display: inline-block;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
        }

        @media print {
            .print-btn { display: none; }
            body { background: white; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn btn-dark btn-lg rounded-pill shadow print-btn">
        <i class="fa-solid fa-print"></i> Cetak Kartu Pelajar
    </button>

    <div class="id-card">
        <!-- Header -->
        <div class="header-school">
            <h5 class="fw-extrabold m-0 text-uppercase tracking-wide">SMP IT AL-MUTAQIN</h5>
            <small class="text-warning fw-semibold">KARTU PRESENSI SISWA</small>
        </div>

        <!-- QR Code Container -->
        <div class="text-center my-3">
            <div class="qr-box">
                {!! $qrSvg !!}
            </div>
            <small class="d-block text-white-50 mt-2">Token: {{ $siswa->qr_code_token }}</small>
        </div>

        <!-- Student Info -->
        <div class="bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur">
            <h5 class="fw-bold text-center mb-1 text-white">{{ $siswa->nama }}</h5>
            <div class="text-center mb-2">
                <span class="badge bg-warning text-dark px-3 py-1">KELAS {{ $siswa->kelas->nama_kelas ?? '-' }}</span>
            </div>
            <hr class="my-2 border-white opacity-25">
            <div class="row text-start fs-7">
                <div class="col-6"><span class="text-white-50">NISN:</span> <strong>{{ $siswa->nisn }}</strong></div>
                <div class="col-6"><span class="text-white-50">NIS:</span> <strong>{{ $siswa->nis ?? '-' }}</strong></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center border-top border-white-10 pt-2">
            <small class="text-white-50 fs-8">SMP IT Al-Mutaqin • Kartu Wajib Dibawa Setiap Hari</small>
        </div>
    </div>

</body>
</html>
