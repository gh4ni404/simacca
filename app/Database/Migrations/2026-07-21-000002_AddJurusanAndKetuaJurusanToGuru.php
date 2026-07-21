<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJurusanAndKetuaJurusanToGuru extends Migration
{
    public function up()
    {
        $this->forge->addColumn('guru', [
            'jurusan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'kelas_id',
            ],
            'is_ketua_jurusan' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
                'after'   => 'jurusan',
            ],
        ]);

        $this->forge->addKey('jurusan');
        $this->db->query('ALTER TABLE guru ADD INDEX idx_is_ketua_jurusan (is_ketua_jurusan)');
    }

    public function down()
    {
        $this->forge->dropColumn('guru', ['jurusan', 'is_ketua_jurusan']);
    }
}
