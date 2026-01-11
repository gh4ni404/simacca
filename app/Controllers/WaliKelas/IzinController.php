<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\IzinSiswaModel;

class IzinController extends BaseController
{
    protected $guruModel;
    protected $kelasModel;
    protected $izinSiswaModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
        $this->izinSiswaModel = new IzinSiswaModel();
    }

    public function index()
    {
        // Get guru data
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru || !$guru['is_wali_kelas']) {
            return redirect()->to('/access-denied')->with('error', 'Anda bukan wali kelas');
        }

        // Get kelas data
        $kelas = $this->kelasModel->getByWaliKelas($guru['id']);

        if (!$kelas) {
            return redirect()->to('/access-denied')->with('error', 'Anda belum ditugaskan sebagai wali kelas');
        }

        // Get filter status
        $status = $this->request->getGet('status') ?? null;

        // Get izin data
        $izinData = $this->izinSiswaModel->getByKelas($kelas['id'], $status);

        // Count by status
        $countPending = $this->izinSiswaModel->getByKelas($kelas['id'], 'pending');
        $countDisetujui = $this->izinSiswaModel->getByKelas($kelas['id'], 'disetujui');
        $countDitolak = $this->izinSiswaModel->getByKelas($kelas['id'], 'ditolak');

        $data = [
            'title' => 'Persetujuan Izin Siswa',
            'guru' => $guru,
            'kelas' => $kelas,
            'izinData' => $izinData,
            'status' => $status,
            'countPending' => count($countPending),
            'countDisetujui' => count($countDisetujui),
            'countDitolak' => count($countDitolak)
        ];

        return view('walikelas/izin/index', $data);
    }

    public function approve($id)
    {
        $userId = session()->get('user_id');
        $catatan = $this->request->getPost('catatan');

        $result = $this->izinSiswaModel->approveIzin($id, $userId, $catatan);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Izin berhasil disetujui'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Gagal menyetujui izin'
        ]);
    }

    public function reject($id)
    {
        $userId = session()->get('user_id');
        $catatan = $this->request->getPost('catatan');

        $result = $this->izinSiswaModel->rejectIzin($id, $userId, $catatan);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Izin berhasil ditolak'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Gagal menolak izin'
        ]);
    }
}
