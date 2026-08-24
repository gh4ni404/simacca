<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create Jurnal Piket Table
 *
 * Creates teacher duty journal entries (jurnal_piket).
 * Stores daily logs, task details, incident notes, and photo documentation for teacher duty.
 *
 * Foreign Keys:
 *   - guru_id -> guru(id) ON DELETE CASCADE
 *
 * Unique Constraint: (guru_id, tanggal) - one journal entry per teacher per day
 */
class CreateJurnalPiketTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'guru_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'tahun_ajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 9,
                'comment'    => 'Format: YYYY/YYYY (e.g. 2025/2026)',
            ],
            'semester' => [
                'type'       => 'ENUM',
                'constraint' => ['ganjil', 'genap'],
                'default'    => 'ganjil',
            ],
            'rincian_tugas' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'foto_dokumentasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('guru_id');
        $this->forge->addKey('tanggal');
        $this->forge->addKey('tahun_ajaran');
        $this->forge->addKey('semester');
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jurnal_piket');
    }

    public function down()
    {
        $this->forge->dropTable('jurnal_piket');
    }
}
