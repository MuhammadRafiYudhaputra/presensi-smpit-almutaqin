<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Presensi Siswa SMP IT Al-Muttaqin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            padding: 2.5rem 3rem;
            background: #f8fafc;
        }

        .paper-container {
            background: #ffffff;
            max-width: 1050px;
            margin: 0 auto;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .table-print th, .table-print td {
            border: 1px solid #000 !important;
            padding: 6px 10px;
        }

        .control-bar {
            background: #ffffff;
            max-width: 1050px;
            margin: 0 auto 1.5rem;
            padding: 1.25rem 1.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            font-family: system-ui, -apple-system, sans-serif;
        }

        .editable-field {
            outline: none;
            border-bottom: 1px dashed transparent;
            transition: border 0.2s;
            display: inline-block;
            min-width: 150px;
        }

        .editable-field:hover, .editable-field:focus {
            border-bottom: 1px dashed #2563eb;
            background-color: rgba(37, 99, 235, 0.05);
        }

        @media print {
            .control-bar {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .paper-container {
                box-shadow: none !important;
                padding: 0 !important;
                max-width: 100% !important;
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

    <!-- Control Toolbar -->
    <div class="control-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                <i class="fa-solid fa-print me-1"></i> Cetak / Simpan PDF
            </button>
        </div>

        <div class="row g-3 pt-2 border-top">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-dark mb-1">Kota & Tanggal TTD:</label>
                <input type="text" id="inputKotaTgl" class="form-control form-control-sm" placeholder="Contoh: Garut, 18 Agustus 2026" oninput="syncSignature()">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-dark mb-1">Nama Kepala Sekolah:</label>
                <input type="text" id="inputKepsek" class="form-control form-control-sm" placeholder="Nama Lengkap & Gelar" oninput="syncSignature()">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-dark mb-1">NIP Kepala Sekolah:</label>
                <input type="text" id="inputNipKepsek" class="form-control form-control-sm" placeholder="NIP (atau - jika tidak ada)" oninput="syncSignature()">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-dark mb-1">Nama Wali Kelas (Opsional):</label>
                <input type="text" id="inputWali" class="form-control form-control-sm" placeholder="Nama Wali Kelas" oninput="syncSignature()">
            </div>
        </div>
    </div>

    <!-- Lembar Dokumen Cetak Laporan -->
    <div class="paper-container">
        <!-- Kop Surat -->
        <div class="header-kop">
            <h4 class="fw-bold text-uppercase m-0" style="letter-spacing: 1px;">YAYASAN AL-MUTAQIN</h4>
            <h2 class="fw-bold text-uppercase m-0" style="letter-spacing: 1.5px;">SMP IT AL-MUTTAQIN</h2>
            <p class="m-0 small">Sistem Monitoring Kehadiran Siswa dengan Notifikasi WhatsApp Otomatis</p>
        </div>

        <!-- Judul Laporan -->
        <div class="text-center mb-4">
            <h4 class="fw-bold text-uppercase text-decoration-underline mb-1">LAPORAN REKAPITULASI KEHADIRAN SISWA</h4>
            <p class="m-0" style="font-size: 0.95rem;">
                @if(($mode ?? 'bulanan') === 'semester')
                    Periode: Semester {{ ucfirst($semester ?? 'Ganjil') }} / {{ $tahun ?? date('Y') }}
                @elseif(($mode ?? 'bulanan') === 'harian')
                    Periode: Tanggal {{ $tanggal ?? date('Y-m-d') }}
                @else
                    Periode: Bulan {{ $bulan ?? date('m') }} / {{ $tahun ?? date('Y') }}
                @endif
                | Kelas: {{ $kelas->nama_kelas ?? 'Semua Kelas' }}
            </p>
        </div>

        <!-- Tabel Data -->
        <table class="table table-print table-bordered align-middle mb-4">
            <thead>
                <tr class="text-center" style="background-color: #f8fafc;">
                    <th style="width: 40px;">No</th>
                    <th style="width: 110px;">NISN</th>
                    <th>Nama Peserta Didik</th>
                    <th style="width: 100px;">Kelas</th>
                    <th style="width: 70px;">Hadir</th>
                    <th style="width: 80px;">Terlambat</th>
                    <th style="width: 70px;">Izin</th>
                    <th style="width: 70px;">Sakit</th>
                    <th style="width: 70px;">Alpa</th>
                    <th style="width: 90px;">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataLaporan as $index => $row)
                <tr>
                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $row->siswa->nisn }}</td>
                    <td>{{ $row->siswa->nama }}</td>
                    <td class="text-center">Kelas {{ $row->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td class="text-center">{{ $row->hadir }}</td>
                    <td class="text-center">{{ $row->terlambat }}</td>
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
        <div class="row mt-5 pt-3">
            <!-- Tanda Tangan Wali Kelas (Jika Ada) -->
            <div class="col-6 text-center" id="colWaliKelas">
                <p class="mb-1" id="dispKotaWali">Garut, {{ date('d F Y') }}</p>
                <p class="mb-1">Mengetahui,</p>
                <p class="fw-bold mb-5">Wali Kelas {{ $kelas->nama_kelas ?? '' }}</p>
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
                <p class="fw-bold mb-5">Kepala Sekolah SMP IT Al-Muttaqin</p>
                <br>
                <p class="fw-bold text-decoration-underline mb-0">
                    ( <span class="editable-field" id="dispNamaKepsek" contenteditable="true">H. Aep Saepudin, S.Pd.I, M.Pd</span> )
                </p>
                <small>NIP. <span class="editable-field" id="dispNipKepsek" contenteditable="true" style="min-width: 80px;">197508122005011003</span></small>
            </div>
        </div>
    </div>

<script>
    // Load stored values or defaults
    const defaultKota = "Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}";
    const defaultKepsek = localStorage.getItem('smpit_kepsek_nama') || "H. Aep Saepudin, S.Pd.I, M.Pd";
    const defaultNip = localStorage.getItem('smpit_kepsek_nip') || "197508122005011003";
    const defaultWali = "{{ $kelas->waliKelas->nama ?? 'Ustadz Ahmad, S.Pd' }}";

    document.getElementById('inputKotaTgl').value = defaultKota;
    document.getElementById('inputKepsek').value = defaultKepsek;
    document.getElementById('inputNipKepsek').value = defaultNip;
    document.getElementById('inputWali').value = defaultWali;

    syncSignature();

    function syncSignature() {
        const kota = document.getElementById('inputKotaTgl').value || defaultKota;
        const kepsek = document.getElementById('inputKepsek').value || '________________________';
        const nip = document.getElementById('inputNipKepsek').value || '-';
        const wali = document.getElementById('inputWali').value || '________________________';

        document.getElementById('dispKotaKepsek').innerText = kota;
        document.getElementById('dispKotaWali').innerText = kota;
        document.getElementById('dispNamaKepsek').innerText = kepsek;
        document.getElementById('dispNipKepsek').innerText = nip;
        document.getElementById('dispNamaWali').innerText = wali;

        // Save in localStorage for future prints
        localStorage.setItem('smpit_kepsek_nama', kepsek);
        localStorage.setItem('smpit_kepsek_nip', nip);
    }

    // Direct contentEditable sync to input fields
    document.getElementById('dispNamaKepsek').addEventListener('input', function() {
        document.getElementById('inputKepsek').value = this.innerText.trim();
        localStorage.setItem('smpit_kepsek_nama', this.innerText.trim());
    });

    document.getElementById('dispNipKepsek').addEventListener('input', function() {
        document.getElementById('inputNipKepsek').value = this.innerText.trim();
        localStorage.setItem('smpit_kepsek_nip', this.innerText.trim());
    });
</script>

</body>
</html>
