<?php
// === LOGIKA DINAMIS UNTUK COVER ===

// 1. Array Bulan Romawi untuk Nomor Surat
$bulan_romawi = [
    1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
    7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
];

// 2. Array Nama Bulan Indonesia untuk Bagian Bawah
$bulan_indo = [
    1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL', 5 => 'MEI', 6 => 'JUNI',
    7 => 'JULI', 8 => 'AGUSTUS', 9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
];

// 3. Ambil Bulan dan Tahun Saat Ini
$bulan_sekarang = date('n'); // Menghasilkan angka 1-12
$tahun_sekarang = date('Y'); // Menghasilkan angka tahun (contoh: 2026)

$romawi = $bulan_romawi[$bulan_sekarang];
$nama_bulan = $bulan_indo[$bulan_sekarang];

// 4. Menangani Nomor Urut (Ubah nilai ini sesuai variabel dari database Anda)
// Jika variabel $no_urut belum dikirim dari controller, default-nya kita set ke 5
$nomor_input = isset($no_urut) ? $no_urut : 5; 
$nomor_3_digit = str_pad($nomor_input, 3, '0', STR_PAD_LEFT); // Mengubah 5 menjadi 005

// 5. Gabungkan menjadi nomor dokumen utuh
$nomor_dokumen = "No." . $nomor_3_digit . "/AUD/IA/" . $romawi . "/" . $tahun_sekarang;

// 6. Nama Departemen Dinamis (jika ada)
$nama_departemen = isset($pic['department']) ? 'DEPARTEMEN ' . strtoupper(esc((string)$pic['department'])) : '';

// 7. Base64 Logo untuk Dompdf agar tidak terkendala path/chroot/network deadlock
$logo_path = FCPATH . 'logo/sanqua.png';
$logo_base64 = '';
if (file_exists($logo_path)) {
    $logo_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path));
}
?>
<!DOCTYPE html>
<html lang="id">
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
        
        /* CSS Tambahan untuk Cover Page */
        .cover-page {
            text-align: center;
            padding-top: 30px;
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000;
        }
        .cover-logo {
            width: 180px; /* Sesuaikan ukuran logo */
            height: auto;
            margin-bottom: 15px;
        }
        .cover-company {
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
        }
        .cover-subtitle {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
            letter-spacing: 1px;
        }
        .cover-box {
            border: 2px solid #000;
            border-radius: 25px;
            width: 75%;
            margin: 40px auto 0 auto;
            padding: 40px 20px;
            text-align: center;
            line-height: 1.8;
        }
        .box-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .box-text {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
        }
        .page-break {
            page-break-after: always;
        }

        /* CSS Halaman Utama (Bawaan Lama) */
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
        .signature-space { height: 80px; text-align: center; vertical-align: middle; }
        .signature-img { max-height: 70px; max-width: 150px; }
    </style>
</head>
<body>

    <div class="cover-page">
        <img src="<?= $logo_base64 ?>" class="cover-logo" alt="Logo SanQua">
        <div class="cover-company">PT SanQua Multi Internasional</div>
        <div class="cover-subtitle">INTERNAL AUDIT</div>
        <div class="cover-subtitle">HEAD OFFICE</div>

        <div class="cover-box">
            <div class="box-title">Laporan Hasil Audit</div>
            <div class="box-text"><?= $nomor_dokumen ?></div>
            <div class="box-text">PT SANQUA MULTI INTERNATIONAL</div>
            <div class="box-text"><?= $nama_departemen ?></div>
            <div class="box-text" style="margin-top: 30px;"><?= $nama_bulan . ' ' . $tahun_sekarang ?></div>
        </div>
    </div>

    <div class="page-break"></div>


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
                    <div class="badge" style="background-color: <?= in_array(trim(strtolower((string)$t['status_progress'])), ['selesai', 'closed']) ? '#0dcaf0' : '#ffc107' ?>;">
                        <?= in_array(trim(strtolower((string)$t['status_progress'])), ['selesai', 'closed']) ? 'SELESAI' : 'BUKA' ?>
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

    <div class="signature-section">
        <div class="signature-wrapper">
            <div class="signature-box">
                <p class="fw-bold">Dibuat Oleh:</p>
                <div class="signature-space">
                    <?php if (!empty($pic['ass_head_signature'])) : ?>
                        <img src="<?= $pic['ass_head_signature'] ?>" class="signature-img">
                    <?php endif; ?>
                </div>
                <p class="fw-bold">(Ass Head Corp Internal Audit)</p>
            </div>
            <div class="signature-box">
                <p class="fw-bold">Diperiksa Oleh:</p>
                <div class="signature-space">
                    <?php if (!empty($pic['cfo_signature'])) : ?>
                        <img src="<?= $pic['cfo_signature'] ?>" class="signature-img">
                    <?php endif; ?>
                </div>
                <p class="fw-bold">(CFO)</p>
            </div>
            <div class="signature-box">
                <p class="fw-bold">Disetujui oleh:</p>
                <div class="signature-space">
                    <?php if (!empty($pic['direktur_signature'])) : ?>
                        <img src="<?= $pic['direktur_signature'] ?>" class="signature-img">
                    <?php endif; ?>
                </div>
                <p class="fw-bold">(Direktur)</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div style="margin-top: 50px; font-size: 10px; color: #777; text-align: right;">
        Dicetak pada: <?= date('d M Y H:i') ?>
    </div>
</body>
</html>