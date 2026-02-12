<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add Keterangan Fields to Absensi Guru Table
 * 
 * Adds separate fields for check-in and check-out notes (keterangan)
 * to provide better tracking of reasons for attendance actions.
 * 
 * New Fields:
 *   - keterangan_masuk: Notes/remarks when checking in
 *   - keterangan_keluar: Notes/remarks when checking out
 * 
 * @package App\Database\Migrations
 * @author SIMACCA Team
 * @version 2.1.0
 */
class AddKeteranganFieldsToAbsensiGuru extends Migration
{
    public function up()
    {
        $fields = [
            'keterangan_masuk' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Notes or remarks for check-in',
                'after'      => 'foto_check_in'
            ],
            'keterangan_keluar' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Notes or remarks for check-out',
                'after'      => 'foto_check_out'
            ],
        ];

        $this->forge->addColumn('absensi_guru', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('absensi_guru', ['keterangan_masuk', 'keterangan_keluar']);
    }
}
