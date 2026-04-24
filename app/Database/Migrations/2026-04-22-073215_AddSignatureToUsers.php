<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSignatureToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'signature' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Base64 image or path to signature'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'signature');
    }
}
