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
    }

    public function index()
    {
        $filters = [
            'tahun_ajaran' => $this->request->getGet('tahun_ajaran'),
            'guru_id'      => $this->request->getGet('guru_id'),
            'tempat_pkl_id' => $this->request->getGet('tempat_pkl_id'),
            'kota'         => $this->request->getGet('kota'),
        ];

        $pembimbingResult = $this->pembimbingPklService->getAllPembimbingPkl($filters);
        $filterLists = $this->pembimbingPklService->getFilterLists();

        $data = [
            'title'             => 'Pembimbing PKL',
            'pageTitle'         => 'Pembagian Pembimbing PKL',
            'pageDescription'   => 'Kelola pembagian guru sebagai pembimbing PKL',
            'user'              => $this->getUserData(),
            'pembimbing'        => $pembimbingResult['data'] ?? [],
            'guruFilterList'    => $filterLists['data']['guruList'] ?? [],
            'tempatFilterList'  => $filterLists['data']['tempatPklList'] ?? [],
            'kotaFilterList'    => $filterLists['data']['kotaList'] ?? [],
            'tahunAjaranList'   => $filterLists['data']['tahunAjaranList'] ?? [],
            'selectedTahun'     => $filters['tahun_ajaran'],
            'selectedGuru'      => $filters['guru_id'],
            'selectedTempat'    => $filters['tempat_pkl_id'],
            'selectedKota'      => $filters['kota'],
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
            'nama_perusahaan'       => $this->request->getPost('nama_perusahaan'),
            'alamat'                => $this->request->getPost('alamat'),
            'kota'                  => $this->request->getPost('kota'),
            'kontak'                => $this->request->getPost('kontak'),
            'telepon'               => $this->request->getPost('telepon'),
            'instruktur_nama'       => $this->request->getPost('instruktur_nama'),
            'instruktur_email'      => $this->request->getPost('instruktur_email'),
            'instruktur_telepon'    => $this->request->getPost('instruktur_telepon'),
            'instruktur_username'   => $this->request->getPost('instruktur_username'),
            'instruktur_password'   => $this->request->getPost('instruktur_password'),
        ];

        $result = $this->pembimbingPklService->createTempatPkl($data);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors'] ?? []);
        }

        $msg = 'Tempat PKL berhasil ditambahkan';
        if (!empty($data['instruktur_nama'])) {
            $msg .= ' beserta akun instruktur';
        }
        session()->setFlashdata('success', $msg);
        return redirect()->to('/admin/pembimbing-pkl/tempat-pkl');
    }

    public function updateTempatPkl($id)
    {
        $data = [
            'nama_perusahaan'       => $this->request->getPost('nama_perusahaan'),
            'alamat'                => $this->request->getPost('alamat'),
            'kota'                  => $this->request->getPost('kota'),
            'kontak'                => $this->request->getPost('kontak'),
            'telepon'               => $this->request->getPost('telepon'),
            'instruktur_nama'       => $this->request->getPost('instruktur_nama'),
            'instruktur_email'      => $this->request->getPost('instruktur_email'),
            'instruktur_telepon'    => $this->request->getPost('instruktur_telepon'),
            'instruktur_username'   => $this->request->getPost('instruktur_username'),
            'instruktur_password'   => $this->request->getPost('instruktur_password'),
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
        $filters = [
            'tahun_ajaran'  => $this->request->getGet('tahun_ajaran'),
            'tempat_pkl_id' => $this->request->getGet('tempat_pkl_id'),
            'kelas'         => $this->request->getGet('kelas'),
            'kota'          => $this->request->getGet('kota'),
        ];

        $siswaPklResult = $this->pembimbingPklService->getAllSiswaPkl($filters);
        $statsResult = $this->pembimbingPklService->getSiswaPklStats();
        $filterLists = $this->pembimbingPklService->getSiswaPklFilterLists();
        $formLists = $this->pembimbingPklService->getFormLists();

        $data = [
            'title'              => 'Penempatan Siswa PKL',
            'pageTitle'          => 'Penempatan Siswa PKL',
            'pageDescription'    => 'Kelola penempatan siswa kelas XII ke tempat PKL',
            'user'               => $this->getUserData(),
            'siswaPkl'           => $siswaPklResult['data'] ?? [],
            'stats'              => $statsResult['data'] ?? [],
            'tahunAjaranList'    => $filterLists['data']['tahunAjaranList'] ?? [],
            'tempatFilterList'   => $filterLists['data']['tempatPklList'] ?? [],
            'kelasFilterList'    => $filterLists['data']['kelasList'] ?? [],
            'kotaFilterList'     => $filterLists['data']['kotaList'] ?? [],
            'tempatPklList'      => $formLists['data']['tempatPklList'] ?? [],
            'selectedTahun'      => $filters['tahun_ajaran'],
            'selectedTempat'     => $filters['tempat_pkl_id'],
            'selectedKelas'      => $filters['kelas'],
            'selectedKota'       => $filters['kota'],
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
            'siswa_id'          => $this->request->getPost('siswa_id'),
            'tempat_pkl_id'     => $this->request->getPost('tempat_pkl_id'),
            'pembimbing_pkl_id' => $this->request->getPost('pembimbing_pkl_id'),
        ];

        $result = $this->pembimbingPklService->createSiswaPkl($data);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors'] ?? []);
        }

        session()->setFlashdata('success', 'Siswa berhasil ditempatkan di tempat PKL.');
        return redirect()->to('/admin/pembimbing-pkl/siswa-pkl');
    }

    public function siswaPklBatchStore()
    {
        $siswaIds        = $this->request->getPost('siswa_ids');
        $tempatPklId     = $this->request->getPost('tempat_pkl_id');
        $pembimbingPklId = $this->request->getPost('pembimbing_pkl_id');

        if (empty($siswaIds) || !is_array($siswaIds)) {
            session()->setFlashdata('error', 'Pilih minimal satu siswa');
            return redirect()->back()->withInput();
        }

        $result = $this->pembimbingPklService->createSiswaPklBatch($siswaIds, $tempatPklId, $pembimbingPklId);

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
