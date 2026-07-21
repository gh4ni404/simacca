<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeCatatanRequiredOnPklProgress extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $db->query("UPDATE pkl_progress SET catatan_pembimbing = '' WHERE catatan_pembimbing IS NULL");
        $db->query("UPDATE pkl_progress SET catatan_instruktur = '' WHERE catatan_instruktur IS NULL");

        $this->forge->modifyColumn('pkl_progress', [
            'catatan_pembimbing' => [
                'type' => 'TEXT',
                'null' => false,
                'default' => '',
            ],
            'catatan_instruktur' => [
                'type' => 'TEXT',
                'null' => false,
                'default' => '',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('pkl_progress', [
            'catatan_pembimbing' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'catatan_instruktur' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }
}
