<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPembimbingPklIdToSiswaPkl extends Migration
{
    public function up()
    {
        // 1. Tambah kolom pembimbing_pkl_id ke tabel siswa_pkl
        $this->forge->addColumn('siswa_pkl', [
            'pembimbing_pkl_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'tempat_pkl_id',
            ],
        ]);

        // 2. Tambah foreign key ke pembimbing_pkl
        $this->db->query('
            ALTER TABLE siswa_pkl
            ADD CONSTRAINT fk_siswa_pkl_pembimbing
            FOREIGN KEY (pembimbing_pkl_id)
            REFERENCES pembimbing_pkl(id)
            ON DELETE SET NULL
            ON UPDATE CASCADE
        ');

        // 3. Isi data pembimbing_pkl_id yang sudah ada berdasarkan tempat_pkl_id dan tahun_ajaran
        // (Jika ada beberapa pembimbing, default ke salah satunya yang aktif)
        $this->db->query('
            UPDATE siswa_pkl sp
            INNER JOIN pembimbing_pkl pp
                ON pp.tempat_pkl_id = sp.tempat_pkl_id
                AND pp.tahun_ajaran = sp.tahun_ajaran
                AND pp.deleted_at IS NULL
            SET sp.pembimbing_pkl_id = pp.id
            WHERE sp.pembimbing_pkl_id IS NULL
              AND sp.deleted_at IS NULL
        ');
    }

    public function down()
    {
        // Hapus foreign key terlebih dahulu
        $this->db->query('
            ALTER TABLE siswa_pkl
            DROP FOREIGN KEY fk_siswa_pkl_pembimbing
        ');

        $this->forge->dropColumn('siswa_pkl', 'pembimbing_pkl_id');
    }
}
