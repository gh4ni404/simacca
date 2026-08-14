<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesToAbsensiPkl extends Migration
{
    public function up()
    {
        // Index untuk query WHERE pembimbing_pkl_id (admin index page, rekap page)
        $this->db->query('ALTER TABLE `absensi_pkl` ADD INDEX `idx_pembimbing_pkl_id` (`pembimbing_pkl_id`)');

        // Index untuk query WHERE deleted_at (soft delete filter)
        $this->db->query('ALTER TABLE `absensi_pkl` ADD INDEX `idx_deleted_at` (`deleted_at`)');

        // Index untuk query WHERE tanggal (date filter)
        $this->db->query('ALTER TABLE `absensi_pkl` ADD INDEX `idx_tanggal` (`tanggal`)');

        // Index untuk query WHERE absensi_pkl_id (JOIN dan WHERE di detail)
        $this->db->query('ALTER TABLE `absensi_pkl_detail` ADD INDEX `idx_absensi_pkl_id` (`absensi_pkl_id`)');

        // Index untuk query WHERE status (filter hadir/izin/sakit/alpa)
        $this->db->query('ALTER TABLE `absensi_pkl_detail` ADD INDEX `idx_status` (`status`)');

        // Composite index untuk batch stats query
        $this->db->query('ALTER TABLE `absensi_pkl_detail` ADD INDEX `idx_absensi_status` (`absensi_pkl_id`, `status`)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `absensi_pkl` DROP INDEX `idx_pembimbing_pkl_id`');
        $this->db->query('ALTER TABLE `absensi_pkl` DROP INDEX `idx_deleted_at`');
        $this->db->query('ALTER TABLE `absensi_pkl` DROP INDEX `idx_tanggal`');
        $this->db->query('ALTER TABLE `absensi_pkl_detail` DROP INDEX `idx_absensi_pkl_id`');
        $this->db->query('ALTER TABLE `absensi_pkl_detail` DROP INDEX `idx_status`');
        $this->db->query('ALTER TABLE `absensi_pkl_detail` DROP INDEX `idx_absensi_status`');
    }
}
