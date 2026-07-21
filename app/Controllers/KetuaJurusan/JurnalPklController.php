<?php

namespace App\Controllers\KetuaJurusan;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KetuaJurusanModel;
use App\Models\PklProgressModel;
use App\Models\PklTaskModel;

class JurnalPklController extends BaseController
{
    protected $guruModel;
    protected $kjModel;
    protected $progressModel;
    protected $taskModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->kjModel = new KetuaJurusanModel();
        $this->progressModel = new PklProgressModel();
        $this->taskModel = new PklTaskModel();
    }

    /**
     * List all jurnal PKL grouped by siswa in this jurusan
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru || empty($guru['jurusan'])) {
            return redirect()->to('/access-denied')->with('error', 'Akses ditolak');
        }

        $jurusan = $guru['jurusan'];

        $filters = [
            'kelas_id'      => $this->request->getGet('kelas_id'),
            'status'        => $this->request->getGet('status'),
            'tanggal_start' => $this->request->getGet('tanggal_start'),
            'tanggal_end'   => $this->request->getGet('tanggal_end'),
            'search'        => $this->request->getGet('search'),
        ];

        // Clean empty filters
        $filters = array_filter($filters, fn($v) => $v !== null && $v !== '');

        $result = $this->kjModel->getJurnalPklGroupedBySiswa($jurusan, null, $filters);
        $grouped = $result['grouped'];
        $stats = $result['stats'];

        $kelasList = $this->kjModel->getKelasByJurusan($jurusan);

        $data = [
            'title'     => 'Jurnal PKL - ' . $jurusan,
            'guru'      => $guru,
            'jurusan'   => $jurusan,
            'grouped'   => $grouped,
            'stats'     => $stats,
            'kelasList' => $kelasList,
            'filters'   => $filters,
            'pageTitle' => 'Jurnal PKL',
        ];

        return view('ketua_jurusan/jurnal_pkl', $data);
    }

    /**
     * Detail jurnal/progress for a single siswa
     */
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
            return redirect()->to('/ketua-jurusan/jurnal-pkl');
        }

        $tasks = $siswaDetail['tasks'];
        $taskIds = array_column($tasks, 'id');

        $progressByTask = [];
        if (!empty($taskIds)) {
            foreach ($taskIds as $taskId) {
                $progressByTask[$taskId] = $this->progressModel->where('task_id', $taskId)
                    ->where('deleted_at', null)
                    ->orderBy('tanggal', 'ASC')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
            }
        }

        $data = [
            'title'          => 'Detail Jurnal PKL - ' . $siswaDetail['siswa']['nama_lengkap'],
            'guru'           => $guru,
            'jurusan'        => $jurusan,
            'siswa'          => $siswaDetail['siswa'],
            'pkl_info'       => $siswaDetail['pkl_info'],
            'tasks'          => $tasks,
            'progressByTask' => $progressByTask,
            'pageTitle'      => 'Detail Jurnal PKL',
        ];

        return view('ketua_jurusan/jurnal_pkl_detail', $data);
    }
}
