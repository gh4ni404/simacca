<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'display_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type'    => 'BOOLEAN',
                'default' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('roles');

        // Seed existing roles
        $data = [
            ['name' => 'admin',       'display_name' => 'Administrator',          'description' => 'Akses penuh ke seluruh sistem', 'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'guru_mapel',  'display_name' => 'Guru Mata Pelajaran',    'description' => 'Guru pengajar mata pelajaran',  'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'wali_kelas',  'display_name' => 'Wali Kelas',             'description' => 'Guru yang mengelola kelas',      'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'wakakur',     'display_name' => 'Wakil Kepala Kurikulum', 'description' => 'Wakil kepala bidang kurikulum',  'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'siswa',       'display_name' => 'Siswa',                  'description' => 'Siswa sekolah',                  'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'instruktur',  'display_name' => 'Instruktur PKL',         'description' => 'Instruktur dari tempat PKL',     'is_active' => true, 'created_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('roles')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('roles');
    }
}
