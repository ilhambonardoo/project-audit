<?php

namespace App\Models;

use CodeIgniter\Model;

class TindakLanjutModel extends Model
{
    protected $table            = 'tindak_lanjut';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'temuan_id', 'tanggapan_auditee', 'status_verifikasi', 'catatan_auditor'
    ];

    protected $useTimestamps = true;
}