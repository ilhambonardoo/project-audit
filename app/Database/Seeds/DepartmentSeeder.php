<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'Departemen Produksi'],
            ['name' => 'Departemen Teknik'],
            ['name' => 'Departemen Quality Control'],
            ['name' => 'Departemen Finance Accounting Tax'],
            ['name' => 'Departemen Human Resources'],
            ['name' => 'Departemen Logistik'],
            ['name' => 'Departemen Management'],
            ['name' => 'Departemen Internal Audit'],
            ['name' => 'Departemen Management Information System'],
            ['name' => 'Departemen Ekspedisi'],
            ['name' => 'Departemen Procurement']
        ];

        $this->db->table('departments')->insertBatch($data);
    }
}
