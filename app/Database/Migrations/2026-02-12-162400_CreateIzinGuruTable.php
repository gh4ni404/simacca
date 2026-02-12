<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create Izin Guru Table
 * 
 * Creates teacher leave/permission request table.
 * Used when teacher cannot attend and needs to request leave.
 * 
 * Dependencies: guru, users
 * Foreign Keys:
 *   - guru_id -> guru(id) ON DELETE CASCADE
 *   - disetujui_oleh -> users(id)
 * 
 * Jenis Izin: izin, sakit, cuti, dinas_luar, lainnya
 * Status: pending, disetujui, ditolak
 * 
 * Workflow:
 *   1. Guru submits izin request
 *   2. Wakakur reviews and approves/rejects
 *   3. If approved, auto-creates absensi_guru record with status
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 2.0.0
 */
class CreateIzinGuruTable extends Migration
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
            'tanggal_mulai' => [
                'type'              => 'DATE',
                'comment'           => 'Start date of leave',
            ],
            'tanggal_selesai' => [
                'type'              => 'DATE',
                'comment'           => 'End date of leave',
            ],
            'jenis_izin' => [
                'type'              => 'ENUM',
                'constraint'        => ['izin', 'sakit', 'cuti', 'dinas_luar', 'lainnya'],
                'default'           => 'izin',
            ],
            'alasan' => [
                'type'              => 'TEXT',
                'comment'           => 'Reason for leave request',
            ],
            'berkas' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255',
                'null'              => true,
                'comment'           => 'Supporting document (medical certificate, etc)',
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
                'comment'           => 'User ID of approver (wakakur)',
            ],
            'tanggal_disetujui' => [
                'type'              => 'DATETIME',
                'null'              => true,
                'comment'           => 'Approval/rejection timestamp',
            ],
            'catatan_persetujuan' => [
                'type'              => 'TEXT',
                'null'              => true,
                'comment'           => 'Notes from approver',
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
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('disetujui_oleh', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('izin_guru');
    }

    public function down()
    {
        $this->forge->dropTable('izin_guru');
    }
}
