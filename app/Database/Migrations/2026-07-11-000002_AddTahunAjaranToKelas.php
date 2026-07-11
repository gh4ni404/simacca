<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTahunAjaranToKelas extends Migration
{
    public function up()
    {
        // Add tahun_ajaran column (nullable initially for existing data)
        $this->forge->addColumn('kelas', [
            'tahun_ajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => '9',
                'null'       => true,
                'after'      => 'jurusan',
            ],
        ]);

        // Seed existing kelas with current active tahun_ajaran from settings
        $db = \Config\Database::connect();
        $setting = $db->table('settings')->where('key', 'tahun_ajaran_aktif')->get()->getRowArray();
        $activeYear = $setting['value'] ?? (date('Y') - 1) . '/' . date('Y');

        $db->table('kelas')->update(['tahun_ajaran' => $activeYear]);

        // Make tahun_ajaran NOT NULL after seeding
        $this->forge->modifyColumn('kelas', [
            'tahun_ajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => '9',
                'null'       => false,
            ],
        ]);

        // Drop old unique key on nama_kelas, add composite unique (nama_kelas + tahun_ajaran)
        $db->query('ALTER TABLE `kelas` DROP INDEX `nama_kelas`');
        $db->query('ALTER TABLE `kelas` ADD UNIQUE KEY `uk_nama_kelas_tahun` (`nama_kelas`, `tahun_ajaran`)');
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Drop composite unique key
        $db->query('ALTER TABLE `kelas` DROP INDEX `uk_nama_kelas_tahun`');

        // Restore old unique key on nama_kelas
        $db->query('ALTER TABLE `kelas` ADD UNIQUE KEY `nama_kelas` (`nama_kelas`)');

        // Drop tahun_ajaran column
        $this->forge->dropColumn('kelas', 'tahun_ajaran');
    }
}
