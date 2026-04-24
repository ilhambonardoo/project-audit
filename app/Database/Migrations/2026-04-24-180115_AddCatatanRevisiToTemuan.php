<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCatatanRevisiToTemuan extends Migration
{
    public function up()
    {
        $fields = [
            'catatan_revisi' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'status_progress',
            ],
        ];
        $this->forge->addColumn('temuan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('temuan', 'catatan_revisi');
    }
}
