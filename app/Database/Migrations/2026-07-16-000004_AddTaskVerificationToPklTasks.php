<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTaskVerificationToPklTasks extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pkl_tasks', [
            'instruktur_verified_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'langkah_kerja'],
            'instruktur_verified_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'instruktur_verified_by'],
            'pembimbing_verified_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'instruktur_verified_at'],
            'pembimbing_verified_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'pembimbing_verified_by'],
        ]);

        $this->forge->addKey('instruktur_verified_by');
        $this->forge->addKey('pembimbing_verified_by');
    }

    public function down()
    {
        $this->forge->dropColumn('pkl_tasks', [
            'instruktur_verified_by',
            'instruktur_verified_at',
            'pembimbing_verified_by',
            'pembimbing_verified_at',
        ]);
    }
}
