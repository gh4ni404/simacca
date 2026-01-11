<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTimestampsToIzinSiswa extends Migration
{
    public function up()
    {
        $fields = [
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];
        
        $this->forge->addColumn('izin_siswa', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('izin_siswa', ['created_at', 'updated_at']);
    }
}
