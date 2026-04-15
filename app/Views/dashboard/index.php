<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-4">
    <h3 class="fw-bold mb-0">Dashboard Monitoring</h3>
    <div class="text-muted small">
        <i class="bi bi-calendar-event me-1"></i> Tanggal Hari Ini: <strong><?= date('d M Y', strtotime($today)) ?></strong>
    </div>
</div>

<?php if (session()->get('role_id') == 1) : ?>
    <div class="row mb-4">
        <?php if ($overdue_count > 0) : ?>
            <div class="col-md-6 mb-3">
                <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center h-100 mb-0 rounded-4">
                    <i class="bi bi-exclamation-triangle-fill fs-1 me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Peringatan Keterlambatan!</h6>
                        <p class="mb-0 small">Terdapat <strong><?= $overdue_count ?> temuan</strong> yang telah melewati batas waktu (Overdue). Segera lakukan eskalasi atau hubungi PIC terkait.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($pending_verif > 0) : ?>
            <div class="col-md-6 mb-3">
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center h-100 mb-0 rounded-4">
                    <i class="bi bi-shield-exclamation fs-1 me-3 text-dark"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Menunggu Verifikasi</h6>
                        <p class="mb-0 small text-dark">Terdapat <strong><?= $pending_verif ?> bukti tindak lanjut</strong> dari PIC yang menunggu keputusan Anda (Approve/Reject).</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="row g-3 mb-5">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-light mb-1">Total Temuan</h6>
                    <h2 class="fw-bold mb-0"><?= $total ?></h2>
                </div>
                <i class="bi bi-files display-4 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-info text-white h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-light mb-1">Status Open</h6>
                    <h2 class="fw-bold mb-0"><?= $open ?></h2>
                </div>
                <i class="bi bi-envelope-open display-4 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-light mb-1">On Progress</h6>
                    <h2 class="fw-bold mb-0"><?= $on_progress ?></h2>
                </div>
                <i class="bi bi-arrow-repeat display-4 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-success text-white h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-light mb-1">Closed / Selesai</h6>
                    <h2 class="fw-bold mb-0"><?= $closed ?></h2>
                </div>
                <i class="bi bi-check-circle display-4 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center">
        <i class="bi bi-bell-fill text-danger me-2 fs-5"></i>
        <h6 class="mb-0 fw-bold">Early Warning System (Mendekati Deadline)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small">
                    <tr>
                        <th class="ps-4 py-3">Judul Temuan</th>
                        <th>Klausul</th>
                        <th>Status</th>
                        <th>Batas Waktu</th>
                        <th>Sisa Waktu</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($early_warning)) : ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-check2-circle fs-2 d-block mb-2 text-success"></i>
                                Aman. Tidak ada temuan yang mendekati batas waktu.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($early_warning as $row) : 
                            $tgl_deadline = strtotime($row['deadline']);
                            $tgl_sekarang = strtotime($today);
                            $selisih_detik = $tgl_deadline - $tgl_sekarang;
                            $sisa_hari = floor($selisih_detik / (60 * 60 * 24));

                            if ($sisa_hari < 0) {
                                $badge_color = 'bg-danger';
                                $status_text = 'Overdue (' . abs($sisa_hari) . ' hari)';
                            } elseif ($sisa_hari <= 3) {
                                $badge_color = 'bg-danger bg-opacity-75'; 
                                $status_text = $sisa_hari . ' Hari Lagi';
                            } elseif ($sisa_hari <= 7) {
                                $badge_color = 'bg-warning text-dark';
                                $status_text = $sisa_hari . ' Hari Lagi';
                            } elseif ($sisa_hari <= 10) {
                                $badge_color = 'bg-info text-dark';
                                $status_text = $sisa_hari . ' Hari Lagi';
                            } elseif ($sisa_hari <= 20) {
                                $badge_color = 'bg-primary text-white';
                                $status_text = $sisa_hari . ' Hari Lagi';
                            } elseif ($sisa_hari <= 30) {
                                $badge_color = 'bg-success bg-opacity-75 text-white';
                                $status_text = $sisa_hari . ' Hari Lagi';
                            } else {
                                $badge_color = 'bg-success';
                                $status_text = $sisa_hari . ' Hari Lagi';
                            }
                        ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark"><?= $row['judul_temuan'] ?></td>
                                <td><?= $row['klausul'] ?></td>
                                <td>
                                    <span class="badge border bg-light text-dark fw-normal"><?= $row['status_progress'] ?></span>
                                </td>
                                <td><?= date('d M Y', strtotime($row['deadline'])) ?></td>
                                <td>
                                    <span class="badge <?= $badge_color ?> rounded-pill px-3"><?= $status_text ?></span>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="/temuan/show/<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>