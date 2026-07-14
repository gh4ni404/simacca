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
            WHERE pt.id = ? AND pt.deleted_at IS NULL
        ", [$taskId])->getRowArray();

        if (!$task) {
            session()->setFlashdata('error', 'Task tidak ditemukan');
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

        $this->progressModel->update($progressId, [
            'catatan_instruktur' => $catatan,
        ]);

        session()->setFlashdata('success', 'Catatan berhasil disimpan');
        return redirect()->back();
    }
}
