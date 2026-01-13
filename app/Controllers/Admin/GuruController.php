<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\MataPelajaranModel;
use App\Models\KelasModel;

class GuruController extends BaseController
{
    protected $userModel;
    protected $guruModel;
    protected $mapelModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->mapelModel = new MataPelajaranModel();
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
        $data = [
            'title' => 'Manajemen Guru',
            'pageTitle' => 'Data Guru',
            'pageDescription' => 'Kelola data guru dan wali kelas',
            'user' => $this->getUserData(),
            'guru' => $this->guruModel->getAllGuru(),
            'totalGuru' => $this->guruModel->countAll(),
            'waliKelas' => $this->guruModel->getWaliKelas(),
            'guruNonWali' => $this->guruModel->getGuruNonWali()
        ];

        return view('admin/guru/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Guru Baru',
            'pageTitle' => 'Tambah Data Guru',
            'pageDescription' => 'Form untuk menambahkan guru baru',
            'user' => $this->getUserData(),
            'mapelList' => $this->mapelModel->getListMapel(),
            'kelasList' => $this->kelasModel->getListKelas(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/guru/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'nip' => 'required|is_unique[guru.nip]',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'username' => 'required|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'email' => 'valid_email',
            'role' => 'required|in_list[guru_mapel,wali_kelas]',
            'mata_pelajaran_id' => 'permit_empty',
            'is_wali_kelas' => 'permit_empty'
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
                'role' => $this->request->getPost('role'),
                'email' => $this->request->getPost('email'),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $userId = $this->userModel->insert($userData);

            // 2. Create guru data
            $guruData = [
                'user_id' => $userId,
                'nip' => $this->request->getPost('nip'),
                'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id') ?: null,
                'is_wali_kelas' => $this->request->getPost('is_wali_kelas') ? 1 : 0,
                'kelas_id' => $this->request->getPost('kelas_id') ?: null,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->guruModel->insert($guruData);

            // 3. If this guru is a wali kelas, update kelas table
            if ($this->request->getPost('is_wali_kelas') && $this->request->getPost('kelas_id')) {
                $guruId = $this->guruModel->getInsertID();
                $this->kelasModel->update($this->request->getPost('kelas_id'), [
                    'wali_kelas_id' => $guruId
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal menyimpan data');
            }

            session()->setFlashdata('success', 'Yeay! Guru baru berhasil ditambahkan 🎓✨');
            return redirect()->to('/admin/guru');
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
        $guru = $this->guruModel->getGuruWithMapel($id);

        if (!$guru) {
            session()->setFlashdata('error', 'Ups, guru ini nggak ketemu ??');
            return redirect()->to('/admin/guru');
        }

        // Get user data
        $user = $this->userModel->find($guru['user_id']);

        $data = [
            'title' => 'Edit Data Guru',
            'pageTitle' => 'Edit Data Guru',
            'pageDescription' => 'Form untuk mengubah data guru',
            'user' => $this->getUserData(),
            'guru' => $guru,
            'userData' => $user,
            'mapelList' => $this->mapelModel->getListMapel(),
            'kelasList' => $this->kelasModel->getListKelas(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/guru/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $guru = $this->guruModel->find($id);

        if (!$guru) {
            session()->setFlashdata('error', 'Ups, guru ini nggak ketemu ??');
            return redirect()->to('/admin/guru');
        }

        // Validation rules
        $rules = [
            'nip' => 'required|is_unique[guru.nip,id,' . $id . ']',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'email' => 'valid_email',
            'role' => 'required|in_list[guru_mapel,wali_kelas]',
            'mata_pelajaran_id' => 'permit_empty',
            'is_wali_kelas' => 'permit_empty'
        ];

        // Jika username berubah
        $userData = $this->userModel->find($guru['user_id']);
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
                'role' => $this->request->getPost('role'),
                'email' => $this->request->getPost('email')
            ];

            // Update password jika diisi
            if ($this->request->getPost('password')) {
                // $userUpdateData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
                $userUpdateData['password'] = $this->request->getPost('password');
            }

            $this->userModel->update($guru['user_id'], $userUpdateData);

            // 2. Update guru data
            $guruData = [
                'nip' => $this->request->getPost('nip'),
                'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id') ?: null,
                'is_wali_kelas' => $this->request->getPost('is_wali_kelas') ? 1 : 0,
                'kelas_id' => $this->request->getPost('kelas_id') ?: null
            ];

            $this->guruModel->update($id, $guruData);
            // dd($this->guruModel->getLastQuery()->getQuery());

            // 3. Handle wali kelas assignment
            $kelasId = $this->request->getPost('kelas_id');
            $isWaliKelas = $this->request->getPost('is_wali_kelas');

            // Reset wali kelas sebelumnya jika ada
            if ($guru['is_wali_kelas'] && $guru['kelas_id']) {
                $this->kelasModel->update($guru['kelas_id'], ['wali_kelas_id' => null]);
            }

            // Set wali kelas baru jika dipilih
            if ($isWaliKelas && $kelasId) {
                // Cek apakah kelas sudah punya wali kelas lain
                $currentWali = $this->kelasModel->where('id', $kelasId)->first();
                if ($currentWali && $currentWali['wali_kelas_id'] && $currentWali['wali_kelas_id'] != $id) {
                    throw new \Exception('Kelas ini sudah memiliki wali kelas');
                }

                $this->kelasModel->update($kelasId, ['wali_kelas_id' => $id]);
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal mengupdate data');
            }

            session()->setFlashdata('success', 'Sip! Data guru sudah diperbarui 👍');
            return redirect()->to('/admin/guru');
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
        $guru = $this->guruModel->find($id);

        if (!$guru) {
            session()->setFlashdata('error', 'Ups, guru ini nggak ketemu ??');
            return redirect()->to('/admin/guru');
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Reset wali kelas jika guru ini adalah wali kelas
            if ($guru['is_wali_kelas'] && $guru['kelas_id']) {
                $this->kelasModel->update($guru['kelas_id'], ['wali_kelas_id' => null]);
            }

            // 2. Delete guru data
            $this->guruModel->delete($id);

            // 3. Delete user account
            $this->userModel->delete($guru['user_id']);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal menghapus data');
            }

            session()->setFlashdata('success', 'Done! Data guru sudah dihapus ✓');
            return redirect()->to('/admin/guru');
        } catch (\Exception $e) {
            $db->transRollback();
            session()->setFlashdata('error', $e->getMessage());
            return redirect()->to('/admin/guru');
        }
    }

    /**
     * Show detail of specified resource.
     */
    public function show($id)
    {
        $guru = $this->guruModel->getGuruWithMapel($id);

        if (!$guru) {
            session()->setFlashdata('error', 'Ups, guru ini nggak ketemu ??');
            return redirect()->to('/admin/guru');
        }

        // Get user data
        $user = $this->userModel->find($guru['user_id']);

        // Get kelas data if wali kelas
        $kelas = null;
        if ($guru['is_wali_kelas'] && $guru['kelas_id']) {
            $kelas = $this->kelasModel->find($guru['kelas_id']);
        }

        $data = [
            'title' => 'Detail Guru',
            'pageTitle' => 'Detail Data Guru',
            'pageDescription' => 'Informasi lengkap data guru',
            'user' => $this->getUserData(),
            'guru' => $guru,
            'userData' => $user,
            'kelas' => $kelas
        ];

        return view('admin/guru/show', $data);
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

        $query = $this->guruModel->where('nip', $nip);

        if ($id) {
            $query->where('id !=', $id);
        }

        $exists = $query->countAllResults() > 0;

        return $this->response->setJSON([
            'available' => !$exists,
            'message' => $exists ? 'NIP sudah digunakan' : 'NIP tersedia'
        ]);
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

    /**
     * Change guru status (active/inactive)
     */
    public function changeStatus($id)
    {
        $guru = $this->guruModel->find($id);

        if (!$guru) {
            session()->setFlashdata('error', 'Ups, guru ini nggak ketemu 🔍');
            return redirect()->to('/admin/guru');
        }

        $user = $this->userModel->find($guru['user_id']);

        if (!$user) {
            session()->setFlashdata('error', 'Ups, user ini nggak ketemu 🔍');
            return redirect()->to('/admin/guru');
        }

        // Toggle status
        $newStatus = $user['is_active'] ? 0 : 1;

        $this->userModel->update($guru['user_id'], ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        $newMessage = $statusText == 'diaktifkan' ? 'Guru diaktifkan! Siap mengajar lagi 🚀' : 'Guru dinonaktifkan. See you soon! 👋';
        session()->setFlashdata('success', $newMessage);

        return redirect()->to('/admin/guru');
    }

    /**
     * Export data guru to Excel
     */
    public function export()
    {
        $guru = $this->guruModel->getAllGuru();

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

                // Start transaction for each row
                $db = \Config\Database::connect();
                $db->transStart();

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
                    $role = in_array($role, ['guru_mapel', 'wali_kelas']) ? $role : 'guru_mapel';

                    // Cek duplikasi NIP
                    $existingNip = $this->guruModel->where('nip', $nip)->first();
                    if ($existingNip) {
                        throw new \Exception("NIP sudah terdaftar: {$nip}");
                    }

                    // Cek duplikasi username
                    $existingUsername = $this->userModel->where('username', $username)->first();
                    if ($existingUsername) {
                        throw new \Exception("Username sudah terdaftar: {$username}");
                    }

                    // 1. Create user account
                    $userData = [
                        'username' => $username,
                        'password' => $password,
                        'role' => $role,
                        'email' => !empty($email) ? $email : null,
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $userId = $this->userModel->insert($userData);

                    if (!$userId) {
                        throw new \Exception("Gagal membuat akun user");
                    }

                    // 2. Create Guru data
                    $guruData = [
                        'user_id' => $userId,
                        'nip' => $nip,
                        'nama_lengkap' => $namaLengkap,
                        'jenis_kelamin' => $jenisKelamin,
                        'mata_pelajaran_id' => !empty($mapelId) ? $mapelId : null,
                        'kelas_id' => !empty($kelasId) ? $kelasId : null,
                        'is_wali_kelas' => $isWaliKelas,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $this->guruModel->insert($guruData);

                    // 3. Jika wali kelas, update kelas
                    if ($isWaliKelas && !empty($kelasId)) {
                        $guruId = $this->guruModel->getInsertID();
                        $this->kelasModel->update($kelasId, [
                            'wali_kelas_id' => $guruId
                        ]);
                    }

                    $db->transComplete();

                    if ($db->transStatus() === false) {
                        throw new \Exception('Gagal menyimpan data transaksi');
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    $db->transRollback();
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
            'G1' => 'ROLE (guru_mapel / wali_kelas)',
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
