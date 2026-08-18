<?php $__env->startSection('content'); ?>
<div class="row g-4 mb-4">
    <!-- Stat Cards -->
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-primary border-4 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted fw-semibold">Siswa Aktif (7, 8, 9)</small>
                    <h3 class="fw-bold m-0 text-primary"><?php echo e($totalSiswaAktif ?? $totalSiswa); ?></h3>
                    <small class="text-muted fs-8"><i class="fa-solid fa-graduation-cap text-secondary me-1"></i>Arsip Alumni: <?php echo e($totalAlumni ?? 0); ?></small>
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
                    <i class="fa-solid fa-user-clock fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-danger border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted fw-semibold">Belum Presensi (Alpa)</small>
                    <h3 class="fw-bold m-0 text-danger"><?php echo e($totalAlpa); ?></h3>
                </div>
                <div class="bg-danger-subtle p-3 rounded-circle text-danger">
                    <i class="fa-solid fa-user-xmark fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-stream text-primary me-2"></i>Aktivitas Presensi Terakhir Hari Ini</h6>
        <a href="<?php echo e(route('admin.rekap.monitoring')); ?>" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Jam Absensi</th>
                    <th>Status</th>
                    <th>Notifikasi WA</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $recentPresensi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="fw-semibold"><?php echo e($item->siswa->nama ?? '-'); ?></td>
                    <td><span class="badge bg-secondary"><?php echo e($item->siswa->kelas->nama_kelas ?? '-'); ?></span></td>
                    <td><span class="fw-bold text-primary"><?php echo e($item->jam_masuk ?? '-'); ?></span></td>
                    <td>
                        <?php if($item->status == 'HADIR'): ?>
                            <span class="badge bg-success">HADIR</span>
                        <?php elseif($item->status == 'TERLAMBAT'): ?>
                            <span class="badge bg-warning text-dark">TERLAMBAT</span>
                        <?php else: ?>
                            <span class="badge bg-danger"><?php echo e($item->status); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($item->wa_masuk_sent): ?>
                            <span class="text-success"><i class="fa-brands fa-whatsapp"></i> Terkirim</span>
                        <?php else: ?>
                            <span class="text-muted"><i class="fa-regular fa-clock"></i> Pending / Queue</span>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>