<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDispenFromAbsensiPklDetail extends Migration
{
    public function up()
    {
        // Hapus nilai 'dispen' dari enum status tabel absensi_pkl_detail.
        // Sebelum mengubah kolom, pastikan tidak ada data dengan status 'dispen'
        // (jika ada, update dulu ke 'alpa' sebagai nilai default).
        $this->db->query("
            UPDATE absensi_pkl_detail
            SET status = 'alpa'
            WHERE status = 'dispen'
        ");

        // Ubah kolom status: hapus 'dispen' dari daftar enum
        $this->db->query("
            ALTER TABLE absensi_pkl_detail
            MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'alpa') NOT NULL DEFAULT 'alpa'
        ");
    }

    public function down()
    {
        // Kembalikan 'dispen' ke dalam enum status
        $this->db->query("
            ALTER TABLE absensi_pkl_detail
            MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'alpa', 'dispen') NOT NULL DEFAULT 'alpa'
        ");
    }
}
