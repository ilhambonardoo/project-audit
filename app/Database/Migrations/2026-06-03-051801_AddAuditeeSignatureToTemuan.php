<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAuditeeSignatureToTemuan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('temuan', [
            'auditee_signature_snapshot' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'auditor_signature_snapshot'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('temuan', 'auditee_signature_snapshot');
    }
}
