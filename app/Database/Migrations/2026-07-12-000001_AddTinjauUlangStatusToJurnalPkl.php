<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTinjauUlangStatusToJurnalPkl extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE jurnal_pkl MODIFY COLUMN status ENUM('pending', 'disetujui', 'revisi', 'ditolak', 'tinjau_ulang') DEFAULT 'pending'");
    }

    public function down()
    {
        $this->db->query("UPDATE jurnal_pkl SET status = 'pending' WHERE status = 'tinjau_ulang'");
        $this->db->query("ALTER TABLE jurnal_pkl MODIFY COLUMN status ENUM('pending', 'disetujui', 'revisi', 'ditolak') DEFAULT 'pending'");
    }
}
