<?php

namespace App\Services;

use App\Models\SiswaModel;
use App\Models\UserModel;
use App\Models\KelasModel;
use App\Models\AbsensiDetailModel;
use App\Models\SettingModel;
use App\Models\RolloverHistoryModel;
use App\Models\RolloverBackupModel;

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
    protected $rolloverHistoryModel;
    protected $rolloverBackupModel;
    
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
        $this->rolloverHistoryModel = new RolloverHistoryModel();
        $this->rolloverBackupModel = new RolloverBackupModel();
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
     * Get all matching siswa IDs (no pagination) for bulk select-all
     *
     * @param array $filters
     * @return array
     */
    public function getAllSiswaIds(array $filters = []): array
    {
        try {
            $keyword = $filters['search'] ?? null;
            $status = $filters['status'] ?? 'active';
            $kelasId = $filters['kelas_id'] ?? null;
            $tahunAjaran = $filters['tahun_ajaran'] ?? get_active_tahun_ajaran();

            $ids = $this->siswaModel->getAllSiswaIds($status, $kelasId, $tahunAjaran, $keyword);

            return $this->successResponse([
                'ids' => $ids,
                'total' => count($ids)
            ]);
        } catch (\Exception $e) {
            $this->logError('getAllSiswaIds', $e);
            return $this->errorResponse('Gagal mengambil data ID siswa: ' . $e->getMessage());
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
     * Batch check NIS status for import preview.
     * Returns status for each NIS: 'new', 'active', or 'inactive'
     * 
     * @param array $nisList Array of NIS strings
     * @return array
     */
    public function checkNisBatch(array $nisList): array
    {
        try {
            $nisList = array_unique(array_filter(array_map('trim', $nisList)));

            if (empty($nisList)) {
                return $this->successResponse(['results' => []]);
            }

            // Get all siswa matching the NIS list (bypass soft deletes to detect deleted records)
            $existingSiswa = $this->db->table('siswa')
                ->select('siswa.nis, siswa.deleted_at, users.is_active')
                ->join('users', 'users.id = siswa.user_id')
                ->whereIn('siswa.nis', $nisList)
                ->get()
                ->getResultArray();

            // Build lookup map: nis => { is_active, is_deleted, deleted_at }
            $nisInfoMap = [];
            foreach ($existingSiswa as $siswa) {
                $nisInfoMap[$siswa['nis']] = [
                    'is_active' => (int) $siswa['is_active'],
                    'is_deleted' => !is_null($siswa['deleted_at']),
                    'deleted_at' => $siswa['deleted_at'],
                ];
            }

            $results = [];
            foreach ($nisList as $nis) {
                if (isset($nisInfoMap[$nis])) {
                    $info = $nisInfoMap[$nis];
                    if ($info['is_deleted']) {
                        $deletedDate = date('d M Y', strtotime($info['deleted_at']));
                        $results[$nis] = [
                            'status' => 'deleted',
                            'label' => "Siswa Dihapus ($deletedDate)",
                        ];
                    } elseif ($info['is_active']) {
                        $results[$nis] = [
                            'status' => 'active',
                            'label' => 'Sudah Aktif',
                        ];
                    } else {
                        $results[$nis] = [
                            'status' => 'inactive',
                            'label' => 'Nonaktif (Akan Diaktifkan)',
                        ];
                    }
                } else {
                    $results[$nis] = [
                        'status' => 'new',
                        'label' => 'Siap Import',
                    ];
                }
            }

            return $this->successResponse(['results' => $results]);
        } catch (\Exception $e) {
            $this->logError('checkNisBatch', $e);
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
                'kelasList' => $this->kelasModel->getListKelas(get_active_tahun_ajaran()),
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
            $byKelas = $this->siswaModel->getCountByKelas(get_active_tahun_ajaran());
            
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
     * - Kelas baru dibuat per tahun ajaran dengan nama yang konsisten
     * - X-AT → XI-AT, XI-MPLB 1 → XII-MPLB 1, dll
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
            $createdKelas = [];

            $fromYear = get_active_tahun_ajaran();

            // Get all active siswa with kelas info from the current year
            $siswaList = $this->siswaModel
                ->select('siswa.*, kelas.tingkat, kelas.jurusan, kelas.nama_kelas, kelas.wali_kelas_id, users.is_active as user_is_active')
                ->join('kelas', 'kelas.id = siswa.kelas_id')
                ->join('users', 'users.id = siswa.user_id')
                ->where('users.is_active', 1)
                ->where('siswa.tahun_ajaran', $fromYear)
                ->findAll();

            // Backup data sebelum perubahan
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

            // Simpan metadata ke rollover_history
            $historyId = $this->rolloverHistoryModel->insert([
                'from_year'      => $fromYear,
                'to_year'        => $newTahunAjaran,
                'total_students' => 0,
                'naik_kelas'     => 0,
                'lulus'          => 0,
                'skipped_count'  => 0,
            ]);
            if (!$historyId) {
                return $this->errorResponse('Gagal menyimpan history rollover.');
            }

            // Simpan backup data
            if (!empty($backup)) {
                if (!$this->rolloverBackupModel->insertBatchForHistory($historyId, $backup)) {
                    return $this->errorResponse('Gagal menyimpan data backup rollover.');
                }
            }

            // Kelompokkan siswa berdasarkan kelas asal (nama_kelas → semua siswa di kelas itu)
            $groups = [];
            foreach ($siswaList as $siswa) {
                $groups[$siswa['kelas_id']][] = $siswa;
            }

            // Proses setiap grup kelas
            foreach ($groups as $sourceKelasId => $groupStudents) {
                $sourceKelas = $groupStudents[0];
                $tingkat = (int) $sourceKelas['tingkat'];

                if ($tingkat >= 12) {
                    // Tingkat 12 → Lulus
                    foreach ($groupStudents as $siswa) {
                        $this->userModel->update($siswa['user_id'], ['is_active' => 0]);
                        $this->siswaModel->update($siswa['id'], [
                            'tahun_ajaran' => $newTahunAjaran,
                        ]);
                        $lulusCount++;
                        $updated[] = "{$siswa['nama_lengkap']} (NIS: {$siswa['nis']}): {$siswa['nama_kelas']} → LULUS";
                    }
                    continue;
                }

                // Hitung nama kelas tujuan untuk tahun berikutnya
                $nextNamaKelas = $this->computeNextKelasName($sourceKelas['nama_kelas']);
                $nextTingkat = (string) ($tingkat + 1);

                // Cari atau buat kelas tujuan di tahun berikutnya
                $targetKelas = $this->kelasModel
                    ->where('nama_kelas', $nextNamaKelas)
                    ->where('tahun_ajaran', $newTahunAjaran)
                    ->first();

                if (!$targetKelas) {
                    // Buat kelas baru untuk tahun berikutnya
                    $this->kelasModel->skipValidation(true);
                    try {
                        $newKelasId = $this->kelasModel->insert([
                            'nama_kelas'    => $nextNamaKelas,
                            'tingkat'       => $nextTingkat,
                            'jurusan'       => $sourceKelas['jurusan'],
                            'tahun_ajaran'  => $newTahunAjaran,
                            'wali_kelas_id' => null, // Wali kelas ditugaskan manual
                        ]);

                        if (!$newKelasId) {
                            throw new \Exception("Gagal membuat kelas $nextNamaKelas");
                        }

                        $targetKelas = $this->kelasModel->find($newKelasId);
                        $createdKelas[] = $nextNamaKelas;
                        $this->logInfo('rolloverTahunAjaran', "Created kelas: $nextNamaKelas (ID: $newKelasId) for $newTahunAjaran");
                    } finally {
                        $this->kelasModel->skipValidation(false);
                    }
                }

                // Pindahkan semua siswa ke kelas tujuan
                foreach ($groupStudents as $siswa) {
                    $this->siswaModel->update($siswa['id'], [
                        'kelas_id'      => $targetKelas['id'],
                        'tahun_ajaran'  => $newTahunAjaran,
                    ]);
                    $naikCount++;
                    $updated[] = "{$siswa['nama_lengkap']} (NIS: {$siswa['nis']}): {$siswa['nama_kelas']} → {$nextNamaKelas}";
                }
            }

            // Update metadata history
            $this->rolloverHistoryModel->update($historyId, [
                'total_students' => $naikCount + $lulusCount,
                'naik_kelas'     => $naikCount,
                'lulus'          => $lulusCount,
                'skipped_count'  => count($skipped),
            ]);

            $this->logInfo('rolloverTahunAjaran', "Naik kelas: $naikCount, Lulus: $lulusCount, Kelas baru: " . implode(', ', $createdKelas));

            return $this->successResponse([
                'history_id'    => $historyId,
                'naik_kelas'    => $naikCount,
                'lulus'         => $lulusCount,
                'skipped'       => $skipped,
                'updated'       => $updated,
                'created_kelas' => $createdKelas,
                'has_backup'    => true,
                'message'       => "Rollover selesai: $naikCount siswa naik kelas, $lulusCount siswa lulus."
                    . (!empty($createdKelas) ? ' ' . count($createdKelas) . ' kelas baru dibuat.' : '')
                    . (!empty($skipped) ? ' ' . count($skipped) . ' siswa dilewati.' : '')
            ]);
        } catch (\Exception $e) {
            $this->logError('rolloverTahunAjaran', $e);
            return $this->errorResponse('Gagal menjalankan rollover: ' . $e->getMessage());
        }
    }

    /**
     * Compute next year's class name from current class name.
     * e.g. X-AT → XI-AT, XI-MPLB 1 → XII-MPLB 1, XII-DKV → LULUS (returns null)
     *
     * @param string $currentName Current class name (e.g. "X-AT", "XI-MPLB 1")
     * @return string|null Next class name or null if graduating (XII → graduation)
     */
    private function computeNextKelasName(string $currentName): string
    {
        $tingkatMap = [
            'X'    => 'XI',
            'XI'   => 'XII',
            'XII'  => null, // Graduation - no next class
        ];

        // Match patterns like "X-AT", "XI-MPLB 1", "XII-DKV 2"
        if (preg_match('/^(X{1,3}|X?I{1,2}V?I{0,3})([\s\-_].+)$/', strtoupper($currentName), $matches)) {
            $roman = $matches[1];
            $suffix = $matches[2];

            if (!isset($tingkatMap[$roman]) || $tingkatMap[$roman] === null) {
                throw new \Exception("Kelas tingkat XII tidak bisa naik kelas");
            }

            return $tingkatMap[$roman] . $suffix;
        }

        // Fallback: try numeric matching (10, 11, 12)
        if (preg_match('/^(10|11|12)([\s\-_].+)$/', $currentName, $matches)) {
            $num = (int) $matches[1];
            $suffix = $matches[2];

            if ($num >= 12) {
                throw new \Exception("Kelas tingkat 12 tidak bisa naik kelas");
            }

            return (string) ($num + 1) . $suffix;
        }

        throw new \Exception("Format nama kelas '$currentName' tidak dikenali");
    }

    public function revertRollover(int $historyId): array
    {
        try {
            // Get history record
            $history = $this->rolloverHistoryModel->find($historyId);
            if (!$history) {
                return $this->errorResponse('Data rollover tidak ditemukan.');
            }

            if ($history['reverted_at'] !== null) {
                return $this->errorResponse('Rollover ini sudah di-revert sebelumnya.');
            }

            // Get backup data for this history (lazy load)
            $changes = $this->rolloverBackupModel->getByHistoryId($historyId);

            if (empty($changes)) {
                return $this->errorResponse('Data backup kosong.');
            }

            $revertCount = 0;
            $errors = [];

            foreach ($changes as $item) {
                try {
                    // Kembalikan data siswa
                    $this->siswaModel->update($item['siswa_id'], [
                        'kelas_id' => $item['old_kelas_id'],
                        'tahun_ajaran' => $item['old_tahun_ajaran'],
                    ]);

                    // Kembalikan status aktif user
                    $this->userModel->update($item['user_id'], [
                        'is_active' => $item['old_is_active'],
                    ]);

                    $revertCount++;
                } catch (\Exception $e) {
                    $errors[] = "Gagal revert siswa ID {$item['siswa_id']}: " . $e->getMessage();
                }
            }

            // Mark history sebagai reverted - harus berhasil sebelum hapus backup
            if (!$this->rolloverHistoryModel->markReverted($historyId)) {
                return $this->errorResponse('Gagal menandai rollover sebagai reverted.');
            }

            // Hapus backup data (cleanup)
            $this->rolloverBackupModel->deleteByHistoryId($historyId);

            $this->logInfo('revertRollover', "Revert sukses: $revertCount siswa dari history #$historyId");

            return $this->successResponse([
                'history_id' => $historyId,
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
            $skippedCount = 0;
            $restoredCount = 0;
            $updatedCount = 0;
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

                    // Check if NIS already exists in database (bypass soft deletes)
                    $existingSiswa = $this->db->table('siswa')
                        ->where('nis', $nis)
                        ->get()
                        ->getRowArray();

                    if ($existingSiswa) {
                        // Find user via raw query to also detect soft-deleted users
                        $existingUser = $this->db->table('users')
                            ->where('id', $existingSiswa['user_id'])
                            ->get()
                            ->getRowArray();

                        if (!is_null($existingSiswa['deleted_at'])) {
                            // SOFT-DELETED student → RESTORE and UPDATE
                            $db->transStart();

                            $kelasId = $this->getKelasIdByName($namaKelas);

                            // Restore siswa via raw query (bypasses allowedFields + soft-delete WHERE filter)
                            $this->db->table('siswa')
                                ->where('id', $existingSiswa['id'])
                                ->update([
                                    'nama_lengkap' => $namaLengkap,
                                    'jenis_kelamin' => $jenisKelamin,
                                    'kelas_id' => $kelasId,
                                    'tahun_ajaran' => $tahunAjaran,
                                    'deleted_at' => null,
                                ]);

                            // Restore user account (whether active or soft-deleted)
                            if ($existingUser) {
                                $userUpdate = [
                                    'password' => password_hash($password, PASSWORD_DEFAULT),
                                    'password_plain' => $password,
                                    'is_active' => 1,
                                    'deleted_at' => null,
                                ];
                                if (!empty($email)) {
                                    $userUpdate['email'] = $email;
                                }
                                // Use raw update to bypass soft delete filter
                                $this->db->table('users')
                                    ->where('id', $existingUser['id'])
                                    ->update($userUpdate);
                            } else {
                                // User record doesn't exist at all — create new one
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
                                    throw new \Exception("Gagal membuat ulang user account");
                                }
                                $this->siswaModel->update($existingSiswa['id'], [
                                    'user_id' => $userId,
                                ]);
                            }

                            $db->transComplete();
                            $restoredCount++;

                            if (!isset($createdClasses[$namaKelas])) {
                                $createdClasses[$namaKelas] = true;
                            }

                        } elseif ($existingUser && $existingUser['is_active'] == 1) {
                            // ACTIVE student with same NIS → UPDATE data
                            $db->transStart();

                            $kelasId = $this->getKelasIdByName($namaKelas);

                            // Update siswa data
                            $this->siswaModel->update($existingSiswa['id'], [
                                'nama_lengkap' => $namaLengkap,
                                'jenis_kelamin' => $jenisKelamin,
                                'kelas_id' => $kelasId,
                                'tahun_ajaran' => $tahunAjaran,
                            ]);

                            // Update user account (password + email)
                            $userUpdate = [
                                'password' => password_hash($password, PASSWORD_DEFAULT),
                                'password_plain' => $password,
                            ];
                            if (!empty($email)) {
                                $userUpdate['email'] = $email;
                            }
                            $this->userModel->update($existingUser['id'], $userUpdate);

                            $db->transComplete();
                            $updatedCount++;

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

            $restoredInfo = $restoredCount > 0 ? " Dipulihkan: $restoredCount," : "";
            $updatedInfo = $updatedCount > 0 ? " Diperbarui: $updatedCount," : "";
            $message = "Import selesai. Berhasil: $successCount,$restoredInfo$updatedInfo Dilewati: $skippedCount, Gagal: $errorCount." . $kelasBaruInfo;

            $this->logInfo('processExcelImport', "Import completed - Success: $successCount, Restored: $restoredCount, Updated: $updatedCount, Skipped: $skippedCount, Failed: $errorCount");

            return $this->successResponse([
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'skipped_count' => $skippedCount,
                'restored_count' => $restoredCount,
                'updated_count' => $updatedCount,
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
     * Get kelas ID by name + tahun_ajaran, create if not exists.
     * Uses caching to avoid N+1 queries during import.
     * 
     * @param string $namaKelas
     * @param string|null $tahunAjaran defaults to active year
     * @return int Kelas ID
     * @throws \Exception
     */
    private function getKelasIdByName(string $namaKelas, ?string $tahunAjaran = null): int
    {
        if (empty($namaKelas) || trim($namaKelas) === '') {
            throw new \Exception("Nama kelas tidak boleh kosong");
        }

        $namaKelas = trim($namaKelas);
        $tahunAjaran = $tahunAjaran ?: get_active_tahun_ajaran();

        if (strlen($namaKelas) > 10) {
            throw new \Exception("Nama kelas '$namaKelas' terlalu panjang (max 10 karakter)");
        }

        $cacheKey = $namaKelas . '|' . $tahunAjaran;
        if (isset($this->kelasCache[$cacheKey])) {
            return $this->kelasCache[$cacheKey];
        }

        $kelas = $this->kelasModel
            ->where('nama_kelas', $namaKelas)
            ->where('tahun_ajaran', $tahunAjaran)
            ->first();

        if ($kelas) {
            $this->kelasCache[$cacheKey] = $kelas['id'];
            return $kelas['id'];
        }

        // Kelas doesn't exist for this year, create new
        $tingkatMap = ['X' => '10', 'XI' => '11', 'XII' => '12'];
        $tingkat = null;
        $jurusan = null;
        $namaKelasUpper = strtoupper($namaKelas);

        if (preg_match('/^(X|XI|XII|10|11|12)[\s\-_](.+)$/', $namaKelasUpper, $matches)) {
            $tingkat = isset($tingkatMap[$matches[1]]) ? $tingkatMap[$matches[1]] : $matches[1];
            $jurusan = trim($matches[2]);
        } else {
            $tingkat = '10';
            $jurusan = $namaKelas;
        }

        if (!in_array($tingkat, ['10', '11', '12'])) {
            throw new \Exception("Tingkat kelas '$namaKelas' tidak valid");
        }

        if (strlen($jurusan) > 50) {
            throw new \Exception("Nama jurusan untuk kelas '$namaKelas' terlalu panjang (max 50 karakter)");
        }

        $this->kelasModel->skipValidation(true);
        try {
            $kelasId = $this->kelasModel->insert([
                'nama_kelas'    => $namaKelas,
                'tingkat'       => $tingkat,
                'jurusan'       => $jurusan,
                'tahun_ajaran'  => $tahunAjaran,
                'wali_kelas_id' => null,
            ]);

            if (!$kelasId) {
                // Race condition: another thread created it
                $kelas = $this->kelasModel
                    ->where('nama_kelas', $namaKelas)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->first();
                if ($kelas) {
                    $this->kelasCache[$cacheKey] = $kelas['id'];
                    return $kelas['id'];
                }
                throw new \Exception("Gagal membuat kelas '$namaKelas'");
            }

            $this->kelasCache[$cacheKey] = $kelasId;
            $this->logInfo('getKelasIdByName', "Created new kelas: $namaKelas for $tahunAjaran (ID: $kelasId)");
            return $kelasId;
        } finally {
            $this->kelasModel->skipValidation(false);
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
