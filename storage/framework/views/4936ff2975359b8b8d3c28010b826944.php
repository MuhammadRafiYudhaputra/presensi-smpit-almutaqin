<?php $__env->startSection('content'); ?>
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-user-graduate me-2 text-primary"></i>Kelola Data Siswa</h5>
            <small class="text-muted">Total siswa terdaftar, impor data Dapodik, dan pembuatan token QR Code</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalImportDapodik">
                <i class="fa-solid fa-file-import me-1"></i> Import Data Dapodik
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddSiswa">
                <i class="fa-solid fa-plus me-1"></i> Tambah Siswa Baru
            </button>
        </div>
    </div>

    <!-- Status Filter Pills: Siswa Aktif vs Arsip Alumni -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div class="btn-group p-1 bg-light rounded-pill border">
            <a href="<?php echo e(route('admin.siswa.index', ['status' => 'aktif', 'search' => $search, 'kelas_id' => $kelasId, 'sort_by' => $sortBy])); ?>" class="btn btn-sm rounded-pill <?php echo e(($status ?? 'aktif') === 'aktif' ? 'btn-primary shadow-sm' : 'btn-light text-muted'); ?>">
                <i class="fa-solid fa-user-check me-1 text-success"></i> Siswa Aktif (Kelas 7, 8, 9)
                <span class="badge bg-white text-primary ms-1 rounded-pill"><?php echo e($countAktif); ?></span>
            </a>
            <a href="<?php echo e(route('admin.siswa.index', ['status' => 'alumni', 'search' => $search, 'kelas_id' => $kelasId, 'sort_by' => $sortBy])); ?>" class="btn btn-sm rounded-pill <?php echo e(($status ?? '') === 'alumni' ? 'btn-primary shadow-sm' : 'btn-light text-muted'); ?>">
                <i class="fa-solid fa-graduation-cap me-1 text-warning"></i> Arsip Alumni / Siswa Lulus
                <span class="badge bg-white text-dark ms-1 rounded-pill"><?php echo e($countAlumni); ?></span>
            </a>
            <a href="<?php echo e(route('admin.siswa.index', ['status' => 'semua', 'search' => $search, 'kelas_id' => $kelasId, 'sort_by' => $sortBy])); ?>" class="btn btn-sm rounded-pill <?php echo e(($status ?? '') === 'semua' ? 'btn-primary shadow-sm' : 'btn-light text-muted'); ?>">
                <i class="fa-solid fa-users me-1"></i> Semua Data
                <span class="badge bg-secondary text-white ms-1 rounded-pill"><?php echo e($countSemua); ?></span>
            </a>
        </div>
    </div>

    <!-- Filter, Search, & Sorting Bar -->
    <form action="<?php echo e(route('admin.siswa.index')); ?>" method="GET" class="row g-2 mb-4 align-items-center">
        <input type="hidden" name="status" value="<?php echo e($status ?? 'aktif'); ?>">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari NISN, Nama Siswa, atau NIK..." value="<?php echo e($search ?? ''); ?>">
                <button type="submit" class="btn btn-primary px-3">Cari</button>
                <?php if(!empty($search) || !empty($kelasId)): ?>
                    <a href="<?php echo e(route('admin.siswa.index', ['status' => $status ?? 'aktif'])); ?>" class="btn btn-outline-secondary">Reset</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-flex align-items-center gap-2">
                <label class="form-label fw-semibold text-nowrap mb-0"><i class="fa-solid fa-filter text-primary me-1"></i> Kelas:</label>
                <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    <?php $__currentLoopData = $kelases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k->id); ?>" <?php echo e(($kelasId ?? '') == $k->id ? 'selected' : ''); ?>>Kelas <?php echo e($k->nama_kelas); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-2">
                <label class="form-label fw-semibold text-nowrap mb-0"><i class="fa-solid fa-sort text-primary me-1"></i> Urutkan:</label>
                <select name="sort_by" class="form-select" onchange="this.form.submit()">
                    <option value="nama_asc" <?php echo e(($sortBy ?? '') === 'nama_asc' ? 'selected' : ''); ?>>Nama Siswa (A-Z)</option>
                    <option value="nama_desc" <?php echo e(($sortBy ?? '') === 'nama_desc' ? 'selected' : ''); ?>>Nama Siswa (Z-A)</option>
                    <option value="nisn" <?php echo e(($sortBy ?? '') === 'nisn' ? 'selected' : ''); ?>>NISN Siswa</option>
                </select>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>NISN</th>
                    <th>Nama Peserta Didik</th>
                    <th style="width: 50px;">JK</th>
                    <th>Tempat, Tanggal Lahir & NIK</th>
                    <th>Kelas / Status</th>
                    <th>Orang Tua & WA</th>
                    <th>Alamat (Dusun/Kel/Kec)</th>
                    <th>QR Code Token</th>
                    <th style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $siswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $siswa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center text-muted fw-semibold"><?php echo e($siswas->firstItem() + $index); ?></td>
                    <td class="text-center">
                        <span class="fw-bold text-dark font-monospace"><?php echo e($siswa->nisn); ?></span>
                    </td>
                    <td>
                        <span class="fw-semibold d-block text-dark"><?php echo e($siswa->nama); ?></span>
                        <?php if($siswa->nik): ?>
                            <small class="text-muted"><i class="fa-solid fa-id-card me-1 fs-8"></i>NIK: <?php echo e($siswa->nik); ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><span class="badge bg-secondary-subtle text-secondary-emphasis border px-2 py-1 fw-bold"><?php echo e($siswa->jenis_kelamin); ?></span></td>
                    <td>
                        <?php if($siswa->tempat_lahir || $siswa->tanggal_lahir): ?>
                            <span class="d-block text-dark small fw-semibold"><i class="fa-solid fa-location-dot me-1 text-danger"></i><?php echo e($siswa->tempat_lahir ?? 'Garut'); ?></span>
                            <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i><?php echo e($siswa->tanggal_lahir ?? '-'); ?></small>
                        <?php else: ?>
                            <span class="text-muted small">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if(stripos($siswa->kelas->nama_kelas ?? '', 'alumni') !== false || stripos($siswa->kelas->nama_kelas ?? '', 'lulus') !== false): ?>
                            <span class="badge bg-secondary text-white fw-bold px-3 py-1 rounded-pill"><i class="fa-solid fa-graduation-cap me-1"></i> ALUMNI / LULUS</span>
                        <?php else: ?>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-3 py-1 rounded-pill">Kelas <?php echo e($siswa->kelas->nama_kelas ?? '-'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="d-block fw-semibold text-dark"><?php echo e($siswa->orangTua->nama_ayah ?? $siswa->orangTua->nama_ibu ?? '-'); ?></span>
                        <small class="text-success"><i class="fa-brands fa-whatsapp me-1"></i><?php echo e($siswa->orangTua->no_wa ?? '-'); ?></small>
                    </td>
                    <td class="small">
                        <?php if($siswa->alamat || $siswa->kelurahan || $siswa->kecamatan): ?>
                            <span><?php echo e($siswa->alamat ?? '-'); ?></span>
                            <?php if($siswa->rt || $siswa->rw): ?> <small class="text-muted d-block">RT <?php echo e($siswa->rt); ?>/RW <?php echo e($siswa->rw); ?></small> <?php endif; ?>
                            <?php if($siswa->kelurahan || $siswa->kecamatan): ?> <small class="text-muted d-block"><?php echo e($siswa->kelurahan); ?>, <?php echo e($siswa->kecamatan); ?></small> <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><code><?php echo e($siswa->qr_code_token); ?></code></td>
                    <td class="text-center">
                        <a href="<?php echo e(route('admin.siswa.card', $siswa->id)); ?>" target="_blank" class="btn btn-sm btn-success rounded-pill mb-1 w-100" title="Cetak Kartu QR">
                            <i class="fa-solid fa-id-card me-1"></i> Cetak QR
                        </a>
                        <div class="d-flex justify-content-center gap-1">
                            <button type="button" class="btn btn-sm btn-warning rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEditSiswa<?php echo e($siswa->id); ?>" title="Edit Data Siswa">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form action="<?php echo e(route('admin.siswa.destroy', $siswa->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus data siswa ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">Belum ada data siswa. Silakan tambahkan siswa baru atau impor dari file Dapodik.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <?php echo e($siswas->links()); ?>

    </div>
</div>

<!-- Modal Tambah Siswa -->
<div class="modal fade" id="modalAddSiswa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.siswa.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NISN</label>
                        <input type="text" name="nisn" class="form-control" required placeholder="Contoh: 0081234567">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Siswa</label>
                        <input type="text" name="nama" class="form-control" required placeholder="Nama Siswa">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L">Laki-laki (L)</option>
                            <option value="P">Perempuan (P)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kelas</label>
                        <select name="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php $__currentLoopData = $kelases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($k->id); ?>"><?php echo e($k->nama_kelas); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Orang Tua / Wali (Kontak WA)</label>
                        <select name="orang_tua_id" class="form-select" required>
                            <option value="">-- Pilih Orang Tua --</option>
                            <?php $__currentLoopData = $orangTuas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ot->id); ?>"><?php echo e($ot->nama_ayah ?? $ot->nama_ibu); ?> (WA: <?php echo e($ot->no_wa); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Siswa Loop -->
<?php $__currentLoopData = $siswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $siswa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="modalEditSiswa<?php echo e($siswa->id); ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Edit Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.siswa.update', $siswa->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NISN</label>
                        <input type="text" name="nisn" class="form-control" value="<?php echo e($siswa->nisn); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Siswa</label>
                        <input type="text" name="nama" class="form-control" value="<?php echo e($siswa->nama); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L" <?php echo e($siswa->jenis_kelamin == 'L' ? 'selected' : ''); ?>>Laki-laki (L)</option>
                            <option value="P" <?php echo e($siswa->jenis_kelamin == 'P' ? 'selected' : ''); ?>>Perempuan (P)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kelas</label>
                        <select name="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php $__currentLoopData = $kelases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($k->id); ?>" <?php echo e($siswa->kelas_id == $k->id ? 'selected' : ''); ?>><?php echo e($k->nama_kelas); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Orang Tua / Wali (Kontak WA)</label>
                        <select name="orang_tua_id" class="form-select" required>
                            <option value="">-- Pilih Orang Tua --</option>
                            <?php $__currentLoopData = $orangTuas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ot->id); ?>" <?php echo e($siswa->orang_tua_id == $ot->id ? 'selected' : ''); ?>><?php echo e($ot->nama_ayah ?? $ot->nama_ibu); ?> (WA: <?php echo e($ot->no_wa); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
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

<!-- Modal Import Data Dapodik -->
<div class="modal fade" id="modalImportDapodik" tabindex="-1" aria-labelledby="modalImportDapodikLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-success" id="modalImportDapodikLabel">
                    <i class="fa-solid fa-file-import me-2"></i>Import Data Siswa Dapodik
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('admin.siswa.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih File CSV / Excel Dapodik</label>
                        <input type="file" name="file_dapodik" class="form-control" accept=".csv,.txt,.xlsx,.xls" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Proses Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/admin/siswa/index.blade.php ENDPATH**/ ?>