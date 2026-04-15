<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TemuanModel;
use App\Models\TindakLanjutModel;

class Dashboard extends BaseController
{
    protected $temuanModel;
    protected $tindakLanjutModel;

    public function __construct()
    {
        $this->temuanModel = new TemuanModel();
        $this->tindakLanjutModel = new TindakLanjutModel();
    }

    public function index()
    {
        $session = session();
        $role_id = $session->get('role_id');
        $user_id = $session->get('id');
        $today   = date('Y-m-d');

        $data = [
            'title' => 'Dashboard',
            'today' => $today
        ];

        if ($role_id == 2) {
            $data['total']       = $this->temuanModel->where('pic_id', $user_id)->countAllResults();
            $data['open']        = $this->temuanModel->where(['pic_id' => $user_id, 'status_progress' => 'Open'])->countAllResults();
            $data['on_progress'] = $this->temuanModel->where(['pic_id' => $user_id, 'status_progress' => 'On Progress'])->countAllResults();
            $data['closed']      = $this->temuanModel->where(['pic_id' => $user_id, 'status_progress' => 'Closed'])->countAllResults();

            $data['early_warning'] = $this->temuanModel
                ->where('pic_id', $user_id)
                ->where('status_progress !=', 'Closed')
                ->orderBy('deadline', 'ASC')
                ->limit(5)
                ->find();
        } else {
            $data['total']       = $this->temuanModel->countAllResults();
            $data['open']        = $this->temuanModel->where('status_progress', 'Open')->countAllResults();
            $data['on_progress'] = $this->temuanModel->where('status_progress', 'On Progress')->countAllResults();
            $data['closed']      = $this->temuanModel->where('status_progress', 'Closed')->countAllResults();

            $data['overdue_count'] = $this->temuanModel
                ->where('deadline <', $today)
                ->where('status_progress !=', 'Closed')
                ->countAllResults();

            $data['pending_verif'] = $this->tindakLanjutModel
                ->where('status_verifikasi', 'pending')
                ->countAllResults();

            $thirtyDaysLater = date('Y-m-d', strtotime('+30 days'));
            $data['early_warning'] = $this->temuanModel
                ->where('status_progress !=', 'Closed')
                ->where('deadline <=', $thirtyDaysLater)
                ->orderBy('deadline', 'ASC')
                ->limit(10)
                ->find();
        }

        return view('dashboard/index', $data);
    }
}
