<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'Produksi'],
            ['name' => 'Teknik / Maintenance'],
            ['name' => 'Quality Control'],
            ['name' => 'Finance / Accounting'],
            ['name' => 'Human Resources'],
            ['name' => 'Logistik / Warehouse'],
            ['name' => 'Management'],
            ['name' => 'Internal Audit']
        ];

        $this->db->table('departments')->insertBatch($data);
    }
}
