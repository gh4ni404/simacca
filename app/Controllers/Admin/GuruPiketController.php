<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\GuruPiketService;

class GuruPiketController extends BaseController
{
    protected $guruPiketService;

    public function __construct()
    {
        $this->guruPiketService = new GuruPiketService();

        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/access-denied');
        }
    }

    /**
     * Display guru piket schedule grouped by day
     */
    public function index()
    {
        $tahunAjaran = get_active_tahun_ajaran();
        $semester = $this->request->getGet('semester') ?: 'ganjil';
        $result = $this->guruPiketService->getAllGroupedByHari($tahunAjaran, $semester);

        $masterJobdeskModel = new \App\Models\MasterJobdeskPiketModel();
        $masterJobdeskList = $masterJobdeskModel->where('is_active', 1)->orderBy('nama_jobdesk', 'ASC')->findAll();

        $data = [
            'title'               => 'Manajemen Guru Piket',
            'pageTitle'           => 'Jadwal Guru Piket',
            'pageDescription'     => 'Kelola jadwal piket guru berdasarkan hari',
            'user'                => $this->getUserData(),
            'tahunAjaran'         => $tahunAjaran,
            'semester'            => $semester,
            'grouped'             => $result['data']['grouped'] ?? [],
            'stats'               => $result['data']['stats'] ?? [],
            'hariList'            => ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'],
            'defaultRincianTugas' => $this->guruPiketService->getDefaultRincianTugas(),
            'masterJobdeskList'   => $masterJobdeskList,
        ];

        return view('admin/guru_piket/index', $data);
    }

    /**
     * Store new guru piket assignment
     */
    public function store()
    {
        $tahunAjaran = get_active_tahun_ajaran();

        $data = [
            'guru_id'      => $this->request->getPost('guru_id'),
            'jobdesk_id'   => $this->request->getPost('jobdesk_id'),
            'tahun_ajaran' => $tahunAjaran,
            'semester'     => $this->request->getPost('semester'),
            'hari'          => $this->request->getPost('hari'),
            'keterangan'    => $this->request->getPost('keterangan'),
            'rincian_tugas' => $this->request->getPost('rincian_tugas'),
            'is_active'     => $this->request->getPost('is_active') ?? 1,
        ];

        $result = $this->guruPiketService->create($data);

        // Return JSON for AJAX requests
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
            if (!empty($result['errors'])) {
                session()->setFlashdata('errors', $result['errors']);
            }
            return redirect()->back()->withInput();
        }

        session()->setFlashdata('success', 'Jadwal piket berhasil ditambahkan');
        return redirect()->to('/admin/guru-piket');
    }

    /**
     * Update guru piket assignment
     */
    public function update($id)
    {
        $tahunAjaran = get_active_tahun_ajaran();

        $data = [
            'guru_id'      => $this->request->getPost('guru_id'),
            'jobdesk_id'   => $this->request->getPost('jobdesk_id'),
            'tahun_ajaran' => $tahunAjaran,
            'semester'     => $this->request->getPost('semester'),
            'hari'          => $this->request->getPost('hari'),
            'keterangan'    => $this->request->getPost('keterangan'),
            'rincian_tugas' => $this->request->getPost('rincian_tugas'),
            'is_active'     => $this->request->getPost('is_active') ?? 1,
        ];

        $result = $this->guruPiketService->update($id, $data);

        // Return JSON for AJAX requests
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
            if (!empty($result['errors'])) {
                session()->setFlashdata('errors', $result['errors']);
            }
            return redirect()->back()->withInput();
        }

        session()->setFlashdata('success', 'Jadwal piket berhasil diperbarui');
        return redirect()->to('/admin/guru-piket');
    }

    /**
     * Delete guru piket assignment
     */
    public function delete($id)
    {
        $result = $this->guruPiketService->delete($id);

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
        } else {
            session()->setFlashdata('success', 'Jadwal piket berhasil dihapus');
        }

        return redirect()->to('/admin/guru-piket');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        $result = $this->guruPiketService->toggleStatus($id);

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
        } else {
            $statusText = $result['data']['new_status'] ? 'diaktifkan' : 'dinonaktifkan';
            session()->setFlashdata('success', "Jadwal piket berhasil {$statusText}");
        }

        return redirect()->to('/admin/guru-piket');
    }

    /**
     * Bulk assign multiple guru to a day (AJAX)
     */
    public function bulkAssign()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $tahunAjaran = get_active_tahun_ajaran();
        $semester = $this->request->getPost('semester') ?: 'ganjil';
        $guruIds = $this->request->getPost('guru_ids') ?? [];
        $hari = $this->request->getPost('hari');
        $jobdeskId = $this->request->getPost('jobdesk_id') ? (int)$this->request->getPost('jobdesk_id') : null;
        $keterangan = $this->request->getPost('keterangan');
        $rincianTugas = $this->request->getPost('rincian_tugas');

        $result = $this->guruPiketService->bulkAssign($guruIds, $hari, $tahunAjaran, $semester, $keterangan, $rincianTugas, $jobdeskId);

        return $this->response->setJSON($result);
    }

    /**
     * Get available guru for AJAX
     */
    public function getAvailableGuru()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $hari = $this->request->getPost('hari');
        $tahunAjaran = get_active_tahun_ajaran();
        $semester = $this->request->getPost('semester') ?: 'ganjil';
        $excludeId = $this->request->getPost('exclude_id');

        $result = $this->guruPiketService->getAvailableGuru($hari, $tahunAjaran, $semester, $excludeId);

        return $this->response->setJSON($result);
    }
}
