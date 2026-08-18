<?php $__env->startSection('content'); ?>
<div class="row g-4">
    <!-- Form Pengaturan Fonnte API & Template Pesan -->
    <div class="col-lg-7">
        <div class="card card-custom p-4 shadow-sm border-0 rounded-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i class="fa-brands fa-whatsapp text-success me-2 fs-4"></i>Pengaturan WhatsApp Gateway & Template
                    </h5>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-bolt me-1"></i> Fonnte API
                </span>
            </div>

            <form action="<?php echo e(route('admin.fonnte.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Fonnte API Token</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-key text-warning"></i></span>
                        <input type="password" name="api_token" class="form-control" value="<?php echo e($setting->api_token); ?>" placeholder="Masukkan token perangkat Fonnte Anda">
                    </div>
                </div>

                <div class="form-check form-switch mb-4 p-3 bg-light rounded-3 border">
                    <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active" id="is_active" <?php echo e($setting->is_active ? 'checked' : ''); ?> style="cursor: pointer;">
                    <label class="form-check-label fw-bold text-dark" for="is_active" style="cursor: pointer;">
                        Aktifkan Pengiriman Notifikasi WhatsApp Otomatis ke Orang Tua
                    </label>
                </div>

                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-comments me-2"></i>Kustomisasi Template Pesan WhatsApp</h6>

                <!-- 1. Template Pesan Masuk -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">
                        <span class="badge bg-success me-1">1</span> Template Pesan Presensi MASUK (Tepat Waktu)
                    </label>
                    <textarea name="template_masuk" class="form-control font-monospace text-dark" rows="3" required><?php echo e($setting->template_masuk); ?></textarea>
                    <small class="text-muted">Dikirim saat siswa melakukan scan pertama dan tiba sebelum batas keterlambatan.</small>
                </div>

                <!-- 2. Template Pesan Terlambat -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">
                        <span class="badge bg-warning text-dark me-1">2</span> Template Pesan Presensi TERLAMBAT
                    </label>
                    <textarea name="template_terlambat" class="form-control font-monospace text-dark" rows="3" required><?php echo e($setting->template_terlambat); ?></textarea>
                    <small class="text-muted">Dikirim saat siswa melakukan scan masuk melebihi batas jam keterlambatan.</small>
                </div>

                <!-- 3. Template Pesan Pulang -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">
                        <span class="badge bg-info text-dark me-1">3</span> Template Pesan Presensi PULANG (Kepulangan Siswa)
                    </label>
                    <textarea name="template_pulang" class="form-control font-monospace text-dark" rows="3" required><?php echo e($setting->template_pulang); ?></textarea>
                    <small class="text-muted">Dikirim saat siswa melakukan scan kedua saat jam kepulangan sekolah di sore hari.</small>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Pengaturan Template
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Panduan Variabel & Uji Coba Pengiriman -->
    <div class="col-lg-5">
        <!-- Card Uji Coba Pengiriman Pesan -->
        <div class="card card-custom p-4 shadow-sm border-0 rounded-4">
            <h6 class="fw-bold mb-2 text-dark">
                <i class="fa-solid fa-paper-plane text-success me-2"></i>Uji Coba Pengiriman Pesan WA
            </h6>
            <p class="text-muted small mb-3">Kirim pesan simulasi ke nomor WhatsApp Anda untuk memastikan token dan koneksi server Fonnte berfungsi normal.</p>

            <form action="<?php echo e(route('admin.fonnte.test')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">Nomor WhatsApp Tujuan</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-phone text-muted"></i></span>
                        <input type="text" name="target_no_wa" class="form-control" placeholder="Contoh: 081234567890 / 6281234567890" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">Isi Pesan Uji Coba</label>
                    <textarea name="message" class="form-control font-monospace" rows="3" required>Assalamu'alaikum Warahmatullahi Wabarakatuh.
Ini adalah pesan uji coba sistem presensi SMP IT Al-Muttaqin via Fonnte API Gateway. Koneksi berhasil!</textarea>
                </div>

                <button type="submit" class="btn btn-success rounded-pill w-100 fw-bold shadow-sm">
                    <i class="fa-brands fa-whatsapp me-2"></i> Kirim Pesan Uji Coba Sekarang
                </button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\.gemini\antigravity-ide\scratch\smpit-almutaqin-presensi\resources\views/admin/fonnte/index.blade.php ENDPATH**/ ?>