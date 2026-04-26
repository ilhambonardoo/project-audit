<?php

namespace App\Controllers;

use App\Models\TemuanModel;
use App\Models\TindakLanjutModel;
use App\Models\UserModel;
use CodeIgniter\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class Laporan extends BaseController
{
    protected $temuanModel;
    protected $tindakLanjutModel;
    protected $userModel;

    public function __construct()
    {
        $this->temuanModel = new TemuanModel();
        $this->tindakLanjutModel = new TindakLanjutModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data['pic_groups'] = $this->temuanModel
            ->select('users.name as pic_name, users.department, users.id as pic_id')
            ->join('users', 'users.id = temuan.pic_id')
            ->groupBy('pic_id')
            ->findAll();

        return view('laporan/index', $data);
    }

    public function preview($pic_id)
    {
        $data['temuan'] = $this->getLaporanDataByPic($pic_id);
        
        if (empty($data['temuan'])) {
            return redirect()->back()->with('error', 'Data temuan tidak ditemukan.');
        }

        $data['pic'] = $data['temuan'][0];
        return view('laporan/preview', $data);
    }

    public function exportPdf($pic_id)
    {
        $data['temuan'] = $this->getLaporanDataByPic($pic_id);
        $data['pic'] = $data['temuan'][0];
        $data['is_pdf'] = true;

        $html = view('laporan/pdf_template', $data);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'Laporan_Audit_' . str_replace(' ', '_', $data['pic']['pic_name']) . '_' . date('Ymd') . '.pdf';
        $dompdf->stream($filename, ["Attachment" => false]);
    }

    public function exportWord($pic_id)
    {
        $temuanList = $this->getLaporanDataByPic($pic_id);
        $picInfo = $temuanList[0];
        
        $phpWord = new PhpWord();
        $section = $phpWord->addSection(['orientation' => 'landscape']);

        // Title
        $section->addText("PEMBAHASAN TEMUAN PT KENCANA ABADI JAYA", ['bold' => true, 'size' => 14], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Table Styles
        $tableStyle = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 50];
        $phpWord->addTableStyle('AuditTable', $tableStyle);
        $table = $section->addTable('AuditTable');

        // Header Row
        $table->addRow();
        $headerCellStyle = ['bgColor' => 'EEEEEE', 'valign' => 'center'];
        $headerTextStyle = ['bold' => true, 'size' => 8];
        $cellTextStyle = ['size' => 8];

        $table->addCell(400, $headerCellStyle)->addText("No.", $headerTextStyle);
        $table->addCell(800, $headerCellStyle)->addText("Klausul", $headerTextStyle);
        $table->addCell(1200, $headerCellStyle)->addText("PIC", $headerTextStyle);
        $table->addCell(1200, $headerCellStyle)->addText("Kategori", $headerTextStyle);
        $table->addCell(3000, $headerCellStyle)->addText("Uraian", $headerTextStyle);
        $table->addCell(3000, $headerCellStyle)->addText("Rekomendasi", $headerTextStyle);
        $table->addCell(2000, $headerCellStyle)->addText("Tanggapan", $headerTextStyle);
        $table->addCell(1000, $headerCellStyle)->addText("Level/Status", $headerTextStyle);
        $table->addCell(1000, $headerCellStyle)->addText("Target", $headerTextStyle);
        $table->addCell(1500, $headerCellStyle)->addText("Kriteria", $headerTextStyle);

        foreach ($temuanList as $index => $t) {
            $status = (trim(strtolower((string)$t['status_progress'])) === 'closed') ? 'CLOSED' : 'OPEN';
            
            $table->addRow();
            $table->addCell(400)->addText((string)($index + 1), $cellTextStyle);
            $table->addCell(800)->addText((string)$t['klausul'], $cellTextStyle);
            $table->addCell(1200)->addText((string)$t['pic_name'], $cellTextStyle);
            $table->addCell(1200)->addText((string)$t['kategori_status'], $cellTextStyle);
            $table->addCell(3000)->addText((string)$t['uraian_temuan'], $cellTextStyle);
            $table->addCell(3000)->addText((string)$t['rekomendasi'], $cellTextStyle);
            $table->addCell(2000)->addText((string)($t['tanggapan_auditee'] ?? '-'), $cellTextStyle);
            $table->addCell(1000)->addText((string)$t['level_temuan'] . "\n" . $status, ['bold' => true, 'size' => 8]);
            $table->addCell(1000)->addText((string)$t['deadline'], $cellTextStyle);
            $table->addCell(1500)->addText((string)$t['kriteria'], $cellTextStyle);
        }

        $section->addTextBreak(2);

        // Signatures
        $sigTable = $section->addTable(['borderSize' => 0, 'cellMargin' => 0]);
        $sigTable->addRow();
        $sigTable->addCell(5000, ['alignment' => 'center'])->addText("Mengetahui:", ['bold' => true], ['alignment' => 'center']);
        $sigTable->addCell(5000, ['alignment' => 'center'])->addText("Diperiksa Oleh:", ['bold' => true], ['alignment' => 'center']);
        $sigTable->addCell(5000, ['alignment' => 'center'])->addText("Disetujui Oleh:", ['bold' => true], ['alignment' => 'center']);
        
        $sigTable->addRow();
        // Row for signature images
        $cell1 = $sigTable->addCell(5000);
        if (!empty($picInfo['dept_head_signature'])) {
            try {
                $cell1->addImage($picInfo['dept_head_signature'], ['height' => 60, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            } catch (\Exception $e) { $cell1->addTextBreak(3); }
        } else { $cell1->addTextBreak(3); }

        $cell2 = $sigTable->addCell(5000);
        if (!empty($picInfo['director_signature'])) {
            try {
                $cell2->addImage($picInfo['director_signature'], ['height' => 60, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            } catch (\Exception $e) { $cell2->addTextBreak(3); }
        } else { $cell2->addTextBreak(3); }

        $cell3 = $sigTable->addCell(5000);
        if (!empty($picInfo['plant_manager_signature'])) {
            try {
                $cell3->addImage($picInfo['plant_manager_signature'], ['height' => 60, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            } catch (\Exception $e) { $cell3->addTextBreak(3); }
        } else { $cell3->addTextBreak(3); }

        $sigTable->addRow();
        $sigTable->addCell(5000, ['alignment' => 'center'])->addText("(Ass. Head Corp Finance Controller)", ['bold' => true], ['alignment' => 'center']);
        $sigTable->addCell(5000, ['alignment' => 'center'])->addText("(Chief Financial Officer)", ['bold' => true], ['alignment' => 'center']);
        $sigTable->addCell(5000, ['alignment' => 'center'])->addText("(Plant Manager)", ['bold' => true], ['alignment' => 'center']);
        
        $filename = 'Laporan_Audit_' . str_replace(' ', '_', $picInfo['pic_name']) . '_' . date('Ymd') . '.docx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('php://output');
        exit;
    }

    private function getLaporanDataByPic($pic_id)
    {
        return $this->temuanModel
            ->select('temuan.*, 
                     pic.name as pic_name, pic.department, 
                     COALESCE(pic_approval.signature_snapshot, pic.signature) as pic_signature,
                     tindak_lanjut.tanggapan_auditee, 
                     auditor.name as auditor_name, 
                     COALESCE(temuan.auditor_signature_snapshot, auditor.signature) as auditor_signature_final,
                     
                     -- Kepala Departemen (Role 3)
                     COALESCE(dept_approval.signature_snapshot, dept_user.signature) as dept_head_signature,
                     dept_user.name as dept_head_name,
                     
                     -- Plant Manager (Role 4)
                     COALESCE(plant_approval.signature_snapshot, plant_user.signature) as plant_manager_signature,
                     plant_user.name as plant_manager_name,
                     
                     -- Direktur (Role 5)
                     COALESCE(dir_approval.signature_snapshot, dir_user.signature) as director_signature,
                     dir_user.name as director_name')
            ->join('users as pic', 'pic.id = temuan.pic_id')
            ->join('users as auditor', 'auditor.id = temuan.auditor_id', 'left')
            
            // Join for Kepala Departemen (Level/Role 3)
            ->join('approvals as dept_approval', "dept_approval.temuan_id = temuan.id AND dept_approval.level_urut = 3 AND TRIM(LOWER(dept_approval.status)) = 'approved'", 'left')
            ->join('users as dept_user', 'dept_user.role_id = 3', 'left')
            
            // Join for Plant Manager (Level/Role 4)
            ->join('approvals as plant_approval', "plant_approval.temuan_id = temuan.id AND plant_approval.level_urut = 4 AND TRIM(LOWER(plant_approval.status)) = 'approved'", 'left')
            ->join('users as plant_user', 'plant_user.role_id = 4', 'left')
            
            // Join for Direktur (Level/Role 5)
            ->join('approvals as dir_approval', "dir_approval.temuan_id = temuan.id AND dir_approval.level_urut = 5 AND TRIM(LOWER(dir_approval.status)) = 'approved'", 'left')
            ->join('users as dir_user', 'dir_user.role_id = 5', 'left')

            ->join('approvals as pic_approval', "pic_approval.temuan_id = temuan.id AND pic_approval.level_urut = 2", 'left')
            ->join('tindak_lanjut', 'tindak_lanjut.temuan_id = temuan.id', 'left')
            ->where('temuan.pic_id', $pic_id)
            ->findAll();
    }

    private function addWordRow($table, $label, $value)
    {
        $table->addRow();
        $table->addCell(3000, ['bgColor' => 'EEEEEE'])->addText($label, ['bold' => true]);
        $table->addCell(6000)->addText($value);
    }
}
