<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePklTaskOrientedTables extends Migration
{
    public function up()
    {
        // Categories
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('pkl_categories');

        // Tasks
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'kategori_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'judul' => ['type' => 'VARCHAR', 'constraint' => 255],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kategori_id', 'pkl_categories', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addKey(['siswa_id']);
        $this->forge->createTable('pkl_tasks');

        // Progress
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'task_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tanggal' => ['type' => 'DATE'],
            'deskripsi' => ['type' => 'TEXT'],
            'foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'],
            'catatan_pembimbing' => ['type' => 'TEXT', 'null' => true],
            'verified_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'verified_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('task_id', 'pkl_tasks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('verified_by', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addKey(['task_id', 'tanggal']);
        $this->forge->addKey(['tanggal']);
        $this->forge->createTable('pkl_progress');

        // Seed default categories
        $categories = [
            ['nama' => 'Desain', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Programming', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Administrasi', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Marketing', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Lainnya', 'created_at' => date('Y-m-d H:i:s')],
        ];
        $builder = $this->db->table('pkl_categories');
        foreach ($categories as $cat) {
            $builder->insert($cat);
        }
    }

    public function down()
    {
        $this->forge->dropTable('pkl_progress');
        $this->forge->dropTable('pkl_tasks');
        $this->forge->dropTable('pkl_categories');
    }
}
