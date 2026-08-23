<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create Master Jobdesk Piket Table
 *
 * Creates master duty task templates for teacher duty assignments (guru_piket).
 */
class CreateMasterJobdeskPiketTable extends Migration
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
            'kode_jobdesk' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'nama_jobdesk' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'rincian_tugas' => [
                'type' => 'TEXT',
            ],
            'is_active' => [
                'type'    => 'BOOLEAN',
                'default' => 1,
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
        $this->forge->addUniqueKey('kode_jobdesk');
        $this->forge->addKey('is_active');
        $this->forge->createTable('master_jobdesk_piket');

        // Seed default master jobdesks
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $defaults = [
            [
                'kode_jobdesk'  => 'JOB-GERBANG',
                'nama_jobdesk'  => 'Piket Gerbang & Kedisiplinan Siswa',
                'rincian_tugas' => "1. Hadir di gerbang sekolah 15 menit sebelum bel masuk.\n2. Menyambut kedatangan siswa dan memantau kerapian seragam.\n3. Mencatat siswa terlambat & menyerahkan ke Guru BK/Kesiswaan.\n4. Memantau ketertiban lingkungan gerbang dan akses keluar masuk.",
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'kode_jobdesk'  => 'JOB-SHALAT',
                'nama_jobdesk'  => 'Piket Portal Presensi Shalat Berjamaah',
                'rincian_tugas' => "1. Membuka & mengelola Portal Presensi Shalat Berjamaah (Dzuhur/Ashar/Jumat).\n2. Melakukan pengawasan dan pencatatan presensi siswa saat ibadah shalat.\n3. Mencatat siswa yang berhalangan (uzur/sakit).\n4. Memastikan ketertiban barisan shalat di masjid/musholla.",
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'kode_jobdesk'  => 'JOB-K7',
                'nama_jobdesk'  => 'Piket Ketertiban & K7 Lingkungan',
                'rincian_tugas' => "1. Memantau Kebersihan, Keamanan, Ketertiban, Keindahan, Kekeluargaan, Kerindangan, dan Kesehatan (K7).\n2. Memeriksa kebersihan koridor, kelas, dan area fasilitas umum sekolah.\n3. Menangani izin keluar kelas / izin meninggalkan area sekolah saat KBM.\n4. Mengisi Jurnal Piket Harian.",
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        $db->table('master_jobdesk_piket')->insertBatch($defaults);
    }

    public function down()
    {
        $this->forge->dropTable('master_jobdesk_piket');
    }
}
