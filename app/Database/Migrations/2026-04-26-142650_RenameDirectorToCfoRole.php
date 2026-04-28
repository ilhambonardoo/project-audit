<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameDirectorToCfoRole extends Migration
{
    public function up()
    {
        $this->db->table('roles')
                 ->where('role_name', 'Direktur / CFO')
                 ->update(['role_name' => 'CFO (Chief Financial Officer)']);
    }

    public function down()
    {
        $this->db->table('roles')
                 ->where('role_name', 'CFO (Chief Financial Officer)')
                 ->update(['role_name' => 'Direktur / CFO']);
    }
}
