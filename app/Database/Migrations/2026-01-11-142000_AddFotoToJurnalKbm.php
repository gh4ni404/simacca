<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoToJurnalKbm extends Migration
{
    public function up()
    {
        // Add foto_dokumentasi column to jurnal_kbm table
        $fields = [
            'foto_dokumentasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'catatan_khusus'
            ]
        ];
        
        $this->forge->addColumn('jurnal_kbm', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('jurnal_kbm', 'foto_dokumentasi');
    }
}
