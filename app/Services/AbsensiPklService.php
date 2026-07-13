<?php

namespace App\Services;

use App\Models\AbsensiPklModel;
use App\Models\AbsensiPklDetailModel;
use App\Models\PembimbingPklModel;
use App\Models\SiswaPklModel;
use App\Models\SiswaModel;

class AbsensiPklService extends BaseService
{
    protected $absensiPklModel;
    protected $absensiPklDetailModel;
    protected $pembimbingPklModel;
    protected $siswaPklModel;
    protected $siswaModel;

    public function __construct()
    {
        parent::__construct();

        $this->absensiPklModel = new AbsensiPklModel();
        $this->absensiPklDetailModel = new AbsensiPklDetailModel();
        $this->pembimbingPklModel = new PembimbingPklModel();
        $this->siswaPklModel = new SiswaPklModel();
        $this->siswaModel = new SiswaModel();
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
                $item['dispen_count'] = $stats['dispen'];
                $item['persen_kehadiran'] = $stats['persen_kehadiran'];
                $item['can_edit'] = true; // Pembimbing can always edit
                $item['can_delete'] = true;
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

            // Check for duplicate
            if ($this->absensiPklModel->isAlreadyAbsen($data['pembimbing_pkl_id'], $data['tanggal'])) {
                return $this->errorResponse('Absensi untuk tanggal ini sudah dibuat sebelumnya');
            }

            return $this->executeInTransaction(function () use ($data) {
                // Insert header
                $headerData = [
                    'pembimbing_pkl_id' => $data['pembimbing_pkl_id'],
                    'tanggal'           => $data['tanggal'],
                    'keterangan_umum'   => $data['keterangan_umum'] ?? null,
                    'created_by'        => $data['created_by'],
                    'created_at'        => date('Y-m-d H:i:s'),
                ];

                $absensiPklId = $this->absensiPklModel->insert($headerData);

                if (!$absensiPklId) {
                    throw new \RuntimeException('Gagal menyimpan absensi PKL header');
                }

                // Insert detail
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
            return $this->errorResponse('Gagal menyimpan absensi PKL: ' . $e->getMessage());
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
                foreach ($data['siswa'] as $siswaId => $siswaData) {
                    if (empty($siswaId)) {
                        continue;
                    }
                    $this->absensiPklDetailModel->upsertAbsensi($id, (int) $siswaId, [
                        'status'     => $siswaData['status'] ?? 'alpa',
                        'keterangan' => $siswaData['keterangan'] ?? null,
                        'waktu_absen' => date('Y-m-d H:i:s'),
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

            // Get all pembimbing_pkl for this guru
            $pembimbingList = $this->pembimbingPklModel->where('guru_id', $guruId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->findAll();

            $siswa = [];
            foreach ($pembimbingList as $pembimbing) {
                $siswaList = $this->siswaPklModel->select('
                        siswa_pkl.*,
                        siswa.nama_lengkap,
                        siswa.nis,
                        kelas.nama_kelas,
                        tempat_pkl.nama_perusahaan
                    ')
                    ->join('siswa', 'siswa.id = siswa_pkl.siswa_id')
                    ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
                    ->join('tempat_pkl', 'tempat_pkl.id = siswa_pkl.tempat_pkl_id')
                    ->where('siswa_pkl.tempat_pkl_id', $pembimbing['tempat_pkl_id'])
                    ->where('siswa_pkl.tahun_ajaran', $tahunAjaran)
                    ->orderBy('siswa.nama_lengkap', 'ASC')
                    ->findAll();

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
            $absensi = $this->absensiPklModel->getForAdmin($pembimbingPklId, $from, $to);
            $rekapPembimbing = $this->absensiPklModel->getRekapByPembimbing($from, $to);
            $globalStats = $this->absensiPklDetailModel->getGlobalStats($from, $to);
            $recentActivity = $this->absensiPklDetailModel->getRecentActivity(15);

            // Enrich rekap with detail stats
            foreach ($rekapPembimbing as &$item) {
                $stats = $this->absensiPklDetailModel->getStatsByPembimbingPkl($item['pembimbing_pkl_id']);
                $item['stats'] = $stats;
            }
            unset($item);

            // Enrich absensi with stats
            foreach ($absensi as &$item) {
                $stats = $this->absensiPklDetailModel->getDetailStats($item['id']);
                $item['total_siswa'] = $stats['total'];
                $item['hadir_count'] = $stats['hadir'];
                $item['persen_kehadiran'] = $stats['persen_kehadiran'];
            }
            unset($item);

            $pembimbingOptions = $this->getPembimbingOptions();

            return $this->successResponse([
                'absensi'           => $absensi,
                'rekapPembimbing'   => $rekapPembimbing,
                'globalStats'       => $globalStats,
                'recentActivity'    => $recentActivity,
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
