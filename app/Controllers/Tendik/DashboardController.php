<?php

namespace App\Controllers\Tendik;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;

class DashboardController extends BaseController
{
    protected $guruModel;
    protected $siswaModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->guruModel  = new GuruModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
    }

    public function index()
    {
        $activeTA = get_active_tahun_ajaran();

        $totalSiswa = $this->siswaModel->where('tahun_ajaran', $activeTA)->where('deleted_at', null)->countAllResults();
        $totalKelas = $this->kelasModel->where('tahun_ajaran', $activeTA)->countAllResults();

        $data = [
            'title'           => 'Dashboard Tenaga Pendidik / Staf',
            'pageTitle'       => 'Dashboard Tenaga Pendidik & Staf',
            'pageDescription' => 'Portal Layanan Administration Tenaga Pendidik & Staf SIMACCA',
            'user'            => $this->getUserData(),
            'activeTA'        => $activeTA,
            'stats'           => [
                'total_siswa' => $totalSiswa,
                'total_kelas' => $totalKelas,
            ]
        ];

        return view('tendik/dashboard', $data);
    }
}
