<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Presensi Siswa - {{ $siswa->nama }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1.5rem;
        }

        .id-card {
            width: 380px;
            min-height: 540px;
            background: linear-gradient(145deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            border-radius: 24px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.3);
            color: #fff;
            padding: 1.75rem 1.5rem;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 3px solid #3b82f6;
        }

        .id-card::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 180px;
            height: 180px;
            background: rgba(59, 130, 246, 0.15);
            border-radius: 50%;
        }

        .header-school {
            text-align: center;
            border-bottom: 2px dashed rgba(255, 255, 255, 0.2);
            padding-bottom: 0.85rem;
        }

        .qr-box {
            background: #ffffff;
            padding: 14px;
            border-radius: 18px;
            display: inline-block;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            line-height: 0;
        }

        .qr-box svg, .qr-box img {
            display: block;
            margin: 0 auto;
            max-width: 160px;
            height: auto;
        }

        .print-btn {
            position: fixed;
            top: 25px;
            right: 25px;
            z-index: 100;
        }

        @media print {
            .print-btn { display: none !important; }
            body { 
                background: transparent !important;
                padding: 0 !important;
            }
            .id-card {
                box-shadow: none !important;
                border: 2px solid #000 !important;
            }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn btn-primary btn-lg rounded-pill shadow-lg print-btn px-4 fw-bold">
        <i class="fa-solid fa-print me-2"></i> Cetak Kartu Presensi
    </button>

    <div class="id-card">
        <!-- Header -->
        <div class="header-school">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                <i class="fa-solid fa-table-cells-large text-warning fs-4"></i>
                <h5 class="fw-bold m-0 text-white tracking-wide">SMP IT AL-MUTTAQIN</h5>
            </div>
            <small class="text-info fw-bold" style="letter-spacing: 1px; font-size: 0.75rem;">KARTU PRESENSI SISWA DIGITAL</small>
        </div>

        <!-- QR Code Container -->
        <div class="text-center my-3">
            <div class="qr-box">
                @if(isset($qrSvg) && !empty($qrSvg))
                    {!! $qrSvg !!}
                @else
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($siswa->qr_code_token) }}" alt="QR Code" width="160" height="160" />
                @endif
            </div>
            <div class="mt-2">
                <code class="text-warning fw-bold" style="font-size: 0.82rem;">{{ $siswa->qr_code_token }}</code>
            </div>
        </div>

        <!-- Student Info Box -->
        <div class="bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-10">
            <h5 class="fw-bold text-center mb-1 text-white">{{ $siswa->nama }}</h5>
            <div class="text-center mb-2">
                <span class="badge bg-primary px-3 py-1 rounded-pill">
                    KELAS {{ $siswa->kelas->nama_kelas ?? '-' }}
                </span>
            </div>
            <hr class="my-2 border-white opacity-25">
            <div class="row text-start small">
                <div class="col-6 text-white-50">NISN: <strong class="text-white d-block">{{ $siswa->nisn }}</strong></div>
                <div class="col-6 text-white-50">NIS: <strong class="text-white d-block">{{ $siswa->nis ?? '-' }}</strong></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center pt-2">
            <small class="text-white-50" style="font-size: 0.72rem;">
                <i class="fa-solid fa-circle-exclamation text-warning me-1"></i> Wajib dibawa dan di-scan setiap hari di sekolah
            </small>
        </div>
    </div>

</body>
</html>
