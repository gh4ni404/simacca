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
    
    // Performance: Cache kelas lookups during import to avoid N+1 queries
    private $kelasCache = [];

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

            session()->setFlashdata('success', 'Welcome aboard! Siswa baru sudah terdaftar 🎒✨');
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
            session()->setFlashdata('error', 'Hmm, siswa ini nggak ketemu ??');
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
            session()->setFlashdata('error', 'Hmm, siswa ini nggak ketemu ??');
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

            session()->setFlashdata('success', 'Nice! Data siswa sudah diperbarui ??');
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
            session()->setFlashdata('error', 'Hmm, siswa ini nggak ketemu ??');
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

            session()->setFlashdata('success', 'Data siswa sudah dihapus ?');
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
            session()->setFlashdata('error', 'Hmm, siswa ini nggak ketemu ??');
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
            session()->setFlashdata('error', 'Hmm, siswa ini nggak ketemu ??');
            return redirect()->to('/admin/siswa');
        }

        $user = $this->userModel->find($siswa['user_id']);

        if (!$user) {
            session()->setFlashdata('error', 'Ups, user ini nggak ketemu ??');
            return redirect()->to('/admin/siswa');
        }

        // Toggle status
        $newStatus = $user['is_active'] ? 0 : 1;

        $this->userModel->update($siswa['user_id'], ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        session()->setFlashdata('success', $statusText == 'diaktifkan' ? 'Siswa aktif kembali! Let''s go ??' : 'Siswa dinonaktifkan. Take care! ??');

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
            $createdClasses = []; // Track kelas baru yang dibuat

            foreach ($rows as $index => $row) {
                // BUG FIX #7: Skip baris kosong dengan validasi lebih baik
                if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
                    continue; // Skip empty rows
                }

                $rowNumber = $index + 2; // Excel row number (header = row 1)

                try {
                    // BUG FIX #7: Validasi data sebelum insert
                    $nis = trim($row[1] ?? '');
                    $namaLengkap = trim($row[2] ?? '');
                    $jenisKelamin = strtoupper(trim($row[3] ?? ''));
                    $namaKelas = trim($row[4] ?? '');
                    $tahunAjaran = trim($row[5] ?? '');
                    
                    // Validasi field required
                    if (empty($nis)) {
                        throw new \Exception("NIS tidak boleh kosong");
                    }
                    if (empty($namaLengkap)) {
                        throw new \Exception("Nama lengkap tidak boleh kosong");
                    }
                    if (empty($namaKelas)) {
                        throw new \Exception("Nama kelas tidak boleh kosong");
                    }
                    if (empty($tahunAjaran)) {
                        throw new \Exception("Tahun ajaran tidak boleh kosong");
                    }
                    if (!in_array($jenisKelamin, ['L', 'P'])) {
                        throw new \Exception("Jenis kelamin harus L atau P");
                    }

                    // Start transaction for each row
                    // Note: Using manual DB connection is intentional for per-row transactions
                    // This allows partial success in bulk imports (CI4 best practice for bulk operations)
                    $db = \Config\Database::connect();
                    $db->transStart();

                    // Generate username from NIS if not provided
                    $username = !empty($row[7]) ? trim($row[7]) : 'siswa_' . $nis;
                    $password = !empty($row[8]) ? trim($row[8]) : 'siswa123';
                    $email = !empty($row[6]) ? trim($row[6]) : null;

                    // 1. Create user account
                    $userData = [
                        'username' => $username,
                        'password' => $password,
                        'role' => 'siswa',
                        'email' => $email,
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $userId = $this->userModel->insert($userData);
                    
                    if (!$userId) {
                        throw new \Exception("Gagal membuat user account");
                    }

                    // 2. Get or create kelas (track kelas baru)
                    $kelasId = $this->getKelasIdByName($namaKelas);
                    
                    // Track kelas baru yang dibuat
                    if (!isset($createdClasses[$namaKelas])) {
                        $createdClasses[$namaKelas] = true;
                    }

                    // 3. Create siswa data
                    $siswaData = [
                        'user_id' => $userId,
                        'nis' => $nis,
                        'nama_lengkap' => $namaLengkap,
                        'jenis_kelamin' => $jenisKelamin,
                        'kelas_id' => $kelasId,
                        'tahun_ajaran' => $tahunAjaran,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $siswaId = $this->siswaModel->insert($siswaData);
                    
                    if (!$siswaId) {
                        throw new \Exception("Gagal membuat data siswa");
                    }

                    $db->transComplete();

                    if ($db->transStatus() === FALSE) {
                        throw new \Exception("Transaksi database gagal");
                    }

                    $successCount++;
                } catch (\Exception $e) {
                    if (isset($db)) {
                        $db->transRollback();
                    }
                    $errorCount++;
                    
                    // BUG FIX #7: Error message lebih detail
                    $errorMsg = $e->getMessage();
                    
                    // Tambahkan context berdasarkan jenis error
                    if (strpos($errorMsg, 'Duplicate entry') !== false) {
                        if (strpos($errorMsg, 'nis') !== false) {
                            $errorMsg = "NIS '$nis' sudah terdaftar";
                        } elseif (strpos($errorMsg, 'username') !== false) {
                            $errorMsg = "Username '$username' sudah digunakan";
                        }
                    }
                    
                    $errors[] = "Baris $rowNumber (NIS: $nis, Nama: $namaLengkap): $errorMsg";
                }
            }
            
            // BUG FIX #7: Informasi kelas baru yang dibuat
            $kelasBaruInfo = count($createdClasses) > 0 
                ? " Kelas baru dibuat: " . implode(', ', array_keys($createdClasses)) . "."
                : "";

            $message = "Import selesai. Berhasil: $successCount, Gagal: $errorCount." . $kelasBaruInfo;

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
     * Get kelas ID by name, create if not exists
     * CI4 Performance: Uses caching to avoid N+1 queries during import
     */
    private function getKelasIdByName($namaKelas)
    {
        // BUG FIX #2: Validasi input nama kelas
        if (empty($namaKelas) || trim($namaKelas) === '') {
            throw new \Exception("Nama kelas tidak boleh kosong");
        }
        
        // Normalize whitespace
        $namaKelas = trim($namaKelas);
        
        // BUG FIX #4: Validasi panjang nama kelas (max 10 chars di database)
        if (strlen($namaKelas) > 10) {
            throw new \Exception("Nama kelas '$namaKelas' terlalu panjang (max 10 karakter)");
        }
        
        // CI4 Performance: Check cache first to avoid repeated DB queries
        if (isset($this->kelasCache[$namaKelas])) {
            return $this->kelasCache[$namaKelas];
        }
        
        // Cek apakah kelas sudah ada
        $kelas = $this->kelasModel->where('nama_kelas', $namaKelas)->first();
        
        if ($kelas) {
            // Cache the result
            $this->kelasCache[$namaKelas] = $kelas['id'];
            return $kelas['id'];
        }
        
        // Jika kelas belum ada, buat kelas baru
        // Parse nama kelas untuk mendapatkan tingkat dan jurusan
        // Format yang didukung: X-RPL, XI-RPL, XII-RPL, 10-RPL, 11-RPL, 12-RPL
        $tingkat = null;
        $jurusan = null;
        
        // BUG FIX #6: Konversi ke uppercase dulu sebelum preg_match
        $namaKelasUpper = strtoupper($namaKelas);
        
        // Coba parse dengan format "X-RPL" atau "10-RPL"
        if (preg_match('/^(X|XI|XII|10|11|12)[\s\-_](.+)$/', $namaKelasUpper, $matches)) {
            // Konversi tingkat romawi ke angka
            $tingkatMap = [
                'X' => '10',
                'XI' => '11', 
                'XII' => '12'
            ];
            
            $tingkatInput = $matches[1];
            $tingkat = isset($tingkatMap[$tingkatInput]) ? $tingkatMap[$tingkatInput] : $tingkatInput;
            $jurusan = trim($matches[2]);
        } else {
            // Jika format tidak sesuai, gunakan default
            $tingkat = '10';
            $jurusan = $namaKelas;
        }
        
        // BUG FIX #3: Validasi tingkat harus 10, 11, atau 12
        if (!in_array($tingkat, ['10', '11', '12'])) {
            throw new \Exception("Tingkat kelas '$namaKelas' tidak valid. Format yang didukung: X-XXX, XI-XXX, XII-XXX, atau 10-XXX, 11-XXX, 12-XXX");
        }
        
        // BUG FIX #4: Validasi panjang jurusan (max 50 chars)
        if (strlen($jurusan) > 50) {
            throw new \Exception("Nama jurusan untuk kelas '$namaKelas' terlalu panjang (max 50 karakter)");
        }
        
        // Buat kelas baru
        $kelasData = [
            'nama_kelas' => $namaKelas,
            'tingkat' => $tingkat,
            'jurusan' => $jurusan,
            'wali_kelas_id' => null
        ];
        
        try {
            // CI4 Best Practice: Use insert() with skipValidation parameter
            // Skip validation to avoid is_unique constraint during auto-create
            $this->kelasModel->skipValidation(true);
            
            try {
                $kelasId = $this->kelasModel->insert($kelasData);
                
                // BUG FIX #1: Double check untuk handle race condition
                if (!$kelasId) {
                    $kelas = $this->kelasModel->where('nama_kelas', $namaKelas)->first();
                    if ($kelas) {
                        return $kelas['id'];
                    }
                    throw new \Exception("Gagal membuat kelas '$namaKelas'");
                }
                
                // Cache the newly created kelas
                $this->kelasCache[$namaKelas] = $kelasId;
                
                return $kelasId;
            } finally {
                // Always restore validation state (CI4 Best Practice)
                $this->kelasModel->skipValidation(false);
            }
        } catch (\Exception $e) {
            // BUG FIX #1: Handle duplicate key error (race condition)
            if (strpos($e->getMessage(), 'Duplicate entry') !== false || 
                strpos($e->getMessage(), 'UNIQUE constraint') !== false) {
                // Kelas sudah dibuat oleh thread lain, cari lagi
                $kelas = $this->kelasModel->where('nama_kelas', $namaKelas)->first();
                if ($kelas) {
                    // Cache the result
                    $this->kelasCache[$namaKelas] = $kelas['id'];
                    return $kelas['id'];
                }
            }
            
            // BUG FIX #7: Error message lebih informatif
            throw new \Exception("Gagal membuat kelas '$namaKelas': " . $e->getMessage());
        }
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
            session()->setFlashdata('error', 'Eh, pilih siswanya dulu dong ??');
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
