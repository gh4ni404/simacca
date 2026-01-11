<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;

class SiswaController extends BaseController
{
    protected $userModel;
    protected $siswaModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();

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
        $currentPage = $this->request->getVar('page') ?? 1;

        // Get search keyword
        $keyword = $this->request->getVar('search');

        if ($keyword) {
            $siswa = $this->siswaModel->searchSiswa($keyword);
            $total = count($siswa);
        } else {
            $siswa = $this->siswaModel->getAllSiswa();
            $total = $this->siswaModel->countAll();
        }

        $data = [
            'title' => 'Manajemen Siswa',
            'pageTitle' => 'Data Siswa',
            'pageDescription' => 'Kelola data siswa dan absensi',
            'user' => $this->getUserData(),
            'siswa' => $siswa,
            'totalSiswa' => $total,
            'kelasSummary' => $this->siswaModel->getCountByKelas(),
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'keyword' => $keyword
        ];

        return view('admin/siswa/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Siswa Baru',
            'pageTitle' => 'Tambah Data Siswa',
            'pageDescription' => 'Form untuk menambahkan siswa baru',
            'user' => $this->getUserData(),
            'kelasList' => $this->kelasModel->getListKelas(),
            'validation' => \Config\Services::validation(),
            'tahunAjaranList' => $this->getTahunAjaranList()
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
            'tahun_ajaran' => 'required',
            'username' => 'required|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'email' => 'valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Create user account
            $userData = [
                'username' => $this->request->getPost('username'),
                'password' => $this->request->getPost('password'),
                'role' => 'siswa',
                'email' => $this->request->getPost('email'),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $userId = $this->userModel->insert($userData);

            // 2. Create siswa data
            $siswaData = [
                'user_id' => $userId,
                'nis' => $this->request->getPost('nis'),
                'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'kelas_id' => $this->request->getPost('kelas_id'),
                'tahun_ajaran' => $this->request->getPost('tahun_ajaran'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->siswaModel->insert($siswaData);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal menyimpan data');
            }

            session()->setFlashdata('success', 'Data siswa berhasil ditambahkan');
            return redirect()->to('/admin/siswa');
        } catch (\Exception $e) {
            $db->transRollback();
            session()->setFlashdata('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $siswa = $this->siswaModel->getSiswaWithWaliKelas($id);

        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->to('/admin/siswa');
        }

        // Get user data
        $user = $this->userModel->find($siswa['user_id']);

        $data = [
            'title' => 'Edit Data Siswa',
            'pageTitle' => 'Edit Data Siswa',
            'pageDescription' => 'Form untuk mengubah data siswa',
            'user' => $this->getUserData(),
            'siswa' => $siswa,
            'userData' => $user,
            'kelasList' => $this->kelasModel->getListKelas(),
            'tahunAjaranList' => $this->getTahunAjaranList(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/siswa/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $siswa = $this->siswaModel->find($id);

        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->to('/admin/siswa');
        }

        // Validation rules
        $rules = [
            'nis' => 'required|is_unique[siswa.nis,id,' . $id . ']',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'required|numeric',
            'tahun_ajaran' => 'required',
            'email' => 'valid_email'
        ];

        // Jika username berubah
        $userData = $this->userModel->find($siswa['user_id']);
        if ($this->request->getPost('username') != $userData['username']) {
            $rules['username'] = 'required|is_unique[users.username]';
        }

        // Jika password diisi
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Update user account
            $userUpdateData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email')
            ];

            // Update password jika diisi
            if ($this->request->getPost('password')) {
                $userUpdateData['password'] = $this->request->getPost('password');
            }

            $this->userModel->update($siswa['user_id'], $userUpdateData);

            // 2. Update siswa data
            $siswaData = [
                'nis' => $this->request->getPost('nis'),
                'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'kelas_id' => $this->request->getPost('kelas_id'),
                'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
            ];

            $this->siswaModel->update($id, $siswaData);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal mengupdate data');
            }

            session()->setFlashdata('success', 'Data siswa berhasil diupdate');
            return redirect()->to('/admin/siswa');
        } catch (\Exception $e) {
            $db->transRollback();
            session()->setFlashdata('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $siswa = $this->siswaModel->find($id);

        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->to('/admin/siswa');
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Delete siswa data
            $this->siswaModel->delete($id);

            // 2. Delete user account
            $this->userModel->delete($siswa['user_id']);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal menghapus data');
            }

            session()->setFlashdata('success', 'Data siswa berhasil dihapus');
            return redirect()->to('/admin/siswa');
        } catch (\Exception $e) {
            $db->transRollback();
            session()->setFlashdata('error', $e->getMessage());
            return redirect()->to('/admin/siswa');
        }
    }

    /**
     * Show detail of specified resource.
     */
    public function show($id)
    {
        $siswa = $this->siswaModel->getSiswaWithWaliKelas($id);

        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->to('/admin/siswa');
        }

        // Get user data
        $user = $this->userModel->find($siswa['user_id']);

        // Get kelas data
        $kelas = $this->kelasModel->find($siswa['kelas_id']);

        // Get absensi statistics
        $absensiModel = new \App\Models\AbsensiDetailModel();
        $absensiStats = $absensiModel->getStatistikSiswa($id);

        $data = [
            'title' => 'Detail Siswa',
            'pageTitle' => 'Detail Data Siswa',
            'pageDescription' => 'Informasi lengkap data siswa',
            'user' => $this->getUserData(),
            'siswa' => $siswa,
            'userData' => $user,
            'kelas' => $kelas,
            'absensiStats' => $absensiStats
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

        $query = $this->siswaModel->where('nis', $nis);

        if ($id) {
            $query->where('id !=', $id);
        }

        $exists = $query->countAllResults() > 0;

        return $this->response->setJSON([
            'available' => !$exists,
            'message' => $exists ? 'NIS sudah digunakan' : 'NIS tersedia'
        ]);
    }

    /**
     * Change siswa status (active/inactive)
     */
    public function changeStatus($id)
    {
        $siswa = $this->siswaModel->find($id);

        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->to('/admin/siswa');
        }

        $user = $this->userModel->find($siswa['user_id']);

        if (!$user) {
            session()->setFlashdata('error', 'Data user tidak ditemukan');
            return redirect()->to('/admin/siswa');
        }

        // Toggle status
        $newStatus = $user['is_active'] ? 0 : 1;

        $this->userModel->update($siswa['user_id'], ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        session()->setFlashdata('success', "Siswa berhasil $statusText");

        return redirect()->to('/admin/siswa');
    }

    /**
     * Export data siswa to Excel
     */
    public function export()
    {
        $siswa = $this->siswaModel->getAllSiswa();

        // Create Excel file using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'NIS');
        $sheet->setCellValue('C1', 'NAMA SISWA');
        $sheet->setCellValue('D1', 'JENIS KELAMIN');
        $sheet->setCellValue('E1', 'KELAS');
        $sheet->setCellValue('F1', 'TAHUN AJARAN');
        $sheet->setCellValue('G1', 'STATUS');
        $sheet->setCellValue('H1', 'EMAIL');
        $sheet->setCellValue('I1', 'USERNAME');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE0E0E0']
            ]
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Fill data
        $row = 2;
        $no = 1;
        foreach ($siswa as $s) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $s['nis']);
            $sheet->setCellValue('C' . $row, $s['nama_lengkap']);
            $sheet->setCellValue('D' . $row, $s['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('E' . $row, $s['nama_kelas'] ?? '-');
            $sheet->setCellValue('F' . $row, $s['tahun_ajaran']);
            $sheet->setCellValue('G' . $row, $s['is_active'] ? 'Aktif' : 'Nonaktif');
            $sheet->setCellValue('H' . $row, $s['email'] ?? '-');
            $sheet->setCellValue('I' . $row, $s['username']);

            $row++;
        }

        // Auto size columns
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'data-siswa-' . date('Y-m-d') . '.xlsx';

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
        helper('security');
        $file = $this->request->getFile('file_excel');

        // Validate file upload with MIME type checking
        $allowedTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel'
        ];
        
        $validation = validate_file_upload($file, $allowedTypes, 5242880); // 5MB limit
        
        if (!$validation['valid']) {
            session()->setFlashdata('error', $validation['error']);
            return redirect()->to('/admin/siswa/import');
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            array_shift($rows);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                if (empty($row[0])) continue; // Skip empty rows

                try {
                    // Start transaction for each row
                    $db = \Config\Database::connect();
                    $db->transStart();

                    // Generate username from NIS if not provided
                    $username = !empty($row[7]) ? $row[7] : 'siswa_' . $row[1];
                    $password = !empty($row[8]) ? $row[8] : 'siswa123';

                    // 1. Create user account
                    $userData = [
                        'username' => $username,
                        'password' => $password,
                        'role' => 'siswa',
                        'email' => $row[6] ?? null,
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $userId = $this->userModel->insert($userData);

                    // 2. Create siswa data
                    $siswaData = [
                        'user_id' => $userId,
                        'nis' => $row[1],
                        'nama_lengkap' => $row[2],
                        'jenis_kelamin' => strtoupper($row[3]) == 'P' ? 'P' : 'L',
                        'kelas_id' => $this->getKelasIdByName($row[4]),
                        'tahun_ajaran' => $row[5],
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $this->siswaModel->insert($siswaData);

                    $db->transComplete();

                    if ($db->transStatus() === FALSE) {
                        throw new \Exception("Gagal import baris " . ($index + 2));
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    $db->transRollback();
                    $errorCount++;
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Import selesai. Berhasil: $successCount, Gagal: $errorCount";

            if (!empty($errors)) {
                session()->setFlashdata('import_errors', $errors);
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('/admin/siswa');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Error: ' . $e->getMessage());
            return redirect()->to('/admin/siswa/import');
        }
    }

    /**
     * Get kelas ID by name
     */
    private function getKelasIdByName($namaKelas)
    {
        $kelas = $this->kelasModel->where('nama_kelas', $namaKelas)->first();
        return $kelas ? $kelas['id'] : null;
    }

    /**
     * Get list of academic years
     */
    private function getTahunAjaranList()
    {
        $currentYear = date('Y');
        $years = [];

        for ($i = -2; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $years[] = ($year - 1) . '/' . $year;
        }

        return $years;
    }

    /**
     * Bulk action (activate/deactivate/delete)
     */
    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids');

        if (empty($ids)) {
            session()->setFlashdata('error', 'Tidak ada siswa yang dipilih');
            return redirect()->to('/admin/siswa');
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($ids as $id) {
            try {
                $siswa = $this->siswaModel->find($id);

                if (!$siswa) {
                    $errorCount++;
                    continue;
                }

                switch ($action) {
                    case 'activate':
                        $this->userModel->update($siswa['user_id'], ['is_active' => 1]);
                        $successCount++;
                        break;

                    case 'deactivate':
                        $this->userModel->update($siswa['user_id'], ['is_active' => 0]);
                        $successCount++;
                        break;

                    case 'delete':
                        $this->siswaModel->delete($id);
                        $this->userModel->delete($siswa['user_id']);
                        $successCount++;
                        break;
                }
            } catch (\Exception $e) {
                $errorCount++;
            }
        }

        $actionText = [
            'activate' => 'diaktifkan',
            'deactivate' => 'dinonaktifkan',
            'delete' => 'dihapus'
        ];

        $message = "Berhasil {$actionText[$action]} $successCount siswa";
        if ($errorCount > 0) {
            $message .= ", gagal $errorCount";
        }

        session()->setFlashdata('success', $message);
        return redirect()->to('/admin/siswa');
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'No',
            'NIS*',
            'Nama Lengkap*',
            'Jenis Kelamin* (L/P)',
            'Kelas* (nama kelas)',
            'Tahun Ajaran* (format: 2023/2024)',
            'Email',
            'Username',
            'Password'
        ];

        // Add headers
        foreach ($headers as $col => $header) {
            $sheet->setCellValue(chr(65 + $col) . '1', $header);
        }

        // Add sample data
        $sampleData = [
            ['1', '20230001', 'Andi Wijaya', 'L', 'X-RPL', '2023/2024', 'andi@email.com', 'andi_wijaya', 'password123'],
            ['2', '20230002', 'Siti Aminah', 'P', 'X-RPL', '2023/2024', 'siti@email.com', 'siti_aminah', 'password123'],
            ['3', '20230003', 'Budi Santoso', 'L', 'XI-RPL', '2023/2024', 'budi@email.com', 'budi_santoso', 'password123'],
        ];

        $row = 2;
        foreach ($sampleData as $data) {
            foreach ($data as $col => $value) {
                $sheet->setCellValue(chr(65 + $col) . $row, $value);
            }
            $row++;
        }

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ]
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Auto size columns
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'template-import-siswa.xlsx';

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

        $query = $this->userModel->where('username', $username);
        if ($userId) {
            $query->where('id !=', $userId);
        }
        $exists = $query->countAllResults() > 0;

        return $this->response->setJSON([
            'available' => !$exists,
            'message' => $exists ? 'Username sudah digunakan' : 'Username tersedia'
        ]);
    }
}
