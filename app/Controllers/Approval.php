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
            case 3:
                $statusFilter = 'Waiting Kadep Approval';
                break;
            case 4:
                $statusFilter = 'Waiting Manager Approval';
                break;
            case 5:
                $statusFilter = 'Waiting Direktur Approval';
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

        $allowedStatus = [
            3 => 'Waiting Kadep Approval',
            4 => 'Waiting Manager Approval',
            5 => 'Waiting Direktur Approval'
        ];

        if (!isset($allowedStatus[$roleId]) || $temuan['status_progress'] !== $allowedStatus[$roleId]) {
            return redirect()->back()->with('error', 'Data ini sudah diproses atau bukan wewenang Anda saat ini.');
        }

        $nextStatus = '';
        $actionLog  = '';

        $db = \Config\Database::connect();
        $db->transStart();

        if ($decision === 'Reject') {
            $nextStatus = 'On Progress';
            $actionLog  = "Rejected by Role " . $roleId . " - Reason: " . $notes;

            $tindakLanjut = $this->tindakLanjutModel->where('temuan_id', $temuanId)->first();
            
            if($tindakLanjut){
                $this->tindakLanjutModel->update($tindakLanjut['id'], [
                    'status_verifikasi' => 'revision_required', 
                    'catatan_auditor'   => 'Ditolak oleh Manajemen: ' . $notes
                ]);
            }
        } else {
            switch ($roleId) {
                case 3:
                    $nextStatus = 'Waiting Manager Approval';
                    $actionLog  = "Approved by Kepala Departemen";
                    break;
                case 4:
                    $nextStatus = 'Waiting Direktur Approval';
                    $actionLog  = "Approved by Manager";
                    break;
                case 5:
                    $nextStatus = 'Closed';
                    $actionLog  = "Approved by Direktur (Closed)";
                    break;
                default:
                    $db->transRollback();
                    return redirect()->back()->with('error', 'Otoritas tidak valid.');
            }
        }

        $this->temuanModel->update($temuanId, [
            'status_progress' => $nextStatus
        ]);

        $this->approvalModel->save([
            'temuan_id'   => $temuanId,
            'approver_id' => $userId,
            'level_urut'  => $roleId,
            'status'      => strtolower($decision),
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        $this->auditTrailModel->save([
            'user_id'    => $userId,
            'action'     => $actionLog . " [Temuan ID: $temuanId]",
            'ip_address' => $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses persetujuan. Terjadi kesalahan pada database.');
        }

        return redirect()->to('/approval')->with('message', 'Persetujuan berhasil diproses. Status: ' . $nextStatus);
    }
}
