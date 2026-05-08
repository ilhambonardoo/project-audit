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
            ['id' => 3, 'role_name' => 'Kadep'],
            ['id' => 4, 'role_name' => 'Plant Manager'],
            ['id' => 5, 'role_name' => 'CFO'],
            ['id' => 6, 'role_name' => 'Lead Auditor'],
        ];

        // Using simple insert because of specific IDs
        foreach ($data as $role) {
            $this->db->table('roles')->replace($role);
        }
    }
}
