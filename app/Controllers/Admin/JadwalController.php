<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\JadwalService;
use CodeIgniter\Exceptions\PageNotFoundException;

class JadwalController extends BaseController
{
    protected $jadwalService;
    protected $session;

    public function __construct()
    {
        $this->jadwalService = new JadwalService();
        $this->session = session();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $filters = [
            'per_page' => $this->request->getGet('per_page') ?? 10,
            'search' => $this->request->getGet('search'),
            'semester' => $this->request->getGet('semester'),
            'tahun_ajaran' => $this->request->getGet('tahun_ajaran') ?: get_active_tahun_ajaran()
        ];

        $result = $this->jadwalService->getAllJadwal($filters);

        $data = [
            'title' => 'Manajemen Jadwal Mengajar',
            'pageTitle' => 'Jadwal Mengajar',
            'pageDescription' => 'Kelola jadwal mengajar guru',
            'jadwal' => $result['data']['jadwal'],
            'pager' => $result['data']['pager'],
            'search' => $filters['search'],
            'perPage' => $filters['per_page'],
            'semester' => $filters['semester'],
            'tahunAjaran' => $filters['tahun_ajaran'],
            'hariList' => $result['data']['hariList'],
            'semesterList' => $result['data']['semesterList'],
            'tahunAjaranList' => $result['data']['tahunAjaranList']
        ];

        return view('admin/jadwal/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $formLists = $this->jadwalService->getFormLists();

        $data = [
            'title' => 'Tambah Jadwal Mengajar',
            'pageTitle' => 'Tambah Jadwal Mengajar',
            'pageDescription' => 'Isi form untuk menambahkan jadwal mengajar baru',
            'validation' => \Config\Services::validation(),
            'guruOptions' => $formLists['data']['guruOptions'],
            'mapelOptions' => $formLists['data']['mapelOptions'],
            'kelasOptions' => $formLists['data']['kelasOptions'],
            'hariList' => $formLists['data']['hariList'],
            'semesterList' => $formLists['data']['semesterList'],
        ];

        return view('admin/jadwal/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        // Validate input
        $rules = (new \App\Models\JadwalMengajarModel())->getValidationRules();
        unset($rules['tahun_ajaran']);
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data
        $data = [
            'guru_id' => $this->request->getPost('guru_id'),
            'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'hari' => $this->request->getPost('hari'),
            'jam_mulai' => $this->request->getPost('jam_mulai'),
            'jam_selesai' => $this->request->getPost('jam_selesai'),
            'semester' => $this->request->getPost('semester'),
        ];

        $result = $this->jadwalService->createJadwal($data);

        if ($result['success']) {
            $this->session->setFlashdata('success', "Jadwal baru siap! Let's teach");
            return redirect()->to('/admin/jadwal');
        } else {
            $this->session->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $result = $this->jadwalService->getJadwalById($id);

        if (!$result['success']) {
            throw new PageNotFoundException('Jadwal mengajar tidak ditemukan');
        }

        $formLists = $this->jadwalService->getFormLists();

        $data = [
            'title' => 'Edit Jadwal Mengajar',
            'pageTitle' => 'Edit Jadwal Mengajar',
            'pageDescription' => 'Edit data jadwal mengajar',
            'jadwal' => $result['data']['jadwal'],
            'validation' => \Config\Services::validation(),
            'guruOptions' => $formLists['data']['guruOptions'],
            'mapelOptions' => $formLists['data']['mapelOptions'],
            'kelasOptions' => $formLists['data']['kelasOptions'],
            'hariList' => $formLists['data']['hariList'],
            'semesterList' => $formLists['data']['semesterList'],
        ];

        return view('admin/jadwal/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        // Validate input
        $rules = (new \App\Models\JadwalMengajarModel())->getValidationRules();
        unset($rules['tahun_ajaran']);
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data
        $data = [
            'guru_id' => $this->request->getPost('guru_id'),
            'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'hari' => $this->request->getPost('hari'),
            'jam_mulai' => $this->request->getPost('jam_mulai'),
            'jam_selesai' => $this->request->getPost('jam_selesai'),
            'semester' => $this->request->getPost('semester'),
        ];

        $result = $this->jadwalService->updateJadwal($id, $data);

        if ($result['success']) {
            $this->session->setFlashdata('success', 'Jadwal updated! All set');
            return redirect()->to('/admin/jadwal');
        } else {
            if ($result['code'] == 404) {
                throw new PageNotFoundException('Jadwal mengajar tidak ditemukan');
            }
            $this->session->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $result = $this->jadwalService->deleteJadwal($id);

        if ($result['success']) {
            $this->session->setFlashdata('success', 'Sip, Jadwal sudah dihapus ya! 🗑️');
        } else {
            if ($result['code'] == 404) {
                throw new PageNotFoundException('Jadwal mengajar tidak ditemukan');
            }
            $this->session->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/admin/jadwal');
    }

    /**
     * Get jadwal by guru (AJAX)
     */
    public function getByGuru()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $guruId = $this->request->getGet('guru_id');
        $result = $this->jadwalService->getByGuru($guruId);

        return $this->response->setJSON([
            'success' => $result['success'],
            'data' => $result['data']['jadwal'] ?? []
        ]);
    }

    /**
     * Get jadwal by kelas (AJAX)
     */
    public function getByKelas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $kelasId = $this->request->getGet('kelas_id');
        $result = $this->jadwalService->getByKelas($kelasId);

        return $this->response->setJSON([
            'success' => $result['success'],
            'data' => $result['data']['jadwal'] ?? []
        ]);
    }

    /**
     * Check schedule conflict (AJAX)
     */
    public function checkConflict()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $data = [
            'guru_id' => $this->request->getPost('guru_id'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'hari' => $this->request->getPost('hari'),
            'jam_mulai' => $this->request->getPost('jam_mulai'),
            'jam_selesai' => $this->request->getPost('jam_selesai'),
            'exclude_id' => $this->request->getPost('exclude_id')
        ];

        $result = $this->jadwalService->checkConflict($data);

        return $this->response->setJSON([
            'success' => $result['success'],
            'conflict_guru' => $result['data']['conflict_guru'] ?? false,
            'conflict_kelas' => $result['data']['conflict_kelas'] ?? false
        ]);
    }

    /**
     * Export jadwal to Excel
     */
    public function export()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $filters = [
            'semester' => $this->request->getGet('semester'),
            'tahun_ajaran' => $this->request->getGet('tahun_ajaran')
        ];

        $result = $this->jadwalService->exportToExcel($filters);

        if (!$result['success']) {
            $this->session->setFlashdata('error', $result['message']);
            return redirect()->to('/admin/jadwal');
        }

        $spreadsheet = $result['data']['spreadsheet'];
        $filename = $result['data']['filename'];

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    /**
     * Show import form
     */
    public function import()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Import Jadwal Mengajar',
            'pageTitle' => 'Import Jadwal Mengajar',
            'pageDescription' => 'Upload file Excel untuk import jadwal mengajar',
            'user' => $this->getUserData()
        ];

        return view('admin/jadwal/import', $data);
    }

    /**
     * Process import from Excel
     */
    public function processImport()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $file = $this->request->getFile('file_excel');
        $skipDuplicate = (bool)$this->request->getPost('skip_duplicate');

        $result = $this->jadwalService->processExcelImport($file, $skipDuplicate);

        if ($result['success']) {
            if (!empty($result['data']['errors'])) {
                $this->session->setFlashdata('import_errors', $result['data']['errors']);
            }
            
            if ($result['data']['error_count'] > 0 && $result['data']['success_count'] == 0) {
                $this->session->setFlashdata('error', $result['data']['message']);
            } else {
                $this->session->setFlashdata('success', $result['data']['message']);
            }

            if (!empty($result['data']['errors'])) {
                return redirect()->to('/admin/jadwal/import');
            }
            return redirect()->to('/admin/jadwal');
        } else {
            $this->session->setFlashdata('error', $result['message']);
            return redirect()->to('/admin/jadwal/import');
        }
    }

    /**
     * Download template Excel for import (User-Friendly dengan Dropdown)
     */
    public function downloadTemplate()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $result = $this->jadwalService->generateImportTemplate();

        if (!$result['success']) {
            $this->session->setFlashdata('error', $result['message']);
            return redirect()->to('/admin/jadwal');
        }

        $spreadsheet = $result['data']['spreadsheet'];
        $filename = $result['data']['filename'];

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }
}