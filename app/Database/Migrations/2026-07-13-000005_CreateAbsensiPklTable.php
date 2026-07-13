<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAbsensiPklTable extends Migration
{
    public function up()
    {
        // Header table: one record per pembimbing per day
        $this->forge->addField([
            'id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'auto_increment'    => true,
            ],
            'pembimbing_pkl_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'tanggal' => [
                'type'              => 'DATE',
            ],
            'keterangan_umum' => [
                'type'              => 'TEXT',
                'null'              => true,
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
            'deleted_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('pembimbing_pkl_id', 'pembimbing_pkl', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['pembimbing_pkl_id', 'tanggal']);
        $this->forge->createTable('absensi_pkl');

        // Detail table: one record per siswa per absensi session
        $this->forge->addField([
            'id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'auto_increment'    => true,
            ],
            'absensi_pkl_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'siswa_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'status' => [
                'type'              => 'ENUM',
                'constraint'        => ['hadir', 'izin', 'sakit', 'alpa', 'dispen'],
                'default'           => 'alpa',
            ],
            'keterangan' => [
                'type'              => 'TEXT',
                'null'              => true,
            ],
            'waktu_absen' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('absensi_pkl_id', 'absensi_pkl', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['absensi_pkl_id', 'siswa_id']);
        $this->forge->createTable('absensi_pkl_detail');
    }

    public function down()
    {
        $this->forge->dropTable('absensi_pkl_detail');
        $this->forge->dropTable('absensi_pkl');
    }
}
