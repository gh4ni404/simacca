<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePembimbingPklTable extends Migration
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
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tempat_pkl_id', 'tempat_pkl', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addKey(['tahun_ajaran', 'guru_id', 'tempat_pkl_id'], false, true);
        $this->forge->createTable('pembimbing_pkl');
    }

    public function down()
    {
        $this->forge->dropTable('pembimbing_pkl');
    }
}
