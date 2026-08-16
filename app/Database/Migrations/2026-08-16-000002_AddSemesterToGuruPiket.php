<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add Semester to Guru Piket
 *
 * Adds semester column (ganjil/genap) to guru_piket table.
 * Drops old UNIQUE constraint and adds new one with semester.
 *
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class AddSemesterToGuruPiket extends Migration
{
    public function up()
    {
        // Add semester column after tahun_ajaran
        $this->forge->addColumn('guru_piket', [
            'semester' => [
                'type'       => 'ENUM',
                'constraint' => ['ganjil', 'genap'],
                'after'      => 'tahun_ajaran',
                'default'    => 'ganjil',
                'comment'    => 'ganjil (odd) or genap (even) semester',
            ],
        ]);

        // Drop old UNIQUE constraint
        $this->db->query("ALTER TABLE `guru_piket` DROP INDEX `guru_id_tahun_ajaran_hari`");

        // Add new UNIQUE constraint with semester
        $this->db->query("ALTER TABLE `guru_piket` ADD UNIQUE KEY `guru_id_tahun_ajaran_semester_hari` (`guru_id`, `tahun_ajaran`, `semester`, `hari`)");

        // Add index for semester
        $this->db->query("ALTER TABLE `guru_piket` ADD INDEX `semester` (`semester`)");
    }

    public function down()
    {
        // Drop new UNIQUE constraint and index
        $this->db->query("ALTER TABLE `guru_piket` DROP INDEX `guru_id_tahun_ajaran_semester_hari`");
        $this->db->query("ALTER TABLE `guru_piket` DROP INDEX `semester`");

        // Drop semester column
        $this->forge->dropColumn('guru_piket', 'semester');

        // Restore old UNIQUE constraint
        $this->db->query("ALTER TABLE `guru_piket` ADD UNIQUE KEY `guru_id_tahun_ajaran_hari` (`guru_id`, `tahun_ajaran`, `hari`)");
    }
}
