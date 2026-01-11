<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

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
