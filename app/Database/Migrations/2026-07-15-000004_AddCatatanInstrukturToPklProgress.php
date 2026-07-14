<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCatatanInstrukturToPklProgress extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pkl_progress', [
            'catatan_instruktur' => [
                'type'   => 'TEXT',
                'null'   => true,
                'after'  => 'catatan_pembimbing',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pkl_progress', 'catatan_instruktur');
    }
}
