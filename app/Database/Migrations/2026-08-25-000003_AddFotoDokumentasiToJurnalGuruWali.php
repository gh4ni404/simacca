<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoDokumentasiToJurnalGuruWali extends Migration
{
    public function up()
    {
        $fields = [
            'foto_dokumentasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'tindak_lanjut',
            ],
        ];

        if ($this->db->tableExists('jurnal_guru_wali') && !$this->db->fieldExists('foto_dokumentasi', 'jurnal_guru_wali')) {
            $this->forge->addColumn('jurnal_guru_wali', $fields);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('jurnal_guru_wali') && $this->db->fieldExists('foto_dokumentasi', 'jurnal_guru_wali')) {
            $this->forge->dropColumn('jurnal_guru_wali', 'foto_dokumentasi');
        }
    }
}
