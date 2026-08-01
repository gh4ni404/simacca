<?php

namespace App\Services;

use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\JadwalMengajarModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\IzinSiswaModel;

/**
 * Absensi Service
 * 
 * Handles all business logic related to Absensi (Attendance) management:
 * - CRUD operations for absensi and absensi details
 * - Statistics calculation
 * - Access control and validation
 * - Editable status checking (24-hour rule)
 * - Unlock functionality (admin)
 * - Substitute teacher handling
 */
class AbsensiService extends BaseService
{
    protected $absensiModel;
    protected $absensiDetailModel;
    protected $jadwalModel;
    protected $guruModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $izinModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->izinModel = new IzinSiswaModel();
    }

    /**
     * Get absensi by guru with optional filters
     * 
     * @param int $guruId
     * @param string|null $tanggal
     * @return array
     */
    public function getByGuru(int $guruId, ?string $tanggal = null, ?string $tahunAjaran = null): array
    {
        try {
            $absensi = $this->absensiModel->getByGuru($guruId, $tanggal, null, $tahunAjaran);
            
            // Add can_edit and can_delete flags
            foreach ($absensi as &$item) {
                $item['can_edit'] = $this->isAbsensiEditable($item);
                $item['can_delete'] = $this->isAbsensiEditable($item);
            }
            unset($item); // Break reference
            
            return $this->successResponse($absensi);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get absensi by guru: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data absensi');
        }
    }

    /**
     * Get absensi by guru and kelas
     * 
     * @param int $guruId
     * @param int $kelasId
     * @param string|null $tanggal
     * @return array
     */
    public function getByGuruAndKelas(int $guruId, int $kelasId, ?string $tanggal = null, ?string $tahunAjaran = null): array
    {
        try {
            $absensiList = $this->absensiModel->getByGuruAndKelas($guruId, $kelasId, $tanggal, $tahunAjaran);
            
            // Add can_edit and can_delete flags
            foreach ($absensiList as &$item) {
                $item['can_edit'] = $this->isAbsensiEditable($item);
                $item['can_delete'] = $this->isAbsensiEditable($item);
            }
            unset($item);
            
            return $this->successResponse($absensiList);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get absensi by guru and kelas: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data absensi');
        }
    }

    /**
     * Get absensi detail with all related data
     * 
     * @param int $id
     * @return array
     */
    public function getAbsensiDetail(int $id): array
    {
        try {
            $absensi = $this->absensiModel->getAbsensiWithDetail($id);
            
            if (!$absensi) {
                return $this->errorResponse('Data absensi tidak ditemukan');
            }
            
            $absensiDetails = $this->absensiDetailModel->getByAbsensi($id);
            $statistics = $this->calculateStatistics($absensiDetails);
            
            $data = [
                'absensi' => $absensi,
                'absensiDetails' => $absensiDetails,
                'statistics' => $statistics,
                'isEditable' => $this->isAbsensiEditable($absensi)
            ];
            
            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get absensi detail: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil detail absensi');
        }
    }

    /**
     * Create new absensi with details
     * 
     * @param array $data
     * @return array
     */
    public function createAbsensi(array $data): array
    {
        // Validate main absensi data
        $rules = [
            'jadwal_mengajar_id' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'materi_pembelajaran' => 'permit_empty',
            'siswa' => 'required',
            'created_by' => 'required|numeric'
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        // Verify jadwal exists
        $jadwal = $this->jadwalModel->find($data['jadwal_mengajar_id']);
        if (!$jadwal) {
            return $this->errorResponse('Jadwal tidak valid');
        }

        // Check if absensi already exists
        if ($this->absensiModel->isAlreadyAbsen($data['jadwal_mengajar_id'], $data['tanggal'])) {
            return $this->errorResponse('Absen di tanggal ini sudah diisi sebelumnya');
        }

        return $this->executeInTransaction(function () use ($data, $jadwal) {
            // AUTO-CALCULATE pertemuan_ke scoped by guru+mapel+kelas+tahun_ajaran
            $pertemuanKe = $this->calculateNextPertemuan($data['jadwal_mengajar_id']);
            
            // Prepare absensi data
            $absensiData = [
                'jadwal_mengajar_id' => $data['jadwal_mengajar_id'],
                'tanggal' => $data['tanggal'],
                'pertemuan_ke' => $pertemuanKe, // Auto-calculated, not from user input
                'materi_pembelajaran' => $data['materi_pembelajaran'] ?? null,
                'created_by' => $data['created_by'],
                'guru_pengganti_id' => $data['guru_pengganti_id'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Insert absensi
            $absensiId = $this->absensiModel->insert($absensiData);

            if (!$absensiId) {
                throw new \Exception('Gagal menyimpan data absensi');
            }

            // Insert absensi details
            $siswaData = $data['siswa'];
            $batchData = [];

            foreach ($siswaData as $siswaId => $siswaDetail) {
                $batchData[] = [
                    'absensi_id' => $absensiId,
                    'siswa_id' => $siswaId,
                    'status' => $siswaDetail['status'],
                    'keterangan' => $siswaDetail['keterangan'] ?? null,
                    'waktu_absen' => date('Y-m-d H:i:s')
                ];
            }

            if (!empty($batchData)) {
                $this->absensiDetailModel->insertBatch($batchData);
            }

            $this->log('info', "Absensi created: ID {$absensiId}, Jadwal {$data['jadwal_mengajar_id']}, Tanggal {$data['tanggal']}");

            return [
                'absensi_id' => $absensiId,
                'total_siswa' => count($siswaData)
            ];
        });
    }

    /**
     * Update absensi with details
     * 
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateAbsensi(int $id, array $data): array
    {
        $absensi = $this->absensiModel->find($id);

        if (!$absensi) {
            return $this->errorResponse('Data absensi tidak ditemukan');
        }

        // Check if editable
        if (!$this->isAbsensiEditable($absensi)) {
            return $this->errorResponse('Absen sudah lewat 24 jam, tidak bisa diedit');
        }

        // Validate input
        $rules = [
            'tanggal' => 'required|valid_date',
            'pertemuan_ke' => 'required|numeric|greater_than[0]',
            'siswa' => 'required'
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        // Validate siswa data
        $siswaData = $data['siswa'];
        if (empty($siswaData) || !is_array($siswaData)) {
            return $this->errorResponse('Data siswa tidak valid');
        }

        return $this->executeInTransaction(function () use ($id, $absensi, $data, $siswaData) {
            // Update main absensi
            $absensiData = [
                'id' => $id,
                'tanggal' => $data['tanggal'],
                'pertemuan_ke' => $data['pertemuan_ke'],
                'materi_pembelajaran' => $data['materi_pembelajaran'] ?? $absensi['materi_pembelajaran'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (!$this->absensiModel->save($absensiData)) {
                throw new \Exception('Gagal mengupdate data absensi');
            }

            // Update absensi details
            $updateCount = 0;
            $insertCount = 0;

            foreach ($siswaData as $siswaId => $siswaDetail) {
                if (!is_numeric($siswaId)) {
                    continue;
                }

                if (!isset($siswaDetail['status']) || empty($siswaDetail['status'])) {
                    continue;
                }

                // Normalize status
                $status = strtolower(trim($siswaDetail['status']));
                if ($status === 'alpha') {
                    $status = 'alpa';
                }

                // Validate status
                $validStatuses = ['hadir', 'izin', 'sakit', 'alpa'];
                if (!in_array($status, $validStatuses)) {
                    continue;
                }

                $existing = $this->absensiDetailModel
                    ->where('absensi_id', $id)
                    ->where('siswa_id', $siswaId)
                    ->first();

                if ($existing) {
                    // Update existing
                    $updateResult = $this->absensiDetailModel->update($existing['id'], [
                        'status' => $status,
                        'keterangan' => $siswaDetail['keterangan'] ?? null
                    ]);

                    if ($updateResult) {
                        $updateCount++;
                    }
                } else {
                    // Insert new
                    $insertResult = $this->absensiDetailModel->insert([
                        'absensi_id' => $id,
                        'siswa_id' => $siswaId,
                        'status' => $status,
                        'keterangan' => $siswaDetail['keterangan'] ?? null,
                        'waktu_absen' => date('Y-m-d H:i:s')
                    ]);

                    if ($insertResult) {
                        $insertCount++;
                    }
                }
            }

            $this->log('info', "Absensi updated: ID {$id}, Updated: {$updateCount}, Inserted: {$insertCount}");

            return [
                'absensi_id' => $id,
                'updated' => $updateCount,
                'inserted' => $insertCount
            ];
        });
    }

    /**
     * Delete absensi
     * 
     * @param int $id
     * @return array
     */
    public function deleteAbsensi(int $id): array
    {
        $absensi = $this->absensiModel->find($id);

        if (!$absensi) {
            return $this->errorResponse('Data absensi tidak ditemukan');
        }

        // Check if editable
        if (!$this->isAbsensiEditable($absensi)) {
            return $this->errorResponse('Absen sudah lewat 24 jam, tidak bisa dihapus');
        }

        return $this->executeInTransaction(function () use ($id) {
            // Delete will cascade to absensi_detail
            $this->absensiModel->delete($id);
            
            $this->log('info', "Absensi deleted: ID {$id}");

            return ['absensi_id' => $id];
        });
    }

    /**
     * Get kelas summary for a guru
     * 
     * @param array $absensi
     * @return array
     */
    public function getKelasSummary(array $absensi): array
    {
        $kelasSummary = [];
        
        foreach ($absensi as $item) {
            $kelasId = $item['kelas_id'];
            $kelasName = $item['nama_kelas'];
            $mapelName = $item['nama_mapel'];
            
            // Create unique key: kelas_id + mata_pelajaran
            $summaryKey = $kelasId . '_' . $mapelName;
            
            if (!isset($kelasSummary[$summaryKey])) {
                $kelasSummary[$summaryKey] = [
                    'kelas_id' => $kelasId,
                    'kelas_nama' => $kelasName,
                    'mata_pelajaran' => $mapelName,
                    'total_pertemuan' => 0,
                    'total_hadir' => 0,
                    'total_siswa' => 0,
                    'avg_kehadiran' => 0,
                    'last_absensi' => null,
                    'jam_mulai' => $item['jam_mulai'] ?? null,
                    'jam_selesai' => $item['jam_selesai'] ?? null,
                    'hari' => $item['hari'] ?? null
                ];
            }
            
            // Accumulate data
            $kelasSummary[$summaryKey]['total_pertemuan']++;
            $kelasSummary[$summaryKey]['total_hadir'] += $item['hadir'] ?? 0;
            $kelasSummary[$summaryKey]['total_siswa'] = max($kelasSummary[$summaryKey]['total_siswa'], $item['total_siswa'] ?? 0);
            
            // Track latest absensi date
            if (!$kelasSummary[$summaryKey]['last_absensi'] || $item['tanggal'] > $kelasSummary[$summaryKey]['last_absensi']) {
                $kelasSummary[$summaryKey]['last_absensi'] = $item['tanggal'];
            }
        }
        
        // Calculate average kehadiran
        foreach ($kelasSummary as &$summary) {
            $totalExpected = $summary['total_pertemuan'] * $summary['total_siswa'];
            if ($totalExpected > 0) {
                $summary['avg_kehadiran'] = round(($summary['total_hadir'] / $totalExpected) * 100, 1);
            }
        }
        unset($summary);
        
        // Sort by kelas name
        uasort($kelasSummary, function($a, $b) {
            return strcmp($a['kelas_nama'], $b['kelas_nama']);
        });
        
        return $kelasSummary;
    }

    /**
     * Get absensi statistics for a guru
     * 
     * @param int $guruId
     * @param string|null $tanggal
     * @return array
     */
    public function getAbsensiStats(int $guruId, ?string $tanggal = null, ?string $tahunAjaran = null): array
    {
        try {
            $stats = [
                'total' => 0,
                'hadir' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alpa' => 0
            ];

            $builder = $this->absensiDetailModel
                ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
                ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
                ->where('jadwal_mengajar.guru_id', $guruId);

            if ($tahunAjaran) {
                $builder->where('jadwal_mengajar.tahun_ajaran', $tahunAjaran);
            }

            if ($tanggal) {
                $builder->where('absensi.tanggal', $tanggal);
            }

            $details = $builder->select('absensi_detail.status, COUNT(*) as jumlah')
                ->groupBy('absensi_detail.status')
                ->findAll();

            foreach ($details as $detail) {
                $stats[$detail['status']] = $detail['jumlah'];
                $stats['total'] += $detail['jumlah'];
            }

            return $this->successResponse($stats);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get absensi stats: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil statistik');
        }
    }

    /**
     * Get next pertemuan number
     * 
     * Scoped by guru + mata_pelajaran + kelas + tahun_ajaran so that
     * all sessions of the same subject for the same class share one
     * continuous pertemuan sequence (e.g. Senin=1, Rabu=2, Senin=3, ...).
     * 
     * @param int $guruId
     * @param int|null $kelasId
     * @param int|null $jadwalId
     * @return array
     */
    public function getNextPertemuan(int $guruId, ?int $kelasId = null, ?int $jadwalId = null): array
    {
        try {
            $nextPertemuan = $this->calculateNextPertemuan($jadwalId);

            return $this->successResponse(['pertemuan_ke' => $nextPertemuan]);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get next pertemuan: ' . $e->getMessage());
            return $this->errorResponse('Gagal mendapatkan nomor pertemuan');
        }
    }

    /**
     * Calculate next pertemuan_ke by scoped lookup (guru + mapel + kelas + tahun_ajaran)
     * 
     * @param int|null $jadwalId
     * @return int
     */
    private function calculateNextPertemuan(?int $jadwalId): int
    {
        if (!$jadwalId) {
            return 1;
        }

        $jadwal = $this->jadwalModel->find($jadwalId);
        if (!$jadwal) {
            return 1;
        }

        $lastAbsensi = $this->absensiModel
            ->select('absensi.pertemuan_ke')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->where('jadwal_mengajar.guru_id', $jadwal['guru_id'])
            ->where('jadwal_mengajar.mata_pelajaran_id', $jadwal['mata_pelajaran_id'])
            ->where('jadwal_mengajar.kelas_id', $jadwal['kelas_id'])
            ->where('jadwal_mengajar.tahun_ajaran', $jadwal['tahun_ajaran'])
            ->orderBy('absensi.pertemuan_ke', 'DESC')
            ->first();

        return $lastAbsensi ? ($lastAbsensi['pertemuan_ke'] + 1) : 1;
    }

    /**
     * Check if absensi is editable (within 24 hours or unlocked by admin)
     * 
     * @param array $absensi
     * @return bool
     */
    public function isAbsensiEditable(array $absensi): bool
    {
        if (!isset($absensi['created_at'])) {
            return false;
        }

        // Determine reference time (unlocked_at or created_at)
        $referenceTime = !empty($absensi['unlocked_at']) 
            ? strtotime($absensi['unlocked_at']) 
            : strtotime($absensi['created_at']);

        $now = time();
        $hoursPassed = ($now - $referenceTime) / 3600;

        // Editable if less than 24 hours have passed
        return $hoursPassed < 24;
    }

    /**
     * Unlock absensi (admin only)
     * 
     * @param int $absensiId
     * @return array
     */
    public function unlockAbsensi(int $absensiId): array
    {
        $absensi = $this->absensiModel->find($absensiId);

        if (!$absensi) {
            return $this->errorResponse('Absensi tidak ditemukan');
        }

        try {
            $updated = $this->absensiModel->update($absensiId, [
                'unlocked_at' => date('Y-m-d H:i:s')
            ]);

            if ($updated) {
                // Get details for success message
                $absensiDetail = $this->absensiModel
                    ->select('absensi.*, kelas.nama_kelas, guru.nama_lengkap as nama_guru, mata_pelajaran.nama_mapel')
                    ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
                    ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
                    ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
                    ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
                    ->find($absensiId);

                $this->log('info', "Absensi unlocked: ID {$absensiId}");

                return $this->successResponse($absensiDetail, 'Absensi berhasil di-unlock');
            }

            return $this->errorResponse('Gagal unlock absensi');
        } catch (\Exception $e) {
            $this->log('error', 'Failed to unlock absensi: ' . $e->getMessage());
            return $this->errorResponse('Gagal unlock absensi');
        }
    }

    /**
     * Bulk unlock multiple absensi
     * 
     * @param array $absensiIds
     * @return array
     */
    public function bulkUnlockAbsensi(array $absensiIds): array
    {
        if (empty($absensiIds)) {
            return $this->errorResponse('Pilih minimal satu absensi');
        }

        try {
            $successCount = 0;
            foreach ($absensiIds as $id) {
                $updated = $this->absensiModel->update($id, [
                    'unlocked_at' => date('Y-m-d H:i:s')
                ]);
                if ($updated) {
                    $successCount++;
                }
            }

            $this->log('info', "Bulk unlock completed: {$successCount} absensi unlocked");

            return $this->successResponse(
                ['count' => $successCount],
                "Berhasil unlock {$successCount} absensi"
            );
        } catch (\Exception $e) {
            $this->log('error', 'Failed to bulk unlock: ' . $e->getMessage());
            return $this->errorResponse('Gagal unlock absensi');
        }
    }

    /**
     * Verify access to absensi
     * 
     * @param array $absensi
     * @param int $userId
     * @param int $guruId
     * @return array
     */
    public function verifyAccess(array $absensi, int $userId, int $guruId): array
    {
        $jadwal = $this->jadwalModel->find($absensi['jadwal_mengajar_id']);
        
        $hasAccess = ($absensi['created_by'] == $userId)
            || ($jadwal && $jadwal['guru_id'] == $guruId)
            || ($absensi['guru_pengganti_id'] == $guruId);

        if (!$hasAccess) {
            return $this->errorResponse('Anda tidak memiliki akses ke absensi ini');
        }

        return $this->successResponse(['has_access' => true]);
    }

    /**
     * Calculate statistics from absensi details
     * 
     * @param array $absensiDetails
     * @return array
     */
    protected function calculateStatistics(array $absensiDetails): array
    {
        $total = count($absensiDetails);
        $statistics = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'percentage' => 0
        ];

        foreach ($absensiDetails as $detail) {
            if (isset($statistics[$detail['status']])) {
                $statistics[$detail['status']]++;
            }
        }

        if ($total > 0) {
            $hadir = $statistics['hadir'];
            $statistics['percentage'] = round(($hadir / $total) * 100, 2);
        }

        return $statistics;
    }

    /**
     * Get siswa by kelas with approved izin
     * 
     * @param int $kelasId
     * @param string|null $tanggal
     * @return array
     */
    public function getSiswaByKelas(int $kelasId, ?string $tanggal = null, ?string $tahunAjaran = null): array
    {
        try {
            $siswaList = $this->siswaModel->getByKelas($kelasId, $tahunAjaran);
            
            $approvedIzin = [];
            if ($tanggal) {
                $approvedIzin = $this->izinModel->getApprovedIzinByDate($tanggal, $kelasId);
            }
            
            return $this->successResponse([
                'siswa' => $siswaList,
                'approvedIzin' => $approvedIzin
            ]);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get siswa by kelas: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data siswa');
        }
    }

    /**
     * Check if absensi already exists
     * 
     * @param int $jadwalId
     * @param string $tanggal
     * @return array
     */
    public function checkAbsensiExists(int $jadwalId, string $tanggal): array
    {
        try {
            $exists = $this->absensiModel->isAlreadyAbsen($jadwalId, $tanggal);
            
            if ($exists) {
                $absensi = $this->absensiModel->getByJadwalAndTanggal($jadwalId, $tanggal);
                return $this->successResponse([
                    'exists' => true,
                    'absensi' => $absensi
                ]);
            }
            
            return $this->successResponse(['exists' => false]);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to check absensi exists: ' . $e->getMessage());
            return $this->errorResponse('Gagal memeriksa absensi');
        }
    }
}
