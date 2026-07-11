<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteToAllTables extends Migration
{
    protected $tables = [
        'users',
        'guru',
        'siswa',
        'kelas',
        'mata_pelajaran',
        'absensi',
        'absensi_detail',
        'absensi_guru',
        'jadwal_mengajar',
        'jurnal_kbm',
        'jurnal_pkl',
        'izin_siswa',
        'izin_guru',
        'tempat_pkl',
        'siswa_pkl',
        'pembimbing_pkl',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            $this->forge->addColumn($table, [
                'deleted_at' => [
                    'type'   => 'DATETIME',
                    'null'   => true,
                    'after'  => 'created_at',
                ],
            ]);
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            $this->forge->dropColumn($table, 'deleted_at');
        }
    }
}
