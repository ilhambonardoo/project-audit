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

        $data['pic'] = $data['temuan'][0]; // Get PIC info from first record
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
        $sigTable->addCell(5000, ['alignment' => 'center'])->addText("Auditor", ['bold' => true], ['alignment' => 'center']);
        $sigTable->addCell(5000, ['alignment' => 'center'])->addText("Lead Auditor", ['bold' => true], ['alignment' => 'center']);
        $sigTable->addCell(5000, ['alignment' => 'center'])->addText("Auditee", ['bold' => true], ['alignment' => 'center']);
        
        $sigTable->addRow();
        $sigTable->addCell(5000)->addTextBreak(3);
        $sigTable->addCell(5000)->addTextBreak(3);
        $sigTable->addCell(5000)->addTextBreak(3);

        $sigTable->addRow();
        $sigTable->addCell(5000, ['alignment' => 'center'])->addText("( " . ($picInfo['auditor_name'] ?? '........') . " )", ['bold' => true], ['alignment' => 'center']);
        $sigTable->addCell(5000, ['alignment' => 'center'])->addText("( Senior Auditor )", ['bold' => true], ['alignment' => 'center']);
        $sigTable->addCell(5000, ['alignment' => 'center'])->addText("( " . $picInfo['pic_name'] . " )", ['bold' => true], ['alignment' => 'center']);
        
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
                     COALESCE(lead_approval.signature_snapshot, lead_user.signature) as lead_auditor_signature,
                     lead_user.name as lead_auditor_name')
            ->join('users as pic', 'pic.id = temuan.pic_id')
            ->join('users as auditor', 'auditor.id = temuan.auditor_id', 'left')
            ->join('approvals as lead_approval', "lead_approval.temuan_id = temuan.id AND lead_approval.level_urut = 6 AND lead_approval.status = 'approved'", 'left')
            ->join('users as lead_user', 'lead_user.role_id = 6', 'left') 
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
