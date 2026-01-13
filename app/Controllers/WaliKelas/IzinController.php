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
        log_message('info', '[WALI KELAS IZIN] Index started');
        
        // Get guru data
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        log_message('info', '[WALI KELAS IZIN] User ID: ' . $userId);
        log_message('info', '[WALI KELAS IZIN] Guru found: ' . ($guru ? $guru['id'] : 'none'));

        if (!$guru || !$guru['is_wali_kelas']) {
            log_message('warning', '[WALI KELAS IZIN] Not a wali kelas');
            session()->setFlashdata('error', 'Sorry, kamu bukan wali kelas 🙅‍♂️');
            return redirect()->to('/access-denied');
        }

        // Get kelas data
        $kelas = $this->kelasModel->getByWaliKelas($guru['id']);

        log_message('info', '[WALI KELAS IZIN] Kelas found: ' . ($kelas ? $kelas['id'] . ' - ' . $kelas['nama_kelas'] : 'none'));

        if (!$kelas) {
            log_message('warning', '[WALI KELAS IZIN] No kelas assigned');
            session()->setFlashdata('error', 'Kamu belum jadi wali kelas nih 👨‍🏫');
            return redirect()->to('/walikelas/dashboard');
        }

        // Get filter status
        $status = $this->request->getGet('status') ?? null;

        log_message('info', '[WALI KELAS IZIN] Filter status: ' . ($status ?? 'all'));

        // Get izin data
        $izinData = $this->izinSiswaModel->getByKelas($kelas['id'], $status);

        log_message('info', '[WALI KELAS IZIN] Total izin found: ' . count($izinData));

        // Count by status
        $countPending = $this->izinSiswaModel->getByKelas($kelas['id'], 'pending');
        $countDisetujui = $this->izinSiswaModel->getByKelas($kelas['id'], 'disetujui');
        $countDitolak = $this->izinSiswaModel->getByKelas($kelas['id'], 'ditolak');

        log_message('info', '[WALI KELAS IZIN] Count - Pending: ' . count($countPending) . ', Disetujui: ' . count($countDisetujui) . ', Ditolak: ' . count($countDitolak));

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
        log_message('info', '[WALI KELAS IZIN] Approve started - ID: ' . $id);
        
        $userId = session()->get('user_id');
        $catatan = $this->request->getPost('catatan');

        $result = $this->izinSiswaModel->approveIzin($id, $userId, $catatan);

        if ($result) {
            log_message('info', '[WALI KELAS IZIN] Approve successful');
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Izin disetujui! Nice decision 👍✨'
            ]);
        }

        log_message('error', '[WALI KELAS IZIN] Approve failed');
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Oops, gagal approve izin 😅'
        ]);
    }

    public function reject($id)
    {
        log_message('info', '[WALI KELAS IZIN] Reject started - ID: ' . $id);
        
        $userId = session()->get('user_id');
        $catatan = $this->request->getPost('catatan');

        $result = $this->izinSiswaModel->rejectIzin($id, $userId, $catatan);

        if ($result) {
            log_message('info', '[WALI KELAS IZIN] Reject successful');
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Izin ditolak. Hope you understand 🤝'
            ]);
        }

        log_message('error', '[WALI KELAS IZIN] Reject failed');
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Hmm, gagal reject izin 😕'
        ]);
    }
}
