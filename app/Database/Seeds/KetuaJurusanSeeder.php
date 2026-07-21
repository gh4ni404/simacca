<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class KetuaJurusanSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Create 3 ketua jurusan users
        $users = [
            [
                'username'   => 'ketuajurusan_dkv',
                'password'   => password_hash('ketua123', PASSWORD_DEFAULT),
                'role'       => 'ketua_jurusan',
                'email'      => 'ketuajurusan_dkv@smkn8bone.sch.id',
                'is_active'  => true,
                'created_at' => Time::now()->toDateTimeString(),
            ],
            [
                'username'   => 'ketuajurusan_mplb',
                'password'   => password_hash('ketua123', PASSWORD_DEFAULT),
                'role'       => 'ketua_jurusan',
                'email'      => 'ketuajurusan_mplb@smkn8bone.sch.id',
                'is_active'  => true,
                'created_at' => Time::now()->toDateTimeString(),
            ],
            [
                'username'   => 'ketuajurusan_at',
                'password'   => password_hash('ketua123', PASSWORD_DEFAULT),
                'role'       => 'ketua_jurusan',
                'email'      => 'ketuajurusan_at@smkn8bone.sch.id',
                'is_active'  => true,
                'created_at' => Time::now()->toDateTimeString(),
            ],
        ];

        $db->table('users')->insertBatch($users);
        $lastUserId = $db->insertID();

        // 2. Insert into user_roles table
        $userRoles = [];
        for ($i = 0; $i < 3; $i++) {
            $userRoles[] = [
                'user_id'    => $lastUserId - 2 + $i,
                'role'       => 'ketua_jurusan',
                'created_at' => Time::now()->toDateTimeString(),
            ];
            // Also add guru_mapel role
            $userRoles[] = [
                'user_id'    => $lastUserId - 2 + $i,
                'role'       => 'guru_mapel',
                'created_at' => Time::now()->toDateTimeString(),
            ];
        }
        $db->table('user_roles')->insertBatch($userRoles);

        // 3. Insert guru records
        $guruData = [
            [
                'user_id'          => $lastUserId - 2,
                'nip'              => '198501012010011001',
                'nama_lengkap'     => 'Ir. Ahmad Ridwan, M.Kom',
                'jenis_kelamin'    => 'L',
                'mata_pelajaran_id' => null,
                'is_wali_kelas'    => false,
                'kelas_id'         => null,
                'jurusan'          => 'DKV',
                'is_ketua_jurusan' => true,
                'created_at'       => Time::now()->toDateTimeString(),
            ],
            [
                'user_id'          => $lastUserId - 1,
                'nip'              => '198601012010011002',
                'nama_lengkap'     => 'Siti Nurhaliza, S.Pd',
                'jenis_kelamin'    => 'P',
                'mata_pelajaran_id' => null,
                'is_wali_kelas'    => false,
                'kelas_id'         => null,
                'jurusan'          => 'MPLB',
                'is_ketua_jurusan' => true,
                'created_at'       => Time::now()->toDateTimeString(),
            ],
            [
                'user_id'          => $lastUserId,
                'nip'              => '198701012010011003',
                'nama_lengkap'     => 'Bambang Sutrisno, S.T',
                'jenis_kelamin'    => 'L',
                'mata_pelajaran_id' => null,
                'is_wali_kelas'    => false,
                'kelas_id'         => null,
                'jurusan'          => 'AT',
                'is_ketua_jurusan' => true,
                'created_at'       => Time::now()->toDateTimeString(),
            ],
        ];

        $db->table('guru')->insertBatch($guruData);

        // 4. Insert corresponding guru_mapel roles in user_roles (already done above)
        // Also ensure ketua_jurusan role exists in roles table
        $existingRole = $db->table('roles')->where('name', 'ketua_jurusan')->get()->getRowArray();
        if (!$existingRole) {
            $db->table('roles')->insert([
                'name'         => 'ketua_jurusan',
                'display_name' => 'Ketua Jurusan',
                'description'  => 'Memantau dan memonitoring kegiatan siswa PKL berdasarkan jurusan',
                'is_active'    => true,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        echo "=== Ketua Jurusan Seeder Complete ===" . PHP_EOL;
        echo "Akun yang dibuat:" . PHP_EOL;
        echo "1. ketuajurusan_dkv / ketua123 (Jurusan DKV)" . PHP_EOL;
        echo "2. ketuajurusan_mplb / ketua123 (Jurusan MPLB)" . PHP_EOL;
        echo "3. ketuajurusan_at / ketua123 (Jurusan AT)" . PHP_EOL;
    }
}
