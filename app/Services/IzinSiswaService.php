<?php

namespace App\Services;

use App\Models\IzinSiswaModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\UserModel;

/**
 * IzinSiswaService
 * 
 * Business logic layer for managing izin siswa (student leave/permission) operations
 * Handles validation, data processing, and approval workflows
 */
class IzinSiswaService extends BaseService
{
    protected IzinSiswaModel $izinModel;
    protected SiswaModel $siswaModel;
    protected KelasModel $kelasModel;
    protected UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->izinModel = new IzinSiswaModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->userModel = new UserModel();
    }

    /**
     * Get all izin with pagination and filters
     * 
     * @param int $perPage Number of items per page
     * @param array $filters Filters (siswa_id, kelas_id, status, start_date, end_date)
     * @return array
     */
    public function getAllIzin(int $perPage = 20, array $filters = []): array
    {
        try {
            $builder = $this->izinModel
                ->select('izin_siswa.*,
                         siswa.nama_lengkap,
                         siswa.nis,
                         kelas.nama_kelas,
                         users.username as disetujui_oleh_nama')
                ->join('siswa', 'siswa.id = izin_siswa.siswa_id AND siswa.deleted_at IS NULL', 'left')
                ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
                ->join('users', 'users.id = izin_siswa.disetujui_oleh', 'left');

            // Apply filters
            if (!empty($filters['siswa_id'])) {
                $builder->where('izin_siswa.siswa_id', $filters['siswa_id']);
            }

            if (!empty($filters['kelas_id'])) {
                $builder->where('siswa.kelas_id', $filters['kelas_id']);
            }

            if (!empty($filters['status'])) {
                $builder->where('izin_siswa.status', $filters['status']);
            }

            if (!empty($filters['jenis_izin'])) {
                $builder->where('izin_siswa.jenis_izin', $filters['jenis_izin']);
            }

            if (!empty($filters['start_date'])) {
                $builder->where('izin_siswa.tanggal >=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $builder->where('izin_siswa.tanggal <=', $filters['end_date']);
            }

            $builder->orderBy('izin_siswa.tanggal', 'DESC')
                ->orderBy('izin_siswa.status', 'ASC');

            return $this->success([
                'izin' => $builder->paginate($perPage),
                'pager' => $this->izinModel->pager
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in IzinSiswaService::getAllIzin: ' . $e->getMessage());
            return $this->error('Gagal mengambil data izin: ' . $e->getMessage());
        }
    }

    /**
     * Get izin by ID
     * 
     * @param int $id
     * @return array
     */
    public function getIzinById(int $id): array
    {
        try {
            $izin = $this->db->table('izin_siswa')
                ->select('izin_siswa.*,
                         siswa.nama_lengkap,
                         siswa.nis,
                         kelas.nama_kelas,
                         users.username as disetujui_oleh_nama')
                ->join('siswa', 'siswa.id = izin_siswa.siswa_id AND siswa.deleted_at IS NULL', 'left')
                ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
                ->join('users', 'users.id = izin_siswa.disetujui_oleh', 'left')
                ->where('izin_siswa.id', $id)
                ->get()
                ->getRowArray();

            if (!$izin) {
                return $this->error('Izin tidak ditemukan', 404);
            }

            return $this->success($izin);
        } catch (\Exception $e) {
            log_message('error', 'Error in IzinSiswaService::getIzinById: ' . $e->getMessage());
            return $this->error('Gagal mengambil data izin: ' . $e->getMessage());
        }
    }

    /**
     * Get izin by siswa
     * 
     * @param int $siswaId
     * @return array
     */
    public function getIzinBySiswa(int $siswaId): array
    {
        try {
            $izin = $this->izinModel->getbySiswa($siswaId);

            return $this->success($izin);
        } catch (\Exception $e) {
            log_message('error', 'Error in IzinSiswaService::getIzinBySiswa: ' . $e->getMessage());
            return $this->error('Gagal mengambil data izin: ' . $e->getMessage());
        }
    }

    /**
     * Get izin by kelas
     * 
     * @param int $kelasId
     * @param string|null $status
     * @return array
     */
    public function getIzinByKelas(int $kelasId, ?string $status = null): array
    {
        try {
            $izin = $this->izinModel->getByKelas($kelasId, $status);

            return $this->success($izin);
        } catch (\Exception $e) {
            log_message('error', 'Error in IzinSiswaService::getIzinByKelas: ' . $e->getMessage());
            return $this->error('Gagal mengambil data izin: ' . $e->getMessage());
        }
    }

    /**
     * Get pending approval izin for wali kelas
     * 
     * @param int $kelasId
     * @return array
     */
    public function getPendingApproval(int $kelasId): array
    {
        try {
            $izin = $this->izinModel->getPendingApproval($kelasId);

            return $this->success($izin);
        } catch (\Exception $e) {
            log_message('error', 'Error in IzinSiswaService::getPendingApproval: ' . $e->getMessage());
            return $this->error('Gagal mengambil data izin: ' . $e->getMessage());
        }
    }

    /**
     * Create new izin
     * 
     * @param array $data
     * @return array
     */
    public function createIzin(array $data): array
    {
        try {
            $this->db->transStart();

            // Validate required fields
            if (empty($data['siswa_id'])) {
                return $this->error('Siswa ID wajib diisi');
            }

            if (empty($data['tanggal'])) {
                return $this->error('Tanggal wajib diisi');
            }

            if (empty($data['jenis_izin'])) {
                return $this->error('Jenis izin wajib dipilih');
            }

            if (empty($data['alasan'])) {
                return $this->error('Alasan wajib diisi');
            }

            // Check if siswa exists
            $siswa = $this->siswaModel->find($data['siswa_id']);
            if (!$siswa) {
                return $this->error('Data siswa tidak ditemukan');
            }

            // Check if izin already exists for this date
            if ($this->izinModel->isIzinExist($data['siswa_id'], $data['tanggal'])) {
                return $this->error('Izin untuk tanggal ini sudah ada');
            }

            // Normalize jenis_izin
            $data['jenis_izin'] = ucfirst(strtolower($data['jenis_izin']));

            // Set default status if not provided
            if (!isset($data['status'])) {
                $data['status'] = 'pending';
            }

            $izinId = $this->izinModel->insert($data);

            if (!$izinId) {
                $this->db->transRollback();
                $errors = $this->izinModel->errors();
                return $this->error('Gagal membuat izin: ' . implode(', ', $errors));
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal membuat izin');
            }

            return $this->success([
                'id' => $izinId,
                'message' => 'Izin berhasil diajukan'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in IzinSiswaService::createIzin: ' . $e->getMessage());
            return $this->error('Gagal membuat izin: ' . $e->getMessage());
        }
    }

    /**
     * Update izin
     * 
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateIzin(int $id, array $data): array
    {
        try {
            $this->db->transStart();

            // Check if izin exists
            $izin = $this->izinModel->find($id);
            if (!$izin) {
                return $this->error('Izin tidak ditemukan', 404);
            }

            // Don't allow updating approved/rejected izin
            if ($izin['status'] != 'pending' && !isset($data['status'])) {
                return $this->error('Izin yang sudah disetujui/ditolak tidak dapat diubah');
            }

            // If tanggal is being updated, check for duplicates
            if (isset($data['tanggal']) && $data['tanggal'] != $izin['tanggal']) {
                if ($this->izinModel->isIzinExist($izin['siswa_id'], $data['tanggal'])) {
                    return $this->error('Izin untuk tanggal ini sudah ada');
                }
            }

            // Normalize jenis_izin if provided
            if (isset($data['jenis_izin'])) {
                $data['jenis_izin'] = ucfirst(strtolower($data['jenis_izin']));
            }

            $success = $this->izinModel->update($id, $data);

            if (!$success) {
                $this->db->transRollback();
                $errors = $this->izinModel->errors();
                return $this->error('Gagal mengupdate izin: ' . implode(', ', $errors));
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal mengupdate izin');
            }

            return $this->success([
                'id' => $id,
                'message' => 'Izin berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in IzinSiswaService::updateIzin: ' . $e->getMessage());
            return $this->error('Gagal mengupdate izin: ' . $e->getMessage());
        }
    }

    /**
     * Delete izin
     * 
     * @param int $id
     * @return array
     */
    public function deleteIzin(int $id): array
    {
        try {
            $this->db->transStart();

            // Check if izin exists
            $izin = $this->izinModel->find($id);
            if (!$izin) {
                return $this->error('Izin tidak ditemukan', 404);
            }

            // Delete berkas file if exists
            if (!empty($izin['berkas'])) {
                $berkasPath = WRITEPATH . 'uploads/' . $izin['berkas'];
                if (file_exists($berkasPath)) {
                    @unlink($berkasPath);
                }
            }

            $success = $this->izinModel->delete($id);

            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal menghapus izin');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal menghapus izin');
            }

            return $this->success([
                'message' => 'Izin berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in IzinSiswaService::deleteIzin: ' . $e->getMessage());
            return $this->error('Gagal menghapus izin: ' . $e->getMessage());
        }
    }

    /**
     * Approve izin
     * 
     * @param int $izinId
     * @param int $userId User who approves
     * @param string|null $catatan Optional notes
     * @return array
     */
    public function approveIzin(int $izinId, int $userId, ?string $catatan = null): array
    {
        try {
            $this->db->transStart();

            // Check if izin exists
            $izin = $this->izinModel->find($izinId);
            if (!$izin) {
                return $this->error('Izin tidak ditemukan', 404);
            }

            // Check if already approved/rejected
            if ($izin['status'] != 'pending') {
                return $this->error('Izin sudah diproses sebelumnya');
            }

            // Check if user exists
            $user = $this->userModel->find($userId);
            if (!$user) {
                return $this->error('User tidak ditemukan');
            }

            $success = $this->izinModel->approveIzin($izinId, $userId, $catatan);

            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal menyetujui izin');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal menyetujui izin');
            }

            return $this->success([
                'id' => $izinId,
                'message' => 'Izin berhasil disetujui'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in IzinSiswaService::approveIzin: ' . $e->getMessage());
            return $this->error('Gagal menyetujui izin: ' . $e->getMessage());
        }
    }

    /**
     * Reject izin
     * 
     * @param int $izinId
     * @param int $userId User who rejects
     * @param string|null $catatan Optional notes
     * @return array
     */
    public function rejectIzin(int $izinId, int $userId, ?string $catatan = null): array
    {
        try {
            $this->db->transStart();

            // Check if izin exists
            $izin = $this->izinModel->find($izinId);
            if (!$izin) {
                return $this->error('Izin tidak ditemukan', 404);
            }

            // Check if already approved/rejected
            if ($izin['status'] != 'pending') {
                return $this->error('Izin sudah diproses sebelumnya');
            }

            // Check if user exists
            $user = $this->userModel->find($userId);
            if (!$user) {
                return $this->error('User tidak ditemukan');
            }

            $success = $this->izinModel->rejectIzin($izinId, $userId, $catatan);

            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal menolak izin');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal menolak izin');
            }

            return $this->success([
                'id' => $izinId,
                'message' => 'Izin berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in IzinSiswaService::rejectIzin: ' . $e->getMessage());
            return $this->error('Gagal menolak izin: ' . $e->getMessage());
        }
    }

    /**
     * Upload berkas izin
     * 
     * @param int $izinId
     * @param mixed $file Uploaded file
     * @return array
     */
    public function uploadBerkas(int $izinId, $file): array
    {
        try {
            // Check if izin exists
            $izin = $this->izinModel->find($izinId);
            if (!$izin) {
                return $this->error('Izin tidak ditemukan', 404);
            }

            // Validate file
            if (!$file->isValid()) {
                return $this->error('File tidak valid');
            }

            // Validate file type (images and PDF)
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return $this->error('Tipe file tidak didukung. Hanya JPG, PNG, dan PDF yang diperbolehkan');
            }

            // Validate file size (max 1MB)
            if ($file->getSize() > 1048576) {
                return $this->error('Ukuran file terlalu besar. Maksimal 1MB');
            }

            // Delete old berkas if exists
            if (!empty($izin['berkas'])) {
                $oldBerkasPath = WRITEPATH . 'uploads/' . $izin['berkas'];
                if (file_exists($oldBerkasPath)) {
                    @unlink($oldBerkasPath);
                }
            }

            // Generate new filename
            $newName = 'izin_' . $izinId . '_' . time() . '.' . $file->getExtension();

            // Move file to uploads directory
            $file->move(WRITEPATH . 'uploads', $newName);

            // Update izin with new berkas
            $updateResult = $this->updateIzin($izinId, ['berkas' => $newName]);

            if (!$updateResult['success']) {
                // Rollback: delete uploaded file
                @unlink(WRITEPATH . 'uploads/' . $newName);
                return $updateResult;
            }

            return $this->success([
                'filename' => $newName,
                'message' => 'Berkas berhasil diupload'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in IzinSiswaService::uploadBerkas: ' . $e->getMessage());
            return $this->error('Gagal mengupload berkas: ' . $e->getMessage());
        }
    }

    /**
     * Get izin statistics
     * 
     * @param array $filters
     * @return array
     */
    public function getIzinStatistics(array $filters = []): array
    {
        try {
            $builder = $this->db->table('izin_siswa')
                ->join('siswa', 'siswa.id = izin_siswa.siswa_id AND siswa.deleted_at IS NULL');

            // Apply filters
            if (!empty($filters['siswa_id'])) {
                $builder->where('izin_siswa.siswa_id', $filters['siswa_id']);
            }

            if (!empty($filters['kelas_id'])) {
                $builder->where('siswa.kelas_id', $filters['kelas_id']);
            }

            if (!empty($filters['start_date'])) {
                $builder->where('izin_siswa.tanggal >=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $builder->where('izin_siswa.tanggal <=', $filters['end_date']);
            }

            // Count total first
            $statistics = [
                'total_izin' => $builder->countAllResults(false)
            ];
            
            // Reset and count each status separately
            $baseBuilder = $this->db->table('izin_siswa')
                ->join('siswa', 'siswa.id = izin_siswa.siswa_id AND siswa.deleted_at IS NULL');
            
            if (!empty($filters['siswa_id'])) {
                $baseBuilder->where('izin_siswa.siswa_id', $filters['siswa_id']);
            }
            if (!empty($filters['kelas_id'])) {
                $baseBuilder->where('siswa.kelas_id', $filters['kelas_id']);
            }
            if (!empty($filters['start_date'])) {
                $baseBuilder->where('izin_siswa.tanggal >=', $filters['start_date']);
            }
            if (!empty($filters['end_date'])) {
                $baseBuilder->where('izin_siswa.tanggal <=', $filters['end_date']);
            }
            
            $statistics['pending'] = (clone $baseBuilder)->where('izin_siswa.status', 'pending')->countAllResults();
            $statistics['disetujui'] = (clone $baseBuilder)->where('izin_siswa.status', 'disetujui')->countAllResults();
            $statistics['ditolak'] = (clone $baseBuilder)->where('izin_siswa.status', 'ditolak')->countAllResults();

            // Reset builder for jenis_izin stats
            $builder = $this->db->table('izin_siswa')
                ->join('siswa', 'siswa.id = izin_siswa.siswa_id AND siswa.deleted_at IS NULL');

            if (!empty($filters['siswa_id'])) {
                $builder->where('izin_siswa.siswa_id', $filters['siswa_id']);
            }

            if (!empty($filters['kelas_id'])) {
                $builder->where('siswa.kelas_id', $filters['kelas_id']);
            }

            if (!empty($filters['start_date'])) {
                $builder->where('izin_siswa.tanggal >=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $builder->where('izin_siswa.tanggal <=', $filters['end_date']);
            }

            $jenisStats = $builder->select('jenis_izin, COUNT(*) as total')
                ->groupBy('jenis_izin')
                ->get()
                ->getResultArray();

            $statistics['per_jenis'] = [];
            foreach ($jenisStats as $stat) {
                $statistics['per_jenis'][$stat['jenis_izin']] = $stat['total'];
            }

            return $this->success($statistics);
        } catch (\Exception $e) {
            log_message('error', 'Error in IzinSiswaService::getIzinStatistics: ' . $e->getMessage());
            return $this->error('Gagal mengambil statistik izin: ' . $e->getMessage());
        }
    }

    /**
     * Get approved izin by date and kelas
     * 
     * @param string $tanggal
     * @param int|null $kelasId
     * @return array
     */
    public function getApprovedIzinByDate(string $tanggal, ?int $kelasId = null): array
    {
        try {
            $izin = $this->izinModel->getApprovedIzinByDate($tanggal, $kelasId);

            return $this->success($izin);
        } catch (\Exception $e) {
            log_message('error', 'Error in IzinSiswaService::getApprovedIzinByDate: ' . $e->getMessage());
            return $this->error('Gagal mengambil data izin: ' . $e->getMessage());
        }
    }
}
