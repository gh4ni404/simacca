<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixStatusColumnLengthPklProgress extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('pkl_progress', [
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'draft'],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('pkl_progress', [
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'],
        ]);
    }
}
