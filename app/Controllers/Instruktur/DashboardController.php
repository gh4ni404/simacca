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
            return redirect()->to('/login')->with('error', 'Data instruktur tidak ditemukan.');
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
        $allProgress = [];
        $statsProgress = ['total' => 0, 'submitted' => 0, 'approved' => 0, 'revision' => 0];
        $pendingProgress = [];

        if (!empty($siswaIds)) {
            $allProgress     = $this->progressModel->getBySiswaIds($siswaIds);
            $recentProgress  = array_slice($allProgress, 0, 10);
            $statsProgress   = $this->progressModel->getStatsBySiswaIds($siswaIds);
            $pendingProgress = $this->progressModel->getPendingBySiswaIds($siswaIds);
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
            'allProgress'       => $allProgress,
            'statsProgress'     => $statsProgress,
            'pembimbingList'    => $pembimbingList,
            'totalSiswa'        => count($siswaList),
            'tahunAjaran'       => $tahunAjaran,
            'pendingProgress'   => $pendingProgress,
        ];

        return view('instruktur/dashboard', $data);
    }
}
