<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSessionExpiresAtToPrayerSessions extends Migration
{
    public function up()
    {
        $fields = [
            'session_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'expires_at',
            ],
        ];
        $this->forge->addColumn('prayer_sessions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('prayer_sessions', 'session_expires_at');
    }
}
