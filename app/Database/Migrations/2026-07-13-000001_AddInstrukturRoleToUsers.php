<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInstrukturRoleToUsers extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru_mapel', 'wali_kelas', 'wakakur', 'siswa', 'instruktur') DEFAULT 'siswa'");
    }

    public function down()
    {
        $this->db->query("UPDATE users SET role = 'guru_mapel' WHERE role = 'instruktur'");
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru_mapel', 'wali_kelas', 'wakakur', 'siswa') DEFAULT 'siswa'");
    }
}
