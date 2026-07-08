<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTempatPklTable extends Migration
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
            'nama_perusahaan' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255',
            ],
            'alamat' => [
                'type'              => 'TEXT',
                'null'              => true,
            ],
            'kota' => [
                'type'              => 'VARCHAR',
                'constraint'        => '100',
                'null'              => true,
            ],
            'kontak' => [
                'type'              => 'VARCHAR',
                'constraint'        => '100',
                'null'              => true,
            ],
            'telepon' => [
                'type'              => 'VARCHAR',
                'constraint'        => '20',
                'null'              => true,
            ],
            'created_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('tempat_pkl');
    }

    public function down()
    {
        $this->forge->dropTable('tempat_pkl');
    }
}
