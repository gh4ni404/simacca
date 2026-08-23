<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNamaSesiToPrayerSessions extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        if (!$db->fieldExists('nama_sesi', 'prayer_sessions')) {
            $fields = [
                'nama_sesi' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'default'    => 'Shalat Dzuhur',
                    'after'      => 'guru_piket_id',
                ],
            ];
            $this->forge->addColumn('prayer_sessions', $fields);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        if ($db->fieldExists('nama_sesi', 'prayer_sessions')) {
            $this->forge->dropColumn('prayer_sessions', 'nama_sesi');
        }
    }
}
