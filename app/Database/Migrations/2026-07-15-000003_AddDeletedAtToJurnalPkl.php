<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToJurnalPkl extends Migration
{
    public function up()
    {
        $this->forge->addColumn('jurnal_pkl', [
            'deleted_at' => [
                'type'   => 'DATETIME',
                'null'   => true,
                'after'  => 'created_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('jurnal_pkl', 'deleted_at');
    }
}
