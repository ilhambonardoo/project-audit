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
            // PIC Production
            [
                'name'          => 'PIC Produksi',
                'email'         => 'pic.produksi@audit.com',
                'password'      => password_hash('pic123', PASSWORD_DEFAULT),
                'role_id'       => 2,
                'department_id' => 1, // Produksi
                'department'    => 'Produksi',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            // Ass Head Corp Internal Audit Production
            [
                'name'          => 'Ass Head Corp IA Produksi',
                'email'         => 'asshead.produksi@audit.com',
                'password'      => password_hash('asshead123', PASSWORD_DEFAULT),
                'role_id'       => 3,
                'department_id' => 1, // Produksi
                'department'    => 'Produksi',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            // CFO
            [
                'name'          => 'CFO User',
                'email'         => 'cfo@audit.com',
                'password'      => password_hash('cfo123', PASSWORD_DEFAULT),
                'role_id'       => 5,
                'department_id' => 4, // Finance / Accounting
                'department'    => 'Finance / Accounting',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert using query builder to handle multiple rows
        $this->db->table('users')->insertBatch($data);
    }
}
