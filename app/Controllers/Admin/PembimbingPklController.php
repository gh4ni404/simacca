<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\PembimbingPklService;

class PembimbingPklController extends BaseController
{
    protected $pembimbingPklService;

    public function __construct()
    {
        $this->pembimbingPklService = new PembimbingPklService();

        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/access-denied');
        }
    }

    public function index()
    {
        $tahunAjaran = $this->request->getGet('tahun_ajaran');
        $pembimbingResult = $this->pembimbingPklService->getAllPembimbingPkl($tahunAjaran);

        $data = [
            'title'             => 'Pembimbing PKL',
            'pageTitle'         => 'Pembagian Pembimbing PKL',
            'pageDescription'   => 'Kelola pembagian guru sebagai pembimbing PKL',
            'user'              => $this->getUserData(),
            'pembimbing'        => $pembimbingResult['data'] ?? [],
            'tahunAjaranList'   => $this->pembimbingPklService->getFormLists()['data']['tahunAjaranList'] ?? [],
            'selectedTahun'     => $tahunAjaran,
        ];

        return view('admin/pembimbing_pkl/index', $data);
    }

    public function create()
    {
        $listsResult = $this->pembimbingPklService->getFormLists();

        $data = [
            'title'             => 'Tambah Pembimbing PKL',
            'pageTitle'         => 'Tambah Pembimbing PKL',
            'pageDescription'   => 'Form untuk menambahkan pembimbing PKL baru',
            'user'              => $this->getUserData(),
            'guruList'          => $listsResult['data']['guruList'] ?? [],
            'tempatPklList'     => $listsResult['data']['tempatPklList'] ?? [],
            'validation'        => \Config\Services::validation(),
        ];

        return view('admin/pembimbing_pkl/create', $data);
    }

    public function store()
    {
        $data = [
            'guru_id'       => $this->request->getPost('guru_id'),
            'tempat_pkl_id' => $this->request->getPost('tempat_pkl_id'),
        ];

        $result = $this->pembimbingPklService->createPembimbingPkl($data);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors'] ?? []);
        }

        session()->setFlashdata('success', 'Pembimbing PKL berhasil ditambahkan');
        return redirect()->to('/admin/pembimbing-pkl');
    }

    public function edit($id)
    {
        $pembimbingResult = $this->pembimbingPklService->getPembimbingPklById($id);

        if (!$pembimbingResult['success']) {
            session()->setFlashdata('error', 'Data pembimbing PKL tidak ditemukan');
            return redirect()->to('/admin/pembimbing-pkl');
        }

        $listsResult = $this->pembimbingPklService->getFormLists();

        $data = [
            'title'             => 'Edit Pembimbing PKL',
            'pageTitle'         => 'Edit Pembimbing PKL',
            'pageDescription'   => 'Form untuk mengubah data pembimbing PKL',
            'user'              => $this->getUserData(),
            'pembimbing'        => $pembimbingResult['data'],
            'guruList'          => $listsResult['data']['guruList'] ?? [],
            'tempatPklList'     => $listsResult['data']['tempatPklList'] ?? [],
            'validation'        => \Config\Services::validation(),
        ];

        return view('admin/pembimbing_pkl/edit', $data);
    }

    public function update($id)
    {
        $data = [
            'guru_id'       => $this->request->getPost('guru_id'),
            'tempat_pkl_id' => $this->request->getPost('tempat_pkl_id'),
        ];

        $result = $this->pembimbingPklService->updatePembimbingPkl($id, $data);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors'] ?? []);
        }

        session()->setFlashdata('success', 'Data pembimbing PKL berhasil diperbarui');
        return redirect()->to('/admin/pembimbing-pkl');
    }

    public function delete($id)
    {
        $result = $this->pembimbingPklService->deletePembimbingPkl($id);

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
        } else {
            session()->setFlashdata('success', 'Data pembimbing PKL berhasil dihapus');
        }

        return redirect()->to('/admin/pembimbing-pkl');
    }

    public function tempatPkl()
    {
        $result = $this->pembimbingPklService->getAllTempatPkl();

        $data = [
            'title'             => 'Tempat PKL',
            'pageTitle'         => 'Data Tempat PKL',
            'pageDescription'   => 'Kelola data tempat pelaksanaan PKL',
            'user'              => $this->getUserData(),
            'tempatPkl'         => $result['data'] ?? [],
            'validation'        => \Config\Services::validation(),
        ];

        return view('admin/pembimbing_pkl/tempat_pkl', $data);
    }

    public function storeTempatPkl()
    {
        $data = [
            'nama_perusahaan' => $this->request->getPost('nama_perusahaan'),
            'alamat'          => $this->request->getPost('alamat'),
            'kota'            => $this->request->getPost('kota'),
            'kontak'          => $this->request->getPost('kontak'),
            'telepon'         => $this->request->getPost('telepon'),
        ];

        $result = $this->pembimbingPklService->createTempatPkl($data);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors'] ?? []);
        }

        session()->setFlashdata('success', 'Tempat PKL berhasil ditambahkan');
        return redirect()->to('/admin/pembimbing-pkl/tempat-pkl');
    }

    public function updateTempatPkl($id)
    {
        $data = [
            'nama_perusahaan' => $this->request->getPost('nama_perusahaan'),
            'alamat'          => $this->request->getPost('alamat'),
            'kota'            => $this->request->getPost('kota'),
            'kontak'          => $this->request->getPost('kontak'),
            'telepon'         => $this->request->getPost('telepon'),
        ];

        $result = $this->pembimbingPklService->updateTempatPkl($id, $data);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors'] ?? []);
        }

        session()->setFlashdata('success', 'Tempat PKL berhasil diperbarui');
        return redirect()->to('/admin/pembimbing-pkl/tempat-pkl');
    }

    public function deleteTempatPkl($id)
    {
        $result = $this->pembimbingPklService->deleteTempatPkl($id);

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
        } else {
            session()->setFlashdata('success', 'Tempat PKL berhasil dihapus');
        }

        return redirect()->to('/admin/pembimbing-pkl/tempat-pkl');
    }

    public function siswaPkl()
    {
        $tahunAjaran = $this->request->getGet('tahun_ajaran');
        $siswaPklResult = $this->pembimbingPklService->getAllSiswaPkl($tahunAjaran);
        $statsResult = $this->pembimbingPklService->getSiswaPklStats();
        $formLists = $this->pembimbingPklService->getFormLists();

        $data = [
            'title'             => 'Penempatan Siswa PKL',
            'pageTitle'         => 'Penempatan Siswa PKL',
            'pageDescription'   => 'Kelola penempatan siswa kelas XII ke tempat PKL',
            'user'              => $this->getUserData(),
            'siswaPkl'          => $siswaPklResult['data'] ?? [],
            'stats'             => $statsResult['data'] ?? [],
            'tahunAjaranList'   => $formLists['data']['tahunAjaranList'] ?? [],
            'tempatPklList'     => $formLists['data']['tempatPklList'] ?? [],
            'selectedTahun'     => $tahunAjaran,
        ];

        return view('admin/pembimbing_pkl/siswa_pkl', $data);
    }

    public function siswaPklCreate()
    {
        $listsResult = $this->pembimbingPklService->getFormListsSiswa();

        $data = [
            'title'             => 'Tambah Penempatan Siswa PKL',
            'pageTitle'         => 'Tambah Penempatan Siswa PKL',
            'pageDescription'   => 'Tempatkan siswa kelas XII ke tempat PKL',
            'user'              => $this->getUserData(),
            'siswaList'         => $listsResult['data']['siswaList'] ?? [],
            'tempatPklList'     => $listsResult['data']['tempatPklList'] ?? [],
            'validation'        => \Config\Services::validation(),
        ];

        return view('admin/pembimbing_pkl/siswa_pkl_create', $data);
    }

    public function siswaPklStore()
    {
        $data = [
            'siswa_id'      => $this->request->getPost('siswa_id'),
            'tempat_pkl_id' => $this->request->getPost('tempat_pkl_id'),
        ];

        $result = $this->pembimbingPklService->createSiswaPkl($data);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors'] ?? []);
        }

        session()->setFlashdata('success', 'Siswa berhasil ditempatkan di tempat PKL. Pembimbing otomatis mengikuti tempat PKL.');
        return redirect()->to('/admin/pembimbing-pkl/siswa-pkl');
    }

    public function siswaPklBatchStore()
    {
        $siswaIds       = $this->request->getPost('siswa_ids');
        $tempatPklId    = $this->request->getPost('tempat_pkl_id');

        if (empty($siswaIds) || !is_array($siswaIds)) {
            session()->setFlashdata('error', 'Pilih minimal satu siswa');
            return redirect()->back()->withInput();
        }

        $result = $this->pembimbingPklService->createSiswaPklBatch($siswaIds, $tempatPklId);

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }

        session()->setFlashdata('success', $result['message']);
        return redirect()->to('/admin/pembimbing-pkl/siswa-pkl');
    }

    public function siswaPklDelete($id)
    {
        $result = $this->pembimbingPklService->deleteSiswaPkl($id);

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
        } else {
            session()->setFlashdata('success', 'Penempatan siswa PKL berhasil dihapus');
        }

        return redirect()->to('/admin/pembimbing-pkl/siswa-pkl');
    }

    public function siswaPklBulkDelete()
    {
        $ids = $this->request->getPost('ids');

        if (empty($ids) || !is_array($ids)) {
            session()->setFlashdata('error', 'Tidak ada data yang dipilih');
            return redirect()->to('/admin/pembimbing-pkl/siswa-pkl');
        }

        $result = $this->pembimbingPklService->bulkDeleteSiswaPkl($ids);

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
        } else {
            session()->setFlashdata('success', $result['message']);
        }

        return redirect()->to('/admin/pembimbing-pkl/siswa-pkl');
    }

    public function getSiswaXIIUnplaced()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false])->setStatusCode(403);
        }

        $tahunAjaran = get_active_tahun_ajaran();
        $result = $this->pembimbingPklService->getSiswaXIIWithPlacement($tahunAjaran);

        return $this->response->setJSON($result);
    }

    public function getPembimbingByTempatPkl()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/pembimbing-pkl');
        }

        $json = $this->request->getJSON();
        $tempatPklId = $json->tempat_pkl_id ?? null;
        $tahunAjaran = $json->tahun_ajaran ?? null;

        $result = $this->pembimbingPklService->getPembimbingByTempatPkl((int) $tempatPklId, $tahunAjaran);

        return $this->response->setJSON($result['data'] ?? []);
    }

    public function getSiswaPklByTempatPkl()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/pembimbing-pkl');
        }

        $json = $this->request->getJSON();
        $tempatPklId = $json->tempat_pkl_id ?? null;
        $tahunAjaran = $json->tahun_ajaran ?? null;

        $result = $this->pembimbingPklService->getSiswaPklByTempatPkl((int) $tempatPklId, $tahunAjaran);

        return $this->response->setJSON($result['data'] ?? []);
    }
}
