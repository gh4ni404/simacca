<?php

namespace App\Services;

use App\Models\JurnalKbmModel;
use App\Models\AbsensiModel;
use App\Models\JadwalMengajarModel;

/**
 * JurnalKbmService
 * 
 * Business logic layer for managing jurnal KBM (teaching journal) operations
 * Handles validation, data processing, and complex operations
 */
class JurnalKbmService extends BaseService
{
    protected JurnalKbmModel $jurnalModel;
    protected AbsensiModel $absensiModel;
    protected JadwalMengajarModel $jadwalModel;

    public function __construct()
    {
        parent::__construct();
        $this->jurnalModel = new JurnalKbmModel();
        $this->absensiModel = new AbsensiModel();
        $this->jadwalModel = new JadwalMengajarModel();
    }

    /**
     * Get all jurnal with pagination and filters
     * 
     * @param int $perPage Number of items per page
     * @param array $filters Filters (guru_id, kelas_id, start_date, end_date)
     * @return array
     */
    public function getAllJurnal(int $perPage = 20, array $filters = []): array
    {
        try {
            $builder = $this->jurnalModel
                ->select('jurnal_kbm.*,
                         absensi.tanggal,
                         absensi.pertemuan_ke,
                         absensi.materi_pembelajaran,
                         jadwal_mengajar.jam_mulai,
                         jadwal_mengajar.jam_selesai,
                         guru.nama_lengkap as nama_guru,
                         guru.nip,
                         mata_pelajaran.nama_mapel,
                         kelas.nama_kelas')
                ->join('absensi', 'absensi.id = jurnal_kbm.absensi_id')
                ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
                ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
                ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
                ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id');

            // Apply filters
            if (!empty($filters['guru_id'])) {
                $builder->where('jadwal_mengajar.guru_id', $filters['guru_id']);
            }

            if (!empty($filters['kelas_id'])) {
                $builder->where('jadwal_mengajar.kelas_id', $filters['kelas_id']);
            }

            if (!empty($filters['mapel_id'])) {
                $builder->where('jadwal_mengajar.mata_pelajaran_id', $filters['mapel_id']);
            }

            if (!empty($filters['start_date'])) {
                $builder->where('absensi.tanggal >=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $builder->where('absensi.tanggal <=', $filters['end_date']);
            }

            $builder->orderBy('absensi.tanggal', 'DESC');

            return $this->success([
                'jurnal' => $builder->paginate($perPage),
                'pager' => $this->jurnalModel->pager
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalKbmService::getAllJurnal: ' . $e->getMessage());
            return $this->error('Gagal mengambil data jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Get jurnal by ID with complete details
     * 
     * @param int $id
     * @return array
     */
    public function getJurnalById(int $id): array
    {
        try {
            $jurnal = $this->jurnalModel->getJurnalWithDetail($id);

            if (!$jurnal) {
                return $this->error('Jurnal tidak ditemukan', 404);
            }

            return $this->success($jurnal);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalKbmService::getJurnalById: ' . $e->getMessage());
            return $this->error('Gagal mengambil data jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Get jurnal by absensi ID
     * 
     * @param int $absensiId
     * @return array
     */
    public function getJurnalByAbsensi(int $absensiId): array
    {
        try {
            $jurnal = $this->jurnalModel->getByAbsensi($absensiId);

            if (!$jurnal) {
                return $this->error('Jurnal tidak ditemukan', 404);
            }

            return $this->success($jurnal);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalKbmService::getJurnalByAbsensi: ' . $e->getMessage());
            return $this->error('Gagal mengambil data jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Get jurnal by guru
     * 
     * @param int $guruId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getJurnalByGuru(int $guruId, ?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $jurnal = $this->jurnalModel->getByGuru($guruId, $startDate, $endDate);

            return $this->success($jurnal);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalKbmService::getJurnalByGuru: ' . $e->getMessage());
            return $this->error('Gagal mengambil data jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Get jurnal by kelas
     * 
     * @param int $kelasId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getJurnalByKelas(int $kelasId, ?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $jurnal = $this->jurnalModel->getByKelas($kelasId, $startDate, $endDate);

            return $this->success($jurnal);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalKbmService::getJurnalByKelas: ' . $e->getMessage());
            return $this->error('Gagal mengambil data jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Get jurnal by guru and kelas (security-safe version)
     * 
     * @param int $guruId
     * @param int $kelasId
     * @return array
     */
    public function getJurnalByGuruAndKelas(int $guruId, int $kelasId): array
    {
        try {
            $jurnal = $this->jurnalModel->getByGuruAndKelas($guruId, $kelasId);

            return $this->success($jurnal);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalKbmService::getJurnalByGuruAndKelas: ' . $e->getMessage());
            return $this->error('Gagal mengambil data jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Create new jurnal
     * 
     * @param array $data
     * @return array
     */
    public function createJurnal(array $data): array
    {
        try {
            $this->db->transStart();

            // Validate required fields
            if (empty($data['absensi_id'])) {
                return $this->error('Absensi ID wajib diisi');
            }

            if (empty($data['kegiatan_pembelajaran'])) {
                return $this->error('Kegiatan pembelajaran wajib diisi');
            }

            // Check if absensi exists
            $absensi = $this->absensiModel->find($data['absensi_id']);
            if (!$absensi) {
                return $this->error('Data absensi tidak ditemukan');
            }

            // Check if jurnal already exists for this absensi
            if ($this->jurnalModel->isJurnalExist($data['absensi_id'])) {
                return $this->error('Jurnal untuk absensi ini sudah ada');
            }

            // Set created_at if not provided
            if (!isset($data['created_at'])) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }

            $jurnalId = $this->jurnalModel->insert($data);

            if (!$jurnalId) {
                $this->db->transRollback();
                $errors = $this->jurnalModel->errors();
                return $this->error('Gagal membuat jurnal: ' . implode(', ', $errors));
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal membuat jurnal');
            }

            return $this->success([
                'id' => $jurnalId,
                'message' => 'Jurnal berhasil dibuat'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in JurnalKbmService::createJurnal: ' . $e->getMessage());
            return $this->error('Gagal membuat jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Update jurnal
     * 
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateJurnal(int $id, array $data): array
    {
        try {
            $this->db->transStart();

            // Check if jurnal exists
            $jurnal = $this->jurnalModel->find($id);
            if (!$jurnal) {
                return $this->error('Jurnal tidak ditemukan', 404);
            }

            // If absensi_id is being updated, validate it
            if (isset($data['absensi_id']) && $data['absensi_id'] != $jurnal['absensi_id']) {
                $absensi = $this->absensiModel->find($data['absensi_id']);
                if (!$absensi) {
                    return $this->error('Data absensi tidak ditemukan');
                }

                // Check if jurnal already exists for new absensi
                if ($this->jurnalModel->isJurnalExist($data['absensi_id'])) {
                    return $this->error('Jurnal untuk absensi ini sudah ada');
                }
            }

            $success = $this->jurnalModel->update($id, $data);

            if (!$success) {
                $this->db->transRollback();
                $errors = $this->jurnalModel->errors();
                return $this->error('Gagal mengupdate jurnal: ' . implode(', ', $errors));
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal mengupdate jurnal');
            }

            return $this->success([
                'id' => $id,
                'message' => 'Jurnal berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in JurnalKbmService::updateJurnal: ' . $e->getMessage());
            return $this->error('Gagal mengupdate jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Delete jurnal
     * 
     * @param int $id
     * @return array
     */
    public function deleteJurnal(int $id): array
    {
        try {
            $this->db->transStart();

            // Check if jurnal exists
            $jurnal = $this->jurnalModel->find($id);
            if (!$jurnal) {
                return $this->error('Jurnal tidak ditemukan', 404);
            }

            // Delete foto dokumentasi if exists
            if (!empty($jurnal['foto_dokumentasi'])) {
                $fotoPath = WRITEPATH . 'uploads/' . $jurnal['foto_dokumentasi'];
                if (file_exists($fotoPath)) {
                    @unlink($fotoPath);
                }
            }

            $success = $this->jurnalModel->delete($id);

            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal menghapus jurnal');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal menghapus jurnal');
            }

            return $this->success([
                'message' => 'Jurnal berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in JurnalKbmService::deleteJurnal: ' . $e->getMessage());
            return $this->error('Gagal menghapus jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Upload foto dokumentasi
     * 
     * @param int $jurnalId
     * @param mixed $file Uploaded file
     * @return array
     */
    public function uploadFotoDokumentasi(int $jurnalId, $file): array
    {
        try {
            // Check if jurnal exists
            $jurnal = $this->jurnalModel->find($jurnalId);
            if (!$jurnal) {
                return $this->error('Jurnal tidak ditemukan', 404);
            }

            // Validate file
            if (!$file->isValid()) {
                return $this->error('File tidak valid');
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return $this->error('Tipe file tidak didukung. Hanya JPG, JPEG, dan PNG yang diperbolehkan');
            }

            // Validate file size (max 1MB)
            if ($file->getSize() > 1048576) {
                return $this->error('Ukuran file terlalu besar. Maksimal 1MB');
            }

            // Delete old foto if exists
            if (!empty($jurnal['foto_dokumentasi'])) {
                $oldFotoPath = WRITEPATH . 'uploads/' . $jurnal['foto_dokumentasi'];
                if (file_exists($oldFotoPath)) {
                    @unlink($oldFotoPath);
                }
            }

            // Generate new filename
            $newName = 'jurnal_' . $jurnalId . '_' . time() . '.' . $file->getExtension();

            // Move file to uploads directory
            $file->move(WRITEPATH . 'uploads', $newName);

            // Optimize image server-side (safety net after client-side compression)
            helper('image');
            $filePath = WRITEPATH . 'uploads/' . $newName;
            if (file_exists($filePath)) {
                $originalSize = filesize($filePath);
                optimize_jurnal_photo($filePath, $filePath);
                if (file_exists($filePath)) {
                    $newSize = filesize($filePath);
                    $savings = round((($originalSize - $newSize) / $originalSize) * 100, 2);
                    log_message('info', "Jurnal photo optimized in service: {$newName} - {$savings}% smaller");
                }
            }

            // Update jurnal with new foto
            $updateResult = $this->updateJurnal($jurnalId, ['foto_dokumentasi' => $newName]);

            if (!$updateResult['success']) {
                // Rollback: delete uploaded file
                @unlink(WRITEPATH . 'uploads/' . $newName);
                return $updateResult;
            }

            return $this->success([
                'filename' => $newName,
                'message' => 'Foto dokumentasi berhasil diupload'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalKbmService::uploadFotoDokumentasi: ' . $e->getMessage());
            return $this->error('Gagal mengupload foto: ' . $e->getMessage());
        }
    }

    /**
     * Delete foto dokumentasi
     * 
     * @param int $jurnalId
     * @return array
     */
    public function deleteFotoDokumentasi(int $jurnalId): array
    {
        try {
            // Check if jurnal exists
            $jurnal = $this->jurnalModel->find($jurnalId);
            if (!$jurnal) {
                return $this->error('Jurnal tidak ditemukan', 404);
            }

            if (empty($jurnal['foto_dokumentasi'])) {
                return $this->error('Tidak ada foto dokumentasi untuk dihapus');
            }

            // Delete file
            $fotoPath = WRITEPATH . 'uploads/' . $jurnal['foto_dokumentasi'];
            if (file_exists($fotoPath)) {
                @unlink($fotoPath);
            }

            // Update jurnal
            return $this->updateJurnal($jurnalId, ['foto_dokumentasi' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalKbmService::deleteFotoDokumentasi: ' . $e->getMessage());
            return $this->error('Gagal menghapus foto: ' . $e->getMessage());
        }
    }

    /**
     * Get jurnal statistics
     * 
     * @param array $filters
     * @return array
     */
    public function getJurnalStatistics(array $filters = []): array
    {
        try {
            $builder = $this->db->table('jurnal_kbm')
                ->join('absensi', 'absensi.id = jurnal_kbm.absensi_id')
                ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id');

            // Apply filters
            if (!empty($filters['guru_id'])) {
                $builder->where('jadwal_mengajar.guru_id', $filters['guru_id']);
            }

            if (!empty($filters['kelas_id'])) {
                $builder->where('jadwal_mengajar.kelas_id', $filters['kelas_id']);
            }

            if (!empty($filters['start_date'])) {
                $builder->where('absensi.tanggal >=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $builder->where('absensi.tanggal <=', $filters['end_date']);
            }

            $statistics = [
                'total_jurnal' => $builder->countAllResults(false),
                'jurnal_dengan_foto' => $builder->where('jurnal_kbm.foto_dokumentasi IS NOT NULL')->countAllResults()
            ];

            return $this->success($statistics);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalKbmService::getJurnalStatistics: ' . $e->getMessage());
            return $this->error('Gagal mengambil statistik jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Check if jurnal exists for absensi
     * 
     * @param int $absensiId
     * @return bool
     */
    public function isJurnalExist(int $absensiId): bool
    {
        return $this->jurnalModel->isJurnalExist($absensiId);
    }
}
