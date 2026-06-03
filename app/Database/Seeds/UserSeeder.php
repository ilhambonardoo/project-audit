<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Admin User
            [
                'name'          => 'Administrator',
                'email'         => 'admin@audit.com',
                'password'      => password_hash('admin123', PASSWORD_DEFAULT),
                'role_id'       => 1,
                'department_id' => 7, // Management (based on DepartmentSeeder)
                'department'    => 'Management',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            // Lead Auditor
            [
                'name'          => 'Lead Auditor',
                'email'         => 'auditor@audit.com',
                'password'      => password_hash('auditor123', PASSWORD_DEFAULT),
                'role_id'       => 6,
                'department_id' => 8, // Internal Audit
                'department'    => 'Internal Audit',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'PIC MIS',
                'email'         => 'pic.mis@audit.com',
                'password'      => password_hash('mis123', PASSWORD_DEFAULT),
                'role_id'       => 2,
                'department_id' => 9,
                'department'    => 'Departemen Management Information System',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'PIC Procurement',
                'email'         => 'pic.procurement@audit.com',
                'password'      => password_hash('pro123', PASSWORD_DEFAULT),
                'role_id'       => 2,
                'department_id' => 11,
                'department'    => 'Departemen Procurement',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'PIC FAT',
                'email'         => 'pic.fat@audit.com',
                'password'      => password_hash('fat123', PASSWORD_DEFAULT),
                'role_id'       => 2,
                'department_id' => 4,
                'department'    => 'Finance Accounting Tax',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'name'          => 'PIC Ekspedisi',
                'email'         => 'pic.ekspedisi@audit.com',
                'password'      => password_hash('eks123', PASSWORD_DEFAULT),
                'role_id'       => 2,
                'department_id' => 10,
                'department'    => 'Departemen Ekspedisi',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            // Ass Head Corp Internal Audit Production
            [
                'name'          => 'Ass Head Corp IA Produksi',
                'email'         => 'asshead@audit.com',
                'password'      => password_hash('asshead123', PASSWORD_DEFAULT),
                'role_id'       => 3,
                'department_id' => 8,
                'department'    => 'Internal Audit',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            // CFO
            [
                'name'          => 'CFO User',
                'email'         => 'cfo@audit.com',
                'password'      => password_hash('cfo123', PASSWORD_DEFAULT),
                'role_id'       => 5,
                'department_id' => 4,
                'department'    => 'Finance Accounting Tax',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert using query builder to handle multiple rows
        $this->db->table('users')->insertBatch($data);
    }
}
