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


    private function getFilteredBuilder(){
        $role_id = session()->get('role_id');
        $department = session()->get('department');

        $builder = $this->temuanModel->builder();

        if(!in_array($role_id, [1, 6])){
            $builder->select('temuan.*')->join('users', 'users.id = temuan.pic_id')->where('users.department', $department);
        }

        return $builder;
    }



    public function index()
    {
        $today = date('Y-m-d');

        // ini buat hitung statistik menggunakan builder yang sudah di filter
        $total = $this->getFilteredBuilder()->countAllResults();
        $open = $this->getFilteredBuilder()->where('status_progress', 'Open')->countAllResults();
        $closed = $this->getFilteredBuilder()->where('status_progress', 'Closed')->countAllResults();

        $proses = $this->getFilteredBuilder()->groupStart()
                    ->where('status_progress', 'On Progress')
                    ->orLike('status_progress', 'Waiting', 'after')
                    ->groupEnd()->countAllResults();

        // Data chart (overdue atau on time)
        $on_time_count = $this->getFilteredBuilder()
            ->where('status_progress', 'Closed')
            ->where('temuan.updated_at <= temuan.deadline')
            ->countAllResults();

        $overdue_count_closed = $this->getFilteredBuilder()
            ->where('status_progress', 'Closed')
            ->where('temuan.updated_at > temuan.deadline')
            ->countAllResults();

        $early_warning = $this->getFilteredBuilder()
            ->where('status_progress !=', 'Closed')
            ->orderBy('deadline', 'ASC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $overdue_alert_count = $this->getFilteredBuilder()
            ->where('status_progress !=', 'Closed')
            ->where('deadline <', $today)
            ->countAllResults();

        $pending_verif = $this->getPendingVerifCount();

        $data = [
            'title'         => 'Dashboard Analytics',
            'today'         => $today,
            'total'         => $total,
            'open'          => $open,
            'proses'        => $proses,
            'closed'        => $closed,
            'chart_data'    => [
                'on_time' => $on_time_count,
                'overdue' => $overdue_count_closed
            ],
            'early_warning' => $early_warning,
            'overdue_count' => $overdue_alert_count,
            'pending_verif' => $pending_verif
        ];

        return view('dashboard/index', $data);

    }

    private function getPendingVerifCount()
    {
        $role_id = session()->get('role_id');
        $department = session()->get('department');
        
        $builder = $this->tindakLanjutModel->builder();
        $builder->where('status_verifikasi', 'pending');

        if (!in_array($role_id, [1, 6])) {
            $builder->join('temuan', 'temuan.id = tindak_lanjut.temuan_id')
                    ->join('users', 'users.id = temuan.pic_id')
                    ->where('users.department', $department);
        }

        return $builder->countAllResults();
    }
}
