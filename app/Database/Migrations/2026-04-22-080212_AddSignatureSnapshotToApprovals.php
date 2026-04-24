<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSignatureSnapshotToApprovals extends Migration
{
    public function up()
    {
        $this->forge->addColumn('approvals', [
            'signature_snapshot' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Snapshot tanda tangan saat proses approval'
            ]
        ]);
        
        $this->forge->addColumn('temuan', [
            'auditor_signature_snapshot' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Snapshot tanda tangan auditor saat membuat temuan'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('approvals', 'signature_snapshot');
        $this->forge->dropColumn('temuan', 'auditor_signature_snapshot');
    }
}
