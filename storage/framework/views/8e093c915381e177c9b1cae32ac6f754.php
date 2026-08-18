<?php $__env->startSection('content'); ?>
<!-- Welcome Banner -->
<div class="card card-custom p-4 mb-4 text-white" style="background: linear-gradient(135deg, #0F52BA 0%, #1E293B 100%);">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1"><i class="fa-solid fa-chalkboard-user me-2 text-warning"></i>Portal Guru - Selamat Datang, <?php echo e(auth()->user()->name); ?>!</h4>
            <p class="mb-0 text-white-50">
                <?php if($waliKelas): ?>
                    <span class="badge bg-success me-1"><i class="fa-solid fa-user-shield"></i> Wali Kelas <?php echo e($waliKelas->nama_kelas); ?></span> Penanggung jawab kelas <?php echo e($waliKelas->nama_kelas); ?>.
                <?php else: ?>
                    Tenaga Pendidik / Ustadz SMP IT Al-Mutaqin.
                <?php endif; ?>
            </p>
        </div>
        <div class="d-none d-md-block text-end">
            <span class="fs-6 opacity-75"><i class="fa-regular fa-clock me-1"></i> <?php echo e(date('H:i')); ?> WIB</span>
        </div>
    </div>
</div>

<!-- Quick Statistics -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-primary border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted fw-semibold">Total Siswa</small>
                    <h3 class="fw-bold m-0 text-primary"><?php echo e($totalSiswa); ?></h3>
                </div>
                <div class="bg-primary-subtle p-3 rounded-circle text-primary">
                    <i class="fa-solid fa-user-graduate fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-success border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted fw-semibold">Hadir Hari Ini</small>
                    <h3 class="fw-bold m-0 text-success"><?php echo e($totalHadir); ?></h3>
                </div>
                <div class="bg-success-subtle p-3 rounded-circle text-success">
                    <i class="fa-solid fa-user-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-warning border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted fw-semibold">Terlambat Hari Ini</small>
                    <h3 class="fw-bold m-0 text-warning"><?php echo e($totalTerlambat); ?></h3>
                </div>
                <div class="bg-warning-subtle p-3 rounded-circle text-warning">
                    <i class="fa-solid fa-clock fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-danger border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted fw-semibold">Alpa / Belum Absen</small>
                    <h3 class="fw-bold m-0 text-danger"><?php echo e($totalAlpa); ?></h3>
                </div>
                <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                    <i class="fa-solid fa-user-xmark fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Navigation Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <a href="<?php echo e(route('guru.monitoring')); ?>" class="text-decoration-none">
            <div class="card card-custom p-4 h-100 border-0 shadow-sm text-center">
                <div class="mb-3 text-primary">
                    <i class="fa-solid fa-clock-rotate-left fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Live Monitoring Kehadiran</h5>
                <small class="text-muted">Pantau status kehadiran siswa secara realtime hari ini</small>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('guru.rekap')); ?>" class="text-decoration-none">
            <div class="card card-custom p-4 h-100 border-0 shadow-sm text-center">
                <div class="mb-3 text-success">
                    <i class="fa-solid fa-file-invoice fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Rekapitulasi Kehadiran</h5>
                <small class="text-muted">Lihat laporan rekapitulasi kehadiran bulanan per kelas</small>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('guru.rekap.cetak')); ?>" target="_blank" class="text-decoration-none">
            <div class="card card-custom p-4 h-100 border-0 shadow-sm text-center">
                <div class="mb-3 text-info">
                    <i class="fa-solid fa-print fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Cetak & Export Laporan</h5>
                <small class="text-muted">Cetak laporan resmi presensi untuk dokumen sekolah/ortu</small>
            </div>
        </a>
    </div>
</div>

<!-- Recent Attendances Today -->
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-list-check me-2 text-primary"></i>Aktivitas Presensi Terakhir Hari Ini</h5>
        <a href="<?php echo e(route('guru.monitoring')); ?>" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Waktu Scan</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $recentKehadirans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><small class="text-muted"><?php echo e($k->updated_at->format('H:i:s')); ?> WIB</small></td>
                    <td class="fw-semibold"><?php echo e($k->siswa->nama ?? '-'); ?></td>
                    <td><span class="badge bg-secondary-subtle text-secondary"><?php echo e($k->siswa->kelas->nama_kelas ?? '-'); ?></span></td>
                    <td><?php echo e($k->jam_masuk ?? '-'); ?></td>
                    <td><?php echo e($k->jam_pulang ?? '-'); ?></td>
                    <td>
                        <?php if($k->status === 'HADIR'): ?>
                            <span class="badge bg-success-subtle text-success badge-status">HADIR TEPAT WAKTU</span>
                        <?php elseif($k->status === 'TERLAMBAT'): ?>
                            <span class="badge bg-warning-subtle text-warning badge-status">TERLAMBAT</span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger badge-status"><?php echo e($k->status); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada aktivitas presensi hari ini.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/guru/dashboard.blade.php ENDPATH**/ ?>