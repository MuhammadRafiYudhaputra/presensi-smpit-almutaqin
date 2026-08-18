<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            padding: 1.5rem;
            background: #fff;
        }

        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .table-print th, .table-print td {
            border: 1px solid #000 !important;
            padding: 6px 8px;
            font-size: 0.9rem;
        }

        .toolbar-panel {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 2rem;
            font-family: system-ui, -apple-system, sans-serif;
        }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; background: #fff; }
        }
    </style>
</head>
<body>

    <?php
        $mode = $mode ?? request('mode', 'bulan');
        $semester = $semester ?? request('semester', 1);
        $tahunAjaran = $tahunAjaran ?? request('tahun_ajaran', date('Y').'/'.(date('Y')+1));
        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulanText = $namaBulan[(int)$bulan] ?? $bulan;
        $tanggalHariIni = 'Garut, ' . date('d') . ' ' . ($namaBulan[(int)date('n')] ?? date('F')) . ' ' . date('Y');
        $periodeText = ($mode === 'semester') 
            ? 'Semester ' . ($semester ?? 1) . ' (' . (($semester ?? 1) == 1 ? 'Ganjil' : 'Genap') . ') Tahun Ajaran ' . $tahunAjaran 
            : 'Bulan ' . $bulanText . ' ' . $tahun;
    ?>

    <!-- Toolbar Setting Tanda Tangan & Tombol Cetak -->
    <div class="toolbar-panel no-print shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0 text-primary">
                <i class="fa-solid fa-pen-nib me-1"></i> Pengaturan Tanda Tangan Laporan & Cetak Rapor
            </h6>
            <button onclick="triggerPrint()" class="btn btn-primary rounded-pill px-4 fw-bold">
                <i class="fa-solid fa-print me-1"></i> Cetak Laporan / PDF
            </button>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-dark mb-1">Nama Kepala Sekolah</label>
                <input type="text" id="input_nama_kepala" class="form-control form-control-sm" placeholder="Contoh: H. Ahmad, S.Pd., M.Pd." oninput="updateSignature()" value="<?php echo e(request('nama_kepala', 'H. Ahmad, S.Pd., M.Pd.')); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-dark mb-1">NIP Kepala Sekolah</label>
                <input type="text" id="input_nip_kepala" class="form-control form-control-sm" placeholder="Contoh: 197801012005011002" oninput="updateSignature()" value="<?php echo e(request('nip_kepala', '197801012005011002')); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-dark mb-1">Kota & Tanggal Laporan</label>
                <input type="text" id="input_tanggal_laporan" class="form-control form-control-sm" placeholder="Contoh: Garut, 07 Agustus 2026" oninput="updateSignature()" value="<?php echo e($tanggalHariIni); ?>">
            </div>
        </div>
    </div>

    <!-- Kop Surat Resmi -->
    <div class="header-kop text-center">
        <h3 class="fw-bold text-uppercase m-0">YAYASAN AL-MUTTAQIN</h3>
        <h2 class="fw-bold text-uppercase m-0">SMP IT AL-MUTTAQIN</h2>
        <p class="m-0 small">Jl. Al-Muttaqin No. 12 Tarogong Kaler, Kab. Garut - Jawa Barat</p>
    </div>

    <!-- Judul Laporan Centered -->
    <div class="text-center mb-4">
        <h4 class="fw-bold text-uppercase text-decoration-underline m-0">LAPORAN REKAPITULASI & PERSENTASE KEHADIRAN SISWA</h4>
        <p class="m-0 mt-1 fw-bold">Periode: <?php echo e($periodeText); ?> | Kelas: <?php echo e($kelasObj->nama_kelas ?? 'Semua Kelas'); ?></p>
    </div>

    <!-- Tabel Presensi Siap Cetak -->
    <table class="table table-print table-bordered align-middle">
        <thead>
            <tr class="text-center bg-light">
                <th>No</th>
                <th>NISN</th>
                <th>Nama Peserta Didik</th>
                <th>JK</th>
                <th>Kelas</th>
                <th>Hadir</th>
                <th>Terlambat</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Alpa</th>
                <th>Persentase (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $rekapData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($index + 1); ?></td>
                <td class="text-center fw-bold"><?php echo e($row['siswa']->nisn); ?></td>
                <td><?php echo e($row['siswa']->nama); ?></td>
                <td class="text-center"><?php echo e($row['siswa']->jenis_kelamin); ?></td>
                <td class="text-center"><?php echo e($row['siswa']->kelas->nama_kelas ?? '-'); ?></td>
                <td class="text-center"><?php echo e($row['hadir']); ?></td>
                <td class="text-center"><?php echo e($row['terlambat']); ?></td>
                <td class="text-center"><?php echo e($row['izin']); ?></td>
                <td class="text-center"><?php echo e($row['sakit']); ?></td>
                <td class="text-center"><?php echo e($row['alpa']); ?></td>
                <td class="text-center fw-bold"><?php echo e($row['persentase']); ?>%</td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <!-- Tanda Tangan Kepala Sekolah -->
    <div class="d-flex justify-content-end mt-5">
        <div class="text-center" style="width: 280px;">
            <p class="mb-1" id="display_kota_tanggal"><?php echo e($tanggalHariIni); ?></p>
            <p class="mb-1">Mengetahui,</p>
            <p class="fw-bold mb-5">Kepala Sekolah SMP IT Al-Muttaqin</p>
            <br>
            <p class="fw-bold text-decoration-underline mb-0" id="display_nama_kepala">( H. Ahmad, S.Pd., M.Pd. )</p>
            <small id="display_nip_kepala">NIP. 197801012005011002</small>
        </div>
    </div>

    <script>
        function updateSignature() {
            const nama = document.getElementById('input_nama_kepala').value.trim();
            const nip = document.getElementById('input_nip_kepala').value.trim();
            const tgl = document.getElementById('input_tanggal_laporan').value.trim();

            document.getElementById('display_nama_kepala').innerText = nama ? `( ${nama} )` : '( ________________________ )';
            document.getElementById('display_nip_kepala').innerText = nip ? `NIP. ${nip}` : 'NIP. -';
            document.getElementById('display_kota_tanggal').innerText = tgl ? tgl : '<?php echo e($tanggalHariIni); ?>';
        }

        function triggerPrint() {
            const originalTitle = document.title;
            document.title = "";
            window.print();
            setTimeout(() => {
                document.title = originalTitle;
            }, 600);
        }

        window.onload = updateSignature;
    </script>
</body>
</html>
<?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/admin/rekap/cetak.blade.php ENDPATH**/ ?>