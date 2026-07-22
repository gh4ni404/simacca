<?php

namespace App\Services;

use App\Models\PklCategoryModel;
use App\Models\PklTaskModel;
use App\Models\PklProgressModel;

class PklService extends BaseService
{
    protected PklTaskModel $taskModel;
    protected PklProgressModel $progressModel;
    protected PklCategoryModel $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->taskModel = new PklTaskModel();
        $this->progressModel = new PklProgressModel();
        $this->categoryModel = new PklCategoryModel();
    }

    // ─── Task ────────────────────────────────────────────

    public function createTask(array $data): array
    {
        try {
            $this->db->transStart();

            if (empty($data['siswa_id']) || empty($data['judul'])) {
                return $this->error('Siswa ID dan Judul wajib diisi');
            }

            $id = $this->taskModel->insert($data);
            if (!$id) {
                $this->db->transRollback();
                $errors = $this->taskModel->errors();
                return $this->error('Gagal membuat task: ' . implode(', ', $errors));
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return $this->error('Gagal membuat task');
            }

            return $this->success(['id' => $id, 'message' => 'Task berhasil dibuat']);
        } catch (\Exception $e) {
            $this->db->transRollback();
            $this->logError('createTask', $e);
            return $this->error('Gagal membuat task: ' . $e->getMessage());
        }
    }

    public function getTasksBySiswa(int $siswaId): array
    {
        try {
            $data = $this->taskModel->getBySiswaWithCategory($siswaId);
            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getTasksBySiswa', $e);
            return $this->error('Gagal mengambil data task');
        }
    }

    public function getActiveTasksBySiswa(int $siswaId): array
    {
        try {
            $data = $this->taskModel->getActiveBySiswa($siswaId);
            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getActiveTasksBySiswa', $e);
            return $this->error('Gagal mengambil data task');
        }
    }

    public function getAllTasksBySiswa(int $siswaId): array
    {
        try {
            $data = $this->taskModel->getBySiswaWithCategory($siswaId);
            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getAllTasksBySiswa', $e);
            return $this->error('Gagal mengambil data task');
        }
    }

    public function getTaskById(int $id): array
    {
        try {
            $data = $this->taskModel->getWithProgress($id);
            if (!$data) {
                return $this->error('Task tidak ditemukan', 404);
            }
            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getTaskById', $e);
            return $this->error('Gagal mengambil data task');
        }
    }

    // ─── Progress ────────────────────────────────────────

    public function createProgress(array $data): array
    {
        try {
            $this->db->transStart();

            if (empty($data['task_id']) || empty($data['tanggal']) || empty($data['deskripsi'])) {
                return $this->error('Task, tanggal, dan deskripsi wajib diisi');
            }

            $task = $this->taskModel->find($data['task_id']);
            if (!$task) {
                return $this->error('Task tidak ditemukan');
            }

            if (!isset($data['status'])) {
                $data['status'] = 'submitted';
            }

            $id = $this->progressModel->insert($data);
            if (!$id) {
                $this->db->transRollback();
                $errors = $this->progressModel->errors();
                return $this->error('Gagal menyimpan progress: ' . implode(', ', $errors));
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return $this->error('Gagal menyimpan progress');
            }

            return $this->success(['id' => $id, 'message' => 'Progress berhasil disimpan']);
        } catch (\Exception $e) {
            $this->db->transRollback();
            $this->logError('createProgress', $e);
            return $this->error('Gagal menyimpan progress: ' . $e->getMessage());
        }
    }

    public function updateProgress(int $id, array $data): array
    {
        try {
            $this->db->transStart();

            $progress = $this->progressModel->find($id);
            if (!$progress) {
                return $this->error('Progress tidak ditemukan', 404);
            }

            if ($progress['status'] === 'approved') {
                return $this->error('Progress yang sudah disetujui tidak dapat diubah');
            }

            $success = $this->progressModel->update($id, $data);
            if (!$success) {
                $this->db->transRollback();
                $errors = $this->progressModel->errors();
                return $this->error('Gagal mengupdate progress: ' . implode(', ', $errors));
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return $this->error('Gagal mengupdate progress');
            }

            return $this->success(['id' => $id, 'message' => 'Progress berhasil diupdate']);
        } catch (\Exception $e) {
            $this->db->transRollback();
            $this->logError('updateProgress', $e);
            return $this->error('Gagal mengupdate progress: ' . $e->getMessage());
        }
    }

    public function deleteProgress(int $id): array
    {
        try {
            $this->db->transStart();

            $progress = $this->progressModel->find($id);
            if (!$progress) {
                return $this->error('Progress tidak ditemukan', 404);
            }

            if ($progress['status'] === 'approved') {
                return $this->error('Progress yang sudah disetujui tidak dapat dihapus');
            }

            if (!empty($progress['foto'])) {
                $fotoPath = WRITEPATH . 'uploads/pkl_progress/' . $progress['foto'];
                if (file_exists($fotoPath)) {
                    @unlink($fotoPath);
                }
            }

            $success = $this->progressModel->delete($id);
            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal menghapus progress');
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return $this->error('Gagal menghapus progress');
            }

            return $this->success(['message' => 'Progress berhasil dihapus']);
        } catch (\Exception $e) {
            $this->db->transRollback();
            $this->logError('deleteProgress', $e);
            return $this->error('Gagal menghapus progress: ' . $e->getMessage());
        }
    }

    public function getProgressById(int $id): array
    {
        try {
            $db = \Config\Database::connect();
            $sql = "SELECT pp.*, pt.judul AS nama_task, pt.siswa_id,
                           pc.nama AS kategori_nama
                    FROM pkl_progress pp
                    JOIN pkl_tasks pt ON pt.id = pp.task_id
                    LEFT JOIN pkl_categories pc ON pc.id = pt.kategori_id
                    WHERE pp.id = ? AND pp.deleted_at IS NULL";
            $data = $db->query($sql, [$id])->getRowArray();

            if (!$data) {
                return $this->error('Progress tidak ditemukan', 404);
            }
            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getProgressById', $e);
            return $this->error('Gagal mengambil data progress');
        }
    }

    public function getTodayProgress(int $siswaId): array
    {
        try {
            $data = $this->progressModel->getTodayProgress($siswaId);
            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getTodayProgress', $e);
            return $this->error('Gagal mengambil data progress hari ini');
        }
    }

    public function getTimeline(int $siswaId): array
    {
        try {
            $data = $this->progressModel->getTimeline($siswaId);
            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getTimeline', $e);
            return $this->error('Gagal mengambil timeline');
        }
    }

    public function getProgressByTanggal(int $siswaId, string $tanggal): array
    {
        try {
            $data = $this->progressModel->getByTanggal($siswaId, $tanggal);
            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getProgressByTanggal', $e);
            return $this->error('Gagal mengambil data progress');
        }
    }

    public function getProgressByTask(int $taskId, ?array $statuses = null): array
    {
        try {
            if (!empty($statuses)) {
                $data = $this->progressModel->where('task_id', $taskId)
                    ->whereIn('status', $statuses)
                    ->orderBy('tanggal', 'ASC')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
            } else {
                $data = $this->progressModel->getByTask($taskId);
            }
            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getProgressByTask', $e);
            return $this->error('Gagal mengambil data progress');
        }
    }

    public function getJurnalByTanggal(int $siswaId, ?string $startDate = null, ?string $endDate = null, ?array $statuses = null): array
    {
        try {
            $db = \Config\Database::connect();
            $sql = "SELECT pp.*, pt.judul AS nama_task, pc.nama AS kategori_nama
                    FROM pkl_progress pp
                    JOIN pkl_tasks pt ON pt.id = pp.task_id
                    LEFT JOIN pkl_categories pc ON pc.id = pt.kategori_id
                    WHERE pt.siswa_id = ? AND pp.deleted_at IS NULL AND pt.deleted_at IS NULL";
            $binds = [$siswaId];

            if ($startDate) {
                $sql .= ' AND pp.tanggal >= ?';
                $binds[] = $startDate;
            }
            if ($endDate) {
                $sql .= ' AND pp.tanggal <= ?';
                $binds[] = $endDate;
            }

            if (!empty($statuses)) {
                $placeholders = implode(',', array_fill(0, count($statuses), '?'));
                $sql .= " AND pp.status IN ($placeholders)";
                $binds = array_merge($binds, $statuses);
            }

            $sql .= ' ORDER BY pp.tanggal ASC, pp.created_at ASC';
            $data = $db->query($sql, $binds)->getResultArray();
            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getJurnalByTanggal', $e);
            return $this->error('Gagal mengambil jurnal');
        }
    }

    public function getCatatanByTask(int $siswaId): array
    {
        try {
            $db = \Config\Database::connect();
            $sql = "SELECT pt.id, pt.judul, pc.nama AS kategori_nama,
                           COUNT(pp.id) AS total_progress,
                           MIN(pp.tanggal) AS tanggal_mulai,
                           MAX(pp.tanggal) AS tanggal_selesai,
                           SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS approved_count
                    FROM pkl_tasks pt
                    LEFT JOIN pkl_categories pc ON pc.id = pt.kategori_id
                    LEFT JOIN pkl_progress pp ON pp.task_id = pt.id AND pp.deleted_at IS NULL
                    WHERE pt.siswa_id = ? AND pt.deleted_at IS NULL
                    GROUP BY pt.id, pt.judul, pc.nama
                    ORDER BY pt.created_at ASC";
            $data = $db->query($sql, [$siswaId])->getResultArray();
            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getCatatanByTask', $e);
            return $this->error('Gagal mengambil catatan');
        }
    }

    // ─── Verification ────────────────────────────────────

    public function verify(int $id, int $userId, string $status, ?string $catatan = null): array
    {
        try {
            $this->db->transStart();

            $progress = $this->progressModel->find($id);
            if (!$progress) {
                return $this->error('Progress tidak ditemukan', 404);
            }

            if ($progress['status'] !== 'submitted' && $progress['status'] !== 'draft' && $progress['status'] !== 'verified_by_instruktur') {
                return $this->error('Progress ini sudah diverifikasi sebelumnya');
            }

            $data = [
                'status' => $status,
                'verified_by' => $userId,
                'verified_at' => date('Y-m-d H:i:s'),
                'catatan_pembimbing' => $catatan ?? '',
            ];

            $success = $this->progressModel->update($id, $data);
            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal memverifikasi progress');
            }

            if ($status === 'revision') {
                $task = $this->db->table('pkl_tasks')->where('id', $progress['task_id'])->where('deleted_at IS NULL', null, false)->get()->getRowArray();
                if ($task && $task['status'] === 'completed') {
                    $this->db->table('pkl_tasks')->where('id', $progress['task_id'])->update(['status' => 'active']);
                }
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return $this->error('Gagal memverifikasi progress');
            }

            return $this->success(['id' => $id, 'message' => 'Progress berhasil ' . $status]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            $this->logError('verify', $e);
            return $this->error('Gagal memverifikasi progress: ' . $e->getMessage());
        }
    }

    public function cancelVerification(int $id): array
    {
        try {
            $this->db->transStart();

            $progress = $this->progressModel->find($id);
            if (!$progress) {
                return $this->error('Progress tidak ditemukan', 404);
            }

            if ($progress['status'] === 'draft') {
                return $this->error('Progress ini belum diverifikasi');
            }

            $data = [
                'status' => $progress['instruktur_verified_by'] ? 'verified_by_instruktur' : 'submitted',
                'verified_by' => null,
                'verified_at' => null,
                'catatan_pembimbing' => '',
            ];

            $success = $this->progressModel->update($id, $data);
            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal membatalkan verifikasi');
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return $this->error('Gagal membatalkan verifikasi');
            }

            return $this->success(['message' => 'Verifikasi progress berhasil dibatalkan']);
        } catch (\Exception $e) {
            $this->db->transRollback();
            $this->logError('cancelVerification', $e);
            return $this->error('Gagal membatalkan verifikasi: ' . $e->getMessage());
        }
    }

    // ─── Guru/Pembimbing ─────────────────────────────────

    public function getGroupedBySiswaForPembimbing(): array
    {
        try {
            $data = $this->progressModel->getGroupedBySiswaForPembimbing();
            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getGroupedBySiswaForPembimbing', $e);
            return $this->error('Gagal mengambil data progress');
        }
    }

    public function getWeekReadiness(int $siswaId, string $weekStart, string $weekEnd, int $requiredDays): array
    {
        try {
            $db = \Config\Database::connect();

            $sql = "SELECT pp.tanggal, pp.status,
                           pp.verified_by, pp.catatan_pembimbing,
                           pp.instruktur_verified_by, pp.catatan_instruktur
                    FROM pkl_progress pp
                    JOIN pkl_tasks pt ON pt.id = pp.task_id
                    WHERE pt.siswa_id = ?
                      AND pp.deleted_at IS NULL
                      AND pt.deleted_at IS NULL
                      AND pp.tanggal >= ?
                      AND pp.tanggal <= ?
                    ORDER BY pp.tanggal ASC";

            $progress = $db->query($sql, [$siswaId, $weekStart, $weekEnd])->getResultArray();

            $dayStatus = [];
            foreach ($progress as $p) {
                $date = $p['tanggal'];
                if (!isset($dayStatus[$date])) {
                    $dayStatus[$date] = ['total' => 0, 'verified' => 0];
                }
                $dayStatus[$date]['total']++;
                $bothVerified = $p['status'] === 'approved'
                    && !empty($p['verified_by'])
                    && !empty($p['catatan_pembimbing'])
                    && !empty($p['instruktur_verified_by'])
                    && !empty($p['catatan_instruktur']);
                if ($bothVerified) {
                    $dayStatus[$date]['verified']++;
                }
            }

            $start = new \DateTime($weekStart);
            $end = new \DateTime($weekEnd);
            $end->modify('+1 day');
            $interval = new \DateInterval('P1D');
            $period = new \DatePeriod($start, $interval, $end);

            $readyDays = 0;
            $totalDays = 0;

            foreach ($period as $dt) {
                $dayOfWeek = (int) $dt->format('N');
                if ($dayOfWeek > $requiredDays) continue;

                $totalDays++;
                $dateStr = $dt->format('Y-m-d');

                if (isset($dayStatus[$dateStr]) && $dayStatus[$dateStr]['total'] > 0) {
                    $d = $dayStatus[$dateStr];
                    if ($d['total'] === $d['verified']) $readyDays++;
                }
            }

            $targetDays = min($requiredDays, $totalDays);
            $weekReady = ($readyDays >= $targetDays && $targetDays > 0);

            return [
                'week_ready' => $weekReady,
                'ready_days' => $readyDays,
                'required_days' => $requiredDays,
                'total_workdays' => $totalDays,
            ];
        } catch (\Exception $e) {
            log_message('error', '[PKL] getWeekReadiness error: ' . $e->getMessage());
            return [
                'week_ready' => false,
                'ready_days' => 0,
                'required_days' => $requiredDays,
                'total_workdays' => 0,
            ];
        }
    }

    // ─── Categories ──────────────────────────────────────

    public function getCategories(): array
    {
        try {
            $data = $this->categoryModel->getDropdown();
            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getCategories', $e);
            return $this->error('Gagal mengambil kategori');
        }
    }

    // ─── Statistics ──────────────────────────────────────

    public function getStatistics(int $siswaId): array
    {
        try {
            $db = \Config\Database::connect();
            $sql = "SELECT
                        COUNT(DISTINCT pp.task_id) AS total_tasks,
                        COUNT(pp.id) AS total_progress,
                        SUM(CASE WHEN pp.status = 'draft' THEN 1 ELSE 0 END) AS draft,
                        SUM(CASE WHEN pp.status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                        SUM(CASE WHEN pp.status = 'verified_by_instruktur' THEN 1 ELSE 0 END) AS verified_by_instruktur,
                        SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS approved,
                        SUM(CASE WHEN pp.status = 'revision' THEN 1 ELSE 0 END) AS revision,
                        SUM(CASE WHEN pp.status = 'approved'
                            AND pp.instruktur_verified_by IS NOT NULL
                            AND pp.verified_by IS NOT NULL
                            AND pp.catatan_instruktur IS NOT NULL AND pp.catatan_instruktur != ''
                            AND pp.catatan_pembimbing IS NOT NULL AND pp.catatan_pembimbing != ''
                            THEN 1 ELSE 0 END) AS fully_verified
                    FROM pkl_tasks pt
                    LEFT JOIN pkl_progress pp ON pp.task_id = pt.id AND pp.deleted_at IS NULL
                    WHERE pt.siswa_id = ? AND pt.deleted_at IS NULL";

            $result = $db->query($sql, [$siswaId])->getRowArray();
            return $this->success($result ?: [
                'total_tasks' => 0, 'total_progress' => 0,
                'draft' => 0, 'submitted' => 0, 'verified_by_instruktur' => 0, 'approved' => 0, 'revision' => 0, 'fully_verified' => 0,
            ]);
        } catch (\Exception $e) {
            $this->logError('getStatistics', $e);
            return $this->error('Gagal mengambil statistik');
        }
    }
}
