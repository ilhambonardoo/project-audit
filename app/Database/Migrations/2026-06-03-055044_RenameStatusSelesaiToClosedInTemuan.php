<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameStatusSelesaiToClosedInTemuan extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE temuan MODIFY COLUMN status_progress ENUM(
            'Buka', 
            'Menunggu Persetujuan Lead Auditor', 
            'Menunggu Persetujuan Kadep', 
            'Sedang Berjalan', 
            'Menunggu Verifikasi', 
            'Menunggu Persetujuan Manager', 
            'Menunggu Persetujuan Direktur', 
            'Closed',
            'Draft'
        ) DEFAULT 'Buka'");

        $this->db->query("UPDATE temuan SET status_progress = 'Closed' WHERE status_progress = 'Selesai'");
    }

    public function down()
    {
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

        $this->db->query("UPDATE temuan SET status_progress = 'Selesai' WHERE status_progress = 'Closed'");
    }
}
