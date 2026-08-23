<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add rincian_tugas column to guru_piket table
 */
class AddRincianTugasToGuruPiket extends Migration
{
    public function up()
    {
        $fields = [
            'rincian_tugas' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'keterangan',
                'comment'    => 'Rincian tugas, kewajiban, peran, dan tanggung jawab guru piket',
            ],
        ];

        $this->forge->addColumn('guru_piket', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('guru_piket', 'rincian_tugas');
    }
}
