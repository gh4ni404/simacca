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
            $data = $this->progressModel
                ->select('pkl_progress.*, pkl_tasks.judul AS nama_task, pkl_tasks.siswa_id, pkl_categories.nama AS kategori_nama')
                ->join('pkl_tasks', 'pkl_tasks.id = pkl_progress.task_id AND pkl_tasks.deleted_at IS NULL')
                ->join('pkl_categories', 'pkl_categories.id = pkl_tasks.kategori_id', 'left')
                ->where('pkl_progress.id', $id)
                ->first();

            if (!$data) {
                return $this->error('Progress tidak ditemukan', 404);
            }
            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getProgressById', $e);
            return $this->error('Gagal mengambil data progress');
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
            $builder = $this->progressModel
                ->select('pkl_progress.*, pkl_tasks.judul AS nama_task, pkl_categories.nama AS kategori_nama')
                ->join('pkl_tasks', 'pkl_tasks.id = pkl_progress.task_id AND pkl_tasks.deleted_at IS NULL')
                ->join('pkl_categories', 'pkl_categories.id = pkl_tasks.kategori_id', 'left')
                ->where('pkl_tasks.siswa_id', $siswaId);

            if ($startDate) {
                $builder->where('pkl_progress.tanggal >=', $startDate);
            }
            if ($endDate) {
                $builder->where('pkl_progress.tanggal <=', $endDate);
            }

            if (!empty($statuses)) {
                $builder->whereIn('pkl_progress.status', $statuses);
            }

            $data = $builder->orderBy('pkl_progress.tanggal', 'ASC')
                ->orderBy('pkl_progress.created_at', 'ASC')
                ->findAll();

            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getJurnalByTanggal', $e);
            return $this->error('Gagal mengambil jurnal');
        }
    }

    public function getCatatanByTask(int $siswaId): array
    {
        try {
            $data = $this->progressModel
                ->select('
                    pkl_tasks.id, pkl_tasks.judul, pkl_categories.nama AS kategori_nama,
                    COUNT(pkl_progress.id) AS total_progress,
                    MIN(pkl_progress.tanggal) AS tanggal_mulai,
                    MAX(pkl_progress.tanggal) AS tanggal_selesai,
                    SUM(CASE WHEN pkl_progress.status = \'approved\' THEN 1 ELSE 0 END) AS approved_count
                ', false)
                ->join('pkl_tasks', 'pkl_tasks.id = pkl_progress.task_id AND pkl_tasks.deleted_at IS NULL')
                ->join('pkl_categories', 'pkl_categories.id = pkl_tasks.kategori_id', 'left')
                ->where('pkl_tasks.siswa_id', $siswaId)
                ->groupBy('pkl_tasks.id, pkl_tasks.judul, pkl_categories.nama')
                ->orderBy('pkl_tasks.created_at', 'ASC')
                ->findAll();

            return $this->success($data);
        } catch (\Exception $e) {
            $this->logError('getCatatanByTask', $e);
            return $this->error('Gagal mengambil catatan');
        }
    }

    // ─── Verification ────────────────────────────────────

    public function verify(int $id, int $userId, string $status, ?string $catatan = null, string $role = 'pembimbing'): array
    {
        try {
            $this->db->transStart();

            $progress = $this->progressModel->find($id);
            if (!$progress) {
                return $this->error('Progress tidak ditemukan', 404);
            }

            if (!in_array($status, ['approved', 'revision'])) {
                return $this->error('Status verifikasi tidak valid');
            }

            $verifiedByField = ($role === 'instruktur') ? 'instruktur_verified_by' : 'verified_by';
            $verifiedAtField = ($role === 'instruktur') ? 'instruktur_verified_at' : 'verified_at';
            $catatanField = ($role === 'instruktur') ? 'catatan_instruktur' : 'catatan_pembimbing';

            // ── Revision ──
            if ($status === 'revision') {
                $data = [
                    'status' => 'revision',
                    'revision_requested_by' => $role,
                    $catatanField => $catatan ?? '',
                    // Clear this party's verification when requesting revision
                    $verifiedByField => null,
                    $verifiedAtField => null,
                ];
                if (!empty($progress['revision_requested_by']) && $progress['revision_requested_by'] !== $role) {
                    $data['revision_requested_by'] = 'both';
                }

                $success = $this->progressModel->update($id, $data);
                if (!$success) {
                    $this->db->transRollback();
                    return $this->error('Gagal merevisi progress');
                }

                $task = $this->db->table('pkl_tasks')->where('id', $progress['task_id'])->where('deleted_at IS NULL', null, false)->get()->getRowArray();
                if ($task && $task['status'] === 'completed') {
                    $this->db->table('pkl_tasks')->where('id', $progress['task_id'])->update(['status' => 'active']);
                }

                $this->db->transComplete();
                return $this->success(['id' => $id, 'message' => 'Progress direvisi']);
            }

            // ── Approval (status === 'approved') ──
            if (!empty($progress[$verifiedByField])) {
                return $this->error('Progress ini sudah diverifikasi oleh ' . $role);
            }

            // Handle revision status specially
            if ($progress['status'] === 'revision') {
                $revisionRequestedBy = $progress['revision_requested_by'];

                // If this party didn't request revision (and it's not 'both'),
                // save their verification but keep status as 'revision'
                if ($revisionRequestedBy !== $role && $revisionRequestedBy !== 'both') {
                    $data = [
                        $verifiedByField => $userId,
                        $verifiedAtField => date('Y-m-d H:i:s'),
                        $catatanField => $catatan ?? '',
                    ];

                    $success = $this->progressModel->update($id, $data);
                    if (!$success) {
                        $this->db->transRollback();
                        return $this->error('Gagal memverifikasi progress');
                    }

                    $this->db->transComplete();
                    if ($this->db->transStatus() === false) {
                        return $this->error('Gagal memverifikasi progress');
                    }

                    return $this->success(['id' => $id, 'message' => 'Verifikasi disimpan. Menunggu revisi dari siswa.']);
                }

                // This party requested revision and is now approving the revision
                // Check if the other party has verified
                $otherVerified = ($role === 'instruktur')
                    ? (!empty($progress['verified_by']) && !empty($progress['catatan_pembimbing']))
                    : (!empty($progress['instruktur_verified_by']) && !empty($progress['catatan_instruktur']));

                $newStatus = $otherVerified ? 'approved' : 'verified';

                $data = [
                    'status' => $newStatus,
                    $verifiedByField => $userId,
                    $verifiedAtField => date('Y-m-d H:i:s'),
                    $catatanField => $catatan ?? '',
                    'revision_requested_by' => null,
                ];

                $success = $this->progressModel->update($id, $data);
                if (!$success) {
                    $this->db->transRollback();
                    return $this->error('Gagal memverifikasi progress');
                }

                $this->db->transComplete();
                if ($this->db->transStatus() === false) {
                    return $this->error('Gagal memverifikasi progress');
                }

                return $this->success(['id' => $id, 'message' => 'Progress berhasil diverifikasi']);
            }

            // Normal approval flow (not revision status)
            $pembimbingVerified = !empty($progress['verified_by']) && !empty($progress['catatan_pembimbing']);
            $instrukturVerified = !empty($progress['instruktur_verified_by']) && !empty($progress['catatan_instruktur']);

            $verifiedCount = ($pembimbingVerified ? 1 : 0) + ($instrukturVerified ? 1 : 0);
            $newStatus = ($verifiedCount >= 1) ? 'approved' : 'verified';

            $data = [
                'status' => $newStatus,
                $verifiedByField => $userId,
                $verifiedAtField => date('Y-m-d H:i:s'),
                $catatanField => $catatan ?? '',
                'revision_requested_by' => null,
            ];

            $success = $this->progressModel->update($id, $data);
            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal memverifikasi progress');
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return $this->error('Gagal memverifikasi progress');
            }

            return $this->success(['id' => $id, 'message' => 'Progress berhasil diverifikasi']);
        } catch (\Exception $e) {
            $this->db->transRollback();
            $this->logError('verify', $e);
            return $this->error('Gagal memverifikasi progress: ' . $e->getMessage());
        }
    }

    public function cancelVerification(int $id, string $role = 'pembimbing'): array
    {
        try {
            $this->db->transStart();

            $progress = $this->progressModel->find($id);
            if (!$progress) {
                return $this->error('Progress tidak ditemukan', 404);
            }

            $verifiedByField = ($role === 'instruktur') ? 'instruktur_verified_by' : 'verified_by';
            $verifiedAtField = ($role === 'instruktur') ? 'instruktur_verified_at' : 'verified_at';
            $catatanField = ($role === 'instruktur') ? 'catatan_instruktur' : 'catatan_pembimbing';

            $hasVerified = !empty($progress[$verifiedByField]);
            $hasRequestedRevision = ($progress['revision_requested_by'] === $role || $progress['revision_requested_by'] === 'both');

            if (!$hasVerified && !$hasRequestedRevision) {
                return $this->error('Tidak ada verifikasi atau revisi yang dapat dibatalkan');
            }

            // ── Cancel revision request ──
            if ($hasRequestedRevision && !$hasVerified) {
                if ($progress['revision_requested_by'] === 'both') {
                    $newRevisionBy = $role;
                } else {
                    $newRevisionBy = null;
                }

                $data = ['revision_requested_by' => $newRevisionBy];

                $otherVerified = ($role === 'instruktur')
                    ? (!empty($progress['verified_by']) && !empty($progress['catatan_pembimbing']))
                    : (!empty($progress['instruktur_verified_by']) && !empty($progress['catatan_instruktur']));
                $otherRequestedRevision = ($role === 'instruktur')
                    ? ($progress['revision_requested_by'] === 'pembimbing')
                    : ($progress['revision_requested_by'] === 'instruktur');

                if ($otherVerified) {
                    $data['status'] = 'verified';
                } elseif (!$otherRequestedRevision && $newRevisionBy === null) {
                    $data['status'] = 'submitted';
                }

                $success = $this->progressModel->update($id, $data);
                if (!$success) {
                    $this->db->transRollback();
                    return $this->error('Gagal membatalkan revisi');
                }

                $this->db->transComplete();
                return $this->success(['message' => 'Revisi berhasil dibatalkan']);
            }

            // ── Cancel verification ──
            $data = [
                $verifiedByField => null,
                $verifiedAtField => null,
                $catatanField => '',
            ];

            $otherVerified = ($role === 'instruktur')
                ? (!empty($progress['verified_by']) && !empty($progress['catatan_pembimbing']))
                : (!empty($progress['instruktur_verified_by']) && !empty($progress['catatan_instruktur']));

            $otherRequestedRevision = ($role === 'instruktur')
                ? ($progress['revision_requested_by'] === 'pembimbing')
                : ($progress['revision_requested_by'] === 'instruktur');

            if ($otherVerified) {
                $data['status'] = 'verified';
            } elseif ($otherRequestedRevision) {
                // Other party has an active revision request — keep revision status
                $data['status'] = 'revision';
            } else {
                $data['status'] = 'submitted';
            }

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

    // ─── Ketua Jurusan Actions ───────────────────────────

    /**
     * Cancel verification by ketua jurusan for approved journals without catatan
     */
    public function cancelVerificationForKetuaJurusan(int $id): array
    {
        try {
            $this->db->transStart();

            $progress = $this->progressModel->find($id);
            if (!$progress) {
                return $this->error('Progress tidak ditemukan', 404);
            }

            if ($progress['status'] !== 'approved') {
                return $this->error('Hanya jurnal dengan status approved yang dapat dibatalkan');
            }

            $pembimbingVerified = !empty($progress['verified_by']) && !empty($progress['catatan_pembimbing']);
            $instrukturVerified = !empty($progress['instruktur_verified_by']) && !empty($progress['catatan_instruktur']);

            if ($pembimbingVerified && $instrukturVerified) {
                return $this->error('Jurnal ini sudah memiliki catatan dari kedua belah pihak');
            }

            $data = [];

            if (!$pembimbingVerified) {
                $data['verified_by'] = null;
                $data['verified_at'] = null;
            }

            if (!$instrukturVerified) {
                $data['instruktur_verified_by'] = null;
                $data['instruktur_verified_at'] = null;
            }

            if ($pembimbingVerified) {
                $data['status'] = 'verified';
            } else {
                $data['status'] = 'submitted';
            }

            $success = $this->progressModel->update($id, $data);
            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal membatalkan verifikasi');
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return $this->error('Gagal membatalkan verifikasi');
            }

            return $this->success(['message' => 'Verifikasi progress berhasil dibatalkan oleh Ketua Jurusan']);
        } catch (\Exception $e) {
            $this->db->transRollback();
            $this->logError('cancelVerificationForKetuaJurusan', $e);
            return $this->error('Gagal membatalkan verifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Add catatan on behalf of pembimbing or instruktur by ketua jurusan
     */
    public function addCatatanOnBehalf(int $id, string $role, string $catatan): array
    {
        try {
            $this->db->transStart();

            $progress = $this->progressModel->find($id);
            if (!$progress) {
                return $this->error('Progress tidak ditemukan', 404);
            }

            if ($progress['status'] !== 'approved') {
                return $this->error('Hanya jurnal dengan status approved yang dapat ditambahkan catatan');
            }

            if (!in_array($role, ['pembimbing', 'instruktur'])) {
                return $this->error('Role tidak valid');
            }

            $catatanField = ($role === 'instruktur') ? 'catatan_instruktur' : 'catatan_pembimbing';
            $verifiedByField = ($role === 'instruktur') ? 'instruktur_verified_by' : 'verified_by';
            $verifiedAtField = ($role === 'instruktur') ? 'instruktur_verified_at' : 'verified_at';

            if (!empty($progress[$catatanField])) {
                return $this->error('Catatan ' . $role . ' sudah ada');
            }

            if (empty($progress[$verifiedByField])) {
                return $this->error('Verifikasi ' . $role . ' belum ada');
            }

            $data = [
                $catatanField => $catatan,
            ];

            $success = $this->progressModel->update($id, $data);
            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal menyimpan catatan');
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return $this->error('Gagal menyimpan catatan');
            }

            return $this->success(['message' => 'Catatan berhasil ditambahkan oleh Ketua Jurusan']);
        } catch (\Exception $e) {
            $this->db->transRollback();
            $this->logError('addCatatanOnBehalf', $e);
            return $this->error('Gagal menyimpan catatan: ' . $e->getMessage());
        }
    }

    /**
     * Edit existing catatan on behalf of pembimbing or instruktur by ketua jurusan
     */
    public function editCatatanOnBehalf(int $id, string $role, string $catatan): array
    {
        try {
            $this->db->transStart();

            $progress = $this->progressModel->find($id);
            if (!$progress) {
                return $this->error('Progress tidak ditemukan', 404);
            }

            if ($progress['status'] !== 'approved') {
                return $this->error('Hanya jurnal dengan status approved yang dapat diedit');
            }

            if (!in_array($role, ['pembimbing', 'instruktur'])) {
                return $this->error('Role tidak valid');
            }

            $catatanField = ($role === 'instruktur') ? 'catatan_instruktur' : 'catatan_pembimbing';

            if (empty($progress[$catatanField])) {
                return $this->error('Tidak ada catatan yang dapat diedit');
            }

            $data = [
                $catatanField => $catatan,
            ];

            $success = $this->progressModel->update($id, $data);
            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal mengedit catatan');
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return $this->error('Gagal mengedit catatan');
            }

            return $this->success(['message' => 'Catatan berhasil diedit oleh Ketua Jurusan']);
        } catch (\Exception $e) {
            $this->db->transRollback();
            $this->logError('editCatatanOnBehalf', $e);
            return $this->error('Gagal mengedit catatan: ' . $e->getMessage());
        }
    }

    /**
     * Verify on behalf of pembimbing or instruktur by ketua jurusan
     */
    public function verifyOnBehalf(int $id, string $role, string $catatan, int $userId): array
    {
        try {
            $this->db->transStart();

            $progress = $this->progressModel->find($id);
            if (!$progress) {
                return $this->error('Progress tidak ditemukan', 404);
            }

            if ($progress['status'] !== 'approved') {
                return $this->error('Hanya jurnal dengan status approved yang dapat diverifikasi');
            }

            if (!in_array($role, ['pembimbing', 'instruktur'])) {
                return $this->error('Role tidak valid');
            }

            $catatanField = ($role === 'instruktur') ? 'catatan_instruktur' : 'catatan_pembimbing';
            $verifiedByField = ($role === 'instruktur') ? 'instruktur_verified_by' : 'verified_by';
            $verifiedAtField = ($role === 'instruktur') ? 'instruktur_verified_at' : 'verified_at';

            if (!empty($progress[$verifiedByField])) {
                return $this->error('Verifikasi ' . $role . ' sudah ada');
            }

            $data = [
                $catatanField => $catatan,
                $verifiedByField => $userId,
                $verifiedAtField => date('Y-m-d H:i:s'),
            ];

            $success = $this->progressModel->update($id, $data);
            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal menyimpan verifikasi');
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return $this->error('Gagal menyimpan verifikasi');
            }

            return $this->success(['message' => 'Verifikasi berhasil ditambahkan oleh Ketua Jurusan']);
        } catch (\Exception $e) {
            $this->db->transRollback();
            $this->logError('verifyOnBehalf', $e);
            return $this->error('Gagal menyimpan verifikasi: ' . $e->getMessage());
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
            $totalTasks = $this->taskModel->where('siswa_id', $siswaId)->countAllResults();

            $stats = $this->progressModel
                ->select('
                    COUNT(pkl_progress.id) AS total_progress,
                    SUM(CASE WHEN pkl_progress.status = \'draft\' THEN 1 ELSE 0 END) AS draft,
                    SUM(CASE WHEN pkl_progress.status = \'submitted\' THEN 1 ELSE 0 END) AS submitted,
                    SUM(CASE WHEN pkl_progress.status = \'verified\' THEN 1 ELSE 0 END) AS verified,
                    SUM(CASE WHEN pkl_progress.status = \'approved\' THEN 1 ELSE 0 END) AS approved,
                    SUM(CASE WHEN pkl_progress.status = \'revision\' THEN 1 ELSE 0 END) AS revision,
                    SUM(CASE WHEN pkl_progress.status = \'approved\'
                        AND pkl_progress.instruktur_verified_by IS NOT NULL
                        AND pkl_progress.verified_by IS NOT NULL
                        AND pkl_progress.catatan_instruktur IS NOT NULL AND pkl_progress.catatan_instruktur != \'\'
                        AND pkl_progress.catatan_pembimbing IS NOT NULL AND pkl_progress.catatan_pembimbing != \'\'
                        THEN 1 ELSE 0 END) AS fully_verified
                ', false)
                ->join('pkl_tasks', 'pkl_tasks.id = pkl_progress.task_id AND pkl_tasks.deleted_at IS NULL')
                ->where('pkl_tasks.siswa_id', $siswaId)
                ->first();

            return $this->success([
                'total_tasks' => $totalTasks,
                'total_progress' => (int)($stats['total_progress'] ?? 0),
                'draft' => (int)($stats['draft'] ?? 0),
                'submitted' => (int)($stats['submitted'] ?? 0),
                'verified' => (int)($stats['verified'] ?? 0),
                'approved' => (int)($stats['approved'] ?? 0),
                'revision' => (int)($stats['revision'] ?? 0),
                'fully_verified' => (int)($stats['fully_verified'] ?? 0),
            ]);
        } catch (\Exception $e) {
            $this->logError('getStatistics', $e);
            return $this->error('Gagal mengambil statistik');
        }
    }
}
