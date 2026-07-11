<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\SiswaService;

class SiswaController extends BaseController
{
    protected $siswaService;

    public function __construct()
    {
        $this->siswaService = new SiswaService();

        // Cek role admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/access-denied');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perPage = 10;
        $keyword = $this->request->getVar('search');
        $status = $this->request->getVar('status') ?? 'active';
        $kelasId = $this->request->getVar('kelas_id');
        $currentPage = $this->request->getVar('page') ?? 1;

        // Use service to get siswa data
        $result = $this->siswaService->getAllSiswa([
            'search' => $keyword,
            'status' => $status,
            'kelas_id' => $kelasId,
            'page' => $currentPage,
            'perPage' => $perPage
        ]);

        $kelasModel = new \App\Models\KelasModel();
        $allKelas = $kelasModel->where('tahun_ajaran', get_active_tahun_ajaran())->findAll();

        $data = [
            'title' => 'Manajemen Siswa',
            'pageTitle' => 'Data Siswa',
            'pageDescription' => 'Kelola data siswa dan absensi',
            'user' => $this->getUserData(),
            'siswa' => $result['data']['siswa'],
            'totalSiswa' => $result['data']['total'],
            'kelasSummary' => $result['data']['kelasSummary'],
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'keyword' => $keyword,
            'status' => $status,
            'kelasId' => $kelasId,
            'allKelas' => $allKelas
        ];

        return view('admin/siswa/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formLists = $this->siswaService->getFormLists();

        $data = [
            'title' => 'Tambah Siswa Baru',
            'pageTitle' => 'Tambah Data Siswa',
            'pageDescription' => 'Form untuk menambahkan siswa baru',
            'user' => $this->getUserData(),
            'kelasList' => $formLists['data']['kelasList'],
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/siswa/tambah', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'nis' => 'required|is_unique[siswa.nis]',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'required|numeric',
            'username' => 'required|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'email' => 'valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Use service to create siswa
        $data = [
            'nis' => $this->request->getPost('nis'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'email' => $this->request->getPost('email')
        ];

        $result = $this->siswaService->createSiswa($data);

        if ($result['success']) {
            session()->setFlashdata('success', 'Welcome aboard! Siswa baru sudah terdaftar 🎒✨');
            return redirect()->to('/admin/siswa');
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $result = $this->siswaService->getSiswaById($id);

        if (!$result['success']) {
            session()->setFlashdata('error', 'Hmm, siswa ini nggak ketemu.');
            return redirect()->to('/admin/siswa');
        }

        $formLists = $this->siswaService->getFormLists();

        $data = [
            'title' => 'Edit Data Siswa',
            'pageTitle' => 'Edit Data Siswa',
            'pageDescription' => 'Form untuk mengubah data siswa',
            'user' => $this->getUserData(),
            'siswa' => $result['data']['siswa'],
            'userData' => $result['data']['user'],
            'kelasList' => $formLists['data']['kelasList'],
            'validation' => \Config\Services::validation()
        ];

        return view('admin/siswa/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        // Validation rules
        $rules = [
            'nis' => 'required|is_unique[siswa.nis,id,' . $id . ']',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'required|numeric',
            'username' => 'required',
            'email' => 'valid_email'
        ];

        // Jika password diisi
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Use service to update siswa
        $data = [
            'nis' => $this->request->getPost('nis'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password')
        ];

        $result = $this->siswaService->updateSiswa($id, $data);

        if ($result['success']) {
            session()->setFlashdata('success', 'Nice! Data siswa sudah diperbarui.');
            return redirect()->to('/admin/siswa');
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $result = $this->siswaService->deleteSiswa($id);

        if ($result['success']) {
            session()->setFlashdata('success', 'Sip, Data siswa sudah dihapus ya! 🗑️');
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/admin/siswa');
    }

    /**
     * Show detail of specified resource.
     */
    public function show($id)
    {
        $result = $this->siswaService->getSiswaById($id);

        if (!$result['success']) {
            session()->setFlashdata('error', 'Hmm, siswa ini nggak ketemu.');
            return redirect()->to('/admin/siswa');
        }

        $data = [
            'title' => 'Detail Siswa',
            'pageTitle' => 'Detail Data Siswa',
            'pageDescription' => 'Informasi lengkap data siswa',
            'user' => $this->getUserData(),
            'siswa' => $result['data']['siswa'],
            'userData' => $result['data']['user'],
            'kelas' => $result['data']['kelas'],
            'absensiStats' => $result['data']['absensiStats']
        ];

        return view('admin/siswa/show', $data);
    }

    /**
     * AJAX: Check NIS availability
     */
    public function checkNis()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/siswa');
        }

        $nis = $this->request->getPost('nis');
        $id = $this->request->getPost('id');

        $result = $this->siswaService->checkNisAvailability($nis, $id);

        return $this->response->setJSON([
            'available' => $result['data']['available'],
            'message' => $result['data']['message']
        ]);
    }

    /**
     * AJAX: Batch check NIS status for import preview
     */
    public function checkNisBatch()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/siswa');
        }

        $nisList = $this->request->getGet('nis_list');

        if (!is_array($nisList)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data NIS tidak valid'
            ]);
        }

        $result = $this->siswaService->checkNisBatch($nisList);

        return $this->response->setJSON($result);
    }

    /**
     * AJAX: Get all siswa IDs matching current filters (for select-all across pages)
     */
    public function getAllIds()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/siswa');
        }

        $keyword = $this->request->getGet('search');
        $status = $this->request->getGet('status') ?? 'active';
        $kelasId = $this->request->getGet('kelas_id');

        $result = $this->siswaService->getAllSiswaIds([
            'search' => $keyword,
            'status' => $status,
            'kelas_id' => $kelasId,
        ]);

        return $this->response->setJSON([
            'success' => $result['success'],
            'ids' => $result['data']['ids'] ?? [],
            'total' => $result['data']['total'] ?? 0,
        ]);
    }

    /**
     * Change siswa status (active/inactive)
     */
    public function changeStatus($id)
    {
        $result = $this->siswaService->changeStatus($id);

        if ($result['success']) {
            $newStatus = $result['data']['new_status'];
            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            session()->setFlashdata('success', $statusText == 'diaktifkan' ? "Siswa aktif kembali! Let's go." : 'Siswa dinonaktifkan. Take care!');
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/admin/siswa');
    }

    /**
     * Export data siswa to Excel
     */
    public function export()
    {
        $result = $this->siswaService->exportToExcel();

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
            return redirect()->to('/admin/siswa');
        }

        $spreadsheet = $result['data']['spreadsheet'];
        $filename = $result['data']['filename'];

        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    /**
     * Import data siswa from Excel
     */
    public function import()
    {
        $data = [
            'title' => 'Import Data Siswa',
            'pageTitle' => 'Import Data Siswa',
            'pageDescription' => 'Upload file Excel untuk import data siswa',
            'user' => $this->getUserData()
        ];

        return view('admin/siswa/import', $data);
    }

    /**
     * Process import
     */
    public function processImport()
    {
        $file = $this->request->getFile('file_excel');

        $result = $this->siswaService->processExcelImport($file);

        if ($result['success']) {
            if (!empty($result['data']['errors'])) {
                session()->setFlashdata('errors', $result['data']['errors']);
            }
            session()->setFlashdata('success', $result['data']['message']);
            return redirect()->to('/admin/siswa');
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->to('/admin/siswa/import');
        }
    }

    /**
     * Bulk action (activate/deactivate/delete)
     */
    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids');

        if (empty($ids)) {
            session()->setFlashdata('error', 'Eh, pilih siswanya dulu dong!');
            return redirect()->to('/admin/siswa');
        }

        $result = $this->siswaService->bulkAction($action, $ids);

        if ($result['success']) {
            session()->setFlashdata('success', $result['data']['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/admin/siswa');
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate()
    {
        $result = $this->siswaService->generateImportTemplate();

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
            return redirect()->to('/admin/siswa');
        }

        $spreadsheet = $result['data']['spreadsheet'];
        $filename = $result['data']['filename'];

        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    /**
     * AJAX: Check username availability for siswa
     */
    public function checkUsername()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/siswa');
        }

        $username = $this->request->getPost('username');
        $userId = $this->request->getPost('user_id');

        $result = $this->siswaService->checkUsernameAvailability($username, $userId);

        return $this->response->setJSON([
            'available' => $result['data']['available'],
            'message' => $result['data']['message']
        ]);
    }
}
