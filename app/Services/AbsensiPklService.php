<?php

namespace App\Services;

use App\Models\AbsensiPklModel;
use App\Models\AbsensiPklDetailModel;
use App\Models\HariLiburModel;
use App\Models\PembimbingPklModel;
use App\Models\SiswaPklModel;
use App\Models\SiswaModel;

class AbsensiPklService extends BaseService
{
    protected $absensiPklModel;
    protected $absensiPklDetailModel;
    protected $hariLiburModel;
    protected $pembimbingPklModel;
    protected $siswaPklModel;
    protected $siswaModel;

    public function __construct()
    {
        parent::__construct();

        $this->absensiPklModel       = new AbsensiPklModel();
        $this->absensiPklDetailModel = new AbsensiPklDetailModel();
        $this->hariLiburModel        = new HariLiburModel();
        $this->pembimbingPklModel    = new PembimbingPklModel();
        $this->siswaPklModel         = new SiswaPklModel();
        $this->siswaModel            = new SiswaModel();
    }

    /**
     * Get absensi list by guru (as pembimbing)
     */
    public function getByGuru(int $guruId, ?string $tanggal = null): array
    {
        try {
            $absensi = $this->absensiPklModel->getByGuru($guruId, $tanggal);

            // Enrich with detail stats
            foreach ($absensi as &$item) {
                $stats = $this->absensiPklDetailModel->getDetailStats($item['id']);
                $item['total_siswa'] = $stats['total'];
                $item['hadir_count'] = $stats['hadir'];
                $item['izin_count'] = $stats['izin'];
                $item['sakit_count'] = $stats['sakit'];
                $item['alpa_count'] = $stats['alpa'];
                $item['persen_kehadiran'] = $stats['persen_kehadiran'];
                $item['can_edit']              = true; // Pembimbing can always edit
                $item['can_delete']            = true;
                $item['missing_pulang_count']  = $stats['hadir'] > 0
                    ? $this->absensiPklDetailModel->getMissingPulangCount($item['id'])
                    : 0;
            }
            unset($item);

            return $this->successResponse($absensi);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get absensi pkl by guru: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data absensi PKL');
        }
    }

    /**
     * Get absensi detail
     */
    public function getAbsensiDetail(int $id): array
    {
        try {
            $absensi = $this->absensiPklModel->getAbsensiPklWithDetail($id);

            if (!$absensi) {
                return $this->errorResponse('Data absensi PKL tidak ditemukan');
            }

            $details = $this->absensiPklDetailModel->getByAbsensiPkl($id);
            $statistics = $this->absensiPklDetailModel->getDetailStats($id);

            $data = [
                'absensi'      => $absensi,
                'details'      => $details,
                'statistics'   => $statistics,
            ];

            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get absensi pkl detail: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil detail absensi PKL');
        }
    }

    /**
     * Create new absensi pkl
     */
    public function createAbsensiPkl(array $data): array
    {
        try {
            // Validate required fields
            if (empty($data['pembimbing_pkl_id']) || empty($data['tanggal']) || empty($data['siswa'])) {
                return $this->errorResponse('Data tidak lengkap. Pastikan tanggal dan data siswa terisi.');
            }

            // Check for existing record (including soft-deleted)
            $existing = $this->absensiPklModel->onlyDeleted()
                ->where('pembimbing_pkl_id', $data['pembimbing_pkl_id'])
                ->where('tanggal', $data['tanggal'])
                ->first();

            return $this->executeInTransaction(function () use ($data, $existing) {
                // If exists and deleted, restore it
                if ($existing) {
                    $absensiPklId = $existing['id'];
                    // Restore the soft-deleted header by clearing deleted_at
                    $this->absensiPklModel->db->table('absensi_pkl')
                        ->where('id', $absensiPklId)
                        ->update([
                            'deleted_at'      => null,
                            'keterangan_umum' => $data['keterangan_umum'] ?? null,
                            'updated_at'      => date('Y-m-d H:i:s'),
                        ]);
                    // Remove old detail records before re-inserting
                    $this->absensiPklDetailModel->where('absensi_pkl_id', $absensiPklId)->delete();
                } else {
                    // Check for active record duplicate
                    if ($this->absensiPklModel->isAlreadyAbsen($data['pembimbing_pkl_id'], $data['tanggal'])) {
                        throw new \Exception('Absensi untuk tanggal ini sudah dibuat sebelumnya');
                    }

                    // Insert header
                    $headerData = [
                        'pembimbing_pkl_id' => $data['pembimbing_pkl_id'],
                        'tanggal'           => $data['tanggal'],
                        'keterangan_umum'   => $data['keterangan_umum'] ?? null,
                        'created_by'        => $data['created_by'],
                        'created_at'        => date('Y-m-d H:i:s'),
                    ];

                    $absensiPklId = $this->absensiPklModel->insert($headerData);
                }

                if (!$absensiPklId) {
                    throw new \RuntimeException('Gagal menyimpan absensi PKL header');
                }

                // Format waktu_absen and waktu_pulang
                if (!empty($data['siswa'])) {
                    $tanggal = $data['tanggal'];

                    // Cek apakah tanggal ini adalah hari libur nasional (Opsi B)
                    $isHariLibur = $this->hariLiburModel->isHariLibur($tanggal);

                    // Validasi: jam masuk & jam pulang wajib diisi jika status hadir
                    $missingJamMasuk = [];
                    $missingJamPulang = [];
                    foreach ($data['siswa'] as $siswaId => $siswaData) {
                        $status = $siswaData['status'] ?? 'alpa';
                        if ($isHariLibur && $status !== 'hadir') continue; // akan di-override ke libur
                        if ($status === 'hadir') {
                            if (empty($siswaData['waktu_absen'])) {
                                $missingJamMasuk[] = $siswaId;
                            }
                            if (empty($siswaData['waktu_pulang'])) {
                                $missingJamPulang[] = $siswaId;
                            }
                        }
                    }
                    if (!empty($missingJamMasuk) || !empty($missingJamPulang)) {
                        $msg = '';
                        if (!empty($missingJamMasuk)) {
                            $msg .= count($missingJamMasuk) . ' siswa jam masuk belum diisi';
                        }
                        if (!empty($missingJamPulang)) {
                            if ($msg) $msg .= ' & ';
                            $msg .= count($missingJamPulang) . ' siswa jam pulang belum diisi';
                        }
                        throw new \RuntimeException($msg);
                    }

                    foreach ($data['siswa'] as $siswaId => &$siswaData) {
                        // Auto-override ke libur jika kalender menandai hari ini libur
                        // dan pembimbing tidak sengaja memilih status lain
                        if ($isHariLibur && ($siswaData['status'] ?? 'alpa') !== 'hadir') {
                            $siswaData['status'] = 'libur';
                        }

                        if (($siswaData['status'] ?? 'alpa') === 'hadir') {
                            if (!empty($siswaData['waktu_absen'])) {
                                $timeAbsen = trim($siswaData['waktu_absen']);
                                if (strlen($timeAbsen) === 5) $timeAbsen .= ':00';
                                $siswaData['waktu_absen'] = $tanggal . ' ' . $timeAbsen;
                            } else {
                                $siswaData['waktu_absen'] = null;
                            }

                            if (!empty($siswaData['waktu_pulang'])) {
                                $timePulang = trim($siswaData['waktu_pulang']);
                                if (strlen($timePulang) === 5) $timePulang .= ':00';
                                $siswaData['waktu_pulang'] = $tanggal . ' ' . $timePulang;
                            } else {
                                $siswaData['waktu_pulang'] = null;
                            }
                        } else {
                            $siswaData['waktu_absen'] = null;
                            $siswaData['waktu_pulang'] = null;
                        }
                    }
                    unset($siswaData);
                }

                // Insert/Update detail
                $inserted = $this->absensiPklDetailModel->insertBatchAbsensi($absensiPklId, $data['siswa']);

                if (!$inserted) {
                    throw new \RuntimeException('Gagal menyimpan detail absensi PKL');
                }

                return $this->successResponse([
                'absensi_pkl_id' => $absensiPklId,
                'total_siswa'    => count($data['siswa']),
            ], 'Absensi PKL berhasil disimpan');
            });
        } catch (\Exception $e) {
            $this->log('error', 'Failed to create absensi pkl: ' . $e->getMessage());
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Update absensi pkl
     */
    public function updateAbsensiPkl(int $id, array $data): array
    {
        $absensi = $this->absensiPklModel->find($id);
        if (!$absensi) {
            return $this->errorResponse('Data absensi PKL tidak ditemukan');
        }

        return $this->executeInTransaction(function () use ($id, $data, $absensi) {
            // Update header
            $headerData = [
                'tanggal'         => $data['tanggal'] ?? $absensi['tanggal'],
                'keterangan_umum' => $data['keterangan_umum'] ?? $absensi['keterangan_umum'],
                'updated_at'      => date('Y-m-d H:i:s'),
            ];

            $this->absensiPklModel->update($id, $headerData);

            // Update details
            if (!empty($data['siswa'])) {
                $tanggal = $data['tanggal'] ?? $absensi['tanggal'];

                // Cek apakah tanggal ini adalah hari libur nasional (Opsi B)
                $isHariLibur = $this->hariLiburModel->isHariLibur($tanggal);

                // Validasi: jam masuk & jam pulang wajib diisi jika status hadir
                $missingJamMasuk = [];
                $missingJamPulang = [];
                foreach ($data['siswa'] as $siswaId => $siswaData) {
                    if (empty($siswaId)) continue;
                    $status = $siswaData['status'] ?? 'alpa';
                    if ($isHariLibur && $status !== 'hadir') continue;
                    if ($status === 'hadir') {
                        if (empty($siswaData['waktu_absen'])) {
                            $missingJamMasuk[] = $siswaId;
                        }
                        if (empty($siswaData['waktu_pulang'])) {
                            $missingJamPulang[] = $siswaId;
                        }
                    }
                }
                if (!empty($missingJamMasuk) || !empty($missingJamPulang)) {
                    $msg = '';
                    if (!empty($missingJamMasuk)) {
                        $msg .= count($missingJamMasuk) . ' siswa jam masuk belum diisi';
                    }
                    if (!empty($missingJamPulang)) {
                        if ($msg) $msg .= ' & ';
                        $msg .= count($missingJamPulang) . ' siswa jam pulang belum diisi';
                    }
                    throw new \RuntimeException($msg);
                }

                foreach ($data['siswa'] as $siswaId => $siswaData) {
                    if (empty($siswaId)) {
                        continue;
                    }

                    // Auto-override ke libur jika kalender menandai hari ini libur
                    if ($isHariLibur && ($siswaData['status'] ?? 'alpa') !== 'hadir') {
                        $siswaData['status'] = 'libur';
                    }

                    $waktuAbsen  = null;
                    $waktuPulang = null;
                    
                    if (($siswaData['status'] ?? 'alpa') === 'hadir') {
                        if (!empty($siswaData['waktu_absen'])) {
                            $timeAbsen = trim($siswaData['waktu_absen']);
                            if (strlen($timeAbsen) === 5) $timeAbsen .= ':00';
                            $waktuAbsen = $tanggal . ' ' . $timeAbsen;
                        }
                        if (!empty($siswaData['waktu_pulang'])) {
                            $timePulang = trim($siswaData['waktu_pulang']);
                            if (strlen($timePulang) === 5) $timePulang .= ':00';
                            $waktuPulang = $tanggal . ' ' . $timePulang;
                        }
                    }
                    
                    $this->absensiPklDetailModel->upsertAbsensi($id, (int) $siswaId, [
                        'status'       => $siswaData['status'] ?? 'alpa',
                        'keterangan'   => $siswaData['keterangan'] ?? null,
                        'waktu_absen'  => $waktuAbsen,
                        'waktu_pulang' => $waktuPulang,
                    ]);
                }
            }

            return $this->successResponse(['absensi_pkl_id' => $id], 'Absensi PKL berhasil diperbarui');
        });
    }

    /**
     * Delete absensi pkl (soft delete)
     */
    public function deleteAbsensiPkl(int $id): array
    {
        try {
            $absensi = $this->absensiPklModel->find($id);
            if (!$absensi) {
                return $this->errorResponse('Data absensi PKL tidak ditemukan');
            }

            $this->absensiPklModel->delete($id);

            return $this->successResponse(null, 'Absensi PKL berhasil dihapus');
        } catch (\Exception $e) {
            $this->log('error', 'Failed to delete absensi pkl: ' . $e->getMessage());
            return $this->errorResponse('Gagal menghapus absensi PKL');
        }
    }

    /**
     * Get siswa by pembimbing for attendance form
     */
    public function getSiswaByPembimbing(int $guruId): array
    {
        try {
            $tahunAjaran = get_active_tahun_ajaran();

            // Get all pembimbing_pkl for this guru with nama_perusahaan
            $pembimbingList = $this->pembimbingPklModel
                ->select('pembimbing_pkl.*, tempat_pkl.nama_perusahaan')
                ->join('tempat_pkl', 'tempat_pkl.id = pembimbing_pkl.tempat_pkl_id')
                ->where('guru_id', $guruId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->findAll();

            $siswa = [];
            foreach ($pembimbingList as $pembimbing) {
                // Check if there are any students assigned specifically to this pembimbing
                $hasPembimbingFilter = $this->siswaPklModel
                    ->where('pembimbing_pkl_id', $pembimbing['id'])
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->countAllResults() > 0;

                $siswaQuery = $this->siswaPklModel->select('
                        siswa_pkl.*,
                        siswa.nama_lengkap,
                        siswa.nis,
                        kelas.nama_kelas,
                        tempat_pkl.nama_perusahaan
                    ')
                    ->join('siswa', 'siswa.id = siswa_pkl.siswa_id')
                    ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
                    ->join('tempat_pkl', 'tempat_pkl.id = siswa_pkl.tempat_pkl_id')
                    ->where('siswa_pkl.tahun_ajaran', $tahunAjaran)
                    ->orderBy('siswa.nama_lengkap', 'ASC');

                if ($hasPembimbingFilter) {
                    $siswaQuery->where('siswa_pkl.pembimbing_pkl_id', $pembimbing['id']);
                } else {
                    // Fallback for legacy data: filter by tempat_pkl_id but exclude students explicitly assigned to others
                    $siswaQuery->where('siswa_pkl.tempat_pkl_id', $pembimbing['tempat_pkl_id'])
                        ->groupStart()
                            ->where('siswa_pkl.pembimbing_pkl_id IS NULL', null, false)
                            ->orWhere('siswa_pkl.pembimbing_pkl_id', $pembimbing['id'])
                        ->groupEnd();
                }

                $siswaList = $siswaQuery->findAll();

                foreach ($siswaList as $s) {
                    $s['pembimbing_pkl_id'] = $pembimbing['id'];
                    $siswa[] = $s;
                }
            }

            return $this->successResponse([
                'siswa' => $siswa,
                'pembimbing_list' => $pembimbingList,
            ]);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get siswa by pembimbing: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data siswa bimbingan');
        }
    }

    /**
     * Get rekap for siswa
     */
    public function getRekapSiswa(int $siswaId): array
    {
        try {
            $rekap = $this->absensiPklDetailModel->getRekapSiswa($siswaId);
            $statistik = $this->absensiPklDetailModel->getStatistikSiswa($siswaId);

            return $this->successResponse([
                'rekap'    => $rekap,
                'statistik' => $statistik,
            ]);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get rekap siswa: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil rekap absensi');
        }
    }

    /**
     * Get admin dashboard data
     */
    public function getAdminDashboard(?int $pembimbingPklId = null, ?string $from = null, ?string $to = null): array
    {
        try {
            $rekapPembimbing = $this->absensiPklModel->getRekapByPembimbing($from, $to);
            $globalStats = $this->absensiPklDetailModel->getGlobalStats($from, $to);

            // Batch stats for rekapPembimbing (1 query instead of N)
            $pembimbingIds = array_column($rekapPembimbing, 'pembimbing_pkl_id');
            $pembimbingStats = $this->absensiPklDetailModel->getStatsByPembimbingIds($pembimbingIds);

            foreach ($rekapPembimbing as &$item) {
                $id = $item['pembimbing_pkl_id'];
                $item['hadir']            = $pembimbingStats[$id]['hadir'] ?? 0;
                $item['izin']             = $pembimbingStats[$id]['izin'] ?? 0;
                $item['sakit']            = $pembimbingStats[$id]['sakit'] ?? 0;
                $item['alpa']             = $pembimbingStats[$id]['alpa'] ?? 0;
                $item['total']            = $pembimbingStats[$id]['total'] ?? 0;
                $item['persen_kehadiran'] = $pembimbingStats[$id]['persen_kehadiran'] ?? 0;
            }
            unset($item);

            $pembimbingOptions = $this->getPembimbingOptions();

            return $this->successResponse([
                'rekapPembimbing'   => $rekapPembimbing,
                'globalStats'       => $globalStats,
                'pembimbingOptions' => $pembimbingOptions,
            ]);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get admin dashboard: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data dashboard admin');
        }
    }

    /**
     * Get pembimbing options for dropdown
     */
    public function getPembimbingOptions(): array
    {
        $tahunAjaran = get_active_tahun_ajaran();
        $pembimbing = $this->pembimbingPklModel->getByTahunAjaran($tahunAjaran);

        $options = ['' => 'Semua Pembimbing'];
        foreach ($pembimbing as $p) {
            $options[$p['id']] = $p['nama_guru'] . ' - ' . $p['nama_perusahaan'];
        }

        return $options;
    }
}
