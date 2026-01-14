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
                    $guruInput = trim($row[3]);
                    $mataPelajaranInput = trim($row[4]);
                    $kelasInput = trim($row[5]);
                    $semester = trim($row[6]);
                    $tahunAjaran = trim($row[7]);

                    // Validate required fields
                    if (empty($hari) || empty($jamMulai) || empty($jamSelesai) || 
                        empty($guruInput) || empty($mataPelajaranInput) || empty($kelasInput) ||
                        empty($semester) || empty($tahunAjaran)) {
                        throw new \Exception("Data tidak lengkap pada baris " . ($index + 2));
                    }

                    // Validate hari
                    $hariValid = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                    if (!in_array($hari, $hariValid)) {
                        throw new \Exception("Hari tidak valid: {$hari}");
                    }

                    // ===== PROSES GURU: Support ID atau Nama =====
                    $guruId = null;
                    if (is_numeric($guruInput)) {
                        // Jika input berupa angka, anggap sebagai ID
                        $guruId = (int)$guruInput;
                    } else {
                        // Jika input berupa string, coba beberapa cara
                        // 1. Coba extract NIP dari format "Nama (NIP)" untuk backward compatibility
                        if (preg_match('/\(([^)]+)\)/', $guruInput, $matches)) {
                            $nip = trim($matches[1]);
                            $guru = $this->guruModel->where('nip', $nip)->first();
                            if ($guru) {
                                $guruId = $guru['id'];
                            }
                        }
                        
                        // 2. Jika belum ketemu, cari berdasarkan nama lengkap exact match
                        if (!$guruId) {
                            $guru = $this->guruModel->where('nama_lengkap', trim($guruInput))->first();
                            if ($guru) {
                                $guruId = $guru['id'];
                            }
                        }
                        
                        // 3. Jika masih belum ketemu, cari dengan LIKE (partial match)
                        if (!$guruId) {
                            $guru = $this->guruModel->like('nama_lengkap', trim($guruInput))->first();
                            if ($guru) {
                                $guruId = $guru['id'];
                            }
                        }
                    }

                    // Validate guru exists
                    $guru = $this->guruModel->find($guruId);
                    if (!$guru) {
                        throw new \Exception("Guru '{$guruInput}' tidak ditemukan");
                    }

                    // ===== PROSES MATA PELAJARAN: Support ID atau Nama =====
                    $mataPelajaranId = null;
                    if (is_numeric($mataPelajaranInput)) {
                        // Jika input berupa angka, anggap sebagai ID
                        $mataPelajaranId = (int)$mataPelajaranInput;
                    } else {
                        // Jika input berupa string, coba beberapa cara
                        // 1. Coba extract kode dari format "Nama (Kode)" untuk backward compatibility
                        if (preg_match('/\(([^)]+)\)/', $mataPelajaranInput, $matches)) {
                            $kode = trim($matches[1]);
                            $mapel = $this->mapelModel->where('kode_mapel', $kode)->first();
                            if ($mapel) {
                                $mataPelajaranId = $mapel['id'];
                            }
                        }
                        
                        // 2. Jika belum ketemu, cari berdasarkan nama mapel exact match
                        if (!$mataPelajaranId) {
                            $mapel = $this->mapelModel->where('nama_mapel', trim($mataPelajaranInput))->first();
                            if ($mapel) {
                                $mataPelajaranId = $mapel['id'];
                            }
                        }
                        
                        // 3. Jika masih belum ketemu, cari dengan LIKE (partial match)
                        if (!$mataPelajaranId) {
                            $mapel = $this->mapelModel->like('nama_mapel', trim($mataPelajaranInput))->first();
                            if ($mapel) {
                                $mataPelajaranId = $mapel['id'];
                            }
                        }
                    }

                    // Validate mata pelajaran exists
                    $mapel = $this->mapelModel->find($mataPelajaranId);
                    if (!$mapel) {
                        throw new \Exception("Mata Pelajaran '{$mataPelajaranInput}' tidak ditemukan");
                    }

                    // ===== PROSES KELAS: Support ID atau Nama =====
                    $kelasId = null;
                    if (is_numeric($kelasInput)) {
                        // Jika input berupa angka, anggap sebagai ID
                        $kelasId = (int)$kelasInput;
                    } else {
                        // Cari berdasarkan nama kelas
                        $kelas = $this->kelasModel->where('nama_kelas', $kelasInput)->first();
                        if ($kelas) {
                            $kelasId = $kelas['id'];
                        }
                    }

                    // Validate kelas exists
                    $kelas = $this->kelasModel->find($kelasId);
                    if (!$kelas) {
                        throw new \Exception("Kelas '{$kelasInput}' tidak ditemukan");
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
     * Download template Excel for import (User-Friendly dengan Dropdown)
     */
    public function downloadTemplate()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        // ===== SHEET 1: Template Import Jadwal =====
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Jadwal');

        // Set headers dengan kolom nama yang user-friendly
        $headers = [
            'A1' => 'HARI',
            'B1' => 'JAM MULAI',
            'C1' => 'JAM SELESAI',
            'D1' => 'NAMA GURU',
            'E1' => 'MATA PELAJARAN',
            'F1' => 'KELAS',
            'G1' => 'SEMESTER',
            'H1' => 'TAHUN AJARAN',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4472C4']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Get data untuk dropdown
        $guruList = $this->guruModel->select('guru.id, guru.nama_lengkap, guru.nip')
            ->join('users', 'users.id = guru.user_id')
            ->where('users.is_active', 1)
            ->orderBy('guru.nama_lengkap', 'ASC')
            ->findAll();
        $mapelList = $this->mapelModel->select('id, nama_mapel, kode_mapel')
            ->orderBy('nama_mapel', 'ASC')
            ->findAll();
        $kelasList = $this->kelasModel->select('id, nama_kelas')
            ->orderBy('nama_kelas', 'ASC')
            ->findAll();

        // ===== SHEET 2: Data Guru =====
        $guruSheet = $spreadsheet->createSheet();
        $guruSheet->setTitle('Data Guru');
        $guruSheet->setCellValue('A1', 'ID');
        $guruSheet->setCellValue('B1', 'NIP');
        $guruSheet->setCellValue('C1', 'NAMA LENGKAP');
        $guruSheet->getStyle('A1:C1')->applyFromArray($headerStyle);
        
        $row = 2;
        foreach ($guruList as $guru) {
            $guruSheet->setCellValue('A' . $row, $guru['id']);
            $guruSheet->setCellValue('B' . $row, $guru['nip']);
            $guruSheet->setCellValue('C' . $row, $guru['nama_lengkap']);
            $row++;
        }
        $guruSheet->getColumnDimension('A')->setWidth(8);
        $guruSheet->getColumnDimension('B')->setWidth(20);
        $guruSheet->getColumnDimension('C')->setWidth(35);

        // ===== SHEET 3: Data Mata Pelajaran =====
        $mapelSheet = $spreadsheet->createSheet();
        $mapelSheet->setTitle('Data Mata Pelajaran');
        $mapelSheet->setCellValue('A1', 'ID');
        $mapelSheet->setCellValue('B1', 'KODE');
        $mapelSheet->setCellValue('C1', 'NAMA MATA PELAJARAN');
        $mapelSheet->getStyle('A1:C1')->applyFromArray($headerStyle);
        
        $row = 2;
        foreach ($mapelList as $mapel) {
            $mapelSheet->setCellValue('A' . $row, $mapel['id']);
            $mapelSheet->setCellValue('B' . $row, $mapel['kode_mapel']);
            $mapelSheet->setCellValue('C' . $row, $mapel['nama_mapel']);
            $row++;
        }
        $mapelSheet->getColumnDimension('A')->setWidth(8);
        $mapelSheet->getColumnDimension('B')->setWidth(15);
        $mapelSheet->getColumnDimension('C')->setWidth(35);

        // ===== SHEET 4: Data Kelas =====
        $kelasSheet = $spreadsheet->createSheet();
        $kelasSheet->setTitle('Data Kelas');
        $kelasSheet->setCellValue('A1', 'ID');
        $kelasSheet->setCellValue('B1', 'NAMA KELAS');
        $kelasSheet->getStyle('A1:B1')->applyFromArray($headerStyle);
        
        $row = 2;
        foreach ($kelasList as $kelas) {
            $kelasSheet->setCellValue('A' . $row, $kelas['id']);
            $kelasSheet->setCellValue('B' . $row, $kelas['nama_kelas']);
            $row++;
        }
        $kelasSheet->getColumnDimension('A')->setWidth(8);
        $kelasSheet->getColumnDimension('B')->setWidth(25);

        // ===== Kembali ke Sheet Template =====
        $spreadsheet->setActiveSheetIndex(0);

        // Create dropdown lists untuk kolom yang membutuhkan
        $totalGuruRows = count($guruList);
        $totalMapelRows = count($mapelList);
        $totalKelasRows = count($kelasList);

        // Prepare dropdown data
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $semesterList = ['Ganjil', 'Genap'];

        // Add dropdowns untuk 50 baris dengan referensi ke sheet lain
        for ($row = 2; $row <= 51; $row++) {
            // Dropdown HARI (kolom A)
            $validation = $sheet->getCell('A' . $row)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . implode(',', $hariList) . '"');
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Input Error');
            $validation->setError('Pilih hari dari dropdown');

            // Dropdown NAMA GURU (kolom D) - Referensi ke sheet "Data Guru"
            if ($totalGuruRows > 0) {
                $validation = $sheet->getCell('D' . $row)->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                // Referensi ke kolom C (NAMA LENGKAP) di sheet "Data Guru"
                $validation->setFormula1("'Data Guru'!\$C\$2:\$C\$" . ($totalGuruRows + 1));
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Input Error');
                $validation->setError('Pilih guru dari dropdown');
            }

            // Dropdown MATA PELAJARAN (kolom E) - Referensi ke sheet "Data Mata Pelajaran"
            if ($totalMapelRows > 0) {
                $validation = $sheet->getCell('E' . $row)->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                // Referensi ke kolom C (NAMA MATA PELAJARAN) di sheet "Data Mata Pelajaran"
                $validation->setFormula1("'Data Mata Pelajaran'!\$C\$2:\$C\$" . ($totalMapelRows + 1));
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Input Error');
                $validation->setError('Pilih mata pelajaran dari dropdown');
            }

            // Dropdown KELAS (kolom F) - Referensi ke sheet "Data Kelas"
            if ($totalKelasRows > 0) {
                $validation = $sheet->getCell('F' . $row)->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                // Referensi ke kolom B (NAMA KELAS) di sheet "Data Kelas"
                $validation->setFormula1("'Data Kelas'!\$B\$2:\$B\$" . ($totalKelasRows + 1));
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Input Error');
                $validation->setError('Pilih kelas dari dropdown');
            }

            // Dropdown SEMESTER (kolom G)
            $validation = $sheet->getCell('G' . $row)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . implode(',', $semesterList) . '"');
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Input Error');
            $validation->setError('Pilih semester dari dropdown');
        }

        // Add sample data dengan nama (bukan ID) - hanya nama tanpa NIP/Kode
        if (!empty($guruList) && !empty($mapelList) && !empty($kelasList)) {
            $sheet->setCellValue('A2', 'Senin');
            $sheet->setCellValue('B2', '07:00:00');
            $sheet->setCellValue('C2', '08:30:00');
            $sheet->setCellValue('D2', $guruList[0]['nama_lengkap']); // Hanya nama
            $sheet->setCellValue('E2', $mapelList[0]['nama_mapel']); // Hanya nama mapel
            $sheet->setCellValue('F2', $kelasList[0]['nama_kelas']);
            $sheet->setCellValue('G2', 'Ganjil');
            $sheet->setCellValue('H2', date('Y') . '/' . (date('Y') + 1));
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(15);

        // Freeze header
        $sheet->freezePane('A2');

        // ===== SHEET 5: Petunjuk =====
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Petunjuk');
        
        $instructions = [
            ['PETUNJUK IMPORT JADWAL MENGAJAR'],
            [''],
            ['CARA MENGISI TEMPLATE:'],
            ['1. HARI: Pilih dari dropdown (Senin, Selasa, Rabu, Kamis, Jumat)'],
            ['2. JAM MULAI: Format HH:MM:SS (contoh: 07:00:00, 08:30:00)'],
            ['3. JAM SELESAI: Format HH:MM:SS, harus lebih besar dari Jam Mulai'],
            ['4. NAMA GURU: Pilih dari dropdown - HANYA NAMA (data dari sheet "Data Guru")'],
            ['5. MATA PELAJARAN: Pilih dari dropdown - HANYA NAMA (data dari sheet "Data Mata Pelajaran")'],
            ['6. KELAS: Pilih dari dropdown - NAMA KELAS (data dari sheet "Data Kelas")'],
            ['7. SEMESTER: Pilih dari dropdown (Ganjil atau Genap)'],
            ['8. TAHUN AJARAN: Format YYYY/YYYY (contoh: 2023/2024, 2024/2025)'],
            [''],
            ['TIPS PENTING:'],
            ['✓ CUKUP PILIH NAMA dari dropdown (tidak perlu NIP atau kode!)'],
            ['✓ Dropdown otomatis mengambil data dari sheet referensi'],
            ['✓ Jangan mengubah nama kolom header di sheet "Template Import Jadwal"'],
            ['✓ Jangan mengedit sheet "Data Guru", "Data Mata Pelajaran", dan "Data Kelas"'],
            ['✓ Format jam HARUS HH:MM:SS (2 digit jam : 2 digit menit : 2 digit detik)'],
            ['✓ Sistem akan otomatis mengecek konflik jadwal (guru dan kelas)'],
            ['✓ Centang "Lewati jadwal konflik" saat upload untuk skip data yang konflik'],
            [''],
            ['VALIDASI OTOMATIS:'],
            ['→ Sistem akan mengecek apakah guru sudah mengajar di jam yang sama'],
            ['→ Sistem akan mengecek apakah kelas sudah ada pelajaran di jam yang sama'],
            ['→ Data yang valid akan diimport, yang invalid akan dilaporkan'],
            [''],
            ['CONTOH DATA VALID (Format Baru):'],
            ['Senin | 07:00:00 | 08:30:00 | Ahmad Yani | Matematika | X RPL 1 | Ganjil | 2023/2024'],
            [''],
            ['SHEET REFERENSI:'],
            ['• Sheet "Data Guru": Lihat ID, NIP, dan Nama Lengkap - Pilih dari kolom NAMA LENGKAP'],
            ['• Sheet "Data Mata Pelajaran": Lihat ID, Kode, dan Nama - Pilih dari kolom NAMA MATA PELAJARAN'],
            ['• Sheet "Data Kelas": Lihat ID dan Nama - Pilih dari kolom NAMA KELAS'],
            [''],
            ['BACKWARD COMPATIBILITY:'],
            ['Sistem masih support format lama dengan ID angka atau format "Nama (NIP/Kode)"'],
            [''],
            ['Jika ada pertanyaan, hubungi administrator sistem.'],
        ];

        $instructionSheet->fromArray($instructions, null, 'A1');
        $instructionSheet->getColumnDimension('A')->setWidth(100);
        
        // Style untuk instruction
        $instructionSheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF4472C4']],
        ]);
        $instructionSheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
        ]);
        $instructionSheet->getStyle('A13')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
        ]);
        $instructionSheet->getStyle('A21')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
        ]);
        $instructionSheet->getStyle('A27')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
        ]);
        $instructionSheet->getStyle('A32')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
        ]);

        // Output
        $spreadsheet->setActiveSheetIndex(0);
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template-import-jadwal-' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }
}
