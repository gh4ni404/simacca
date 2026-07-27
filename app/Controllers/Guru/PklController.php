<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Services\PklService;
use App\Models\GuruModel;

class PklController extends BaseController
{
    protected $guruModel;
    protected $pklService;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->pklService = new PklService();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/access-denied')->with('error', 'Data guru nggak ketemu 🤔');
        }

        $result = $this->pklService->getGroupedBySiswaForPembimbing();
        $grouped = $result['success'] ? $result['data']['grouped'] : [];
        $stats = $result['success'] ? $result['data']['stats'] : [];

        $data = [
            'title' => 'Verifikasi Jurnal PKL',
            'guru' => $guru,
            'groupedData' => $grouped,
            'stats' => $stats,
        ];

        return view('guru/pkl/index', $data);
    }

    public function verify($id)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/access-denied')->with('error', 'Data guru nggak ketemu 🤔');
        }

        $status = $this->request->getPost('status');
        $catatan = trim($this->request->getPost('catatan') ?? '');

        if (!in_array($status, ['approved', 'revision'])) {
            session()->setFlashdata('error', 'Status verifikasi nggak valid 🤔');
            return redirect()->to('/guru/jurnal-pkl');
        }

        if ($catatan === '') {
            session()->setFlashdata('error', 'Catatan pembimbing wajib diisi ya');
            return redirect()->back()->withInput();
        }

        if (mb_strlen($catatan) > 200) {
            session()->setFlashdata('error', 'Catatan pembimbing maksimal 200 karakter');
            return redirect()->back()->withInput();
        }

        $result = $this->pklService->verify($id, $userId, $status, $catatan, 'pembimbing');

        if ($result['success']) {
            $messages = [
                'approved' => 'Progress udah disetujui nih 👍',
                'revision' => 'Progress udah direvisi ✓',
            ];
            session()->setFlashdata('success', $messages[$status]);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/guru/jurnal-pkl');
    }

    public function detail($id)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/access-denied')->with('error', 'Data guru nggak ketemu 🤔');
        }

        $result = $this->pklService->getProgressById($id);
        if (!$result['success']) {
            session()->setFlashdata('error', 'Progress nggak ketemu 🤔');
            return redirect()->to('/guru/jurnal-pkl');
        }

        $data = [
            'title' => 'Detail Progress PKL',
            'guru' => $guru,
            'progress' => $result['data'],
        ];

        return view('guru/pkl/detail', $data);
    }

    public function cancelVerification($id)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/access-denied')->with('error', 'Data guru nggak ketemu 🤔');
        }

        $result = $this->pklService->cancelVerification($id, 'pembimbing');

        if ($result['success']) {
            session()->setFlashdata('success', 'Verifikasi progress udah dibatalkan ✓');
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/guru/jurnal-pkl');
    }

    public function getTasksBySiswa($siswaId)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $week = $this->request->getGet('week');
        $db = \Config\Database::connect();

        $sql = "
            SELECT DISTINCT pt.id, pt.judul, MIN(pp.tanggal) AS tanggal
            FROM pkl_tasks pt
            JOIN siswa_pkl sp ON sp.siswa_id = pt.siswa_id AND sp.tahun_ajaran = ?
            JOIN pembimbing_pkl ppk ON ppk.id = sp.pembimbing_pkl_id AND ppk.guru_id = ?
            JOIN pkl_progress pp ON pp.task_id = pt.id AND pp.deleted_at IS NULL
            WHERE pt.siswa_id = ? AND pt.deleted_at IS NULL
        ";
        $params = [get_active_tahun_ajaran(), $guru['id'], $siswaId];

        if (!empty($week)) {
            $startDate = get_jurnal_pkl_start_date();
            $weekRange = get_week_range($startDate, (int) $week);
            $sql .= " AND pp.tanggal >= ? AND pp.tanggal <= ?";
            $params[] = $weekRange['start'];
            $params[] = $weekRange['end'];
        }

        $sql .= " GROUP BY pt.id, pt.judul ORDER BY tanggal ASC";

        // Add status_label based on whether catatan_pembimbing exists
        $tasks = $db->query($sql, $params)->getResultArray();
        foreach ($tasks as &$task) {
            $progressSql = "
                SELECT 
                    SUM(CASE WHEN pp.catatan_pembimbing IS NOT NULL AND pp.catatan_pembimbing != '' THEN 1 ELSE 0 END) AS ada_catatan,
                    COUNT(*) AS total
                FROM pkl_progress pp
                WHERE pp.task_id = ? AND pp.deleted_at IS NULL
            ";
            $row = $db->query($progressSql, [$task['id']])->getRowArray();

            if ($row && $row['ada_catatan'] > 0) {
                $task['status_label'] = 'Sudah Ada Catatan';
            } else {
                $task['status_label'] = 'Belum Ada Catatan';
            }
        }
        unset($task);

        return $this->response->setJSON(['success' => true, 'data' => $tasks]);
    }

    public function getFilteredProgress($siswaId)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $week = $this->request->getGet('week');
        $taskId = $this->request->getGet('task_id');

        if (empty($week) || empty($taskId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Filter week dan task wajib dipilih']);
        }

        $startDate = get_jurnal_pkl_start_date();
        $weekRange = get_week_range($startDate, (int) $week);

        $db = \Config\Database::connect();
        $progress = $db->query("
            SELECT pp.*, pt.judul AS task_judul, pt.siswa_id,
                   s.nama_lengkap AS nama_siswa, s.nis,
                   k.nama_kelas, pc.nama AS kategori_nama,
                   users.profile_photo, tempat_pkl.nama_perusahaan
            FROM pkl_progress pp
            JOIN pkl_tasks pt ON pt.id = pp.task_id AND pt.deleted_at IS NULL
            JOIN siswa s ON s.id = pt.siswa_id AND s.deleted_at IS NULL
            JOIN siswa_pkl sp ON sp.siswa_id = s.id AND sp.tahun_ajaran = ?
            JOIN pembimbing_pkl ppk ON ppk.id = sp.pembimbing_pkl_id AND ppk.guru_id = ?
            LEFT JOIN kelas k ON k.id = s.kelas_id
            LEFT JOIN users ON users.id = s.user_id
            LEFT JOIN pkl_categories pc ON pc.id = pt.kategori_id
            LEFT JOIN tempat_pkl ON tempat_pkl.id = sp.tempat_pkl_id
            WHERE pt.siswa_id = ?
              AND pp.task_id = ?
              AND pp.tanggal >= ? AND pp.tanggal <= ?
              AND pp.deleted_at IS NULL
            ORDER BY pp.tanggal DESC
        ", [get_active_tahun_ajaran(), $guru['id'], $siswaId, $taskId, $weekRange['start'], $weekRange['end']])->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $progress]);
    }

    public function getWeekInfo()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $startDate = get_jurnal_pkl_start_date();
        $endDate = get_jurnal_pkl_end_date();
        $pklEndDate = $endDate ?: date('Y-m-d');

        if (!$startDate) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tanggal mulai PKL belum diatur']);
        }

        $start = new \DateTime($startDate);
        $end = new \DateTime($pklEndDate);
        $totalDays = (int) $start->diff($end)->days;
        $totalWeeks = max(1, (int) ceil(($totalDays + 1) / 7));

        $weeks = [];
        for ($i = 1; $i <= $totalWeeks; $i++) {
            $range = get_week_range($startDate, $i);
            $weekStart = new \DateTime($range['start']);
            $weekEnd = new \DateTime($range['end']);
            $weeks[] = [
                'week' => $i,
                'start' => $range['start'],
                'end' => $range['end'],
                'label' => 'Minggu ' . $i . ' (' . $weekStart->format('j M') . ' - ' . $weekEnd->format('j M') . ')',
            ];
        }

        return $this->response->setJSON(['success' => true, 'data' => $weeks]);
    }
}
