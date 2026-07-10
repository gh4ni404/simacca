<?php

namespace App\Services;

use App\Models\SiswaModel;
use App\Models\UserModel;
use App\Models\KelasModel;
use App\Models\AbsensiDetailModel;
use App\Models\SettingModel;

/**
 * SiswaService
 * 
 * Service layer for managing siswa (student) business logic.
 * Handles CRUD operations, Excel import, kelas auto-creation, and validations.
 * 
 * @package App\Services
 */
class SiswaService extends BaseService
{
    protected $siswaModel;
    protected $userModel;
    protected $kelasModel;
    protected $absensiDetailModel;
    
    /**
     * Performance: Cache kelas lookups during import to avoid N+1 queries
     */
    private $kelasCache = [];

    public function __construct()
    {
        parent::__construct();
        $this->siswaModel = new SiswaModel();
        $this->userModel = new UserModel();
        $this->kelasModel = new KelasModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
    }

    /**
     * Get all siswa with filtering and search
     * 
     * @param array $filters Optional filters (search keyword)
     * @return array
     */
    public function getAllSiswa(array $filters = []): array
    {
        try {
            $keyword = $filters['search'] ?? null;
            $status = $filters['status'] ?? 'active';
            $kelasId = $filters['kelas_id'] ?? null;
            $page = (int) ($filters['page'] ?? 1);
            $perPage = (int) ($filters['perPage'] ?? 10);
            $offset = ($page - 1) * $perPage;
            $tahunAjaran = $filters['tahun_ajaran'] ?? get_active_tahun_ajaran();

            $modelFilters = [
                'status' => $status,
                'kelas_id' => $kelasId,
            ];

            if ($keyword) {
                $total = $this->siswaModel->countSearch($keyword, $status, $kelasId, $tahunAjaran);
                $siswa = $this->siswaModel->searchSiswa($keyword, $status, $perPage, $offset, $kelasId, $tahunAjaran);
            } else {
                $siswa = $this->siswaModel->getAllSiswa($status, $perPage, $offset, $kelasId, $tahunAjaran);
                $total = match ($status) {
                    'inactive' => $this->siswaModel->countInactive($tahunAjaran),
                    'all' => $this->siswaModel->countAll(),
                    default => $this->siswaModel->countActive($tahunAjaran),
                };
                if ($kelasId) {
                    $total = $this->siswaModel->getCountKelasById($kelasId, $status, $tahunAjaran);
                }
            }

            return $this->successResponse([
                'siswa' => $siswa,
                'total' => $total,
                'perPage' => $perPage,
                'currentPage' => $page,
                'kelasSummary' => $this->siswaModel->getCountByKelas($tahunAjaran)
            ]);
        } catch (\Exception $e) {
            $this->logError('getAllSiswa', $e);
            return $this->errorResponse('Gagal mengambil data siswa: ' . $e->getMessage());
        }
    }

    /**
     * Get siswa by ID with complete information
     * 
     * @param int $id
     * @return array
     */
    public function getSiswaById(int $id): array
    {
        try {
            $siswa = $this->siswaModel->getSiswaWithWaliKelas($id);
            
            if (!$siswa) {
                return $this->errorResponse('Siswa tidak ditemukan', 404);
            }
            
            // Get user data
            $user = $this->userModel->find($siswa['user_id']);
            
            // Get kelas data
            $kelas = $this->kelasModel->find($siswa['kelas_id']);
            
            // Get absensi statistics
            $absensiStats = $this->absensiDetailModel->getStatistikSiswa($id);
            
            return $this->successResponse([
                'siswa' => $siswa,
                'user' => $user,
                'kelas' => $kelas,
                'absensiStats' => $absensiStats
            ]);
        } catch (\Exception $e) {
            $this->logError('getSiswaById', $e);
            return $this->errorResponse('Gagal mengambil data siswa: ' . $e->getMessage());
        }
    }

    /**
     * Create new siswa with user account
     * 
     * @param array $data Siswa and user data
     * @return array
     */
    public function createSiswa(array $data): array
    {
        // Validate required fields
        $validation = $this->validateSiswaData($data);
        if (!$validation['success']) {
            return $validation;
        }

        return $this->executeInTransaction(function() use ($data) {
            // 1. Create user account
            $userData = [
                'username' => $data['username'],
                'password' => $data['password'],
                'role' => 'siswa',
                'email' => $data['email'] ?? null,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $userId = $this->userModel->insert($userData);
            
            if (!$userId) {
                throw new \Exception('Gagal membuat user account');
            }

            // 2. Create siswa data
            $siswaData = [
                'user_id' => $userId,
                'nis' => $data['nis'],
                'nama_lengkap' => $data['nama_lengkap'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'kelas_id' => $data['kelas_id'],
                'tahun_ajaran' => get_active_tahun_ajaran(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $siswaId = $this->siswaModel->insert($siswaData);
            
            if (!$siswaId) {
                throw new \Exception('Gagal membuat data siswa');
            }

            $this->logInfo('createSiswa', "Created siswa: $siswaId, user: $userId");

            // 3. Send welcome email if email is provided
            if (!empty($data['email'])) {
                $this->sendWelcomeEmail(
                    $data['email'],
                    $data['username'],
                    $data['password'],
                    'siswa',
                    $data['nama_lengkap']
                );
            }

            return $this->successResponse([
                'siswa_id' => $siswaId,
                'user_id' => $userId,
                'message' => 'Siswa berhasil ditambahkan'
            ]);
        });
    }

    /**
     * Update siswa data and user account
     * 
     * @param int $id Siswa ID
     * @param array $data Update data
     * @return array
     */
    public function updateSiswa(int $id, array $data): array
    {
        try {
            $siswa = $this->siswaModel->find($id);
            
            if (!$siswa) {
                return $this->errorResponse('Siswa tidak ditemukan', 404);
            }

            return $this->executeInTransaction(function() use ($id, $siswa, $data) {
                // 1. Update user account
                $userData = $this->userModel->find($siswa['user_id']);
                $userUpdateData = [
                    'username' => $data['username'],
                    'email' => $data['email'] ?? null
                ];

                // Update password if provided
                $plainPassword = null;
                if (!empty($data['password'])) {
                    $plainPassword = $data['password'];
                    $userUpdateData['password'] = $plainPassword;
                    $this->logInfo('updateSiswa', "Password will be updated for user_id: {$siswa['user_id']}");
                }

                // Skip Model validation since we already validated in controller
                $this->userModel->skipValidation(true);
                $result = $this->userModel->update($siswa['user_id'], $userUpdateData);
                $this->userModel->skipValidation(false);
                
                if (!$result) {
                    $this->logError('updateSiswa', 'Failed to update user. Errors: ' . json_encode($this->userModel->errors()));
                    throw new \Exception('Gagal mengupdate data user');
                }

                // 2. Update siswa data
                $siswaData = [
                    'nis' => $data['nis'],
                    'nama_lengkap' => $data['nama_lengkap'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'kelas_id' => $data['kelas_id'],
                ];

                $this->siswaModel->update($id, $siswaData);

                // Send email notification if password was changed
                if ($plainPassword && !empty($userData['email'])) {
                    helper('email');
                    
                    $fullName = $siswa['nama_lengkap'] ?? $userData['username'];
                    
                    $emailSent = send_password_changed_by_admin_notification(
                        $userData['email'],
                        $fullName,
                        $userData['username'],
                        $plainPassword
                    );
                    
                    if ($emailSent) {
                        $this->logInfo('updateSiswa', "Password change notification sent to: {$userData['email']}");
                    } else {
                        $this->logWarning('updateSiswa', "Failed to send password notification to: {$userData['email']}");
                    }
                }

                $this->logInfo('updateSiswa', "Updated siswa: $id");

                return $this->successResponse([
                    'siswa_id' => $id,
                    'message' => 'Data siswa berhasil diperbarui'
                ]);
            });
        } catch (\Exception $e) {
            $this->logError('updateSiswa', $e);
            return $this->errorResponse('Gagal mengupdate data siswa: ' . $e->getMessage());
        }
    }

    protected function sendWelcomeEmail(
        string $email,
        string $username,
        string $password,
        string $role,
        string $fullName
    ): void {
        helper('email');

        $emailSent = send_welcome_email(
            $email,
            $username,
            $password,
            $role,
            $fullName,
            $email
        );

        if ($emailSent) {
            $this->logInfo('createSiswa', "Welcome email sent to: {$email}");
        } else {
            $this->logWarning('createSiswa', "Failed to send welcome email to: {$email}");
        }
    }

    /**
     * Delete siswa and cascade to user account
     * 
     * @param int $id Siswa ID
     * @return array
     */
    public function deleteSiswa(int $id): array
    {
        try {
            $siswa = $this->siswaModel->find($id);
            
            if (!$siswa) {
                return $this->errorResponse('Siswa tidak ditemukan', 404);
            }

            return $this->executeInTransaction(function() use ($id, $siswa) {
                // 1. Delete siswa data
                $this->siswaModel->delete($id);

                // 2. Delete user account
                $this->userModel->delete($siswa['user_id']);

                $this->logInfo('deleteSiswa', "Deleted siswa: $id, user: {$siswa['user_id']}");

                return $this->successResponse([
                    'message' => 'Data siswa berhasil dihapus'
                ]);
            });
        } catch (\Exception $e) {
            $this->logError('deleteSiswa', $e);
            return $this->errorResponse('Gagal menghapus data siswa: ' . $e->getMessage());
        }
    }

    /**
     * Change siswa status (active/inactive)
     * 
     * @param int $id Siswa ID
     * @return array
     */
    public function changeStatus(int $id): array
    {
        try {
            $siswa = $this->siswaModel->find($id);
            
            if (!$siswa) {
                return $this->errorResponse('Siswa tidak ditemukan', 404);
            }

            $user = $this->userModel->find($siswa['user_id']);
            
            if (!$user) {
                return $this->errorResponse('User tidak ditemukan', 404);
            }

            // Toggle status
            $newStatus = $user['is_active'] ? 0 : 1;
            
            $this->userModel->update($siswa['user_id'], ['is_active' => $newStatus]);
            
            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            $this->logInfo('changeStatus', "Siswa $id status changed to: $statusText");

            return $this->successResponse([
                'new_status' => $newStatus,
                'message' => "Siswa berhasil $statusText"
            ]);
        } catch (\Exception $e) {
            $this->logError('changeStatus', $e);
            return $this->errorResponse('Gagal mengubah status siswa: ' . $e->getMessage());
        }
    }

    /**
     * Bulk action on multiple siswa
     * 
     * @param string $action Action type: activate, deactivate, delete
     * @param array $ids Array of siswa IDs
     * @return array
     */
    public function bulkAction(string $action, array $ids): array
    {
        if (empty($ids)) {
            return $this->errorResponse('Tidak ada siswa yang dipilih');
        }

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $siswa = $this->siswaModel->find($id);

                if (!$siswa) {
                    $errorCount++;
                    $errors[] = "Siswa ID $id tidak ditemukan";
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

                    default:
                        $errorCount++;
                        $errors[] = "Action tidak valid: $action";
                }
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "Error pada siswa ID $id: " . $e->getMessage();
            }
        }

        $this->logInfo('bulkAction', "Action: $action, Success: $successCount, Failed: $errorCount");

        return $this->successResponse([
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors,
            'message' => "Berhasil $action $successCount siswa" . ($errorCount > 0 ? ", gagal $errorCount" : '')
        ]);
    }

    /**
     * Check NIS availability
     * 
     * @param string $nis
     * @param int|null $excludeId Siswa ID to exclude from check
     * @return array
     */
    public function checkNisAvailability(string $nis, ?int $excludeId = null): array
    {
        try {
            $query = $this->siswaModel->where('nis', $nis);

            if ($excludeId) {
                $query->where('id !=', $excludeId);
            }

            $exists = $query->countAllResults() > 0;

            return $this->successResponse([
                'available' => !$exists,
                'message' => $exists ? 'NIS sudah digunakan' : 'NIS tersedia'
            ]);
        } catch (\Exception $e) {
            $this->logError('checkNisAvailability', $e);
            return $this->errorResponse('Gagal memeriksa NIS: ' . $e->getMessage());
        }
    }

    /**
     * Check username availability
     * 
     * @param string $username
     * @param int|null $excludeUserId User ID to exclude from check
     * @return array
     */
    public function checkUsernameAvailability(string $username, ?int $excludeUserId = null): array
    {
        try {
            $query = $this->userModel->where('username', $username);
            
            if ($excludeUserId) {
                $query->where('id !=', $excludeUserId);
            }
            
            $exists = $query->countAllResults() > 0;

            return $this->successResponse([
                'available' => !$exists,
                'message' => $exists ? 'Username sudah digunakan' : 'Username tersedia'
            ]);
        } catch (\Exception $e) {
            $this->logError('checkUsernameAvailability', $e);
            return $this->errorResponse('Gagal memeriksa username: ' . $e->getMessage());
        }
    }

    /**
     * Get form dropdown lists (kelas, tahun ajaran)
     * 
     * @return array
     */
    public function getFormLists(): array
    {
        try {
            return $this->successResponse([
                'kelasList' => $this->kelasModel->getListKelas(),
                'tahunAjaranList' => $this->getTahunAjaranList()
            ]);
        } catch (\Exception $e) {
            $this->logError('getFormLists', $e);
            return $this->errorResponse('Gagal mengambil data form: ' . $e->getMessage());
        }
    }

    /**
     * Get statistics for dashboard
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        try {
            $total = $this->siswaModel->countAll();
            $byKelas = $this->siswaModel->getCountByKelas();
            
            // Count active/inactive
            $active = $this->siswaModel
                ->join('users', 'users.id = siswa.user_id')
                ->where('users.is_active', 1)
                ->countAllResults();
            
            $inactive = $total - $active;

            return $this->successResponse([
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'byKelas' => $byKelas
            ]);
        } catch (\Exception $e) {
            $this->logError('getStatistics', $e);
            return $this->errorResponse('Gagal mengambil statistik: ' . $e->getMessage());
        }
    }

    /**
     * Validate siswa data before create/update
     * 
     * @param array $data
     * @param bool $isUpdate
     * @return array
     */
    private function validateSiswaData(array $data, bool $isUpdate = false): array
    {
        $errors = [];

        // Required fields for create
        if (!$isUpdate) {
            if (empty($data['nis'])) {
                $errors['nis'] = 'NIS harus diisi';
            }
            if (empty($data['username'])) {
                $errors['username'] = 'Username harus diisi';
            }
            if (empty($data['password']) || strlen($data['password']) < 6) {
                $errors['password'] = 'Password minimal 6 karakter';
            }
        }

        if (empty($data['nama_lengkap'])) {
            $errors['nama_lengkap'] = 'Nama lengkap harus diisi';
        }
        if (empty($data['jenis_kelamin']) || !in_array($data['jenis_kelamin'], ['L', 'P'])) {
            $errors['jenis_kelamin'] = 'Jenis kelamin harus L atau P';
        }
        if (empty($data['kelas_id'])) {
            $errors['kelas_id'] = 'Kelas harus dipilih';
        }
        if (empty($data['tahun_ajaran'])) {
            $errors['tahun_ajaran'] = 'Tahun ajaran harus diisi';
        }

        if (!empty($errors)) {
            return $this->errorResponse('Validasi gagal', 422, $errors);
        }

        return $this->successResponse(['message' => 'Validasi berhasil']);
    }

    /**
     * Get list of academic years
     * 
     * @return array
     */
    /**
     * Rollover siswa ke tahun ajaran baru: naik kelas otomatis
     * - Tingkat 10 → 11 (cari kelas dengan jurusan sama)
     * - Tingkat 11 → 12 (cari kelas dengan jurusan sama)
     * - Tingkat 12 → Lulus (nonaktifkan user)
     *
     * @param string $newTahunAjaran Tahun ajaran baru (format: YYYY/YYYY)
     * @return array
     */
    public function rolloverTahunAjaran(string $newTahunAjaran): array
    {
        try {
            $naikCount = 0;
            $lulusCount = 0;
            $skipped = [];
            $updated = [];

            // Get all active siswa with kelas info
            $siswaList = $this->siswaModel
                ->select('siswa.*, kelas.tingkat, kelas.jurusan, kelas.nama_kelas, users.is_active as user_is_active')
                ->join('kelas', 'kelas.id = siswa.kelas_id')
                ->join('users', 'users.id = siswa.user_id')
                ->where('users.is_active', 1)
                ->findAll();

            // Kumpulkan backup SEMUA siswa dulu sebelum perubahan apa pun
            $backup = [];
            foreach ($siswaList as $siswa) {
                $backup[] = [
                    'siswa_id' => $siswa['id'],
                    'user_id' => $siswa['user_id'],
                    'kelas_id' => $siswa['kelas_id'],
                    'tahun_ajaran' => $siswa['tahun_ajaran'],
                    'is_active' => $siswa['user_is_active'],
                ];
            }

            // Simpan backup ke settings SEBELUM melakukan perubahan
            $settingModel = new SettingModel();
            $settingModel->setSetting('rollover_backup', json_encode([
                'new_tahun_ajaran' => $newTahunAjaran,
                'created_at' => date('Y-m-d H:i:s'),
                'changes' => $backup,
            ]));

            // Lakukan rollover
            foreach ($siswaList as $siswa) {
                $tingkat = (int) $siswa['tingkat'];
                $jurusan = $siswa['jurusan'];
                $nextTingkat = $tingkat + 1;

                if ($tingkat < 12) {
                    // Cari kelas tujuan dengan tingkat+1 dan jurusan sama
                    $targetKelas = $this->kelasModel
                        ->where('tingkat', $nextTingkat)
                        ->where('jurusan', $jurusan)
                        ->first();

                    if (!$targetKelas) {
                        $skipped[] = "{$siswa['nama_lengkap']} (NIS: {$siswa['nis']}) - Kelas {$siswa['nama_kelas']} → tidak ada kelas tingkat $nextTingkat untuk jurusan $jurusan";
                        continue;
                    }

                    // Update kelas_id dan tahun_ajaran
                    $this->siswaModel->update($siswa['id'], [
                        'kelas_id' => $targetKelas['id'],
                        'tahun_ajaran' => $newTahunAjaran,
                    ]);

                    $naikCount++;
                    $updated[] = "{$siswa['nama_lengkap']} (NIS: {$siswa['nis']}): {$siswa['nama_kelas']} → {$targetKelas['nama_kelas']}";
                } else {
                    // Tingkat 12 → Lulus: nonaktifkan user
                    $this->userModel->update($siswa['user_id'], ['is_active' => 0]);

                    // Update tahun_ajaran tetap disimpan untuk histori
                    $this->siswaModel->update($siswa['id'], [
                        'tahun_ajaran' => $newTahunAjaran,
                    ]);

                    $lulusCount++;
                    $updated[] = "{$siswa['nama_lengkap']} (NIS: {$siswa['nis']}): {$siswa['nama_kelas']} → LULUS";
                }
            }

            $this->logInfo('rolloverTahunAjaran', "Naik kelas: $naikCount, Lulus: $lulusCount, Skipped: " . count($skipped));

            return $this->successResponse([
                'naik_kelas' => $naikCount,
                'lulus' => $lulusCount,
                'skipped' => $skipped,
                'updated' => $updated,
                'has_backup' => true,
                'message' => "Rollover selesai: $naikCount siswa naik kelas, $lulusCount siswa lulus." . (!empty($skipped) ? ' ' . count($skipped) . ' siswa dilewati.' : '')
            ]);
        } catch (\Exception $e) {
            $this->logError('rolloverTahunAjaran', $e);
            return $this->errorResponse('Gagal menjalankan rollover: ' . $e->getMessage());
        }
    }

    public function revertRollover(): array
    {
        try {
            $settingModel = new SettingModel();
            $backupJson = $settingModel->get('rollover_backup');

            if (!$backupJson) {
                return $this->errorResponse('Tidak ada data rollover yang bisa di-revert.');
            }

            $backup = json_decode($backupJson, true);
            $changes = $backup['changes'] ?? [];

            if (empty($changes)) {
                return $this->errorResponse('Data backup kosong.');
            }

            $revertCount = 0;
            $errors = [];

            foreach ($changes as $item) {
                try {
                    // Kembalikan data siswa
                    $this->siswaModel->update($item['siswa_id'], [
                        'kelas_id' => $item['kelas_id'],
                        'tahun_ajaran' => $item['tahun_ajaran'],
                    ]);

                    // Kembalikan status aktif user
                    $this->userModel->update($item['user_id'], [
                        'is_active' => $item['is_active'],
                    ]);

                    $revertCount++;
                } catch (\Exception $e) {
                    $errors[] = "Gagal revert siswa ID {$item['siswa_id']}: " . $e->getMessage();
                }
            }

            // Hapus backup setelah revert berhasil
            $settingModel->setSetting('rollover_backup', '');

            $this->logInfo('revertRollover', "Revert sukses: $revertCount siswa");

            return $this->successResponse([
                'reverted' => $revertCount,
                'errors' => $errors,
                'message' => "Rollover berhasil di-revert: $revertCount siswa dikembalikan." . (!empty($errors) ? ' ' . count($errors) . ' gagal.' : '')
            ]);
        } catch (\Exception $e) {
            $this->logError('revertRollover', $e);
            return $this->errorResponse('Gagal revert rollover: ' . $e->getMessage());
        }
    }

    private function getTahunAjaranList(): array
    {
        return get_tahun_ajaran_list();
    }

    /**
     * Process Excel import with validation and kelas auto-creation
     * 
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file
     * @return array
     */
    public function processExcelImport($file): array
    {
        try {
            // Validate file
            helper('security');
            $allowedTypes = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel'
            ];
            
            $validation = validate_file_upload($file, $allowedTypes, 5242880); // 5MB limit
            
            if (!$validation['valid']) {
                return $this->errorResponse($validation['error']);
            }

            // Load spreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            array_shift($rows);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            $createdClasses = []; // Track kelas baru yang dibuat

            // Set non-strict mode to auto-reset transStatus after each iteration
            $db = \Config\Database::connect();
            $db->transStrict(false);

            foreach ($rows as $index => $row) {
                // Skip empty rows
                if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
                    continue;
                }

                $rowNumber = $index + 2; // Excel row number (header = row 1)

                $nis = '';
                $username = '';

                try {
                    // Validate and sanitize data
                    $nis = trim($row[1] ?? '');
                    $namaLengkap = trim($row[2] ?? '');
                    $jenisKelamin = strtoupper(trim($row[3] ?? ''));
                    $namaKelas = trim($row[4] ?? '');
                    $tahunAjaran = trim($row[5] ?? '');
                    
                    // Validate required fields
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

                    // Generate username and password
                    $username = !empty($row[7]) ? trim($row[7]) : 'siswa_' . $nis;
                    $password = !empty($row[8]) ? trim($row[8]) : 'siswa123';
                    $email = !empty($row[6]) ? trim($row[6]) : null;

                    // Check if NIS already exists in database
                    $existingSiswa = $this->siswaModel->where('nis', $nis)->first();

                    if ($existingSiswa) {
                        // Get existing user
                        $existingUser = $this->userModel->find($existingSiswa['user_id']);

                        if ($existingUser && $existingUser['is_active'] == 1) {
                            // ACTIVE student with same NIS → UPDATE existing records
                            $db->transStart();

                            // Get or create kelas
                            $kelasId = $this->getKelasIdByName($namaKelas);

                            // Update user data (only email if provided)
                            $userUpdate = ['password' => $password];
                            if (!empty($email)) {
                                $userUpdate['email'] = $email;
                            }
                            $this->userModel->update($existingUser['id'], $userUpdate);

                            // Update siswa data
                            $this->siswaModel->update($existingSiswa['id'], [
                                'nama_lengkap' => $namaLengkap,
                                'jenis_kelamin' => $jenisKelamin,
                                'kelas_id' => $kelasId,
                                'tahun_ajaran' => $tahunAjaran,
                            ]);

                            $db->transComplete();
                            $successCount++;

                            // Track kelas
                            if (!isset($createdClasses[$namaKelas])) {
                                $createdClasses[$namaKelas] = true;
                            }
                        } else {
                            // INACTIVE student → REACTIVATE and UPDATE
                            $db->transStart();

                            $kelasId = $this->getKelasIdByName($namaKelas);

                            // Reactivate user and update data
                            $userUpdate = [
                                'password' => $password,
                                'is_active' => 1,
                            ];
                            if (!empty($email)) {
                                $userUpdate['email'] = $email;
                            }
                            $this->userModel->update($existingUser['id'], $userUpdate);

                            // Update siswa data
                            $this->siswaModel->update($existingSiswa['id'], [
                                'nama_lengkap' => $namaLengkap,
                                'jenis_kelamin' => $jenisKelamin,
                                'kelas_id' => $kelasId,
                                'tahun_ajaran' => $tahunAjaran,
                            ]);

                            $db->transComplete();
                            $successCount++;

                            if (!isset($createdClasses[$namaKelas])) {
                                $createdClasses[$namaKelas] = true;
                            }
                        }
                    } else {
                        // NEW student → CREATE user + siswa
                        $db->transStart();

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

                        // 2. Get or create kelas
                        $kelasId = $this->getKelasIdByName($namaKelas);

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
                        $successCount++;

                        // Send welcome email for NEW students
                        if (!empty($email)) {
                            helper('email');
                            $emailSent = send_welcome_email(
                                $email,
                                $username,
                                $password,
                                'siswa',
                                $namaLengkap,
                                $email
                            );
                            if ($emailSent) {
                                $this->logInfo('processExcelImport', "Welcome email sent to: {$email}");
                            } else {
                                $this->logWarning('processExcelImport', "Failed to send welcome email to: {$email}");
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Safe to call even if no transaction active (check transDepth internally)
                    if (isset($db)) {
                        $db->transRollback();
                    }
                    $errorCount++;
                    
                    $errors[] = "Baris $rowNumber (NIS: $nis, Nama: $namaLengkap): " . $e->getMessage();
                }
            }
            
            // Informasi kelas baru yang dibuat
            $kelasBaruInfo = count($createdClasses) > 0 
                ? " Kelas baru dibuat: " . implode(', ', array_keys($createdClasses)) . "."
                : "";

            $message = "Import selesai. Berhasil: $successCount, Gagal: $errorCount." . $kelasBaruInfo;

            $this->logInfo('processExcelImport', "Import completed - Success: $successCount, Failed: $errorCount");

            return $this->successResponse([
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => $errors,
                'created_classes' => array_keys($createdClasses),
                'message' => $message
            ]);
        } catch (\Exception $e) {
            $this->logError('processExcelImport', $e);
            return $this->errorResponse('Error: ' . $e->getMessage());
        }
    }

    /**
     * Get kelas ID by name, create if not exists
     * Uses caching to avoid N+1 queries during import
     * 
     * @param string $namaKelas
     * @return int Kelas ID
     * @throws \Exception
     */
    private function getKelasIdByName(string $namaKelas): int
    {
        // Validate input
        if (empty($namaKelas) || trim($namaKelas) === '') {
            throw new \Exception("Nama kelas tidak boleh kosong");
        }
        
        // Normalize whitespace
        $namaKelas = trim($namaKelas);
        
        // Validate length (max 10 chars in database)
        if (strlen($namaKelas) > 10) {
            throw new \Exception("Nama kelas '$namaKelas' terlalu panjang (max 10 karakter)");
        }
        
        // Check cache first to avoid repeated DB queries
        if (isset($this->kelasCache[$namaKelas])) {
            return $this->kelasCache[$namaKelas];
        }
        
        // Check if kelas already exists
        $kelas = $this->kelasModel->where('nama_kelas', $namaKelas)->first();
        
        if ($kelas) {
            // Cache the result
            $this->kelasCache[$namaKelas] = $kelas['id'];
            return $kelas['id'];
        }
        
        // Kelas doesn't exist, create new
        // Parse nama kelas to get tingkat and jurusan
        // Supported formats: X-RPL, XI-RPL, XII-RPL, 10-RPL, 11-RPL, 12-RPL
        $tingkat = null;
        $jurusan = null;
        
        // Convert to uppercase first
        $namaKelasUpper = strtoupper($namaKelas);
        
        // Try to parse format "X-RPL" or "10-RPL"
        if (preg_match('/^(X|XI|XII|10|11|12)[\s\-_](.+)$/', $namaKelasUpper, $matches)) {
            // Convert roman numerals to numbers
            $tingkatMap = [
                'X' => '10',
                'XI' => '11', 
                'XII' => '12'
            ];
            
            $tingkatInput = $matches[1];
            $tingkat = isset($tingkatMap[$tingkatInput]) ? $tingkatMap[$tingkatInput] : $tingkatInput;
            $jurusan = trim($matches[2]);
        } else {
            // If format doesn't match, use default
            $tingkat = '10';
            $jurusan = $namaKelas;
        }
        
        // Validate tingkat must be 10, 11, or 12
        if (!in_array($tingkat, ['10', '11', '12'])) {
            throw new \Exception("Tingkat kelas '$namaKelas' tidak valid. Format yang didukung: X-XXX, XI-XXX, XII-XXX, atau 10-XXX, 11-XXX, 12-XXX");
        }
        
        // Validate jurusan length (max 50 chars)
        if (strlen($jurusan) > 50) {
            throw new \Exception("Nama jurusan untuk kelas '$namaKelas' terlalu panjang (max 50 karakter)");
        }
        
        // Create new kelas
        $kelasData = [
            'nama_kelas' => $namaKelas,
            'tingkat' => $tingkat,
            'jurusan' => $jurusan,
            'wali_kelas_id' => null
        ];
        
        try {
            // Skip validation to avoid is_unique constraint during auto-create
            $this->kelasModel->skipValidation(true);
            
            try {
                $kelasId = $this->kelasModel->insert($kelasData);
                
                // Double check to handle race condition
                if (!$kelasId) {
                    $kelas = $this->kelasModel->where('nama_kelas', $namaKelas)->first();
                    if ($kelas) {
                        $this->kelasCache[$namaKelas] = $kelas['id'];
                        return $kelas['id'];
                    }
                    throw new \Exception("Gagal membuat kelas '$namaKelas'");
                }
                
                // Cache the newly created kelas
                $this->kelasCache[$namaKelas] = $kelasId;
                
                $this->logInfo('getKelasIdByName', "Created new kelas: $namaKelas (ID: $kelasId)");
                
                return $kelasId;
            } finally {
                // Always restore validation state
                $this->kelasModel->skipValidation(false);
            }
        } catch (\Exception $e) {
            // Handle duplicate key error (race condition)
            if (strpos($e->getMessage(), 'Duplicate entry') !== false || 
                strpos($e->getMessage(), 'UNIQUE constraint') !== false) {
                // Kelas was created by another thread, search again
                $kelas = $this->kelasModel->where('nama_kelas', $namaKelas)->first();
                if ($kelas) {
                    // Cache the result
                    $this->kelasCache[$namaKelas] = $kelas['id'];
                    return $kelas['id'];
                }
            }
            
            throw new \Exception("Gagal membuat kelas '$namaKelas': " . $e->getMessage());
        }
    }

    /**
     * Export siswa data to Excel
     * 
     * @return array Spreadsheet object and filename
     */
    public function exportToExcel(): array
    {
        try {
            $tahunAjaran = get_active_tahun_ajaran();
            $siswa = $this->siswaModel->getAllSiswa('active', null, 0, null, $tahunAjaran);

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

            $filename = 'data-siswa-' . date('Y-m-d') . '.xlsx';

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
     * Generate Excel import template
     * 
     * @return array Spreadsheet object and filename
     */
    public function generateImportTemplate(): array
    {
        try {
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

            $filename = 'template-import-siswa.xlsx';

            return $this->successResponse([
                'spreadsheet' => $spreadsheet,
                'filename' => $filename
            ]);
        } catch (\Exception $e) {
            $this->logError('generateImportTemplate', $e);
            return $this->errorResponse('Gagal generate template: ' . $e->getMessage());
        }
    }
}
