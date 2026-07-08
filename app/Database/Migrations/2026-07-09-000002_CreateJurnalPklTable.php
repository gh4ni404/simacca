<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJurnalPklTable extends Migration
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
            'nama_kegiatan' => [
                'type'              => 'VARCHAR',
                'constraint'        => 255,
            ],
            'deskripsi' => [
                'type'              => 'TEXT',
            ],
            'foto' => [
                'type'              => 'VARCHAR',
                'constraint'        => 255,
                'null'              => true,
            ],
            'status' => [
                'type'              => 'ENUM',
                'constraint'        => ['pending', 'disetujui', 'revisi', 'ditolak'],
                'default'           => 'pending',
            ],
            'tanggal' => [
                'type'              => 'DATE',
            ],
            'catatan_pembimbing' => [
                'type'              => 'TEXT',
                'null'              => true,
            ],
            'verified_by' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'null'              => true,
            ],
            'verified_at' => [
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

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('verified_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addKey(['siswa_id', 'tanggal']);
        $this->forge->createTable('jurnal_pkl');
    }

    public function down()
    {
        $this->forge->dropTable('jurnal_pkl');
    }
}
