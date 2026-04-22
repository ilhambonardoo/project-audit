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

        $builder = $this->temuanModel;
        
        if ($role_id == 2) {
            $builder = $builder->where('pic_id', $user_id);
        }

        $total = (clone $builder)->countAllResults();

        $open = (clone $builder)->where('status_progress', 'Open')->countAllResults();

        $proses = (clone $builder)
            ->groupStart()
                ->where('status_progress', 'On Progress')
                ->orLike('status_progress', 'Waiting', 'after')
            ->groupEnd()
            ->countAllResults();

        $closed = (clone $builder)->where('status_progress', 'Closed')->countAllResults();

        $on_time_count = (clone $builder)
            ->where('status_progress', 'Closed')
            ->where('updated_at <=', 'deadline', false)
            ->countAllResults();
            
        $overdue_count = (clone $builder)
            ->where('status_progress', 'Closed')
            ->where('updated_at >', 'deadline', false)
            ->countAllResults();

        $early_warning = (clone $builder)
            ->where('status_progress !=', 'Closed')
            ->orderBy('deadline', 'ASC')
            ->limit(5)
            ->findAll();

        $overdue_alert_count = (clone $builder)
            ->where('status_progress !=', 'Closed')
            ->where('deadline <', $today)
            ->countAllResults();

        $pending_verif = $this->tindakLanjutModel
            ->where('status_verifikasi', 'pending')
            ->countAllResults();

        $data = [
            'title'         => 'Dashboard Analytics',
            'today'         => $today,
            'total'         => $total,
            'open'          => $open,
            'proses'        => $proses,
            'closed'        => $closed,
            'chart_data'    => [
                'on_time' => $on_time_count,
                'overdue' => $overdue_count
            ],
            'early_warning' => $early_warning,
            'overdue_count' => $overdue_alert_count,
            'pending_verif' => $pending_verif
        ];

        return view('dashboard/index', $data);
    }
}
