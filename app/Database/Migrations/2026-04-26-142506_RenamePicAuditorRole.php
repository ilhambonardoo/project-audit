<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenamePicAuditorRole extends Migration
{
    public function up()
    {
        $this->db->table('roles')
                 ->where('role_name', 'PIC / Auditee')
                 ->update(['role_name' => 'PIC (Auditee)']);
    }

    public function down()
    {
        $this->db->table('roles')
                 ->where('role_name', 'PIC (Auditee)')
                 ->update(['role_name' => 'PIC / Auditee']);
    }
}
