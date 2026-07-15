<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Services\PklService;
use App\Models\GuruModel;

class PklController extends BaseController
{
    protected $guruModel;
    protected $pklService;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->pklService = new PklService();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/access-denied')->with('error', 'Data guru tidak ditemukan');
        }

        $result = $this->pklService->getGroupedBySiswaForPembimbing();
        $grouped = $result['success'] ? $result['data']['grouped'] : [];
        $stats = $result['success'] ? $result['data']['stats'] : [];

        $db = \Config\Database::connect();

        $tasks = $db->query("
            SELECT pt.*, pc.nama AS kategori_nama,
                   s.nama_lengkap AS nama_siswa, s.nis,
                   k.nama_kelas,
                   ip.nama_lengkap AS nama_instruktur,
                   tp.nama_perusahaan
            FROM pkl_tasks pt
            LEFT JOIN pkl_categories pc ON pc.id = pt.kategori_id
            JOIN siswa s ON s.id = pt.siswa_id AND s.deleted_at IS NULL
            LEFT JOIN kelas k ON k.id = s.kelas_id
            JOIN siswa_pkl sp ON sp.siswa_id = s.id AND sp.tahun_ajaran = (SELECT `value` FROM `settings` WHERE `key` = 'tahun_ajaran_aktif')
            JOIN pembimbing_pkl pp ON pp.tempat_pkl_id = sp.tempat_pkl_id AND pp.tahun_ajaran = sp.tahun_ajaran
            LEFT JOIN tempat_pkl tp ON tp.id = sp.tempat_pkl_id
            LEFT JOIN instruktur_pkl ip ON ip.user_id = pt.instruktur_verified_by AND ip.deleted_at IS NULL
            WHERE pp.guru_id = ?
              AND pt.status = 'verified_by_instruktur'
              AND pt.deleted_at IS NULL
            ORDER BY pt.instruktur_verified_at DESC
        ", [$guru['id']])->getResultArray();

        $data = [
            'title' => 'Verifikasi Jurnal PKL',
            'guru' => $guru,
            'groupedData' => $grouped,
            'stats' => $stats,
            'tasks' => $tasks,
        ];

        return view('guru/pkl/index', $data);
    }

    public function verify($id)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/access-denied')->with('error', 'Data guru tidak ditemukan');
        }

        $status = $this->request->getPost('status');
        $catatan = $this->request->getPost('catatan');

        if (!in_array($status, ['approved', 'revision'])) {
            session()->setFlashdata('error', 'Status verifikasi tidak valid');
            return redirect()->to('/guru/jurnal-pkl');
        }

        $result = $this->pklService->verify($id, $userId, $status, $catatan);

        if ($result['success']) {
            $messages = [
                'approved' => 'Progress berhasil disetujui',
                'revision' => 'Progress direvisi',
            ];
            session()->setFlashdata('success', $messages[$status]);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/guru/jurnal-pkl');
    }

    public function detail($id)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/access-denied')->with('error', 'Data guru tidak ditemukan');
        }

        $result = $this->pklService->getProgressById($id);
        if (!$result['success']) {
            session()->setFlashdata('error', 'Progress tidak ditemukan');
            return redirect()->to('/guru/jurnal-pkl');
        }

        $data = [
            'title' => 'Detail Progress PKL',
            'guru' => $guru,
            'progress' => $result['data'],
        ];

        return view('guru/pkl/detail', $data);
    }

    public function cancelVerification($id)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/access-denied')->with('error', 'Data guru tidak ditemukan');
        }

        $result = $this->pklService->cancelVerification($id);

        if ($result['success']) {
            session()->setFlashdata('success', 'Verifikasi progress berhasil dibatalkan');
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/guru/jurnal-pkl');
    }

    public function taskVerification()
    {
        return redirect()->to('/guru/jurnal-pkl');
    }

    public function verifikasiTaskPembimbing($id)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/access-denied')->with('error', 'Data guru tidak ditemukan');
        }

        $db = \Config\Database::connect();

        $task = $db->query("
            SELECT pt.*
            FROM pkl_tasks pt
            JOIN siswa_pkl sp ON sp.siswa_id = pt.siswa_id AND sp.tahun_ajaran = (SELECT `value` FROM `settings` WHERE `key` = 'tahun_ajaran_aktif')
            JOIN pembimbing_pkl pp ON pp.tempat_pkl_id = sp.tempat_pkl_id AND pp.tahun_ajaran = sp.tahun_ajaran
            WHERE pt.id = ? AND pp.guru_id = ? AND pt.deleted_at IS NULL
        ", [$id, $guru['id']])->getRowArray();

        if (!$task) {
            session()->setFlashdata('error', 'Task tidak ditemukan');
            return redirect()->to('/guru/jurnal-pkl');
        }

        if ($task['status'] !== 'verified_by_instruktur') {
            session()->setFlashdata('error', 'Hanya task verified instruktur yang bisa diverifikasi');
            return redirect()->back();
        }

        $db->table('pkl_tasks')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'pembimbing_verified_by' => $userId,
                'pembimbing_verified_at' => date('Y-m-d H:i:s'),
            ]);

        session()->setFlashdata('success', 'Task berhasil disetujui');
        return redirect()->to('/guru/jurnal-pkl');
    }
}
