<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJurnalGuruWaliTable extends Migration
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
            'siswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tahun_ajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'jenis_bimbingan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Akademik',
            ],
            'catatan' => [
                'type' => 'TEXT',
            ],
            'tindak_lanjut' => [
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addKey(['guru_id', 'tanggal']);
        $this->forge->addKey(['siswa_id', 'tanggal']);
        $this->forge->addKey('tahun_ajaran');
        $this->forge->createTable('jurnal_guru_wali');
    }

    public function down()
    {
        $this->forge->dropTable('jurnal_guru_wali');
    }
}
