<?php

namespace App\Services;

use App\Models\GuruWaliSiswaModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use Config\Database;

class GuruWaliService
{
    protected $guruWaliModel;
    protected $guruModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $db;

    public function __construct()
    {
        $this->guruWaliModel = new GuruWaliSiswaModel();
        $this->guruModel     = new GuruModel();
        $this->siswaModel    = new SiswaModel();
        $this->kelasModel    = new KelasModel();
        $this->db            = Database::connect();
    }

    /**
     * Get complete overview data for Master Guru Wali dashboard
     */
    public function getOverviewData(string $tahunAjaran, array $filters = []): array
    {
        try {
            $stats       = $this->guruWaliModel->getStats($tahunAjaran);
            $siswaList   = $this->guruWaliModel->getSiswaWithGuruWali($tahunAjaran, $filters);
            $teacherList = $this->guruWaliModel->getGuruWaliSummary($tahunAjaran, $filters['search'] ?? null);
            $kelasList   = $this->kelasModel->where('tahun_ajaran', $tahunAjaran)->orderBy('tingkat, nama_kelas')->findAll();
            $availableGuru = $this->guruModel->select('guru.id, guru.nip, guru.nama_lengkap, mata_pelajaran.nama_mapel')
                ->join('users', 'users.id = guru.user_id', 'left')
                ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left')
                ->where('guru.deleted_at IS NULL')
                ->where('users.is_active', 1)
                ->orderBy('guru.nama_lengkap', 'ASC')
                ->findAll();

            // Distinct Jurusan
            $jurusanList = array_values(array_unique(array_filter(array_column($kelasList, 'jurusan'))));

            return [
                'success' => true,
                'data'    => [
                    'stats'         => $stats,
                    'siswaList'     => $siswaList,
                    'teacherList'   => $teacherList,
                    'kelasList'     => $kelasList,
                    'availableGuru' => $availableGuru,
                    'jurusanList'   => $jurusanList,
                ],
            ];
        } catch (\Throwable $e) {
            log_message('error', 'Error in GuruWaliService::getOverviewData: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal memuat data Guru Wali: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Assign a single student to a Guru Wali
     */
    public function assignSiswa(int $siswaId, int $guruId, string $tahunAjaran, ?string $keterangan = null): array
    {
        $this->db->transBegin();

        try {
            $siswa = $this->siswaModel->find($siswaId);
            if (!$siswa) {
                return ['success' => false, 'message' => 'Data Siswa tidak ditemukan'];
            }

            $guru = $this->guruModel->find($guruId);
            if (!$guru) {
                return ['success' => false, 'message' => 'Data Guru tidak ditemukan'];
            }

            $this->guruWaliModel->assignSiswa($siswaId, $guruId, $tahunAjaran, $keterangan);

            $this->db->transCommit();

            return [
                'success' => true,
                'message' => "Siswa {$siswa['nama_lengkap']} berhasil ditugaskan ke Guru Wali {$guru['nama_lengkap']}",
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Error in GuruWaliService::assignSiswa: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal menugaskan Guru Wali: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Batch assign multiple students to a single Guru Wali
     */
    public function bulkAssignSiswa(array $siswaIds, int $guruId, string $tahunAjaran): array
    {
        if (empty($siswaIds)) {
            return ['success' => false, 'message' => 'Pilih minimal satu siswa untuk ditugaskan'];
        }

        $this->db->transBegin();

        try {
            $guru = $this->guruModel->find($guruId);
            if (!$guru) {
                return ['success' => false, 'message' => 'Data Guru tidak ditemukan'];
            }

            $count = 0;
            foreach ($siswaIds as $siswaId) {
                $siswaId = (int) $siswaId;
                if ($siswaId > 0) {
                    $this->guruWaliModel->assignSiswa($siswaId, $guruId, $tahunAjaran);
                    $count++;
                }
            }

            $this->db->transCommit();

            return [
                'success' => true,
                'message' => "Berhasil menugaskan {$count} siswa ke Guru Wali {$guru['nama_lengkap']}",
                'count'   => $count,
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Error in GuruWaliService::bulkAssignSiswa: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal melakukan penugasan massal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Auto-distribute students evenly across selected teachers
     */
    public function autoDistribute(array $siswaIds, array $guruIds, string $tahunAjaran): array
    {
        if (empty($siswaIds)) {
            // If siswaIds is empty, auto-detect unassigned students
            $unassigned = $this->guruWaliModel->getSiswaWithGuruWali($tahunAjaran, ['status' => 'unassigned']);
            $siswaIds = array_column($unassigned, 'siswa_id');
        }

        if (empty($siswaIds)) {
            return ['success' => false, 'message' => 'Tidak ada siswa yang perlu dibagikan'];
        }

        if (empty($guruIds)) {
            return ['success' => false, 'message' => 'Pilih minimal satu Guru Wali untuk pembagian otomatis'];
        }

        $this->db->transBegin();

        try {
            $guruCount = count($guruIds);
            $assignedCount = 0;

            foreach ($siswaIds as $index => $siswaId) {
                $targetGuruId = $guruIds[$index % $guruCount];
                $this->guruWaliModel->assignSiswa((int) $siswaId, (int) $targetGuruId, $tahunAjaran, 'Pembagian Otomatis Sistem');
                $assignedCount++;
            }

            $this->db->transCommit();

            return [
                'success' => true,
                'message' => "Berhasil membagi {$assignedCount} siswa secara merata ke {$guruCount} Guru Wali",
                'count'   => $assignedCount,
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Error in GuruWaliService::autoDistribute: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal melakukan pembagian otomatis: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Unassign a student from their Guru Wali
     */
    public function unassignSiswa(int $siswaId, string $tahunAjaran): array
    {
        try {
            $deleted = $this->guruWaliModel->unassignSiswa($siswaId, $tahunAjaran);
            if ($deleted) {
                return ['success' => true, 'message' => 'Penugasan Guru Wali berhasil dilepas'];
            }
            return ['success' => false, 'message' => 'Siswa tidak memiliki penugasan Guru Wali pada tahun ajaran ini'];
        } catch (\Throwable $e) {
            log_message('error', 'Error in GuruWaliService::unassignSiswa: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal melepas Guru Wali: ' . $e->getMessage()];
        }
    }

    /**
     * Bulk unassign multiple students
     */
    public function bulkUnassignSiswa(array $siswaIds, string $tahunAjaran): array
    {
        if (empty($siswaIds)) {
            return ['success' => false, 'message' => 'Pilih minimal satu siswa'];
        }

        $this->db->transBegin();

        try {
            $count = 0;
            foreach ($siswaIds as $siswaId) {
                $this->guruWaliModel->unassignSiswa((int) $siswaId, $tahunAjaran);
                $count++;
            }

            $this->db->transCommit();

            return [
                'success' => true,
                'message' => "Berhasil melepas penugasan Guru Wali untuk {$count} siswa",
                'count'   => $count,
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Error in GuruWaliService::bulkUnassignSiswa: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal melepas penugasan massal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get detail of a teacher and their assigned students
     */
    public function getSiswaByGuru(int $guruId, string $tahunAjaran): array
    {
        try {
            $guru = $this->guruModel->select('guru.*, users.profile_photo, users.email, mata_pelajaran.nama_mapel')
                ->join('users', 'users.id = guru.user_id', 'left')
                ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left')
                ->where('guru.id', $guruId)
                ->first();

            if (!$guru) {
                return ['success' => false, 'message' => 'Guru tidak ditemukan'];
            }

            $siswaList = $this->guruWaliModel->getSiswaByGuru($guruId, $tahunAjaran);

            return [
                'success' => true,
                'data'    => [
                    'guru'      => $guru,
                    'siswaList' => $siswaList,
                    'total'     => count($siswaList),
                ],
            ];
        } catch (\Throwable $e) {
            log_message('error', 'Error in GuruWaliService::getSiswaByGuru: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal mengambil data siswa binaan: ' . $e->getMessage()];
        }
    }

    /**
     * Prepare data for official print preview
     */
    public function getPrintData(string $tahunAjaran, ?int $guruId = null): array
    {
        try {
            $stats = $this->guruWaliModel->getStats($tahunAjaran);
            
            $db = Database::connect();
            $guruBuilder = $db->table('guru g')
                ->select('
                    g.id as guru_id,
                    g.nip,
                    g.nama_lengkap,
                    g.jenis_kelamin,
                    mp.nama_mapel,
                    COUNT(gws.id) as total_siswa
                ')
                ->join('mata_pelajaran mp', 'mp.id = g.mata_pelajaran_id', 'left')
                ->join('guru_wali_siswa gws', "gws.guru_id = g.id AND gws.tahun_ajaran = '{$tahunAjaran}' AND gws.deleted_at IS NULL", 'inner')
                ->where('g.deleted_at IS NULL');

            if ($guruId) {
                $guruBuilder->where('g.id', $guruId);
            }

            $guruWaliList = $guruBuilder->groupBy('g.id')
                ->orderBy('g.nama_lengkap', 'ASC')
                ->get()
                ->getResultArray();

            // Hydrate each teacher with their student list
            foreach ($guruWaliList as &$gw) {
                $gw['siswa'] = $this->guruWaliModel->getSiswaByGuru((int) $gw['guru_id'], $tahunAjaran);
            }

            return [
                'success' => true,
                'data'    => [
                    'tahunAjaran'  => $tahunAjaran,
                    'stats'        => $stats,
                    'guruWaliList' => $guruWaliList,
                ],
            ];
        } catch (\Throwable $e) {
            log_message('error', 'Error in GuruWaliService::getPrintData: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal menyiapkan data cetak: ' . $e->getMessage(),
            ];
        }
    }
}
