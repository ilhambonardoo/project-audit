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
        $statusFilter = '';

        switch ($roleId) {
            case 6: // Lead Auditor
                $statusFilter = 'Waiting Lead Auditor Approval';
                break;
            case 3: // Kadep
                $statusFilter = 'Waiting Kadep Approval';
                break;
            case 5: // Direktur / CFO
                $statusFilter = 'Waiting Direktur Approval';
                break;
            case 4: // Plant Manager
                $statusFilter = 'Waiting Manager Approval';
                break;
            default:
                return redirect()->back()->with('error', 'Akses Ditolak.');
        }

        $data = [
            'title'  => 'Daftar Persetujuan (Approval)',
            'temuan' => $this->temuanModel->where('status_progress', $statusFilter)->findAll()
        ];

        return view('approval/index', $data);
    }

    public function process()
    {
        $roleId   = session()->get('role_id');
        $userId   = session()->get('id');
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
            if ($decision === 'Reject') {
                if ($roleId == 6) {
                    $nextStatus = 'Draft';
                    $actionLog = "Rejected by Lead Auditor - Auditor must revise. Reason: " . $notes;
                } else if ($roleId == 3) {
                    $nextStatus = 'Open';
                    $actionLog  = "Rejected by Kadep - Back to Open. Reason: " . $notes;
                } else {
                    $nextStatus = 'On Progress';
                    $actionLog  = "Rejected by Role " . $roleId . " - Reason: " . $notes;
                }

                $updateData = [
                    'status_progress' => $nextStatus,
                    'catatan_revisi'  => $notes
                ];

                if ($roleId == 6) {
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
            } else if ($decision === 'Approve') {
                switch ($roleId) {
                    case 6:
                        $nextStatus = 'Open';
                        $actionLog  = "Approved by Lead Auditor - Ready for Auditee";
                        $db->table('temuan')->where('id', $temuanId)->update(['status_progress' => $nextStatus, 'catatan_revisi' => null]);
                        break;
                    case 3:
                        $nextStatus = 'Waiting Direktur Approval';
                        $actionLog  = "Approved by Kepala Departemen";
                        $db->table('temuan')->where('id', $temuanId)->update(['status_progress' => $nextStatus]);
                        break;
                    case 5:
                        $nextStatus = 'Waiting Manager Approval';
                        $actionLog  = "Approved by Direktur / CFO";
                        $db->table('temuan')->where('id', $temuanId)->update(['status_progress' => $nextStatus]);
                        break;
                    case 4:
                        $nextStatus = 'Closed';
                        $actionLog  = "Approved by Plant Manager (Closed)";
                        $db->table('temuan')->where('id', $temuanId)->update(['status_progress' => $nextStatus]);
                        break;
                }
            }

            $db->table('approvals')->insert([
                'temuan_id'   => $temuanId,
                'approver_id' => $userId,
                'level_urut'  => $roleId,
                'status'      => strtolower($decision),
                'signature_snapshot' => ($decision === 'Reject') ? null : session()->get('signature'),
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
