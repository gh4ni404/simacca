<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixStatusColumnLengthPklTasks extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('pkl_tasks', [
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'active'],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('pkl_tasks', [
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
        ]);
    }
}
