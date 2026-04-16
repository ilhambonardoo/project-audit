<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row align-items-center my-4">
    <div class="col">
        <h4 class="fw-bold mb-0">Tindak Lanjut Temuan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mt-2 mb-0">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/temuan/show/<?= $temuan['id']; ?>">Detail Temuan</a></li>
                <li class="breadcrumb-item active">Kirim Bukti</li>
            </ol>
        </nav>
    </div>
    <div class="col-auto">
        <a href="/temuan/show/<?= $temuan['id']; ?>" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Batal
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-light">
            <div class="card-body p-4">
                <h6 class="fw-bold text-muted text-uppercase mb-3">Informasi Temuan</h6>
                <h5 class="fw-bold text-dark mb-3"><?= esc($temuan['judul_temuan']); ?></h5>
                
                <div class="mb-3">
                    <label class="small text-muted fw-bold">Uraian Temuan</label>
                    <p class="small mb-0"><?= nl2br($temuan['uraian_temuan']); ?></p>
                </div>

                <div class="mb-3">
                    <label class="small text-muted fw-bold">Rekomendasi Auditor</label>
                    <div class="p-2 bg-white border rounded-3 small text-success fst-italic">
                        <?= nl2br($temuan['rekomendasi']); ?>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <span class="small text-muted">Batas Waktu:</span>
                    <span class="badge bg-danger rounded-pill px-3"><?= date('d M Y', strtotime($temuan['deadline'])); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-primary text-white py-3 rounded-top-4">
                <h6 class="mb-0 fw-bold"><i class="bi bi-cloud-arrow-up me-2"></i> Form Bukti Perbaikan</h6>
            </div>
            <div class="card-body p-4 p-lg-5">
                
                <form action="/tindak-lanjut/store" method="post" enctype="multipart/form-data">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="temuan_id" value="<?= $temuan['id']; ?>">

                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger rounded-3 small">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <?= session()->getFlashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Tanggapan / Keterangan Perbaikan <span class="text-danger">*</span></label>
                        <textarea class="form-control bg-light border-0 shadow-none" name="tanggapan_auditee" rows="5" placeholder="Jelaskan tindakan perbaikan yang telah dilakukan oleh divisi Anda..." required><?= old('tanggapan_auditee'); ?></textarea>
                        <div class="form-text small">Jelaskan secara rinci tindakan *corrective* yang sudah diambil.</div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold small text-muted">Upload Bukti Pendukung <span class="text-danger">*</span></label>
                        <input type="file" class="form-control shadow-none" name="file_bukti" accept=".pdf, .jpg, .jpeg, .png" required>
                        <div class="form-text small text-danger"><i class="bi bi-info-circle me-1"></i> Format diizinkan: PDF, JPG, PNG. Maksimal ukuran: 5MB.</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold shadow-sm">
                            <i class="bi bi-send me-2"></i> Kirim Bukti ke Auditor
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>