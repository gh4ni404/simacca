<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLangkahKerjaToTasks extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pkl_tasks', [
            'langkah_kerja' => ['type' => 'TEXT', 'null' => true, 'after' => 'estimasi'],
        ]);

        $this->forge->addColumn('pkl_task_templates', [
            'langkah_kerja' => ['type' => 'TEXT', 'null' => true, 'after' => 'estimasi'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pkl_tasks', 'langkah_kerja');
        $this->forge->dropColumn('pkl_task_templates', 'langkah_kerja');
    }
}
