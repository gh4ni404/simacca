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
     * Create a new journal entry
     */
    public function createJurnal(array $data): array
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

            $insertData = [
                'guru_id'         => $guruId,
                'siswa_id'        => $siswaId,
                'tahun_ajaran'    => $data['tahun_ajaran'] ?? get_active_tahun_ajaran(),
                'tanggal'         => $data['tanggal'],
                'jenis_bimbingan' => $data['jenis_bimbingan'] ?? 'Akademik',
                'catatan'         => $data['catatan'],
                'tindak_lanjut'   => $data['tindak_lanjut'] ?? null,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
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
    public function updateJurnal(int $id, int $guruId, array $data): array
    {
        $this->db->transBegin();

        try {
            $existing = $this->jurnalModel->where('id', $id)->where('guru_id', $guruId)->first();
            if (!$existing) {
                return ['success' => false, 'message' => 'Data jurnal tidak ditemukan atau bukan milik Anda.'];
            }

            $updateData = [
                'tanggal'         => $data['tanggal'] ?? $existing['tanggal'],
                'siswa_id'        => !empty($data['siswa_id']) ? (int) $data['siswa_id'] : $existing['siswa_id'],
                'jenis_bimbingan' => $data['jenis_bimbingan'] ?? $existing['jenis_bimbingan'],
                'catatan'         => $data['catatan'] ?? $existing['catatan'],
                'tindak_lanjut'   => array_key_exists('tindak_lanjut', $data) ? $data['tindak_lanjut'] : $existing['tindak_lanjut'],
                'updated_at'      => date('Y-m-d H:i:s'),
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
