@extends('layouts.app')

@section('content')
<style>
    .scanner-main-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 2.25rem 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        position: relative;
        overflow: hidden;
    }

    .digital-clock-badge {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #0284c7;
        font-weight: 800;
        font-size: 1.25rem;
        letter-spacing: 0.5px;
        padding: 0.4rem 1.15rem;
        border-radius: 50rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: absolute;
        top: 1.5rem;
        left: 1.75rem;
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
        letter-spacing: 0.5px;
        box-shadow: none;
    }

    .scanner-input:focus {
        background: #ffffff;
        border-color: #2563eb;
        color: #0f172a;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
    }

    .scanner-input::placeholder {
        color: #94a3b8;
        font-size: 0.95rem;
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
        transition: all 0.2s ease;
    }

    .scanner-btn:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #ffffff;
    }

    .result-display-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.75rem;
        max-width: 620px;
        margin: 0 auto;
        text-align: center;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
</style>

<div class="scanner-main-card text-center">
    <!-- Live Digital Clock Badge -->
    <div class="digital-clock-badge" id="liveClockBadge">
        <i class="fa-regular fa-clock text-primary"></i>
        <span id="liveClock">--.--.--</span>
    </div>

    <!-- Mode Selector Tabs (Kamera HP vs Scanner USB) -->
    <div class="d-flex justify-content-center mb-3">
        <div class="nav nav-pills bg-light p-1 rounded-pill border" id="scannerModeTabs" role="tablist">
            <button class="nav-link active rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2" id="tab-camera" data-bs-toggle="pill" data-bs-target="#mode-camera" type="button" role="tab" onclick="switchScanMode('camera')">
                <i class="fa-solid fa-camera"></i> Kamera HP / Webcam
            </button>
            <button class="nav-link rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2" id="tab-usb" data-bs-toggle="pill" data-bs-target="#mode-usb" type="button" role="tab" onclick="switchScanMode('usb')">
                <i class="fa-solid fa-barcode"></i> Scanner USB Fisik
            </button>
        </div>
    </div>

    <!-- Mode 1: Kamera HP / Webcam Container -->
    <div id="cameraScanSection" class="mb-3">
        <!-- Insecure context (HTTP) Alert if any -->
        <div id="httpWarningAlert" class="alert alert-warning border-warning py-2 px-3 small rounded-3 mx-auto mb-3" style="max-width: 420px; display: none;">
            <i class="fa-solid fa-triangle-exclamation me-1 text-warning"></i>
            <strong>Peringatan HTTPS:</strong> Browser HP mewajibkan koneksi <strong>HTTPS</strong> untuk mengakses kamera. Pastikan URL dibuka dengan <code>https://</code>.
        </div>

        <div class="position-relative mx-auto rounded-4 overflow-hidden shadow-sm border border-primary border-opacity-25 bg-dark" style="max-width: 400px; min-height: 260px; display: flex; align-items: center; justify-content: center;">
            <div id="reader" style="width: 100%;"></div>
            
            <div id="cameraLoadingPlaceholder" class="p-4 bg-light text-center w-100" style="display: none;">
                <div class="spinner-border text-primary mb-2" role="status"></div>
                <div class="fw-bold text-dark small">Menghubungkan ke Sensor Kamera...</div>
                <small class="text-muted d-block">Izinkan akses kamera di browser Anda</small>
            </div>

            <div id="cameraStoppedPlaceholder" class="p-4 bg-light text-center w-100">
                <i class="fa-solid fa-camera fs-1 text-primary mb-2 opacity-50"></i>
                <div class="fw-bold text-dark small mb-1">Kamera Belum Aktif</div>
                <small class="text-muted d-block mb-3">Klik tombol di bawah untuk menyalakan kamera HP / Webcam</small>
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2" onclick="startCameraScanner()">
                    <i class="fa-solid fa-camera"></i> Nyalakan Kamera
                </button>
            </div>
        </div>

        <!-- Camera Control Bar -->
        <div class="d-flex align-items-center justify-content-center gap-2 mt-2.5 flex-wrap">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill fw-semibold" id="cameraStatusBadge" style="font-size: 0.78rem;">
                <i class="fa-solid fa-camera me-1"></i> Siap Menyalakan Kamera
            </span>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-none" id="btnSwitchCam" onclick="toggleCameraFacing()" style="font-size: 0.78rem; display: none;">
                <i class="fa-solid fa-camera-rotate"></i> Putar Kamera
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-none" id="btnStopCam" onclick="stopCameraScanner()" style="font-size: 0.78rem; display: none;">
                <i class="fa-solid fa-stop"></i> Matikan Kamera
            </button>
        </div>
    </div>

    <!-- Mode 2: USB Scanner Gun Container -->
    <div id="usbScanSection" style="display: none;">
        <!-- Center Target QR Box -->
        <div class="scanner-target-box">
            <i class="fa-solid fa-qrcode fs-2 mb-1 text-primary"></i>
            <span class="fw-bold text-dark fs-6 d-block">SCAN KARTU QR</span>
            <small class="text-muted" style="font-size: 0.75rem;">Dekatkan ke Scanner USB</small>
        </div>

        <!-- Input Scanner Form -->
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
    <div class="result-display-card" id="resultContainer">
        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary mb-2 d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="fa-solid fa-id-card-clip fs-4"></i>
        </div>
        <h6 class="fw-bold text-dark mb-1">Siap Menerima Presensi Siswa</h6>
        <small class="text-muted">Arahkan Kartu QR Siswa pada Kamera HP atau Sensor USB Scanner.</small>
    </div>
</div>

<!-- Include Library HTML5-QRCode dengan Fallback CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
    if (typeof Html5Qrcode === 'undefined') {
        document.write('<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"><\/script>');
    }
</script>

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

    // 2. HTTPS Check for Mobile Camera Permission
    if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
        document.getElementById('httpWarningAlert').style.display = 'block';
    }

    // 3. State & Mode Handling
    let activeMode = 'camera'; // 'camera' or 'usb'
    let currentFacingMode = "environment"; // "environment" (belakang) or "user" (depan)
    let html5QrCode = null;
    let isCameraRunning = false;
    let canScanCamera = true;

    function switchScanMode(mode) {
        activeMode = mode;
        const cameraSection = document.getElementById('cameraScanSection');
        const usbSection = document.getElementById('usbScanSection');
        const qrInput = document.getElementById('qrInput');

        if (mode === 'camera') {
            cameraSection.style.display = 'block';
            usbSection.style.display = 'none';
            startCameraScanner();
        } else {
            cameraSection.style.display = 'none';
            usbSection.style.display = 'block';
            stopCameraScanner();
            setTimeout(() => {
                qrInput.focus();
            }, 200);
        }
    }

    // 4. Camera Scanner Lifecycle
    function startCameraScanner() {
        if (isCameraRunning) return;

        const readerElem = document.getElementById('reader');
        const statusBadge = document.getElementById('cameraStatusBadge');
        const loadingPlaceholder = document.getElementById('cameraLoadingPlaceholder');
        const stoppedPlaceholder = document.getElementById('cameraStoppedPlaceholder');
        const btnSwitchCam = document.getElementById('btnSwitchCam');
        const btnStopCam = document.getElementById('btnStopCam');

        if (!readerElem) return;

        stoppedPlaceholder.style.display = 'none';
        loadingPlaceholder.style.display = 'block';
        readerElem.style.display = 'none';

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        const config = { 
            fps: 15, 
            qrbox: (viewfinderWidth, viewfinderHeight) => {
                const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                const size = Math.floor(minEdge * 0.72);
                return { width: Math.max(180, size), height: Math.max(180, size) };
            },
            aspectRatio: 1.0
        };

        html5QrCode.start(
            { facingMode: currentFacingMode }, 
            config, 
            onCameraScanSuccess, 
            () => {} // continuous frame scan
        )
        .then(() => {
            isCameraRunning = true;
            loadingPlaceholder.style.display = 'none';
            stoppedPlaceholder.style.display = 'none';
            readerElem.style.display = 'block';
            btnSwitchCam.style.display = 'inline-flex';
            btnStopCam.style.display = 'inline-flex';

            statusBadge.className = 'badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill fw-semibold';
            statusBadge.innerHTML = '<i class="fa-solid fa-video me-1"></i> Kamera Aktif — Arahkan QR Code Kartu Siswa';
        })
        .catch(err => {
            console.warn("Gagal membuka kamera dengan facingMode:", currentFacingMode, err);
            // Fallback coba kamera apa saja tanpa batasan facingMode
            html5QrCode.start(
                { facingMode: (currentFacingMode === 'environment' ? 'user' : 'environment') }, 
                config, 
                onCameraScanSuccess, 
                () => {}
            )
            .then(() => {
                isCameraRunning = true;
                loadingPlaceholder.style.display = 'none';
                stoppedPlaceholder.style.display = 'none';
                readerElem.style.display = 'block';
                btnSwitchCam.style.display = 'inline-flex';
                btnStopCam.style.display = 'inline-flex';

                statusBadge.className = 'badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill fw-semibold';
                statusBadge.innerHTML = '<i class="fa-solid fa-video me-1"></i> Kamera Aktif — Arahkan QR Code Kartu Siswa';
            })
            .catch(finalErr => {
                console.error("Camera permission or hardware error:", finalErr);
                loadingPlaceholder.style.display = 'none';
                stoppedPlaceholder.style.display = 'block';
                readerElem.style.display = 'none';
                btnSwitchCam.style.display = 'none';
                btnStopCam.style.display = 'none';

                statusBadge.className = 'badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill fw-semibold';
                statusBadge.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> Gagal membuka kamera. Pastikan izin kamera browser telah diizinkan.';
            });
        });
    }

    function stopCameraScanner() {
        if (html5QrCode && isCameraRunning) {
            html5QrCode.stop().then(() => {
                isCameraRunning = false;
                document.getElementById('reader').style.display = 'none';
                document.getElementById('cameraStoppedPlaceholder').style.display = 'block';
                document.getElementById('btnSwitchCam').style.display = 'none';
                document.getElementById('btnStopCam').style.display = 'none';
                const statusBadge = document.getElementById('cameraStatusBadge');
                statusBadge.className = 'badge bg-secondary bg-opacity-10 text-muted border px-3 py-2 rounded-pill fw-semibold';
                statusBadge.innerHTML = '<i class="fa-solid fa-video-slash me-1"></i> Kamera Dinonaktifkan';
            }).catch(err => console.error("Error stopping camera:", err));
        }
    }

    function toggleCameraFacing() {
        stopCameraScanner();
        currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
        setTimeout(() => {
            startCameraScanner();
        }, 300);
    }

    function onCameraScanSuccess(decodedText) {
        if (!canScanCamera) return;
        canScanCamera = false;

        processPresensi(decodedText, () => {
            // Cooldown 2.5 detik agar kartu tidak langsung ter-scan ganda
            setTimeout(() => {
                canScanCamera = true;
            }, 2500);
        });
    }

    // 5. USB Scanner Gun Input Listener
    const qrInput = document.getElementById('qrInput');
    let scanTimeout = null;

    qrInput.addEventListener('input', function() {
        if (scanTimeout) clearTimeout(scanTimeout);
        if (this.value.length >= 6) {
            scanTimeout = setTimeout(() => {
                submitUsbScan();
            }, 250);
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

    // 6. Unified Core AJAX Process Scan
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

    // 7. Auto Start Camera on page load if permission was previously granted or triggered
    document.addEventListener('DOMContentLoaded', () => {
        startCameraScanner();
    });

    // 8. Audio Feedback Synthesizer using Web Audio API
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
