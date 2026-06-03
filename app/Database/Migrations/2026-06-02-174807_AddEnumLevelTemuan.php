<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnumLevelTemuan extends Migration
{
    public function up()
    {
        $fields = [
            'level_temuan' => [
                'name'       => 'level_temuan', 
                'type'       => 'ENUM',
                'constraint' => ['Observasi', 'Rendah', 'Menengah', 'Tinggi'],
                'default'    => 'Rendah',
                'null'       => false,
            ],
        ];
        
        $this->forge->modifyColumn('temuan', $fields);
    }

    public function down()
    {
        $fields = [
            'level_temuan' => [
                'name'       => 'level_temuan',
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
        ];

        $this->forge->modifyColumn('temuan', $fields);
    }
}