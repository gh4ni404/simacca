<?php

namespace App\Controllers\KetuaJurusan;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KetuaJurusanModel;

class DashboardController extends BaseController
{
    protected $guruModel;
    protected $kjModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->kjModel = new KetuaJurusanModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru || empty($guru['jurusan'])) {
            return redirect()->to('/access-denied')->with('error', 'Anda tidak terdaftar sebagai Ketua Jurusan');
        }

        $jurusan = $guru['jurusan'];

        // Get dashboard statistics
        $stats = $this->kjModel->getDashboardStats($jurusan);

        // Get kelas list for sidebar/filter
        $kelasList = $this->kjModel->getKelasByJurusan($jurusan);

        // Get siswa PKL list
        $siswaPklList = $this->kjModel->getSiswaPklByJurusan($jurusan);

        // Get recent jurnal (latest 10 progress entries)
        $recentJurnal = $this->kjModel->getJurnalPklByJurusan($jurusan, null, []);

        // Get absensi PKL (recent 5 sessions)
        $absensiPklList = $this->kjModel->getAbsensiPklByJurusan($jurusan);

        $data = [
            'title'         => 'Dashboard Ketua Jurusan',
            'guru'          => $guru,
            'jurusan'       => $jurusan,
            'stats'         => $stats,
            'kelasList'     => $kelasList,
            'siswaPklList'  => $siswaPklList,
            'recentJurnal'  => array_slice($recentJurnal, 0, 10),
            'absensiPklList' => array_slice($absensiPklList, 0, 5),
            'pageTitle'     => 'Dashboard',
        ];

        return view('ketua_jurusan/dashboard', $data);
    }
}
