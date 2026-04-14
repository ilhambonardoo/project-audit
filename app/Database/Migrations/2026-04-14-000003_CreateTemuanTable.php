<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTemuanTable extends Migration
{
public function up()
{
    $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'auditor_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'pic_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'klausul'         => ['type' => 'VARCHAR', 'constraint' => 50],
            'judul_temuan'    => ['type' => 'VARCHAR', 'constraint' => 255],
            'uraian_temuan'   => ['type' => 'TEXT'], 
            'kriteria'        => ['type' => 'TEXT'], 
            'rekomendasi'     => ['type' => 'TEXT'], 
            'kategori_status' => ['type' => 'ENUM', 'constraint' => ['Temuan Baru', 'Temuan Berulang']],
            'level_temuan'    => ['type' => 'ENUM', 'constraint' => ['Rendah', 'Menengah', 'Tinggi']],
            'status_progress' => ['type' => 'ENUM', 'constraint' => ['open', 'on progress', 'waiting verification', 'closed']],
            'deadline'        => ['type' => 'DATE'],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('auditor_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('pic_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('temuan');
    }

    public function down() { $this->forge->dropTable('temuan'); }
}
