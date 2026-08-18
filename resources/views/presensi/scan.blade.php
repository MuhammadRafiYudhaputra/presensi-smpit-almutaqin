<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kios Tap Presensi - SMP IT Al-Mutaqin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-glow: #00E676;
            --warning-glow: #FFD600;
            --danger-glow: #FF1744;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top center, #1E293B 0%, #0F172A 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            overflow: hidden;
            user-select: none;
        }

        .kiosk-container {
            width: 100%;
            max-width: 900px;
        }

        .tap-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            padding: 3rem 2rem;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Pulsing Tap Zone Animation */
        .tap-target-zone {
            width: 240px;
            height: 240px;
            margin: 1.5rem auto;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 230, 118, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
            border: 3px dashed var(--primary-glow);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            animation: pulseGlow 2s infinite ease-in-out;
            position: relative;
        }

        @keyframes pulseGlow {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 230, 118, 0.4);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 30px 12px rgba(0, 230, 118, 0.2);
                transform: scale(1.03);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 230, 118, 0.4);
                transform: scale(1);
            }
        }

        .laser-line {
            width: 80%;
            height: 3px;
            background: #00E676;
            box-shadow: 0 0 15px #00E676;
            position: absolute;
            animation: scanLaser 2s infinite alternate ease-in-out;
        }

        @keyframes scanLaser {
            0% { top: 20%; }
            100% { top: 80%; }
        }

        .status-display {
            border-radius: 20px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .status-idle {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .status-success {
            background: rgba(0, 230, 118, 0.15);
            border: 2px solid #00E676;
            color: #69F0AE;
            transform: scale(1.02);
        }

        .status-warning {
            background: rgba(255, 214, 0, 0.15);
            border: 2px solid #FFD600;
            color: #FFE57F;
            transform: scale(1.02);
        }

        .status-danger {
            background: rgba(255, 23, 68, 0.15);
            border: 2px solid #FF1744;
            color: #FF8A80;
            transform: scale(1.02);
        }

        /* Hidden Input Field Always Focused */
        .hardware-scanner-input {
            position: absolute;
            opacity: 0;
            top: -1000px;
            left: -1000px;
        }

        .avatar-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
        }
    </style>
</head>
<body onclick="refocusScannerInput()">

    <div class="kiosk-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 px-2">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-primary bg-opacity-20 rounded-4 text-primary">
                    <i class="fa-solid fa-school fs-2"></i>
                </div>
                <div>
                    <h3 class="fw-extrabold m-0">SMP IT AL-MUTAQIN</h3>
                    <p class="text-secondary mb-0">Kios Presensi Otomatis • Notifikasi WA Otomatis (Fonnte API)</p>
                </div>
            </div>
            <div class="text-end">
                <span class="badge bg-success bg-opacity-20 text-success px-3 py-2 rounded-pill mb-1 fs-7">
                    <i class="fa-solid fa-circle me-1 fs-8"></i> Alat Scanner Active
                </span>
                <h2 class="fw-extrabold text-warning m-0" id="live_clock">--:--:--</h2>
            </div>
        </div>

        <!-- Main Tap Card -->
        <div class="tap-card">
            <!-- HARDWARE SCANNER TAP ZONE -->
            <div class="tap-target-zone">
                <div class="laser-line"></div>
                <i class="fa-solid fa-id-card fs-1 text-warning mb-2"></i>
                <span class="fw-bold text-white fs-5">TEMPELKAN KARTU</span>
                <small class="text-white-50 fs-7">Scan QR Code Siswa Pada Alat</small>
            </div>
            
            <p class="text-secondary small mb-0">
                <i class="fa-solid fa-barcode text-warning me-1"></i> 
                Dekatkan Kartu Pelajar ke sensor alat scanner USB untuk melakukan presensi.
            </p>

            <!-- Hidden Input Field for Hardware USB HID Scanner -->
            <input type="text" id="scanner_hidden_input" class="hardware-scanner-input" autofocus autocomplete="off">

            <!-- RESULT STATUS FEEDBACK CARD -->
            <div id="status_display" class="status-display status-idle mt-4">
                <div id="idle_view">
                    <div class="avatar-circle text-secondary">
                        <i class="fa-solid fa-user-clock"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Siap Menerima Presensi</h5>
                    <p class="text-secondary mb-0">Silakan tap kartu QR Code siswa pada alat scanner.</p>
                </div>

                <div id="result_view" style="display: none;">
                    <div class="avatar-circle" id="result_icon_bg">
                        <i id="result_icon" class="fa-solid fa-user-check"></i>
                    </div>
                    <h3 class="fw-extrabold mb-1" id="result_title">PRESENSI BERHASIL</h3>
                    <h4 class="fw-bold text-white mb-2" id="result_student_name">Nama Siswa</h4>
                    <p class="fs-6 mb-2" id="result_message">Pesan status presensi</p>

                    <div class="d-inline-flex align-items-center gap-2 bg-dark bg-opacity-50 px-3 py-2 rounded-pill mt-2">
                        <i class="fa-brands fa-whatsapp text-success fs-5"></i>
                        <small id="result_wa_status" class="text-white-50">Mengirim notifikasi WhatsApp ke Orang Tua...</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Effects -->
    <audio id="sound_success" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>
    <audio id="sound_error" src="https://assets.mixkit.co/active_storage/sfx/2573/2573-preview.mp3" preload="auto"></audio>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const hiddenInput = document.getElementById('scanner_hidden_input');
        const soundSuccess = document.getElementById('sound_success');
        const soundError = document.getElementById('sound_error');

        let isProcessing = false;

        // Auto Focus Engine for Hardware USB HID Scanner
        function refocusScannerInput() {
            if (document.activeElement !== hiddenInput) {
                hiddenInput.focus();
            }
        }

        window.onload = function() {
            refocusScannerInput();
            setInterval(refocusScannerInput, 1000);
        };

        // Live Clock Update
        setInterval(() => {
            const now = new Date();
            document.getElementById('live_clock').innerText = now.toLocaleTimeString('id-ID');
        }, 1000);

        // Hardware Scanner Keypress Event (Hardware scanner inputs fast & hits Enter)
        hiddenInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const token = this.value.trim();
                this.value = '';
                if (token !== '') {
                    processScanToken(token);
                }
            }
        });

        // Main Process Scan Token Logic
        function processScanToken(token) {
            if (isProcessing) return;
            isProcessing = true;

            const statusDisplay = document.getElementById('status_display');
            const idleView = document.getElementById('idle_view');
            const resultView = document.getElementById('result_view');
            const resultIconBg = document.getElementById('result_icon_bg');
            const resultIcon = document.getElementById('result_icon');
            const resultTitle = document.getElementById('result_title');
            const resultStudentName = document.getElementById('result_student_name');
            const resultMessage = document.getElementById('result_message');
            const resultWaStatus = document.getElementById('result_wa_status');

            idleView.style.display = 'none';
            resultView.style.display = 'block';
            statusDisplay.className = "status-display status-idle";
            resultTitle.innerText = "MEMPROSES KARTU...";
            resultStudentName.innerText = "Mengecek data siswa & mengirim WhatsApp...";
            resultMessage.innerText = "";
            resultWaStatus.innerText = "Koneksi ke Server Fonnte API...";

            fetch("{{ route('presensi.scan.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ qr_code_token: token })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    try { soundSuccess.play(); } catch(e){}

                    if (data.status === 'TERLAMBAT') {
                        statusDisplay.className = "status-display status-warning";
                        resultIconBg.style.background = 'rgba(255, 214, 0, 0.2)';
                        resultIcon.className = 'fa-solid fa-triangle-exclamation text-warning';
                        resultTitle.innerText = "PRESENSI TERLAMBAT";
                    } else {
                        statusDisplay.className = "status-display status-success";
                        resultIconBg.style.background = 'rgba(0, 230, 118, 0.2)';
                        resultIcon.className = 'fa-solid fa-circle-check text-success';
                        resultTitle.innerText = (data.type === 'masuk') ? "PRESENSI MASUK BERHASIL" : "PRESENSI PULANG BERHASIL";
                    }

                    resultStudentName.innerText = data.siswa ? `${data.siswa.nama} (${data.siswa.kelas ? data.siswa.kelas.nama_kelas : '-'})` : '';
                    resultMessage.innerText = data.message;
                    resultWaStatus.innerHTML = `<span class="text-success"><i class="fa-solid fa-check me-1"></i> Notifikasi WhatsApp Terkirim ke Orang Tua (${data.siswa.orang_tua ? data.siswa.orang_tua.no_wa : '-'})</span>`;
                } else {
                    try { soundError.play(); } catch(e){}

                    statusDisplay.className = "status-display status-danger";
                    resultIconBg.style.background = 'rgba(255, 23, 68, 0.2)';
                    resultIcon.className = 'fa-solid fa-circle-xmark text-danger';
                    resultTitle.innerText = "GAGAL PRESENSI";
                    resultStudentName.innerText = data.siswa ? data.siswa.nama : 'Kartu Tidak Dikenali';
                    resultMessage.innerText = data.message;
                    resultWaStatus.innerText = "Notifikasi WA tidak dikirim.";
                }
            })
            .catch(err => {
                try { soundError.play(); } catch(e){}
                statusDisplay.className = "status-display status-danger";
                resultTitle.innerText = "ERR_NETWORK";
                resultStudentName.innerText = "Koneksi Terputus";
                resultMessage.innerText = err.message;
            })
            .finally(() => {
                setTimeout(() => {
                    idleView.style.display = 'block';
                    resultView.style.display = 'none';
                    statusDisplay.className = "status-display status-idle";
                    isProcessing = false;
                    refocusScannerInput();
                }, 4000);
            });
        }
    </script>
</body>
</html>
