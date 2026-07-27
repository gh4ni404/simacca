<?php

namespace App\Controllers\Instruktur;

use App\Controllers\BaseController;
use App\Models\InstrukturPklModel;
use App\Models\SiswaPklModel;
use App\Models\PklTaskModel;
use App\Models\PklProgressModel;
use App\Models\PembimbingPklModel;

class JurnalPklController extends BaseController
{
    protected $instrukturPklModel;
    protected $siswaPklModel;
    protected $taskModel;
    protected $progressModel;
    protected $pembimbingPklModel;

    public function __construct()
    {
        $this->instrukturPklModel = new InstrukturPklModel();
        $this->siswaPklModel = new SiswaPklModel();
        $this->taskModel = new PklTaskModel();
        $this->progressModel = new PklProgressModel();
        $this->pembimbingPklModel = new PembimbingPklModel();
    }

    private function getInstruktur()
    {
        $instrukturId = session()->get('instruktur_id');
        return $this->instrukturPklModel->find($instrukturId);
    }

    public function index()
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login')->with('error', 'Sesi Anda telah habis atau data instruktur tidak ditemukan.');
        }

        $tahunAjaran = get_active_tahun_ajaran();
        $siswaList = $this->siswaPklModel
            ->select('siswa_pkl.*, siswa.nama_lengkap, siswa.nis, kelas.nama_kelas, users.profile_photo')
            ->join('siswa', 'siswa.id = siswa_pkl.siswa_id AND siswa.deleted_at IS NULL', 'left')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('users', 'users.id = siswa.user_id', 'left')
            ->where('siswa_pkl.tempat_pkl_id', $instruktur['tempat_pkl_id'])
            ->where('siswa_pkl.tahun_ajaran', $tahunAjaran)
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->findAll();

        $siswaIds = array_column($siswaList, 'siswa_id');
        $siswaStats = [];

        if (!empty($siswaIds)) {
            $db = \Config\Database::connect();
            $rows = $db->query("
                SELECT pt.siswa_id,
                       COUNT(DISTINCT pt.id) AS total_tasks,
                       COUNT(pp.id) AS total_progress,
                       SUM(CASE WHEN pp.status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                       SUM(CASE WHEN pp.status = 'verified_by_instruktur' THEN 1 ELSE 0 END) AS verified_by_instruktur,
                       SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS approved,
                       SUM(CASE WHEN pp.status = 'revision' THEN 1 ELSE 0 END) AS revision,
                       MAX(pp.tanggal) AS last_activity
                FROM pkl_tasks pt
                LEFT JOIN pkl_progress pp ON pp.task_id = pt.id AND pp.deleted_at IS NULL
                WHERE pt.siswa_id IN (" . implode(',', $siswaIds) . ")
                  AND pt.deleted_at IS NULL
                GROUP BY pt.siswa_id
            ")->getResultArray();

            foreach ($rows as $row) {
                $siswaStats[$row['siswa_id']] = $row;
            }

            // Fetch all pending review progress entries across all students
            $pendingProgress = $db->query("
                SELECT pp.*, pt.siswa_id, pt.judul AS task_judul, s.nama_lengkap AS nama_siswa, s.nis, k.nama_kelas, users.profile_photo
                FROM pkl_progress pp
                JOIN pkl_tasks pt ON pt.id = pp.task_id
                JOIN siswa s ON s.id = pt.siswa_id
                JOIN siswa_pkl sp ON sp.siswa_id = s.id AND sp.tahun_ajaran = ?
                LEFT JOIN kelas k ON k.id = s.kelas_id
                LEFT JOIN users ON users.id = s.user_id
                WHERE sp.tempat_pkl_id = ?
                  AND pp.status = 'submitted'
                  AND pp.deleted_at IS NULL
                  AND pt.deleted_at IS NULL
                ORDER BY pp.tanggal ASC
            ", [$tahunAjaran, $instruktur['tempat_pkl_id']])->getResultArray();
            // Fetch all progress entries for full history view
            $allProgressRows = $db->query("
                SELECT pp.*, pt.judul AS task_judul, pt.siswa_id
                FROM pkl_progress pp
                JOIN pkl_tasks pt ON pt.id = pp.task_id
                JOIN siswa_pkl sp ON sp.siswa_id = pt.siswa_id AND sp.tahun_ajaran = ?
                WHERE sp.tempat_pkl_id = ?
                  AND pp.deleted_at IS NULL
                  AND pt.deleted_at IS NULL
                ORDER BY pp.tanggal DESC
            ", [$tahunAjaran, $instruktur['tempat_pkl_id']])->getResultArray();

            $allProgress = [];
            foreach ($allProgressRows as $row) {
                $allProgress[$row['siswa_id']][] = $row;
            }
        } else {
            $pendingProgress = [];
            $allProgress = [];
        }

        $data = [
            'title'           => 'Jurnal PKL - Instruktur',
            'pageTitle'       => 'Jurnal PKL',
            'siswaList'       => $siswaList,
            'siswaStats'      => $siswaStats,
            'pendingProgress' => $pendingProgress,
            'allProgress'     => $allProgress,
        ];

        return view('instruktur/jurnal_pkl/index', $data);
    }

    public function pendingReview()
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login')->with('error', 'Sesi Anda telah habis atau data instruktur tidak ditemukan.');
        }

        $tahunAjaran = get_active_tahun_ajaran();
        $db = \Config\Database::connect();

        $pendingProgress = $db->query("
            SELECT pp.*, pt.siswa_id, pt.judul AS task_judul,
                   s.nama_lengkap AS nama_siswa, s.nis, k.nama_kelas, users.profile_photo
            FROM pkl_progress pp
            JOIN pkl_tasks pt ON pt.id = pp.task_id AND pt.deleted_at IS NULL
            JOIN siswa s ON s.id = pt.siswa_id AND s.deleted_at IS NULL
            JOIN siswa_pkl sp ON sp.siswa_id = s.id AND sp.tahun_ajaran = ?
            LEFT JOIN kelas k ON k.id = s.kelas_id
            LEFT JOIN users ON users.id = s.user_id
            WHERE sp.tempat_pkl_id = ?
              AND pp.status = 'submitted'
              AND pp.deleted_at IS NULL
            ORDER BY pp.tanggal ASC
        ", [$tahunAjaran, $instruktur['tempat_pkl_id']])->getResultArray();

        $data = [
            'title'           => 'Menunggu Catatan - Instruktur',
            'pageTitle'       => 'Menunggu Catatan',
            'pendingProgress' => $pendingProgress,
        ];

        return view('instruktur/jurnal_pkl/pending_review', $data);
    }

    public function siswaDetail(int $siswaId)
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login')->with('error', 'Sesi Anda telah habis atau data instruktur tidak ditemukan.');
        }

        $db = \Config\Database::connect();

        $siswa = $db->query("
            SELECT s.id, s.nama_lengkap, s.nis, k.nama_kelas
            FROM siswa s
            LEFT JOIN kelas k ON k.id = s.kelas_id
            WHERE s.id = ? AND s.deleted_at IS NULL
        ", [$siswaId])->getRowArray();

        if (!$siswa) {
            session()->setFlashdata('error', 'Siswa tidak ditemukan');
            return redirect()->to('/instruktur/jurnal-pkl');
        }

        $tasks = $db->query("
            SELECT pt.*, pc.nama AS kategori_nama,
                   COUNT(pp.id) AS total_progress,
                   SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
                   SUM(CASE WHEN pp.status = 'verified_by_instruktur' THEN 1 ELSE 0 END) AS verified_count
            FROM pkl_tasks pt
            LEFT JOIN pkl_categories pc ON pc.id = pt.kategori_id
            LEFT JOIN pkl_progress pp ON pp.task_id = pt.id AND pp.deleted_at IS NULL
            WHERE pt.siswa_id = ? AND pt.deleted_at IS NULL
            GROUP BY pt.id
            ORDER BY pt.created_at DESC
        ", [$siswaId])->getResultArray();

        $data = [
            'title'     => 'Detail Jurnal - ' . $siswa['nama_lengkap'],
            'pageTitle' => 'Detail Jurnal',
            'siswa'     => $siswa,
            'tasks'     => $tasks,
        ];

        return view('instruktur/jurnal_pkl/siswa_detail', $data);
    }

    public function taskDetail(int $taskId)
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login')->with('error', 'Sesi Anda telah habis atau data instruktur tidak ditemukan.');
        }

        $db = \Config\Database::connect();

        $task = $db->query("
            SELECT pt.*, pc.nama AS kategori_nama, s.nama_lengkap AS nama_siswa, s.nis
            FROM pkl_tasks pt
            LEFT JOIN pkl_categories pc ON pc.id = pt.kategori_id
            JOIN siswa s ON s.id = pt.siswa_id
            JOIN siswa_pkl sp ON sp.siswa_id = s.id AND sp.tahun_ajaran = (SELECT `value` FROM `settings` WHERE `key` = 'tahun_ajaran_aktif')
            WHERE pt.id = ? AND pt.deleted_at IS NULL
              AND sp.tempat_pkl_id = ?
        ", [$taskId, $instruktur['tempat_pkl_id']])->getRowArray();

        if (!$task) {
            session()->setFlashdata('error', 'Task tidak ditemukan atau bukan wilayah Anda');
            return redirect()->to('/instruktur/jurnal-pkl');
        }

        $progress = $db->query("
            SELECT pp.*
            FROM pkl_progress pp
            WHERE pp.task_id = ? AND pp.deleted_at IS NULL
            ORDER BY pp.tanggal ASC
        ", [$taskId])->getResultArray();

        $data = [
            'title'     => $task['judul'] . ' - ' . $task['nama_siswa'],
            'pageTitle' => 'Detail Task',
            'task'      => $task,
            'progress'  => $progress,
        ];

        return view('instruktur/jurnal_pkl/task_detail', $data);
    }

    public function addCatatan(int $progressId)
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login')->with('error', 'Sesi Anda telah habis atau data instruktur tidak ditemukan.');
        }

        $catatan = trim($this->request->getPost('catatan_instruktur') ?? '');

        if ($catatan === '') {
            session()->setFlashdata('error', 'Catatan instruktur wajib diisi');
            return redirect()->back()->withInput();
        }

        if (mb_strlen($catatan) > 200) {
            session()->setFlashdata('error', 'Catatan instruktur maksimal 200 karakter');
            return redirect()->back()->withInput();
        }

        $progress = $this->progressModel->find($progressId);

        if (!$progress) {
            session()->setFlashdata('error', 'Progress tidak ditemukan');
            return redirect()->to('/instruktur/jurnal-pkl');
        }

        $db = \Config\Database::connect();
        $authorized = $db->query("
            SELECT 1 FROM pkl_tasks pt
            JOIN siswa_pkl sp ON sp.siswa_id = pt.siswa_id AND sp.tahun_ajaran = (SELECT `value` FROM `settings` WHERE `key` = 'tahun_ajaran_aktif')
            WHERE pt.id = ? AND sp.tempat_pkl_id = ? AND pt.deleted_at IS NULL
        ", [$progress['task_id'], $instruktur['tempat_pkl_id']])->getRowArray();

        if (!$authorized) {
            session()->setFlashdata('error', 'Anda tidak memiliki akses');
            return redirect()->to('/instruktur/jurnal-pkl');
        }

        $this->progressModel->update($progressId, [
            'catatan_instruktur' => $catatan,
        ]);

        session()->setFlashdata('success', 'Catatan berhasil disimpan');
        return redirect()->back();
    }

    public function verifyProgress(int $progressId)
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login');
        }

        $status = $this->request->getPost('status');
        $catatan = trim($this->request->getPost('catatan_instruktur') ?? '');

        if (!in_array($status, ['verified_by_instruktur', 'revision'])) {
            session()->setFlashdata('error', 'Status verifikasi tidak valid');
            return redirect()->back();
        }

        if ($catatan === '') {
            session()->setFlashdata('error', 'Catatan instruktur wajib diisi');
            return redirect()->back()->withInput();
        }

        if (mb_strlen($catatan) > 200) {
            session()->setFlashdata('error', 'Catatan instruktur maksimal 200 karakter');
            return redirect()->back()->withInput();
        }

        $progress = $this->progressModel->find($progressId);
        if (!$progress) {
            session()->setFlashdata('error', 'Progress tidak ditemukan');
            return redirect()->to('/instruktur/jurnal-pkl');
        }

        $db = \Config\Database::connect();
        $authorized = $db->query("
            SELECT 1 FROM pkl_tasks pt
            JOIN siswa_pkl sp ON sp.siswa_id = pt.siswa_id AND sp.tahun_ajaran = (SELECT `value` FROM `settings` WHERE `key` = 'tahun_ajaran_aktif')
            WHERE pt.id = ? AND sp.tempat_pkl_id = ? AND pt.deleted_at IS NULL
        ", [$progress['task_id'], $instruktur['tempat_pkl_id']])->getRowArray();

        if (!$authorized) {
            session()->setFlashdata('error', 'Anda tidak memiliki akses');
            return redirect()->to('/instruktur/jurnal-pkl');
        }

        if (!in_array($progress['status'], ['submitted', 'approved'])) {
            session()->setFlashdata('error', 'Progress ini sudah diverifikasi sebelumnya');
            return redirect()->back();
        }

        $userId = session()->get('user_id');
        $updateData = [
            'instruktur_verified_by' => $userId,
            'instruktur_verified_at' => date('Y-m-d H:i:s'),
            'catatan_instruktur' => $catatan,
        ];

        if ($progress['status'] !== 'approved' || $status === 'revision') {
            $updateData['status'] = $status;
        }

        $this->progressModel->update($progressId, $updateData);

        if ($status === 'revision') {
            $task = $db->table('pkl_tasks')->where('id', $progress['task_id'])->where('deleted_at IS NULL', null, false)->get()->getRowArray();
            if ($task && $task['status'] === 'completed') {
                $db->table('pkl_tasks')->where('id', $progress['task_id'])->update(['status' => 'active']);
            }
        }

        $messages = [
            'verified_by_instruktur' => 'Progress berhasil diverifikasi',
            'revision' => 'Progress direvisi',
        ];
        session()->setFlashdata('success', $messages[$status]);
        return redirect()->back();
    }

    public function cancelVerifikasiProgress(int $progressId)
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login');
        }

        $progress = $this->progressModel->find($progressId);
        if (!$progress) {
            session()->setFlashdata('error', 'Progress tidak ditemukan');
            return redirect()->to('/instruktur/jurnal-pkl');
        }

        $db = \Config\Database::connect();
        $authorized = $db->query("
            SELECT 1 FROM pkl_tasks pt
            JOIN siswa_pkl sp ON sp.siswa_id = pt.siswa_id AND sp.tahun_ajaran = (SELECT `value` FROM `settings` WHERE `key` = 'tahun_ajaran_aktif')
            WHERE pt.id = ? AND sp.tempat_pkl_id = ? AND pt.deleted_at IS NULL
        ", [$progress['task_id'], $instruktur['tempat_pkl_id']])->getRowArray();

        if (!$authorized) {
            session()->setFlashdata('error', 'Anda tidak memiliki akses');
            return redirect()->to('/instruktur/jurnal-pkl');
        }

        if (!in_array($progress['status'], ['verified_by_instruktur', 'revision']) && !$progress['instruktur_verified_by']) {
            session()->setFlashdata('error', 'Progress ini belum diverifikasi oleh instruktur');
            return redirect()->back();
        }

        $updateData = [
            'instruktur_verified_by' => null,
            'instruktur_verified_at' => null,
            'catatan_instruktur' => '',
        ];

        if ($progress['status'] !== 'approved') {
            $updateData['status'] = 'submitted';
        }

        $this->progressModel->update($progressId, $updateData);

        session()->setFlashdata('success', 'Verifikasi progress berhasil dibatalkan');
        return redirect()->back();
    }

    public function getTasksBySiswa(int $siswaId)
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $week = $this->request->getGet('week');
        $db = \Config\Database::connect();

        $sql = "
            SELECT DISTINCT pt.id, pt.judul, MIN(pp.tanggal) AS tanggal
            FROM pkl_tasks pt
            JOIN siswa_pkl sp ON sp.siswa_id = pt.siswa_id AND sp.tahun_ajaran = ?
            JOIN pkl_progress pp ON pp.task_id = pt.id AND pp.deleted_at IS NULL
            WHERE pt.siswa_id = ? AND pt.deleted_at IS NULL AND sp.tempat_pkl_id = ?
        ";
        $params = [get_active_tahun_ajaran(), $siswaId, $instruktur['tempat_pkl_id']];

        if (!empty($week)) {
            $startDate = get_jurnal_pkl_start_date();
            $weekRange = get_week_range($startDate, (int) $week);
            $sql .= " AND pp.tanggal >= ? AND pp.tanggal <= ?";
            $params[] = $weekRange['start'];
            $params[] = $weekRange['end'];
        }

        $sql .= " GROUP BY pt.id, pt.judul ORDER BY tanggal ASC";

        // Add status_label based on whether catatan_instruktur exists
        $tasks = $db->query($sql, $params)->getResultArray();
        foreach ($tasks as &$task) {
            $progressSql = "
                SELECT 
                    SUM(CASE WHEN pp.catatan_instruktur IS NOT NULL AND pp.catatan_instruktur != '' THEN 1 ELSE 0 END) AS ada_catatan,
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

    public function getFilteredProgress(int $siswaId)
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $week = $this->request->getGet('week');
        $taskId = $this->request->getGet('task_id');

        if (empty($week) || empty($taskId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Filter week dan task wajib dipilih']);
        }

        $db = \Config\Database::connect();
        $startDate = get_jurnal_pkl_start_date();
        $weekRange = get_week_range($startDate, (int) $week);

        $progress = $db->query("
            SELECT pp.*, pt.judul AS task_judul, pt.siswa_id
            FROM pkl_progress pp
            JOIN pkl_tasks pt ON pt.id = pp.task_id AND pt.deleted_at IS NULL
            JOIN siswa_pkl sp ON sp.siswa_id = pt.siswa_id AND sp.tahun_ajaran = ?
            WHERE pt.siswa_id = ?
              AND pp.task_id = ?
              AND pp.tanggal >= ? AND pp.tanggal <= ?
              AND pp.deleted_at IS NULL
              AND sp.tempat_pkl_id = ?
            ORDER BY pp.tanggal DESC
        ", [get_active_tahun_ajaran(), $siswaId, $taskId, $weekRange['start'], $weekRange['end'], $instruktur['tempat_pkl_id']])->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $progress]);
    }

    public function getWeekInfo()
    {
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

    public function allProgress()
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login')->with('error', 'Sesi Anda telah habis atau data instruktur tidak ditemukan.');
        }

        $tahunAjaran = get_active_tahun_ajaran();
        $db = \Config\Database::connect();

        $siswaRekap = $db->query("
            SELECT
                s.id AS siswa_id,
                s.nama_lengkap,
                s.nis,
                k.nama_kelas,
                users.profile_photo,
                COUNT(pp.id) AS total_progress,
                SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS approved,
                SUM(CASE WHEN pp.status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                SUM(CASE WHEN pp.status = 'verified_by_instruktur' THEN 1 ELSE 0 END) AS verified,
                SUM(CASE WHEN pp.status = 'revision' THEN 1 ELSE 0 END) AS revision,
                MAX(pp.tanggal) AS last_activity
            FROM siswa_pkl sp
            JOIN siswa s ON s.id = sp.siswa_id AND s.deleted_at IS NULL
            LEFT JOIN kelas k ON k.id = s.kelas_id
            LEFT JOIN users ON users.id = s.user_id
            LEFT JOIN pkl_tasks pt ON pt.siswa_id = s.id AND pt.deleted_at IS NULL
            LEFT JOIN pkl_progress pp ON pp.task_id = pt.id AND pp.deleted_at IS NULL
            WHERE sp.tempat_pkl_id = ?
              AND sp.tahun_ajaran = ?
            GROUP BY s.id
            ORDER BY s.nama_lengkap ASC
        ", [$instruktur['tempat_pkl_id'], $tahunAjaran])->getResultArray();

        $total = array_sum(array_column($siswaRekap, 'total_progress'));

        $data = [
            'title'      => 'Rekapan Progress Siswa',
            'pageTitle'  => 'Rekapan Progress',
            'siswaRekap' => $siswaRekap,
            'total'      => $total,
        ];

        return view('instruktur/jurnal_pkl/all_progress', $data);
    }
}
