<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add Jobdesk ID to Guru Piket Table
 *
 * Adds jobdesk_id column and foreign key reference to master_jobdesk_piket table.
 */
class AddJobdeskIdToGuruPiketTable extends Migration
{
    public function up()
    {
        $fields = [
            'jobdesk_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'guru_id',
            ],
        ];

        $this->forge->addColumn('guru_piket', $fields);
        $this->db->query("ALTER TABLE `guru_piket` ADD INDEX `jobdesk_id` (`jobdesk_id`)");
        $this->db->query("ALTER TABLE `guru_piket` ADD CONSTRAINT `fk_guru_piket_jobdesk_id` FOREIGN KEY (`jobdesk_id`) REFERENCES `master_jobdesk_piket` (`id`) ON DELETE SET NULL ON UPDATE CASCADE");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `guru_piket` DROP FOREIGN KEY `fk_guru_piket_jobdesk_id`");
        $this->db->query("ALTER TABLE `guru_piket` DROP INDEX `jobdesk_id`");
        $this->forge->dropColumn('guru_piket', 'jobdesk_id');
    }
}
