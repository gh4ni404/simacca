<?php

namespace App\Controllers\KetuaJurusan;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KetuaJurusanModel;

class SiswaPklController extends BaseController
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
            return redirect()->to('/access-denied')->with('error', 'Akses ditolak');
        }

        $jurusan = $guru['jurusan'];

        $siswaPklList = $this->kjModel->getSiswaPklByJurusan($jurusan);
        $kelasList = $this->kjModel->getKelasByJurusan($jurusan);

        // Enrich with task/progress stats
        $db = \Config\Database::connect();
        foreach ($siswaPklList as &$siswa) {
            $stats = $db->table('pkl_tasks')
                ->select('
                    COUNT(DISTINCT CASE WHEN pkl_progress.id IS NOT NULL THEN pkl_tasks.id END) AS total_tasks,
                    COUNT(pkl_progress.id) AS total_progress,
                    SUM(CASE WHEN pkl_progress.status = \'approved\' THEN 1 ELSE 0 END) AS approved,
                    SUM(CASE WHEN pkl_progress.status = \'submitted\' THEN 1 ELSE 0 END) AS submitted,
                    SUM(CASE WHEN pkl_progress.status = \'revision\' THEN 1 ELSE 0 END) AS revision
                ')
                ->join('pkl_progress', 'pkl_progress.task_id = pkl_tasks.id AND pkl_progress.deleted_at IS NULL', 'left')
                ->where('pkl_tasks.siswa_id', $siswa['siswa_id'])
                ->where('pkl_tasks.deleted_at', null)
                ->get()
                ->getRowArray();

            $siswa['total_tasks'] = (int)($stats['total_tasks'] ?? 0);
            $siswa['total_progress'] = (int)($stats['total_progress'] ?? 0);
            $siswa['approved'] = (int)($stats['approved'] ?? 0);
            $siswa['submitted'] = (int)($stats['submitted'] ?? 0);
            $siswa['revision'] = (int)($stats['revision'] ?? 0);
        }
        unset($siswa);

        $data = [
            'title'        => 'Siswa PKL - ' . $jurusan,
            'guru'         => $guru,
            'jurusan'      => $jurusan,
            'siswaPklList' => $siswaPklList,
            'kelasList'    => $kelasList,
            'pageTitle'    => 'Siswa PKL',
        ];

        return view('ketua_jurusan/siswa_pkl', $data);
    }

    public function detail(int $siswaId)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru || empty($guru['jurusan'])) {
            return redirect()->to('/access-denied')->with('error', 'Akses ditolak');
        }

        $jurusan = $guru['jurusan'];

        $siswaDetail = $this->kjModel->getSiswaDetailForJurusan($siswaId, $jurusan);

        if (!$siswaDetail) {
            session()->setFlashdata('error', 'Siswa tidak ditemukan di jurusan ini');
            return redirect()->to('/ketua-jurusan/siswa-pkl');
        }

        $data = [
            'title'     => 'Detail Siswa PKL - ' . $siswaDetail['siswa']['nama_lengkap'],
            'guru'      => $guru,
            'jurusan'   => $jurusan,
            'siswa'     => $siswaDetail['siswa'],
            'pkl_info'  => $siswaDetail['pkl_info'],
            'tasks'     => $siswaDetail['tasks'],
            'pageTitle' => 'Detail Siswa PKL',
        ];

        return view('ketua_jurusan/siswa_pkl_detail', $data);
    }
}
