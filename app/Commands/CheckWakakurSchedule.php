<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckWakakurSchedule extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Maintenance';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'wakakur:check-schedule';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Check if Wakakur users have teaching schedules';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'wakakur:check-schedule';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('Checking Wakakur teaching schedule...', 'yellow');
        CLI::newLine();
        
        $query = $db->query("
            SELECT 
                u.id as user_id,
                u.username, 
                u.role, 
                g.id as guru_id,
                g.nama_lengkap,
                COUNT(jm.id) as jumlah_jadwal
            FROM users u 
            LEFT JOIN guru g ON u.id = g.user_id 
            LEFT JOIN jadwal_mengajar jm ON g.id = jm.guru_id 
            WHERE u.role = 'wakakur' 
            GROUP BY u.id, g.id
        ");
        
        $result = $query->getResultArray();
        
        if (empty($result)) {
            CLI::error('No Wakakur users found.');
            return;
        }
        
        foreach ($result as $row) {
            CLI::write('User ID: ' . $row['user_id']);
            CLI::write('Username: ' . $row['username']);
            CLI::write('Role: ' . $row['role']);
            CLI::write('Guru ID: ' . ($row['guru_id'] ?: 'NULL'));
            CLI::write('Nama Lengkap: ' . ($row['nama_lengkap'] ?: 'NULL'));
            CLI::write('Jumlah Jadwal Mengajar: ' . $row['jumlah_jadwal']);
            
            if ($row['jumlah_jadwal'] > 0) {
                CLI::newLine();
                CLI::write('✅ Wakakur ini MENGAJAR dan perlu akses ke route Guru!', 'green');
                
                // Get jadwal details
                $jadwalQuery = $db->query("
                    SELECT 
                        jm.id,
                        k.nama_kelas,
                        mp.nama_mapel,
                        jm.hari,
                        jm.jam_mulai,
                        jm.jam_selesai
                    FROM jadwal_mengajar jm
                    JOIN kelas k ON jm.kelas_id = k.id
                    JOIN mata_pelajaran mp ON jm.mata_pelajaran_id = mp.id
                    WHERE jm.guru_id = {$row['guru_id']}
                    ORDER BY jm.hari, jm.jam_mulai
                ");
                
                $jadwals = $jadwalQuery->getResultArray();
                
                CLI::newLine();
                CLI::write('Jadwal Mengajar:', 'cyan');
                CLI::write(str_repeat('-', 80));
                
                foreach ($jadwals as $jadwal) {
                    CLI::write("  {$jadwal['hari']} | {$jadwal['jam_mulai']}-{$jadwal['jam_selesai']} | {$jadwal['nama_kelas']} | {$jadwal['nama_mapel']}");
                }
            } else {
                CLI::newLine();
                CLI::write('❌ Wakakur ini TIDAK MENGAJAR, hanya tugas administratif.', 'red');
            }
            
            CLI::write(str_repeat('=', 80));
            CLI::newLine();
        }
        
        CLI::write('Done!', 'green');
    }
}
