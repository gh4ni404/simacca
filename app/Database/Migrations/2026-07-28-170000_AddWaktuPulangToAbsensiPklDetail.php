<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWaktuPulangToAbsensiPklDetail extends Migration
{
    public function up()
    {
        $fields = [
            'waktu_pulang' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'waktu_absen'
            ]
        ];
        $this->forge->addColumn('absensi_pkl_detail', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('absensi_pkl_detail', 'waktu_pulang');
    }
}
