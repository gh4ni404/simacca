<?php

namespace App\Services;

use App\Models\JadwalMengajarModel;
use App\Models\GuruModel;
use App\Models\MataPelajaranModel;
use App\Models\KelasModel;

/**
 * JadwalService
 * 
 * Service layer for managing jadwal mengajar (teaching schedule) business logic.
 * Handles CRUD operations, conflict detection, Excel import/export with multi-format support.
 * 
 * @package App\Services
 */
class JadwalService extends BaseService
{
    protected $jadwalModel;
    protected $guruModel;
    protected $mapelModel;
    protected $kelasModel;

    public function __construct()
    {
        parent::__construct();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->guruModel = new GuruModel();
        $this->mapelModel = new MataPelajaranModel();
        $this->kelasModel = new KelasModel();
    }

    /**
     * Get all jadwal with pagination and filters
     * 
     * @param array $filters
     * @return array
     */
    public function getAllJadwal(array $filters = []): array
    {
        try {
            $perPage = $filters['per_page'] ?? 10;
            $search = $filters['search'] ?? null;
            $semester = $filters['semester'] ?? null;
            $tahunAjaran = $filters['tahun_ajaran'] ?? null;

            $jadwal = $this->jadwalModel->getAllJadwal($perPage, $search, $semester, $tahunAjaran);
            
            return $this->successResponse([
                'jadwal' => $jadwal,
                'pager' => $this->jadwalModel->pager,
                'hariList' => $this->jadwalModel->getHariList(),
                'semesterList' => $this->jadwalModel->getSemesterList(),
                'tahunAjaranList' => $this->jadwalModel->getTahunAjaranList()
            ]);
        } catch (\Exception $e) {
            $this->logError('getAllJadwal', $e);
            return $this->errorResponse('Gagal mengambil data jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Get jadwal by ID with detail
     * 
     * @param int $id
     * @return array
     */
    public function getJadwalById(int $id): array
    {
        try {
            $jadwal = $this->jadwalModel->getJadwalWithDetail($id);
            
            if (!$jadwal) {
                return $this->errorResponse('Jadwal tidak ditemukan', 404);
            }
            
            return $this->successResponse(['jadwal' => $jadwal]);
        } catch (\Exception $e) {
            $this->logError('getJadwalById', $e);
            return $this->errorResponse('Gagal mengambil data jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Create new jadwal with conflict checking
     * 
     * @param array $data
     * @return array
     */
    public function createJadwal(array $data): array
    {
        try {
            // Check for schedule conflict for teacher
            if ($this->jadwalModel->checkConflict($data['guru_id'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'])) {
                return $this->errorResponse('Guru bentrok nih! Ada jadwal lain di jam yang sama', 409);
            }

            // Check for schedule conflict for class
            if ($this->jadwalModel->checkKelasConflict($data['kelas_id'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'])) {
                return $this->errorResponse('Kelas udah ada jadwal di jam ini', 409);
            }

            // Prepare data
            $jadwalData = [
                'guru_id' => $data['guru_id'],
                'mata_pelajaran_id' => $data['mata_pelajaran_id'],
                'kelas_id' => $data['kelas_id'],
                'hari' => $data['hari'],
                'jam_mulai' => $data['jam_mulai'],
                'jam_selesai' => $data['jam_selesai'],
                'semester' => $data['semester'],
                'tahun_ajaran' => get_active_tahun_ajaran()
            ];

            // Save to database
            if ($this->jadwalModel->save($jadwalData)) {
                $this->logInfo('createJadwal', "Created jadwal for guru: {$data['guru_id']}, kelas: {$data['kelas_id']}");
                return $this->successResponse(['message' => 'Jadwal berhasil ditambahkan']);
            } else {
                return $this->errorResponse('Gagal menambahkan jadwal');
            }
        } catch (\Exception $e) {
            $this->logError('createJadwal', $e);
            return $this->errorResponse('Gagal menambahkan jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Update jadwal with conflict checking
     * 
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateJadwal(int $id, array $data): array
    {
        try {
            // Check if exists
            $jadwal = $this->jadwalModel->find($id);
            if (!$jadwal) {
                return $this->errorResponse('Jadwal tidak ditemukan', 404);
            }

            // Check for schedule conflict for teacher (excluding current)
            if ($this->jadwalModel->checkConflict($data['guru_id'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'], $id)) {
                return $this->errorResponse('Guru bentrok nih! Ada jadwal lain di jam yang sama', 409);
            }

            // Check for schedule conflict for class (excluding current)
            if ($this->jadwalModel->checkKelasConflict($data['kelas_id'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'], $id)) {
                return $this->errorResponse('Kelas udah ada jadwal di jam ini', 409);
            }

            // Prepare data
            $jadwalData = [
                'id' => $id,
                'guru_id' => $data['guru_id'],
                'mata_pelajaran_id' => $data['mata_pelajaran_id'],
                'kelas_id' => $data['kelas_id'],
                'hari' => $data['hari'],
                'jam_mulai' => $data['jam_mulai'],
                'jam_selesai' => $data['jam_selesai'],
                'semester' => $data['semester'],
            ];

            // Update database
            if ($this->jadwalModel->save($jadwalData)) {
                $this->logInfo('updateJadwal', "Updated jadwal: $id");
                return $this->successResponse(['message' => 'Jadwal berhasil diupdate']);
            } else {
                return $this->errorResponse('Gagal mengupdate jadwal');
            }
        } catch (\Exception $e) {
            $this->logError('updateJadwal', $e);
            return $this->errorResponse('Gagal mengupdate jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Delete jadwal with absensi check
     * 
     * @param int $id
     * @return array
     */
    public function deleteJadwal(int $id): array
    {
        try {
            // Check if exists
            $jadwal = $this->jadwalModel->find($id);
            if (!$jadwal) {
                return $this->errorResponse('Jadwal tidak ditemukan', 404);
            }

            // Check if jadwal has related absensi data
            $db = \Config\Database::connect();
            $checkAbsensi = $db->table('absensi')
                ->where('jadwal_mengajar_id', $id)
                ->countAllResults();

            if ($checkAbsensi > 0) {
                return $this->errorResponse('Jadwal udah ada absensinya, nggak bisa dihapus ya!', 409);
            }

            // Delete from database
            if ($this->jadwalModel->delete($id)) {
                $this->logInfo('deleteJadwal', "Deleted jadwal: $id");
                return $this->successResponse(['message' => 'Jadwal berhasil dihapus']);
            } else {
                return $this->errorResponse('Gagal menghapus jadwal');
            }
        } catch (\Exception $e) {
            $this->logError('deleteJadwal', $e);
            return $this->errorResponse('Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Get jadwal by guru ID
     * 
     * @param int $guruId
     * @return array
     */
    public function getByGuru(int $guruId): array
    {
        try {
            $jadwal = $this->jadwalModel->getByGuru($guruId);
            return $this->successResponse(['jadwal' => $jadwal]);
        } catch (\Exception $e) {
            $this->logError('getByGuru', $e);
            return $this->errorResponse('Gagal mengambil jadwal guru: ' . $e->getMessage());
        }
    }

    /**
     * Get jadwal by kelas ID
     * 
     * @param int $kelasId
     * @return array
     */
    public function getByKelas(int $kelasId): array
    {
        try {
            $jadwal = $this->jadwalModel->getByKelas($kelasId);
            return $this->successResponse(['jadwal' => $jadwal]);
        } catch (\Exception $e) {
            $this->logError('getByKelas', $e);
            return $this->errorResponse('Gagal mengambil jadwal kelas: ' . $e->getMessage());
        }
    }

    /**
     * Check schedule conflicts for guru and kelas
     * 
     * @param array $data
     * @return array
     */
    public function checkConflict(array $data): array
    {
        try {
            $conflictGuru = $this->jadwalModel->checkConflict(
                $data['guru_id'], 
                $data['hari'], 
                $data['jam_mulai'], 
                $data['jam_selesai'], 
                $data['exclude_id'] ?? null
            );
            
            $conflictKelas = $this->jadwalModel->checkKelasConflict(
                $data['kelas_id'], 
                $data['hari'], 
                $data['jam_mulai'], 
                $data['jam_selesai'], 
                $data['exclude_id'] ?? null
            );

            return $this->successResponse([
                'conflict_guru' => $conflictGuru,
                'conflict_kelas' => $conflictKelas
            ]);
        } catch (\Exception $e) {
            $this->logError('checkConflict', $e);
            return $this->errorResponse('Gagal memeriksa konflik: ' . $e->getMessage());
        }
    }

    /**
     * Validate jadwal batch for preview
     * 
     * @param array $data Array of jadwal rows from Excel
     * @return array
     */
    public function checkJadwalBatch(array $data): array
    {
        try {
            $results = [];
            $hariValid = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            $semesterValid = ['Ganjil', 'Genap'];
            $tahunAjaran = get_active_tahun_ajaran();

            foreach ($data as $item) {
                $row = $item['row'] ?? 0;
                $hari = $item['hari'] ?? '';
                $jamMulai = $item['jam_mulai'] ?? '';
                $jamSelesai = $item['jam_selesai'] ?? '';
                $guruInput = $item['guru'] ?? '';
                $mapelInput = $item['mapel'] ?? '';
                $kelasInput = $item['kelas'] ?? '';
                $semester = $item['semester'] ?? '';

                $errors = [];

                if (empty($hari) || empty($jamMulai) || empty($jamSelesai) || empty($guruInput) || empty($mapelInput) || empty($kelasInput) || empty($semester)) {
                    $errors[] = 'Data tidak lengkap';
                } else {
                    if (!in_array($hari, $hariValid)) {
                        $errors[] = 'Hari tidak valid';
                    }
                    if (!in_array($semester, $semesterValid)) {
                        $errors[] = 'Semester tidak valid';
                    }
                    if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $jamMulai)) {
                        $errors[] = 'Format jam mulai salah';
                    }
                    if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $jamSelesai)) {
                        $errors[] = 'Format jam selesai salah';
                    }

                    if (empty($errors)) {
                        $guruId = $this->resolveGuruId($guruInput);
                        if (!$guruId || !$this->guruModel->find($guruId)) {
                            $errors[] = 'Guru tidak ditemukan';
                        }

                        $mapelId = $this->resolveMataPelajaranId($mapelInput);
                        if (!$mapelId || !$this->mapelModel->find($mapelId)) {
                            $errors[] = 'Mapel tidak ditemukan';
                        }

                        $kelasId = $this->resolveKelasId($kelasInput);
                        if (!$kelasId || !$this->kelasModel->find($kelasId)) {
                            $errors[] = 'Kelas tidak ditemukan';
                        }

                        if (empty($errors)) {
                            $hasConflictGuru = $this->jadwalModel->checkConflict($guruId, $hari, $jamMulai, $jamSelesai, null, $tahunAjaran);
                            $hasConflictKelas = $this->jadwalModel->checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai, null, $tahunAjaran);

                            if ($hasConflictGuru) {
                                $errors[] = 'Konflik jadwal guru';
                            }
                            if ($hasConflictKelas) {
                                $errors[] = 'Konflik jadwal kelas';
                            }
                        }
                    }
                }

                if (empty($errors)) {
                    $results[] = [
                        'row' => $row,
                        'status' => 'valid',
                        'message' => ''
                    ];
                } elseif (count($errors) <= 2 && !in_array('Data tidak lengkap', $errors)) {
                    $results[] = [
                        'row' => $row,
                        'status' => 'warning',
                        'message' => implode(', ', $errors)
                    ];
                } else {
                    $results[] = [
                        'row' => $row,
                        'status' => 'error',
                        'message' => implode(', ', $errors)
                    ];
                }
            }

            return $this->successResponse([
                'results' => $results
            ]);
        } catch (\Exception $e) {
            $this->logError('checkJadwalBatch', $e);
            return $this->errorResponse('Gagal memvalidasi data: ' . $e->getMessage());
        }
    }

    /**
     * Get form dropdown lists
     * 
     * @return array
     */
    public function getFormLists(): array
    {
        try {
            return $this->successResponse([
                'guruOptions' => $this->guruModel->getGuruDropdown(),
                'mapelOptions' => $this->mapelModel->getAllMapelForDropdown(),
                'kelasOptions' => $this->kelasModel->getListKelas(get_active_tahun_ajaran()),
                'hariList' => $this->jadwalModel->getHariList(),
                'semesterList' => $this->jadwalModel->getSemesterList(),
                'tahunAjaranList' => $this->jadwalModel->getTahunAjaranList()
            ]);
        } catch (\Exception $e) {
            $this->logError('getFormLists', $e);
            return $this->errorResponse('Gagal mengambil data form: ' . $e->getMessage());
        }
    }

    /**
     * Export jadwal to Excel
     * 
     * @param array $filters
     * @return array
     */
    public function exportToExcel(array $filters = []): array
    {
        try {
            $semester = $filters['semester'] ?? null;
            $tahunAjaran = $filters['tahun_ajaran'] ?? null;

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

            $filename = 'jadwal-mengajar-' . date('Y-m-d-H-i-s') . '.xlsx';

            return $this->successResponse([
                'spreadsheet' => $spreadsheet,
                'filename' => $filename
            ]);
        } catch (\Exception $e) {
            $this->logError('exportToExcel', $e);
            return $this->errorResponse('Gagal export data: ' . $e->getMessage());
        }
    }

    /**
     * Process Excel import with multi-format support
     * 
     * @param $file
     * @param bool $skipDuplicate
     * @return array
     */
    public function processExcelImport($file, bool $skipDuplicate = false): array
    {
        try {
            // Validate file
            helper('security');
            $allowedTypes = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel'
            ];
            
            $validation = validate_file_upload($file, $allowedTypes, 5242880);
            
            if (!$validation['valid']) {
                return $this->errorResponse($validation['error']);
            }

            // Load spreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            // Skip header row
            array_shift($rows);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                if (empty($row[0])) continue; // Skip empty rows

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
                    $tahunAjaran = get_active_tahun_ajaran();

                    // Validate required fields
                    if (empty($hari) || empty($jamMulai) || empty($jamSelesai) || 
                        empty($guruInput) || empty($mataPelajaranInput) || empty($kelasInput) ||
                        empty($semester)) {
                        throw new \Exception("Data tidak lengkap pada baris " . ($index + 2));
                    }

                    // Validate hari
                    $hariValid = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                    if (!in_array($hari, $hariValid)) {
                        throw new \Exception("Hari tidak valid: {$hari}");
                    }

                    // Process Guru: Support ID or Name
                    $guruId = $this->resolveGuruId($guruInput);
                    $guru = $this->guruModel->find($guruId);
                    if (!$guru) {
                        throw new \Exception("Guru '{$guruInput}' tidak ditemukan");
                    }

                    // Process Mata Pelajaran: Support ID or Name
                    $mataPelajaranId = $this->resolveMataPelajaranId($mataPelajaranInput);
                    $mapel = $this->mapelModel->find($mataPelajaranId);
                    if (!$mapel) {
                        throw new \Exception("Mata Pelajaran '{$mataPelajaranInput}' tidak ditemukan");
                    }

                    // Process Kelas: Support ID or Name
                    $kelasId = $this->resolveKelasId($kelasInput);
                    $kelas = $this->kelasModel->find($kelasId);
                    if (!$kelas) {
                        throw new \Exception("Kelas '{$kelasInput}' tidak ditemukan");
                    }

                    // Check for schedule conflict for teacher
                    if ($this->jadwalModel->checkConflict($guruId, $hari, $jamMulai, $jamSelesai, null, $tahunAjaran)) {
                        if ($skipDuplicate) {
                            $errorCount++;
                            $errors[] = "Baris " . ($index + 2) . ": Guru {$guru['nama_lengkap']} sudah memiliki jadwal di waktu yang sama (dilewati)";
                            $db->transRollback();
                            continue;
                        } else {
                            throw new \Exception("Guru {$guru['nama_lengkap']} sudah memiliki jadwal di waktu yang sama");
                        }
                    }

                    // Check for schedule conflict for class
                    if ($this->jadwalModel->checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai, null, $tahunAjaran)) {
                        if ($skipDuplicate) {
                            $errorCount++;
                            $errors[] = "Baris " . ($index + 2) . ": Kelas {$kelas['nama_kelas']} sudah memiliki jadwal di waktu yang sama (dilewati)";
                            $db->transRollback();
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

                    $result = $this->jadwalModel->insert($jadwalData);

                    if (!$result) {
                        $validationErrors = $this->jadwalModel->errors();
                        throw new \Exception('Validasi gagal: ' . implode(', ', $validationErrors));
                    }

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

            $this->logInfo('processExcelImport', $message);

            return $this->successResponse([
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => $errors,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            $this->logError('processExcelImport', $e);
            return $this->errorResponse('Error saat memproses file: ' . $e->getMessage());
        }
    }

    /**
     * Resolve Guru ID from various input formats
     * Supports: ID, Name, "Name (NIP)" format
     * 
     * @param mixed $input
     * @return int|null
     */
    private function resolveGuruId($input): ?int
    {
        if (is_numeric($input)) {
            return (int)$input;
        }

        // Try extract NIP from format "Nama (NIP)"
        if (preg_match('/\(([^)]+)\)/', $input, $matches)) {
            $nip = trim($matches[1]);
            $guru = $this->guruModel->where('nip', $nip)->first();
            if ($guru) return $guru['id'];
        }
        
        // Try exact match by name
        $guru = $this->guruModel->where('nama_lengkap', trim($input))->first();
        if ($guru) return $guru['id'];
        
        // Try partial match with LIKE
        $guru = $this->guruModel->like('nama_lengkap', trim($input))->first();
        if ($guru) return $guru['id'];

        return null;
    }

    /**
     * Resolve Mata Pelajaran ID from various input formats
     * Supports: ID, Name, "Name (Kode)" format
     * 
     * @param mixed $input
     * @return int|null
     */
    private function resolveMataPelajaranId($input): ?int
    {
        if (is_numeric($input)) {
            return (int)$input;
        }

        // Try extract kode from format "Nama (Kode)"
        if (preg_match('/\(([^)]+)\)/', $input, $matches)) {
            $kode = trim($matches[1]);
            $mapel = $this->mapelModel->where('kode_mapel', $kode)->first();
            if ($mapel) return $mapel['id'];
        }
        
        // Try exact match by name
        $mapel = $this->mapelModel->where('nama_mapel', trim($input))->first();
        if ($mapel) return $mapel['id'];
        
        // Try partial match with LIKE
        $mapel = $this->mapelModel->like('nama_mapel', trim($input))->first();
        if ($mapel) return $mapel['id'];

        return null;
    }

    /**
     * Resolve Kelas ID from various input formats
     * Supports: ID, Name
     * 
     * @param mixed $input
     * @return int|null
     */
    private function resolveKelasId($input): ?int
    {
        if (is_numeric($input)) {
            return (int)$input;
        }

        $kelas = $this->kelasModel->where('nama_kelas', $input)->first();
        return $kelas ? $kelas['id'] : null;
    }

    /**
     * Generate Excel import template with dropdowns
     * 
     * @return array
     */
    public function generateImportTemplate(): array
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            
            // Template sheet
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Template Import Jadwal');

            // Set headers
            $headers = [
                'A1' => 'HARI',
                'B1' => 'JAM MULAI',
                'C1' => 'JAM SELESAI',
                'D1' => 'NAMA GURU',
                'E1' => 'MATA PELAJARAN',
                'F1' => 'KELAS',
                'G1' => 'SEMESTER',
            ];

            foreach ($headers as $cell => $text) {
                $sheet->setCellValue($cell, $text);
            }

            // Style header
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
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
            $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(25);

            // Get data for reference sheets
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

            // Create reference sheets (Guru, Mapel, Kelas)
            $this->createReferenceSheets($spreadsheet, $guruList, $mapelList, $kelasList, $headerStyle);

            // Add dropdowns to template
            $this->addDropdownsToTemplate($sheet, count($guruList), count($mapelList), count($kelasList));

            // Add sample data
            if (!empty($guruList) && !empty($mapelList) && !empty($kelasList)) {
                $sheet->setCellValue('A2', 'Senin');
                $sheet->setCellValue('B2', '07:00:00');
                $sheet->setCellValue('C2', '08:30:00');
                $sheet->setCellValue('D2', $guruList[0]['nama_lengkap']);
                $sheet->setCellValue('E2', $mapelList[0]['nama_mapel']);
                $sheet->setCellValue('F2', $kelasList[0]['nama_kelas']);
                $sheet->setCellValue('G2', 'Ganjil');
            }

            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(12);
            $sheet->getColumnDimension('B')->setWidth(12);
            $sheet->getColumnDimension('C')->setWidth(12);
            $sheet->getColumnDimension('D')->setWidth(35);
            $sheet->getColumnDimension('E')->setWidth(35);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(12);

            $sheet->freezePane('A2');

            // Create instruction sheet
            $this->createInstructionSheet($spreadsheet);

            $spreadsheet->setActiveSheetIndex(0);
            $filename = 'template-import-jadwal-' . date('Y-m-d') . '.xlsx';

            return $this->successResponse([
                'spreadsheet' => $spreadsheet,
                'filename' => $filename
            ]);
        } catch (\Exception $e) {
            $this->logError('generateImportTemplate', $e);
            return $this->errorResponse('Gagal generate template: ' . $e->getMessage());
        }
    }

    /**
     * Create reference sheets for dropdowns
     */
    private function createReferenceSheets($spreadsheet, $guruList, $mapelList, $kelasList, $headerStyle): void
    {
        // Data Guru sheet
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

        // Data Mata Pelajaran sheet
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

        // Data Kelas sheet
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
    }

    /**
     * Add dropdown validations to template sheet
     */
    private function addDropdownsToTemplate($sheet, $totalGuru, $totalMapel, $totalKelas): void
    {
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $semesterList = ['Ganjil', 'Genap'];

        for ($row = 2; $row <= 51; $row++) {
            // Dropdown HARI
            $validation = $sheet->getCell('A' . $row)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowDropDown(true);
            $validation->setFormula1('"' . implode(',', $hariList) . '"');
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Input Error');
            $validation->setError('Pilih hari dari dropdown');

            // Dropdown NAMA GURU
            if ($totalGuru > 0) {
                $validation = $sheet->getCell('D' . $row)->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                $validation->setFormula1("'Data Guru'!\$C\$2:\$C\$" . ($totalGuru + 1));
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Input Error');
                $validation->setError('Pilih guru dari dropdown');
            }

            // Dropdown MATA PELAJARAN
            if ($totalMapel > 0) {
                $validation = $sheet->getCell('E' . $row)->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                $validation->setFormula1("'Data Mata Pelajaran'!\$C\$2:\$C\$" . ($totalMapel + 1));
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Input Error');
                $validation->setError('Pilih mata pelajaran dari dropdown');
            }

            // Dropdown KELAS
            if ($totalKelas > 0) {
                $validation = $sheet->getCell('F' . $row)->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                $validation->setFormula1("'Data Kelas'!\$B\$2:\$B\$" . ($totalKelas + 1));
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Input Error');
                $validation->setError('Pilih kelas dari dropdown');
            }

            // Dropdown SEMESTER
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
    }

    /**
     * Create instruction sheet
     */
    private function createInstructionSheet($spreadsheet): void
    {
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
            [''],
            ['TIPS PENTING:'],
            ['✓ CUKUP PILIH NAMA dari dropdown (tidak perlu NIP atau kode!)'],
            ['✓ Dropdown otomatis mengambil data dari sheet referensi'],
            ['✓ Jangan mengubah nama kolom header'],
            ['✓ Format jam HARUS HH:MM:SS'],
            ['✓ Tahun ajaran otomatis diambil dari pengaturan sistem'],
            ['✓ Sistem akan otomatis mengecek konflik jadwal'],
            ['✓ Centang "Lewati jadwal konflik" saat upload untuk skip data yang konflik'],
            [''],
            ['VALIDASI OTOMATIS:'],
            ['→ Tahun ajaran menggunakan tahun ajaran aktif dari pengaturan sistem'],
            ['→ Sistem akan mengecek apakah guru sudah mengajar di jam yang sama (tahun ajaran aktif)'],
            ['→ Sistem akan mengecek apakah kelas sudah ada pelajaran di jam yang sama (tahun ajaran aktif)'],
            ['→ Data yang valid akan diimport, yang invalid akan dilaporkan'],
        ];

        $instructionSheet->fromArray($instructions, null, 'A1');
        $instructionSheet->getColumnDimension('A')->setWidth(100);
        
        $instructionSheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF4472C4']],
        ]);
        $instructionSheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
        ]);
        $instructionSheet->getStyle('A13')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
        ]);
        $instructionSheet->getStyle('A20')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
        ]);
    }
}
