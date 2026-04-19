<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row align-items-center my-4">
    <div class="col">
        <h4 class="fw-bold mb-0">Rincian Temuan Audit</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mt-2 mb-0">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/temuan">Daftar Temuan</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
    <div class="col-auto">
        <a href="/temuan" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card card-modern border-0 shadow-sm h-100 overflow-hidden">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i> Laporan Temuan Auditor</h6>
            </div>
            <div class="card-body p-4 p-lg-5">
                <div class="row mb-4">
                    <div class="col-sm-6 mb-3 mb-sm-0 text-muted small">
                        <strong><i class="bi bi-bookmark-fill text-primary"></i> Klausul/Standar</strong>
                        <div class="fs-5 fw-bold text-dark mt-1"><?= $temuan['klausul']; ?></div>
                    </div>
                    <div class="col-sm-6 text-muted small">
                        <?php 
                            $badge_class = 'bg-secondary';
                            if ($temuan['level_temuan'] == 'Tinggi') $badge_class = 'bg-danger';
                            elseif ($temuan['level_temuan'] == 'Menengah') $badge_class = 'bg-warning text-dark';
                            elseif ($temuan['level_temuan'] == 'Rendah') $badge_class = 'bg-success';
                        ?>
                        <strong><i class="bi bi-shield-exclamation text-primary"></i> Level Risiko</strong>
                        <div class="mt-1"><span class="badge <?= $badge_class; ?> rounded-pill px-3 fs-6"><?= $temuan['level_temuan']; ?></span></div>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="fw-bold text-primary mb-3"># <?= $temuan['judul_temuan']; ?></h5>
                    <div class="row g-2">
                        <div class="col-auto"><span class="badge border bg-light text-muted fw-normal p-2"><i class="bi bi-pencil-square"></i> Auditor: <strong><?= $auditor_name; ?></strong></span></div>
                        <div class="col-auto"><span class="badge border bg-light text-muted fw-normal p-2"><i class="bi bi-person"></i> PIC Audit: <strong><?= $pic_name; ?></strong></span></div>
                    </div>
                </div>

                <hr class="my-4 op-10">

                <div class="mb-4">
                    <label class="fw-bold text-uppercase small text-muted mb-2">Uraian / Detail Temuan</label>
                    <div class="bg-light p-3 rounded-3 border-start border-4 border-primary">
                        <?= nl2br($temuan['uraian_temuan']); ?>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="fw-bold text-uppercase small text-muted mb-2">Kriteria / SOP</label>
                        <p class="text-dark small lh-base"><?= esc($temuan['kriteria']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-uppercase small text-muted mb-2">Rekomendasi / Saran</label>
                        <p class="text-dark small lh-base text-success fw-semibold fst-italic"><?= esc($temuan['rekomendasi']); ?></p>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-5">
                    <div class="text-muted small">Dibuat pada: <strong><?= date('d M Y', strtotime($temuan['created_at'])); ?></strong></div>
                    <div class="text-danger small fw-bold"><i class="bi bi-clock"></i> Deadline: <?= date('d M Y', strtotime($temuan['deadline'])); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card card-modern border-0 shadow-sm mb-4 h-100 overflow-hidden">
            <div class="card-header bg-dark text-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2"></i> Status Tindak Lanjut</h6>
            </div>
            <div class="card-body p-4">
                <?php if (empty($tindak_lanjut)) : ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-hourglass-split display-1 text-muted opacity-25"></i>
                        </div>
                        <h6 class="fw-bold">Menunggu Tanggapan PIC</h6>
                        <p class="text-muted small">Auditee belum memberikan respon tindak lanjut untuk temuan ini.</p>
                        <?php if (session()->get('role_id') == 2 && session()->get('id') == $temuan['pic_id']) :?>
                            <?php 
                                if (empty($tindak_lanjut) || $tindak_lanjut['status_verifikasi'] == 'revision_required' || $tindak_lanjut['status_verifikasi'] == 'rejected') :                            
                            ?>
                                
                                <?php if (!empty($tindak_lanjut) && $tindak_lanjut['status_verifikasi'] == 'revision_required'): ?>
                                    <div class="alert alert-danger mb-3">
                                        <strong><i class="bi bi-exclamation-triangle"></i> Revisi Diperlukan!</strong><br>
                                        Catatan: <?= $tindak_lanjut['catatan_auditor']; ?>
                                    </div>
                                <?php endif; ?>

                                <a href="<?= base_url('tindak-lanjut/create/' . $temuan['id']); ?>" class="btn btn-primary rounded-pill px-4">
                                    <i class="bi bi-send me-2"></i> Kirim Tindak Lanjut
                                </a>

                            <?php elseif ($tindak_lanjut['status_verifikasi'] == 'approved') : ?>
                                
                                <div class="alert alert-success text-center">
                                    <h5><i class="bi bi-check-circle-fill"></i> TERVERIFIKASI</h5>
                                    <p class="mb-0">Bukti Anda sedang dalam proses persetujuan Manajemen.</p>
                                </div>

                            <?php endif; ?>

                        <?php endif; ?>
                    </div>
                <?php else : ?>
                    <div class="mb-4">
                        <label class="fw-bold text-uppercase small text-muted mb-2">Tanggapan PIC</label>
                        <div class="p-3 bg-light rounded-3 shadow-sm mb-3">
                             <p class="mb-0"><?= nl2br($tindak_lanjut['tanggapan_auditee']); ?></p>
                             <small class="text-muted mt-2 d-block">Dikirim pada: <?= date('d M Y H:i', strtotime($tindak_lanjut['created_at'])); ?></small>
                        </div>

                        <label class="fw-bold text-uppercase small text-muted mb-2">Bukti Pendukung</label>
                        <div class="list-group mb-3">
                            <?php if (count($bukti_pendukung) > 0) : ?>
                            <?php foreach ($bukti_pendukung as $bukti) : ?>
                                <a href="<?= base_url($bukti['file_path']); ?>" download="<?= $bukti['file_name']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded-3 mb-2 border">
                                    <div class="text-truncate">
                                        <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                        <span class="small"><?= $bukti['file_name']; ?></span>
                                    </div>
                                    <i class="bi bi-download text-muted"></i>
                                </a>
                            <?php endforeach; ?>
                            <?php else : ?>
                                <small class="text-muted d-block ps-2 italic">Tidak ada file bukti diunggah.</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (session()->get('role_id') == 1 && $tindak_lanjut['status_verifikasi'] == 'pending') : ?>
                        <hr class="my-4">
                        <div class="p-4 rounded-4 border-2 border border-warning bg-warning bg-opacity-10">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-pencil-fill me-2"></i> Verifikasi Auditor</h6>
                            <form action="/temuan/verify" method="post">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="temuan_id" value="<?= $temuan['id']; ?>">
                                <input type="hidden" name="tindak_lanjut_id" value="<?= $tindak_lanjut['id']; ?>">

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Ambil Keputusan</label>
                                    <select name="keputusan" class="form-select shadow-none border-2" required>
                                        <option value="" selected disabled>-- Pilih Status Verifikasi --</option>
                                        <option value="approve">Setujui (Approve / Closed)</option>
                                        <option value="reject">Tolak (Revision Required)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Catatan Auditor</label>
                                    <textarea name="catatan_auditor" class="form-control shadow-none border-2" rows="3" placeholder="Berikan alasan persetujuan atau poin revisi..." required></textarea>
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-dark shadow rounded-pill">Kirim Keputusan Verifikasi</button>
                                </div>
                            </form>
                        </div>
                    <?php else : ?>
                        <div class="alert <?= ($tindak_lanjut['status_verifikasi'] == 'approved') ? 'alert-success' : 'alert-warning'; ?> border-0 shadow-sm rounded-4 text-center mt-3">
                            <div class="fs-4 fw-bold mb-1">
                                <?= ($tindak_lanjut['status_verifikasi'] == 'approved') ? '<i class="bi bi-check-circle-fill"></i> TERVERIFIKASI' : '<i class="bi bi-arrow-repeat"></i> SEDANG DIREVISI'; ?>
                            </div>
                            <div class="small fw-normal mb-3"><?= esc($tindak_lanjut['catatan_auditor']); ?></div>

                            <?php if (session()->get('role_id') == 2 && session()->get('id') == $temuan['pic_id'] && $tindak_lanjut['status_verifikasi'] == 'revision_required') : ?>
                                <hr>
                                <p class="small text-muted mb-3">Klik tombol di bawah untuk mengunggah revisi tindak lanjut Anda.</p>
                                <a href="<?= base_url('tindak-lanjut/create/' . $temuan['id']); ?>" class="btn btn-primary rounded-pill px-4">
                                    <i class="bi bi-pencil-square me-2"></i> Kirim Revisi
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
