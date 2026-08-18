<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    <div class="card card-custom p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1"><i class="fa-solid fa-graduation-cap me-2 text-primary"></i>Kenaikan Kelas & Tahun Ajaran Baru</h5>
                <small class="text-muted">Fitur otomatisasi transisi kenaikan kelas siswa (Kelas 7 &rarr; 8, Kelas 8 &rarr; 9, Kelas 9 &rarr; ALUMNI)</small>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2 fs-5"></i> <?php echo nl2br(e(session('success'))); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Summary Cards Total Siswa Per Tingkat -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10 rounded-4 p-3 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-primary fw-bold small text-uppercase">Siswa Kelas 7</span>
                        <h3 class="fw-extrabold text-primary mb-0 mt-1"><?php echo e($countKelas7); ?></h3>
                    </div>
                    <div class="bg-primary text-white rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-user-graduate fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info bg-opacity-10 rounded-4 p-3 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-info fw-bold small text-uppercase">Siswa Kelas 8</span>
                        <h3 class="fw-extrabold text-info mb-0 mt-1"><?php echo e($countKelas8); ?></h3>
                    </div>
                    <div class="bg-info text-white rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-user-graduate fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10 rounded-4 p-3 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-warning-emphasis fw-bold small text-uppercase">Siswa Kelas 9</span>
                        <h3 class="fw-extrabold text-warning-emphasis mb-0 mt-1"><?php echo e($countKelas9); ?></h3>
                    </div>
                    <div class="bg-warning text-dark rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-user-graduate fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10 rounded-4 p-3 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-success fw-bold small text-uppercase">Siswa Lulus / ALUMNI</span>
                        <h3 class="fw-extrabold text-success mb-0 mt-1"><?php echo e($countAlumni); ?></h3>
                    </div>
                    <div class="bg-success text-white rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-award fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Opsi Mode Kenaikan Kelas -->
    <div class="row g-4">
        <!-- OPSI 1: Kenaikan Kelas Otomatis Massal -->
        <div class="col-lg-5">
            <div class="card card-custom p-4 h-100 border-start border-4 border-primary">
                <h5 class="fw-bold text-primary mb-3">
                    <i class="fa-solid fa-wand-magic-sparkles me-2"></i>Kenaikan Kelas Otomatis Massal
                </h5>
                <p class="text-muted small mb-3">
                    Fitur ini memproses seluruh siswa sekaligus saat berganti Tahun Ajaran Baru secara otomatis:
                </p>
                <div class="list-group list-group-flush mb-4">
                    <div class="list-group-item bg-transparent px-0 py-2 border-0 d-flex align-items-center">
                        <span class="badge bg-primary rounded-circle p-2 me-3"><i class="fa-solid fa-arrow-right"></i></span>
                        <div>
                            <strong class="d-block text-dark">Siswa Kelas 7 &rarr; Naik ke Kelas 8</strong>
                            <small class="text-muted">Seluruh siswa aktif Kelas 7 otomatis dipindahkan ke Kelas 8.</small>
                        </div>
                    </div>
                    <div class="list-group-item bg-transparent px-0 py-2 border-0 d-flex align-items-center">
                        <span class="badge bg-info rounded-circle p-2 me-3"><i class="fa-solid fa-arrow-right"></i></span>
                        <div>
                            <strong class="d-block text-dark">Siswa Kelas 8 &rarr; Naik ke Kelas 9</strong>
                            <small class="text-muted">Seluruh siswa aktif Kelas 8 otomatis dipindahkan ke Kelas 9.</small>
                        </div>
                    </div>
                    <div class="list-group-item bg-transparent px-0 py-2 border-0 d-flex align-items-center">
                        <span class="badge bg-success rounded-circle p-2 me-3"><i class="fa-solid fa-check"></i></span>
                        <div>
                            <strong class="d-block text-dark">Siswa Kelas 9 &rarr; Lulus / ALUMNI</strong>
                            <small class="text-muted">Seluruh siswa Kelas 9 diubah statusnya menjadi Lulus / ALUMNI.</small>
                        </div>
                    </div>
                </div>

                <div class="mt-auto pt-3 border-top">
                    <button type="button" class="btn btn-primary rounded-pill w-100 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKonfirmasiMassal">
                        <i class="fa-solid fa-rocket me-2"></i>Proses Kenaikan Kelas Massal
                    </button>
                </div>
            </div>
        </div>

        <!-- OPSI 2: Kenaikan Kelas Pilihan (Per Kelas / Per Siswa) -->
        <div class="col-lg-7">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold mb-3">
                    <i class="fa-solid fa-sliders me-2 text-warning"></i>Kenaikan Kelas Pilihan / Per-Kelas
                </h5>
                <p class="text-muted small mb-3">
                    Gunakan fitur ini jika ingin memproses kenaikan kelas untuk kelas tertentu atau memilah siswa tertentu (misal jika ada siswa tinggal kelas):
                </p>

                <!-- Form Filter Kelas Asal -->
                <form action="<?php echo e(route('admin.kenaikan.index')); ?>" method="GET" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-dark">Pilih Kelas Asal</label>
                            <select name="kelas_asal_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Pilih Kelas Asal Siswa --</option>
                                <?php $__currentLoopData = $kelases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($k->id); ?>" <?php echo e($selectedKelasAsal == $k->id ? 'selected' : ''); ?>>
                                        Kelas <?php echo e($k->nama_kelas); ?> (<?php echo e($k->siswas_count ?? 0); ?> Siswa)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fa-solid fa-filter me-1"></i> Tampilkan Siswa
                            </button>
                        </div>
                    </div>
                </form>

                <?php if(!empty($selectedKelasAsal)): ?>
                    <form action="<?php echo e(route('admin.kenaikan.prosesPilihan')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAllSiswa" onclick="toggleSelectAll(this)">
                                <label class="form-check-label fw-bold text-dark" for="selectAllSiswa">Pilih Semua Siswa</label>
                            </div>
                            <span class="badge bg-light text-dark border">Total <?php echo e(count($siswas)); ?> Siswa</span>
                        </div>

                        <div class="table-responsive mb-3" style="max-height: 280px; overflow-y: auto;">
                            <table class="table table-sm table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">#</th>
                                        <th>NISN</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas Saat Ini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $siswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="text-center">
                                            <input class="form-check-input check-siswa" type="checkbox" name="siswa_ids[]" value="<?php echo e($s->id); ?>" checked>
                                        </td>
                                        <td class="fw-bold text-primary"><?php echo e($s->nisn); ?></td>
                                        <td class="fw-semibold text-dark"><?php echo e($s->nama); ?></td>
                                        <td><span class="badge bg-info text-dark">Kelas <?php echo e($s->kelas->nama_kelas ?? '-'); ?></span></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Tidak ada siswa di kelas asal ini.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if(count($siswas) > 0): ?>
                        <div class="p-3 bg-light rounded-4 border">
                            <label class="form-label fw-bold text-dark mb-2">Pindahkan / Naikkan Ke Kelas Tujuan:</label>
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <select name="kelas_tujuan_id" class="form-select" required>
                                        <option value="">-- Pilih Kelas Tujuan --</option>
                                        <?php $__currentLoopData = $kelases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($k->id); ?>">Kelas <?php echo e($k->nama_kelas); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success w-100 fw-bold" onclick="return confirm('Proses kenaikan kelas untuk siswa terpilih?')">
                                        <i class="fa-solid fa-check me-1"></i> Simpan Pilihan
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>
                <?php else: ?>
                    <div class="text-center text-muted py-5 border rounded-4 bg-light">
                        <i class="fa-solid fa-arrow-up-long fs-2 mb-2 text-secondary"></i>
                        <p class="mb-0">Silakan pilih **Kelas Asal** terlebih dahulu untuk memproses kenaikan kelas pilihan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Kenaikan Kelas Otomatis Massal -->
<div class="modal fade" id="modalKonfirmasiMassal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-primary"><i class="fa-solid fa-circle-exclamation me-2 text-warning"></i>Konfirmasi Kenaikan Kelas Massal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.kenaikan.prosesMassal')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body text-start">
                    <p class="text-dark fw-semibold mb-2">Apakah Anda yakin ingin memproses kenaikan kelas untuk **Seluruh Siswa** di Tahun Ajaran Baru ini?</p>
                    <div class="alert alert-warning rounded-3 small mb-0">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Prosedur ini akan mengubah data kelas seluruh siswa:
                        <ul class="mb-0 mt-1 ps-3">
                            <li>Kelas 7 &rarr; Kelas 8</li>
                            <li>Kelas 8 &rarr; Kelas 9</li>
                            <li>Kelas 9 &rarr; ALUMNI</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Ya, Proses Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSelectAll(source) {
    checkboxes = document.getElementsByClassName('check-siswa');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/admin/kenaikan/index.blade.php ENDPATH**/ ?>