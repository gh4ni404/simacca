<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create Absensi Guru Table
 * 
 * Creates teacher attendance table for self check-in/check-out system.
 * Includes selfie photo validation, location data, and work duration tracking.
 * 
 * Dependencies: guru, users
 * Foreign Keys:
 *   - guru_id -> guru(id) ON DELETE CASCADE
 *   - created_by -> users(id)
 * 
 * Status Enum: hadir, terlambat, izin, sakit, alpha, cuti
 * Photo Storage: writable/uploads/absensi_guru/YYYY/MM/DD/
 * 
 * Business Rules:
 *   - Auto hadir: check_in <= 07:15:00
 *   - Auto terlambat: check_in > 07:15:00
 *   - Auto alpha: if no check_in by 10:00:00
 *   - Minimum work duration: 8 hours (480 minutes)
 *   - Early checkout warning if duration < 480 minutes
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 2.0.0
 */
class CreateAbsensiGuruTable extends Migration
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
            'tanggal' => [
                'type'              => 'DATE',
            ],
            'status' => [
                'type'              => 'ENUM',
                'constraint'        => ['hadir', 'terlambat', 'izin', 'sakit', 'alpha', 'cuti'],
                'default'           => 'alpha',
            ],
            'check_in' => [
                'type'              => 'TIME',
                'null'              => true,
            ],
            'check_out' => [
                'type'              => 'TIME',
                'null'              => true,
            ],
            'durasi_menit' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'null'              => true,
                'comment'           => 'Duration in minutes between check_in and check_out',
            ],
            'foto_check_in' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255',
                'null'              => true,
                'comment'           => 'Selfie photo path for check-in',
            ],
            'foto_check_out' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255',
                'null'              => true,
                'comment'           => 'Selfie photo path for check-out',
            ],
            'latitude_check_in' => [
                'type'              => 'DECIMAL',
                'constraint'        => '10,8',
                'null'              => true,
                'comment'           => 'GPS latitude for check-in validation',
            ],
            'longitude_check_in' => [
                'type'              => 'DECIMAL',
                'constraint'        => '11,8',
                'null'              => true,
                'comment'           => 'GPS longitude for check-in validation',
            ],
            'latitude_check_out' => [
                'type'              => 'DECIMAL',
                'constraint'        => '10,8',
                'null'              => true,
                'comment'           => 'GPS latitude for check-out validation',
            ],
            'longitude_check_out' => [
                'type'              => 'DECIMAL',
                'constraint'        => '11,8',
                'null'              => true,
                'comment'           => 'GPS longitude for check-out validation',
            ],
            'early_checkout' => [
                'type'              => 'BOOLEAN',
                'default'           => 0,
                'comment'           => 'Flag if checkout before 8 hours',
            ],
            'early_checkout_reason' => [
                'type'              => 'TEXT',
                'null'              => true,
                'comment'           => 'Reason for early checkout if duration < 480 min',
            ],
            'catatan' => [
                'type'              => 'TEXT',
                'null'              => true,
                'comment'           => 'Additional notes or remarks',
            ],
            'set_by_wakakur' => [
                'type'              => 'BOOLEAN',
                'default'           => 0,
                'comment'           => 'Flag if status manually set by wakakur',
            ],
            'set_by_user_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'null'              => true,
                'comment'           => 'User ID who manually set the status (wakakur)',
            ],
            'created_by' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
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
        $this->forge->addKey(['guru_id', 'tanggal'], false, true); // Unique constraint
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('set_by_user_id', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('absensi_guru');
    }

    public function down()
    {
        $this->forge->dropTable('absensi_guru');
    }
}
