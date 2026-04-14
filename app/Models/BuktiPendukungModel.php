<?php

namespace App\Models;

use CodeIgniter\Model;

class BuktiPendukungModel extends Model
{
    protected $table            = 'bukti_pendukung';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['tindak_lanjut_id', 'file_name', 'file_path', 'uploaded_at'];

    protected $useTimestamps = false; 
}