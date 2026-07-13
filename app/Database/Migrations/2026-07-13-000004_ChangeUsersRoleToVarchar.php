<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeUsersRoleToVarchar extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'siswa'");
    }

    public function down()
    {
        $this->db->query("UPDATE users SET role = 'siswa' WHERE role NOT IN ('admin','guru_mapel','wali_kelas','wakakur','siswa','instruktur')");
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin','guru_mapel','wali_kelas','wakakur','siswa','instruktur') NOT NULL DEFAULT 'siswa'");
    }
}
