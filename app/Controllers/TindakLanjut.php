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
        $fileBukti = $this->request->getFile('file_bukti');
        
        if ($fileBukti && $fileBukti->getError() !== UPLOAD_ERR_OK) {
            $errorMsg = 'Gagal upload file: ';
            switch ($fileBukti->getError()) {
                case UPLOAD_ERR_INI_SIZE:
                    $errorMsg .= 'Ukuran file melebihi batas server (' . ini_get('upload_max_filesize') . ').';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $errorMsg .= 'Ukuran file melebihi batas form.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorMsg .= 'File hanya terupload sebagian.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMsg .= 'Tidak ada file yang dipilih.';
                    break;
                default:
                    $errorMsg .= 'Terjadi kesalahan sistem saat upload.';
            }
            return redirect()->back()->withInput()->with('error', $errorMsg);
        }

        $validationRule = [
            'file_bukti' => [
                'label' => 'File Bukti',
                'rules' => 'uploaded[file_bukti]'
                    . '|max_size[file_bukti,20480]'
                    . '|ext_in[file_bukti,png,jpg,jpeg,pdf,doc,docx,xls,xlsx,zip]',
            ],
            'tanggapan_auditee' => [
                'label' => 'Tanggapan PIC',
                'rules' => 'required',
            ],
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $temuan_id = $this->request->getPost('temuan_id');
        $existingTL = $this->tindakLanjutModel->where('temuan_id', $temuan_id)->first();

        if ($fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $originalName = $fileBukti->getClientName();
            $newName = $fileBukti->getRandomName();
            $fileBukti->move(WRITEPATH . 'uploads', $newName);

            $db = \Config\Database::connect();
        $db->transStart();

        try {
            if ($existingTL) {
                $oldBukti = $this->buktiModel->where('tindak_lanjut_id', $existingTL['id'])->findAll();
                foreach ($oldBukti as $b) {
                    $oldFilePath = WRITEPATH . 'uploads/' . $b['file_name'];
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
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
                'file_path'        => 'writable/uploads/' . $newName,
                'uploaded_at'      => date('Y-m-d H:i:s')
            ]);

            $db->transComplete();

            return redirect()->to('/temuan/show/' . $temuan_id)->with('success', 'Tindak lanjut/revisi berhasil dikirim. Menunggu verifikasi auditor.');
        
        } catch (\Exception $e) {
            $db->transRollback();
            if (file_exists(WRITEPATH . 'uploads/' . $newName)) {
                unlink(WRITEPATH . 'uploads/' . $newName);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    } else {
        return redirect()->back()->with('error', 'File tidak valid atau belum diupload.');
    }
    }

    public function download($id)
    {
        $bukti = $this->buktiModel->find($id);
        if (!$bukti) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $filePath = WRITEPATH . 'uploads/' . $bukti['file_name'];

        if (file_exists($filePath)) {
            return $this->response->download($filePath, null);
        } else {
            return redirect()->back()->with('error', 'File tidak ditemukan di server.');
        }
    }
}