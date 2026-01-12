<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Update Kelas Foreign Key
 * 
 * Adds wali_kelas_id foreign key to kelas table.
 * This allows assignment of a homeroom teacher to each class.
 * 
 * Dependencies: kelas, guru
 * Foreign Keys: wali_kelas_id -> guru(id)
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class UpdateKelasForeignKey extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE kelas ADD CONSTRAINT kelas_wali_kelas_id_foreign FOREIGN KEY (wali_kelas_id) REFERENCES guru(id) ON DELETE SET NULL ON UPDATE SET NULL');
    }

    public function down()
    {
        // Check if foreign key exists before dropping
        $query = $this->db->query("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'kelas' 
            AND CONSTRAINT_NAME = 'kelas_wali_kelas_id_foreign'
        ");
        
        if ($query->getNumRows() > 0) {
            $this->db->query('ALTER TABLE kelas DROP FOREIGN KEY kelas_wali_kelas_id_foreign');
        }
    }
}
