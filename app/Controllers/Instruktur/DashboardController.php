<?php

namespace App\Controllers\Instruktur;

use App\Controllers\BaseController;
use App\Models\InstrukturPklModel;
use App\Models\SiswaPklModel;
use App\Models\PklTaskModel;
use App\Models\PklProgressModel;
use App\Models\PembimbingPklModel;
use App\Models\TempatPklModel;

class DashboardController extends BaseController
{
    protected $instrukturPklModel;
    protected $siswaPklModel;
    protected $taskModel;
    protected $progressModel;
    protected $pembimbingPklModel;
    protected $tempatPklModel;

    public function __construct()
    {
        $this->instrukturPklModel = new InstrukturPklModel();
        $this->siswaPklModel = new SiswaPklModel();
        $this->taskModel = new PklTaskModel();
        $this->progressModel = new PklProgressModel();
        $this->pembimbingPklModel = new PembimbingPklModel();
        $this->tempatPklModel = new TempatPklModel();
    }

    public function index()
    {
        $userId = session()->get('userId');
        $instrukturId = session()->get('instruktur_id');

        $instruktur = $this->instrukturPklModel->find($instrukturId);

        if (!$instruktur) {
            session()->setFlashdata('error', 'Data instruktur tidak ditemukan');
            return redirect()->to('/login');
        }

        $tempatPkl = $this->tempatPklModel->find($instruktur['tempat_pkl_id']);
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

        $recentProgress = [];
        $statsProgress = ['total' => 0, 'submitted' => 0, 'approved' => 0, 'revision' => 0];

        if (!empty($siswaIds)) {
            $db = \Config\Database::connect();

            $recentProgress = $db->query("
                SELECT pp.*, pt.judul AS nama_task, pt.siswa_id,
                       s.nama_lengkap AS nama_siswa, k.nama_kelas,
                       pc.nama AS kategori_nama
                FROM pkl_progress pp
                JOIN pkl_tasks pt ON pt.id = pp.task_id
                JOIN siswa s ON s.id = pt.siswa_id
                LEFT JOIN kelas k ON k.id = s.kelas_id
                LEFT JOIN pkl_categories pc ON pc.id = pt.kategori_id
                WHERE pt.siswa_id IN (" . implode(',', $siswaIds) . ")
                  AND pp.deleted_at IS NULL AND pt.deleted_at IS NULL
                ORDER BY pp.tanggal DESC, pp.created_at DESC
                LIMIT 10
            ")->getResultArray();

            $statsRow = $db->query("
                SELECT
                    COUNT(pp.id) AS total,
                    SUM(CASE WHEN pp.status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                    SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS approved,
                    SUM(CASE WHEN pp.status = 'revision' THEN 1 ELSE 0 END) AS revision
                FROM pkl_progress pp
                JOIN pkl_tasks pt ON pt.id = pp.task_id
                WHERE pt.siswa_id IN (" . implode(',', $siswaIds) . ")
                  AND pp.deleted_at IS NULL AND pt.deleted_at IS NULL
            ")->getRowArray();

            $statsProgress = [
                'total' => (int) ($statsRow['total'] ?? 0),
                'submitted' => (int) ($statsRow['submitted'] ?? 0),
                'approved' => (int) ($statsRow['approved'] ?? 0),
                'revision' => (int) ($statsRow['revision'] ?? 0),
            ];
        }

        $pembimbingList = $this->pembimbingPklModel
            ->select('pembimbing_pkl.*, guru.nama_lengkap AS nama_guru, guru.nip')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id', 'left')
            ->where('pembimbing_pkl.tempat_pkl_id', $instruktur['tempat_pkl_id'])
            ->where('pembimbing_pkl.tahun_ajaran', $tahunAjaran)
            ->findAll();

        $data = [
            'title'             => 'Dashboard Instruktur PKL',
            'pageTitle'         => 'Dashboard',
            'pageDescription'   => 'Selamat datang, ' . esc($instruktur['nama_lengkap']),
            'user'              => $this->getUserData(),
            'instruktur'        => $instruktur,
            'tempatPkl'         => $tempatPkl,
            'siswaList'         => $siswaList,
            'recentProgress'    => $recentProgress,
            'statsProgress'     => $statsProgress,
            'pembimbingList'    => $pembimbingList,
            'totalSiswa'        => count($siswaList),
            'tahunAjaran'       => $tahunAjaran,
        ];

        return view('instruktur/dashboard', $data);
    }
}
