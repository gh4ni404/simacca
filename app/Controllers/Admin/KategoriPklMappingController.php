<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KategoriPklMappingModel;
use App\Models\PklCategoryModel;
use App\Models\TempatPklModel;

class KategoriPklMappingController extends BaseController
{
    protected $mappingModel;
    protected $kategoriModel;
    protected $tempatPklModel;

    public function __construct()
    {
        $this->mappingModel   = new KategoriPklMappingModel();
        $this->kategoriModel  = new PklCategoryModel();
        $this->tempatPklModel = new TempatPklModel();
    }

    public function index()
    {
        $selectedTempatId = $this->request->getGet('tempat_pkl_id');

        $data = [
            'title'            => 'Mapping Kategori PKL',
            'allTempatPkl'     => $this->tempatPklModel->orderBy('nama_perusahaan', 'ASC')->findAll(),
            'allKategori'      => $this->kategoriModel->orderBy('nama', 'ASC')->findAll(),
            'selectedTempatId' => $selectedTempatId,
            'mappedKategoriIds'=> [],
            'mappingSummary'   => $this->mappingModel->getMappingSummary(),
        ];

        if ($selectedTempatId) {
            $data['mappedKategoriIds'] = $this->mappingModel->getMappedKategoriIds((int) $selectedTempatId);
        }

        return view('admin/kategori_pkl_mapping/index', $data);
    }

    public function store()
    {
        $tempatPklId = $this->request->getPost('tempat_pkl_id');
        $kategoriId  = $this->request->getPost('kategori_id');

        if (!$tempatPklId || !$kategoriId) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        $status = $this->mappingModel->toggleMapping((int) $tempatPklId, (int) $kategoriId);

        return $this->response->setJSON([
            'success' => true,
            'status'  => $status,
            'message' => $status === 'added' ? 'Kategori ditambahkan ke mapping' : 'Kategori dihapus dari mapping',
        ]);
    }

    public function getMappedKategori()
    {
        $tempatPklId = $this->request->getPost('tempat_pkl_id');

        if (!$tempatPklId) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'mapped' => []]);
        }

        $mapped = $this->mappingModel->getMappedKategoriIds((int) $tempatPklId);

        return $this->response->setJSON(['success' => true, 'mapped' => $mapped]);
    }

    public function getMappingSummary()
    {
        $summary = $this->mappingModel->getMappingSummary();
        return $this->response->setJSON(['success' => true, 'summary' => $summary]);
    }
}
