<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card card-custom p-4 shadow-sm border-0 rounded-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-1 text-primary">
                        <i class="fa-solid fa-clock text-warning me-2"></i>Pengaturan Jam Presensi & Keterlambatan
                    </h5>
                    <small class="text-muted">Atur jam presensi masuk, batas toleransi terlambat, dan jam pulang sesuai jadwal operasional sekolah</small>
                </div>
            </div>



            <?php if($errors->any()): ?>
                <div class="alert alert-danger rounded-4 mb-4 shadow-sm" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <strong>Terjadi Kesalahan:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('admin.jampresensi.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="row g-4 mb-4">
                    <!-- Jam Masuk -->
                    <div class="col-md-4">
                        <div class="card bg-primary bg-opacity-10 border-primary border-opacity-25 p-3 rounded-4 h-100">
                            <label class="form-label fw-bold text-primary mb-2">
                                <i class="fa-solid fa-bell me-1"></i> Jam Masuk
                            </label>
                            <input type="time" name="jam_masuk" class="form-control form-control-lg fw-bold text-center bg-white shadow-sm" value="<?php echo e(old('jam_masuk', substr($jamPresensi->jam_masuk ?? '07:00:00', 0, 5))); ?>" required>
                            <small class="text-muted mt-2 d-block">Jam awal mulainya presensi pagi siswa tiba di sekolah.</small>
                        </div>
                    </div>

                    <!-- Batas Terlambat -->
                    <div class="col-md-4">
                        <div class="card bg-warning bg-opacity-10 border-warning border-opacity-25 p-3 rounded-4 h-100">
                            <label class="form-label fw-bold text-warning-emphasis mb-2">
                                <i class="fa-solid fa-user-clock me-1"></i> Batas Terlambat
                            </label>
                            <input type="time" name="jam_terlambat" class="form-control form-control-lg fw-bold text-center border-warning bg-white shadow-sm" value="<?php echo e(old('jam_terlambat', substr($jamPresensi->jam_terlambat ?? '07:15:00', 0, 5))); ?>" required>
                            <small class="text-muted mt-2 d-block">Siswa yang scan setelah jam ini otomatis tercatat <strong>TERLAMBAT</strong>.</small>
                        </div>
                    </div>

                    <!-- Jam Pulang -->
                    <div class="col-md-4">
                        <div class="card bg-info bg-opacity-10 border-info border-opacity-25 p-3 rounded-4 h-100">
                            <label class="form-label fw-bold text-info-emphasis mb-2">
                                <i class="fa-solid fa-door-open me-1"></i> Jam Pulang
                            </label>
                            <input type="time" name="jam_pulang" class="form-control form-control-lg fw-bold text-center bg-white shadow-sm" value="<?php echo e(old('jam_pulang', substr($jamPresensi->jam_pulang ?? '15:00:00', 0, 5))); ?>" required>
                            <small class="text-muted mt-2 d-block">Waktu resmi kepulangan sekolah untuk scan pulang sore.</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Pengaturan Jam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/admin/jampresensi/index.blade.php ENDPATH**/ ?>