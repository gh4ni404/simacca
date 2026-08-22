<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\MataPelajaranModel;
use App\Models\KelasModel;
use App\Models\UserRoleModel;

/**
 * Guru Service
 * 
 * Handles all business logic related to Guru (Teacher) management:
 * - CRUD operations
 * - User account management
 * - Wali Kelas (Homeroom teacher) assignment
 * - Import/Export functionality
 */
class GuruService extends BaseService
{
    protected $userModel;
    protected $guruModel;
    protected $mapelModel;
    protected $kelasModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->mapelModel = new MataPelajaranModel();
        $this->kelasModel = new KelasModel();
    }

    /**
     * Get all guru with related data
     * 
     * @return array
     */
    public function getAllGuru(): array
    {
        try {
            $guru = $this->guruModel->getAllGuru();
            
            return $this->successResponse($guru);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get all guru: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data guru');
        }
    }

    /**
     * Get guru by ID with related data
     * 
     * @param int $id
     * @return array
     */
    public function getGuruById(int $id): array
    {
        try {
            $guru = $this->guruModel->getGuruWithMapel($id);
            
            if (!$guru) {
                return $this->errorResponse('Guru tidak ditemukan');
            }

            // Get user data
            $user = $this->userModel->find($guru['user_id']);
            
            // Get kelas data if wali kelas
            $kelas = null;
            if ($guru['is_wali_kelas'] && $guru['kelas_id']) {
                $kelas = $this->kelasModel->find($guru['kelas_id']);
            }

            $data = [
                'guru' => $guru,
                'user' => $user,
                'kelas' => $kelas
            ];

            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get guru by ID: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data guru');
        }
    }

    /**
     * Get statistics data
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        try {
            $data = [
                'totalGuru' => $this->guruModel->countAll(),
                'waliKelas' => $this->guruModel->getWaliKelas(),
                'guruNonWali' => $this->guruModel->getGuruNonWali()
            ];

            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get statistics: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil statistik');
        }
    }

    /**
     * Create new guru
     * 
     * @param array $data
     * @return array
     */
    public function createGuru(array $data): array
    {
        $roles = $data['roles'] ?? ($data['role'] ?? ['guru_mapel']);
        if (is_string($roles)) {
            $roles = array_filter(array_map('trim', explode(',', $roles)));
        }
        if (empty($roles)) {
            $roles = ['guru_mapel'];
        }
        $isWaliKelas = in_array('wali_kelas', $roles);
        $isKetuaJurusan = in_array('ketua_jurusan', $roles);

        // Validate input
        $rules = [
            'nip' => 'required|is_unique[guru.nip]',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'username' => 'required|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'email' => 'permit_empty|valid_email',
            'roles' => 'required',
            'mata_pelajaran_id' => 'permit_empty|integer',
        ];

        if ($isWaliKelas) {
            $rules['kelas_id'] = 'required|integer';
        }
        if ($isKetuaJurusan) {
            $rules['jurusan'] = 'required';
        }

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        return $this->executeInTransaction(function () use ($data, $roles, $isWaliKelas, $isKetuaJurusan) {
            $primaryRole = $roles[0];

            // 1. Create user account
            $userData = [
                'username' => $data['username'],
                'password' => $data['password'],
                'role' => $primaryRole,
                'email' => $data['email'] ?? null,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $userId = $this->userModel->insert($userData);

            if (!$userId) {
                throw new \Exception('Gagal membuat akun user');
            }

            // 2. Sync roles to user_roles table
            $userRoleModel = new UserRoleModel();
            $userRoleModel->syncRoles($userId, $roles);

            // 3. Create guru data
            $guruData = [
                'user_id'          => $userId,
                'nip'              => $data['nip'],
                'nama_lengkap'     => $data['nama_lengkap'],
                'jenis_kelamin'    => $data['jenis_kelamin'],
                'mata_pelajaran_id' => $data['mata_pelajaran_id'] ?? null,
                'is_wali_kelas'    => $isWaliKelas ? 1 : 0,
                'kelas_id'         => $isWaliKelas ? ($data['kelas_id'] ?? null) : null,
                'jurusan'          => $isKetuaJurusan ? ($data['jurusan'] ?? null) : null,
                'is_ketua_jurusan' => $isKetuaJurusan ? 1 : 0,
                'created_at'       => date('Y-m-d H:i:s')
            ];

            $guruId = $this->guruModel->insert($guruData);

            if (!$guruId) {
                throw new \Exception('Gagal membuat data guru');
            }

            // 4. If wali kelas, update kelas table
            if ($isWaliKelas && !empty($guruData['kelas_id'])) {
                $this->assignWaliKelas($guruId, $guruData['kelas_id']);
            }

            $this->log('info', "Guru created successfully: {$data['nama_lengkap']} (ID: {$guruId})");

            // 5. Send welcome email if email is provided
            if (!empty($data['email'])) {
                $this->sendWelcomeEmail(
                    $data['email'],
                    $data['username'],
                    $data['password'],
                    $primaryRole,
                    $data['nama_lengkap']
                );
            }

            return [
                'guru_id' => $guruId,
                'user_id' => $userId
            ];
        });
    }

    /**
     * Update guru data
     * 
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateGuru(int $id, array $data): array
    {
        $guru = $this->guruModel->find($id);

        if (!$guru) {
            return $this->errorResponse('Guru tidak ditemukan');
        }

        // Get current user data
        $userData = $this->userModel->find($guru['user_id']);

        $roles = $data['roles'] ?? [];
        if (is_string($roles)) {
            $roles = [$roles];
        }
        $isWaliKelas = in_array('wali_kelas', $roles);
        $isKetuaJurusan = in_array('ketua_jurusan', $roles);

        // Build validation rules
        $rules = [
            'nip' => "required|is_unique[guru.nip,id,{$id}]",
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'email' => 'permit_empty|valid_email',
            'roles' => 'required',
            'mata_pelajaran_id' => 'permit_empty|integer',
        ];

        if ($isWaliKelas) {
            $rules['kelas_id'] = 'required|integer';
        }
        if ($isKetuaJurusan) {
            $rules['jurusan'] = 'required';
        }

        // Check if username changed
        if (isset($data['username']) && $data['username'] != $userData['username']) {
            $rules['username'] = 'required|is_unique[users.username]';
        }

        // Check if password provided
        if (!empty($data['password'])) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        return $this->executeInTransaction(function () use ($id, $guru, $userData, $data, $roles, $isWaliKelas, $isKetuaJurusan) {
            $primaryRole = $roles[0];

            // 1. Update user account
            $userUpdateData = [
                'username' => $data['username'] ?? $userData['username'],
                'role' => $primaryRole,
                'email' => $data['email'] ?? null
            ];

            $plainPassword = null;
            if (!empty($data['password'])) {
                $plainPassword = $data['password'];
                $userUpdateData['password'] = $plainPassword;
                $this->log('info', "Password will be updated for user_id: {$guru['user_id']}");
            }

            // Skip model validation since we already validated
            $this->userModel->skipValidation(true);
            $result = $this->userModel->update($guru['user_id'], $userUpdateData);
            $this->userModel->skipValidation(false);

            if (!$result) {
                $this->log('error', 'Failed to update user. Errors: ' . json_encode($this->userModel->errors()));
                throw new \Exception('Gagal mengupdate data user');
            }

            // 2. Sync roles to user_roles table
            $userRoleModel = new UserRoleModel();
            $userRoleModel->syncRoles($guru['user_id'], $roles);

            // 3. Update guru data
            $guruUpdateData = [
                'nip'              => $data['nip'],
                'nama_lengkap'     => $data['nama_lengkap'],
                'jenis_kelamin'    => $data['jenis_kelamin'],
                'mata_pelajaran_id' => $data['mata_pelajaran_id'] ?? null,
                'is_wali_kelas'    => $isWaliKelas ? 1 : 0,
                'kelas_id'         => $isWaliKelas ? ($data['kelas_id'] ?? null) : null,
                'jurusan'          => $isKetuaJurusan ? ($data['jurusan'] ?? null) : null,
                'is_ketua_jurusan' => $isKetuaJurusan ? 1 : 0,
            ];

            $this->guruModel->update($id, $guruUpdateData);

            // 4. Handle wali kelas assignment
            $this->handleWaliKelasUpdate($id, $guru, [
                'is_wali_kelas' => $isWaliKelas ? 1 : 0,
                'kelas_id' => $guruUpdateData['kelas_id']
            ]);

            // 5. Send email notification if password changed
            if ($plainPassword && !empty($userData['email'])) {
                $this->sendPasswordChangeNotification(
                    $userData['email'],
                    $data['nama_lengkap'],
                    $userUpdateData['username'],
                    $plainPassword
                );
            }

            $this->log('info', "Guru updated successfully: {$data['nama_lengkap']} (ID: {$id})");

            return ['guru_id' => $id];
        });
    }

    /**
     * Delete guru
     * 
     * @param int $id
     * @return array
     */
    public function deleteGuru(int $id): array
    {
        $guru = $this->guruModel->find($id);

        if (!$guru) {
            return $this->errorResponse('Guru tidak ditemukan');
        }

        return $this->executeInTransaction(function () use ($id, $guru) {
            // 1. Reset wali kelas if applicable
            if ($guru['is_wali_kelas'] && $guru['kelas_id']) {
                $this->kelasModel->update($guru['kelas_id'], ['wali_kelas_id' => null]);
            }

            // 2. Delete guru data
            $this->guruModel->delete($id);

            // 3. Delete user account
            $this->userModel->delete($guru['user_id']);

            $this->log('info', "Guru deleted successfully: ID {$id}");

            return ['guru_id' => $id];
        });
    }

    /**
     * Change guru account status (active/inactive)
     * 
     * @param int $id
     * @return array
     */
    public function changeStatus(int $id): array
    {
        $guru = $this->guruModel->find($id);

        if (!$guru) {
            return $this->errorResponse('Guru tidak ditemukan');
        }

        $user = $this->userModel->find($guru['user_id']);

        if (!$user) {
            return $this->errorResponse('User tidak ditemukan');
        }

        try {
            // Toggle status
            $newStatus = $user['is_active'] ? 0 : 1;
            $this->userModel->update($guru['user_id'], ['is_active' => $newStatus]);

            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            $this->log('info', "Guru status changed to {$statusText}: ID {$id}");

            return $this->successResponse(
                ['new_status' => $newStatus],
                "Guru berhasil {$statusText}"
            );
        } catch (\Exception $e) {
            $this->log('error', 'Failed to change guru status: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengubah status guru');
        }
    }

    /**
     * Check if NIP is available
     * 
     * @param string $nip
     * @param int|null $excludeId
     * @return array
     */
    public function checkNipAvailability(string $nip, ?int $excludeId = null): array
    {
        try {
            $query = $this->guruModel->where('nip', $nip);

            if ($excludeId) {
                $query->where('id !=', $excludeId);
            }

            $exists = $query->countAllResults() > 0;

            return $this->successResponse([
                'available' => !$exists,
                'message' => $exists ? 'NIP sudah digunakan' : 'NIP tersedia'
            ]);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to check NIP availability: ' . $e->getMessage());
            return $this->errorResponse('Gagal memeriksa NIP');
        }
    }

    /**
     * Check if username is available
     * 
     * @param string $username
     * @param int|null $excludeUserId
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
            $this->log('error', 'Failed to check username availability: ' . $e->getMessage());
            return $this->errorResponse('Gagal memeriksa username');
        }
    }

    /**
     * Assign wali kelas to a class
     * 
     * @param int $guruId
     * @param int $kelasId
     * @return void
     * @throws \Exception
     */
    protected function assignWaliKelas(int $guruId, int $kelasId): void
    {
        // Check if class already has a wali kelas
        $kelas = $this->kelasModel->find($kelasId);
        
        if ($kelas && $kelas['wali_kelas_id'] && $kelas['wali_kelas_id'] != $guruId) {
            throw new \Exception('Kelas ini sudah memiliki wali kelas');
        }

        $this->kelasModel->update($kelasId, ['wali_kelas_id' => $guruId]);
    }

    /**
     * Handle wali kelas update logic
     * 
     * @param int $guruId
     * @param array $currentGuru
     * @param array $newData
     * @return void
     */
    protected function handleWaliKelasUpdate(int $guruId, array $currentGuru, array $newData): void
    {
        $kelasId = $newData['kelas_id'] ?? null;
        $isWaliKelas = $newData['is_wali_kelas'] ?? 0;

        // Reset previous wali kelas assignment
        if ($currentGuru['is_wali_kelas'] && $currentGuru['kelas_id']) {
            $this->kelasModel->update($currentGuru['kelas_id'], ['wali_kelas_id' => null]);
        }

        // Set new wali kelas assignment
        if ($isWaliKelas && $kelasId) {
            $this->assignWaliKelas($guruId, $kelasId);
        }
    }

    /**
     * Send password change notification email
     * 
     * @param string $email
     * @param string $fullName
     * @param string $username
     * @param string $password
     * @return void
     */
    protected function sendPasswordChangeNotification(
        string $email, 
        string $fullName, 
        string $username, 
        string $password
    ): void {
        helper('email');
        
        $emailSent = send_password_changed_by_admin_notification(
            $email,
            $fullName,
            $username,
            $password
        );
        
        if ($emailSent) {
            $this->log('info', "Password change notification sent to: {$email}");
        } else {
            $this->log('warning', "Failed to send password notification to: {$email}");
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
            $this->log('info', "Welcome email sent to: {$email}");
        } else {
            $this->log('warning', "Failed to send welcome email to: {$email}");
        }
    }

    /**
     * Get lists for dropdowns
     * 
     * @return array
     */
    public function getFormLists(): array
    {
        try {
            $data = [
                'mapelList' => $this->mapelModel->getListMapel(),
                'kelasList' => $this->kelasModel->getListKelas(get_active_tahun_ajaran())
            ];

            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get form lists: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data list');
        }
    }

    /**
     * Diagnostic test for multi-role teachers
     * Checks data integrity, role-attribute synchronization, and simulated session hydration.
     */
    public function runMultiRoleDiagnostic(): array
    {
        $userRoleModel = new UserRoleModel();
        $allGuru = $this->guruModel->getAllGuru();
        $results = [];

        $totalChecked = 0;
        $totalPassed = 0;
        $totalWarnings = 0;

        foreach ($allGuru as $guru) {
            $totalChecked++;
            $userId = $guru['user_id'];
            $userRoles = $userRoleModel->getRolesByUserId($userId);
            if (empty($userRoles)) {
                $userRoles = [$guru['role']];
            }

            $isWaliKelasRole = in_array('wali_kelas', $userRoles);
            $isKetuaJurusanRole = in_array('ketua_jurusan', $userRoles);

            $checks = [];
            $status = 'PASS';

            // 1. Roles Sync Check
            $primaryMatch = ($userRoles[0] === $guru['role']);
            $checks['roles_sync'] = [
                'status' => $primaryMatch ? 'OK' : 'WARN',
                'message' => $primaryMatch 
                    ? "Primary role ({$guru['role']}) synced with user_roles" 
                    : "Primary role mismatch: users.role is '{$guru['role']}', user_roles primary is '{$userRoles[0]}'"
            ];
            if (!$primaryMatch) $status = 'WARN';

            // 2. Wali Kelas Attribute & Academic Year Check
            $activeTA = get_active_tahun_ajaran();
            $kelasActive = $this->kelasModel->getByWaliKelas($guru['id'], $activeTA);

            // Fetch historical classes assigned to this guru across all academic years
            $allKelasAssigned = $this->kelasModel->where('wali_kelas_id', $guru['id'])->orderBy('tahun_ajaran', 'DESC')->findAll();
            $historicalClasses = array_map(function($k) {
                return $k['nama_kelas'] . ' (' . $k['tahun_ajaran'] . ')';
            }, $allKelasAssigned);

            if ($isWaliKelasRole) {
                if ($kelasActive) {
                    $checks['wali_kelas'] = [
                        'status' => 'OK',
                        'message' => "Wali Kelas Aktif TA {$activeTA} -> Kelas: {$kelasActive['nama_kelas']} (ID: {$kelasActive['id']})"
                    ];
                } else {
                    $status = 'WARN';
                    $checks['wali_kelas'] = [
                        'status' => 'WARN',
                        'message' => "Role 'wali_kelas' ada di user_roles, tetapi belum diplotkan pada kelas manapun di TA {$activeTA}" . (!empty($historicalClasses) ? " (Riwayat TA Lalu: " . implode(', ', $historicalClasses) . ")" : "")
                    ];
                }
            } else {
                if (!$kelasActive) {
                    $checks['wali_kelas'] = [
                        'status' => 'OK',
                        'message' => "Tidak mengampu kelas pada TA Aktif {$activeTA}" . (!empty($historicalClasses) ? " (Pernah mengampu: " . implode(', ', $historicalClasses) . ")" : "")
                    ];
                } else {
                    $status = 'WARN';
                    $checks['wali_kelas'] = [
                        'status' => 'WARN',
                        'message' => "Bukan ber-role Wali Kelas di user_roles tetapi terdaftar mengampu kelas {$kelasActive['nama_kelas']} pada TA {$activeTA}"
                    ];
                }
            }

            // 3. Ketua Jurusan Attribute Check
            if ($isKetuaJurusanRole) {
                if ($guru['is_ketua_jurusan'] && !empty($guru['jurusan'])) {
                    $checks['ketua_jurusan'] = [
                        'status' => 'OK',
                        'message' => "Ketua Jurusan role valid -> Terhubung ke Jurusan: {$guru['jurusan']}"
                    ];
                } else {
                    $status = 'WARN';
                    $checks['ketua_jurusan'] = [
                        'status' => 'FAIL',
                        'message' => "Role 'ketua_jurusan' aktif tetapi jurusan kosong/tidak terisi di tabel guru!"
                    ];
                }
            } else {
                if (!$guru['is_ketua_jurusan'] && empty($guru['jurusan'])) {
                    $checks['ketua_jurusan'] = [
                        'status' => 'OK',
                        'message' => "Tidak ber-role Ketua Jurusan (is_ketua_jurusan = 0, jurusan = null)"
                    ];
                } else {
                    $status = 'WARN';
                    $checks['ketua_jurusan'] = [
                        'status' => 'WARN',
                        'message' => "Bukan Ketua Jurusan tetapi is_ketua_jurusan = {$guru['is_ketua_jurusan']} / jurusan = " . ($guru['jurusan'] ?? 'null')
                    ];
                }
            }

            // 4. Simulated Session Hydration & Access Routes
            $accessibleRoutes = [];
            if (in_array('guru_mapel', $userRoles) || in_array('wakakur', $userRoles)) {
                $accessibleRoutes[] = '/guru/dashboard';
                $accessibleRoutes[] = '/guru/absensi';
                $accessibleRoutes[] = '/guru/jurnal';
            }
            if (in_array('wali_kelas', $userRoles) && $kelasActive) {
                $accessibleRoutes[] = '/walikelas/dashboard';
                $accessibleRoutes[] = '/walikelas/siswa';
                $accessibleRoutes[] = '/walikelas/absensi';
            }
            if (in_array('wakakur', $userRoles)) {
                $accessibleRoutes[] = '/wakakur/dashboard';
                $accessibleRoutes[] = '/wakakur/absensi-guru';
            }
            if (in_array('ketua_jurusan', $userRoles)) {
                $accessibleRoutes[] = '/ketua-jurusan/dashboard';
                $accessibleRoutes[] = '/ketua-jurusan/jurnal-pkl';
            }
            if (in_array('kepala_sekolah', $userRoles)) {
                $accessibleRoutes[] = '/kepala-sekolah/dashboard';
            }
            if (in_array('tendik', $userRoles)) {
                $accessibleRoutes[] = '/tendik/dashboard';
            }

            $effectiveRoles = $userRoles;
            if (!$kelasActive) {
                $effectiveRoles = array_values(array_diff($effectiveRoles, ['wali_kelas']));
                if (empty($effectiveRoles)) {
                    $effectiveRoles = [$userRoles[0]];
                }
            }

            $sessionSimulation = [
                'user_id'             => $userId,
                'username'            => $guru['username'],
                'role'                => $effectiveRoles[0] ?? $userRoles[0],
                'all_roles'           => $effectiveRoles,
                'guru_id'             => $guru['id'],
                'nama_lengkap'        => $guru['nama_lengkap'],
                'nip'                 => $guru['nip'],
                'jurusan'             => $guru['jurusan'] ?? null,
                'is_ketua_jurusan'    => (bool)($guru['is_ketua_jurusan'] ?? false),
                'kelas_id'            => $kelasActive['id'] ?? null,
                'tahun_ajaran_aktif'  => $activeTA,
            ];

            if ($status === 'PASS') {
                $totalPassed++;
            } else {
                $totalWarnings++;
            }

            $results[] = [
                'guru_id'           => $guru['id'],
                'user_id'           => $userId,
                'nama_lengkap'      => $guru['nama_lengkap'],
                'nip'               => $guru['nip'],
                'username'          => $guru['username'],
                'roles'             => $userRoles,
                'effective_roles'   => $effectiveRoles,
                'kelas_aktif'       => $kelasActive ? ($kelasActive['nama_kelas'] . ' (' . $activeTA . ')') : null,
                'riwayat_kelas'     => $historicalClasses,
                'is_multi_role'     => count($userRoles) > 1,
                'status'            => $status,
                'checks'            => $checks,
                'accessible_routes' => array_unique($accessibleRoutes),
                'session_hydrated'  => $sessionSimulation
            ];
        }

        return $this->successResponse([
            'summary' => [
                'active_tahun_ajaran' => get_active_tahun_ajaran(),
                'total_guru'          => $totalChecked,
                'total_passed'        => $totalPassed,
                'total_warnings'      => $totalWarnings,
                'multi_role_count'    => count(array_filter($results, fn($r) => $r['is_multi_role']))
            ],
            'diagnostics' => $results
        ]);
    }

    /**
     * Auto-fix legacy multi-role attribute inconsistencies
     */
    public function autoFixInconsistencies(): array
    {
        return $this->executeInTransaction(function () {
            $userRoleModel = new UserRoleModel();
            $allGuru = $this->guruModel->findAll();
            $fixedCount = 0;

            foreach ($allGuru as $guru) {
                $userId = $guru['user_id'];
                $roles = $userRoleModel->getRolesByUserId($userId);
                if (empty($roles)) {
                    $userModel = new UserModel();
                    $u = $userModel->find($userId);
                    if ($u) {
                        $roles = [$u['role']];
                        $userRoleModel->syncRoles($userId, $roles);
                    } else {
                        continue;
                    }
                }

                $isWaliKelas = in_array('wali_kelas', $roles);
                $isKetuaJurusan = in_array('ketua_jurusan', $roles);

                $updateData = [];

                if (!$isWaliKelas && ($guru['is_wali_kelas'] != 0 || $guru['kelas_id'] !== null)) {
                    $updateData['is_wali_kelas'] = 0;
                    $updateData['kelas_id'] = null;
                    if ($guru['kelas_id']) {
                        $this->kelasModel->update($guru['kelas_id'], ['wali_kelas_id' => null]);
                    }
                }

                if (!$isKetuaJurusan && ($guru['is_ketua_jurusan'] != 0 || $guru['jurusan'] !== null)) {
                    $updateData['is_ketua_jurusan'] = 0;
                    $updateData['jurusan'] = null;
                }

                if ($isWaliKelas && $guru['is_wali_kelas'] == 0) {
                    $updateData['is_wali_kelas'] = 1;
                }

                if ($isKetuaJurusan && $guru['is_ketua_jurusan'] == 0) {
                    $updateData['is_ketua_jurusan'] = 1;
                }

                if (!empty($updateData)) {
                    $this->guruModel->update($guru['id'], $updateData);
                    $fixedCount++;
                }
            }

            return ['fixed_count' => $fixedCount];
        });
    }
}
