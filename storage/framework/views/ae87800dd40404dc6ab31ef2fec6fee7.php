<?php $__env->startSection('content'); ?>
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Live Monitoring Kehadiran
                <?php if(auth()->user()->role === 'guru' && isset($waliKelas) && $waliKelas): ?>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 fs-7 ms-2 rounded-pill">
                        <i class="fa-solid fa-user-shield me-1"></i> Kelas <?php echo e($waliKelas->nama_kelas); ?>

                    </span>
                <?php endif; ?>
            </h5>
            <small class="text-muted">Pantau aktivitas scan presensi siswa secara real-time</small>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="<?php echo e(route(auth()->user()->role === 'guru' ? 'guru.monitoring' : 'admin.rekap.monitoring')); ?>" class="row g-3 mb-4">
        <div class="<?php echo e(auth()->user()->role === 'admin' ? 'col-md-4' : 'col-md-6'); ?>">
            <label class="form-label fw-semibold">Pilih Tanggal</label>
            <input type="date" name="tanggal" value="<?php echo e($tanggal); ?>" class="form-control" onchange="this.form.submit()">
        </div>
        <?php if(auth()->user()->role === 'admin'): ?>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Filter Kelas</label>
            <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                <?php $__currentLoopData = $kelases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k->id); ?>" <?php echo e($kelasId == $k->id ? 'selected' : ''); ?>>Kelas <?php echo e($k->nama_kelas); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <?php endif; ?>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Waktu Scan</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Jam Absensi</th>
                    <th>Status Presensi</th>
                    <th>Status Notifikasi WA</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $kehadirans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($kh->created_at->format('H:i:s')); ?></td>
                    <td class="fw-semibold"><?php echo e($kh->siswa->nama ?? '-'); ?></td>
                    <td><span class="badge bg-secondary"><?php echo e($kh->siswa->kelas->nama_kelas ?? '-'); ?></span></td>
                    <td><span class="text-success fw-bold"><?php echo e($kh->jam_masuk ?? '-'); ?></span></td>
                    <td>
                        <?php if($kh->status == 'HADIR'): ?>
                            <span class="badge bg-success">HADIR</span>
                        <?php elseif($kh->status == 'TERLAMBAT'): ?>
                            <span class="badge bg-warning text-dark">TERLAMBAT</span>
                        <?php else: ?>
                            <span class="badge bg-danger"><?php echo e($kh->status); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($kh->wa_masuk_sent): ?>
                            <span class="badge bg-success-subtle text-success"><i class="fa-brands fa-whatsapp me-1"></i> WA Masuk Sent</span>
                        <?php else: ?>
                            <span class="badge bg-light text-muted">Waiting Queue</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Tidak ada data presensi pada tanggal terpilih.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <?php echo e($kehadirans->appends(request()->query())->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/admin/rekap/monitoring.blade.php ENDPATH**/ ?>