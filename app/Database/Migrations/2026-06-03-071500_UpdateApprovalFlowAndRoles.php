<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateApprovalFlowAndRoles extends Migration
{
    public function up()
    {
        // Update status_progress enum to include new statuses for Final Report signing flow
        $this->db->query("ALTER TABLE temuan MODIFY COLUMN status_progress ENUM(
            'Draft', 
            'Open', 
            'On Progress', 
            'Sedang Berjalan',
            'Waiting Verification', 
            'Menunggu Persetujuan Lead Auditor', 
            'Closed',
            'Waiting CFO Approval',
            'Waiting Direktur Approval',
            'Selesai'
        ) DEFAULT 'Draft'");

        // Update roles table to reflect new titles
        $this->db->table('roles')->where('id', 3)->update(['role_name' => 'Ass Head Corp Internal Audit']);
        $this->db->table('roles')->where('id', 4)->update(['role_name' => 'Direktur']);
        
        // CFO remains the same
        // $this->db->table('roles')->where('id', 5)->update(['role_name' => 'CFO']);
    }

    public function down()
    {
        // Rollback status enum
        $this->db->query("ALTER TABLE temuan MODIFY COLUMN status_progress ENUM(
            'Draft', 
            'Open', 
            'On Progress', 
            'Menunggu Persetujuan Lead Auditor', 
            'Menunggu Persetujuan Kadep', 
            'Waiting Verification', 
            'Waiting Manager Approval', 
            'Waiting Direktur Approval', 
            'Closed'
        ) DEFAULT 'Draft'");

        // Rollback role names
        $this->db->table('roles')->where('id', 3)->update(['role_name' => 'Kadep']);
        $this->db->table('roles')->where('id', 4)->update(['role_name' => 'Plant Manager']);
    }
}
