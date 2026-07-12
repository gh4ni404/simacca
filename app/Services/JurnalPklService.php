<?php

namespace App\Services;

use App\Models\JurnalPklModel;
use App\Models\SiswaModel;
use App\Models\UserModel;

class JurnalPklService extends BaseService
{
    protected JurnalPklModel $jurnalModel;
    protected SiswaModel $siswaModel;
    protected UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->jurnalModel = new JurnalPklModel();
        $this->siswaModel = new SiswaModel();
        $this->userModel = new UserModel();
    }

    public function getAllBySiswa(int $siswaId): array
    {
        try {
            $data = $this->jurnalModel->getBySiswa($siswaId);

            return $this->success($data);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalPklService::getAllBySiswa: ' . $e->getMessage());
            return $this->error('Gagal mengambil data jurnal');
        }
    }

    public function getWeeklyGrouped(int $siswaId, ?string $startDate = null): array
    {
        try {
            if ($startDate === null) {
                helper('setting');
                $startDate = get_jurnal_pkl_start_date();
            }
            $weekBase = $startDate ? get_jurnal_pkl_week_base() : null;
            $data = $this->jurnalModel->getWeeklyGrouped($siswaId, $startDate, $weekBase);

            return $this->success($data);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalPklService::getWeeklyGrouped: ' . $e->getMessage());
            return $this->error('Gagal mengambil data jurnal per minggu');
        }
    }

    public function getByWeek(int $siswaId, int $tahun, int $minggu, ?string $startDate = null): array
    {
        try {
            if ($startDate === null) {
                helper('setting');
                $startDate = get_jurnal_pkl_start_date();
            }
            $weekBase = $startDate ? get_jurnal_pkl_week_base() : null;
            $data = $this->jurnalModel->getBySiswaAndWeek($siswaId, $tahun, $minggu, $startDate, $weekBase);

            return $this->success($data);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalPklService::getByWeek: ' . $e->getMessage());
            return $this->error('Gagal mengambil data jurnal per minggu');
        }
    }

    public function getById(int $id): array
    {
        try {
            $data = $this->jurnalModel->find($id);
            if (!$data) {
                return $this->error('Jurnal tidak ditemukan', 404);
            }

            return $this->success($data);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalPklService::getById: ' . $e->getMessage());
            return $this->error('Gagal mengambil data jurnal');
        }
    }

    public function create(array $data): array
    {
        try {
            $this->db->transStart();

            if (empty($data['siswa_id'])) {
                return $this->error('Siswa ID wajib diisi');
            }

            $siswa = $this->siswaModel->find($data['siswa_id']);
            if (!$siswa) {
                return $this->error('Data siswa tidak ditemukan');
            }

            if (!isset($data['status'])) {
                $data['status'] = 'pending';
            }

            $id = $this->jurnalModel->insert($data);
            if (!$id) {
                $this->db->transRollback();
                $errors = $this->jurnalModel->errors();
                return $this->error('Gagal membuat jurnal: ' . implode(', ', $errors));
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal membuat jurnal');
            }

            return $this->success([
                'id' => $id,
                'message' => 'Jurnal berhasil ditambahkan',
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in JurnalPklService::create: ' . $e->getMessage());
            return $this->error('Gagal membuat jurnal: ' . $e->getMessage());
        }
    }

    public function update(int $id, array $data): array
    {
        try {
            $this->db->transStart();

            $jurnal = $this->jurnalModel->find($id);
            if (!$jurnal) {
                return $this->error('Jurnal tidak ditemukan', 404);
            }

            if ($jurnal['status'] === 'disetujui') {
                return $this->error('Jurnal yang sudah disetujui tidak dapat diubah');
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
                'message' => 'Jurnal berhasil diupdate',
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in JurnalPklService::update: ' . $e->getMessage());
            return $this->error('Gagal mengupdate jurnal: ' . $e->getMessage());
        }
    }

    public function delete(int $id): array
    {
        try {
            $this->db->transStart();

            $jurnal = $this->jurnalModel->find($id);
            if (!$jurnal) {
                return $this->error('Jurnal tidak ditemukan', 404);
            }

            if (!empty($jurnal['foto'])) {
                $fotoPath = WRITEPATH . 'uploads/jurnal_pkl/' . $jurnal['foto'];
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
                'message' => 'Jurnal berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in JurnalPklService::delete: ' . $e->getMessage());
            return $this->error('Gagal menghapus jurnal: ' . $e->getMessage());
        }
    }

    public function verify(int $id, int $userId, string $status, ?string $catatan = null): array
    {
        try {
            $this->db->transStart();

            $jurnal = $this->jurnalModel->find($id);
            if (!$jurnal) {
                return $this->error('Jurnal tidak ditemukan', 404);
            }

            if ($jurnal['status'] !== 'pending' && $jurnal['status'] !== 'tinjau_ulang') {
                return $this->error('Jurnal ini sudah diverifikasi sebelumnya');
            }

            $user = $this->userModel->find($userId);
            if (!$user) {
                return $this->error('User tidak ditemukan');
            }

            $data = [
                'status' => $status,
                'verified_by' => $userId,
                'verified_at' => date('Y-m-d H:i:s'),
                'catatan_pembimbing' => $catatan,
            ];

            $success = $this->jurnalModel->update($id, $data);
            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal memverifikasi jurnal');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal memverifikasi jurnal');
            }

            $statusLabel = [
                'disetujui' => 'disetujui',
                'revisi' => 'revisi',
                'ditolak' => 'ditolak',
            ];

            return $this->success([
                'id' => $id,
                'message' => 'Jurnal berhasil ' . ($statusLabel[$status] ?? $status),
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in JurnalPklService::verify: ' . $e->getMessage());
            return $this->error('Gagal memverifikasi jurnal: ' . $e->getMessage());
        }
    }

    public function cancelVerification(int $id): array
    {
        try {
            $this->db->transStart();

            $jurnal = $this->jurnalModel->find($id);
            if (!$jurnal) {
                return $this->error('Jurnal tidak ditemukan', 404);
            }

            if ($jurnal['status'] === 'pending') {
                return $this->error('Jurnal ini belum diverifikasi');
            }

            $data = [
                'status' => 'pending',
                'verified_by' => null,
                'verified_at' => null,
                'catatan_pembimbing' => null,
            ];

            $success = $this->jurnalModel->update($id, $data);
            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal membatalkan verifikasi');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal membatalkan verifikasi');
            }

            return $this->success([
                'id' => $id,
                'message' => 'Verifikasi jurnal berhasil dibatalkan',
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in JurnalPklService::cancelVerification: ' . $e->getMessage());
            return $this->error('Gagal membatalkan verifikasi: ' . $e->getMessage());
        }
    }

    public function getStatistics(int $siswaId): array
    {
        try {
            $stats = $this->jurnalModel->getStatistics($siswaId);

            return $this->success($stats);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalPklService::getStatistics: ' . $e->getMessage());
            return $this->error('Gagal mengambil statistik');
        }
    }

    public function getPendingByPembimbing(int $guruId): array
    {
        try {
            $data = $this->jurnalModel->getPendingByPembimbing($guruId);

            return $this->success($data);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalPklService::getPendingByPembimbing: ' . $e->getMessage());
            return $this->error('Gagal mengambil data jurnal');
        }
    }

    public function getGroupedBySiswaForPembimbing(int $guruId): array
    {
        try {
            $rawData = $this->jurnalModel->getPendingByPembimbing($guruId);

            $grouped = [];
            $stats = [
                'total_siswa' => 0,
                'total_jurnal' => 0,
                'pending' => 0,
                'disetujui' => 0,
                'revisi' => 0,
                'ditolak' => 0,
                'tinjau_ulang' => 0,
            ];

            foreach ($rawData as $row) {
                $siswaId = $row['siswa_id'];
                $stats['total_jurnal']++;
                $stats[$row['status']] = ($stats[$row['status']] ?? 0) + 1;

                if (!isset($grouped[$siswaId])) {
                    $grouped[$siswaId] = [
                        'siswa_id' => $siswaId,
                        'nama_siswa' => $row['nama_siswa'],
                        'nis' => $row['nis'],
                        'nama_kelas' => $row['nama_kelas'],
                        'jurnal' => [],
                        'pending_count' => 0,
                    ];
                    $stats['total_siswa']++;
                }

                $grouped[$siswaId]['jurnal'][] = $row;

                if ($row['status'] === 'pending') {
                    $grouped[$siswaId]['pending_count']++;
                }
            }

            uasort($grouped, fn($a, $b) => $b['pending_count'] <=> $a['pending_count']);

            return $this->success([
                'grouped' => array_values($grouped),
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in JurnalPklService::getGroupedBySiswaForPembimbing: ' . $e->getMessage());
            return $this->error('Gagal mengambil data jurnal');
        }
    }
}
