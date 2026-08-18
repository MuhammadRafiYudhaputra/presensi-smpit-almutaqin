<?php $__env->startSection('content'); ?>
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-users me-2 text-primary"></i>Kelola Data Orang Tua / Wali</h5>
            <small class="text-muted">Kelola data orang tua/wali murid, dan kontak WhatsApp notifikasi</small>
        </div>
        <div class="d-flex gap-2 ms-auto">
            <button type="button" class="btn btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalImportOrangTua">
                <i class="fa-solid fa-file-import me-1"></i> Import Data Orang Tua
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddOrangTua">
                <i class="fa-solid fa-plus me-1"></i> Tambah Orang Tua Baru
            </button>
        </div>
    </div>

    <!-- Filter, Search, & Sorting Bar -->
    <form action="<?php echo e(route('admin.orangtua.index')); ?>" method="GET" class="row g-2 mb-4 align-items-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari Nama Ayah, Nama Ibu, NIK, No WA, atau Alamat..." value="<?php echo e($search ?? ''); ?>">
                <button type="submit" class="btn btn-primary px-3">Cari</button>
                <?php if(!empty($search)): ?>
                    <a href="<?php echo e(route('admin.orangtua.index')); ?>" class="btn btn-outline-secondary">Reset</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4 offset-md-2">
            <div class="d-flex align-items-center gap-2">
                <label class="form-label fw-semibold text-nowrap mb-0"><i class="fa-solid fa-sort text-primary me-1"></i> Urutkan:</label>
                <select name="sort_by" class="form-select" onchange="this.form.submit()">
                    <option value="nama_asc" <?php echo e(($sortBy ?? '') === 'nama_asc' ? 'selected' : ''); ?>>Nama Ayah (A-Z)</option>
                    <option value="nama_desc" <?php echo e(($sortBy ?? '') === 'nama_desc' ? 'selected' : ''); ?>>Nama Ayah (Z-A)</option>
                    <option value="no_wa" <?php echo e(($sortBy ?? '') === 'no_wa' ? 'selected' : ''); ?>>Nomor WA WhatsApp</option>
                </select>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Data Ayah (NIK & Pekerjaan)</th>
                    <th>Data Ibu (NIK & Pekerjaan)</th>
                    <th>No. WhatsApp Notifikasi</th>
                    <th>Peserta Didik (Anak)</th>
                    <th>Alamat</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $orangTuas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center"><?php echo e($orangTuas->firstItem() + $index); ?></td>
                    <td>
                        <span class="fw-semibold d-block text-dark"><?php echo e($ot->nama_ayah ?? '-'); ?></span>
                        <?php if($ot->nik_ayah): ?> <small class="text-muted d-block"><i class="fa-solid fa-id-card me-1 fs-8"></i>NIK: <?php echo e($ot->nik_ayah); ?></small> <?php endif; ?>
                        <?php if($ot->pekerjaan_ayah): ?> <small class="text-primary d-block"><i class="fa-solid fa-briefcase me-1 fs-8"></i><?php echo e($ot->pekerjaan_ayah); ?></small> <?php endif; ?>
                    </td>
                    <td>
                        <span class="fw-semibold d-block text-dark"><?php echo e($ot->nama_ibu ?? '-'); ?></span>
                        <?php if($ot->nik_ibu): ?> <small class="text-muted d-block"><i class="fa-solid fa-id-card me-1 fs-8"></i>NIK: <?php echo e($ot->nik_ibu); ?></small> <?php endif; ?>
                        <?php if($ot->pekerjaan_ibu): ?> <small class="text-primary d-block"><i class="fa-solid fa-briefcase me-1 fs-8"></i><?php echo e($ot->pekerjaan_ibu); ?></small> <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-success fs-7 px-3 py-2"><i class="fa-brands fa-whatsapp me-1"></i> <?php echo e($ot->no_wa); ?></span>
                    </td>
                    <td>
                        <?php $__empty_2 = true; $__currentLoopData = $ot->siswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                            <span class="badge bg-info-subtle text-dark border px-2 py-1 mb-1 d-inline-block">
                                <?php echo e($s->nama); ?> <strong class="text-primary">(<?php echo e($s->kelas->nama_kelas ?? '-'); ?>)</strong>
                            </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                            <span class="text-muted small">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="small"><?php echo e($ot->alamat ?? '-'); ?></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-warning rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEditOrangTua<?php echo e($ot->id); ?>" title="Edit Data Orang Tua">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <form action="<?php echo e(route('admin.orangtua.destroy', $ot->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus data orang tua ini?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada data orang tua. Silakan tambahkan data baru atau impor massal dari file CSV/Excel Dapodik.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <?php echo e($orangTuas->links()); ?>

    </div>
</div>

<!-- Modal Import Orang Tua -->
<div class="modal fade" id="modalImportOrangTua" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold"><i class="fa-solid fa-file-csv me-2 text-success"></i>Import Data Orang Tua (CSV / Excel)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.orangtua.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body text-start"> 
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih File CSV / Excel Dapodik</label>
                        <input type="file" name="file_orangtua" class="form-control" accept=".csv, .xlsx, .xls" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Orang Tua -->
<div class="modal fade" id="modalAddOrangTua" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Data Orang Tua / Wali Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.orangtua.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body text-start">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-2"><i class="fa-solid fa-user-tie me-1"></i>Data Ayah</h6>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Nama Ayah</label>
                                <input type="text" name="nama_ayah" class="form-control form-control-sm" placeholder="Nama Lengkap Ayah">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">NIK Ayah</label>
                                <input type="text" name="nik_ayah" class="form-control form-control-sm" placeholder="NIK Ayah">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Pekerjaan Ayah</label>
                                <input type="text" name="pekerjaan_ayah" class="form-control form-control-sm" placeholder="Contoh: Wiraswasta">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success mb-2"><i class="fa-solid fa-user-nurse me-1"></i>Data Ibu</h6>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Nama Ibu</label>
                                <input type="text" name="nama_ibu" class="form-control form-control-sm" placeholder="Nama Lengkap Ibu">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">NIK Ibu</label>
                                <input type="text" name="nik_ibu" class="form-control form-control-sm" placeholder="NIK Ibu">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Pekerjaan Ibu</label>
                                <input type="text" name="pekerjaan_ibu" class="form-control form-control-sm" placeholder="Contoh: Ibu Rumah Tangga">
                            </div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nama Wali (Jika ada)</label>
                            <input type="text" name="nama_wali" class="form-control form-control-sm" placeholder="Nama Wali">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-success"><i class="fa-brands fa-whatsapp me-1"></i>Nomor WhatsApp Notifikasi *</label>
                            <input type="text" name="no_wa" class="form-control form-control-sm" required placeholder="Contoh: 081234567890">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Alamat Tempat Tinggal</label>
                            <textarea name="alamat" class="form-control form-control-sm" rows="2" placeholder="Alamat lengkap keluarga..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Data Orang Tua</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Orang Tua Loop -->
<?php $__currentLoopData = $orangTuas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="modalEditOrangTua<?php echo e($ot->id); ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Edit Data Orang Tua / Wali</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.orangtua.update', $ot->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body text-start">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-2"><i class="fa-solid fa-user-tie me-1"></i>Data Ayah</h6>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Nama Ayah</label>
                                <input type="text" name="nama_ayah" class="form-control form-control-sm" value="<?php echo e($ot->nama_ayah); ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">NIK Ayah</label>
                                <input type="text" name="nik_ayah" class="form-control form-control-sm" value="<?php echo e($ot->nik_ayah); ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Pekerjaan Ayah</label>
                                <input type="text" name="pekerjaan_ayah" class="form-control form-control-sm" value="<?php echo e($ot->pekerjaan_ayah); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success mb-2"><i class="fa-solid fa-user-nurse me-1"></i>Data Ibu</h6>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Nama Ibu</label>
                                <input type="text" name="nama_ibu" class="form-control form-control-sm" value="<?php echo e($ot->nama_ibu); ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">NIK Ibu</label>
                                <input type="text" name="nik_ibu" class="form-control form-control-sm" value="<?php echo e($ot->nik_ibu); ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Pekerjaan Ibu</label>
                                <input type="text" name="pekerjaan_ibu" class="form-control form-control-sm" value="<?php echo e($ot->pekerjaan_ibu); ?>">
                            </div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nama Wali</label>
                            <input type="text" name="nama_wali" class="form-control form-control-sm" value="<?php echo e($ot->nama_wali); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-success"><i class="fa-brands fa-whatsapp me-1"></i>Nomor WhatsApp Notifikasi *</label>
                            <input type="text" name="no_wa" class="form-control form-control-sm" value="<?php echo e($ot->no_wa); ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Alamat Tempat Tinggal</label>
                            <textarea name="alamat" class="form-control form-control-sm" rows="2"><?php echo e($ot->alamat); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/admin/orangtua/index.blade.php ENDPATH**/ ?>