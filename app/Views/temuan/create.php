<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row align-items-center my-4">
    <div class="col">
        <h4 class="fw-bold mb-0">Input Temuan Baru</h4>
        <p class="text-muted mt-2 mb-0">Lengkapi detail temuan di bawah ini. <span class="badge bg-info text-dark ms-2"><i class="bi bi-alarm"></i> Deadline otomatis: 30 Hari (Early Warning System)</span></p>
    </div>
    <div class="col-auto">
        <a href="/temuan" class="btn btn-light rounded-pill px-4 border shadow-sm">
            <i class="bi bi-arrow-left me-2"></i> Batal
        </a>
    </div>
</div>

<div class="card card-modern border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-primary py-3">
        <h6 class="card-title text-white mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Formulir Auditor</h6>
    </div>
    <div class="card-body p-4 p-lg-5">
        <form action="/temuan/store" method="post" id="formTemuan">
            <?= csrf_field(); ?>

            <div class="row g-4">
                <!-- Klausul & PIC -->
                <div class="col-md-6 mb-3">
                    <label for="klausul" class="form-label fw-semibold">Grup / Klausul Audit</label>
                    <input type="text" class="form-control form-control-lg border-2" id="klausul" name="klausul" placeholder="Contoh: ISO 9001:2015" required autofocus>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="pic_id" class="form-label fw-semibold">Tugaskan ke PIC (Auditee)</label>
                    <select class="form-select form-select-lg border-2 shadow-none" id="pic_id" name="pic_id" required>
                        <option value="" selected disabled>-- Pilih PIC --</option>
                        <?php foreach($users as $user): ?>
                            <option value="<?= $user['id']; ?>"><?= $user['name']; ?> (<?= $user['department']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Judul Temuan -->
                <div class="col-12 mb-3">
                    <label for="judul_temuan" class="form-label fw-semibold">Judul Singkat Temuan</label>
                    <input type="text" class="form-control form-control-lg border-2" id="judul_temuan" name="judul_temuan" placeholder="Tentukan pokok permasalahan" required>
                </div>

                <!-- Uraian Temuan -->
                <div class="col-12 mb-3">
                    <label for="uraian_temuan" class="form-label fw-semibold">Uraian / Detail Ketidaksesuaian</label>
                    <textarea class="form-control border-2 shadow-none" id="uraian_temuan" name="uraian_temuan" rows="4" placeholder="Deskripsikan temuan secara detil..." required></textarea>
                </div>

                <!-- Kriteria/SOP -->
                <div class="col-md-6 mb-3">
                    <label for="kriteria" class="form-label fw-semibold">Kriteria / SOP Terkait</label>
                    <textarea class="form-control border-2 shadow-none" id="kriteria" name="kriteria" rows="3" placeholder="Sebutkan pasal atau standar operasional..." required></textarea>
                </div>

                <!-- Rekomendasi Auditor -->
                <div class="col-md-6 mb-3">
                    <label for="rekomendasi" class="form-label fw-semibold">Rekomendasi / Saran Auditor</label>
                    <textarea class="form-control border-2 shadow-none" id="rekomendasi" name="rekomendasi" rows="3" placeholder="Apa tindakan perbaikan yang disarankan?" required></textarea>
                </div>

                <!-- Kategori & Level Risiko -->
                <div class="col-md-6 mb-3">
                    <label for="kategori_status" class="form-label fw-semibold">Kategori Temuan</label>
                    <div class="d-flex gap-4 mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kategori_status" id="kategori1" value="Temuan Baru" checked required>
                            <label class="form-check-label" for="kategori1">Temuan Baru</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kategori_status" id="kategori2" value="Berulang" required>
                            <label class="form-check-label" for="kategori2">Berulang</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="level_temuan" class="form-label fw-semibold">Level Risiko</label>
                    <select class="form-select border-2 shadow-none" id="level_temuan" name="level_temuan" required>
                        <option value="Rendah">Rendah (Low Risk - Observasi)</option>
                        <option value="Menengah" selected>Menengah (Minor NC)</option>
                        <option value="Tinggi">Tinggi (Major NC)</option>
                    </select>
                </div>
            </div>

            <hr class="my-5">

            <div class="d-grid d-md-flex justify-content-md-end gap-3">
                <button type="reset" class="btn btn-outline-secondary btn-lg px-4 rounded-pill">Reset Form</button>
                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow">
                    <i class="bi bi-cloud-upload me-2"></i> Simpan Temuan & Aktifkan EWS
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
