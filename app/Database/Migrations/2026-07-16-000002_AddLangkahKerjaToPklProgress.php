<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLangkahKerjaToPklProgress extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pkl_progress', [
            'langkah_kerja' => ['type' => 'TEXT', 'null' => true, 'after' => 'deskripsi'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pkl_progress', 'langkah_kerja');
    }
}
