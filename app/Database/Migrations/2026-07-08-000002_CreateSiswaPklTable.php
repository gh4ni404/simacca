<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiswaPklTable extends Migration
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
            'tempat_pkl_id' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
            ],
            'tahun_ajaran' => [
                'type'              => 'VARCHAR',
                'constraint'        => '9',
            ],
            'created_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tempat_pkl_id', 'tempat_pkl', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['siswa_id', 'tahun_ajaran']);
        $this->forge->createTable('siswa_pkl');
    }

    public function down()
    {
        $this->forge->dropTable('siswa_pkl');
    }
}
