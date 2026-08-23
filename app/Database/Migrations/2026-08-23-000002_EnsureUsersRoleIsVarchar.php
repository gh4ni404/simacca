<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Ensure users.role column is VARCHAR(50) to support multi-roles like ketua_jurusan, kepala_sekolah, tendik
 */
class EnsureUsersRoleIsVarchar extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'guru_mapel'");
    }

    public function down()
    {
        // No action needed
    }
}
