@extends('layouts.app')

@section('content')
<style>
    .scanner-dark-card {
        background: #0f172a;
        border-radius: 24px;
        padding: 2.5rem 2rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
    }

    .digital-clock {
        color: #facc15;
        font-weight: 800;
        font-size: 1.6rem;
        letter-spacing: 1px;
        position: absolute;
        top: 1.75rem;
        left: 2rem;
    }

    .scanner-target-circle {
        width: 200px;
        height: 200px;
        margin: 1.5rem auto 1rem;
        border-radius: 50%;
        border: 2px dashed #00e676;
        background: radial-gradient(circle, rgba(0, 230, 118, 0.1) 0%, rgba(15, 23, 42, 0) 70%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        box-shadow: 0 0 25px rgba(0, 230, 118, 0.2);
    }

    .scanner-target-circle::after {
        content: '';
        position: absolute;
        top: 15%;
        left: 10%;
        right: 10%;
        height: 2px;
        background: #00e676;
        box-shadow: 0 0 10px #00e676, 0 0 20px #00e676;
        animation: scanBeam 2.2s infinite ease-in-out;
    }

    @keyframes scanBeam {
        0%, 100% { top: 20%; opacity: 0.3; }
        50% { top: 80%; opacity: 1; }
    }

    .scanner-input-group {
        max-width: 650px;
        margin: 1.5rem auto 1.5rem;
    }

    .scanner-input {
        background: transparent;
        border: 2px solid #00e676;
        color: #ffffff;
        border-radius: 14px 0 0 14px;
        padding: 0.85rem 1.25rem;
        font-size: 1.05rem;
        letter-spacing: 1px;
    }

    .scanner-input:focus {
        background: transparent;
        border-color: #00e676;
        color: #ffffff;
        box-shadow: 0 0 15px rgba(0, 230, 118, 0.4);
    }

    .scanner-input::placeholder {
        color: #64748b;
        font-size: 0.95rem;
    }

    .scanner-btn {
        background: #059669;
        border: 2px solid #059669;
        color: #ffffff;
        font-weight: 700;
        border-radius: 0 14px 14px 0;
        padding: 0.85rem 1.75rem;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s ease;
    }

    .scanner-btn:hover {
        background: #10b981;
        border-color: #10b981;
        color: #ffffff;
    }

    .result-display-card {
        background: rgba(30, 41, 59, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        padding: 2rem;
        max-width: 650px;
        margin: 0 auto;
        text-align: center;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="scanner-dark-card text-center">
    <!-- Live Digital Clock -->
    <div class="digital-clock" id="liveClock">--.--.--</div>

    <!-- Center Target Circle -->
    <div class="scanner-target-circle">
        <i class="fa-solid fa-table-cells-large text-warning fs-3 mb-1"></i>
        <span class="fw-bold text-white fs-6 d-block">SCAN KARTU QR</span>
        <small class="text-secondary" style="font-size: 0.75rem;">Arahkan QR Code ke Scanner USB</small>
    </div>

    <!-- Status Subtext -->
    <div class="text-success small fw-semibold mb-3">
        <i class="fa-solid fa-barcode me-1"></i> Sensor USB Scanner terhubung. Dekatkan QR Code siswa.
    </div>

    <!-- Input Scanner Form -->
    <form id="formScan" onsubmit="event.preventDefault(); submitScan();" class="scanner-input-group">
        <div class="input-group">
            <input type="text" id="qrInput" class="form-control scanner-input" placeholder="Hasil scan USB akan tampil di sini..." autocomplete="off" autofocus>
            <button type="submit" class="btn scanner-btn">
                <i class="fa-solid fa-qrcode"></i> Scan
            </button>
        </div>
    </form>

    <!-- Result Display Card -->
    <div class="result-display-card" id="resultContainer">
        <div class="bg-secondary bg-opacity-25 p-3 rounded-circle text-white mb-3 d-inline-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
            <i class="fa-solid fa-user-check fs-4"></i>
        </div>
        <h6 class="fw-semibold text-white mb-0">Silakan scan Kartu Presensi siswa pada alat USB scanner.</h6>
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

    // 2. Auto Focus QR Input
    const qrInput = document.getElementById('qrInput');
    qrInput.focus();
    document.addEventListener('click', () => qrInput.focus());

    // 3. Fast submit on Enter / USB scan
    let scanTimeout = null;
    qrInput.addEventListener('input', function() {
        if (scanTimeout) clearTimeout(scanTimeout);
        if (this.value.length >= 6) {
            scanTimeout = setTimeout(() => {
                submitScan();
            }, 250);
        }
    });

    // 4. AJAX Process Scan
    function submitScan() {
        const token = qrInput.value.trim();
        if (!token) return;

        qrInput.disabled = true;

        fetch("{{ route('presensi.scan.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ qr_token: token })
        })
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('resultContainer');
            if (data.success) {
                const isPulang = (data.type === 'pulang');
                const isTerlambat = (data.status === 'TERLAMBAT');
                const statusBadge = isPulang 
                    ? `<span class="badge bg-primary px-3 py-2 rounded-pill fs-6"><i class="fa-solid fa-door-open me-1"></i> PULANG SEKOLAH</span>`
                    : (isTerlambat 
                        ? `<span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6"><i class="fa-solid fa-clock me-1"></i> TERLAMBAT</span>`
                        : `<span class="badge bg-success px-3 py-2 rounded-pill fs-6"><i class="fa-solid fa-circle-check me-1"></i> HADIR TEPAT WAKTU</span>`);

                container.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                        <div class="bg-success bg-opacity-25 p-3 rounded-circle text-success" style="width: 55px; height: 55px; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-check fs-3"></i>
                        </div>
                        <div class="text-start">
                            <h5 class="fw-bold text-white mb-0">${data.siswa.nama}</h5>
                            <small class="text-secondary">Kelas ${data.siswa.kelas ? data.siswa.kelas.nama_kelas : '-'} | NISN: ${data.siswa.nisn}</small>
                        </div>
                    </div>
                    <div class="my-2">${statusBadge}</div>
                    <small class="text-success mt-1"><i class="fa-brands fa-whatsapp me-1"></i> Notifikasi WhatsApp Terkirim ke Orang Tua (${data.waktu})</small>
                `;
            } else if (data.type === 'belum_pulang') {
                container.innerHTML = `
                    <div class="bg-warning bg-opacity-25 p-3 rounded-circle text-warning mb-2" style="width: 55px; height: 55px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-clock-rotate-left fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-warning mb-1">BELUM WAKTUNYA PULANG!</h6>
                    <small class="text-white">${data.message}</small>
                `;
            } else {
                container.innerHTML = `
                    <div class="bg-danger bg-opacity-25 p-3 rounded-circle text-danger mb-2" style="width: 55px; height: 55px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-xmark fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-danger mb-1">Presensi Gagal!</h6>
                    <small class="text-white">${data.message}</small>
                `;
            }
        })
        .catch(err => {
            document.getElementById('resultContainer').innerHTML = `
                <div class="text-danger fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i> Terjadi kesalahan koneksi server.</div>
            `;
        })
        .finally(() => {
            qrInput.value = '';
            qrInput.disabled = false;
            qrInput.focus();
        });
    }
</script>
@endsection
