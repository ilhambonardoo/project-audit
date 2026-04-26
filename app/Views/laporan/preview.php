<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-5 mb-5 px-5">
    <div class="row">
        <div class="col-12 px-4 text-end mb-3">
             <a href="<?= base_url('laporan/exportPdf/' . $pic['pic_id']) ?>" class="btn btn-danger" target="_blank">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <a href="<?= base_url('laporan/exportWord/' . $pic['pic_id']) ?>" class="btn btn-warning">
                <i class="bi bi-file-earmark-word"></i> Export Word
            </a>
        </div>
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="text-center py-4">
                        <h5 class="fw-bold text-uppercase mb-0">PEMBAHASAN TEMUAN PT KENCANA ABADI JAYA</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered audit-table mb-0">
                            <thead>
                                <tr class="text-center bg-gray-200">
                                    <th width="40px">No.</th>
                                    <th width="100px">Klausul</th>
                                    <th width="120px">PIC</th>
                                    <th width="140px">Kategori & Status Temuan</th>
                                    <th>Uraian Temuan</th>
                                    <th>Rekomendasi</th>
                                    <th width="150px">Tanggapan Auditee</th>
                                    <th width="100px">Level Temuan</th>
                                    <th width="100px">Target Waktu Selesai</th>
                                    <th width="150px">Kriteria</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($temuan as $key => $t): ?>
                                <tr>
                                    <td class="text-center"><?= (int)$key + 1 ?></td>
                                    <td class="text-center small"><?= esc((string)$t['klausul']) ?></td>
                                    <td class="text-center small"><?= esc((string)$t['pic_name']) ?></td>
                                    <td class="text-center small"><?= esc((string)$t['kategori_status']) ?></td>
                                    <td class="small">
                                        <ul class="ps-3 mb-0">
                                            <li><?= nl2br(esc((string)$t['uraian_temuan'])) ?></li>
                                        </ul>
                                    </td>
                                    <td class="small">
                                        <ul class="ps-3 mb-0">
                                            <li><?= nl2br(esc((string)$t['rekomendasi'])) ?></li>
                                        </ul>
                                    </td>
                                    <td class="small">
                                        <?= $t['tanggapan_auditee'] ? '• ' . nl2br(esc((string)$t['tanggapan_auditee'])) : '•' ?>
                                    </td>
                                    <td class="text-center small">
                                        <div class="fw-bold mb-1"><?= esc((string)$t['level_temuan']) ?></div>
                                        <?php if (trim(strtolower((string)$t['status_progress'])) === 'closed') : ?>
                                            <span class="badge bg-info text-dark border border-dark rounded-0 px-2">CLOSED</span>
                                        <?php else : ?>
                                            <span class="badge bg-warning text-dark border border-dark rounded-0 px-2">OPEN</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center small"><?= esc((string)$t['deadline']) ?></td>
                                    <td class="small">
                                        <ul class="ps-3 mb-0 text-start">
                                            <li><?= esc((string)$t['kriteria']) ?></li>
                                        </ul>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-5 signature-section-web mt-4 mb-5">
    <div class="row text-center">
        <div class="col-4">
            <p class="fw-bold mb-1">Mengetahui:</p>
            <div class="d-flex align-items-center justify-content-center" style="height: 100px;">
                <?php if (!empty($pic['dept_head_signature'])) : ?>
                    <img src="<?= $pic['dept_head_signature'] ?>" style="max-height: 80px; max-width: 150px;">
                <?php else: ?>
                   <div class="text-muted opacity-25 italic small">Belum Tanda Tangan</div>
                <?php endif; ?>
            </div>
            <p class="fw-bold mb-0">(Ass. Head Corp Finance Controller)</p>
        </div>
        <div class="col-4">
            <p class="fw-bold mb-1">Diperiksa Oleh:</p>
            <div class="d-flex align-items-center justify-content-center" style="height: 100px;">
                <?php if (!empty($pic['director_signature'])) : ?>
                    <img src="<?= $pic['director_signature'] ?>" style="max-height: 80px; max-width: 150px;">
                <?php else: ?>
                   <div class="text-muted opacity-25 italic small">Belum Tanda Tangan</div>
                <?php endif; ?>
            </div>
            <p class="fw-bold mb-0">(Chief Financial Officer)</p>
        </div>
        <div class="col-4">
            <p class="fw-bold mb-1">Disetujui Oleh:</p>
            <div class="d-flex align-items-center justify-content-center" style="height: 100px;">
                <?php if (!empty($pic['plant_manager_signature'])) : ?>
                    <img src="<?= $pic['plant_manager_signature'] ?>" style="max-height: 80px; max-width: 150px;">
                <?php else: ?>
                   <div class="text-muted opacity-25 italic small">Belum Tanda Tangan</div>
                <?php endif; ?>
            </div>
            <p class="fw-bold mb-0">(Plant Manager)</p>
        </div>
    </div>
</div>
        </div>
    </div>

<style>
    .audit-table {
        border: 2px solid #333 !important;
    }
    .audit-table thead th {
        background-color: #e9ecef !important;
        border: 1px solid #333 !important;
        vertical-align: middle;
        font-size: 11px;
        text-transform: uppercase;
        padding: 12px 6px;
    }
    .audit-table tbody td {
        border: 1px solid #333 !important;
        vertical-align: top;
        padding: 12px;
        font-size: 11px;
    }
    .bg-gray-200 {
        background-color: #e9ecef !important;
    }
    .badge {
        font-size: 9px;
    }
    ul li {
        margin-bottom: 5px;
    }
</style>
<?= $this->endSection() ?>

