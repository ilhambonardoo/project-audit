<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Temuan Audit - <?= esc((string)$pic['pic_name']) ?></title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            color: #111;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h3 {
            margin: 0;
            padding: 0;
            text-decoration: underline;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 5px 3px;
            vertical-align: top;
        }
        .table th {
            font-size: 8.5px;
            text-transform: uppercase;
        }
        .bg-light {
            background-color: #d1d5db;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 4px;
            border: 1px solid #000;
            background: #0dcaf0;
            font-weight: bold;
            margin-top: 3px;
        }
        .signature-section {
            width: 100%;
            margin-top: 30px;
        }
        .signature-wrapper {
            width: 100%;
        }
        .signature-box {
            width: 30%;
            float: left;
            text-align: center;
        }
        .signature-space {
            height: 60px;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3 class="fw-bold text-uppercase">PEMBAHASAN TEMUAN PT KENCANA ABADI JAYA</h3>
    </div>

    <table class="table">
        <thead>
            <tr class="text-center bg-light">
                <th width="20">No.</th>
                <th width="45">Klausul</th>
                <th width="70">PIC</th>
                <th width="80">Kategori & Status Temuan</th>
                <th>Uraian Temuan</th>
                <th>Rekomendasi</th>
                <th width="80">Tanggapan Auditee</th>
                <th width="50">Level Temuan</th>
                <th width="55">Target Waktu</th>
                <th width="80">Kriteria</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($temuan as $key => $t): ?>
            <tr>
                <td class="text-center"><?= (int)$key + 1 ?></td>
                <td class="text-center"><?= esc((string)$t['klausul']) ?></td>
                <td class="text-center"><?= esc((string)$t['pic_name']) ?></td>
                <td class="text-center"><?= esc((string)$t['kategori_status']) ?></td>
                <td>
                    <ul style="margin: 0; padding-left: 10px;">
                        <li><?= nl2br(esc((string)$t['uraian_temuan'])) ?></li>
                    </ul>
                </td>
                <td>
                    <ul style="margin: 0; padding-left: 10px;">
                        <li><?= nl2br(esc((string)$t['rekomendasi'])) ?></li>
                    </ul>
                </td>
                <td>
                    <?= $t['tanggapan_auditee'] ? '• ' . nl2br(esc((string)$t['tanggapan_auditee'])) : '•' ?>
                </td>
                <td class="text-center">
                    <div class="fw-bold"><?= esc((string)$t['level_temuan']) ?></div>
                    <div class="badge" style="background-color: <?= (trim(strtolower((string)$t['status_progress'])) === 'closed') ? '#0dcaf0' : '#ffc107' ?>;">
                        <?= (trim(strtolower((string)$t['status_progress'])) === 'closed') ? 'CLOSED' : 'OPEN' ?>
                    </div>
                </td>
                <td class="text-center"><?= esc((string)$t['deadline']) ?></td>
                <td>
                    <ul style="margin: 0; padding-left: 10px;">
                        <li><?= esc((string)$t['kriteria']) ?></li>
                    </ul>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <style>
        .signature-space { height: 80px; text-align: center; vertical-align: middle; }
        .signature-img { max-height: 70px; max-width: 150px; }
    </style>
    <div class="signature-section">
        <div class="signature-wrapper">
            <div class="signature-box">
                <p class="fw-bold">Auditor</p>
                <div class="signature-space">
                    <?php if (!empty($pic['auditor_signature_final'])) : ?>
                        <img src="<?= $pic['auditor_signature_final'] ?>" class="signature-img">
                    <?php endif; ?>
                </div>
                <p class="fw-bold">( <?= esc((string)($pic['auditor_name'] ?? '................')) ?> )</p>
            </div>
            <div class="signature-box">
                <p class="fw-bold">Lead Auditor</p>
                <div class="signature-space">
                    <?php if (!empty($pic['lead_auditor_signature'])) : ?>
                        <img src="<?= $pic['lead_auditor_signature'] ?>" class="signature-img">
                    <?php endif; ?>
                </div>
                <p class="fw-bold">( <?= esc((string)($pic['lead_auditor_name'] ?? '................')) ?> )</p>
            </div>
            <div class="signature-box">
                <p class="fw-bold">Auditee / PIC</p>
                <div class="signature-space">
                    <?php if (!empty($pic['pic_signature'])) : ?>
                        <img src="<?= $pic['pic_signature'] ?>" class="signature-img">
                    <?php endif; ?>
                </div>
                <p class="fw-bold">( <?= esc((string)$pic['pic_name']) ?> )</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div style="margin-top: 50px; font-size: 10px; color: #777; text-align: right;">
        Dicetak pada: <?= date('d M Y H:i') ?>
    </div>
</body>
</html>
