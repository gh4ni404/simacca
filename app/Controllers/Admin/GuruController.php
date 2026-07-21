<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\GuruService;
use App\Models\RoleModel;
use App\Models\UserRoleModel;

class GuruController extends BaseController
{
    protected $guruService;

    public function __construct()
    {
        $this->guruService = new GuruService();

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
        $guruResult = $this->guruService->getAllGuru();
        $statsResult = $this->guruService->getStatistics();

        $data = [
            'title' => 'Manajemen Guru',
            'pageTitle' => 'Data Guru',
            'pageDescription' => 'Kelola data guru dan wali kelas',
            'user' => $this->getUserData(),
            'guru' => $guruResult['data'] ?? [],
            'totalGuru' => $statsResult['data']['totalGuru'] ?? 0,
            'waliKelas' => $statsResult['data']['waliKelas'] ?? [],
            'guruNonWali' => $statsResult['data']['guruNonWali'] ?? []
        ];

        return view('admin/guru/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $listsResult = $this->guruService->getFormLists();
        $roleModel = new RoleModel();

        $data = [
            'title' => 'Tambah Guru Baru',
            'pageTitle' => 'Tambah Data Guru',
            'pageDescription' => 'Form untuk menambahkan guru baru',
            'user' => $this->getUserData(),
            'mapelList' => $listsResult['data']['mapelList'] ?? [],
            'kelasList' => $listsResult['data']['kelasList'] ?? [],
            'roleList' => $roleModel->getDropdown(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/guru/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $roles = $this->request->getPost('roles') ?? [];
        $isKetuaJurusan = in_array('ketua_jurusan', $roles) ? 1 : 0;
        $data = [
            'nip' => $this->request->getPost('nip'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'email' => $this->request->getPost('email'),
            'roles' => $roles,
            'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id') ?: null,
            'is_wali_kelas' => $this->request->getPost('is_wali_kelas') ? 1 : 0,
            'kelas_id' => $this->request->getPost('kelas_id') ?: null,
            'jurusan' => $isKetuaJurusan ? ($this->request->getPost('jurusan') ?: null) : null,
            'is_ketua_jurusan' => $isKetuaJurusan
        ];

        $result = $this->guruService->createGuru($data);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        session()->setFlashdata('success', 'Yeay! Guru baru berhasil ditambahkan 🎓✨');
        return redirect()->to('/admin/guru');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $guruResult = $this->guruService->getGuruById($id);

        if (!$guruResult['success']) {
            session()->setFlashdata('error', 'Ups, guru ini nggak ketemu 🔍');
            return redirect()->to('/admin/guru');
        }

        $listsResult = $this->guruService->getFormLists();
        $roleModel = new RoleModel();
        $userRoleModel = new UserRoleModel();
        $allRoles = $userRoleModel->getRolesByUserId($guruResult['data']['user']['id']);

        $data = [
            'title' => 'Edit Data Guru',
            'pageTitle' => 'Edit Data Guru',
            'pageDescription' => 'Form untuk mengubah data guru',
            'user' => $this->getUserData(),
            'guru' => $guruResult['data']['guru'],
            'userData' => $guruResult['data']['user'],
            'mapelList' => $listsResult['data']['mapelList'] ?? [],
            'kelasList' => $listsResult['data']['kelasList'] ?? [],
            'roleList' => $roleModel->getDropdown(),
            'allRoles' => $allRoles,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/guru/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $roles = $this->request->getPost('roles') ?? [];
        $isKetuaJurusan = in_array('ketua_jurusan', $roles) ? 1 : 0;
        $data = [
            'nip' => $this->request->getPost('nip'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'email' => $this->request->getPost('email'),
            'roles' => $roles,
            'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id') ?: null,
            'is_wali_kelas' => $this->request->getPost('is_wali_kelas') ? 1 : 0,
            'kelas_id' => $this->request->getPost('kelas_id') ?: null,
            'jurusan' => $isKetuaJurusan ? ($this->request->getPost('jurusan') ?: null) : null,
            'is_ketua_jurusan' => $isKetuaJurusan
        ];

        $result = $this->guruService->updateGuru($id, $data);

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        session()->setFlashdata('success', 'Sip! Data guru sudah diperbarui 👍');
        return redirect()->to('/admin/guru');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $result = $this->guruService->deleteGuru($id);

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
        } else {
            session()->setFlashdata('success', 'Done! Data guru sudah dihapus ✓');
        }

        return redirect()->to('/admin/guru');
    }

    /**
     * Show detail of specified resource.
     */
    public function show($id)
    {
        $guruResult = $this->guruService->getGuruById($id);

        if (!$guruResult['success']) {
            session()->setFlashdata('error', 'Ups, guru ini nggak ketemu 🔍');
            return redirect()->to('/admin/guru');
        }

        $userRoleModel = new UserRoleModel();
        $roleModel = new RoleModel();
        $allRoles = $userRoleModel->getRolesByUserId($guruResult['data']['user']['id']);

        $data = [
            'title' => 'Detail Guru',
            'pageTitle' => 'Detail Data Guru',
            'pageDescription' => 'Informasi lengkap data guru',
            'user' => $this->getUserData(),
            'guru' => $guruResult['data']['guru'],
            'userData' => $guruResult['data']['user'],
            'kelas' => $guruResult['data']['kelas'],
            'allRoles' => $allRoles,
            'roleList' => $roleModel->getDropdown()
        ];

        return view('admin/guru/show', $data);
    }

    /**
     * AJAX: Update guru roles (quick role management from detail page)
     */
    public function updateRoles($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $guruResult = $this->guruService->getGuruById($id);
        if (!$guruResult['success']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Guru tidak ditemukan']);
        }

        $roles = $this->request->getPost('roles') ?? [];
        $userId = $guruResult['data']['user']['id'];

        if (empty($roles)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pilih minimal satu role']);
        }

        try {
            $userRoleModel = new UserRoleModel();
            $userRoleModel->syncRoles($userId, $roles);

            // Update primary role in users table
            $primaryRole = $roles[0];
            $userModel = new \App\Models\UserModel();
            $userModel->skipValidation(true);
            $userModel->update($userId, ['role' => $primaryRole]);
            $userModel->skipValidation(false);

            // Update jurusan/is_ketua_jurusan in guru table
            $isKetuaJurusan = in_array('ketua_jurusan', $roles) ? 1 : 0;
            $jurusan = $isKetuaJurusan ? ($this->request->getPost('jurusan') ?: null) : null;
            $guruModel = new \App\Models\GuruModel();
            $guruModel->skipValidation(true);
            $guruModel->update($id, [
                'is_ketua_jurusan' => $isKetuaJurusan,
                'jurusan'          => $jurusan
            ]);
            $guruModel->skipValidation(false);

            $roleLabels = [];
            $roleModel = new RoleModel();
            foreach ($roles as $r) {
                $roleLabels[] = $roleModel->getDisplayName($r);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Role berhasil diperbarui',
                'roles' => $roles,
                'role_labels' => $roleLabels
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui role: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: Check NIP availability
     */
    public function checkNip()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/guru');
        }

        $nip = $this->request->getPost('nip');
        $id = $this->request->getPost('id');

        $result = $this->guruService->checkNipAvailability($nip, $id);

        return $this->response->setJSON($result['data']);
    }

    /**
     * AJAX: Check username availability
     */
    public function checkUsername()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/guru');
        }

        $username = $this->request->getPost('username');
        $userId = $this->request->getPost('user_id');

        $result = $this->guruService->checkUsernameAvailability($username, $userId);

        return $this->response->setJSON($result['data']);
    }

    /**
     * Change guru status (active/inactive)
     */
    public function changeStatus($id)
    {
        $result = $this->guruService->changeStatus($id);

        if (!$result['success']) {
            session()->setFlashdata('error', $result['message']);
        } else {
            $newStatus = $result['data']['new_status'];
            $message = $newStatus ? 'Guru diaktifkan! Siap mengajar lagi 🚀' : 'Guru dinonaktifkan. See you soon! 👋';
            session()->setFlashdata('success', $message);
        }

        return redirect()->to('/admin/guru');
    }

    /**
     * Export data guru to Excel
     */
    public function export()
    {
        $guruResult = $this->guruService->getAllGuru();
        $guru = $guruResult['data'] ?? [];

        // Create Excel file using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'NIP');
        $sheet->setCellValue('C1', 'NAMA GURU');
        $sheet->setCellValue('D1', 'JENIS KELAMIN');
        $sheet->setCellValue('E1', 'MATA PELAJARAN');
        $sheet->setCellValue('F1', 'ROLE');
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
        foreach ($guru as $g) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $g['nip']);
            $sheet->setCellValue('C' . $row, $g['nama_lengkap']);
            $sheet->setCellValue('D' . $row, $g['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('E' . $row, $g['nama_mapel'] ?? '-');
            $sheet->setCellValue('F' . $row, $g['is_wali_kelas'] ? 'Wali Kelas' : 'Guru Mapel');
            $sheet->setCellValue('G' . $row, $g['is_active'] ? 'Aktif' : 'Nonaktif');
            $sheet->setCellValue('H' . $row, $g['email'] ?? '-');
            $sheet->setCellValue('I' . $row, $g['username']);

            $row++;
        }

        // Auto size columns
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'data-guru-' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    /**
     * Import data guru from Excel
     */
    public function import()
    {
        $data = [
            'title' => 'Import Data Guru',
            'pageTitle' => 'Import Data Guru',
            'pageDescription' => 'Upload file Excel untuk import data guru',
            'user' => $this->getUserData()
        ];

        return view('admin/guru/import', $data);
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
            return redirect()->to('/admin/guru/import');
        }

        try {
            // Load spreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // skip header row
            array_shift($rows);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                if (empty($row[0])) continue; // Skip empty rows

                try {
                    $nip = trim($row[0]);
                    $namaLengkap = trim($row[1]);
                    $jenisKelamin = strtoupper(trim($row[2]));
                    $username = trim($row[3]);
                    $password = trim($row[4]);
                    $email = trim($row[5]);
                    $role = trim($row[6]);
                    $mapelId = isset($row[7]) ? trim($row[7]) : null;
                    $kelasId = isset($row[8]) ? trim($row[8]) : null;
                    $isWaliKelas = (isset($row[9]) && $row[9] == 1) ? 1 : 0;

                    // Validasi data
                    if (empty($nip) || empty($namaLengkap) || empty($username) || empty($password) || empty($role)) {
                        throw new \Exception("Data tidak lengkap pada baris " . ($index + 2));
                    }

                    // Validasi jenis kelamin
                    $jenisKelamin = ($jenisKelamin == 'L' || $jenisKelamin == 'P') ? $jenisKelamin : 'L';

                    // Validasi role
                    $role = in_array($role, ['guru_mapel', 'wali_kelas', 'wakakur']) ? $role : 'guru_mapel';

                    // Create guru using service
                    $guruData = [
                        'nip' => $nip,
                        'nama_lengkap' => $namaLengkap,
                        'jenis_kelamin' => $jenisKelamin,
                        'username' => $username,
                        'password' => $password,
                        'email' => !empty($email) ? $email : null,
                        'role' => $role,
                        'mata_pelajaran_id' => !empty($mapelId) ? $mapelId : null,
                        'kelas_id' => !empty($kelasId) ? $kelasId : null,
                        'is_wali_kelas' => $isWaliKelas
                    ];

                    $result = $this->guruService->createGuru($guruData);

                    if (!$result['success']) {
                        throw new \Exception(implode(', ', $result['errors']));
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Import selesai. Berhasil: {$successCount}, Gagal: {$errorCount}";

            if (!empty($errors)) {
                session()->setFlashdata('import_errors', $errors);
            }

            if ($errorCount > 0 && $successCount == 0) {
                session()->setFlashdata('error', $message);
            } else {
                session()->setFlashdata('success', $message);
            }

            return redirect()->to('/admin/guru');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Waduh, file-nya bermasalah nih 😅 Coba cek lagi ya');
            return redirect()->to('/admin/guru/import');
        }
    }

    /**
     * Download template Excel import guru
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Guru');

        // Header
        $headers = [
            'A1' => 'NIP',
            'B1' => 'NAMA LENGKAP',
            'C1' => 'JENIS KELAMIN (L/P)',
            'D1' => 'USERNAME',
            'E1' => 'PASSWORD',
            'F1' => 'EMAIL',
            'G1' => 'ROLE (guru_mapel / wali_kelas / wakakur)',
            'H1' => 'MATA_PELAJARAN_ID',
            'I1' => 'KELAS_ID',
            'J1' => 'IS_WALI_KELAS (1/0)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Styling header
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFDDEEFF']
            ]
        ]);

        // Contoh data
        $sheet->fromArray([
            [
                '1987654321',
                'Budi Santoso',
                'L',
                'budi.santoso',
                'password123',
                'budi@email.com',
                'guru_mapel',
                2,
                '',
                0
            ],
            [
                '1987654322',
                'Siti Aminah',
                'P',
                'siti.aminah',
                'password123',
                'siti@email.com',
                'wali_kelas',
                '',
                3,
                1
            ],
            [
                '1122334455',
                'Ahmad Wakakur',
                'L',
                'ahmad.wakakur',
                'password123',
                'ahmad@email.com',
                'wakakur',
                1,
                5,
                1
            ]
        ], null, 'A2');

        // Auto width
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze header
        $sheet->freezePane('A2');

        // Output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template-import-guru.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }
}
