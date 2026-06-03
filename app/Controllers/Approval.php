<?php

namespace App\Controllers;

use App\Models\TemuanModel;
use App\Models\AuditTrailModel;
use App\Models\ApprovalModel;
use App\Models\TindakLanjutModel;

class Approval extends BaseController
{
    protected $temuanModel;
    protected $auditTrailModel;
    protected $approvalModel;
    protected $tindakLanjutModel;

    public function __construct()
    {
        $this->temuanModel    = new TemuanModel();
        $this->auditTrailModel = new AuditTrailModel();
        $this->approvalModel  = new ApprovalModel();
        $this->tindakLanjutModel = new TindakLanjutModel();
    }

    public function index()
    {
        $roleId = session()->get('role_id');
        $userDept = session()->get('department');
        $statusFilter = '';

        switch ($roleId) {
            case 1: // Admin Auditor
                $statusFilter = 'Waiting Admin Approval'; // Approval dari Lead Auditor yang buat temuan
                break;
            case 6: // Lead Auditor
                $statusFilter = 'Menunggu Persetujuan Lead Auditor';
                break;
            case 3: // Ass Head Corp Internal Audit
                $statusFilter = 'Closed'; // Laporan Final dimulai setelah Closed
                break;
            case 5: // CFO
                $statusFilter = 'Closed';
                break;
            case 4: // Direktur
                $statusFilter = 'Closed';
                break;
            default:
                return redirect()->back()->with('error', 'Akses Ditolak.');
        }

        $builder = $this->temuanModel
            ->select('temuan.*, users.name as pic_name, users.department')
            ->join('users', 'users.id = temuan.pic_id')
            ->where('status_progress', $statusFilter);

        // Filter based on signature flow for top-tier roles
        if ($roleId == 3) {
            $builder->where("NOT EXISTS (SELECT 1 FROM approvals WHERE approvals.temuan_id = temuan.id AND approvals.level_urut = 3 AND approvals.signature_snapshot IS NOT NULL)");
        } else if ($roleId == 5) {
            $builder->where("EXISTS (SELECT 1 FROM approvals WHERE approvals.temuan_id = temuan.id AND approvals.level_urut = 3 AND approvals.signature_snapshot IS NOT NULL)");
            $builder->where("NOT EXISTS (SELECT 1 FROM approvals WHERE approvals.temuan_id = temuan.id AND approvals.level_urut = 5 AND approvals.signature_snapshot IS NOT NULL)");
        } else if ($roleId == 4) {
            $builder->where("EXISTS (SELECT 1 FROM approvals WHERE approvals.temuan_id = temuan.id AND approvals.level_urut = 5 AND approvals.signature_snapshot IS NOT NULL)");
            $builder->where("NOT EXISTS (SELECT 1 FROM approvals WHERE approvals.temuan_id = temuan.id AND approvals.level_urut = 4 AND approvals.signature_snapshot IS NOT NULL)");
        }

        // Top 3 tier roles can see all departments
        // Role 3 (Ass Head), 5 (CFO), 4 (Direktur) do not have department filter anymore
        // Previously Role 3 had department filter.
        
        $data = [
            'title'  => 'Daftar Persetujuan (Approval)',
            'temuan' => $builder->findAll()
        ];

        return view('approval/index', $data);
    }

    public function process()
    {
        $roleId   = session()->get('role_id');
        $userId   = session()->get('id');
        $signature = session()->get('signature');

        // Validasi tanda tangan sebelum memproses approval
        if (empty($signature)) {
            return redirect()->back()->with('error', 'Anda harus memiliki tanda tangan digital sebelum melakukan persetujuan/penolakan. Silakan update di menu Profil.');
        }

        $temuanId = $this->request->getPost('temuan_id');
        $decision = $this->request->getPost('decision');
        $notes    = $this->request->getPost('notes');

        $temuan = $this->temuanModel->find($temuanId);
            
        if (!$temuan) {
            return redirect()->back()->with('error', 'Data temuan tidak ditemukan.');
        }

        $nextStatus = '';
        $actionLog  = '';

        $db = \Config\Database::connect();
        
        try {
            if ($decision === 'Tolak') {
                if ($roleId == 1) {
                    $nextStatus = 'Draft';
                    $actionLog = "Ditolak oleh Admin Auditor - Auditor harus revisi. Alasan: " . $notes;
                } else if ($roleId == 6) {
                    $nextStatus = 'Draft';
                    $actionLog = "Ditolak oleh Lead Auditor - Auditor harus revisi. Alasan: " . $notes;
                } else if (in_array($roleId, [3, 4, 5])) {
                    $nextStatus = 'Sedang Berjalan';
                    $actionLog  = "Ditolak oleh Role " . $roleId . " - Kembali ke Auditee. Alasan: " . $notes;
                } else {
                    $nextStatus = 'Closed';
                    $actionLog  = "Ditolak oleh Role " . $roleId . " - Kembali ke status Closed. Alasan: " . $notes;
                }

                $updateData = [
                    'status_progress' => $nextStatus,
                    'catatan_revisi'  => $notes
                ];

                if (in_array($roleId, [1, 6])) {
                    $updateData['auditor_signature_snapshot'] = null;
                }

                $db->table('temuan')->where('id', $temuanId)->update($updateData);

                if ($nextStatus !== 'Draft') {
                    $tindakLanjut = $this->tindakLanjutModel->where('temuan_id', $temuanId)->first();
                    if($tindakLanjut) {
                        $db->table('tindak_lanjut')->where('id', $tindakLanjut['id'])->update([
                            'status_verifikasi' => 'revision_required', 
                            'catatan_auditor'   => 'Ditolak: ' . $notes
                        ]);
                    }
                }
            } else if ($decision === 'Setujui') {
                switch ($roleId) {
                    case 1: // Admin Auditor approves Lead Auditor's temuan
                        $nextStatus = 'Sedang Berjalan';
                        $actionLog  = "Disetujui oleh Admin Auditor - Siap untuk Auditee";
                        $db->table('temuan')->where('id', $temuanId)->update(['status_progress' => $nextStatus, 'catatan_revisi' => null]);
                        break;
                    case 6:
                        $nextStatus = 'Sedang Berjalan';
                        $actionLog  = "Disetujui oleh Lead Auditor - Siap untuk Auditee";
                        $db->table('temuan')->where('id', $temuanId)->update(['status_progress' => $nextStatus, 'catatan_revisi' => null]);
                        break;
                    case 3:
                        $nextStatus = 'Closed';
                        $actionLog  = "Disetujui oleh Ass Head Corp IA - Lanjut ke CFO";
                        $db->table('temuan')->where('id', $temuanId)->update(['status_progress' => $nextStatus]);
                        break;
                    case 5:
                        $nextStatus = 'Closed';
                        $actionLog  = "Disetujui oleh CFO - Lanjut ke Direktur";
                        $db->table('temuan')->where('id', $temuanId)->update(['status_progress' => $nextStatus]);
                        break;
                    case 4:
                        $nextStatus = 'Closed';
                        $actionLog  = "Disetujui oleh Direktur (Selesai)";
                        $db->table('temuan')->where('id', $temuanId)->update(['status_progress' => $nextStatus]);
                        break;
                }
            }

            $db->table('approvals')->insert([
                'temuan_id'   => $temuanId,
                'approver_id' => $userId,
                'level_urut'  => $roleId,
                'status'      => strtolower($decision),
                'signature_snapshot' => ($decision === 'Tolak') ? null : session()->get('signature'),
                'created_at'  => date('Y-m-d H:i:s')
            ]);

            // MENYESUAIKAN KOLOM KE 'action' SESUAI AuditTrailModel
            $db->table('audit_trails')->insert([
                'user_id'    => $userId,
                'action'     => $actionLog . " [Temuan ID: $temuanId]",
                'ip_address' => $this->request->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to('/approval')->with('message', 'Persetujuan berhasil diproses. Status Baru: ' . $nextStatus);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses (Database Error): ' . $e->getMessage());
        }
    }
}
