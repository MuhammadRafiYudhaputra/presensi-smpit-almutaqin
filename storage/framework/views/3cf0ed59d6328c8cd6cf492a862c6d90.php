<?php $__env->startSection('content'); ?>
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-chalkboard-user me-2 text-primary"></i>Kelola Data Wali Kelas</h5>
            <small class="text-muted">Data Tenaga Pendidik, Penugasan Kelas, & Akun Login Portal Guru</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalImportGuru">
                <i class="fa-solid fa-file-import me-1"></i> Import Data Wali Kelas
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddGuru">
                <i class="fa-solid fa-plus me-1"></i> Tambah Wali Kelas Baru
            </button>
        </div>
    </div>

    <!-- Filter & Sorting Dropdown -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-4">
            <form action="<?php echo e(route('admin.guru.index')); ?>" method="GET" class="d-flex align-items-center gap-2">
                <label class="form-label fw-semibold text-nowrap mb-0"><i class="fa-solid fa-sort text-primary me-1"></i> Urutkan:</label>
                <select name="sort_by" class="form-select" onchange="this.form.submit()">
                    <option value="nama_asc" <?php echo e(($sortBy ?? '') === 'nama_asc' ? 'selected' : ''); ?>>Nama Wali Kelas (A-Z)</option>
                    <option value="nama_desc" <?php echo e(($sortBy ?? '') === 'nama_desc' ? 'selected' : ''); ?>>Nama Wali Kelas (Z-A)</option>
                    <option value="nip" <?php echo e(($sortBy ?? '') === 'nip' ? 'selected' : ''); ?>>NIP Pegawai</option>
                </select>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>NIP Pegawai</th>
                    <th>Nama Wali Kelas</th>
                    <th>Email Login Portal</th>
                    <th>Penugasan Kelas</th>
                    <th>No. HP / WA</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $guru): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center"><?php echo e($gurus->firstItem() + $index); ?></td>
                    <td class="text-center font-monospace"><?php echo e($guru->nip ?? '-'); ?></td>
                    <td>
                        <span class="fw-bold text-dark d-block"><?php echo e($guru->nama); ?></span>
                        <?php if($guru->alamat): ?> <small class="text-muted"><i class="fa-solid fa-location-dot me-1 fs-8"></i><?php echo e($guru->alamat); ?></small> <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-light text-primary border"><i class="fa-solid fa-envelope me-1"></i><?php echo e($guru->user->email ?? '-'); ?></span>
                    </td>
                    <td class="text-center">
                        <?php if($guru->kelas): ?>
                            <span class="badge bg-success fs-7 px-3 py-2"><i class="fa-solid fa-school me-1"></i>Wali Kelas <?php echo e($guru->kelas->nama_kelas); ?></span>
                        <?php else: ?>
                            <span class="badge bg-secondary text-white"><i class="fa-solid fa-minus me-1"></i>Belum ada kelas</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($guru->no_hp): ?>
                            <span class="badge bg-success-subtle text-success border px-2 py-1"><i class="fa-brands fa-whatsapp me-1"></i><?php echo e($guru->no_hp); ?></span>
                        <?php else: ?>
                            <span class="text-muted small">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-info text-white rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalResetPasswordGuru<?php echo e($guru->id); ?>" title="Reset Password Akun">
                            <i class="fa-solid fa-key"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-warning rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEditGuru<?php echo e($guru->id); ?>" title="Edit Data Wali Kelas">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <form action="<?php echo e(route('admin.guru.destroy', $guru->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus data wali kelas ini?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada data wali kelas. Silakan tambahkan wali kelas baru atau impor dari file CSV/Excel.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <?php echo e($gurus->links()); ?>

    </div>
</div>

<!-- Modal Import Wali Kelas -->
<div class="modal fade" id="modalImportGuru" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold"><i class="fa-solid fa-file-csv me-2 text-success"></i>Import Data Wali Kelas (CSV / Excel)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.guru.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih File CSV / Excel</label>
                        <input type="file" name="file_guru" class="form-control" accept=".csv, .xlsx, .xls" required>
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

<!-- Modal Tambah Wali Kelas -->
<div class="modal fade" id="modalAddGuru" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Data Wali Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.guru.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIP (Opsional)</label>
                        <input type="text" name="nip" class="form-control" placeholder="Nomor Induk Pegawai">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Wali Kelas</label>
                        <input type="text" name="nama" class="form-control" required placeholder="Nama & Gelar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email (Untuk Login Portal Guru)</label>
                        <input type="email" name="email" class="form-control" required placeholder="walikelas@almuttaqin.sch.id">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Login</label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="08123456789">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat tinggal"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Wali Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Wali Kelas Loop -->
<?php $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guru): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="modalEditGuru<?php echo e($guru->id); ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Edit Data Wali Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.guru.update', $guru->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIP (Opsional)</label>
                        <input type="text" name="nip" class="form-control" value="<?php echo e($guru->nip); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Wali Kelas</label>
                        <input type="text" name="nama" class="form-control" value="<?php echo e($guru->nama); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email (Untuk Login)</label>
                        <input type="email" name="email" class="form-control" value="<?php echo e($guru->user->email ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Baru (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control" value="<?php echo e($guru->no_hp); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"><?php echo e($guru->alamat); ?></textarea>
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

<!-- Modal Reset Password Guru Loop -->
<?php $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guru): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="modalResetPasswordGuru<?php echo e($guru->id); ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-info"><i class="fa-solid fa-key me-2"></i>Reset Password Wali Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.guru.resetPassword', $guru->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body text-start">
                    <p class="text-dark small mb-3">
                        Masukkan password baru untuk akun login Wali Kelas <strong><?php echo e($guru->nama); ?></strong> (Email: <code><?php echo e($guru->user->email ?? '-'); ?></code>):
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4 fw-bold">Simpan Password Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/admin/guru/index.blade.php ENDPATH**/ ?>