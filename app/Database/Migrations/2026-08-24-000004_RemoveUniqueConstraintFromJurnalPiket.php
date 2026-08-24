<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Remove Unique Constraint From Jurnal Piket
 *
 * Removes unique restriction on (guru_id, tanggal) to allow teachers
 * to input multiple journal entries or duty reports on the same date without restriction.
 */
class RemoveUniqueConstraintFromJurnalPiket extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        try {
            // Find all unique indexes on jurnal_piket other than PRIMARY
            $indexes = $db->query("SHOW INDEX FROM `jurnal_piket` WHERE Non_unique = 0 AND Key_name != 'PRIMARY'")->getResultArray();
            $droppedKeys = [];

            foreach ($indexes as $index) {
                $keyName = $index['Key_name'];
                if (!in_array($keyName, $droppedKeys)) {
                    $db->query("ALTER TABLE `jurnal_piket` DROP INDEX `{$keyName}`");
                    $droppedKeys[] = $keyName;
                }
            }
        } catch (\Throwable $e) {
            // Ignore if index doesn't exist or table is SQLite/different driver
            log_message('info', 'Drop unique index from jurnal_piket: ' . $e->getMessage());
        }
    }

    public function down()
    {
        try {
            $db = \Config\Database::connect();
            $db->query("ALTER TABLE `jurnal_piket` ADD UNIQUE KEY `guru_id_tanggal` (`guru_id`, `tanggal`)");
        } catch (\Throwable $e) {
            log_message('info', 'Add unique index to jurnal_piket: ' . $e->getMessage());
        }
    }
}
