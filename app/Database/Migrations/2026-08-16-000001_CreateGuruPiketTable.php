<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create Guru Piket Table
 *
 * Creates teacher duty assignment table (piket).
 * No shift system, no dates - just day-based assignments tied to active academic year.
 *
 * Dependencies: guru
 * Foreign Keys:
 *   - guru_id -> guru(id) ON DELETE CASCADE
 *
 * Unique Constraint: (guru_id, tahun_ajaran, hari) - one guru per day per academic year
 *
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class CreateGuruPiketTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'auto_increment'    => true,
            ],
            'guru_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'tahun_ajaran' => [
                'type'              => 'VARCHAR',
                'constraint'        => 9,
                'comment'           => 'Format: YYYY/YYYY (e.g. 2025/2026)',
            ],
            'hari' => [
                'type'              => 'ENUM',
                'constraint'        => ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'],
            ],
            'keterangan' => [
                'type'              => 'TEXT',
                'null'              => true,
                'comment'           => 'Optional notes for this duty assignment',
            ],
            'is_active' => [
                'type'              => 'BOOLEAN',
                'default'           => 1,
            ],
            'created_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
            'updated_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
            'deleted_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['guru_id', 'tahun_ajaran', 'hari']);
        $this->forge->addKey('guru_id');
        $this->forge->addKey('tahun_ajaran');
        $this->forge->addKey('hari');
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('guru_piket');
    }

    public function down()
    {
        $this->forge->dropTable('guru_piket');
    }
}
