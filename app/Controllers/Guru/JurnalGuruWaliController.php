<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Services\JurnalGuruWaliService;
use App\Models\GuruModel;

class JurnalGuruWaliController extends BaseController
{
    protected $jurnalService;
    protected $guruModel;

    public function __construct()
    {
        $this->jurnalService = new JurnalGuruWaliService();
        $this->guruModel     = new GuruModel();
    }

    /**
     * Get authenticated guru data from current session
     */
    private function getGuruDataOrRedirect()
    {
        $userId = session()->get('user_id') ?? session()->get('userId');
        if (!$userId) {
            return null;
        }

        $guru = $this->guruModel->getByUserId($userId);
        return $guru;
    }

    /**
     * Display main Jurnal Guru Wali page
     */
    public function index()
    {
        $guru = $this->getGuruDataOrRedirect();
        if (!$guru) {
            return redirect()->to('/login')->with('error', 'Sesi Anda telah berakhir atau data Guru tidak ditemukan.');
        }

        $filters = [
            'siswa_id'        => $this->request->getGet('siswa_id'),
            'jenis_bimbingan' => $this->request->getGet('jenis_bimbingan'),
            'start_date'      => $this->request->getGet('start_date'),
            'end_date'        => $this->request->getGet('end_date'),
            'search'          => $this->request->getGet('search'),
        ];

        $siswaBinaan = $this->jurnalService->getSiswaBinaan((int) $guru['id']);
        $jurnalList  = $this->jurnalService->getJurnalList((int) $guru['id'], $filters);

        $data = [
            'title'       => 'Jurnal Guru Wali',
            'guru'        => $guru,
            'siswaBinaan' => $siswaBinaan,
            'jurnalList'  => $jurnalList,
            'filters'     => $filters,
            'totalSiswa'  => count($siswaBinaan),
            'totalJurnal' => count($jurnalList),
        ];

        return view('guru/jurnal_wali/index', $data);
    }

    /**
     * Store new journal entry
     */
    public function store()
    {
        $guru = $this->getGuruDataOrRedirect();
        if (!$guru) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Sesi login tidak valid.',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
            ]);
        }

        $payload = [
            'guru_id'         => (int) $guru['id'],
            'siswa_id'        => (int) $this->request->getPost('siswa_id'),
            'tanggal'         => $this->request->getPost('tanggal') ?: date('Y-m-d'),
            'jenis_bimbingan' => $this->request->getPost('jenis_bimbingan') ?: 'Akademik',
            'catatan'         => $this->request->getPost('catatan'),
            'tindak_lanjut'   => $this->request->getPost('tindak_lanjut'),
            'tahun_ajaran'    => get_active_tahun_ajaran(),
        ];

        $file = $this->request->getFile('foto_dokumentasi');

        $result = $this->jurnalService->createJurnal($payload, $file);
        $result['csrf_token'] = csrf_token();
        $result['csrf_hash']  = csrf_hash();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/guru/jurnal-wali');
    }

    /**
     * Update existing journal entry
     */
    public function update($id)
    {
        $guru = $this->getGuruDataOrRedirect();
        if (!$guru) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Sesi login tidak valid.',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
            ]);
        }

        $payload = [
            'siswa_id'        => (int) $this->request->getPost('siswa_id'),
            'tanggal'         => $this->request->getPost('tanggal'),
            'jenis_bimbingan' => $this->request->getPost('jenis_bimbingan'),
            'catatan'         => $this->request->getPost('catatan'),
            'tindak_lanjut'   => $this->request->getPost('tindak_lanjut'),
        ];

        $file = $this->request->getFile('foto_dokumentasi');
        $hapusFoto = (bool) $this->request->getPost('hapus_foto');

        $result = $this->jurnalService->updateJurnal((int) $id, (int) $guru['id'], $payload, $file, $hapusFoto);
        $result['csrf_token'] = csrf_token();
        $result['csrf_hash']  = csrf_hash();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/guru/jurnal-wali');
    }

    /**
     * Delete journal entry
     */
    public function delete($id)
    {
        $guru = $this->getGuruDataOrRedirect();
        if (!$guru) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Sesi login tidak valid.',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
            ]);
        }

        $result = $this->jurnalService->deleteJurnal((int) $id, (int) $guru['id']);
        $result['csrf_token'] = csrf_token();
        $result['csrf_hash']  = csrf_hash();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/guru/jurnal-wali');
    }

    /**
     * Printable PDF / Preview Page for Jurnal Guru Wali
     */
    public function print()
    {
        $guru = $this->getGuruDataOrRedirect();
        if (!$guru) {
            return redirect()->to('/login')->with('error', 'Sesi tidak valid.');
        }

        $filters = [
            'siswa_id'        => $this->request->getGet('siswa_id'),
            'jenis_bimbingan' => $this->request->getGet('jenis_bimbingan'),
            'start_date'      => $this->request->getGet('start_date'),
            'end_date'        => $this->request->getGet('end_date'),
        ];

        $res = $this->jurnalService->getPrintData((int) $guru['id'], $filters);
        if (!$res['success']) {
            session()->setFlashdata('error', $res['message']);
            return redirect()->to('/guru/jurnal-wali');
        }

        $data = $res['data'];
        $data['title'] = 'Cetak Jurnal Guru Wali - ' . ($data['guru']['nama_lengkap'] ?? 'Guru');

        return view('guru/jurnal_wali/print', $data);
    }
}
