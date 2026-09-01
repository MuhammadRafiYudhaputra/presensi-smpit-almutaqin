@extends('layouts.app')

@section('content')
<style>
    .scanner-main-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 1.75rem 1.25rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        position: relative;
    }

    .digital-clock-badge {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #0284c7;
        font-weight: 800;
        font-size: 1.2rem;
        letter-spacing: 0.5px;
        padding: 0.35rem 1.15rem;
        border-radius: 50rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    /* Video Frame Container */
    .camera-frame-wrapper {
        position: relative;
        width: 100%;
        max-width: 380px;
        margin: 0 auto;
        border-radius: 20px;
        overflow: hidden;
        background: #0f172a;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        border: 2px solid rgba(37, 99, 235, 0.25);
        aspect-ratio: 4 / 3;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #cameraVideo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }

    /* Laser Scanner Beam Overlay */
    .scanner-laser-beam {
        position: absolute;
        top: 20%;
        left: 8%;
        right: 8%;
        height: 2.5px;
        background: #22c55e;
        box-shadow: 0 0 12px #22c55e, 0 0 24px #22c55e;
        animation: laserScan 2s infinite ease-in-out;
        border-radius: 2px;
        z-index: 5;
        pointer-events: none;
    }

    @keyframes laserScan {
        0%, 100% { top: 18%; opacity: 0.3; }
        50% { top: 82%; opacity: 1; }
    }

    /* Target Box Corners */
    .target-box-guide {
        position: absolute;
        top: 15%;
        left: 15%;
        right: 15%;
        bottom: 15%;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 16px;
        pointer-events: none;
        z-index: 4;
    }

    .scanner-target-box {
        width: 180px;
        height: 180px;
        margin: 1.5rem auto 1.25rem;
        border-radius: 20px;
        border: 2px dashed #2563eb;
        background: rgba(37, 99, 235, 0.04);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
    }

    .scanner-input-group {
        max-width: 620px;
        margin: 1.25rem auto 1.25rem;
    }

    .scanner-input {
        background: #ffffff;
        border: 2px solid #2563eb;
        color: #0f172a;
        border-radius: 12px 0 0 12px;
        padding: 0.85rem 1.25rem;
        font-size: 1rem;
    }

    .scanner-btn {
        background: #2563eb;
        border: 2px solid #2563eb;
        color: #ffffff;
        font-weight: 700;
        border-radius: 0 12px 12px 0;
        padding: 0.85rem 1.75rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .result-display-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        max-width: 620px;
        margin: 0 auto;
        text-align: center;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="scanner-main-card text-center">
    <!-- Top Digital Clock Header -->
    <div class="d-flex justify-content-center mb-4">
        <div class="digital-clock-badge shadow-sm" id="liveClockBadge">
            <i class="fa-regular fa-clock text-primary"></i>
            <span id="liveClock">--.--.--</span>
        </div>
    </div>

    <!-- Mode Selector Tabs -->
    <div class="d-flex justify-content-center mb-4">
        <div class="nav nav-pills bg-light p-1.5 rounded-pill border shadow-sm" role="tablist">
            <button class="nav-link active rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2" id="tab-camera" type="button" onclick="switchScanMode('camera')">
                <i class="fa-solid fa-camera"></i> Kamera HP / Webcam
            </button>
            <button class="nav-link rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2" id="tab-usb" type="button" onclick="switchScanMode('usb')">
                <i class="fa-solid fa-barcode"></i> Scanner USB Fisik
            </button>
        </div>
    </div>

    <!-- Mode 1: Native HTML5 Camera Scanner -->
    <div id="cameraScanSection" class="mb-4">
        <div class="camera-frame-wrapper mb-3">
            <!-- Native Video Feed -->
            <video id="cameraVideo" autoplay playsinline muted></video>
            
            <!-- Hidden Canvas for QR Frame Analysis -->
            <canvas id="qrCanvas" style="display: none;"></canvas>

            <!-- Guide Overlay -->
            <div id="scannerOverlay" style="display: none;">
                <div class="target-box-guide"></div>
                <div class="scanner-laser-beam"></div>
            </div>

            <!-- Placeholder State Sebelum Dinyalakan -->
            <div id="cameraStandbyBox" class="p-4 text-center text-white">
                <i class="fa-solid fa-camera fs-1 mb-2 opacity-75 text-primary"></i>
                <h6 class="fw-bold mb-1 text-white">Kamera HP / Webcam</h6>
                <small class="text-white-50 d-block mb-3" style="font-size: 0.8rem;">Klik tombol di bawah untuk membuka kamera</small>
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2" onclick="startCamera()">
                    <i class="fa-solid fa-play"></i> Nyalakan Kamera
                </button>
            </div>
        </div>

        <!-- Camera Actions Toolbar -->
        <div class="d-flex align-items-center justify-content-center flex-wrap mt-3" style="gap: 12px;">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3.5 py-2 rounded-pill fw-semibold shadow-sm" id="cameraStatusBadge" style="font-size: 0.8rem;">
                <i class="fa-solid fa-camera me-1.5"></i> Siap Menyalakan Kamera
            </span>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm" id="btnSwitchCam" onclick="toggleCameraFacing()" style="font-size: 0.8rem; display: none;">
                <i class="fa-solid fa-camera-rotate"></i> Putar Kamera
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-sm" id="btnStopCam" onclick="stopCamera()" style="font-size: 0.8rem; display: none;">
                <i class="fa-solid fa-stop"></i> Matikan Kamera
            </button>
        </div>
    </div>

    <!-- Mode 2: USB Scanner Gun Container -->
    <div id="usbScanSection" class="mb-4" style="display: none;">
        <div class="scanner-target-box mb-3">
            <i class="fa-solid fa-qrcode fs-2 mb-1 text-primary"></i>
            <span class="fw-bold text-dark fs-6 d-block">SCAN KARTU QR</span>
            <small class="text-muted" style="font-size: 0.75rem;">Dekatkan ke Scanner USB</small>
        </div>

        <form id="formScan" onsubmit="event.preventDefault(); submitUsbScan();" class="scanner-input-group">
            <div class="input-group">
                <input type="text" id="qrInput" class="form-control scanner-input" placeholder="Hasil scan USB akan tampil otomatis di sini..." autocomplete="off">
                <button type="submit" class="btn scanner-btn">
                    <i class="fa-solid fa-qrcode"></i> Scan
                </button>
            </div>
        </form>
    </div>

    <!-- Result Display Card -->
    <div class="result-display-card shadow-sm mt-4" id="resultContainer">
        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary mb-2 d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="fa-solid fa-id-card-clip fs-4"></i>
        </div>
        <h6 class="fw-bold text-dark mb-1">Siap Menerima Presensi Siswa</h6>
        <small class="text-muted">Arahkan Kartu QR Siswa pada Kamera HP atau Sensor USB Scanner.</small>
    </div>
</div>

<!-- jsQR High-Performance Scanner Library -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

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

    // 2. State & Mode Handling
    let activeMode = 'camera';
    let currentFacingMode = "environment"; // "environment" = Kamera Belakang, "user" = Kamera Depan
    let videoStream = null;
    let isScanning = false;
    let canProcessScan = true;

    function switchScanMode(mode) {
        activeMode = mode;
        const cameraSection = document.getElementById('cameraScanSection');
        const usbSection = document.getElementById('usbScanSection');
        const tabCamera = document.getElementById('tab-camera');
        const tabUsb = document.getElementById('tab-usb');
        const qrInput = document.getElementById('qrInput');

        if (mode === 'camera') {
            tabCamera.classList.add('active');
            tabUsb.classList.remove('active');
            cameraSection.style.display = 'block';
            usbSection.style.display = 'none';
            startCamera();
        } else {
            tabUsb.classList.add('active');
            tabCamera.classList.remove('active');
            cameraSection.style.display = 'none';
            usbSection.style.display = 'block';
            stopCamera();
            setTimeout(() => qrInput.focus(), 200);
        }
    }

    // 3. Native Camera Streaming & jsQR Processing
    async function startCamera() {
        if (isScanning) return;

        const video = document.getElementById('cameraVideo');
        const standbyBox = document.getElementById('cameraStandbyBox');
        const overlay = document.getElementById('scannerOverlay');
        const statusBadge = document.getElementById('cameraStatusBadge');
        const btnSwitch = document.getElementById('btnSwitchCam');
        const btnStop = document.getElementById('btnStopCam');

        try {
            if (videoStream) {
                videoStream.getTracks().forEach(t => t.stop());
            }

            const constraints = {
                video: {
                    facingMode: currentFacingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            };

            videoStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = videoStream;
            video.setAttribute("playsinline", "true");
            await video.play();

            video.style.display = 'block';
            standbyBox.style.display = 'none';
            overlay.style.display = 'block';
            btnSwitch.style.display = 'inline-flex';
            btnStop.style.display = 'inline-flex';

            statusBadge.className = 'badge bg-success bg-opacity-10 text-success border border-success px-3 py-1.5 rounded-pill fw-semibold';
            statusBadge.innerHTML = '<i class="fa-solid fa-video me-1"></i> Kamera Aktif — Arahkan QR Code Kartu Siswa';

            isScanning = true;
            requestAnimationFrame(scanVideoFrame);
        } catch (err) {
            console.error("Camera Access Error:", err);
            // Fallback coba tanpa batasan facingMode
            try {
                videoStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                video.srcObject = videoStream;
                video.setAttribute("playsinline", "true");
                await video.play();

                video.style.display = 'block';
                standbyBox.style.display = 'none';
                overlay.style.display = 'block';
                btnSwitch.style.display = 'inline-flex';
                btnStop.style.display = 'inline-flex';

                statusBadge.className = 'badge bg-success bg-opacity-10 text-success border border-success px-3 py-1.5 rounded-pill fw-semibold';
                statusBadge.innerHTML = '<i class="fa-solid fa-video me-1"></i> Kamera Aktif — Arahkan QR Code Kartu Siswa';

                isScanning = true;
                requestAnimationFrame(scanVideoFrame);
            } catch (fallbackErr) {
                console.error("Fatal Camera Error:", fallbackErr);
                statusBadge.className = 'badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1.5 rounded-pill fw-semibold';
                statusBadge.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> Izin kamera ditolak atau tidak didukung.';
            }
        }
    }

    function scanVideoFrame() {
        if (!isScanning) return;

        const video = document.getElementById('cameraVideo');
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            const canvas = document.getElementById('qrCanvas');
            const ctx = canvas.getContext('2d');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            
            if (typeof jsQR !== 'undefined') {
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: "dontInvert"
                });

                if (code && code.data && canProcessScan) {
                    canProcessScan = false;
                    processPresensi(code.data, () => {
                        // Cooldown 2.5 detik sebelum scan berikutnya
                        setTimeout(() => {
                            canProcessScan = true;
                        }, 2500);
                    });
                }
            }
        }

        requestAnimationFrame(scanVideoFrame);
    }

    function stopCamera() {
        isScanning = false;
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }

        const video = document.getElementById('cameraVideo');
        const standbyBox = document.getElementById('cameraStandbyBox');
        const overlay = document.getElementById('scannerOverlay');
        const btnSwitch = document.getElementById('btnSwitchCam');
        const btnStop = document.getElementById('btnStopCam');
        const statusBadge = document.getElementById('cameraStatusBadge');

        video.style.display = 'none';
        overlay.style.display = 'none';
        standbyBox.style.display = 'block';
        btnSwitch.style.display = 'none';
        btnStop.style.display = 'none';

        statusBadge.className = 'badge bg-secondary bg-opacity-10 text-muted border px-3 py-1.5 rounded-pill fw-semibold';
        statusBadge.innerHTML = '<i class="fa-solid fa-video-slash me-1"></i> Kamera Dimatikan';
    }

    function toggleCameraFacing() {
        currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
        stopCamera();
        setTimeout(() => startCamera(), 200);
    }

    // 4. USB Scanner Gun Input Listener
    const qrInput = document.getElementById('qrInput');
    let scanTimeout = null;

    qrInput.addEventListener('input', function() {
        if (scanTimeout) clearTimeout(scanTimeout);
        if (this.value.length >= 6) {
            scanTimeout = setTimeout(() => submitUsbScan(), 250);
        }
    });

    document.addEventListener('click', () => {
        if (activeMode === 'usb') qrInput.focus();
    });

    function submitUsbScan() {
        const token = qrInput.value.trim();
        if (!token) return;

        qrInput.disabled = true;
        processPresensi(token, () => {
            qrInput.value = '';
            qrInput.disabled = false;
            qrInput.focus();
        });
    }

    // 5. Core AJAX Process Scan
    function processPresensi(token, callback) {
        token = token.trim();
        if (!token) {
            if (callback) callback();
            return;
        }

        const container = document.getElementById('resultContainer');
        container.innerHTML = `
            <div class="spinner-border text-primary mb-2" role="status" style="width: 2rem; height: 2rem;"></div>
            <div class="fw-bold text-dark small">Memproses Data Presensi...</div>
        `;

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
            if (data.success) {
                playSuccessBeep();
                const isPulang = (data.type === 'pulang');
                const isTerlambat = (data.status === 'TERLAMBAT');
                const statusBadge = isPulang 
                    ? `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill fs-6 fw-bold"><i class="fa-solid fa-door-open me-1"></i> PULANG SEKOLAH</span>`
                    : (isTerlambat 
                        ? `<span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-2 rounded-pill fs-6 fw-bold" style="color: #92400e !important;"><i class="fa-solid fa-clock me-1 text-warning"></i> TERLAMBAT</span>`
                        : `<span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill fs-6 fw-bold"><i class="fa-solid fa-circle-check me-1"></i> HADIR TEPAT WAKTU</span>`);

                container.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success border border-success" style="width: 52px; height: 52px; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-check fs-3"></i>
                        </div>
                        <div class="text-start">
                            <h5 class="fw-bold text-dark mb-0">${data.siswa ? data.siswa.nama : 'Siswa'}</h5>
                            <small class="text-muted">Kelas ${data.siswa && data.siswa.kelas ? data.siswa.kelas.nama_kelas : '-'} &bull; NISN: <strong>${data.siswa ? data.siswa.nisn : '-'}</strong></small>
                        </div>
                    </div>
                    <div class="my-2">${statusBadge}</div>
                    <small class="text-success fw-semibold mt-1"><i class="fa-brands fa-whatsapp me-1"></i> Notifikasi WhatsApp Terkirim ke Orang Tua (${data.waktu})</small>
                `;
            } else if (data.type === 'belum_pulang') {
                playWarningBeep();
                container.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                        <div class="bg-warning bg-opacity-20 p-3 rounded-circle text-warning border border-warning" style="width: 56px; height: 56px; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-triangle-exclamation fs-3 text-warning"></i>
                        </div>
                        <div class="text-start">
                            <h5 class="fw-bold text-dark mb-0">${data.siswa ? data.siswa.nama : 'Siswa'}</h5>
                            <small class="text-muted">Kelas ${data.siswa && data.siswa.kelas ? data.siswa.kelas.nama_kelas : '-'} &bull; NISN: <strong>${data.siswa ? data.siswa.nisn : '-'}</strong></small>
                        </div>
                    </div>
                    <div class="my-2">
                        <span class="badge bg-warning bg-opacity-15 text-dark border border-warning px-3 py-2 rounded-pill fs-6 fw-bold">
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
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger border border-danger mb-2" style="width: 52px; height: 52px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-xmark fs-3 text-danger"></i>
                    </div>
                    <h6 class="fw-bold text-danger mb-1">Presensi Gagal!</h6>
                    <small class="text-muted">${data.message || 'Token QR Code tidak valid.'}</small>
                `;
            }
        })
        .catch(err => {
            console.error('Scan Error:', err);
            container.innerHTML = `
                <div class="text-danger fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i> Terjadi kesalahan koneksi server. Silakan coba lagi.</div>
            `;
        })
        .finally(() => {
            if (callback) callback();
        });
    }

    // 6. Audio Synthesizer
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
</script>
@endsection
