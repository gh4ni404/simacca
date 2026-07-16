<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInstrukturVerificationToPklProgress extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pkl_progress', [
            'instruktur_verified_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'verified_at'],
            'instruktur_verified_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'instruktur_verified_by'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pkl_progress', ['instruktur_verified_by', 'instruktur_verified_at']);
    }
}
