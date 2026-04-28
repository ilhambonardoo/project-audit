<?php 
/** 
 * @var array{id: int|string, klausul: string, level_temuan: string, judul_temuan: string, uraian_temuan: string, kriteria: string, rekomendasi: string, pic_id: int|string, deadline: string, kategori_status: string, status_progress: string, catatan_revisi: string|null} $temuan
 * @var array[] $users
 */ 
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row align-items-center my-4">
    <div class="col">
        <h4 class="fw-bold mb-0">Edit Temuan Audit</h4>
    </div>
    <div class="col-auto">
        <a href="/temuan" class="btn btn-outline-secondary rounded-pill px-4">Kembali</a>
    </div>
</div>

<div class="card card-modern border-0 shadow-sm">
    <div class="card-body p-4 p-lg-5">
        <?php if ($temuan['status_progress'] == 'Draft' && !empty($temuan['catatan_revisi'])): ?>
            <div class="alert alert-warning border-0 shadow-sm mb-4">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Catatan Revisi Lead Auditor</h6>
                        <p class="mb-0 small"><strong><?= $temuan['catatan_revisi']; ?></strong></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form action="/temuan/update/<?= $temuan['id']; ?>" method="post">
            <?= csrf_field(); ?>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">Klausul / Standar</label>
                    <input type="text" class="form-control" name="klausul" value="<?= esc($temuan['klausul']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">Level Risiko</label>
                    <select class="form-select" name="level_temuan" required>
                        <option value="Rendah" <?= ($temuan['level_temuan'] == 'Rendah') ? 'selected' : ''; ?>>Rendah</option>
                        <option value="Menengah" <?= ($temuan['level_temuan'] == 'Menengah') ? 'selected' : ''; ?>>Menengah</option>
                        <option value="Tinggi" <?= ($temuan['level_temuan'] == 'Tinggi') ? 'selected' : ''; ?>>Tinggi</option>
                    </select>
                </div>
                
                <div class="col-md-12">
                    <label class="form-label fw-bold small text-muted">Judul Temuan</label>
                    <input type="text" class="form-control" name="judul_temuan" value="<?= esc($temuan['judul_temuan']); ?>" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-bold small text-muted">Uraian / Detail Temuan</label>
                    <textarea class="form-control" name="uraian_temuan" rows="4" required><?= esc($temuan['uraian_temuan']); ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">Kriteria / SOP</label>
                    <textarea class="form-control" name="kriteria" rows="3" required><?= esc($temuan['kriteria']); ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">Rekomendasi / Saran</label>
                    <textarea class="form-control" name="rekomendasi" rows="3" required><?= esc($temuan['rekomendasi']); ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">Auditee</label>
                    <select class="form-select" name="pic_id" required>
                        <?php foreach ($users as $user) : ?>
                            <option value="<?= $user['id']; ?>" <?= ($temuan['pic_id'] == $user['id']) ? 'selected' : ''; ?>>
                                <?= $user['name']; ?> (<?= $user['department']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">Batas Waktu (Deadline)</label>
                    <input type="date" class="form-control" name="deadline" value="<?= esc($temuan['deadline']); ?>" required>
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-label fw-bold small text-muted">Kategori Temuan</label>
                    <div class="d-flex gap-4 mt-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kategori_status" id="ekategori1" value="Temuan Baru" <?= ($temuan['kategori_status'] == 'Temuan Baru') ? 'checked' : ''; ?> required>
                            <label class="form-check-label" for="ekategori1">Temuan Baru</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kategori_status" id="ekategori2" value="Temuan Berulang 1" <?= ($temuan['kategori_status'] == 'Temuan Berulang 1') ? 'checked' : ''; ?> required>
                            <label class="form-check-label" for="ekategori2">Temuan Berulang 1</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kategori_status" id="ekategori3" value="Temuan Berulang 2" <?= ($temuan['kategori_status'] == 'Temuan Berulang 2') ? 'checked' : ''; ?> required>
                            <label class="form-check-label" for="ekategori3">Temuan Berulang 2</label>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary rounded-pill px-5">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>