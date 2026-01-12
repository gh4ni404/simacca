<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username'          => 'admin',
                'password'          => password_hash('admin123', PASSWORD_DEFAULT),
                'role'              => 'admin',
                'email'             => 'admin@smkn8bone.sch.id',
                'is_active'         => true,
                'created_at'        => Time::now(),
            ],
            [
                'username'          => 'dirwan.jaya1',
                'password'          => password_hash('guru123', PASSWORD_DEFAULT),
                'role'              => 'guru_mapel',
                'email'             => 'guru@smkn8bone.sch.id',
                'is_active'         => true,
                'created_at'        => Time::now(),
            ],
            [
                'username'          => 'gani828',
                'password'          => password_hash('wali123', PASSWORD_DEFAULT),
                'role'              => 'wali_kelas',
                'email'             => 'wali@smkn8bone.sch.id',
                'is_active'         => true,
                'created_at'        => Time::now(),
            ],
            [
                'username'          => 'siswa1',
                'password'          => password_hash('siswa123', PASSWORD_DEFAULT),
                'role'              => 'siswa',
                'email'             => 'siswa@smkn8bone.sch.id',
                'is_active'         => true,
                'created_at'        => Time::now(),
            ]
        ];

        $this->db->table('users')->insertBatch($data);

        // Insert mata pelajaran first (required by foreign key)
        $mataPelajaranData = [
            [
                'nama_mapel'    => 'Matematika',
                'kode_mapel'    => 'MAT',
                'kategori'      => 'umum',
                'created_at'    => Time::now(),
            ],
            [
                'nama_mapel'    => 'Bahasa Indonesia',
                'kode_mapel'    => 'BIN',
                'kategori'      => 'umum',
                'created_at'    => Time::now(),
            ],
        ];
        $this->db->table('mata_pelajaran')->insertBatch($mataPelajaranData);

        // Insert kelas (required by guru and siswa)
        $kelasData = [
            [
                'nama_kelas'    => 'X RPL 1',
                'tingkat'       => '10',
                'jurusan'       => 'RPL',
                'wali_kelas_id' => null,
            ],
        ];
        $this->db->table('kelas')->insertBatch($kelasData);

        $guruData = [
            [
                'user_id'               => 2,
                'nip'                   => '123456789',
                'nama_lengkap'          => 'Dirwan Jaya, S.Pd., M.Pd., Gr.',
                'jenis_kelamin'         => 'L',
                'mata_pelajaran_id'     => 1,
                'is_wali_kelas'         => false,
                'kelas_id'              => null,
                'created_at'            => Time::now(),
            ],
            [
                'user_id'               => 3,
                'nip'                   => '987654321',
                'nama_lengkap'          => 'Mohd. Abdul Ghani, S.Kom',
                'jenis_kelamin'         => 'L',
                'mata_pelajaran_id'     => 2,
                'is_wali_kelas'         => true,
                'kelas_id'              => 1,
                'created_at'            => Time::now(),
            ]
        ];

        $this->db->table('guru')->insertBatch($guruData);

        $siswaData = [
            [
                'user_id'               => 4,
                'nis'                   => '158820',
                'nama_lengkap'          => 'Musfika Ilyas',
                'jenis_kelamin'         => 'P',
                'kelas_id'              => 1,
                'tahun_ajaran'          => '2025/2026',
                'created_at'            => Time::now(),
            ]
        ];

        $this->db->table('siswa')->insertBatch($siswaData);
    }
}
