<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdminApprovalFlow extends Migration
{
    public function up()
    {
        // Update status_progress enum to include Waiting Admin Approval
        $this->db->query("ALTER TABLE temuan MODIFY COLUMN status_progress ENUM(
            'Draft', 
            'Open', 
            'On Progress', 
            'Sedang Berjalan',
            'Waiting Verification', 
            'Menunggu Persetujuan Lead Auditor',
            'Waiting Admin Approval',
            'Closed',
            'Waiting CFO Approval',
            'Waiting Direktur Approval',
            'Selesai'
        ) NOT NULL DEFAULT 'Draft'");
    }

    public function down()
    {
        // Rollback status enum
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
    }
}
