<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Siswa SMP IT Al-Mutaqin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            padding: 2rem;
            background: #fff;
        }

        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }

        .table-print th, .table-print td {
            border: 1px solid #000 !important;
            padding: 6px 10px;
        }

        @media print {
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn btn-primary rounded-pill mb-4 btn-print">
        <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
    </button>

    <div class="header-kop">
        <h3 class="fw-bold text-uppercase m-0">YAYASAN AL-MUTAQIN</h3>
        <h2 class="fw-bold text-uppercase m-0">SMP IT AL-MUTAQIN</h2>
        <p class="m-0 small">Sistem Monitoring Kehadiran Siswa dengan Notifikasi WhatsApp Otomatis</p>
    </div>

    <div class="text-center mb-4">
        <h4 class="fw-bold text-uppercase text-decoration-underline">LAPORAN REKAPITULASI KEHADIRAN SISWA</h4>
        <p class="m-0">
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

    <table class="table table-print table-bordered align-middle">
        <thead>
            <tr class="text-center">
                <th style="width: 40px;">No</th>
                <th>NISN</th>
                <th>Nama Peserta Didik</th>
                <th>Kelas</th>
                <th>Hadir</th>
                <th>Terlambat</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Alpa</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataLaporan as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
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

    <div class="row mt-5">
        <div class="col-8"></div>
        <div class="col-4 text-center">
            <p class="mb-1">Tasikmalaya, {{ date('d F Y') }}</p>
            <p class="mb-1">Mengetahui,</p>
            <p class="fw-bold mb-5">Kepala Sekolah SMP IT Al-Mutaqin</p>
            <br>
            <p class="fw-bold text-decoration-underline mb-0">( ________________________ )</p>
            <small>NIP. -</small>
        </div>
    </div>

</body>
</html>
