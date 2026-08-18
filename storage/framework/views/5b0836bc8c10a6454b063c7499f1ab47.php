<?php $__env->startSection('content'); ?>
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-clipboard-user me-2 text-primary"></i>Rekapitulasi Kehadiran Siswa
                <?php if(auth()->user()->role === 'guru' && isset($waliKelas) && $waliKelas): ?>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 fs-7 ms-2 rounded-pill">
                        <i class="fa-solid fa-user-shield me-1"></i> Kelas <?php echo e($waliKelas->nama_kelas); ?>

                    </span>
                <?php endif; ?>
            </h5>
            <small class="text-muted">
                Laporan presensi harian, rekapitulasi bulanan, dan semester
            </small>
        </div>

        <div class="d-flex align-items-center gap-3 flex-wrap">
            <!-- Mode Switch Pills -->
            <div class="btn-group p-1 bg-light rounded-pill border">
                <a href="<?php echo e(route(auth()->user()->role === 'guru' ? 'guru.rekap' : 'admin.rekap.index', ['mode' => 'harian', 'tanggal' => $tanggal, 'kelas_id' => $kelasId, 'sort_by' => $sortBy])); ?>" class="btn btn-sm rounded-pill <?php echo e($mode === 'harian' ? 'btn-primary shadow-sm' : 'btn-light text-muted'); ?>">
                    <i class="fa-solid fa-calendar-day me-1"></i> Harian
                </a>
                <a href="<?php echo e(route(auth()->user()->role === 'guru' ? 'guru.rekap' : 'admin.rekap.index', ['mode' => 'bulanan', 'bulan' => $bulan, 'tahun' => $tahun, 'kelas_id' => $kelasId, 'sort_by' => $sortBy])); ?>" class="btn btn-sm rounded-pill <?php echo e($mode === 'bulanan' ? 'btn-primary shadow-sm' : 'btn-light text-muted'); ?>">
                    <i class="fa-solid fa-chart-column me-1"></i> Bulanan
                </a>
                <a href="<?php echo e(route(auth()->user()->role === 'guru' ? 'guru.rekap' : 'admin.rekap.index', ['mode' => 'semester', 'semester' => $semester ?? 1, 'tahun_ajaran' => $tahunAjaran ?? (date('Y').'/'.(date('Y')+1)), 'kelas_id' => $kelasId, 'sort_by' => $sortBy])); ?>" class="btn btn-sm rounded-pill <?php echo e($mode === 'semester' ? 'btn-primary shadow-sm' : 'btn-light text-muted'); ?>">
                    <i class="fa-solid fa-graduation-cap me-1"></i> Semester
                </a>
            </div>

            <a href="<?php echo e(route(auth()->user()->role === 'guru' ? 'guru.rekap.cetak' : 'admin.rekap.cetak', ['mode' => $mode, 'bulan' => $bulan, 'tahun' => $tahun, 'semester' => $semester ?? 1, 'tahun_ajaran' => $tahunAjaran ?? (date('Y').'/'.(date('Y')+1)), 'kelas_id' => $kelasId, 'sort_by' => $sortBy])); ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
                <i class="fa-solid fa-print me-1"></i> Cetak Laporan
            </a>
        </div>
    </div>

    <?php if($mode === 'harian'): ?>
        <!-- Filter Form Harian -->
        <form method="GET" action="<?php echo e(route(auth()->user()->role === 'guru' ? 'guru.rekap' : 'admin.rekap.index')); ?>" class="row g-3 mb-4">
            <input type="hidden" name="mode" value="harian">
            <div class="col-md-4">
                <label class="form-label fw-semibold text-dark">Pilih Tanggal Presensi</label>
                <input type="date" name="tanggal" value="<?php echo e($tanggal); ?>" class="form-control" onchange="this.form.submit()">
            </div>
            <?php if(auth()->user()->role === 'admin'): ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-dark">Filter Kelas</label>
                <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    <?php $__currentLoopData = $kelases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k->id); ?>" <?php echo e($kelasId == $k->id ? 'selected' : ''); ?>>Kelas <?php echo e($k->nama_kelas); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-dark">Urutkan Data</label>
                <select name="sort_by" class="form-select" onchange="this.form.submit()">
                    <option value="nama_asc" <?php echo e($sortBy === 'nama_asc' ? 'selected' : ''); ?>>Nama Siswa (A-Z)</option>
                    <option value="nama_desc" <?php echo e($sortBy === 'nama_desc' ? 'selected' : ''); ?>>Nama Siswa (Z-A)</option>
                    <option value="nisn" <?php echo e($sortBy === 'nisn' ? 'selected' : ''); ?>>NISN Siswa</option>
                </select>
            </div>
        </form>

        <!-- Tabel Rekap Harian -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th style="width: 45px;" class="text-dark">No</th>
                        <th class="text-dark">NISN</th>
                        <th class="text-dark text-start">Nama Peserta Didik</th>
                        <th style="width: 55px;" class="text-dark">JK</th>
                        <th class="text-dark">Kelas</th>
                        <th class="text-dark">Jam Masuk (Pagi)</th>
                        <th class="text-dark">Jam Pulang (Sore)</th>
                        <th class="text-dark">Status Kehadiran Harian</th>
                        <th style="width: 120px;" class="text-dark">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $harianData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-center text-muted fw-semibold"><?php echo e($index + 1); ?></td>
                        <td class="text-center">
                            <span class="fw-bold text-dark font-monospace"><?php echo e($row['siswa']->nisn); ?></span>
                        </td>
                        <td class="fw-semibold text-dark"><?php echo e($row['siswa']->nama); ?></td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary-emphasis border px-2 py-1 fw-bold"><?php echo e($row['siswa']->jenis_kelamin); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-3 py-1">Kelas <?php echo e($row['siswa']->kelas->nama_kelas ?? '-'); ?></span>
                        </td>
                        <td class="text-center">
                            <?php if($row['jam_masuk'] !== '-'): ?>
                                <span class="badge bg-light text-dark border px-2 py-1 font-monospace fw-bold"><i class="fa-solid fa-clock text-primary me-1"></i><?php echo e($row['jam_masuk']); ?></span>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($row['jam_pulang'] !== '-'): ?>
                                <span class="badge bg-light text-dark border px-2 py-1 font-monospace fw-bold"><i class="fa-solid fa-door-open text-success me-1"></i><?php echo e($row['jam_pulang']); ?></span>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($row['status'] === 'HADIR'): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                                    <i class="fa-solid fa-circle-check me-1"></i> HADIR
                                </span>
                            <?php elseif($row['status'] === 'TERLAMBAT'): ?>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-bold">
                                    <i class="fa-solid fa-clock me-1"></i> TERLAMBAT
                                </span>
                            <?php elseif($row['status'] === 'IZIN'): ?>
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-2 rounded-pill fw-bold">
                                    <i class="fa-solid fa-envelope me-1"></i> IZIN
                                </span>
                            <?php elseif($row['status'] === 'SAKIT'): ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold">
                                    <i class="fa-solid fa-notes-medical me-1"></i> SAKIT
                                </span>
                            <?php elseif($row['status'] === 'ALPA'): ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold">
                                    <i class="fa-solid fa-circle-xmark me-1"></i> ALPA
                                </span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-semibold">
                                    <i class="fa-regular fa-circle me-1"></i> BELUM ABSEN
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalUpdateStatus<?php echo e($row['siswa']->id); ?>">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Set Status
                            </button>

                            <!-- Modal Update Status Harian -->
                            <div class="modal fade" id="modalUpdateStatus<?php echo e($row['siswa']->id); ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <div class="modal-content rounded-4 border-0">
                                        <div class="modal-header border-0 pb-0">
                                            <h6 class="fw-bold">Set Status Presensi</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?php echo e(route(auth()->user()->role === 'guru' ? 'guru.rekap.updateStatus' : 'admin.rekap.updateStatus')); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="siswa_id" value="<?php echo e($row['siswa']->id); ?>">
                                            <input type="hidden" name="tanggal" value="<?php echo e($tanggal); ?>">

                                            <div class="modal-body text-start">
                                                <p class="small text-muted mb-2">Siswa: <strong><?php echo e($row['siswa']->nama); ?></strong></p>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold small">Pilih Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="HADIR" <?php echo e($row['status'] == 'HADIR' ? 'selected' : ''); ?>>Hadir</option>
                                                        <option value="TERLAMBAT" <?php echo e($row['status'] == 'TERLAMBAT' ? 'selected' : ''); ?>>Terlambat</option>
                                                        <option value="IZIN" <?php echo e($row['status'] == 'IZIN' ? 'selected' : ''); ?>>Izin</option>
                                                        <option value="SAKIT" <?php echo e($row['status'] == 'SAKIT' ? 'selected' : ''); ?>>Sakit</option>
                                                        <option value="ALPA" <?php echo e($row['status'] == 'ALPA' ? 'selected' : ''); ?>>Alpa</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light btn-sm rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">Data siswa tidak ditemukan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php elseif($mode === 'bulanan'): ?>
        <!-- Filter Form Bulanan -->
        <form method="GET" action="<?php echo e(route(auth()->user()->role === 'guru' ? 'guru.rekap' : 'admin.rekap.index')); ?>" class="row g-3 mb-4">
            <input type="hidden" name="mode" value="bulanan">
            <div class="col-md-3">
                <label class="form-label fw-semibold text-dark">Pilih Bulan</label>
                <select name="bulan" class="form-select" onchange="this.form.submit()">
                    <?php for($m=1; $m<=12; $m++): ?>
                        <option value="<?php echo e($m); ?>" <?php echo e($bulan == $m ? 'selected' : ''); ?>><?php echo e(DateTime::createFromFormat('!m', $m)->format('F')); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold text-dark">Pilih Tahun</label>
                <select name="tahun" class="form-select" onchange="this.form.submit()">
                    <?php for($y=date('Y')-2; $y<=date('Y')+1; $y++): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e($tahun == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <?php if(auth()->user()->role === 'admin'): ?>
            <div class="col-md-3">
                <label class="form-label fw-semibold text-dark">Filter Kelas</label>
                <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    <?php $__currentLoopData = $kelases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k->id); ?>" <?php echo e($kelasId == $k->id ? 'selected' : ''); ?>>Kelas <?php echo e($k->nama_kelas); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-md-3">
                <label class="form-label fw-semibold text-dark">Urutkan (Sorting)</label>
                <select name="sort_by" class="form-select" onchange="this.form.submit()">
                    <option value="nama_asc" <?php echo e($sortBy === 'nama_asc' ? 'selected' : ''); ?>>Nama Siswa (A-Z)</option>
                    <option value="nama_desc" <?php echo e($sortBy === 'nama_desc' ? 'selected' : ''); ?>>Nama Siswa (Z-A)</option>
                    <option value="nisn" <?php echo e($sortBy === 'nisn' ? 'selected' : ''); ?>>NISN Siswa</option>
                    <option value="persentase_desc" <?php echo e($sortBy === 'persentase_desc' ? 'selected' : ''); ?>>Persentase Kehadiran (%) Tertinggi</option>
                    <option value="persentase_asc" <?php echo e($sortBy === 'persentase_asc' ? 'selected' : ''); ?>>Persentase Kehadiran (%) Terendah</option>
                    <option value="terlambat_desc" <?php echo e($sortBy === 'terlambat_desc' ? 'selected' : ''); ?>>Terlambat Terbanyak</option>
                </select>
            </div>
        </form>

        <!-- Tabel Rekap Bulanan -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th rowspan="2" class="align-middle text-dark" style="width: 45px;">No</th>
                        <th rowspan="2" class="align-middle text-dark">NISN</th>
                        <th rowspan="2" class="align-middle text-dark text-start">Nama Peserta Didik</th>
                        <th rowspan="2" class="align-middle text-dark" style="width: 55px;">JK</th>
                        <th rowspan="2" class="align-middle text-dark">Kelas</th>
                        <th colspan="5" class="text-dark fw-bold bg-light">Akumulasi Kehadiran (Bulan <?php echo e(DateTime::createFromFormat('!m', $bulan)->format('F')); ?> <?php echo e($tahun); ?>)</th>
                        <th rowspan="2" class="align-middle bg-primary text-white" style="width: 140px;">Persentase (%)</th>
                    </tr>
                    <tr class="bg-light">
                        <th class="text-dark fw-bold text-nowrap px-3 py-2"><i class="fa-solid fa-circle-check text-success me-1"></i>Hadir</th>
                        <th class="text-dark fw-bold text-nowrap px-3 py-2"><i class="fa-solid fa-clock text-warning-emphasis me-1"></i>Terlambat</th>
                        <th class="text-dark fw-bold text-nowrap px-3 py-2"><i class="fa-solid fa-envelope text-info-emphasis me-1"></i>Izin</th>
                        <th class="text-dark fw-bold text-nowrap px-3 py-2"><i class="fa-solid fa-notes-medical text-primary me-1"></i>Sakit</th>
                        <th class="text-dark fw-bold text-nowrap px-3 py-2"><i class="fa-solid fa-circle-xmark text-danger me-1"></i>Alpa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rekapData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-center text-muted fw-semibold"><?php echo e($index + 1); ?></td>
                        <td class="text-center">
                            <span class="fw-bold text-dark font-monospace"><?php echo e($row['siswa']->nisn); ?></span>
                        </td>
                        <td class="fw-semibold text-dark"><?php echo e($row['siswa']->nama); ?></td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary-emphasis border px-2 py-1 fw-bold"><?php echo e($row['siswa']->jenis_kelamin); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-3 py-1">Kelas <?php echo e($row['siswa']->kelas->nama_kelas ?? '-'); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-7 fw-bold"><?php echo e($row['hadir']); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fs-7 fw-bold"><?php echo e($row['terlambat']); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1 fs-7 fw-bold"><?php echo e($row['izin']); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-7 fw-bold"><?php echo e($row['sakit']); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-7 fw-bold"><?php echo e($row['alpa']); ?></span>
                        </td>
                        <td class="text-center">
                            <?php if($row['persentase'] >= 85): ?>
                                <span class="badge bg-success px-3 py-2 fs-7 fw-bold shadow-sm">
                                    <i class="fa-solid fa-circle-check me-1"></i> <?php echo e($row['persentase']); ?>%
                                </span>
                            <?php elseif($row['persentase'] >= 75): ?>
                                <span class="badge bg-primary px-3 py-2 fs-7 fw-bold shadow-sm">
                                    <?php echo e($row['persentase']); ?>%
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger px-3 py-2 fs-7 fw-bold shadow-sm">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> <?php echo e($row['persentase']); ?>%
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">Data siswa tidak ditemukan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <!-- Filter Form Semester -->
        <form method="GET" action="<?php echo e(route(auth()->user()->role === 'guru' ? 'guru.rekap' : 'admin.rekap.index')); ?>" class="row g-3 mb-4">
            <input type="hidden" name="mode" value="semester">
            <div class="col-md-3">
                <label class="form-label fw-semibold text-dark">Tahun Ajaran</label>
                <select name="tahun_ajaran" class="form-select" onchange="this.form.submit()">
                    <?php
                        $currY = (int)date('Y');
                    ?>
                    <?php for($y = $currY - 2; $y <= $currY + 1; $y++): ?>
                        <?php $ta = $y . '/' . ($y + 1); ?>
                        <option value="<?php echo e($ta); ?>" <?php echo e(($tahunAjaran ?? '') === $ta ? 'selected' : ''); ?>>Tahun Ajaran <?php echo e($ta); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold text-dark">Pilih Semester</label>
                <select name="semester" class="form-select" onchange="this.form.submit()">
                    <option value="1" <?php echo e(($semester ?? 1) == 1 ? 'selected' : ''); ?>>Semester 1 (Ganjil: Juli-Desember)</option>
                    <option value="2" <?php echo e(($semester ?? 1) == 2 ? 'selected' : ''); ?>>Semester 2 (Genap: Januari-Juni)</option>
                </select>
            </div>
            <?php if(auth()->user()->role === 'admin'): ?>
            <div class="col-md-3">
                <label class="form-label fw-semibold text-dark">Filter Kelas</label>
                <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    <?php $__currentLoopData = $kelases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k->id); ?>" <?php echo e($kelasId == $k->id ? 'selected' : ''); ?>>Kelas <?php echo e($k->nama_kelas); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-md-3">
                <label class="form-label fw-semibold text-dark">Urutkan (Sorting)</label>
                <select name="sort_by" class="form-select" onchange="this.form.submit()">
                    <option value="nama_asc" <?php echo e($sortBy === 'nama_asc' ? 'selected' : ''); ?>>Nama Siswa (A-Z)</option>
                    <option value="nama_desc" <?php echo e($sortBy === 'nama_desc' ? 'selected' : ''); ?>>Nama Siswa (Z-A)</option>
                    <option value="nisn" <?php echo e($sortBy === 'nisn' ? 'selected' : ''); ?>>NISN Siswa</option>
                    <option value="persentase_desc" <?php echo e($sortBy === 'persentase_desc' ? 'selected' : ''); ?>>Persentase Kehadiran (%) Tertinggi</option>
                    <option value="persentase_asc" <?php echo e($sortBy === 'persentase_asc' ? 'selected' : ''); ?>>Persentase Kehadiran (%) Terendah</option>
                    <option value="terlambat_desc" <?php echo e($sortBy === 'terlambat_desc' ? 'selected' : ''); ?>>Terlambat Terbanyak</option>
                </select>
            </div>
        </form>

        <!-- Tabel Rekap Semester -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th rowspan="2" class="align-middle text-dark" style="width: 45px;">No</th>
                        <th rowspan="2" class="align-middle text-dark">NISN</th>
                        <th rowspan="2" class="align-middle text-dark text-start">Nama Peserta Didik</th>
                        <th rowspan="2" class="align-middle text-dark" style="width: 55px;">JK</th>
                        <th rowspan="2" class="align-middle text-dark">Kelas</th>
                        <th colspan="5" class="text-dark fw-bold bg-light">Rekapitulasi Akumulasi Semester <?php echo e($semester == 1 ? '1 (Ganjil)' : '2 (Genap)'); ?> - TA <?php echo e($tahunAjaran); ?></th>
                        <th rowspan="2" class="align-middle bg-primary text-white" style="width: 140px;">Persentase (%)</th>
                    </tr>
                    <tr class="bg-light">
                        <th class="text-dark fw-bold text-nowrap px-3 py-2"><i class="fa-solid fa-circle-check text-success me-1"></i>Hadir</th>
                        <th class="text-dark fw-bold text-nowrap px-3 py-2"><i class="fa-solid fa-clock text-warning-emphasis me-1"></i>Terlambat</th>
                        <th class="text-dark fw-bold text-nowrap px-3 py-2"><i class="fa-solid fa-envelope text-info-emphasis me-1"></i>Izin</th>
                        <th class="text-dark fw-bold text-nowrap px-3 py-2"><i class="fa-solid fa-notes-medical text-primary me-1"></i>Sakit</th>
                        <th class="text-dark fw-bold text-nowrap px-3 py-2"><i class="fa-solid fa-circle-xmark text-danger me-1"></i>Alpa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $rekapData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-center text-muted fw-semibold"><?php echo e($index + 1); ?></td>
                        <td class="text-center">
                            <span class="fw-bold text-dark font-monospace"><?php echo e($row['siswa']->nisn); ?></span>
                        </td>
                        <td class="fw-semibold text-dark"><?php echo e($row['siswa']->nama); ?></td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary-emphasis border px-2 py-1 fw-bold"><?php echo e($row['siswa']->jenis_kelamin); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-3 py-1">Kelas <?php echo e($row['siswa']->kelas->nama_kelas ?? '-'); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-7 fw-bold"><?php echo e($row['hadir']); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fs-7 fw-bold"><?php echo e($row['terlambat']); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1 fs-7 fw-bold"><?php echo e($row['izin']); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-7 fw-bold"><?php echo e($row['sakit']); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-7 fw-bold"><?php echo e($row['alpa']); ?></span>
                        </td>
                        <td class="text-center">
                            <?php if($row['persentase'] >= 85): ?>
                                <span class="badge bg-success px-3 py-2 fs-7 fw-bold shadow-sm">
                                    <i class="fa-solid fa-circle-check me-1"></i> <?php echo e($row['persentase']); ?>%
                                </span>
                            <?php elseif($row['persentase'] >= 75): ?>
                                <span class="badge bg-primary px-3 py-2 fs-7 fw-bold shadow-sm">
                                    <?php echo e($row['persentase']); ?>%
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger px-3 py-2 fs-7 fw-bold shadow-sm">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> <?php echo e($row['persentase']); ?>%
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">Data siswa tidak ditemukan untuk semester ini.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/admin/rekap/rekap.blade.php ENDPATH**/ ?>