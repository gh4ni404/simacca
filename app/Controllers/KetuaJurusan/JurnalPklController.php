<?php

namespace App\Controllers\KetuaJurusan;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KetuaJurusanModel;
use App\Models\PklProgressModel;
use App\Models\PklTaskModel;
use App\Services\PklService;

class JurnalPklController extends BaseController
{
    protected $guruModel;
    protected $kjModel;
    protected $progressModel;
    protected $taskModel;
    protected $pklService;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->kjModel = new KetuaJurusanModel();
        $this->progressModel = new PklProgressModel();
        $this->taskModel = new PklTaskModel();
        $this->pklService = new PklService();
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

    /**
     * Cancel verification for approved journals without catatan (by Ketua Jurusan)
     */
    public function cancelVerification(int $progressId)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru || empty($guru['jurusan'])) {
            return redirect()->to('/access-denied')->with('error', 'Akses ditolak');
        }

        $progress = $this->progressModel->find($progressId);
        if (!$progress) {
            session()->setFlashdata('error', 'Progress tidak ditemukan');
            return redirect()->back();
        }

        $task = $this->taskModel->find($progress['task_id']);
        if (!$task) {
            session()->setFlashdata('error', 'Task tidak ditemukan');
            return redirect()->back();
        }

        $db = \Config\Database::connect();
        $siswa = $db->table('siswa')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->where('siswa.id', $task['siswa_id'])
            ->where('kelas.jurusan', $guru['jurusan'])
            ->where('siswa.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$siswa) {
            session()->setFlashdata('error', 'Siswa tidak ditemukan di jurusan ini');
            return redirect()->back();
        }

        $result = $this->pklService->cancelVerificationForKetuaJurusan($progressId);

        if ($result['success']) {
            $updatedProgress = $this->progressModel->find($progressId);
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $result['data']['message'] ?? 'Berhasil',
                    'csrf_token' => csrf_hash(),
                    'progress' => [
                        'status' => $updatedProgress['status'] ?? null,
                        'verified_by' => $updatedProgress['verified_by'] ?? null,
                        'catatan_pembimbing' => $updatedProgress['catatan_pembimbing'] ?? null,
                        'instruktur_verified_by' => $updatedProgress['instruktur_verified_by'] ?? null,
                        'catatan_instruktur' => $updatedProgress['catatan_instruktur'] ?? null,
                    ]
                ]);
            }
            session()->setFlashdata('success', 'Verifikasi progress berhasil dibatalkan');
        } else {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $result['message']]);
            }
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->back();
    }

    /**
     * Add catatan or verify on behalf of pembimbing or instruktur (by Ketua Jurusan)
     */
    public function addCatatan(int $progressId)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru || empty($guru['jurusan'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
            }
            return redirect()->to('/access-denied')->with('error', 'Akses ditolak');
        }

        $role = $this->request->getPost('role');
        $action = $this->request->getPost('action') ?? 'add_catatan';
        $catatan = trim($this->request->getPost('catatan') ?? '');

        if (!in_array($role, ['pembimbing', 'instruktur'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Role tidak valid']);
            }
            session()->setFlashdata('error', 'Role tidak valid');
            return redirect()->back();
        }

        if (!in_array($action, ['verify', 'add_catatan', 'edit_catatan'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Action tidak valid']);
            }
            session()->setFlashdata('error', 'Action tidak valid');
            return redirect()->back();
        }

        if ($catatan === '') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Catatan wajib diisi']);
            }
            session()->setFlashdata('error', 'Catatan wajib diisi');
            return redirect()->back()->withInput();
        }

        if (mb_strlen($catatan) > 200) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Catatan maksimal 200 karakter']);
            }
            session()->setFlashdata('error', 'Catatan maksimal 200 karakter');
            return redirect()->back()->withInput();
        }

        $progress = $this->progressModel->find($progressId);
        if (!$progress) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Progress tidak ditemukan']);
            }
            session()->setFlashdata('error', 'Progress tidak ditemukan');
            return redirect()->back();
        }

        $task = $this->taskModel->find($progress['task_id']);
        if (!$task) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Task tidak ditemukan']);
            }
            session()->setFlashdata('error', 'Task tidak ditemukan');
            return redirect()->back();
        }

        $db = \Config\Database::connect();
        $siswa = $db->table('siswa')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->where('siswa.id', $task['siswa_id'])
            ->where('kelas.jurusan', $guru['jurusan'])
            ->where('siswa.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$siswa) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Siswa tidak ditemukan di jurusan ini']);
            }
            session()->setFlashdata('error', 'Siswa tidak ditemukan di jurusan ini');
            return redirect()->back();
        }

        if ($action === 'verify') {
            $result = $this->pklService->verifyOnBehalf($progressId, $role, $catatan, $userId);
        } elseif ($action === 'edit_catatan') {
            $result = $this->pklService->editCatatanOnBehalf($progressId, $role, $catatan);
        } else {
            $result = $this->pklService->addCatatanOnBehalf($progressId, $role, $catatan);
        }

        if ($result['success']) {
            $updatedProgress = $this->progressModel->find($progressId);
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $result['data']['message'] ?? 'Berhasil',
                    'csrf_token' => csrf_hash(),
                    'progress' => [
                        'verified_by' => $updatedProgress['verified_by'] ?? null,
                        'catatan_pembimbing' => $updatedProgress['catatan_pembimbing'] ?? null,
                        'instruktur_verified_by' => $updatedProgress['instruktur_verified_by'] ?? null,
                        'catatan_instruktur' => $updatedProgress['catatan_instruktur'] ?? null,
                    ]
                ]);
            }
            $roleLabel = ($role === 'pembimbing') ? 'Pembimbing' : 'Instruktur';
            $actionLabel = ($action === 'verify') ? 'Verifikasi' : (($action === 'edit_catatan') ? 'Edit catatan' : 'Catatan');
            session()->setFlashdata('success', $actionLabel . ' ' . $roleLabel . ' berhasil ditambahkan');
        } else {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $result['message']]);
            }
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->back();
    }
}
