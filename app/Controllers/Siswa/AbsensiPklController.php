<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Services\AbsensiPklService;
use App\Models\SiswaModel;

class AbsensiPklController extends BaseController
{
    protected $absensiPklService;
    protected $siswaModel;
    protected $session;

    public function __construct()
    {
        $this->absensiPklService = new AbsensiPklService();
        $this->siswaModel = new SiswaModel();
        $this->session = session();
    }

    /**
     * Rekap absensi PKL for siswa
     */
    public function index()
    {
        $userId = $this->session->get('userId');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/login');
        }

        $result = $this->absensiPklService->getRekapSiswa($siswa['id']);
        $rekap = $result['data']['rekap'] ?? [];
        $statistik = $result['data']['statistik'] ?? [];

        // Group rekap by month
        $groupedByMonth = [];
        foreach ($rekap as $item) {
            $month = date('Y-m', strtotime($item['tanggal']));
            $monthLabel = date('F Y', strtotime($item['tanggal']));
            if (!isset($groupedByMonth[$month])) {
                $groupedByMonth[$month] = [
                    'label'   => $monthLabel,
                    'items'   => [],
                ];
            }
            $groupedByMonth[$month]['items'][] = $item;
        }

        $data = [
            'title'           => 'Absensi PKL',
            'pageTitle'       => 'Rekap Absensi PKL',
            'pageDescription' => 'Lihat rekap kehadiran PKL Anda',
            'siswa'           => $siswa,
            'rekap'           => $rekap,
            'statistik'       => $statistik,
            'groupedByMonth'  => $groupedByMonth,
        ];

        return view('siswa/absensi-pkl/index', $data);
    }

    /**
     * Detail absensi session
     */
    public function detail($id)
    {
        $userId = $this->session->get('userId');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/login');
        }

        $result = $this->absensiPklService->getAbsensiDetail((int) $id);

        if (!$result['success']) {
            return redirect()->to('/siswa/absensi-pkl')
                ->with('error', $result['message']);
        }

        $data = [
            'title'           => 'Detail Absensi PKL',
            'pageTitle'       => 'Detail Kehadiran',
            'pageDescription' => 'Detail kehadiran pada sesi ini',
            'absensi'         => $result['data']['absensi'],
            'details'         => $result['data']['details'],
            'statistics'      => $result['data']['statistics'],
        ];

        return view('siswa/absensi-pkl/detail', $data);
    }

    /**
     * Print monthly attendance recap for PKL group
     * @param string|null $bulan Month in "Y-m" format (null = all months)
     */
    public function printRekap($bulan = null)
    {
        $userId = $this->session->get('userId');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/login');
        }

        $siswaPklModel = new \App\Models\SiswaPklModel();
        $tempatPklModel = new \App\Models\TempatPklModel();
        $pembimbingPklModel = new \App\Models\PembimbingPklModel();
        $instrukturModel = new \App\Models\InstrukturPklModel();

        $siswaPkl = $siswaPklModel->getBySiswaAndTahun($siswa['id'], $siswa['tahun_ajaran']);
        if (!$siswaPkl || empty($siswaPkl['tempat_pkl_id'])) {
            return view('siswa/pkl/print-error', [
                'title' => 'Tempat PKL Belum Terdaftar',
                'message' => 'Anda belum terdaftar di tempat PKL mana pun untuk tahun ajaran ini.',
                'details' => [
                    'Pastikan data penempatan PKL Anda telah diinput oleh Admin.',
                    'Jika ada kesalahan, silakan hubungi guru pembimbing PKL Anda.'
                ],
            ]);
        }

        $tempatPkl = $tempatPklModel->find($siswaPkl['tempat_pkl_id']);
        $pembimbing = $pembimbingPklModel->getByTempatPklAndTahun($siswaPkl['tempat_pkl_id'], $siswaPkl['tahun_ajaran']);
        $instruktur = $instrukturModel->getByTempatPkl($siswaPkl['tempat_pkl_id']);

        helper('setting');
        $startDate = get_jurnal_pkl_start_date();
        $endDate = get_jurnal_pkl_end_date();

        if (!$startDate) {
            return view('siswa/pkl/print-error', [
                'title' => 'Periode PKL Belum Diatur',
                'message' => 'Tanggal mulai periode PKL belum diatur di sistem.',
                'details' => ['Harap hubungi pihak administrator sekolah.'],
            ]);
        }

        // Determine date range based on selected month
        $filterStartDate = $startDate;
        $filterEndDate = $endDate ?: date('Y-m-d');

        if ($bulan !== null && preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            // Get first and last day of the selected month
            $monthStart = $bulan . '-01';
            $monthEnd = date('Y-m-t', strtotime($monthStart));

            // Clamp to PKL date range
            $filterStartDate = max($monthStart, $startDate);
            $filterEndDate = $endDate ? min($monthEnd, $endDate) : $monthEnd;
        }

        $db = \Config\Database::connect();
        
        // Fetch attendance specifically for the logged-in student
        $attendanceLookup = [];
        $attendanceRows = $db->table('absensi_pkl_detail')
            ->select('absensi_pkl_detail.status, absensi_pkl_detail.keterangan, absensi_pkl.tanggal, absensi_pkl_detail.waktu_absen, absensi_pkl_detail.waktu_pulang, absensi_pkl.keterangan_umum')
            ->join('absensi_pkl', 'absensi_pkl.id = absensi_pkl_detail.absensi_pkl_id')
            ->where('absensi_pkl_detail.siswa_id', $siswa['id'])
            ->where('absensi_pkl.tanggal >=', $filterStartDate)
            ->where('absensi_pkl.tanggal <=', $filterEndDate)
            ->where('absensi_pkl.deleted_at', null)
            ->get()
            ->getResultArray();

        foreach ($attendanceRows as $row) {
            $attendanceLookup[$row['tanggal']] = [
                'status'           => $row['status'],
                'keterangan'       => $row['keterangan'],
                'keterangan_umum'  => $row['keterangan_umum'],
                'waktu_absen'      => $row['waktu_absen'],
                'waktu_pulang'     => $row['waktu_pulang'],
            ];
        }

        // Fetch journal activities for this student
        $progressModel = new \App\Models\PklProgressModel();
        $progressRows = $progressModel->select('pkl_progress.tanggal, pkl_progress.deskripsi, pkl_tasks.judul')
            ->join('pkl_tasks', 'pkl_tasks.id = pkl_progress.task_id AND pkl_tasks.deleted_at IS NULL')
            ->where('pkl_tasks.siswa_id', $siswa['id'])
            ->where('pkl_progress.deleted_at', null)
            ->findAll();

        $progressLookup = [];
        foreach ($progressRows as $row) {
            $progressLookup[$row['tanggal']] = $row['judul'] . ': ' . $row['deskripsi'];
        }

        // Generate monthly calendar blocks
        $weeks = [];
        $indonesianMonth = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // Helper: Indonesian day name
        $getIndonesianDayName = function ($dayEnglish) {
            $map = [
                'Monday'    => 'Senin',
                'Tuesday'   => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday'  => 'Kamis',
                'Friday'    => 'Jumat',
                'Saturday'  => 'Sabtu',
                'Sunday'    => 'Minggu'
            ];
            return $map[$dayEnglish] ?? $dayEnglish;
        };

        // Determine which months to render
        $startDt = new \DateTime($startDate);
        $endDt = new \DateTime($endDate ?: date('Y-m-d'));

        $monthsToRender = [];
        if ($bulan !== null && preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            $monthsToRender[] = $bulan;
        } else {
            // Generate all months between start and end date
            $current = new \DateTime($startDate);
            $current->modify('first day of this month');
            $lastMonth = new \DateTime($endDate ?: date('Y-m-d'));
            $lastMonth->modify('last day of this month');

            while ($current <= $lastMonth) {
                $monthsToRender[] = $current->format('Y-m');
                $current->modify('first day of next month');
            }
        }

        // Generate days for each month
        foreach ($monthsToRender as $monthStr) {
            $monthStart = new \DateTime($monthStr . '-01');
            $monthEnd = new \DateTime(date('Y-m-t', strtotime($monthStr . '-01')));

            // Clamp to PKL date range
            if ($monthStart < $startDt) $monthStart = clone $startDt;
            if ($monthEnd > $endDt) $monthEnd = clone $endDt;

            $days = [];
            $currentDay = clone $monthStart;

            while ($currentDay <= $monthEnd) {
                $dayOfWeek = (int) $currentDay->format('N'); // 1=Mon, 7=Sun
                $dateStr   = $currentDay->format('Y-m-d');

                // Tampilkan hari jika:
                // - Senin–Sabtu (hari kerja normal), ATAU
                // - Minggu tapi ada data absensi (siswa PKL masuk di hari Minggu)
                $isWeekend     = ($dayOfWeek === 7);
                $hasAttendance = isset($attendanceLookup[$dateStr]);

                if (!$isWeekend || $hasAttendance) {
                    $mn = (int) $currentDay->format('m');
                    $yr = (int) $currentDay->format('Y');
                    $days[] = [
                        'date_str'     => $dateStr,
                        'day_name'     => $getIndonesianDayName($currentDay->format('l')),
                        'display_date' => $currentDay->format('d') . ' ' . $indonesianMonth[$mn] . ' ' . $yr,
                        'is_weekend'   => $isWeekend, // flag untuk styling di view
                    ];
                }
                $currentDay->modify('+1 day');
            }

            if (!empty($days)) {
                $weeks[] = [
                    'week_label' => $indonesianMonth[(int) $monthStart->format('m')] . ' ' . $monthStart->format('Y'),
                    'days'       => $days,
                ];
            }
        }

        $data = [
            'title'             => 'Daftar Hadir PKL',
            'siswa'             => $siswa,
            'tempatPkl'         => $tempatPkl,
            'pembimbing'        => $pembimbing,
            'instruktur'        => $instruktur,
            'weeks'             => $weeks,
            'attendanceLookup'  => $attendanceLookup,
            'progressLookup'    => $progressLookup,
        ];

        return view('siswa/absensi-pkl/print_rekap', $data);
    }
}
