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

        $data = [
            'title' => 'Verifikasi Jurnal PKL',
            'guru' => $guru,
            'groupedData' => $grouped,
            'stats' => $stats,
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
}
