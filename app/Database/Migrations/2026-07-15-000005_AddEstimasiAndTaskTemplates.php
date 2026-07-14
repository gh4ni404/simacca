<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEstimasiAndTaskTemplates extends Migration
{
    public function up()
    {
        // Add estimasi column to pkl_tasks
        $this->forge->addColumn('pkl_tasks', [
            'estimasi' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'after' => 'status'],
        ]);

        // Create pkl_task_templates table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tempat_pkl_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'judul' => ['type' => 'VARCHAR', 'constraint' => 255],
            'kategori_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'estimasi' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('tempat_pkl_id', 'tempat_pkl', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kategori_id', 'pkl_categories', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addKey('tempat_pkl_id');
        $this->forge->createTable('pkl_task_templates');
    }

    public function down()
    {
        $this->forge->dropTable('pkl_task_templates');
        $this->forge->dropColumn('pkl_tasks', 'estimasi');
    }
}
