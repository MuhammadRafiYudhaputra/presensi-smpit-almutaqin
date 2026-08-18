<?php $__env->startSection('content'); ?>
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-school me-2 text-primary"></i>Kelola Data Kelas</h5>
            <small class="text-muted">Daftar kelas, penugasan Wali Kelas, dan kenaikan kelas otomatis di tahun ajaran baru</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-success rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalKenaikanKelasMassal">
                <i class="fa-solid fa-graduation-cap me-1"></i> Kenaikan Kelas (Tahun Ajaran Baru)
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddKelas">
                <i class="fa-solid fa-plus me-1"></i> Tambah Kelas Baru
            </button>
        </div>
    </div>



    <!-- Filter & Sorting Dropdown -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-4">
            <form action="<?php echo e(route('admin.kelas.index')); ?>" method="GET" class="d-flex align-items-center gap-2">
                <label class="form-label fw-semibold text-nowrap mb-0"><i class="fa-solid fa-sort text-primary me-1"></i> Urutkan:</label>
                <select name="sort_by" class="form-select" onchange="this.form.submit()">
                    <option value="nama_asc" <?php echo e(($sortBy ?? '') === 'nama_asc' ? 'selected' : ''); ?>>Nama Kelas (A-Z)</option>
                    <option value="nama_desc" <?php echo e(($sortBy ?? '') === 'nama_desc' ? 'selected' : ''); ?>>Nama Kelas (Z-A)</option>
                </select>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Kelas</th>
                    <th>Wali Kelas Penanggung Jawab</th>
                    <th>Jumlah Peserta Didik</th>
                    <th style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $kelases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center"><?php echo e($kelases->firstItem() + $index); ?></td>
                    <td class="fw-bold text-primary">
                        <i class="fa-solid fa-graduation-cap me-2 text-primary"></i>Kelas <?php echo e($k->nama_kelas); ?>

                    </td>
                    <td>
                        <?php if($k->waliKelas): ?>
                            <span class="fw-semibold d-block text-dark"><i class="fa-solid fa-user-tie me-1 text-success"></i><?php echo e($k->waliKelas->nama); ?></span>
                            <small class="text-muted"><i class="fa-solid fa-id-card me-1 fs-8"></i>NIP: <?php echo e($k->waliKelas->nip ?? '-'); ?></small>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i>Belum ditentukan</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info text-dark fs-7 px-3 py-2">
                            <i class="fa-solid fa-users me-1"></i><?php echo e($k->siswas_count ?? 0); ?> Siswa Terdaftar
                        </span>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-warning rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#modalEditKelas<?php echo e($k->id); ?>" title="Edit Kelas">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <form action="<?php echo e(route('admin.kelas.destroy', $k->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus kelas ini?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada data kelas.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <?php echo e($kelases->links()); ?>

    </div>
</div>

<!-- Modal Kenaikan Kelas Massal (Tahun Ajaran Baru) dengan Opsi Pengecualian / Siswa Tinggal Kelas -->
<div class="modal fade" id="modalKenaikanKelasMassal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-success"><i class="fa-solid fa-graduation-cap me-2"></i>Kenaikan Kelas (Tahun Ajaran Baru)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.kenaikan.prosesMassal')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body text-start">
                    <p class="text-dark small mb-3">
                        Prosedur ini akan memproses kenaikan kelas untuk Seluruh Siswa pada Tahun Ajaran Baru:
                    </p>
                    <div class="alert alert-light border rounded-3 p-3 mb-3">
                        <ul class="mb-0 ps-3 small text-dark">
                            <li class="mb-1"><strong>Siswa Kelas 7</strong> &rarr;  naik ke <strong>Kelas 8</strong></li>
                            <li class="mb-1"><strong>Siswa Kelas 8</strong> &rarr;  naik ke <strong>Kelas 9</strong></li>
                            <li><strong>Siswa Kelas 9</strong> &rarr; diubah menjadi <strong>Lulus / ALUMNI</strong></li>
                        </ul>
                    </div>

                    <!-- Accordion: Siswa Tinggal Kelas (Pengecualian) -->
                    <div class="accordion mb-3" id="accordionTinggalKelas">
                        <div class="accordion-item rounded-3 border">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-2 fw-semibold text-warning-emphasis bg-warning bg-opacity-10 rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTinggalKelas">
                                    <i class="fa-solid fa-user-xmark me-2 text-warning"></i> Ada Siswa Tinggal Kelas / Tidak Naik? (Klik untuk memilih pengecualian)
                                </button>
                            </h2>
                            <div id="collapseTinggalKelas" class="accordion-collapse collapse" data-bs-parent="#accordionTinggalKelas">
                                <div class="accordion-body p-3">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                        <input type="text" id="searchTinggalKelas" class="form-control" placeholder="Ketik Nama atau NISN siswa tinggal kelas" onkeyup="filterSiswaTinggalKelas()">
                                    </div>
                                    <small class="text-muted d-block mb-2">
                                        Centang siswa yang <strong>TETAP DI KELAS SAAT INI (Tidak Naik Kelas)</strong>:
                                    </small>
                                    <div class="border rounded-3 p-2 bg-light" style="max-height: 180px; overflow-y: auto;">
                                        <?php $__empty_1 = true; $__currentLoopData = $activeSiswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <div class="form-check py-1 border-bottom border-light item-siswa-tinggal" data-search="<?php echo e(strtolower($s->nama . ' ' . $s->nisn)); ?>">
                                                <input class="form-check-input" type="checkbox" name="except_siswa_ids[]" value="<?php echo e($s->id); ?>" id="exceptSiswa<?php echo e($s->id); ?>">
                                                <label class="form-check-label small text-dark" for="exceptSiswa<?php echo e($s->id); ?>">
                                                    <strong><?php echo e($s->nama); ?></strong> (NISN: <?php echo e($s->nisn); ?> - Kelas <?php echo e($s->kelas->nama_kelas ?? '-'); ?>)
                                                </label>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <span class="text-muted small">Belum ada siswa aktif.</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">Proses Kenaikan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="modalAddKelas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.kelas.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" required placeholder="Contoh: 7, 8, 9">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Wali Kelas Penanggung Jawab</label>
                        <select name="guru_id" class="form-select">
                            <option value="">-- Pilih Wali Kelas --</option>
                            <?php $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($g->id); ?>"><?php echo e($g->nama); ?> (NIP: <?php echo e($g->nip); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kelas Loop -->
<?php $__currentLoopData = $kelases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="modalEditKelas<?php echo e($k->id); ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Edit Data Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('admin.kelas.update', $k->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" value="<?php echo e($k->nama_kelas); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Wali Kelas Penanggung Jawab</label>
                        <select name="guru_id" class="form-select">
                            <option value="">-- Pilih Wali Kelas --</option>
                            <?php $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($g->id); ?>" <?php echo e($k->guru_id == $g->id ? 'selected' : ''); ?>><?php echo e($g->nama); ?> (NIP: <?php echo e($g->nip); ?>)</option>
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

<script>
function filterSiswaTinggalKelas() {
    let input = document.getElementById('searchTinggalKelas').value.toLowerCase().trim();
    let items = document.getElementsByClassName('item-siswa-tinggal');
    for (let i = 0; i < items.length; i++) {
        let text = items[i].getAttribute('data-search') || '';
        if (text.includes(input)) {
            items[i].style.display = 'block';
        } else {
            items[i].style.display = 'none';
        }
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/admin/kelas/index.blade.php ENDPATH**/ ?>