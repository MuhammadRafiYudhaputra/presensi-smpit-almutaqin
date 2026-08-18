<?php $__env->startSection('content'); ?>
<style>
    :root {
        --primary-glow: #00E676;
        --warning-glow: #FFD600;
        --danger-glow: #FF1744;
    }

    .kiosk-card {
        background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        padding: 2.5rem 2rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        color: #fff;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .tap-target-zone {
        width: 200px;
        height: 200px;
        margin: 1rem auto;
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
        0% { box-shadow: 0 0 0 0 rgba(0, 230, 118, 0.4); transform: scale(1); }
        50% { box-shadow: 0 0 30px 12px rgba(0, 230, 118, 0.2); transform: scale(1.03); }
        100% { box-shadow: 0 0 0 0 rgba(0, 230, 118, 0.4); transform: scale(1); }
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

    .avatar-circle {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2.2rem;
    }

    .scanner-visible-box {
        background: rgba(15, 23, 42, 0.8);
        border: 2px solid rgba(0, 230, 118, 0.4);
        border-radius: 16px;
        padding: 0.75rem 1.2rem;
        color: #00E676;
        font-family: monospace;
        font-size: 1.1rem;
        width: 100%;
        text-align: center;
        box-shadow: inset 0 2px 8px rgba(0,0,0,0.5);
        transition: all 0.2s ease;
    }

    .scanner-visible-box:focus {
        outline: none;
        border-color: #00E676;
        box-shadow: 0 0 15px rgba(0, 230, 118, 0.4);
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="kiosk-card">
            <!-- Header Badge & Live Clock -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-end">
                    <h3 class="fw-extrabold text-warning m-0" id="live_clock">--:--:--</h3>
                </div>
            </div>

            <!-- HARDWARE SCANNER TAP ZONE -->
            <div class="tap-target-zone">
                <div class="laser-line"></div>
                <i class="fa-solid fa-qrcode fs-1 text-warning mb-2"></i>
                <span class="fw-bold text-white fs-5">SCAN KARTU QR</span>
                <small class="text-white-50 fs-7">Arahkan QR Code ke Scanner USB</small>
            </div>
            
            <p class="text-white-50 small mb-3">
                <i class="fa-solid fa-keyboard text-success me-1"></i> 
                Sensor USB Scanner terhubung. Dekatkan QR Code siswa.
            </p>

            <!-- Visible Input Field for USB Scanner / Manual Testing -->
            <form id="scan_form" onsubmit="event.preventDefault(); submitManualScan();" class="row justify-content-center g-2 mb-3">
                <div class="col-md-8 col-10">
                    <input type="text" id="scanner_visible_input" class="scanner-visible-box" placeholder="Hasil scan USB akan tampil di sini..." autocomplete="off" autofocus>
                </div>
                <div class="col-md-2 col-4">
                    <button type="submit" class="btn btn-success fw-bold w-100 py-2 rounded-3">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Scan
                    </button>
                </div>
            </form>

            <!-- RESULT STATUS FEEDBACK CARD -->
            <div id="status_display" class="status-display status-idle mt-3">
                <div id="idle_view">
                    <div class="avatar-circle text-secondary">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <h5 class="fw-bold mb-1 text-white">Silakan scan Kartu Presensi siswa pada alat USB scanner.</h5>
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
</div>

<!-- Audio Effects -->
<audio id="sound_success" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>
<audio id="sound_error" src="https://assets.mixkit.co/active_storage/sfx/2573/2573-preview.mp3" preload="auto"></audio>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const soundSuccess = document.getElementById('sound_success');
    const soundError = document.getElementById('sound_error');

    let isProcessing = false;
    let scanBuffer = "";
    let lastKeyTime = Date.now();
    let scanTimeout = null;

    function getScannerInput() {
        return document.getElementById('scanner_visible_input');
    }

    // Auto Focus Input Box
    function ensureFocus() {
        const inp = getScannerInput();
        if (inp && document.activeElement !== inp) {
            inp.focus();
        }
    }

    window.onload = function() {
        ensureFocus();
        setInterval(ensureFocus, 2000);
    };

    // Live Clock Update
    setInterval(() => {
        const clockEl = document.getElementById('live_clock');
        if (clockEl) {
            const now = new Date();
            clockEl.innerText = now.toLocaleTimeString('id-ID');
        }
    }, 1000);

    // Global USB HID Barcode/QR Scanner Key Listener
    document.addEventListener('keydown', function(e) {
        const currentTime = Date.now();
        const inp = getScannerInput();

        if (currentTime - lastKeyTime > 200) {
            scanBuffer = "";
        }
        lastKeyTime = currentTime;

        if (e.key === 'Enter' || e.key === 'Tab') {
            const tokenFromInput = inp ? inp.value.trim() : "";
            if (tokenFromInput !== "") {
                e.preventDefault();
                if (inp) inp.value = "";
                scanBuffer = "";
                processScanToken(tokenFromInput);
            } else if (scanBuffer.trim().length >= 3) {
                e.preventDefault();
                const token = scanBuffer.trim();
                scanBuffer = "";
                if (inp) inp.value = "";
                processScanToken(token);
            }
        } else if (e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
            scanBuffer += e.key;

            clearTimeout(scanTimeout);
            scanTimeout = setTimeout(() => {
                const tokenFromInput = inp ? inp.value.trim() : "";
                if (tokenFromInput.length >= 3) {
                    if (inp) inp.value = "";
                    scanBuffer = "";
                    processScanToken(tokenFromInput);
                } else if (scanBuffer.length >= 4 && (Date.now() - lastKeyTime) >= 100) {
                    const token = scanBuffer.trim();
                    scanBuffer = "";
                    if (inp) inp.value = "";
                    processScanToken(token);
                }
            }, 150);
        }
    });

    // Submit form manually
    function submitManualScan() {
        const inp = getScannerInput();
        const token = inp ? inp.value.trim() : "";
        if (inp) inp.value = "";
        scanBuffer = "";
        if (token !== "") {
            processScanToken(token);
        }
    }

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
        resultStudentName.innerText = `Token: ${token}`;
        resultMessage.innerText = "Mengecek data siswa & mengirim WhatsApp...";
        resultWaStatus.innerText = "Koneksi ke Server Fonnte API...";

        fetch("<?php echo e(route('presensi.scan.store')); ?>", {
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

                if (data.type === 'pulang') {
                    statusDisplay.className = "status-display status-success";
                    resultIconBg.style.background = 'rgba(13, 202, 240, 0.2)';
                    resultIcon.className = 'fa-solid fa-door-open text-info';
                    resultTitle.innerText = "PRESENSI PULANG BERHASIL";
                } else if (data.status === 'TERLAMBAT') {
                    statusDisplay.className = "status-display status-warning";
                    resultIconBg.style.background = 'rgba(255, 214, 0, 0.2)';
                    resultIcon.className = 'fa-solid fa-triangle-exclamation text-warning';
                    resultTitle.innerText = "PRESENSI MASUK (TERLAMBAT)";
                } else {
                    statusDisplay.className = "status-display status-success";
                    resultIconBg.style.background = 'rgba(0, 230, 118, 0.2)';
                    resultIcon.className = 'fa-solid fa-circle-check text-success';
                    resultTitle.innerText = "PRESENSI MASUK BERHASIL";
                }

                resultStudentName.innerText = data.siswa ? `${data.siswa.nama} (${data.siswa.kelas ? 'Kelas ' + data.siswa.kelas.nama_kelas : '-'})` : '';
                resultMessage.innerText = data.message;
                resultWaStatus.innerHTML = `<span class="text-success"><i class="fa-solid fa-check me-1"></i> Notifikasi WhatsApp Terkirim ke Orang Tua (${data.siswa.orang_tua ? data.siswa.orang_tua.no_wa : '-'})</span>`;
            } else {
                try { soundError.play(); } catch(e){}

                if (data.type === 'belum_pulang') {
                    statusDisplay.className = "status-display status-warning";
                    resultIconBg.style.background = 'rgba(255, 214, 0, 0.2)';
                    resultIcon.className = 'fa-solid fa-clock text-warning';
                    resultTitle.innerText = "BELUM WAKTUNYA PULANG";
                    resultWaStatus.innerText = "Presensi pulang belum dibuka sesuai jadwal operasional sekolah.";
                } else {
                    statusDisplay.className = "status-display status-danger";
                    resultIconBg.style.background = 'rgba(255, 23, 68, 0.2)';
                    resultIcon.className = 'fa-solid fa-circle-xmark text-danger';
                    resultTitle.innerText = "INFORMASI PRESENSI";
                    resultWaStatus.innerText = "Notifikasi WA tidak dikirim.";
                }

                resultStudentName.innerText = data.siswa ? `${data.siswa.nama} (${data.siswa.kelas ? 'Kelas ' + data.siswa.kelas.nama_kelas : '-'})` : 'Kartu Tidak Dikenali';
                resultMessage.innerText = data.message;
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
                const inp = getScannerInput();
                if (inp) inp.value = "";
                scanBuffer = "";
                isProcessing = false;
                ensureFocus();
            }, 3500);
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/presensi/scan.blade.php ENDPATH**/ ?>