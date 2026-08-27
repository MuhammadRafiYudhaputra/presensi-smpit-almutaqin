@extends('layouts.app')

@section('content')
<!-- HTML5 QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<style>
    .scanner-main-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 2rem 1.75rem;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.03);
        position: relative;
        overflow: hidden;
    }

    .digital-clock-badge {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #0284c7;
        font-weight: 800;
        font-size: 1.15rem;
        letter-spacing: 0.5px;
        padding: 0.35rem 1rem;
        border-radius: 50rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    /* Kamera Viewfinder Area */
    .camera-viewport-wrapper {
        max-width: 440px;
        margin: 1.25rem auto 1rem;
        border-radius: 20px;
        overflow: hidden;
        border: 3px solid #2563eb;
        box-shadow: 0 8px 30px rgba(37, 99, 235, 0.12);
        background: #0f172a;
        position: relative;
        min-height: 280px;
    }

    #camera-reader {
        width: 100% !important;
        border: none !important;
    }

    #camera-reader video {
        border-radius: 16px;
        object-fit: cover !important;
    }

    .scanner-target-box {
        width: 170px;
        height: 170px;
        margin: 1.25rem auto 1rem;
        border-radius: 20px;
        border: 2px dashed #2563eb;
        background: rgba(37, 99, 235, 0.04);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.08);
    }

    .scanner-target-box::after {
        content: '';
        position: absolute;
        top: 15%;
        left: 10%;
        right: 10%;
        height: 2px;
        background: #2563eb;
        box-shadow: 0 0 10px #2563eb, 0 0 20px #2563eb;
        animation: scanBeam 2.2s infinite ease-in-out;
        border-radius: 2px;
    }

    @keyframes scanBeam {
        0%, 100% { top: 20%; opacity: 0.3; }
        50% { top: 80%; opacity: 1; }
    }

    .scanner-input-group {
        max-width: 540px;
        margin: 1rem auto 1rem;
    }

    .scanner-input {
        background: #ffffff;
        border: 2px solid #2563eb;
        color: #0f172a;
        border-radius: 12px 0 0 12px;
        padding: 0.75rem 1.15rem;
        font-size: 0.95rem;
    }

    .scanner-input:focus {
        background: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
    }

    .scanner-btn {
        background: #2563eb;
        border: 2px solid #2563eb;
        color: #ffffff;
        font-weight: 700;
        border-radius: 0 12px 12px 0;
        padding: 0.75rem 1.5rem;
    }

    .result-display-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        max-width: 540px;
        margin: 1rem auto 0;
        text-align: center;
        min-height: 110px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: all 0.25s ease;
    }

    .scan-mode-pill {
        cursor: pointer;
        padding: 0.5rem 1.25rem;
        border-radius: 50rem;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
</style>

<div class="scanner-main-card text-center">
    <!-- Header Top Row: Clock & Mode Switcher -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-3">
        <div class="digital-clock-badge" id="liveClockBadge">
            <i class="fa-regular fa-clock text-primary"></i>
            <span id="liveClock">--.--.--</span>
        </div>

        <!-- Mode Switcher: Kamera HP / Webcam VS Alat Scanner USB -->
        <div class="btn-group p-1 bg-light rounded-pill border" role="group">
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-2" id="btnModeCamera" onclick="switchScanMode('camera')">
                <i class="fa-solid fa-camera"></i>
                <span>Kamera HP / Webcam</span>
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-2" id="btnModeUSB" onclick="switchScanMode('usb')">
                <i class="fa-solid fa-barcode"></i>
                <span>Alat Scanner USB / Input</span>
            </button>
        </div>
    </div>

    <!-- 1. TAMPILAN MODE KAMERA (HP / WEBCAM) -->
    <div id="sectionCameraMode">
        <div class="camera-viewport-wrapper" id="cameraWrapper">
            <div id="camera-reader"></div>
        </div>

        <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap mb-2">
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" id="btnSwitchCamera" onclick="toggleCameraFacing()" style="font-size: 0.8rem;">
                <i class="fa-solid fa-camera-rotate"></i>
                <span>Ganti Kamera Depan / Belakang</span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" onclick="restartCameraScanner()" style="font-size: 0.8rem;">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>Refresh Kamera</span>
            </button>
        </div>
        <small class="text-muted d-block mb-3" style="font-size: 0.78rem;">
            <i class="fa-solid fa-circle-info text-primary me-1"></i> Arahkan Kartu QR Siswa tepat di depan lensa kamera.
        </small>
    </div>

    <!-- 2. TAMPILAN MODE ALAT SCANNER USB -->
    <div id="sectionUSBMode" class="d-none">
        <div class="scanner-target-box">
            <i class="fa-solid fa-qrcode fs-2 mb-1 text-primary"></i>
            <span class="fw-bold text-dark fs-6 d-block">SCAN KARTU QR</span>
            <small class="text-muted" style="font-size: 0.75rem;">Dekatkan ke Scanner USB</small>
        </div>

        <div class="d-inline-flex align-items-center gap-2 badge bg-success bg-opacity-10 text-success border border-success px-3 py-1.5 rounded-pill fw-semibold mb-2" style="font-size: 0.78rem;">
            <i class="fa-solid fa-barcode"></i> Sensor USB Scanner Terhubung & Auto-Focus Aktif
        </div>

        <form id="formScan" onsubmit="event.preventDefault(); submitScan(qrInput.value.trim());" class="scanner-input-group">
            <div class="input-group">
                <input type="text" id="qrInput" class="form-control scanner-input" placeholder="Scan kartu USB atau ketik NISN siswa..." autocomplete="off">
                <button type="submit" class="btn scanner-btn">
                    <i class="fa-solid fa-qrcode me-1"></i> Scan
                </button>
            </div>
        </form>
    </div>

    <!-- Result Display Card (Notifikasi & Identitas Siswa) -->
    <div class="result-display-card" id="resultContainer">
        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary mb-2 d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i class="fa-solid fa-id-card-clip fs-5"></i>
        </div>
        <h6 class="fw-bold text-dark mb-1">Siap Menerima Presensi Siswa</h6>
        <small class="text-muted">Dekatkan Kartu QR Siswa ke kamera atau alat scanner.</small>
    </div>
</div>

<script>
    // 1. Live Digital Clock
    function updateClock() {
        const now = new Date();
        const hrs = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        const secs = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('liveClock').innerText = `${hrs}.${mins}.${secs}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. State & Mode Variables
    let currentScanMode = 'camera'; // 'camera' or 'usb'
    let html5QrCode = null;
    let currentFacingMode = "environment"; // "environment" (belakang) or "user" (depan)
    let isScanningPaused = false;
    const qrInput = document.getElementById('qrInput');

    // 3. Switch Mode Function
    function switchScanMode(mode) {
        currentScanMode = mode;
        const btnCam = document.getElementById('btnModeCamera');
        const btnUSB = document.getElementById('btnModeUSB');
        const secCam = document.getElementById('sectionCameraMode');
        const secUSB = document.getElementById('sectionUSBMode');

        if (mode === 'camera') {
            btnCam.className = 'btn btn-sm rounded-pill px-3 py-1.5 fw-bold btn-primary shadow-sm';
            btnUSB.className = 'btn btn-sm rounded-pill px-3 py-1.5 fw-bold btn-light text-muted';
            secCam.classList.remove('d-none');
            secUSB.classList.add('d-none');
            startCameraScanner();
        } else {
            btnUSB.className = 'btn btn-sm rounded-pill px-3 py-1.5 fw-bold btn-primary shadow-sm';
            btnCam.className = 'btn btn-sm rounded-pill px-3 py-1.5 fw-bold btn-light text-muted';
            secUSB.classList.remove('d-none');
            secCam.classList.add('d-none');
            stopCameraScanner();
            setTimeout(() => {
                if (qrInput) qrInput.focus();
            }, 100);
        }
    }

    // 4. HTML5 Camera Scanner Engine
    function startCameraScanner() {
        if (typeof Html5Qrcode === "undefined") return;

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("camera-reader");
        }

        const config = {
            fps: 15,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };

        html5QrCode.start(
            { facingMode: currentFacingMode },
            config,
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            console.warn("Gagal membuka kamera:", err);
            document.getElementById('camera-reader').innerHTML = `
                <div class="p-4 text-white text-center">
                    <i class="fa-solid fa-video-slash fs-2 mb-2 text-warning"></i>
                    <p class="mb-1 fw-bold">Izin Kamera Belum Diberikan / Kamera Tidak Ditemukan</p>
                    <small class="text-white-50 d-block mb-3">Pastikan izin kamera diizinkan di browser Anda, atau gunakan Mode Alat Scanner USB.</small>
                    <button class="btn btn-sm btn-primary rounded-pill px-3" onclick="switchScanMode('usb')">Gunakan Mode USB Scanner</button>
                </div>
            `;
        });
    }

    function stopCameraScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().catch(err => console.log(err));
        }
    }

    function restartCameraScanner() {
        stopCameraScanner();
        setTimeout(startCameraScanner, 300);
    }

    function toggleCameraFacing() {
        currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
        restartCameraScanner();
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (isScanningPaused) return;

        // Throttling debounce agar tidak ter-scan berulang kali dalam 2 detik
        isScanningPaused = true;
        playSuccessBeep();

        submitScan(decodedText.trim());

        setTimeout(() => {
            isScanningPaused = false;
        }, 2200);
    }

    function onScanFailure(error) {
        // Biarkan silent saat kamera belum menangkap QR
    }

    // 5. USB Scanner Input Event Listener
    let scanTimeout = null;
    qrInput.addEventListener('input', function() {
        if (scanTimeout) clearTimeout(scanTimeout);
        if (this.value.length >= 6) {
            scanTimeout = setTimeout(() => {
                submitScan(this.value.trim());
            }, 250);
        }
    });

    // 6. AJAX Process Scan Core
    function submitScan(token) {
        if (!token) return;

        if (qrInput) qrInput.disabled = true;

        fetch("{{ route('presensi.scan.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                "Accept": "application/json"
            },
            body: JSON.stringify({ 
                qr_code_token: token,
                qr_token: token 
            })
        })
        .then(async res => {
            const data = await res.json();
            return { ok: res.ok, data };
        })
        .then(({ ok, data }) => {
            const container = document.getElementById('resultContainer');
            if (data.success) {
                playSuccessBeep();
                const isPulang = (data.type === 'pulang');
                const isTerlambat = (data.status === 'TERLAMBAT');
                const statusBadge = isPulang 
                    ? `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-1.5 rounded-pill fs-6 fw-bold"><i class="fa-solid fa-door-open me-1"></i> PULANG SEKOLAH</span>`
                    : (isTerlambat 
                        ? `<span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-1.5 rounded-pill fs-6 fw-bold" style="color: #92400e !important;"><i class="fa-solid fa-clock me-1 text-warning"></i> TERLAMBAT</span>`
                        : `<span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1.5 rounded-pill fs-6 fw-bold"><i class="fa-solid fa-circle-check me-1"></i> HADIR TEPAT WAKTU</span>`);

                container.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                        <div class="bg-success bg-opacity-10 p-2.5 rounded-circle text-success border border-success" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-check fs-4"></i>
                        </div>
                        <div class="text-start">
                            <h5 class="fw-bold text-dark mb-0">${data.siswa ? data.siswa.nama : 'Siswa'}</h5>
                            <small class="text-muted">Kelas ${data.siswa && data.siswa.kelas ? data.siswa.kelas.nama_kelas : '-'} &bull; NISN: <strong>${data.siswa ? data.siswa.nisn : '-'}</strong></small>
                        </div>
                    </div>
                    <div class="my-1.5">${statusBadge}</div>
                    <small class="text-success fw-semibold mt-1"><i class="fa-brands fa-whatsapp me-1"></i> Notifikasi WhatsApp Terkirim ke Orang Tua (${data.waktu})</small>
                `;
            } else if (data.type === 'belum_pulang') {
                playWarningBeep();
                container.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                        <div class="bg-warning bg-opacity-20 p-2.5 rounded-circle text-warning border border-warning" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-triangle-exclamation fs-4 text-warning"></i>
                        </div>
                        <div class="text-start">
                            <h5 class="fw-bold text-dark mb-0">${data.siswa ? data.siswa.nama : 'Siswa'}</h5>
                            <small class="text-muted">Kelas ${data.siswa && data.siswa.kelas ? data.siswa.kelas.nama_kelas : '-'} &bull; NISN: <strong>${data.siswa ? data.siswa.nisn : '-'}</strong></small>
                        </div>
                    </div>
                    <div class="my-1.5">
                        <span class="badge bg-warning bg-opacity-15 text-dark border border-warning px-3 py-1.5 rounded-pill fs-6 fw-bold">
                            <i class="fa-solid fa-hand me-1 text-warning"></i> ⛔ DITOLAK: BELUM WAKTUNYA PULANG
                        </span>
                    </div>
                    <small class="text-danger fw-semibold d-block mt-1">
                        ${data.message || 'Belum waktunya pulang.'}
                    </small>
                `;
            } else {
                playErrorBeep();
                container.innerHTML = `
                    <div class="bg-danger bg-opacity-10 p-2.5 rounded-circle text-danger border border-danger mb-2" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-xmark fs-4 text-danger"></i>
                    </div>
                    <h6 class="fw-bold text-danger mb-1">Presensi Gagal!</h6>
                    <small class="text-muted">${data.message || 'Token QR Code tidak valid.'}</small>
                `;
            }
        })
        .catch(err => {
            console.error('Scan Error:', err);
            document.getElementById('resultContainer').innerHTML = `
                <div class="text-danger fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i> Terjadi kesalahan koneksi server. Silakan coba lagi.</div>
            `;
        })
        .finally(() => {
            if (qrInput) {
                qrInput.value = '';
                qrInput.disabled = false;
                if (currentScanMode === 'usb') qrInput.focus();
            }
        });
    }

    // 7. Audio Feedback Synthesizer using Web Audio API
    function playSuccessBeep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(800, ctx.currentTime);
            osc.frequency.setValueAtTime(1200, ctx.currentTime + 0.08);
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.25);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.25);
        } catch(e) {}
    }

    function playWarningBeep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(300, ctx.currentTime);
            osc.frequency.setValueAtTime(200, ctx.currentTime + 0.15);
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.35);
        } catch(e) {}
    }

    function playErrorBeep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'square';
            osc.frequency.setValueAtTime(150, ctx.currentTime);
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.3);
        } catch(e) {}
    }

    // Start default mode on page load
    document.addEventListener('DOMContentLoaded', () => {
        switchScanMode('camera');
    });
</script>
@endsection
