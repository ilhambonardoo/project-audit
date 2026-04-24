<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLeadAuditorRole extends Migration
{
    public function up()
    {
        $this->db->table('roles')->insert([
            'id' => 6,
            'role_name' => 'Lead Auditor'
        ]);
    }

    public function down()
    {
        $this->db->table('roles')->where('id', 6)->delete();
    }
}
