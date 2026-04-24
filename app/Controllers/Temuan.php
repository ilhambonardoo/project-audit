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
    protected $temuanModel;
    protected $userModel;
    protected $tindakLanjutModel;
    protected $buktiModel;
    protected $AuditTrailModel;

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
        $data = [
            'title'  => 'Data Temuan Audit',
            'temuan' => $this->temuanModel->orderBy('created_at', 'DESC')->findAll()
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
            'status_progress' => 'Waiting Lead Auditor Approval',
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
        $leadApproval = $approvalModel->where([
            'temuan_id' => $id,
            'level_urut' => 6 // Lead Auditor
        ])->first();

        $data = [
            'title'           => 'Detail Temuan: ' . $temuan['judul_temuan'],
            'temuan'          => $temuan,
            'auditor_name'    => $auditor['name'] ?? 'Tidak Diketahui',
            'pic_name'        => $pic['name'] ?? 'Pilih PIC Terlebih Dahulu',
            'tindak_lanjut'   => $tindak_lanjut,
            'bukti_pendukung' => $bukti_pendukung,
            'lead_signature'  => $leadApproval['signature_snapshot'] ?? null,
            'auditor_signature' => $temuan['auditor_signature_snapshot'] ?? ($auditor['signature'] ?? null)
        ];

        return view('temuan/detail', $data);
    }

    public function verify()
    {
        if (session()->get('role_id') != 1) {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Auditor yang bisa melakukan verifikasi.');
        }

        $temuan_id        = $this->request->getPost('temuan_id');
        $tindak_lanjut_id = $this->request->getPost('tindak_lanjut_id');
        $keputusan        = $this->request->getPost('keputusan');
        $catatan          = $this->request->getPost('catatan_auditor');

        $temuan = $this->temuanModel->find($temuan_id);
        if (!$temuan || $temuan['status_progress'] !== 'On Progress') {
            return redirect()->back()->with('error', 'Temuan ini sudah dalam tahap Approval atau sudah Closed.');
        }

        if ($keputusan == 'approve') {
            $this->tindakLanjutModel->update($tindak_lanjut_id, [
                'status_verifikasi' => 'approved',
                'catatan_auditor'   => $catatan,
                'verified_at'       => date('Y-m-d H:i:s')
            ]);

            $this->temuanModel->update($temuan_id, [
                'status_progress' => 'Waiting Kadep Approval'
            ]);

            $message = 'Tindak lanjut disetujui. Sekarang menunggu persetujuan Kepala Departemen.';
            $aksi_log = 'Approve Bukti (Ke Kadep)';
        } else {
            $this->tindakLanjutModel->update($tindak_lanjut_id, [
                'status_verifikasi' => 'revision_required',
                'catatan_auditor'   => $catatan
            ]);

            $this->temuanModel->update($temuan_id, [
                'status_progress' => 'On Progress'
            ]);

            $message = 'Tindak lanjut ditolak. PIC akan diminta melakukan revisi.';
            $aksi_log = 'Reject Bukti (Minta Revisi)';
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

        $data = [
            'title'  => 'Edit Temuan Audit',
            'temuan' => $temuan,
            'users'  => $users
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
        ];

        // Jika status saat ini adalah Draft (hasil reject Lead Auditor), 
        // maka setelah diupdate, status kembali ke Waiting Lead Auditor Approval
        // dan bubuhkan tanda tangan baru
        if ($temuan['status_progress'] === 'Draft') {
            $updateData['status_progress'] = 'Waiting Lead Auditor Approval';
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
