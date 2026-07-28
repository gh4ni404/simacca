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
     * Print weekly attendance recap for PKL group
     * @param int|null $minggu Week number (null = all weeks)
     */
    public function printRekap($minggu = null)
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

        // Determine date range based on selected week
        $weekStartDate = $startDate;
        $weekEndDate = $endDate ?: date('Y-m-d');

        if ($minggu !== null) {
            $minggu = (int) $minggu;
            $range = get_week_range($startDate, $minggu);
            $weekStartDate = $range['start'];
            $weekEndDate = $range['end'];

            // Clamp end date to PKL end date
            if ($endDate && $weekEndDate > $endDate) {
                $weekEndDate = $endDate;
            }
        }

        $db = \Config\Database::connect();
        
        // Fetch attendance specifically for the logged-in student
        $attendanceLookup = [];
        $attendanceRows = $db->table('absensi_pkl_detail')
            ->select('absensi_pkl_detail.status, absensi_pkl_detail.keterangan, absensi_pkl.tanggal, absensi_pkl_detail.waktu_absen')
            ->join('absensi_pkl', 'absensi_pkl.id = absensi_pkl_detail.absensi_pkl_id')
            ->where('absensi_pkl_detail.siswa_id', $siswa['id'])
            ->where('absensi_pkl.tanggal >=', $weekStartDate)
            ->where('absensi_pkl.tanggal <=', $weekEndDate)
            ->where('absensi_pkl.deleted_at', null)
            ->get()
            ->getResultArray();

        foreach ($attendanceRows as $row) {
            $attendanceLookup[$row['tanggal']] = [
                'status'      => $row['status'],
                'keterangan'  => $row['keterangan'],
                'waktu_absen' => $row['waktu_absen']
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

        // Generate weekly calendar blocks grouped by week
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

        // Helper: week-of-month based on Monday's date
        $weekOfMonth = function (\DateTime $monday) {
            $first = new \DateTime($monday->format('Y-m-01'));
            $firstDow = (int) $first->format('N');
            $firstMonday = clone $first;
            if ($firstDow > 1) {
                $firstMonday->modify('+' . (8 - $firstDow) . ' days');
            }
            $diff = $firstMonday->diff($monday)->days;
            return (int) floor($diff / 7) + 1;
        };

        // Use the setting week-base to determine total weeks
        $end = new \DateTime($endDate ?: date('Y-m-d'));
        $weekBaseStr = get_jurnal_pkl_week_base();
        $weekBase    = new \DateTime($weekBaseStr ?: $startDate);
        $totalWeeks  = (int) floor($weekBase->diff($end)->days / 7) + 1;

        // Determine which weeks to render
        $weeksToRender = ($minggu !== null) ? [$minggu] : range(1, $totalWeeks);

        foreach ($weeksToRender as $w) {
            $range  = get_week_range($startDate, $w);
            $wStart = new \DateTime($range['start']);

            $days = [];
            for ($i = 0; $i < 6; $i++) { // Mon(0)–Sat(5), skip Sunday
                $dayDt   = clone $wStart;
                $dayDt->modify("+$i days");
                $dateStr = $dayDt->format('Y-m-d');

                if ($dateStr >= $weekStartDate && $dateStr <= $weekEndDate) {
                    $mn = (int) $dayDt->format('m');
                    $yr = (int) $dayDt->format('Y');
                    $days[] = [
                        'date_str'     => $dateStr,
                        'day_name'     => $getIndonesianDayName($dayDt->format('l')),
                        'display_date' => $dayDt->format('d') . ' ' . $indonesianMonth[$mn] . ' ' . $yr,
                    ];
                }
            }

            if (empty($days)) {
                continue;
            }

            $monMonth = (int) $wStart->format('m');
            $monYear  = (int) $wStart->format('Y');
            $wNum     = $weekOfMonth($wStart);

            $weeks[] = [
                'week_label' => $indonesianMonth[$monMonth] . ' (Minggu ke-' . $wNum . ') ' . $monYear,
                'days'       => $days,
            ];
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
