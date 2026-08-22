<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Presensi Siswa SMP IT Al-Muttaqin</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #f1f5f9;
            margin: 0;
            padding: 0;
        }

        /* Fixed / Sticky Top Control Bar */
        .control-bar-wrapper {
            position: sticky;
            top: 0;
            z-index: 1050;
            background: rgba(241, 245, 249, 0.95);
            backdrop-filter: blur(8px);
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #cbd5e1;
        }

        .control-bar {
            background: #ffffff;
            max-width: 860px;
            margin: 0 auto;
            padding: 0.85rem 1.15rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            font-family: system-ui, -apple-system, sans-serif;
        }

        /* Sized & Proportionate Paper Container */
        .paper-container {
            background: #ffffff;
            max-width: 860px;
            margin: 1.25rem auto 3rem;
            padding: 2.25rem 2.75rem;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.06);
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 0.6rem;
            margin-bottom: 1.25rem;
        }

        .table-print {
            font-size: 0.82rem;
        }

        .table-print th, .table-print td {
            border: 1px solid #000 !important;
            padding: 5px 8px;
        }

        .editable-field {
            outline: none;
            border-bottom: 1px dashed transparent;
            transition: border 0.2s;
            display: inline-block;
            min-width: 140px;
        }

        .editable-field:hover, .editable-field:focus {
            border-bottom: 1px dashed #2563eb;
            background-color: rgba(37, 99, 235, 0.05);
        }

        @media print {
            .control-bar-wrapper {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .paper-container {
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                border: none !important;
                border-radius: 0 !important;
            }
            .editable-field {
                border: none !important;
                background: transparent !important;
            }
        }
    </style>
</head>
<body>

    @if(request()->get('format') !== 'doc')
    <!-- Fixed / Sticky Top Control Toolbar -->
    <div class="control-bar-wrapper">
        <div class="control-bar">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-file-pdf text-danger fs-4"></i>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Pengaturan Cetak Laporan Presensi</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">Dasar perhitungan: <strong>{{ $hariEfektif }} Hari Efektif</strong>. Keterlambatan dicatat terpisah untuk catatan BK.</small>
                    </div>
                </div>
                <button onclick="window.print()" class="btn btn-sm btn-primary rounded-pill px-3.5 py-1.5 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                    <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
                </button>
            </div>

            <div class="row g-2 pt-2 border-top">
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-bold text-dark mb-0.5" style="font-size: 0.75rem;">Kota &amp; Tanggal TTD:</label>
                    <input type="text" id="inputKotaTgl" class="form-control form-control-sm rounded-2" style="font-size: 0.78rem;" placeholder="Garut, {{ date('d F Y') }}" oninput="syncSignature()">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-bold text-dark mb-0.5" style="font-size: 0.75rem;">Nama Kepala Sekolah:</label>
                    <input type="text" id="inputKepsek" class="form-control form-control-sm rounded-2" style="font-size: 0.78rem;" placeholder="Nama Lengkap &amp; Gelar" oninput="syncSignature()">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-bold text-dark mb-0.5" style="font-size: 0.75rem;">NIP Kepala Sekolah:</label>
                    <input type="text" id="inputNipKepsek" class="form-control form-control-sm rounded-2" style="font-size: 0.78rem;" placeholder="NIP (atau -)" oninput="syncSignature()">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-bold text-dark mb-0.5" style="font-size: 0.75rem;">Nama Wali Kelas (Opsional):</label>
                    <input type="text" id="inputWali" class="form-control form-control-sm rounded-2" style="font-size: 0.78rem;" placeholder="Nama Wali Kelas" oninput="syncSignature()">
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Lembar Dokumen Cetak Laporan (Proporsional & Rapi) -->
    <div class="paper-container">
        <!-- Kop Surat -->
        <div class="header-kop position-relative d-flex align-items-center justify-content-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SMP IT Al-Muttaqin" style="width: 62px; height: 62px; object-fit: contain; position: absolute; left: 8px; top: 2px;">
            <div>
                <h5 class="fw-bold text-uppercase m-0" style="letter-spacing: 0.8px; font-size: 1.05rem;">YAYASAN AL-MUTAQIN</h5>
                <h3 class="fw-bold text-uppercase m-0" style="letter-spacing: 1.2px; font-size: 1.35rem;">SMP IT AL-MUTTAQIN</h3>
                <p class="m-0" style="font-size: 0.82rem;">Sistem Monitoring Kehadiran Siswa dengan Notifikasi WhatsApp Otomatis</p>
                <small style="font-size: 0.75rem; color: #475569;">Tarogong Kaler - Garut, Jawa Barat</small>
            </div>
        </div>

        <!-- Judul Laporan -->
        <div class="text-center mb-3">
            <h5 class="fw-bold text-uppercase text-decoration-underline mb-1" style="font-size: 1.05rem;">LAPORAN REKAPITULASI KEHADIRAN SISWA</h5>
            <p class="m-0 text-dark" style="font-size: 0.82rem;">
                @if(($mode ?? 'bulanan') === 'semester')
                    Periode: Semester {{ ucfirst($semester ?? 'Ganjil') }} / {{ $tahun ?? date('Y') }}
                @elseif(($mode ?? 'bulanan') === 'harian')
                    Periode: Tanggal {{ $tanggal ?? date('Y-m-d') }}
                @else
                    Periode: Bulan {{ $bulan ?? date('m') }} / {{ $tahun ?? date('Y') }}
                @endif
                | Kelas: {{ $kelas->nama_kelas ?? 'Semua Kelas' }}
                @if(($mode ?? 'bulanan') !== 'harian')
                    | Dasar Perhitungan: {{ $hariEfektif }} Hari Efektif
                @endif
            </p>
        </div>

        <!-- Tabel Data Presensi -->
        <table class="table table-print table-bordered align-middle mb-4">
            <thead>
                <tr class="text-center" style="background-color: #f8fafc;">
                    <th style="width: 35px;">No</th>
                    <th style="width: 95px;">NISN</th>
                    <th>Nama Peserta Didik</th>
                    <th style="width: 80px;">Kelas</th>
                    <th style="width: 60px;" title="Total Masuk Sekolah">Hadir</th>
                    <th style="width: 75px;" title="Catatan Keterlambatan untuk Pihak BK">Terlambat (BK)</th>
                    <th style="width: 55px;">Izin</th>
                    <th style="width: 55px;">Sakit</th>
                    <th style="width: 55px;">Alpa</th>
                    <th style="width: 80px;">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataLaporan as $index => $row)
                <tr>
                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $row->siswa->nisn }}</td>
                    <td>{{ $row->siswa->nama }}</td>
                    <td class="text-center">Kelas {{ $row->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td class="text-center fw-bold">{{ $row->hadir }}</td>
                    <td class="text-center">
                        {{ $row->terlambat }}
                        @if($row->terlambat >= 3)
                            <span style="font-size: 0.72rem; color: #b45309; font-weight: bold;">(BK)</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $row->izin }}</td>
                    <td class="text-center">{{ $row->sakit }}</td>
                    <td class="text-center">{{ $row->alpa }}</td>
                    <td class="text-center fw-bold">{{ $row->persentase }}%</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-4 text-muted">Tidak ada data rekapitulasi untuk periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Kolom Tanda Tangan Pejabat (Kepala Sekolah & Wali Kelas) -->
        <div class="row mt-4 pt-2" style="font-size: 0.88rem;">
            <!-- Tanda Tangan Wali Kelas (Jika Ada) -->
            <div class="col-6 text-center" id="colWaliKelas">
                <p class="mb-1" id="dispKotaWali">Garut, {{ date('d F Y') }}</p>
                <p class="mb-1">Mengetahui,</p>
                <p class="fw-bold mb-4">Wali Kelas {{ $kelas->nama_kelas ?? '' }}</p>
                <br>
                <p class="fw-bold text-decoration-underline mb-0">
                    ( <span class="editable-field" id="dispNamaWali" contenteditable="true">{{ $kelas->waliKelas->nama ?? 'Ustadz Ahmad, S.Pd' }}</span> )
                </p>
                <small>NIP. <span class="editable-field" id="dispNipWali" contenteditable="true" style="min-width: 80px;">{{ $kelas->waliKelas->nip ?? '-' }}</span></small>
            </div>

            <!-- Tanda Tangan Kepala Sekolah -->
            <div class="col-6 text-center ms-auto" id="colKepsek">
                <p class="mb-1" id="dispKotaKepsek">Garut, {{ date('d F Y') }}</p>
                <p class="mb-1">Mengetahui,</p>
                <p class="fw-bold mb-4">Kepala Sekolah SMP IT Al-Muttaqin</p>
                <br>
                <p class="fw-bold text-decoration-underline mb-0">
                    ( <span class="editable-field" id="dispNamaKepsek" contenteditable="true">H. Aep Saepudin, S.Pd.I, M.Pd</span> )
                </p>
                <small>NIP. <span class="editable-field" id="dispNipKepsek" contenteditable="true" style="min-width: 80px;">197508122005011003</span></small>
            </div>
        </div>
    </div>

@if(request()->get('format') !== 'doc')
<script>
    // Load stored values or defaults
    const defaultKota = "Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}";
    const defaultKepsek = localStorage.getItem('smpit_kepsek_nama') || "H. Aep Saepudin, S.Pd.I, M.Pd";
    const defaultNip = localStorage.getItem('smpit_kepsek_nip') || "197508122005011003";
    const defaultWali = "{{ $kelas->waliKelas->nama ?? 'Ustadz Ahmad, S.Pd' }}";

    if (document.getElementById('inputKotaTgl')) {
        document.getElementById('inputKotaTgl').value = defaultKota;
        document.getElementById('inputKepsek').value = defaultKepsek;
        document.getElementById('inputNipKepsek').value = defaultNip;
        document.getElementById('inputWali').value = defaultWali;
        syncSignature();
    }

    function syncSignature() {
        const inputKota = document.getElementById('inputKotaTgl');
        if (!inputKota) return;
        const kota = inputKota.value || defaultKota;
        const kepsek = document.getElementById('inputKepsek').value || '________________________';
        const nip = document.getElementById('inputNipKepsek').value || '-';
        const wali = document.getElementById('inputWali').value || '________________________';

        if (document.getElementById('dispKotaKepsek')) document.getElementById('dispKotaKepsek').innerText = kota;
        if (document.getElementById('dispKotaWali')) document.getElementById('dispKotaWali').innerText = kota;
        if (document.getElementById('dispNamaKepsek')) document.getElementById('dispNamaKepsek').innerText = kepsek;
        if (document.getElementById('dispNipKepsek')) document.getElementById('dispNipKepsek').innerText = nip;
        if (document.getElementById('dispNamaWali')) document.getElementById('dispNamaWali').innerText = wali;

        // Save in localStorage for future prints
        localStorage.setItem('smpit_kepsek_nama', kepsek);
        localStorage.setItem('smpit_kepsek_nip', nip);
    }

    // Direct contentEditable sync to input fields
    const dispNamaKepsek = document.getElementById('dispNamaKepsek');
    if (dispNamaKepsek) {
        dispNamaKepsek.addEventListener('input', function() {
            if (document.getElementById('inputKepsek')) document.getElementById('inputKepsek').value = this.innerText.trim();
            localStorage.setItem('smpit_kepsek_nama', this.innerText.trim());
        });
    }

    const dispNipKepsek = document.getElementById('dispNipKepsek');
    if (dispNipKepsek) {
        dispNipKepsek.addEventListener('input', function() {
            if (document.getElementById('inputNipKepsek')) document.getElementById('inputNipKepsek').value = this.innerText.trim();
            localStorage.setItem('smpit_kepsek_nip', this.innerText.trim());
        });
    }
</script>
@endif

</body>
</html>
