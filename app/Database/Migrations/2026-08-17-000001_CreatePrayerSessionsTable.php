<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrayerSessionsTable extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Drop if exists (safety for re-run)
        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        $db->query('DROP TABLE IF EXISTS `prayer_sessions`');
        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
            ],
            'guru_piket_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'is_active' => [
                'type'    => 'BOOLEAN',
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('token');
        $this->forge->addKey('guru_piket_id');
        $this->forge->addKey('is_active');
        $this->forge->addKey('expires_at');
        $this->forge->addForeignKey('guru_piket_id', 'guru_piket', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('prayer_sessions');
    }

    public function down()
    {
        $this->forge->dropTable('prayer_sessions');
    }
}
