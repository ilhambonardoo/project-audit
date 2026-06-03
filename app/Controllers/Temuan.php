<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuditTrailModel;
use App\Models\TemuanModel;
use App\Models\UserModel;
use App\Models\TindakLanjutModel;
use App\Models\BuktiPendukungModel;

class Temuan extends BaseController
{
    protected TemuanModel $temuanModel;
    protected UserModel $userModel;
    protected TindakLanjutModel $tindakLanjutModel;
    protected BuktiPendukungModel $buktiModel;
    protected AuditTrailModel $AuditTrailModel;

    public function __construct()
    {
        $this->temuanModel = new TemuanModel();
        $this->userModel = new UserModel();
        $this->tindakLanjutModel = new TindakLanjutModel();
        $this->buktiModel = new BuktiPendukungModel();
        $this->AuditTrailModel = new AuditTrailModel();
    }

    public function index()
    {
        $role_id = session()->get('role_id');
        $department = session()->get('department');
        $userId = session()->get('id');

        $query = $this->temuanModel->select('temuan.*, users.name as pic_name, users.department as pic_department')
            ->join('users', 'users.id = temuan.pic_id')
            ->orderBy('temuan.created_at', 'DESC');

        // Filter based on role
        // Auditor (1, 6) and Top 3 Tier (3, 4, 5) can see all findings.
        if (!in_array($role_id, [1, 3, 4, 5, 6])) {
            // PIC (Auditee) only see findings for their department
            $query->where('users.department', $department);
        }

        $data = [
            'title'  => 'Data Temuan Audit',
            'temuan' => $query->findAll()
        ];

        return view('temuan/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Temuan Baru',
            'users' => $this->userModel->where('role_id', 2)->findAll()
        ];

        return view('temuan/create', $data);
    }

    public function store()
    {
        // Validasi tanda tangan untuk Auditor
        if (empty(session()->get('signature'))) {
            return redirect()->back()->withInput()->with('error', 'Anda harus memiliki tanda tangan digital sebelum membuat temuan. Silakan update di menu Profil.');
        }

        if (!$this->validate([
            'pic_id'        => 'required',
            'klausul'       => 'required',
            'judul_temuan'  => 'required',
            'uraian_temuan' => 'required',
            'kriteria'      => 'required',
            'rekomendasi'   => 'required',
            'kategori_status' => 'required',
            'level_temuan'  => 'required',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Semua field wajib diisi.');
        }

        $deadline = date('Y-m-d', strtotime('+30 days'));

        // Determine initial status based on role
        // Lead Auditor (6) create → Waiting Admin Approval
        // Admin (1) create → Menunggu Persetujuan Lead Auditor
        $roleId = session()->get('role_id');
        $initialStatus = ($roleId == 6) ? 'Waiting Admin Approval' : 'Menunggu Persetujuan Lead Auditor';

        $this->temuanModel->save([
            'auditor_id'      => session()->get('id'),
            'pic_id'          => $this->request->getPost('pic_id'),
            'klausul'         => $this->request->getPost('klausul'),
            'judul_temuan'    => $this->request->getPost('judul_temuan'),
            'uraian_temuan'   => $this->request->getPost('uraian_temuan'),
            'kriteria'        => $this->request->getPost('kriteria'),
            'rekomendasi'     => $this->request->getPost('rekomendasi'),
            'kategori_status' => $this->request->getPost('kategori_status'),
            'level_temuan'    => $this->request->getPost('level_temuan'),
            'status_progress' => $initialStatus,
            'deadline'        => $deadline,
            'auditor_signature_snapshot' => session()->get('signature'),
        ]);

        $auditModel = $this->AuditTrailModel;
        $auditModel->save([
            'user_id'    => session()->get('id'), 
            'aktivitas'  => 'Create Temuan',
            'keterangan' => 'Auditor membuat temuan baru dengan judul: ' . $this->request->getPost('judul_temuan'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/temuan')->with('success', 'Temuan audit berhasil ditambahkan dan deadline 30 hari telah ditetapkan.');
    }

public function show($id)
{
    $temuan = $this->temuanModel->find($id);
    if (!$temuan) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Temuan dengan ID $id tidak ditemukan.");
    }

    $auditor = $this->userModel->find($temuan['auditor_id']);
    $pic     = $this->userModel->find($temuan['pic_id']);

    $tindak_lanjut = $this->tindakLanjutModel->where('temuan_id', $id)->first();

    $bukti_pendukung = [];
    if ($tindak_lanjut) {
        $bukti_pendukung = $this->buktiModel->where('tindak_lanjut_id', $tindak_lanjut['id'])->findAll();
    }

    // Ambil tanda tangan Lead Auditor dari tabel approvals jika sudah ada
    $approvalModel = new \App\Models\ApprovalModel();
    
    $adminApproval = $approvalModel->where([
        'temuan_id'  => $id,
        'level_urut' => 1, // Admin Auditor
    ])->where('signature_snapshot IS NOT NULL')
      ->orderBy('created_at', 'DESC')
      ->first();

    $leadApproval = $approvalModel->where([
        'temuan_id'  => $id,
        'level_urut' => 6, // Lead Auditor
    ])->where('signature_snapshot IS NOT NULL')
      ->orderBy('created_at', 'DESC')
      ->first();

    // Ambil tanda tangan Auditee dari tabel approvals jika sudah ada
    $auditeeApproval = $approvalModel->where([
        'temuan_id'  => $id,
        'level_urut' => 2, // PIC / Auditee
    ])->where('signature_snapshot IS NOT NULL')
      ->orderBy('created_at', 'DESC')
      ->first();

    // Ambil tanda tangan Management dari tabel approvals jika sudah ada
    $assHeadApproval = $approvalModel->where([
        'temuan_id'  => $id,
        'level_urut' => 3, // Ass Head
    ])->where('signature_snapshot IS NOT NULL')
      ->orderBy('created_at', 'DESC')
      ->first();

    $cfoApproval = $approvalModel->where([
        'temuan_id'  => $id,
        'level_urut' => 5, // CFO
    ])->where('signature_snapshot IS NOT NULL')
      ->orderBy('created_at', 'DESC')
      ->first();

    $direkturApproval = $approvalModel->where([
        'temuan_id'  => $id,
        'level_urut' => 4, // Direktur
    ])->where('signature_snapshot IS NOT NULL')
      ->orderBy('created_at', 'DESC')
      ->first();

    // Dapatkan role ID pembuat temuan
    $auditorRoleId = null;
    if ($auditor) {
        if (is_array($auditor)) {
            $auditorRoleId = $auditor['role_id'] ?? null;
        } else {
            $auditorRoleId = $auditor->role_id ?? null;
        }
    }
    
    // Jika masih tidak ada, query langsung ke database
    if (!$auditorRoleId) {
        $userDb = $this->userModel->find($temuan['auditor_id']);
        if ($userDb) {
            if (is_array($userDb)) {
                $auditorRoleId = $userDb['role_id'] ?? null;
            } else {
                $auditorRoleId = $userDb->role_id ?? null;
            }
        }
    }

    // Logika signature berdasarkan siapa yang membuat:
    // - Admin (1) membuat: Admin signature dari snapshot, Lead signature dari approval table
    // - Lead (6) membuat: Lead signature dari snapshot, Admin signature dari approval table
    $adminSignature = null;
    $leadSignature = null;
    $creatorSignature = $temuan['auditor_signature_snapshot'] ?? null;

    if ($auditorRoleId == 1) {
        // Admin yang membuat temuan
        $adminSignature = $creatorSignature;
        // Lead signature dari approval saat Lead approve
        $leadSignature = $leadApproval['signature_snapshot'] ?? null;
    } else if ($auditorRoleId == 6) {
        // Lead Auditor yang membuat temuan
        $leadSignature = $creatorSignature;
        // Admin signature dari approval saat Admin approve
        $adminSignature = $adminApproval['signature_snapshot'] ?? null;
    }

    $auditeeSignature = $auditeeApproval['signature_snapshot'] ?? null;

    $data = [
        'title'             => 'Detail Temuan: ' . $temuan['judul_temuan'],
        'temuan'            => $temuan,
        'auditor_name'      => is_array($auditor) ? ($auditor['name'] ?? 'Tidak Diketahui') : ($auditor->name ?? 'Tidak Diketahui'),
        'pic_name'          => is_array($pic) ? ($pic['name'] ?? 'Pilih PIC Terlebih Dahulu') : ($pic->name ?? 'Pilih PIC Terlebih Dahulu'),
        'tindak_lanjut'     => $tindak_lanjut,
        'bukti_pendukung'   => $bukti_pendukung,
        'admin_signature'   => $adminSignature,
        'lead_signature'    => $leadSignature,
        'auditee_signature' => $auditeeSignature,
        'ass_head_signature'=> $assHeadApproval['signature_snapshot'] ?? null,
        'cfo_signature'     => $cfoApproval['signature_snapshot'] ?? null,
        'direktur_signature'=> $direkturApproval['signature_snapshot'] ?? null,
        'auditor_signature' => $temuan['auditor_signature_snapshot'] ?? (is_array($auditor) ? ($auditor['signature'] ?? null) : ($auditor->signature ?? null)),
        'auditor_role_id'   => $auditorRoleId
    ];

    return view('temuan/detail', $data);
}

    public function verify()
    {
        if (session()->get('role_id') != 6) {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Lead Auditor yang bisa melakukan verifikasi bukti temuan.');
        }

        $temuan_id        = $this->request->getPost('temuan_id');
        $tindak_lanjut_id = $this->request->getPost('tindak_lanjut_id');
        $keputusan        = $this->request->getPost('keputusan');
        $catatan          = $this->request->getPost('catatan_auditor');

        $temuan = $this->temuanModel->find($temuan_id);
        if (!$temuan || $temuan['status_progress'] !== 'Sedang Berjalan') {
            return redirect()->back()->with('error', 'Temuan ini sudah dalam tahap Persetujuan atau sudah Selesai.');
        }

        if ($keputusan == 'approve') {
            $this->tindakLanjutModel->update($tindak_lanjut_id, [
                'status_verifikasi' => 'approved',
                'catatan_auditor'   => $catatan,
                'verified_at'       => date('Y-m-d H:i:s')
            ]);

            $this->temuanModel->update($temuan_id, [
                'status_progress' => 'Closed'
            ]);

            // Clear old management signatures so they sign the revised version
            $db = \Config\Database::connect();
            $db->table('approvals')
               ->where('temuan_id', $temuan_id)
               ->whereIn('level_urut', [3, 4, 5])
               ->delete();

            $message = 'Bukti temuan disetujui. Temuan sekarang berstatus CLOSED dan masuk ke alur Tanda Tangan Laporan Final.';
            $aksi_log = 'Setujui Bukti (Closed)';
        } else {
            $this->tindakLanjutModel->update($tindak_lanjut_id, [
                'status_verifikasi' => 'revision_required',
                'catatan_auditor'   => $catatan
            ]);

            $this->temuanModel->update($temuan_id, [
                'status_progress' => 'Sedang Berjalan'
            ]);

            $message = 'Bukti temuan ditolak. PIC akan diminta melakukan revisi.';
            $aksi_log = 'Tolak Bukti (Kembali ke Auditee)';
        }

        $this->AuditTrailModel->save([
            'user_id'    => session()->get('id'), 
            'aktivitas'  => 'Verifikasi Temuan - ' . $aksi_log,
            'keterangan' => 'Auditor melakukan ' . $aksi_log . ' pada temuan ID: ' . $temuan_id,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('temuan/show/' . $temuan_id))->with('success', $message);
    }

    public function edit($id)
    {
        $temuan = $this->temuanModel->find($id);
        
        if (!$temuan) {
            return redirect()->to('/temuan')->with('error', 'Data temuan tidak ditemukan.');
        }

        $userModel = new \App\Models\UserModel();
        $users = $userModel->where('role_id', 2)->findAll();

        $auditor = $userModel->find($temuan['auditor_id']);
        $auditorRoleId = null;
        if ($auditor) {
            $auditorRoleId = is_array($auditor) ? ($auditor['role_id'] ?? null) : ($auditor->role_id ?? null);
        }

        $data = [
            'title'           => 'Edit Temuan Audit',
            'temuan'          => $temuan,
            'users'           => $users,
            'auditor_role_id' => $auditorRoleId
        ];

        return view('temuan/edit', $data);
    }

    public function update($id)
    {
        $temuan = $this->temuanModel->find($id);
        if (!$temuan) {
            return redirect()->to('/temuan')->with('error', 'Data tidak ditemukan.');
        }

        $updateData = [
            'klausul'       => $this->request->getPost('klausul'),
            'judul_temuan'  => $this->request->getPost('judul_temuan'),
            'uraian_temuan' => $this->request->getPost('uraian_temuan'),
            'kriteria'      => $this->request->getPost('kriteria'),
            'rekomendasi'   => $this->request->getPost('rekomendasi'),
            'level_temuan'  => $this->request->getPost('level_temuan'),
            'pic_id'        => $this->request->getPost('pic_id'),
            'deadline'      => $this->request->getPost('deadline'),
            'kategori_status' => $this->request->getPost('kategori_status'),
        ];

        // Jika status saat ini adalah Draft (hasil reject), 
        // maka setelah diupdate, status kembali ke approval level yang sesuai:
        // - Jika pembuat/auditor temuan adalah Lead Auditor (role 6), maka kembali ke 'Waiting Admin Approval' (agar divalidasi oleh Admin Auditor)
        // - Jika pembuat/auditor temuan adalah Admin Auditor (role 1) / lainnya, maka kembali ke 'Menunggu Persetujuan Lead Auditor'
        // dan bubuhkan tanda tangan baru
        if ($temuan['status_progress'] === 'Draft') {
            $roleId = session()->get('role_id');
            $auditorRoleId = null;
            if (!empty($temuan['auditor_id'])) {
                $userModel = new \App\Models\UserModel();
                $auditor = $userModel->find($temuan['auditor_id']);
                if ($auditor) {
                    $auditorRoleId = is_array($auditor) ? ($auditor['role_id'] ?? null) : ($auditor->role_id ?? null);
                }
            }
            $targetRole = $auditorRoleId ?: $roleId;

            if ($targetRole == 6) {
                $updateData['status_progress'] = 'Waiting Admin Approval';
            } else {
                $updateData['status_progress'] = 'Menunggu Persetujuan Lead Auditor';
            }
            $updateData['auditor_signature_snapshot'] = session()->get('signature');
        }

        $this->temuanModel->update($id, $updateData);

        // Log Audit Trail
        $this->AuditTrailModel->save([
            'user_id'    => session()->get('id'), 
            'aktivitas'  => 'Update Temuan',
            'keterangan' => 'Auditor memperbarui dan mengirim ulang temuan ID: ' . $id,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/temuan')->with('success', 'Data temuan berhasil diperbarui dan dikirim ulang untuk approval!');
    }

    public function delete($id)
    {
        $temuan = $this->temuanModel->find($id);
        
        if ($temuan) {
            $this->temuanModel->delete($id);
            return redirect()->to('/temuan')->with('success', 'Data temuan berhasil dihapus!');
        }

        return redirect()->to('/temuan')->with('error', 'Data tidak ditemukan.');
    }
}
