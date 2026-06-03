<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TranslateStatusesAndRoles extends Migration
{
    public function up()
    {
        // 1. Update status_progress ENUM in temuan table
        $this->db->query("ALTER TABLE temuan MODIFY COLUMN status_progress ENUM(
            'Buka', 
            'Menunggu Persetujuan Lead Auditor', 
            'Menunggu Persetujuan Kadep', 
            'Sedang Berjalan', 
            'Menunggu Verifikasi', 
            'Menunggu Persetujuan Manager', 
            'Menunggu Persetujuan Direktur', 
            'Selesai',
            'Draft'
        ) DEFAULT 'Buka'");

        // 2. Update existing data to Indonesian
        $this->db->query("UPDATE temuan SET status_progress = 'Buka' WHERE status_progress = 'Open'");
        $this->db->query("UPDATE temuan SET status_progress = 'Menunggu Persetujuan Lead Auditor' WHERE status_progress = 'Waiting Lead Auditor Approval'");
        $this->db->query("UPDATE temuan SET status_progress = 'Menunggu Persetujuan Kadep' WHERE status_progress = 'Waiting Kadep Approval'");
        $this->db->query("UPDATE temuan SET status_progress = 'Sedang Berjalan' WHERE status_progress = 'On Progress'");
        $this->db->query("UPDATE temuan SET status_progress = 'Menunggu Verifikasi' WHERE status_progress = 'Waiting Verification'");
        $this->db->query("UPDATE temuan SET status_progress = 'Menunggu Persetujuan Manager' WHERE status_progress = 'Waiting Manager Approval'");
        $this->db->query("UPDATE temuan SET status_progress = 'Menunggu Persetujuan Direktur' WHERE status_progress = 'Waiting Direktur Approval'");
        $this->db->query("UPDATE temuan SET status_progress = 'Selesai' WHERE status_progress = 'Closed'");

        // 3. Update role_name in roles table
        $this->db->query("UPDATE roles SET role_name = 'PIC / Auditee' WHERE id = 2");
        $this->db->query("UPDATE roles SET role_name = 'Manajer Pabrik' WHERE id = 4");
        $this->db->query("UPDATE roles SET role_name = 'Direktur Keuangan (CFO)' WHERE id = 5");
        $this->db->query("UPDATE roles SET role_name = 'Ketua Auditor' WHERE id = 6");
    }

    public function down()
    {
        // Rollback Roles
        $this->db->query("UPDATE roles SET role_name = 'PIC / Auditee' WHERE id = 2");
        $this->db->query("UPDATE roles SET role_name = 'Plant Manager' WHERE id = 4");
        $this->db->query("UPDATE roles SET role_name = 'CFO' WHERE id = 5");
        $this->db->query("UPDATE roles SET role_name = 'Lead Auditor' WHERE id = 6");

        // Rollback Data
        $this->db->query("UPDATE temuan SET status_progress = 'Open' WHERE status_progress = 'Buka'");
        $this->db->query("UPDATE temuan SET status_progress = 'Waiting Lead Auditor Approval' WHERE status_progress = 'Menunggu Persetujuan Lead Auditor'");
        $this->db->query("UPDATE temuan SET status_progress = 'Waiting Kadep Approval' WHERE status_progress = 'Menunggu Persetujuan Kadep'");
        $this->db->query("UPDATE temuan SET status_progress = 'On Progress' WHERE status_progress = 'Sedang Berjalan'");
        $this->db->query("UPDATE temuan SET status_progress = 'Waiting Verification' WHERE status_progress = 'Menunggu Verifikasi'");
        $this->db->query("UPDATE temuan SET status_progress = 'Waiting Manager Approval' WHERE status_progress = 'Menunggu Persetujuan Manager'");
        $this->db->query("UPDATE temuan SET status_progress = 'Waiting Direktur Approval' WHERE status_progress = 'Menunggu Persetujuan Direktur'");
        $this->db->query("UPDATE temuan SET status_progress = 'Closed' WHERE status_progress = 'Selesai'");

        // Rollback ENUM
        $this->db->query("ALTER TABLE temuan MODIFY COLUMN status_progress ENUM(
            'Open', 
            'Waiting Lead Auditor Approval', 
            'Waiting Kadep Approval', 
            'On Progress', 
            'Waiting Verification', 
            'Waiting Manager Approval', 
            'Waiting Direktur Approval', 
            'Closed',
            'Draft'
        ) DEFAULT 'Open'");
    }
}
