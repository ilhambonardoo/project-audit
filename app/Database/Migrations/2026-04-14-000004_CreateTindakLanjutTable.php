<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTindakLanjutTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'temuan_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tanggapan_auditee' => ['type' => 'TEXT', 'null' => true],
            'status_verifikasi' => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected', 'revision_required']],
            'catatan_auditor'   => ['type' => 'TEXT', 'null' => true], 
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('temuan_id', 'temuan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tindak_lanjut');
    }

    public function down() { $this->forge->dropTable('tindak_lanjut'); }
}
