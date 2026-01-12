<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create Izin Siswa Table
 * 
 * Creates student permission/leave request table with approval workflow.
 * Includes start/end date, reason, and document attachment.
 * 
 * Dependencies: siswa, kelas
 * Foreign Keys:
 *   - siswa_id -> siswa(id) ON DELETE CASCADE
 *   - kelas_id -> kelas(id)
 * 
 * Status Enum: pending, approved, rejected
 * Timestamps: created_at, updated_at included in initial schema
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 1.0.0
 */
class CreateIzinSiswaTable extends Migration
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
            'siswa_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'tanggal' => [
                'type'              => 'DATE',
            ],
            'jenis_izin' => [
                'type'              => 'ENUM',
                'constraint'        => ['sakit', 'izin', 'lainnya'],
            ],
            'alasan' => [
                'type'              => 'TEXT',
            ],
            'berkas' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255',
                'null'              => true,
            ],
            'status' => [
                'type'              => 'ENUM',
                'constraint'        => ['pending', 'disetujui', 'ditolak'],
                'default'           => 'pending',
            ],
            'disetujui_oleh' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'null'              => true,
            ],
            'catatan' => [
                'type'              => 'TEXT',
                'null'              => true
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

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('disetujui_oleh', 'users', 'id', 'SET NUL', 'SET NULL');
        $this->forge->createTable('izin_siswa');
    }

    public function down()
    {
        $this->forge->dropTable('izin_siswa');
    }
}
