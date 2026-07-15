<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKategoriPklMappingTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tempat_pkl_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'kategori_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['tempat_pkl_id', 'kategori_id']);
        $this->forge->addForeignKey('tempat_pkl_id', 'tempat_pkl', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kategori_id', 'pkl_categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addKey('tempat_pkl_id');
        $this->forge->createTable('kategori_pkl_mapping');
    }

    public function down()
    {
        $this->forge->dropTable('kategori_pkl_mapping');
    }
}
