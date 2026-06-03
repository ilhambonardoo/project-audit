<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id' => 1, 'role_name' => 'Admin'],
            ['id' => 2, 'role_name' => 'PIC / Auditee'],
            ['id' => 3, 'role_name' => 'Ass Head Corp Internal Audit'],
            ['id' => 4, 'role_name' => 'Direktur'],
            ['id' => 5, 'role_name' => 'CFO'],
            ['id' => 6, 'role_name' => 'Lead Auditor'],
        ];

        // Using simple insert because of specific IDs
        foreach ($data as $role) {
            $this->db->table('roles')->replace($role);
        }
    }
}
