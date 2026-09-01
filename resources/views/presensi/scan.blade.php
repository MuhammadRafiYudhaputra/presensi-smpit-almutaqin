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
        <div class="position-relative mx-auto" style="max-width: 380px;">
            <div id="reader" class="rounded-4 overflow-hidden shadow-sm border border-primary border-opacity-25 bg-dark"></div>
            <div id="cameraLoadingPlaceholder" class="p-4 bg-light rounded-4 border text-center" style="display: none;">
                <div class="spinner-border text-primary mb-2" role="status"></div>
                <div class="fw-bold text-dark small">Mengakses Sensor Kamera...</div>
                <small class="text-muted">Izinkan browser mengakses kamera smartphone/laptop Anda</small>
            </div>
        </div>
        <div class="mt-2">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-1.5 rounded-pill fw-semibold" id="cameraStatusBadge" style="font-size: 0.78rem;">
                <i class="fa-solid fa-camera me-1"></i> Arahkan QR Code Kartu Siswa ke Bidik Kamera
            </span>
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

<!-- Include Library HTML5-QRCode untuk Scanner Kamera HP / Webcam -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

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
    let activeMode = 'camera'; // 'camera' or 'usb'
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

    // 3. Camera Scanner (Kamera HP / Webcam)
    function startCameraScanner() {
        if (isCameraRunning) return;

        const readerElem = document.getElementById('reader');
        const statusBadge = document.getElementById('cameraStatusBadge');
        const placeholder = document.getElementById('cameraLoadingPlaceholder');

        if (!readerElem) return;

        placeholder.style.display = 'block';
        readerElem.style.display = 'none';

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        const config = { 
            fps: 10, 
            qrbox: { width: 240, height: 240 },
            aspectRatio: 1.0
        };

        // Prioritas gunakan kamera belakang HP (facingMode: environment)
        html5QrCode.start(
            { facingMode: "environment" }, 
            config, 
            onCameraScanSuccess, 
            () => {} // onScanError (silent continuous)
        )
        .then(() => {
            isCameraRunning = true;
            placeholder.style.display = 'none';
            readerElem.style.display = 'block';
            statusBadge.className = 'badge bg-success bg-opacity-10 text-success border border-success px-3 py-1.5 rounded-pill fw-semibold';
            statusBadge.innerHTML = '<i class="fa-solid fa-video me-1"></i> Kamera Aktif &amp; Siap Mendeteksi Kartu QR';
        })
        .catch(err => {
            console.warn("Kamera belakang tidak ditemukan, mencoba kamera default:", err);
            // Fallback coba kamera apa saja yang tersedia (Webcam PC/Laptop)
            html5QrCode.start(
                { facingMode: "user" }, 
                config, 
                onCameraScanSuccess, 
                () => {}
            )
            .then(() => {
                isCameraRunning = true;
                placeholder.style.display = 'none';
                readerElem.style.display = 'block';
                statusBadge.className = 'badge bg-success bg-opacity-10 text-success border border-success px-3 py-1.5 rounded-pill fw-semibold';
                statusBadge.innerHTML = '<i class="fa-solid fa-video me-1"></i> Kamera Aktif &amp; Siap Mendeteksi Kartu QR';
            })
            .catch(finalErr => {
                console.error("Camera access denied or error:", finalErr);
                placeholder.style.display = 'none';
                readerElem.style.display = 'block';
                statusBadge.className = 'badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1.5 rounded-pill fw-semibold';
                statusBadge.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> Izin kamera belum diberikan. Silakan izinkan akses kamera di browser Anda.';
            });
        });
    }

    function stopCameraScanner() {
        if (html5QrCode && isCameraRunning) {
            html5QrCode.stop().then(() => {
                isCameraRunning = false;
            }).catch(err => console.error("Error stopping camera:", err));
        }
    }

    function onCameraScanSuccess(decodedText) {
        if (!canScanCamera) return;
        canScanCamera = false;

        processPresensi(decodedText, () => {
            // Cooldown 2.5 detik sebelum siap menerima scan kartu berikutnya
            setTimeout(() => {
                canScanCamera = true;
            }, 2500);
        });
    }

    // 4. USB Scanner Gun Input Listener
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

    // 5. Unified Core AJAX Process Scan
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

    // 6. Start Camera automatically on initial page load
    document.addEventListener('DOMContentLoaded', () => {
        startCameraScanner();
    });

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
</script>
@endsection
