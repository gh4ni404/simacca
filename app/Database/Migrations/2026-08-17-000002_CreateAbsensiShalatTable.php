<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAbsensiShalatTable extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Drop if exists (safety for re-run)
        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        $db->query('DROP TABLE IF EXISTS `absensi_shalat`');
        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'prayer_session_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'siswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'waktu_absen' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['prayer_session_id', 'siswa_id']);
        $this->forge->addKey('prayer_session_id');
        $this->forge->addKey('siswa_id');
        $this->forge->addForeignKey('prayer_session_id', 'prayer_sessions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('absensi_shalat');
    }

    public function down()
    {
        $this->forge->dropTable('absensi_shalat');
    }
}
