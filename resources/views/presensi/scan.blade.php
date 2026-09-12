@extends('layouts.app')

@section('content')
<style>
    .scanner-main-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 2.25rem 1.5rem;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.03);
        position: relative;
        max-width: 760px;
        margin: 0 auto;
    }

    .digital-clock-badge {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #0284c7;
        font-weight: 800;
        font-size: 1.25rem;
        letter-spacing: 0.5px;
        padding: 0.4rem 1.35rem;
        border-radius: 50rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .scanner-target-box {
        width: 190px;
        height: 190px;
        margin: 1.25rem auto 1.5rem;
        border-radius: 24px;
        border: 2px dashed #2563eb;
        background: rgba(37, 99, 235, 0.03);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        transition: all 0.3s ease;
    }

    .scanner-target-box:hover {
        border-color: #1d4ed8;
        background: rgba(37, 99, 235, 0.06);
    }

    .scanner-input-group {
        max-width: 580px;
        margin: 0 auto 1.25rem;
    }

    .scanner-input {
        background: #ffffff;
        border: 2px solid #2563eb;
        color: #0f172a;
        border-radius: 14px 0 0 14px !important;
        padding: 0.9rem 1.25rem;
        font-size: 1.05rem;
        font-weight: 600;
        box-shadow: none !important;
    }

    .scanner-input:focus {
        border-color: #1d4ed8;
        background: #f8fafc;
    }

    .scanner-btn {
        background: #2563eb;
        border: 2px solid #2563eb;
        color: #ffffff;
        font-weight: 700;
        border-radius: 0 14px 14px 0 !important;
        padding: 0.9rem 1.75rem;
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
        border-radius: 18px;
        padding: 1.75rem;
        max-width: 580px;
        margin: 0 auto;
        text-align: center;
        min-height: 130px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .pulse-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        background-color: #16a34a;
        border-radius: 50%;
        margin-right: 6px;
        animation: pulseAnimation 1.5s infinite;
    }

    @keyframes pulseAnimation {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(22, 163, 74, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
    }
</style>

<div class="scanner-main-card text-center">
    <!-- Top Digital Clock Header -->
    <div class="d-flex justify-content-center mb-3">
        <div class="digital-clock-badge shadow-sm" id="liveClockBadge">
            <i class="fa-regular fa-clock text-primary"></i>
            <span id="liveClock">--.--.--</span>
        </div>
    </div>

    <!-- Scanner Target Visual -->
    <div class="scanner-target-box">
        <i class="fa-solid fa-qrcode fs-1 mb-2 text-primary"></i>
        <span class="fw-bold text-dark fs-6 d-block">SCANNER USB AKTIF</span>
        <small class="text-muted" style="font-size: 0.8rem;">Dekatkan Kartu QR Siswa</small>
    </div>

    <!-- Ready Status Indicator -->
    <div class="mb-3">
        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3.5 py-2 rounded-pill fw-semibold shadow-sm" style="font-size: 0.85rem;">
            <span class="pulse-indicator"></span> Siap Memindai Barcode
        </span>
    </div>

    <!-- Scanner Input Form -->
    <form id="formScan" onsubmit="event.preventDefault(); submitScan();" class="scanner-input-group">
        <div class="input-group shadow-sm">
            <input 
                type="text" 
                id="qrInput" 
                class="form-control scanner-input" 
                placeholder="Arahkan scanner ke kartu QR..." 
                autocomplete="off" 
                autofocus
            >
            <button type="submit" class="btn scanner-btn" id="btnSubmitScan">
                <i class="fa-solid fa-qrcode"></i> Scan
            </button>
        </div>
        <small class="text-muted d-block mt-2" style="font-size: 0.8rem;">
            <i class="fa-solid fa-circle-info text-primary me-1"></i> Sensor scanner akan otomatis memasukkan data dan memproses presensi secara instan.
        </small>
    </form>

    <!-- Result Display Card -->
    <div class="result-display-card shadow-sm mt-3" id="resultContainer">
        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary mb-2 d-inline-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
            <i class="fa-solid fa-id-card-clip fs-4"></i>
        </div>
        <h6 class="fw-bold text-dark mb-1">Siap Menerima Presensi Siswa</h6>
        <small class="text-muted">Arahkan sinar alat scanner pada QR Code kartu siswa.</small>
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

    // 2. Auto-Focus Handling for USB Scanner
    const qrInput = document.getElementById('qrInput');
    const resultContainer = document.getElementById('resultContainer');
    let scanTimeout = null;
    let resultResetTimeout = null;

    function resetResultDisplay() {
        resultContainer.innerHTML = `
            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary mb-2 d-inline-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                <i class="fa-solid fa-id-card-clip fs-4"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">Siap Menerima Presensi Siswa</h6>
            <small class="text-muted">Arahkan sinar alat scanner pada QR Code kartu siswa.</small>
        `;
    }

    function scheduleResultReset() {
        if (resultResetTimeout) clearTimeout(resultResetTimeout);
        resultResetTimeout = setTimeout(() => {
            resetResultDisplay();
            resultResetTimeout = null;
        }, 8000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (qrInput) qrInput.focus();
    });

    document.addEventListener('click', (e) => {
        if (qrInput && !e.target.closest('button') && !e.target.closest('a') && !e.target.closest('input')) {
            qrInput.focus();
        }
    });

    // Deteksi input dari USB Scanner gun
    qrInput.addEventListener('input', function() {
        if (scanTimeout) clearTimeout(scanTimeout);
        if (this.value.length >= 6) {
            scanTimeout = setTimeout(() => submitScan(), 250);
        }
    });

    qrInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (scanTimeout) clearTimeout(scanTimeout);
            submitScan();
        }
    });

    function submitScan() {
        const token = qrInput.value.trim();
        if (!token) {
            qrInput.focus();
            return;
        }

        if (resultResetTimeout) clearTimeout(resultResetTimeout);
        qrInput.disabled = true;
        processPresensi(token, () => {
            qrInput.value = '';
            qrInput.disabled = false;
            qrInput.focus();
        });
    }

    // 3. Core AJAX Process Scan
    function processPresensi(token, callback) {
        token = token.trim();
        if (!token) {
            if (callback) callback();
            return;
        }

        const container = resultContainer;
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
            scheduleResultReset();
        })
        .catch(err => {
            console.error('Scan Error:', err);
            container.innerHTML = `
                <div class="text-danger fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i> Terjadi kesalahan koneksi server. Silakan coba lagi.</div>
            `;
            scheduleResultReset();
        })
        .finally(() => {
            if (callback) callback();
        });
    }

    // 4. Audio Synthesizer Beeps
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
