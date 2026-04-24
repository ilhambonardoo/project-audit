<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDraftToStatusEnums extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE temuan MODIFY COLUMN status_progress ENUM('Draft', 'Open', 'On Progress', 'Waiting Verification', 'Waiting Lead Auditor Approval', 'Waiting Kadep Approval', 'Waiting Manager Approval', 'Waiting Direktur Approval', 'Closed') DEFAULT 'Draft'");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE temuan MODIFY COLUMN status_progress ENUM('Open', 'On Progress', 'Waiting Verification', 'Waiting Lead Auditor Approval', 'Waiting Kadep Approval', 'Waiting Manager Approval', 'Waiting Direktur Approval', 'Closed') DEFAULT 'Open'");
    }
}
