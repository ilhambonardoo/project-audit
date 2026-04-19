<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TemuanModel;
use App\Models\TindakLanjutModel;
use App\Models\BuktiPendukungModel;
use App\Models\AuditTrailModel;

class TindakLanjut extends BaseController
{
    protected $temuanModel;
    protected $tindakLanjutModel;
    protected $buktiModel;
    protected $AuditTrailModel;

    public function __construct()
    {
        $this->temuanModel = new TemuanModel();
        $this->tindakLanjutModel = new TindakLanjutModel();
        $this->buktiModel = new BuktiPendukungModel();
        $this->AuditTrailModel = new AuditTrailModel();
    }

    public function create($temuan_id)
    {
        if (session()->get('role_id') != 2) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Hanya PIC yang dapat menindaklanjuti temuan.');
        }

        $temuan = $this->temuanModel->find($temuan_id);

        if (!$temuan || $temuan['pic_id'] != session()->get('id')) {
            return redirect()->to('/dashboard')->with('error', 'Data tidak ditemukan atau Anda tidak memiliki akses ke temuan ini.');
        }

        $data = [
            'title'  => 'Form Tindak Lanjut Temuan',
            'temuan' => $temuan
        ];

        return view('tindak_lanjut/create', $data);
    }

    public function store()
    {
        $temuan_id = $this->request->getPost('temuan_id');
        $fileBukti = $this->request->getFile('file_bukti'); 

        $existingTL = $this->tindakLanjutModel->where('temuan_id', $temuan_id)->first();

        if ($fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $newName = $fileBukti->getRandomName();
            $fileBukti->move('uploads/bukti', $newName);

            if ($existingTL) {
                $oldBukti = $this->buktiModel->where('tindak_lanjut_id', $existingTL['id'])->findAll();
                foreach ($oldBukti as $b) {
                    if (file_exists($b['file_path'])) {
                        unlink($b['file_path']);
                    }
                }
                $this->buktiModel->where('tindak_lanjut_id', $existingTL['id'])->delete();

                $this->tindakLanjutModel->update($existingTL['id'], [
                    'tanggapan_auditee' => $this->request->getPost('tanggapan_auditee'),
                    'status_verifikasi' => 'pending',
                    'catatan_auditor'   => null,
                    'updated_at'        => date('Y-m-d H:i:s')
                ]);
                $tindakLanjutId = $existingTL['id'];
            } else {
                $this->tindakLanjutModel->save([
                    'temuan_id'         => $temuan_id,
                    'tanggapan_auditee' => $this->request->getPost('tanggapan_auditee'),
                    'status_verifikasi' => 'pending'
                ]);
                $tindakLanjutId = $this->tindakLanjutModel->insertID();
            }

            $this->temuanModel->update($temuan_id, [
                'status_progress' => 'On Progress'
            ]);

            $this->buktiModel->save([
                'tindak_lanjut_id' => $tindakLanjutId,
                'file_name'        => $newName,
                'file_path'        => 'uploads/bukti/' . $newName,
                'uploaded_at'      => date('Y-m-d H:i:s')
            ]);

            return redirect()->to('/temuan/show/' . $temuan_id)->with('success', 'Tindak lanjut/revisi berhasil dikirim. Menunggu verifikasi auditor.');
        } else {
            return redirect()->back()->with('error', 'File tidak valid atau gagal diupload.');
        }
    }
}