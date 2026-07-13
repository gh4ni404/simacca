<?php

namespace App\Controllers\Instruktur;

use App\Controllers\BaseController;
use App\Models\InstrukturPklModel;
use App\Models\SiswaPklModel;
use App\Models\JurnalPklModel;
use App\Models\PembimbingPklModel;
use App\Models\GuruModel;
use App\Models\TempatPklModel;

class DashboardController extends BaseController
{
    protected $instrukturPklModel;
    protected $siswaPklModel;
    protected $jurnalPklModel;
    protected $pembimbingPklModel;
    protected $guruModel;
    protected $tempatPklModel;

    public function __construct()
    {
        $this->instrukturPklModel = new InstrukturPklModel();
        $this->siswaPklModel = new SiswaPklModel();
        $this->jurnalPklModel = new JurnalPklModel();
        $this->pembimbingPklModel = new PembimbingPklModel();
        $this->guruModel = new GuruModel();
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

        $jurnalList = [];
        $statsJurnal = ['total' => 0, 'pending' => 0, 'disetujui' => 0, 'revisi' => 0];

        if (!empty($siswaIds)) {
            $jurnalList = $this->jurnalPklModel
                ->select('jurnal_pkl.*, siswa.nama_lengkap AS nama_siswa')
                ->join('siswa', 'siswa.id = jurnal_pkl.siswa_id', 'left')
                ->whereIn('jurnal_pkl.siswa_id', $siswaIds)
                ->orderBy('jurnal_pkl.tanggal', 'DESC')
                ->orderBy('jurnal_pkl.created_at', 'DESC')
                ->limit(10)
                ->findAll();

            $statsJurnal['total'] = $this->jurnalPklModel->whereIn('siswa_id', $siswaIds)->countAllResults();
            $statsJurnal['pending'] = $this->jurnalPklModel->whereIn('siswa_id', $siswaIds)->where('status', 'pending')->countAllResults();
            $statsJurnal['disetujui'] = $this->jurnalPklModel->whereIn('siswa_id', $siswaIds)->where('status', 'disetujui')->countAllResults();
            $statsJurnal['revisi'] = $this->jurnalPklModel->whereIn('siswa_id', $siswaIds)->where('status', 'revisi')->countAllResults();
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
            'jurnalList'        => $jurnalList,
            'statsJurnal'       => $statsJurnal,
            'pembimbingList'    => $pembimbingList,
            'totalSiswa'        => count($siswaList),
            'tahunAjaran'       => $tahunAjaran,
        ];

        return view('instruktur/dashboard', $data);
    }
}
