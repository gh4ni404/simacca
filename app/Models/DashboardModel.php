<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $table            = '';
    // protected $primaryKey       = 'id';
    // protected $useAutoIncrement = true;
    // protected $returnType       = 'array';
    // protected $useSoftDeletes   = false;
    // protected $protectFields    = true;
    // protected $allowedFields    = [];

    // protected bool $allowEmptyInserts = false;
    // protected bool $updateOnlyChanged = true;

    // protected array $casts = [];
    // protected array $castHandlers = [];

    // Dates
    // protected $useTimestamps = false;
    // protected $dateFormat    = 'datetime';
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    // Validation
    // protected $validationRules      = [];
    // protected $validationMessages   = [];
    // protected $skipValidation       = false;
    // protected $cleanValidationRules = true;

    // Callbacks
    // protected $allowCallbacks = true;
    // protected $beforeInsert   = [];
    // protected $afterInsert    = [];
    // protected $beforeUpdate   = [];
    // protected $afterUpdate    = [];
    // protected $beforeFind     = [];
    // protected $afterFind      = [];
    // protected $beforeDelete   = [];
    // protected $afterDelete    = [];

    /**
     * Get Dashboard stats for admin
     */
    public function getAdminStats()
    {
        $userModel = new UserModel();
        $guruModel = new GuruModel();
        $siswaModel = new SiswaModel();
        $kelasModel = new KelasModel();
        $absensiModel = new AbsensiModel();
        $mapelModel = new MataPelajaranModel();
        $izinModel = new IzinSiswaModel();
        $jadwalModel = new JadwalMengajarModel();

        $today = date('Y-m-d');
        $firstDayOfMonth = date('Y-m-01');
        $lastDayOfMonth = date('Y-m-t');

        $stats = [
            'total_guru' => $guruModel->countAll(),
            'total_siswa' => $siswaModel->where('tahun_ajaran', get_active_tahun_ajaran())->countAllResults(),
            'total_kelas' => $kelasModel->where('tahun_ajaran', get_active_tahun_ajaran())->countAllResults(),
            'total_mapel' => $mapelModel->countAll(),

            'absensi_hari_ini' => $absensiModel->where('tanggal', $today)->countAllResults(),
            'absensi_bulan_ini' => $absensiModel
                ->where('tanggal >=', $firstDayOfMonth)
                ->where('tanggal <=', $lastDayOfMonth)
                ->countAllResults(),

            'izin_pending' => $izinModel->where('status', 'pending')->countAllResults(),
            'jadwal_aktif' => $jadwalModel->countAll(),
            'user_by_role' => $userModel
                ->select('role,Count(*) as total')
                ->groupBy('role')
                ->findAll(),

        ];
        return $stats;
    }

    /**
     * Get dashboard stats for guru
     */
    public function getGuruStats($guruId)
    {
        $jadwalModel = new JadwalMengajarModel();
        $absensiModel = new AbsensiModel();

        $hariIndonesia = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
        ];

        $hariIni = $hariIndonesia[date('l')];

        $stats = [
            'jadwal_hari_ini'   => $jadwalModel->getByGuru($guruId, $hariIni, get_active_tahun_ajaran()),
            'total_jadwal'      => $jadwalModel->where('guru_id', $guruId)->countAllResults(),
            'total_absensi'     => $absensiModel->getByGuru($guruId, null, null, get_active_tahun_ajaran()),
        ];

        return $stats;
    }

    /**
     * Get Dashboard stats for wali kelas
     */
    public function getWaliKelasStats($guruId, $kelasId)
    {
        $siswaModel = new SiswaModel();
        $izinModel = new IzinSiswaModel();
        $absensiModel = new AbsensiModel();

        $stats = [
            'total_siswa'       => $siswaModel->where('kelas_id', $kelasId)->countAllResults(),
            'pending_izin'      => $izinModel->getPendingApproval($kelasId),
            'recent_absensi'    => $absensiModel->getByKelas($kelasId, null, null, get_active_tahun_ajaran()),
        ];

        return $stats;
    }

    /**
     * Get Dashboard stats for siswa
     */
    public function getSiswaStats($siswaId, $kelasId)
    {
        $absensiDetailModel = new AbsensiDetailModel();
        $izinModel = new IzinSiswaModel();

        $bulanIni = date('Y-m');
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');

        $stats = [
            'statistik_kehadiran'   => $absensiDetailModel->getStatistikSiswa($siswaId, $startDate, $endDate),
            'recent_absensi'        => $absensiDetailModel->getAbsensiSiswa($siswaId, null, 5),
            'recent_izin'           => $izinModel->getbySiswa($siswaId),
        ];

        return $stats;
    }

    /**
     * Get summary per kelas
     */
    public function getKelasSummary()
    {
        $kelasModel = new KelasModel();
        $absensiDetailModel = new AbsensiDetailModel();
        $siswaModel = new SiswaModel();

        $kelasList = $kelasModel->getAllKelas(get_active_tahun_ajaran());

        $summary = [];
        $today = date('Y-m-d');


        foreach ($kelasList as $kelas) {
            // Hitung Jumlah siswa per kelas
            // $totalSiswa = $siswaModel->where('kelas_id', $kelas['id'])->countAllResults();
            $totalSiswa = $siswaModel->getCountKelasById($kelas['id'], 'active', get_active_tahun_ajaran());

            // Hitung kehadiran hari ini
            $absensiHariIni = $absensiDetailModel->getAbsensiToday($today, $kelas['id']);

            // Hitung kehadiran bulan ini
            $firstDayOfMonth = date('Y-m-01');
            $lastDayOfMonth = date('Y-m-t');

            $absensiBulanIni = $absensiDetailModel->getAbsensiThisMonth($firstDayOfMonth, $lastDayOfMonth, $kelas['id']);

            $summary[] = [
                'id' => $kelas['id'],
                'nama_kelas' => $kelas['nama_kelas'],
                'wali_kelas' => $kelas['nama_wali_kelas'] ?? '-',
                'total_siswa' => $totalSiswa,
                'absensi_hari_ini' => $absensiHariIni,
                'absensi_bulan_ini' => $absensiBulanIni,
                'tingkat' => $kelas['tingkat'],
                'jurusan' => $kelas['jurusan']
            ];
        }
        return $summary;
    }

    public function getChartData()
    {
        $absensiDetailModel = new AbsensiDetailModel();
        $absensiModel = new AbsensiModel();
        $kelasModel = new KelasModel();

        // Data kehadiran Bulan Ini
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Query untuk statistik kehadiran bulan ini
        $attendanceStats = $absensiDetailModel->getStatistikBulanan($currentMonth, $currentYear);

        // Format data untuk pie chart
        $attendanceData = [
            'labels' => ['Hadir', 'Izin', 'Sakit', 'Alpa'],
            'data' => [0, 0, 0, 0],
            'colors' => ['#10B981', '#3B82F6', '#F59E0B', '#EF4444']
        ];

        foreach ($attendanceStats as $stat) {
            switch ($stat['status']) {
                case 'hadir':
                    $attendanceData['data'][0] = (int)$stat['total'];
                    break;
                case 'izin':
                    $attendanceData['data'][1] = (int)$stat['total'];
                    break;
                case 'sakit':
                    $attendanceData['data'][2] = (int)$stat['total'];
                    break;
                case 'alpa':
                    $attendanceData['data'][3] = (int)$stat['total'];
                    break;
            }
        }

        $last7Days = [];
        $attendanceLast7Days = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayname = date('D', strtotime($date));

            $count = $absensiModel->where('tanggal', $date)->countAllResults();

            $last7Days[] = $dayname;
            $attendanceLast7Days[] = $count;
        }

        // Data siswa per kelas
        $kelasWithSiswa = $kelasModel->getKelasWithJumlahSiswa(null, get_active_tahun_ajaran());

        $kelasLabels = [];
        $kelasData = [];

        foreach ($kelasWithSiswa as $kelas) {
            $kelasLabels[] = $kelas['nama_kelas'];
            $kelasData[] = (int)$kelas['jumlah_siswa'];
        }

        return [
            'attendancePie' => $attendanceData,
            'attendanceLine' => [
                'labels' => $last7Days,
                'data' => $attendanceLast7Days
            ],
            'kelasBar' => [
                'labels' => $kelasLabels,
                'data' => $kelasData
            ]
        ];
    }

   
}
