<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Services\JurnalPklService;
use App\Models\GuruModel;

class JurnalPklController extends BaseController
{
    protected $guruModel;
    protected $jurnalService;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->jurnalService = new JurnalPklService();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/access-denied')->with('error', 'Data guru tidak ditemukan');
        }

        $result = $this->jurnalService->getPendingByPembimbing($guru['id']);
        $pendingData = $result['success'] ? $result['data'] : [];

        $data = [
            'title' => 'Verifikasi Jurnal PKL',
            'guru' => $guru,
            'pendingData' => $pendingData,
        ];

        return view('guru/jurnal_pkl/index', $data);
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

        if (!in_array($status, ['disetujui', 'revisi', 'ditolak'])) {
            session()->setFlashdata('error', 'Status verifikasi tidak valid');
            return redirect()->to('/guru/jurnal-pkl');
        }

        $result = $this->jurnalService->verify($id, $userId, $status, $catatan);

        if ($result['success']) {
            $messages = [
                'disetujui' => 'Jurnal berhasil disetujui ✅',
                'revisi' => 'Jurnal direvisi ✏️',
                'ditolak' => 'Jurnal ditolak ❌',
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

        $result = $this->jurnalService->getById($id);
        if (!$result['success']) {
            session()->setFlashdata('error', 'Jurnal tidak ditemukan');
            return redirect()->to('/guru/jurnal-pkl');
        }

        $data = [
            'title' => 'Detail Jurnal PKL',
            'guru' => $guru,
            'jurnal' => $result['data'],
        ];

        return view('guru/jurnal_pkl/detail', $data);
    }
}
