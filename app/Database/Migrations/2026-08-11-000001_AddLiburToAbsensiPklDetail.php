<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLiburToAbsensiPklDetail extends Migration
{
    public function up()
    {
        // 1. Tambah 'libur' ke ENUM status absensi_pkl_detail
        $this->db->query("
            ALTER TABLE absensi_pkl_detail
            MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'alpa', 'libur') NOT NULL DEFAULT 'alpa'
        ");

        // 2. Buat tabel hari_libur untuk kalender libur nasional/bersama (Opsi B)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('tanggal');
        $this->forge->createTable('hari_libur');
    }

    public function down()
    {
        // Kembalikan ENUM tanpa 'libur' (pindahkan data libur ke alpa dulu)
        $this->db->query("
            UPDATE absensi_pkl_detail SET status = 'alpa' WHERE status = 'libur'
        ");
        $this->db->query("
            ALTER TABLE absensi_pkl_detail
            MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'alpa') NOT NULL DEFAULT 'alpa'
        ");

        $this->forge->dropTable('hari_libur');
    }
}
