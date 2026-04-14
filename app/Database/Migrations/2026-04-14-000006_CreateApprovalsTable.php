<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApprovalsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'temuan_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'approver_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true], 
            'level_urut'   => ['type' => 'INT', 'constraint' => 1],
            'status'       => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected']],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('temuan_id', 'temuan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('approver_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('approvals');
    }

    public function down() { $this->forge->dropTable('approvals'); }
}
