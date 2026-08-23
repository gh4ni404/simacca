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
        $isWaliKelas = in_array('wali_kelas', $roles) ? 1 : 0;
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
            'is_wali_kelas' => $isWaliKelas,
            'kelas_id' => $isWaliKelas ? ($this->request->getPost('kelas_id') ?: null) : null,
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
        $isWaliKelas = in_array('wali_kelas', $roles) ? 1 : 0;
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
            'is_wali_kelas' => $isWaliKelas,
            'kelas_id' => $isWaliKelas ? ($this->request->getPost('kelas_id') ?: null) : null,
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
        $listsResult = $this->guruService->getFormLists();

        $data = [
            'title' => 'Detail Guru',
            'pageTitle' => 'Detail Data Guru',
            'pageDescription' => 'Informasi lengkap data guru',
            'user' => $this->getUserData(),
            'guru' => $guruResult['data']['guru'],
            'userData' => $guruResult['data']['user'],
            'kelas' => $guruResult['data']['kelas'],
            'kelasList' => $listsResult['data']['kelasList'] ?? [],
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

        $isWaliKelas = in_array('wali_kelas', $roles) ? 1 : 0;
        $isKetuaJurusan = in_array('ketua_jurusan', $roles) ? 1 : 0;
        $kelasId = $isWaliKelas ? ($this->request->getPost('kelas_id') ?: ($guruResult['data']['guru']['kelas_id'] ?? null)) : null;
        $jurusan = $isKetuaJurusan ? ($this->request->getPost('jurusan') ?: ($guruResult['data']['guru']['jurusan'] ?? null)) : null;

        if ($isWaliKelas && empty($kelasId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kelas wajib dipilih jika role Wali Kelas diaktifkan']);
        }
        if ($isKetuaJurusan && empty($jurusan)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Jurusan wajib diisi jika role Ketua Jurusan diaktifkan']);
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

            // Update status & atribut in guru table
            $guruModel = new \App\Models\GuruModel();
            $guruModel->skipValidation(true);
            $guruModel->update($id, [
                'is_wali_kelas'    => $isWaliKelas,
                'kelas_id'         => $kelasId,
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
     * Diagnostic Test for Multi-Role Teachers (Admin Quick Test)
     */
    public function testMultiRole()
    {
        $diagnosticResult = $this->guruService->runMultiRoleDiagnostic();

        if ($this->request->isAJAX() || $this->request->getGet('json') === '1') {
            return $this->response->setJSON($diagnosticResult);
        }

        $data = [
            'title'           => 'Quick Test Multi-Role Guru',
            'pageTitle'       => 'Audit & Quick Test Multi-Role Guru',
            'pageDescription' => 'Hasil diagnosa otomatis integritas data, sinkronisasi role, dan simulasi hidrasi session guru',
            'user'            => $this->getUserData(),
            'summary'         => $diagnosticResult['data']['summary'] ?? [],
            'diagnostics'     => $diagnosticResult['data']['diagnostics'] ?? []
        ];

        return view('admin/guru/test_multi_role', $data);
    }

    /**
     * Auto-fix multi-role attribute inconsistencies
     */
    public function fixMultiRole()
    {
        $result = $this->guruService->autoFixInconsistencies();
        if ($result['success']) {
            $count = $result['data']['fixed_count'] ?? 0;
            session()->setFlashdata('success', "Audit berhasil! {$count} data guru berhasil diperbaiki/disinkronkan ✨");
        } else {
            session()->setFlashdata('error', 'Gagal melakukan perbaikan otomatis.');
        }

        return redirect()->to('/admin/guru/test-multi-role');
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

            // Cache Mapel & Kelas for smart name-based & normalized fuzzy lookup
            $mapelModel = new \App\Models\MataPelajaranModel();
            $allMapels  = $mapelModel->findAll();
            $mapelMap   = []; // normalized_name => id
            $mapelListNames = [];
            foreach ($allMapels as $m) {
                $rawName  = trim($m['nama_mapel']);
                $cleanName = preg_replace('/[^a-z0-9]/', '', strtolower($rawName));
                $mapelMap[$cleanName] = $m['id'];
                $mapelMap[strtolower($rawName)] = $m['id'];
                if (!empty($m['kode_mapel'])) {
                    $mapelMap[strtolower(trim($m['kode_mapel']))] = $m['id'];
                }
                $mapelMap[(string)$m['id']] = $m['id'];
                $mapelListNames[] = $rawName;
            }

            $activeTA   = get_active_tahun_ajaran();
            $kelasModel = new \App\Models\KelasModel();
            $allKelas   = $kelasModel->where('tahun_ajaran', $activeTA)->findAll();
            $kelasMap   = []; // normalized_name => id
            $kelasListNames = [];
            foreach ($allKelas as $k) {
                $rawKelas  = trim($k['nama_kelas']);
                $cleanKelas = preg_replace('/[^a-z0-9]/', '', strtolower($rawKelas));
                $kelasMap[$cleanKelas] = $k['id'];
                $kelasMap[strtolower($rawKelas)] = $k['id'];
                $kelasMap[(string)$k['id']] = $k['id'];
                $kelasListNames[] = $rawKelas;
            }

            $roleMap = [
                'guru mapel'               => 'guru_mapel',
                'guru mata pelajaran'      => 'guru_mapel',
                'guru'                     => 'guru_mapel',
                'guru_mapel'               => 'guru_mapel',
                'pengajar'                 => 'guru_mapel',
                'wali kelas'               => 'wali_kelas',
                'walikelas'                => 'wali_kelas',
                'wali_kelas'               => 'wali_kelas',
                'wali'                     => 'wali_kelas',
                'wakakur'                  => 'wakakur',
                'wakil kepala kurikulum'   => 'wakakur',
                'waka kurikulum'           => 'wakakur',
                'wakil kurikulum'          => 'wakakur',
                'ketua jurusan'            => 'ketua_jurusan',
                'kajur'                    => 'ketua_jurusan',
                'kaprog'                   => 'ketua_jurusan',
                'kepala program'           => 'ketua_jurusan',
                'ketua program keahlian'   => 'ketua_jurusan',
                'ketua_jurusan'            => 'ketua_jurusan',
                'kepala sekolah'           => 'kepala_sekolah',
                'kepsek'                   => 'kepala_sekolah',
                'kepala_sekolah'           => 'kepala_sekolah',
                'tendik'                   => 'tendik',
                'tenaga pendidik'          => 'tendik',
                'staf'                     => 'tendik',
                'tu'                       => 'tendik',
                'tata usaha'               => 'tendik',
                'admin'                    => 'admin',
                'administrator'            => 'admin',
            ];

            // Pre-cache all existing NIPs and Usernames for O(1) fast lookup (eliminates N+1 queries)
            $guruModelCheck = new \App\Models\GuruModel();
            $userModelCheck = new \App\Models\UserModel();
            $allGurusCheck  = $guruModelCheck->select('nip, nama_lengkap')->findAll();
            $existingNipMap = [];
            foreach ($allGurusCheck as $gCheck) {
                $existingNipMap[trim($gCheck['nip'])] = $gCheck['nama_lengkap'];
            }
            $allUsersCheck     = $userModelCheck->select('username')->findAll();
            $existingUsernames = array_flip(array_column($allUsersCheck, 'username'));

            foreach ($rows as $index => $row) {
                if (empty($row[0])) continue; // Skip empty rows

                try {
                    $rawNip       = $row[0] ?? '';
                    if (is_numeric($rawNip) || (is_string($rawNip) && preg_match('/^[0-9.]+[eE]\+[0-9]+$/', trim($rawNip)))) {
                        $nip = sprintf('%.0f', (float)$rawNip);
                    } else {
                        $nip = trim((string)$rawNip);
                    }
                    $nip          = preg_replace('/\s+/', '', $nip);
                    $namaLengkap  = trim($row[1]);
                    $jkInput      = strtolower(trim($row[2] ?? ''));
                    $username     = trim($row[3] ?? '');
                    $password     = trim($row[4] ?? '');
                    $email        = trim($row[5] ?? '');
                    $roleRaw      = trim($row[6] ?? '');
                    $mapelInput   = trim($row[7] ?? '');
                    $kelasInput   = trim($row[8] ?? '');
                    $jurusanInput = trim($row[9] ?? '');

                    // Validasi data dasar
                    if (empty($nip) || empty($namaLengkap)) {
                        throw new \Exception("NIP dan Nama Lengkap wajib diisi pada baris " . ($index + 2));
                    }

                    // Jenis kelamin (L/P)
                    $jenisKelamin = (str_contains($jkInput, 'p') || str_contains($jkInput, 'perempuan')) ? 'P' : 'L';

                    // Username & Password otomatis jika kosong
                    if (empty($username)) {
                        $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nip));
                    }
                    if (empty($password)) {
                        $password = 'smk' . (strlen($nip) >= 4 ? substr($nip, -4) : '1234');
                    }

                    // Parse Roles
                    $parsedRoles = [];
                    if (!empty($roleRaw)) {
                        $parts = array_map('trim', explode(',', $roleRaw));
                        foreach ($parts as $p) {
                            $lowerP = strtolower(trim($p));
                            $cleanP = strtolower(trim(preg_replace('/\s+/', ' ', str_replace(['_', '-'], ' ', $p))));
                            if (isset($roleMap[$cleanP])) {
                                $parsedRoles[] = $roleMap[$cleanP];
                            } elseif (isset($roleMap[$lowerP])) {
                                $parsedRoles[] = $roleMap[$lowerP];
                            }
                        }
                    }

                    // If Kelas Wali is filled, auto-add wali_kelas role if missing
                    if (!empty($kelasInput) && !in_array('wali_kelas', $parsedRoles)) {
                        $parsedRoles[] = 'wali_kelas';
                    }

                    // If Jurusan is filled, auto-add ketua_jurusan role if missing
                    if (!empty($jurusanInput) && !in_array('ketua_jurusan', $parsedRoles)) {
                        $parsedRoles[] = 'ketua_jurusan';
                    }

                    if (empty($parsedRoles)) {
                        $parsedRoles = ['guru_mapel'];
                    }

                    // Smart Lookup Mata Pelajaran ID (exact & normalized)
                    $mapelId = null;
                    if (!empty($mapelInput)) {
                        $cleanInput = preg_replace('/[^a-z0-9]/', '', strtolower($mapelInput));
                        if (isset($mapelMap[$cleanInput])) {
                            $mapelId = $mapelMap[$cleanInput];
                        } else {
                            $availMapelStr = implode(', ', array_slice($mapelListNames, 0, 8));
                            throw new \Exception("Mata Pelajaran '{$mapelInput}' tidak ditemukan pada baris " . ($index + 2) . ". Contoh yang tersedia: {$availMapelStr}");
                        }
                    }

                    // Smart Lookup Kelas ID for Active TA (exact & normalized)
                    $kelasId = null;
                    if (in_array('wali_kelas', $parsedRoles)) {
                        if (!empty($kelasInput)) {
                            $cleanKelasInput = preg_replace('/[^a-z0-9]/', '', strtolower($kelasInput));
                            if (isset($kelasMap[$cleanKelasInput])) {
                                $kelasId = $kelasMap[$cleanKelasInput];
                            } else {
                                $availKelasStr = implode(', ', array_slice($kelasListNames, 0, 8));
                                throw new \Exception("Kelas Wali '{$kelasInput}' tidak ditemukan untuk TA Aktif {$activeTA} pada baris " . ($index + 2) . ". Kelas tersedia: {$availKelasStr}");
                            }
                        } else {
                            throw new \Exception("Wali Kelas wajib mengisi kolom Nama Kelas Wali pada baris " . ($index + 2));
                        }
                    }

                    // Validate Jurusan for Ketua Jurusan
                    $jurusan = null;
                    if (in_array('ketua_jurusan', $parsedRoles)) {
                        if (!empty($jurusanInput)) {
                            $jurusan = strtoupper($jurusanInput);
                        } else {
                            throw new \Exception("Ketua Jurusan wajib mengisi kolom Jurusan pada baris " . ($index + 2));
                        }
                    }

                    // Pre-check duplicate NIP & Username using O(1) in-memory lookup
                    if (isset($existingNipMap[$nip])) {
                        throw new \Exception("NIP '{$nip}' ({$namaLengkap}) sudah terdaftar di sistem (milik {$existingNipMap[$nip]})");
                    }

                    if (isset($existingUsernames[$username])) {
                        throw new \Exception("Username '{$username}' ({$namaLengkap}) sudah terdaftar di sistem");
                    }

                    // Create guru using service (disable synchronous welcome email for fast bulk import)
                    $guruData = [
                        'nip'               => $nip,
                        'nama_lengkap'      => $namaLengkap,
                        'jenis_kelamin'     => $jenisKelamin,
                        'username'          => $username,
                        'password'          => $password,
                        'email'             => !empty($email) ? $email : null,
                        'send_email'        => false,
                        'roles'             => array_unique($parsedRoles),
                        'mata_pelajaran_id' => $mapelId,
                        'kelas_id'          => $kelasId,
                        'jurusan'           => $jurusan,
                    ];

                    $result = $this->guruService->createGuru($guruData);

                    if (!$result['success']) {
                        throw new \Exception(implode(', ', $result['errors']));
                    }

                    // Cache freshly created items to prevent intra-file duplicates
                    $existingNipMap[$nip] = $namaLengkap;
                    $existingUsernames[$username] = true;

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
     * Download template Excel import guru dengan Interaktif Dropdown & Reference Sheet
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Sheet 1: Template Utama
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Guru');

        // Sheet 2: Referensi Data
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Referensi Data');

        // Populate Sheet 2 (Referensi Data) dari Database Real-time
        $mapelModel  = new \App\Models\MataPelajaranModel();
        $activeTA    = get_active_tahun_ajaran();
        $kelasModel  = new \App\Models\KelasModel();

        $mapels  = $mapelModel->orderBy('nama_mapel', 'ASC')->findAll();
        $kelases = $kelasModel->where('tahun_ajaran', $activeTA)->orderBy('nama_kelas', 'ASC')->findAll();
        
        $refSheet->setCellValue('A1', 'Mata Pelajaran');
        $refSheet->setCellValue('B1', "Kelas TA {$activeTA}");
        $refSheet->setCellValue('C1', 'Jurusan');

        $refSheet->getStyle('A1:C1')->getFont()->setBold(true);

        $rowMapel = 2;
        foreach ($mapels as $m) {
            $refSheet->setCellValue('A' . $rowMapel, $m['nama_mapel']);
            $rowMapel++;
        }
        $mapelLastRow = max(2, $rowMapel - 1);

        $rowKelas = 2;
        $jurusanSet = [];
        foreach ($kelases as $k) {
            $refSheet->setCellValue('B' . $rowKelas, $k['nama_kelas']);
            if (!empty($k['jurusan'])) {
                $jurusanSet[strtoupper(trim($k['jurusan']))] = true;
            }
            $rowKelas++;
        }
        $kelasLastRow = max(2, $rowKelas - 1);

        $jurusanList = !empty($jurusanSet) ? array_keys($jurusanSet) : ['DKV', 'MPLB', 'AT', 'TKJ', 'RPL'];
        $rowJur = 2;
        foreach ($jurusanList as $j) {
            $refSheet->setCellValue('C' . $rowJur, $j);
            $rowJur++;
        }
        $jurusanLastRow = max(2, $rowJur - 1);

        // Header Sheet 1
        $headers = [
            'A1' => 'NIP',
            'B1' => 'Nama Lengkap',
            'C1' => 'Jenis Kelamin (Pilih Dropdown)',
            'D1' => 'Username (Opsional)',
            'E1' => 'Password (Opsional)',
            'F1' => 'Email (Opsional)',
            'G1' => 'Role (Pilih Dropdown / Dipisah Koma)',
            'H1' => 'Mata Pelajaran (Pilih Dropdown)',
            'I1' => 'Nama Kelas Wali (Pilih Dropdown)',
            'J1' => 'Jurusan Ketua (Pilih Dropdown)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Styling header Sheet 1
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4F46E5'] // Indigo
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Contoh Data
        $firstMapel = $mapels[0]['nama_mapel'] ?? 'Matematika';
        $firstKelas = $kelases[0]['nama_kelas'] ?? 'X RPL 1';
        $firstJur   = $jurusanList[0] ?? 'DKV';

        $sheet->fromArray([
            [
                '198501012010011001',
                'Budi Santoso, S.Pd',
                'Laki-laki',
                'budi.santoso',
                'password123',
                'budi@smk.sch.id',
                'Guru Mapel, Wali Kelas',
                $firstMapel,
                $firstKelas,
                ''
            ],
            [
                '198203152008012002',
                'Siti Aminah, M.Pd',
                'Perempuan',
                'siti.aminah',
                'password123',
                'siti@smk.sch.id',
                'Guru Mapel, Ketua Jurusan',
                $firstMapel,
                '',
                $firstJur
            ],
            [
                '197505101999031003',
                'Drs. H. Ahmad Wijaya, M.M.',
                'Laki-laki',
                'ahmad.kepsek',
                'password123',
                'ahmad@smk.sch.id',
                'Kepala Sekolah',
                '',
                '',
                ''
            ],
            [
                '199008202015021004',
                'Rahmat Hidayat, A.Md',
                'Laki-laki',
                '',
                '',
                'rahmat@smk.sch.id',
                'Tendik',
                '',
                '',
                ''
            ]
        ], null, 'A2');

        // Pasang Excel Data Validation Dropdowns pada Baris 2 s.d. 100
        for ($r = 2; $r <= 100; $r++) {
            // Dropdown C: Jenis Kelamin
            $vC = $sheet->getCell("C{$r}")->getDataValidation();
            $vC->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $vC->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $vC->setAllowBlank(true);
            $vC->setShowDropDown(true);
            $vC->setFormula1('"Laki-laki,Perempuan"');

            // Dropdown G: Role
            $vG = $sheet->getCell("G{$r}")->getDataValidation();
            $vG->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $vG->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $vG->setAllowBlank(true);
            $vG->setShowDropDown(true);
            $vG->setFormula1('"Guru Mapel,Wali Kelas,Guru Mapel; Wali Kelas,Ketua Jurusan,Kepala Sekolah,Tendik,Wakakur"');

            // Dropdown H: Mata Pelajaran (mengacu ke Sheet Referensi Data)
            $vH = $sheet->getCell("H{$r}")->getDataValidation();
            $vH->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $vH->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $vH->setAllowBlank(true);
            $vH->setShowDropDown(true);
            $vH->setFormula1("'Referensi Data'!\$A\$2:\$A\$" . $mapelLastRow);

            // Dropdown I: Kelas Wali (mengacu ke Sheet Referensi Data)
            $vI = $sheet->getCell("I{$r}")->getDataValidation();
            $vI->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $vI->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $vI->setAllowBlank(true);
            $vI->setShowDropDown(true);
            $vI->setFormula1("'Referensi Data'!\$B\$2:\$B\$" . $kelasLastRow);

            // Dropdown J: Jurusan (mengacu ke Sheet Referensi Data)
            $vJ = $sheet->getCell("J{$r}")->getDataValidation();
            $vJ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $vJ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $vJ->setAllowBlank(true);
            $vJ->setShowDropDown(true);
            $vJ->setFormula1("'Referensi Data'!\$C\$2:\$C\$" . $jurusanLastRow);
        }

        // Auto width Sheet 1
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Auto width Sheet 2
        foreach (range('A', 'C') as $col) {
            $refSheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set active sheet ke Sheet 1
        $spreadsheet->setActiveSheetIndex(0);
        $sheet->freezePane('A2');

        // Output file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template-import-guru-smk.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }
}
