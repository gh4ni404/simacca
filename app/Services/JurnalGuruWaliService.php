<?php

namespace App\Services;

use App\Models\JurnalGuruWaliModel;
use App\Models\GuruWaliSiswaModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\SettingModel;
use Config\Database;

class JurnalGuruWaliService
{
    protected $jurnalModel;
    protected $guruWaliModel;
    protected $guruModel;
    protected $siswaModel;
    protected $settingModel;
    protected $db;

    public function __construct()
    {
        $this->jurnalModel     = new JurnalGuruWaliModel();
        $this->guruWaliModel   = new GuruWaliSiswaModel();
        $this->guruModel       = new GuruModel();
        $this->siswaModel      = new SiswaModel();
        $this->settingModel    = new SettingModel();
        $this->db              = Database::connect();
    }

    /**
     * Get all active mentee students assigned to this teacher
     */
    public function getSiswaBinaan(int $guruId): array
    {
        return $this->guruWaliModel->getSiswaByGuru($guruId);
    }

    /**
     * Get journal entries for this teacher with optional filters
     */
    public function getJurnalList(int $guruId, array $filters = []): array
    {
        return $this->jurnalModel->getJurnalByGuru($guruId, $filters);
    }

    /**
     * Handle upload & optimize foto dokumentasi
     */
    public function handleUploadFoto($file): ?string
    {
        if (!$file || !$file->isValid() || $file->hasMoved() || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        helper(['security', 'image']);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        $validation   = validate_file_upload($file, $allowedTypes, 2097152); // 2MB

        if (!$validation['valid']) {
            throw new \RuntimeException($validation['error']);
        }

        $uploadPath = WRITEPATH . 'uploads/jurnal_wali';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $fotoName = $file->getRandomName();
        $file->move($uploadPath, $fotoName);

        $filePath = $uploadPath . '/' . $fotoName;
        if (file_exists($filePath)) {
            optimize_jurnal_photo($filePath, $filePath);
        }

        return $fotoName;
    }

    /**
     * Safely delete physical photo file
     */
    private function removeFotoFile(?string $filename): void
    {
        if (!empty($filename)) {
            $path = WRITEPATH . 'uploads/jurnal_wali/' . basename($filename);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    /**
     * Create a new journal entry
     */
    public function createJurnal(array $data, $file = null): array
    {
        $this->db->transBegin();

        try {
            $guruId = (int) ($data['guru_id'] ?? 0);
            $siswaId = (int) ($data['siswa_id'] ?? 0);

            if (!$guruId || !$siswaId) {
                return ['success' => false, 'message' => 'Data Guru dan Siswa wajib diisi.'];
            }

            if (empty($data['tanggal'])) {
                return ['success' => false, 'message' => 'Tanggal kegiatan wajib diisi.'];
            }

            if (empty($data['catatan'])) {
                return ['success' => false, 'message' => 'Catatan bimbingan wajib diisi.'];
            }

            $fotoName = null;
            if ($file && $file->isValid()) {
                $fotoName = $this->handleUploadFoto($file);
            }

            $insertData = [
                'guru_id'          => $guruId,
                'siswa_id'         => $siswaId,
                'tahun_ajaran'     => $data['tahun_ajaran'] ?? get_active_tahun_ajaran(),
                'tanggal'          => $data['tanggal'],
                'jenis_bimbingan'  => $data['jenis_bimbingan'] ?? 'Akademik',
                'catatan'          => $data['catatan'],
                'tindak_lanjut'    => $data['tindak_lanjut'] ?? null,
                'foto_dokumentasi' => $fotoName,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            $insertId = $this->jurnalModel->insert($insertData);
            if (!$insertId) {
                $this->db->transRollback();
                return ['success' => false, 'message' => 'Gagal menyimpan data jurnal bimbingan.'];
            }

            $this->db->transCommit();
            return [
                'success' => true,
                'message' => 'Jurnal bimbingan siswa berhasil disimpan.',
                'id'      => $insertId,
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Error in JurnalGuruWaliService::createJurnal: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update an existing journal entry
     */
    public function updateJurnal(int $id, int $guruId, array $data, $file = null, bool $hapusFoto = false): array
    {
        $this->db->transBegin();

        try {
            $existing = $this->jurnalModel->where('id', $id)->where('guru_id', $guruId)->first();
            if (!$existing) {
                return ['success' => false, 'message' => 'Data jurnal tidak ditemukan atau bukan milik Anda.'];
            }

            $fotoName = $existing['foto_dokumentasi'] ?? null;
            if ($hapusFoto) {
                $this->removeFotoFile($fotoName);
                $fotoName = null;
            }

            if ($file && $file->isValid()) {
                $this->removeFotoFile($fotoName);
                $fotoName = $this->handleUploadFoto($file);
            }

            $updateData = [
                'tanggal'          => $data['tanggal'] ?? $existing['tanggal'],
                'siswa_id'         => !empty($data['siswa_id']) ? (int) $data['siswa_id'] : $existing['siswa_id'],
                'jenis_bimbingan'  => $data['jenis_bimbingan'] ?? $existing['jenis_bimbingan'],
                'catatan'          => $data['catatan'] ?? $existing['catatan'],
                'tindak_lanjut'    => array_key_exists('tindak_lanjut', $data) ? $data['tindak_lanjut'] : $existing['tindak_lanjut'],
                'foto_dokumentasi' => $fotoName,
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            $this->jurnalModel->update($id, $updateData);
            $this->db->transCommit();

            return [
                'success' => true,
                'message' => 'Jurnal bimbingan berhasil diperbarui.',
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Error in JurnalGuruWaliService::updateJurnal: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete a journal entry
     */
    public function deleteJurnal(int $id, int $guruId): array
    {
        try {
            $existing = $this->jurnalModel->where('id', $id)->where('guru_id', $guruId)->first();
            if (!$existing) {
                return ['success' => false, 'message' => 'Data jurnal tidak ditemukan atau bukan milik Anda.'];
            }

            $this->jurnalModel->delete($id);
            return [
                'success' => true,
                'message' => 'Data jurnal bimbingan berhasil dihapus.',
            ];
        } catch (\Throwable $e) {
            log_message('error', 'Error in JurnalGuruWaliService::deleteJurnal: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get complete printable data for PDF / Print view
     */
    public function getPrintData(int $guruId, array $filters = []): array
    {
        try {
            $guru = $this->guruModel->find($guruId);
            if (!$guru) {
                return ['success' => false, 'message' => 'Data guru tidak ditemukan'];
            }

            $sekolahInfo = [
                'nama_sekolah'        => $this->settingModel->get('nama_sekolah') ?: 'SMK NEGERI 1 SIMACCA',
                'alamat'              => $this->settingModel->get('alamat_sekolah') ?: 'Jl. Pendidikan No. 1',
                'telepon'             => $this->settingModel->get('telepon_sekolah') ?: '-',
                'email'               => $this->settingModel->get('email_sekolah') ?: '-',
                'website'             => $this->settingModel->get('website_sekolah') ?: 'https://simacca.sch.id',
                'kepala_sekolah'      => $this->settingModel->get('kepala_sekolah_nama') ?: 'Kepala Sekolah',
                'nip_kepala_sekolah'  => $this->settingModel->get('kepala_sekolah_nip') ?: '-',
                'kota'                => $this->settingModel->get('kota_sekolah') ?: 'Kota',
            ];
            $siswaBinaan   = $this->getSiswaBinaan($guruId);
            $jurnalList    = $this->getJurnalList($guruId, $filters);
            $selectedSiswa = null;

            if (!empty($filters['siswa_id'])) {
                $selectedSiswa = $this->siswaModel->select('siswa.*, kelas.nama_kelas, kelas.tingkat, kelas.jurusan')
                    ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
                    ->find((int) $filters['siswa_id']);
            }

            return [
                'success' => true,
                'data'    => [
                    'guru'          => $guru,
                    'sekolahInfo'   => $sekolahInfo,
                    'siswaBinaan'   => $siswaBinaan,
                    'jurnalList'    => $jurnalList,
                    'selectedSiswa' => $selectedSiswa,
                    'filters'       => $filters,
                ],
            ];
        } catch (\Throwable $e) {
            log_message('error', 'Error in JurnalGuruWaliService::getPrintData: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal menyiapkan data cetak: ' . $e->getMessage()];
        }
    }
}
