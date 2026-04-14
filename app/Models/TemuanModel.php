<?php

namespace App\Models;

use CodeIgniter\Model;

class TemuanModel extends Model
{
    protected $table            = 'temuan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'auditor_id', 'pic_id', 'klausul', 'judul_temuan', 
        'uraian_temuan', 'kriteria', 'rekomendasi', 
        'kategori_status', 'level_temuan', 'status_progress', 'deadline'
    ];

    protected $useTimestamps = true;

    public function getTemuanOverdue()
    {
        return $this->where('deadline <', date('Y-m-d'))
                    ->where('status_progress !=', 'closed')
                    ->findAll();
    }
}