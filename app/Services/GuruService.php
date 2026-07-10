<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\MataPelajaranModel;
use App\Models\KelasModel;

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
        // Validate input
        $rules = [
            'nip' => 'required|is_unique[guru.nip]',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'username' => 'required|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'email' => 'permit_empty|valid_email',
            'role' => 'required|in_list[guru_mapel,wali_kelas,wakakur]',
            'mata_pelajaran_id' => 'permit_empty|integer',
            'kelas_id' => 'permit_empty|integer',
            'is_wali_kelas' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        return $this->executeInTransaction(function () use ($data) {
            // 1. Create user account
            $userData = [
                'username' => $data['username'],
                'password' => $data['password'],
                'role' => $data['role'],
                'email' => $data['email'] ?? null,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $userId = $this->userModel->insert($userData);

            if (!$userId) {
                throw new \Exception('Gagal membuat akun user');
            }

            // 2. Create guru data
            $guruData = [
                'user_id' => $userId,
                'nip' => $data['nip'],
                'nama_lengkap' => $data['nama_lengkap'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'mata_pelajaran_id' => $data['mata_pelajaran_id'] ?? null,
                'is_wali_kelas' => $data['is_wali_kelas'] ?? 0,
                'kelas_id' => $data['kelas_id'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $guruId = $this->guruModel->insert($guruData);

            if (!$guruId) {
                throw new \Exception('Gagal membuat data guru');
            }

            // 3. If wali kelas, update kelas table
            if (!empty($data['is_wali_kelas']) && !empty($data['kelas_id'])) {
                $this->assignWaliKelas($guruId, $data['kelas_id']);
            }

            $this->log('info', "Guru created successfully: {$data['nama_lengkap']} (ID: {$guruId})");

            // 4. Send welcome email if email is provided
            if (!empty($data['email'])) {
                $this->sendWelcomeEmail(
                    $data['email'],
                    $data['username'],
                    $data['password'],
                    $data['role'],
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

        // Build validation rules
        $rules = [
            'nip' => "required|is_unique[guru.nip,id,{$id}]",
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'email' => 'permit_empty|valid_email',
            'role' => 'required|in_list[guru_mapel,wali_kelas,wakakur]',
            'mata_pelajaran_id' => 'permit_empty|integer',
            'kelas_id' => 'permit_empty|integer',
            'is_wali_kelas' => 'permit_empty|in_list[0,1]'
        ];

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

        return $this->executeInTransaction(function () use ($id, $guru, $userData, $data) {
            // 1. Update user account
            $userUpdateData = [
                'username' => $data['username'] ?? $userData['username'],
                'role' => $data['role'],
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

            // 2. Update guru data
            $guruUpdateData = [
                'nip' => $data['nip'],
                'nama_lengkap' => $data['nama_lengkap'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'mata_pelajaran_id' => $data['mata_pelajaran_id'] ?? null,
                'is_wali_kelas' => $data['is_wali_kelas'] ?? 0,
                'kelas_id' => $data['kelas_id'] ?? null
            ];

            $this->guruModel->update($id, $guruUpdateData);

            // 3. Handle wali kelas assignment
            $this->handleWaliKelasUpdate($id, $guru, $data);

            // 4. Send email notification if password changed
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
                'kelasList' => $this->kelasModel->getListKelas()
            ];

            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get form lists: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data list');
        }
    }
}
