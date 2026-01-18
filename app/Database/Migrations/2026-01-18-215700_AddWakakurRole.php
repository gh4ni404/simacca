<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add Wakakur Role
 * 
 * Adds 'wakakur' (Wakil Kepala Sekolah Bidang Kurikulum) role to users table.
 * Wakakur has combined access of guru_mapel, wali_kelas, and can print detailed attendance reports.
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class AddWakakurRole extends Migration
{
    public function up()
    {
        // Modify the role ENUM to add wakakur
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru_mapel', 'wali_kelas', 'wakakur', 'siswa') DEFAULT 'siswa'");
    }

    public function down()
    {
        // Remove wakakur role - revert to original ENUM
        // First, update any wakakur users to guru_mapel to prevent data loss
        $this->db->query("UPDATE users SET role = 'guru_mapel' WHERE role = 'wakakur'");
        
        // Then remove wakakur from ENUM
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru_mapel', 'wali_kelas', 'siswa') DEFAULT 'siswa'");
    }
}
