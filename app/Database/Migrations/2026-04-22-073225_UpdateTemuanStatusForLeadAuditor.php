<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTemuanStatusForLeadAuditor extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE temuan MODIFY COLUMN status_progress ENUM('Open', 'Waiting Lead Auditor Approval', 'Waiting Kadep Approval', 'On Progress', 'Waiting Verification', 'Waiting Manager Approval', 'Waiting Direktur Approval', 'Closed') DEFAULT 'Open'");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE temuan MODIFY COLUMN status_progress ENUM('Open', 'On Progress', 'Waiting Verification', 'Waiting Kadep Approval', 'Waiting Manager Approval', 'Waiting Direktur Approval', 'Closed') DEFAULT 'Open'");
    }
}
