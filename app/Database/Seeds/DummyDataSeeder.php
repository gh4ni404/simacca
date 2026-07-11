<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $mataPelajaran = [
            [
                'kode_mapel'                => 'MTK',
                'nama_mapel'                => 'Matematika',
                'kategori'                  => 'Umum',
                'created_at'                => Time::now(),
            ],
            [
                'kode_mapel'                => 'BIN',
                'nama_mapel'                => 'Bahasa Indonesia',
                'kategori'                  => 'Umum',
                'created_at'                => Time::now(),
            ],
            [
                'kode_mapel'                => 'BING',
                'nama_mapel'                => 'Bahasa Inggris',
                'kategori'                  => 'Umum',
                'created_at'                => Time::now(),
            ],
            [
                'kode_mapel'                => 'PKN',
                'nama_mapel'                => 'Pendidikan Kewarganegaraan',
                'kategori'                  => 'Umum',
                'created_at'                => Time::now(),
            ],
            [
                'kode_mapel'                => 'PDD',
                'nama_mapel'                => 'Pemrograman Dasar',
                'kategori'                  => 'Kejuruan',
                'created_at'                => Time::now(),
            ],
        ];

        $this->db->table('mata_pelajaran')->insertBatch($mataPelajaran);

        $kelas = [
            [
                'nama_kelas'                => 'X-RPL',
                'tingkat'                   => '10',
                'jurusan'                   => 'Rekayasa Perangkat Lunak',
                'tahun_ajaran'              => get_active_tahun_ajaran(),
                'wali_kelas_id'             => 2,
            ],
            [
                'nama_kelas'                => 'XI-RPL',
                'tingkat'                   => '11',
                'jurusan'                   => 'Rekayasa Perangkat Lunak',
                'tahun_ajaran'              => get_active_tahun_ajaran(),
                'wali_kelas_id'             => null,
            ],
            [
                'nama_kelas'                => 'XII-RPL',
                'tingkat'                   => '12',
                'jurusan'                   => 'Rekayasa Perangkat Lunak',
                'tahun_ajaran'              => get_active_tahun_ajaran(),
                'wali_kelas_id'             => null,
            ],
            [
                'nama_kelas'                => 'X-TKJ',
                'tingkat'                   => '10',
                'jurusan'                   => 'Teknik Komputer Jaringan',
                'tahun_ajaran'              => get_active_tahun_ajaran(),
                'wali_kelas_id'             => null,
            ],
            [
                'nama_kelas'                => 'XI-TKJ',
                'tingkat'                   => '11',
                'jurusan'                   => 'Teknik Komputer Jaringan',
                'tahun_ajaran'              => get_active_tahun_ajaran(),
                'wali_kelas_id'             => null,
            ],
        ];

        $this->db->table('kelas')->insertBatch($kelas);
    }
}
