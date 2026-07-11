<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolloverHistoryTables extends Migration
{
    public function up()
    {
        // Tabel ringan untuk metadata rollover (selalu di-load)
        $this->forge->addField([
            'id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'auto_increment'    => true,
            ],
            'from_year' => [
                'type'              => 'VARCHAR',
                'constraint'        => 9,
            ],
            'to_year' => [
                'type'              => 'VARCHAR',
                'constraint'        => 9,
            ],
            'total_students' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'default'           => 0,
            ],
            'naik_kelas' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'default'           => 0,
            ],
            'lulus' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'default'           => 0,
            ],
            'skipped_count' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'default'           => 0,
            ],
            'reverted_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
            'created_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
            'updated_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('reverted_at');
        $this->forge->addKey('created_at');
        $this->forge->createTable('rollover_history');

        // Tabel backup data (hanya di-load saat revert)
        $this->forge->addField([
            'id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'auto_increment'    => true,
            ],
            'history_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'siswa_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'user_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'old_kelas_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'old_tahun_ajaran' => [
                'type'              => 'VARCHAR',
                'constraint'        => 9,
            ],
            'old_is_active' => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
                'unsigned'          => true,
                'default'           => 1,
            ],
            'created_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('history_id');
        $this->forge->addForeignKey('history_id', 'rollover_history', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rollover_backup');
    }

    public function down()
    {
        $this->forge->dropTable('rollover_backup');
        $this->forge->dropTable('rollover_history');
    }
}
