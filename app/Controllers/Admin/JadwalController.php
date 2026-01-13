<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JadwalMengajarModel;
use App\Models\GuruModel;
use App\Models\MataPelajaranModel;
use App\Models\KelasModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class JadwalController extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;
    protected $mapelModel;
    protected $kelasModel;
    protected $session;

    public function __construct()
    {
        $this->jadwalModel = new JadwalMengajarModel();
        $this->guruModel = new GuruModel();
        $this->mapelModel = new MataPelajaranModel();
        $this->kelasModel = new KelasModel();
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

        $perPage = $this->request->getGet('per_page') ?? 10;
        $search = $this->request->getGet('search');
        $semester = $this->request->getGet('semester');
        $tahunAjaran = $this->request->getGet('tahun_ajaran');

        $data = [
            'title' => 'Manajemen Jadwal Mengajar',
            'pageTitle' => 'Jadwal Mengajar',
            'pageDescription' => 'Kelola jadwal mengajar guru',
            'jadwal' => $this->jadwalModel->getAllJadwal($perPage, $search, $semester, $tahunAjaran),
            'pager' => $this->jadwalModel->pager,
            'search' => $search,
            'perPage' => $perPage,
            'semester' => $semester,
            'tahunAjaran' => $tahunAjaran,
            'hariList' => $this->jadwalModel->getHariList(),
            'semesterList' => $this->jadwalModel->getSemesterList(),
            'tahunAjaranList' => $this->jadwalModel->getTahunAjaranList()
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

        $data = [
            'title' => 'Tambah Jadwal Mengajar',
            'pageTitle' => 'Tambah Jadwal Mengajar',
            'pageDescription' => 'Isi form untuk menambahkan jadwal mengajar baru',
            'validation' => \Config\Services::validation(),
            'guruOptions' => $this->guruModel->getGuruDropdown(),
            'mapelOptions' => $this->mapelModel->getAllMapelForDropdown(),
            'kelasOptions' => $this->kelasModel->getListKelas(),
            'hariList' => $this->jadwalModel->getHariList(),
            'semesterList' => $this->jadwalModel->getSemesterList(),
            'tahunAjaranList' => $this->jadwalModel->getTahunAjaranList(),
            'currentYear' => date('Y')
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
        if (!$this->validate($this->jadwalModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get form data
        $guruId = $this->request->getPost('guru_id');
        $kelasId = $this->request->getPost('kelas_id');
        $hari = $this->request->getPost('hari');
        $jamMulai = $this->request->getPost('jam_mulai');
        $jamSelesai = $this->request->getPost('jam_selesai');

        // Check for schedule conflict for teacher
        if ($this->jadwalModel->checkConflict($guruId, $hari, $jamMulai, $jamSelesai)) {
            $this->session->setFlashdata('error', 'Guru bentrok nih! Ada jadwal lain di jam yang sama ??');
            return redirect()->back()->withInput();
        }

        // Check for schedule conflict for class
        if ($this->jadwalModel->checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai)) {
            $this->session->setFlashdata('error', 'Kelas udah ada jadwal di jam ini ??');
            return redirect()->back()->withInput();
        }

        // Prepare data
        $data = [
            'guru_id' => $guruId,
            'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
            'kelas_id' => $kelasId,
            'hari' => $hari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'semester' => $this->request->getPost('semester'),
            'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
        ];

        // Save to database
        if ($this->jadwalModel->save($data)) {
            $this->session->setFlashdata('success', "Jadwal baru siap! Let's teach");
            return redirect()->to('/admin/jadwal');
        } else {
            $this->session->setFlashdata('error', 'Oops, jadwal gagal ditambahkan ??');
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

        $jadwal = $this->jadwalModel->getJadwalWithDetail($id);

        if (!$jadwal) {
            throw new PageNotFoundException('Jadwal mengajar tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Jadwal Mengajar',
            'pageTitle' => 'Edit Jadwal Mengajar',
            'pageDescription' => 'Edit data jadwal mengajar',
            'jadwal' => $jadwal,
            'validation' => \Config\Services::validation(),
            'guruOptions' => $this->guruModel->getGuruDropdown(),
            'mapelOptions' => $this->mapelModel->getAllMapelForDropdown(),
            'kelasOptions' => $this->kelasModel->getListKelas(),
            'hariList' => $this->jadwalModel->getHariList(),
            'semesterList' => $this->jadwalModel->getSemesterList(),
            'tahunAjaranList' => $this->jadwalModel->getTahunAjaranList()
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

        // Check if exists
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) {
            throw new PageNotFoundException('Jadwal mengajar tidak ditemukan');
        }

        // Validate input
        if (!$this->validate($this->jadwalModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get form data
        $guruId = $this->request->getPost('guru_id');
        $kelasId = $this->request->getPost('kelas_id');
        $hari = $this->request->getPost('hari');
        $jamMulai = $this->request->getPost('jam_mulai');
        $jamSelesai = $this->request->getPost('jam_selesai');

        // Check for schedule conflict for teacher (excluding current)
        if ($this->jadwalModel->checkConflict($guruId, $hari, $jamMulai, $jamSelesai, $id)) {
            $this->session->setFlashdata('error', 'Guru bentrok nih! Ada jadwal lain di jam yang sama ??');
            return redirect()->back()->withInput();
        }

        // Check for schedule conflict for class (excluding current)
        if ($this->jadwalModel->checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai, $id)) {
            $this->session->setFlashdata('error', 'Kelas udah ada jadwal di jam ini ??');
            return redirect()->back()->withInput();
        }

        // Prepare data
        $data = [
            'id' => $id,
            'guru_id' => $guruId,
            'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
            'kelas_id' => $kelasId,
            'hari' => $hari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'semester' => $this->request->getPost('semester'),
            'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
        ];

        // Update database
        if ($this->jadwalModel->save($data)) {
            $this->session->setFlashdata('success', 'Jadwal updated! All set ??');
            return redirect()->to('/admin/jadwal');
        } else {
            $this->session->setFlashdata('error', 'Waduh, update jadwal gagal ??');
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

        // Check if exists
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) {
            throw new PageNotFoundException('Jadwal mengajar tidak ditemukan');
        }

        // Check if jadwal has related absensi data
        $db = \Config\Database::connect();
        $checkAbsensi = $db->table('absensi')
            ->where('jadwal_mengajar_id', $id)
            ->countAllResults();

        if ($checkAbsensi > 0) {
            $this->session->setFlashdata('error', 'Jadwal udah ada absensinya, nggak bisa dihapus ya ??');
            return redirect()->back();
        }

        // Delete from database
        if ($this->jadwalModel->delete($id)) {
            $this->session->setFlashdata('success', 'Jadwal sudah dihapus ?');
        } else {
            $this->session->setFlashdata('error', 'Hmm, gagal hapus jadwal ??');
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
        $jadwal = $this->jadwalModel->getByGuru($guruId);

        return $this->response->setJSON([
            'success' => true,
            'data' => $jadwal
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
        $jadwal = $this->jadwalModel->getByKelas($kelasId);

        return $this->response->setJSON([
            'success' => true,
            'data' => $jadwal
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

        $guruId = $this->request->getPost('guru_id');
        $kelasId = $this->request->getPost('kelas_id');
        $hari = $this->request->getPost('hari');
        $jamMulai = $this->request->getPost('jam_mulai');
        $jamSelesai = $this->request->getPost('jam_selesai');
        $excludeId = $this->request->getPost('exclude_id');

        $conflictGuru = $this->jadwalModel->checkConflict($guruId, $hari, $jamMulai, $jamSelesai, $excludeId);
        $conflictKelas = $this->jadwalModel->checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai, $excludeId);

        return $this->response->setJSON([
            'success' => true,
            'conflict_guru' => $conflictGuru,
            'conflict_kelas' => $conflictKelas
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

        $semester = $this->request->getGet('semester');
        $tahunAjaran = $this->request->getGet('tahun_ajaran');

        $jadwal = $this->jadwalModel->select('jadwal_mengajar.*, 
                                            guru.nama_lengkap as nama_guru,
                                            guru.nip,
                                            mata_pelajaran.nama_mapel,
                                            mata_pelajaran.kode_mapel,
                                            kelas.nama_kelas,
                                            kelas.tingkat,
                                            kelas.jurusan')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->orderBy('jadwal_mengajar.hari', 'ASC')
            ->orderBy('jadwal_mengajar.jam_mulai', 'ASC');

        if ($semester) {
            $jadwal->where('jadwal_mengajar.semester', $semester);
        }

        if ($tahunAjaran) {
            $jadwal->where('jadwal_mengajar.tahun_ajaran', $tahunAjaran);
        }

        $jadwal = $jadwal->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Hari');
        $sheet->setCellValue('C1', 'Jam');
        $sheet->setCellValue('D1', 'Kelas');
        $sheet->setCellValue('E1', 'Guru');
        $sheet->setCellValue('F1', 'Mata Pelajaran');
        $sheet->setCellValue('G1', 'Semester');
        $sheet->setCellValue('H1', 'Tahun Ajaran');

        // Set data
        $no = 1;
        $row = 2;
        foreach ($jadwal as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['hari']);
            $sheet->setCellValue('C' . $row, $item['jam_mulai'] . ' - ' . $item['jam_selesai']);
            $sheet->setCellValue('D' . $row, $item['nama_kelas']);
            $sheet->setCellValue('E' . $row, $item['nama_guru'] . ' (' . $item['nip'] . ')');
            $sheet->setCellValue('F' . $row, $item['nama_mapel'] . ' (' . $item['kode_mapel'] . ')');
            $sheet->setCellValue('G' . $row, $item['semester']);
            $sheet->setCellValue('H' . $row, $item['tahun_ajaran']);
            $row++;
        }

        // Set column width
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(15);

        // Style header
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE2E8F0']
            ]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Set filename
        $filename = 'jadwal-mengajar-' . date('Y-m-d-H-i-s') . '.xlsx';

        // Redirect output to a client's web browser (Excel2007)
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

        helper('security');
        $file = $this->request->getFile('file_excel');

        // Validate file upload
        $allowedTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel'
        ];
        
        $validation = validate_file_upload($file, $allowedTypes, 5242880); // 5MB limit
        
        if (!$validation['valid']) {
            $this->session->setFlashdata('error', $validation['error']);
            return redirect()->to('/admin/jadwal/import');
        }

        try {
            // Load spreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Skip header row
            array_shift($rows);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            $skipDuplicate = $this->request->getPost('skip_duplicate');

            foreach ($rows as $index => $row) {
                if (empty($row[0])) continue; // Skip empty rows

                // Start transaction
                $db = \Config\Database::connect();
                $db->transStart();

                try {
                    $hari = trim($row[0]);
                    $jamMulai = trim($row[1]);
                    $jamSelesai = trim($row[2]);
                    $guruId = trim($row[3]);
                    $mataPelajaranId = trim($row[4]);
                    $kelasId = trim($row[5]);
                    $semester = trim($row[6]);
                    $tahunAjaran = trim($row[7]);

                    // Validate required fields
                    if (empty($hari) || empty($jamMulai) || empty($jamSelesai) || 
                        empty($guruId) || empty($mataPelajaranId) || empty($kelasId) ||
                        empty($semester) || empty($tahunAjaran)) {
                        throw new \Exception("Data tidak lengkap pada baris " . ($index + 2));
                    }

                    // Validate hari
                    $hariValid = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                    if (!in_array($hari, $hariValid)) {
                        throw new \Exception("Hari tidak valid: {$hari}");
                    }

                    // Validate guru exists
                    $guru = $this->guruModel->find($guruId);
                    if (!$guru) {
                        throw new \Exception("Guru ID {$guruId} tidak ditemukan");
                    }

                    // Validate mata pelajaran exists
                    $mapel = $this->mapelModel->find($mataPelajaranId);
                    if (!$mapel) {
                        throw new \Exception("Mata Pelajaran ID {$mataPelajaranId} tidak ditemukan");
                    }

                    // Validate kelas exists
                    $kelas = $this->kelasModel->find($kelasId);
                    if (!$kelas) {
                        throw new \Exception("Kelas ID {$kelasId} tidak ditemukan");
                    }

                    // Check for schedule conflict for teacher
                    if ($this->jadwalModel->checkConflict($guruId, $hari, $jamMulai, $jamSelesai)) {
                        if ($skipDuplicate) {
                            $errorCount++;
                            $errors[] = "Baris " . ($index + 2) . ": Guru {$guru['nama_lengkap']} sudah memiliki jadwal di waktu yang sama (dilewati)";
                            continue;
                        } else {
                            throw new \Exception("Guru {$guru['nama_lengkap']} sudah memiliki jadwal di waktu yang sama");
                        }
                    }

                    // Check for schedule conflict for class
                    if ($this->jadwalModel->checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai)) {
                        if ($skipDuplicate) {
                            $errorCount++;
                            $errors[] = "Baris " . ($index + 2) . ": Kelas {$kelas['nama_kelas']} sudah memiliki jadwal di waktu yang sama (dilewati)";
                            continue;
                        } else {
                            throw new \Exception("Kelas {$kelas['nama_kelas']} sudah memiliki jadwal di waktu yang sama");
                        }
                    }

                    // Insert jadwal
                    $jadwalData = [
                        'guru_id' => $guruId,
                        'mata_pelajaran_id' => $mataPelajaranId,
                        'kelas_id' => $kelasId,
                        'hari' => $hari,
                        'jam_mulai' => $jamMulai,
                        'jam_selesai' => $jamSelesai,
                        'semester' => $semester,
                        'tahun_ajaran' => $tahunAjaran
                    ];

                    $this->jadwalModel->insert($jadwalData);

                    $db->transComplete();

                    if ($db->transStatus() === false) {
                        throw new \Exception('Gagal menyimpan data');
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
                $this->session->setFlashdata('import_errors', $errors);
            }

            if ($errorCount > 0 && $successCount == 0) {
                $this->session->setFlashdata('error', $message);
            } else {
                $this->session->setFlashdata('success', $message);
            }

            return redirect()->to('/admin/jadwal');
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error saat memproses file: ' . $e->getMessage());
            return redirect()->to('/admin/jadwal/import');
        }
    }

    /**
     * Download template Excel for import
     */
    public function downloadTemplate()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Jadwal');

        // Set headers
        $headers = [
            'A1' => 'HARI',
            'B1' => 'JAM MULAI (HH:MM:SS)',
            'C1' => 'JAM SELESAI (HH:MM:SS)',
            'D1' => 'GURU_ID',
            'E1' => 'MATA_PELAJARAN_ID',
            'F1' => 'KELAS_ID',
            'G1' => 'SEMESTER',
            'H1' => 'TAHUN AJARAN',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFD1E7DD']
            ]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Add sample data
        $sheet->fromArray([
            ['Senin', '07:00:00', '08:30:00', 1, 1, 1, 'Ganjil', '2023/2024'],
            ['Senin', '08:30:00', '10:00:00', 2, 2, 1, 'Ganjil', '2023/2024'],
            ['Selasa', '07:00:00', '08:30:00', 1, 1, 2, 'Ganjil', '2023/2024'],
        ], null, 'A2');

        // Auto width
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze header
        $sheet->freezePane('A2');

        // Add instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Petunjuk');
        $instructionSheet->setCellValue('A1', 'PETUNJUK IMPORT JADWAL MENGAJAR');
        $instructionSheet->setCellValue('A3', '1. HARI: Senin, Selasa, Rabu, Kamis, Jumat');
        $instructionSheet->setCellValue('A4', '2. JAM MULAI dan JAM SELESAI: Format HH:MM:SS (contoh: 07:00:00)');
        $instructionSheet->setCellValue('A5', '3. GURU_ID: ID guru dari database (lihat di menu Guru)');
        $instructionSheet->setCellValue('A6', '4. MATA_PELAJARAN_ID: ID mata pelajaran dari database (lihat di menu Mata Pelajaran)');
        $instructionSheet->setCellValue('A7', '5. KELAS_ID: ID kelas dari database (lihat di menu Kelas)');
        $instructionSheet->setCellValue('A8', '6. SEMESTER: Ganjil atau Genap');
        $instructionSheet->setCellValue('A9', '7. TAHUN AJARAN: Format YYYY/YYYY (contoh: 2023/2024)');
        $instructionSheet->setCellValue('A11', 'PENTING:');
        $instructionSheet->setCellValue('A12', '- Jangan mengubah nama kolom header');
        $instructionSheet->setCellValue('A13', '- Pastikan format jam benar (HH:MM:SS)');
        $instructionSheet->setCellValue('A14', '- Pastikan ID guru, mapel, dan kelas sudah ada di database');
        $instructionSheet->setCellValue('A15', '- Sistem akan mengecek konflik jadwal');

        $instructionSheet->getColumnDimension('A')->setWidth(80);

        // Output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template-import-jadwal.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }
}
