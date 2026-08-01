<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\GuruModel;
use App\Models\JadwalMengajarModel;
use App\Models\SiswaModel;
use App\Models\MataPelajaranModel;

class WeeklyReportController extends BaseController
{
    protected $absensiModel;
    protected $absensiDetailModel;
    protected $guruModel;
    protected $jadwalModel;
    protected $siswaModel;
    protected $mapelModel;

    public function __construct()
    {
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->guruModel = new GuruModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->siswaModel = new SiswaModel();
        $this->mapelModel = new MataPelajaranModel();
    }

    public function index()
    {
        $userId = session()->get('user_id') ?? session()->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        // Get all roles for this user to check multi-role
        $allRoles = session()->get('all_roles', []);
        $isGuruMapel = in_array('guru_mapel', $allRoles);
        $isWakakur = in_array('wakakur', $allRoles);

        // Get filter parameters
        $weekStart = $this->request->getGet('week_start');
        $mapelId = $this->request->getGet('mapel_id');

        // Default to current week (Monday to Saturday)
        if (!$weekStart) {
            $today = new \DateTime();
            $dayOfWeek = (int)$today->format('N'); // 1=Monday, 7=Sunday
            $diff = $dayOfWeek - 1;
            $monday = (clone $today)->modify("-{$diff} days");
            $weekStart = $monday->format('Y-m-d');
        }

        // Calculate week end (Saturday)
        $weekEndDate = new \DateTime($weekStart);
        $weekEndDate->modify('+5 days'); // Monday + 5 = Saturday
        $weekEnd = $weekEndDate->format('Y-m-d');

        // Generate dates for the week (Mon-Sat)
        $weekDates = [];
        $tempDate = new \DateTime($weekStart);
        for ($i = 0; $i < 6; $i++) {
            $weekDates[] = [
                'date' => $tempDate->format('Y-m-d'),
                'day_name' => $this->getDayName($tempDate->format('N')),
                'day_short' => $tempDate->format('D'),
            ];
            $tempDate->modify('+1 day');
        }

        // Get jadwal mengajar guru
        $jadwalGuru = $this->jadwalModel->getByGuru($guru['id'], null, get_active_tahun_ajaran());

        // Extract unique subjects taught by this teacher
        $subjectsMap = [];
        foreach ($jadwalGuru as $jadwal) {
            $mapelIdKey = $jadwal['mata_pelajaran_id'];
            if (!isset($subjectsMap[$mapelIdKey])) {
                $subjectsMap[$mapelIdKey] = [
                    'id' => $mapelIdKey,
                    'nama_mapel' => $jadwal['nama_mapel'],
                ];
            }
        }
        $subjectsList = array_values($subjectsMap);

        // Get absensi data for the week
        $absensiData = $this->absensiModel->select('absensi.*, 
                jadwal_mengajar.kelas_id, 
                jadwal_mengajar.mata_pelajaran_id, 
                jadwal_mengajar.hari, 
                jadwal_mengajar.jam_mulai, 
                jadwal_mengajar.jam_selesai,
                mata_pelajaran.nama_mapel,
                kelas.nama_kelas')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('jadwal_mengajar.guru_id', $guru['id'])
            ->where('jadwal_mengajar.tahun_ajaran', get_active_tahun_ajaran())
            ->where('absensi.tanggal >=', $weekStart)
            ->where('absensi.tanggal <=', $weekEnd)
            ->orderBy('absensi.tanggal', 'ASC')
            ->orderBy('jadwal_mengajar.jam_mulai', 'ASC')
            ->findAll();

        // Filter by subject if selected
        if ($mapelId) {
            $absensiData = array_filter($absensiData, function ($item) use ($mapelId) {
                return $item['mata_pelajaran_id'] == $mapelId;
            });
            $absensiData = array_values($absensiData);
        }

        // Group absensi by subject
        $reportBySubject = [];
        foreach ($absensiData as $absensi) {
            $mapelKey = $absensi['mata_pelajaran_id'];
            if (!isset($reportBySubject[$mapelKey])) {
                $reportBySubject[$mapelKey] = [
                    'nama_mapel' => $absensi['nama_mapel'],
                    'mapel_id' => $mapelKey,
                    'sessions' => [],
                    'summary' => [
                        'total_pertemuan' => 0,
                        'total_hadir' => 0,
                        'total_sakit' => 0,
                        'total_izin' => 0,
                        'total_alpa' => 0,
                        'total_siswa' => 0,
                    ]
                ];
            }

            // Get attendance details for this session
            $details = $this->absensiDetailModel->getByAbsensi($absensi['id']);
            
            $sessionDetail = [
                'absensi_id' => $absensi['id'],
                'tanggal' => $absensi['tanggal'],
                'pertemuan_ke' => $absensi['pertemuan_ke'],
                'kelas_nama' => $absensi['nama_kelas'],
                'kelas_id' => $absensi['kelas_id'],
                'hari' => $absensi['hari'],
                'jam' => substr($absensi['jam_mulai'], 0, 5) . ' - ' . substr($absensi['jam_selesai'], 0, 5),
                'materi' => $absensi['materi_pembelajaran'] ?? '-',
                'total_siswa' => count($details),
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpa' => 0,
                'details' => []
            ];

            foreach ($details as $detail) {
                $sessionDetail['details'][] = [
                    'siswa_nama' => $detail['nama_lengkap'],
                    'nis' => $detail['nis'],
                    'status' => $detail['status'],
                    'keterangan' => $detail['keterangan'] ?? '',
                ];

                switch ($detail['status']) {
                    case 'hadir': $sessionDetail['hadir']++; break;
                    case 'sakit': $sessionDetail['sakit']++; break;
                    case 'izin': $sessionDetail['izin']++; break;
                    case 'alpa': $sessionDetail['alpa']++; break;
                }
            }

            $reportBySubject[$mapelKey]['sessions'][] = $sessionDetail;

            // Update summary
            $reportBySubject[$mapelKey]['summary']['total_pertemuan']++;
            $reportBySubject[$mapelKey]['summary']['total_hadir'] += $sessionDetail['hadir'];
            $reportBySubject[$mapelKey]['summary']['total_sakit'] += $sessionDetail['sakit'];
            $reportBySubject[$mapelKey]['summary']['total_izin'] += $sessionDetail['izin'];
            $reportBySubject[$mapelKey]['summary']['total_alpa'] += $sessionDetail['alpa'];
            $reportBySubject[$mapelKey]['summary']['total_siswa'] += $sessionDetail['total_siswa'];
        }

        $data = [
            'title' => 'Laporan Mingguan Absensi Siswa',
            'guru' => $guru,
            'isGuruMapel' => $isGuruMapel,
            'isWakakur' => $isWakakur,
            'allRoles' => $allRoles,
            'subjectsList' => $subjectsList,
            'selectedMapelId' => $mapelId,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'weekDates' => $weekDates,
            'reportBySubject' => $reportBySubject,
            'totalSessions' => count($absensiData),
        ];

        return view('guru/laporan/weekly_report', $data);
    }

    public function print()
    {
        $userId = session()->get('user_id') ?? session()->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        $weekStart = $this->request->getGet('week_start');
        $mapelId = $this->request->getGet('mapel_id');

        if (!$weekStart) {
            return redirect()->to('/guru/laporan-mingguan')->with('error', 'Parameter minggu harus diisi');
        }

        $weekEndDate = new \DateTime($weekStart);
        $weekEndDate->modify('+5 days');
        $weekEnd = $weekEndDate->format('Y-m-d');

        // Get jadwal mengajar guru
        $jadwalGuru = $this->jadwalModel->getByGuru($guru['id'], null, get_active_tahun_ajaran());

        // Extract unique subjects
        $subjectsMap = [];
        foreach ($jadwalGuru as $jadwal) {
            $mapelIdKey = $jadwal['mata_pelajaran_id'];
            if (!isset($subjectsMap[$mapelIdKey])) {
                $subjectsMap[$mapelIdKey] = [
                    'id' => $mapelIdKey,
                    'nama_mapel' => $jadwal['nama_mapel'],
                ];
            }
        }
        $subjectsList = array_values($subjectsMap);

        // Get absensi data
        $absensiQuery = $this->absensiModel->select('absensi.*, 
                jadwal_mengajar.kelas_id, 
                jadwal_mengajar.mata_pelajaran_id, 
                jadwal_mengajar.hari, 
                jadwal_mengajar.jam_mulai, 
                jadwal_mengajar.jam_selesai,
                mata_pelajaran.nama_mapel,
                kelas.nama_kelas')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('jadwal_mengajar.guru_id', $guru['id'])
            ->where('jadwal_mengajar.tahun_ajaran', get_active_tahun_ajaran())
            ->where('absensi.tanggal >=', $weekStart)
            ->where('absensi.tanggal <=', $weekEnd)
            ->orderBy('absensi.tanggal', 'ASC')
            ->orderBy('jadwal_mengajar.jam_mulai', 'ASC');

        if ($mapelId) {
            $absensiQuery->where('jadwal_mengajar.mata_pelajaran_id', $mapelId);
        }

        $absensiData = $absensiQuery->findAll();

        // Group by subject
        $reportBySubject = [];
        foreach ($absensiData as $absensi) {
            $mapelKey = $absensi['mata_pelajaran_id'];
            if (!isset($reportBySubject[$mapelKey])) {
                $reportBySubject[$mapelKey] = [
                    'nama_mapel' => $absensi['nama_mapel'],
                    'sessions' => [],
                    'summary' => [
                        'total_pertemuan' => 0,
                        'total_hadir' => 0,
                        'total_sakit' => 0,
                        'total_izin' => 0,
                        'total_alpa' => 0,
                        'total_siswa' => 0,
                    ]
                ];
            }

            $details = $this->absensiDetailModel->getByAbsensi($absensi['id']);
            
            $sessionDetail = [
                'absensi_id' => $absensi['id'],
                'tanggal' => $absensi['tanggal'],
                'pertemuan_ke' => $absensi['pertemuan_ke'],
                'kelas_nama' => $absensi['nama_kelas'],
                'hari' => $absensi['hari'],
                'jam' => substr($absensi['jam_mulai'], 0, 5) . ' - ' . substr($absensi['jam_selesai'], 0, 5),
                'materi' => $absensi['materi_pembelajaran'] ?? '-',
                'total_siswa' => count($details),
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpa' => 0,
                'details' => []
            ];

            foreach ($details as $detail) {
                $sessionDetail['details'][] = [
                    'siswa_nama' => $detail['nama_lengkap'],
                    'nis' => $detail['nis'],
                    'status' => $detail['status'],
                    'keterangan' => $detail['keterangan'] ?? '',
                ];

                switch ($detail['status']) {
                    case 'hadir': $sessionDetail['hadir']++; break;
                    case 'sakit': $sessionDetail['sakit']++; break;
                    case 'izin': $sessionDetail['izin']++; break;
                    case 'alpa': $sessionDetail['alpa']++; break;
                }
            }

            $reportBySubject[$mapelKey]['sessions'][] = $sessionDetail;

            $reportBySubject[$mapelKey]['summary']['total_pertemuan']++;
            $reportBySubject[$mapelKey]['summary']['total_hadir'] += $sessionDetail['hadir'];
            $reportBySubject[$mapelKey]['summary']['total_sakit'] += $sessionDetail['sakit'];
            $reportBySubject[$mapelKey]['summary']['total_izin'] += $sessionDetail['izin'];
            $reportBySubject[$mapelKey]['summary']['total_alpa'] += $sessionDetail['alpa'];
            $reportBySubject[$mapelKey]['summary']['total_siswa'] += $sessionDetail['total_siswa'];
        }

        $data = [
            'guru' => $guru,
            'subjectsList' => $subjectsList,
            'selectedMapelId' => $mapelId,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'reportBySubject' => $reportBySubject,
        ];

        return view('guru/laporan/weekly_report_print', $data);
    }

    private function getDayName($dayNumber)
    {
        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        return $days[$dayNumber] ?? '';
    }
}
