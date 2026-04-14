<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBuktiPendukungTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tindak_lanjut_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'file_name'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'file_path'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'uploaded_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('tindak_lanjut_id', 'tindak_lanjut', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bukti_pendukung');
    }

    public function down() { $this->forge->dropTable('bukti_pendukung'); }
}
