<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAbsensiShalatAddGuru extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Modify siswa_id to be nullable
        $fields = [
            'siswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ];
        $this->forge->modifyColumn('absensi_shalat', $fields);

        // 2. Add guru_id and user_type columns
        $newFields = [
            'guru_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'siswa_id',
            ],
            'user_type' => [
                'type'       => 'ENUM',
                'constraint' => ['siswa', 'guru'],
                'default'    => 'siswa',
                'null'       => false,
                'after'      => 'guru_id',
            ],
        ];
        $this->forge->addColumn('absensi_shalat', $newFields);

        // 3. Update existing records to ensure user_type is 'siswa' when siswa_id is set
        $db->query("UPDATE `absensi_shalat` SET `user_type` = 'siswa' WHERE `siswa_id` IS NOT NULL");

        // 4. Add foreign key for guru_id
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE', 'fk_absensi_shalat_guru');

        // 5. Add unique key for (prayer_session_id, guru_id) and key for guru_id
        $db->query("ALTER TABLE `absensi_shalat` ADD UNIQUE KEY `uniq_session_guru` (`prayer_session_id`, `guru_id`)");
        $db->query("ALTER TABLE `absensi_shalat` ADD KEY `idx_guru_id` (`guru_id`)");
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Drop foreign key and keys
        $db->query("ALTER TABLE `absensi_shalat` DROP FOREIGN KEY `fk_absensi_shalat_guru`");
        $db->query("ALTER TABLE `absensi_shalat` DROP INDEX `uniq_session_guru`");
        $db->query("ALTER TABLE `absensi_shalat` DROP INDEX `idx_guru_id`");

        // Remove columns
        $this->forge->dropColumn('absensi_shalat', ['guru_id', 'user_type']);

        // Revert siswa_id to not null
        $fields = [
            'siswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ];
        $this->forge->modifyColumn('absensi_shalat', $fields);
    }
}
