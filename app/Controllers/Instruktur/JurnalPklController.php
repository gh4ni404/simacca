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
            return redirect()->to('/login');
        }

        $tahunAjaran = get_active_tahun_ajaran();
        $siswaList = $this->siswaPklModel
            ->select('siswa_pkl.*, siswa.nama_lengkap, siswa.nis, kelas.nama_kelas')
            ->join('siswa', 'siswa.id = siswa_pkl.siswa_id AND siswa.deleted_at IS NULL', 'left')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
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
        }

        $data = [
            'title'       => 'Jurnal PKL - Instruktur',
            'pageTitle'   => 'Jurnal PKL',
            'siswaList'   => $siswaList,
            'siswaStats'  => $siswaStats,
        ];

        return view('instruktur/jurnal_pkl/index', $data);
    }

    public function siswaDetail(int $siswaId)
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login');
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
                   SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS approved_count
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
            return redirect()->to('/login');
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
            return redirect()->to('/login');
        }

        $catatan = $this->request->getPost('catatan_instruktur');
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

    public function verifikasiTask(int $taskId)
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();

        $task = $db->query("
            SELECT pt.*
            FROM pkl_tasks pt
            JOIN siswa_pkl sp ON sp.siswa_id = pt.siswa_id AND sp.tahun_ajaran = (SELECT `value` FROM `settings` WHERE `key` = 'tahun_ajaran_aktif')
            WHERE pt.id = ? AND sp.tempat_pkl_id = ? AND pt.deleted_at IS NULL
        ", [$taskId, $instruktur['tempat_pkl_id']])->getRowArray();

        if (!$task) {
            session()->setFlashdata('error', 'Task tidak ditemukan atau bukan wilayah Anda');
            return redirect()->to('/instruktur/jurnal-pkl');
        }

        if ($task['status'] !== 'completed') {
            session()->setFlashdata('error', 'Hanya task completed yang bisa diverifikasi');
            return redirect()->back();
        }

        $userId = session()->get('user_id');
        $db->table('pkl_tasks')
            ->where('id', $taskId)
            ->update([
                'status' => 'verified_by_instruktur',
                'instruktur_verified_by' => $userId,
                'instruktur_verified_at' => date('Y-m-d H:i:s'),
            ]);

        session()->setFlashdata('success', 'Task berhasil diverifikasi');
        return redirect()->back();
    }

    public function verifyProgress(int $progressId)
    {
        $instruktur = $this->getInstruktur();
        if (!$instruktur) {
            return redirect()->to('/login');
        }

        $status = $this->request->getPost('status');
        $catatan = $this->request->getPost('catatan_instruktur');

        if (!in_array($status, ['approved', 'revision'])) {
            session()->setFlashdata('error', 'Status verifikasi tidak valid');
            return redirect()->back();
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

        if ($progress['status'] !== 'submitted') {
            session()->setFlashdata('error', 'Progress ini sudah diverifikasi sebelumnya');
            return redirect()->back();
        }

        $userId = session()->get('user_id');
        $this->progressModel->update($progressId, [
            'status' => $status,
            'instruktur_verified_by' => $userId,
            'instruktur_verified_at' => date('Y-m-d H:i:s'),
            'catatan_instruktur' => $catatan,
        ]);

        $messages = [
            'approved' => 'Progress berhasil disetujui',
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

        if (!in_array($progress['status'], ['approved', 'revision'])) {
            session()->setFlashdata('error', 'Progress ini belum diverifikasi');
            return redirect()->back();
        }

        $this->progressModel->update($progressId, [
            'status' => 'submitted',
            'instruktur_verified_by' => null,
            'instruktur_verified_at' => null,
            'catatan_instruktur' => null,
        ]);

        session()->setFlashdata('success', 'Verifikasi progress berhasil dibatalkan');
        return redirect()->back();
    }
}
