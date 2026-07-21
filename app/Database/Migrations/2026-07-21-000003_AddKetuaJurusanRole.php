<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKetuaJurusanRole extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Insert ketua_jurusan role if not exists
        $existing = $db->table('roles')->where('name', 'ketua_jurusan')->get()->getRowArray();

        if (!$existing) {
            $db->table('roles')->insert([
                'name'         => 'ketua_jurusan',
                'display_name' => 'Ketua Jurusan',
                'description'  => 'Memantau dan memonitoring kegiatan siswa PKL berdasarkan jurusan',
                'is_active'    => true,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->table('roles')->where('name', 'ketua_jurusan')->delete();
    }
}
