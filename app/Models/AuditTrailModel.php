<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditTrailModel extends Model
{
    protected $table            = 'audit_trails';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'action', 'ip_address', 'created_at'];

    protected $useTimestamps = false;
}