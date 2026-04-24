<?php

namespace App\Models;

use CodeIgniter\Model;

class ApprovalModel extends Model
{
    protected $table            = 'approvals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['temuan_id', 'approver_id', 'level_urut', 'status', 'created_at', 'updated_at', 'signature_snapshot'];

    protected $useTimestamps = true;
}