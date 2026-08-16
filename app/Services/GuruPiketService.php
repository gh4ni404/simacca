<?php

namespace App\Services;

use App\Models\GuruPiketModel;

/**
 * Guru Piket Service
 *
 * Handles all business logic related to Guru Piket (Teacher Duty) management:
 * - CRUD operations
 * - Day-based assignments (no shifts, no dates)
 * - Tied to active academic year (tahun ajaran) and semester
 */
class GuruPiketService extends BaseService
{
    protected $guruPiketModel;

    public function __construct()
    {
        parent::__construct();
        $this->guruPiketModel = new GuruPiketModel();
    }

    /**
     * Get all guru piket grouped by day for a specific tahun ajaran and semester
     */
    public function getAllGroupedByHari(string $tahunAjaran, string $semester): array
    {
        try {
            $grouped = $this->guruPiketModel->getGroupedByHari($tahunAjaran, $semester);
            $stats = $this->guruPiketModel->getStats($tahunAjaran, $semester);

            return $this->successResponse([
                'grouped' => $grouped,
                'stats'   => $stats,
            ]);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get guru piket grouped: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data jadwal piket');
        }
    }

    /**
     * Get guru piket by ID
     */
    public function getById(int $id): array
    {
        try {
            $piket = $this->guruPiketModel->select('guru_piket.*, guru.nama_lengkap, guru.nip')
                ->join('guru', 'guru.id = guru_piket.guru_id')
                ->where('guru_piket.id', $id)
                ->first();

            if (!$piket) {
                return $this->errorResponse('Jadwal piket tidak ditemukan');
            }

            return $this->successResponse($piket);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get guru piket by ID: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data jadwal piket');
        }
    }

    /**
     * Create new guru piket assignment
     */
    public function create(array $data): array
    {
        $rules = [
            'guru_id'      => 'required|integer',
            'tahun_ajaran' => 'required',
            'semester'     => 'required|in_list[ganjil,genap]',
            'hari'         => 'required|in_list[senin,selasa,rabu,kamis,jumat,sabtu]',
            'keterangan'   => 'permit_empty|string',
            'is_active'    => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        // Check if guru already assigned on this day
        if ($this->guruPiketModel->isGuruAssigned($data['guru_id'], $data['hari'], $data['tahun_ajaran'], $data['semester'])) {
            return $this->errorResponse('Guru ini sudah dijadwalkan piket pada hari ' . ucfirst($data['hari']));
        }

        return $this->executeInTransaction(function () use ($data) {
            $piketData = [
                'guru_id'      => $data['guru_id'],
                'tahun_ajaran' => $data['tahun_ajaran'],
                'semester'     => $data['semester'],
                'hari'         => $data['hari'],
                'keterangan'   => $data['keterangan'] ?? null,
                'is_active'    => $data['is_active'] ?? 1,
                'created_at'   => date('Y-m-d H:i:s'),
            ];

            $id = $this->guruPiketModel->insert($piketData);

            if (!$id) {
                throw new \Exception('Gagal membuat jadwal piket');
            }

            $this->log('info', "Guru piket created: guru_id={$data['guru_id']}, hari={$data['hari']}, semester={$data['semester']}, tahun_ajaran={$data['tahun_ajaran']}");

            return ['id' => $id];
        });
    }

    /**
     * Update guru piket assignment
     */
    public function update(int $id, array $data): array
    {
        $piket = $this->guruPiketModel->find($id);

        if (!$piket) {
            return $this->errorResponse('Jadwal piket tidak ditemukan');
        }

        $rules = [
            'guru_id'      => 'required|integer',
            'tahun_ajaran' => 'required',
            'semester'     => 'required|in_list[ganjil,genap]',
            'hari'         => 'required|in_list[senin,selasa,rabu,kamis,jumat,sabtu]',
            'keterangan'   => 'permit_empty|string',
            'is_active'    => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        // Check if guru already assigned on this day (exclude current record)
        if ($this->guruPiketModel->isGuruAssigned($data['guru_id'], $data['hari'], $data['tahun_ajaran'], $data['semester'], $id)) {
            return $this->errorResponse('Guru ini sudah dijadwalkan piket pada hari ' . ucfirst($data['hari']));
        }

        return $this->executeInTransaction(function () use ($id, $data) {
            $updateData = [
                'guru_id'      => $data['guru_id'],
                'tahun_ajaran' => $data['tahun_ajaran'],
                'semester'     => $data['semester'],
                'hari'         => $data['hari'],
                'keterangan'   => $data['keterangan'] ?? null,
                'is_active'    => $data['is_active'] ?? 1,
            ];

            $this->guruPiketModel->update($id, $updateData);

            $this->log('info', "Guru piket updated: ID={$id}");

            return ['id' => $id];
        });
    }

    /**
     * Delete guru piket assignment
     */
    public function delete(int $id): array
    {
        $piket = $this->guruPiketModel->find($id);

        if (!$piket) {
            return $this->errorResponse('Jadwal piket tidak ditemukan');
        }

        return $this->executeInTransaction(function () use ($id) {
            $this->guruPiketModel->delete($id);

            $this->log('info', "Guru piket deleted: ID={$id}");

            return ['id' => $id];
        });
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(int $id): array
    {
        $piket = $this->guruPiketModel->find($id);

        if (!$piket) {
            return $this->errorResponse('Jadwal piket tidak ditemukan');
        }

        try {
            $newStatus = $piket['is_active'] ? 0 : 1;
            $this->guruPiketModel->update($id, ['is_active' => $newStatus]);

            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            $this->log('info', "Guru piket status toggled to {$statusText}: ID={$id}");

            return $this->successResponse(['new_status' => $newStatus], "Jadwal piket berhasil {$statusText}");
        } catch (\Exception $e) {
            $this->log('error', 'Failed to toggle guru piket status: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengubah status jadwal piket');
        }
    }

    /**
     * Bulk assign multiple guru to a day (optimized: single query check + batch insert)
     */
    public function bulkAssign(array $guruIds, string $hari, string $tahunAjaran, string $semester, ?string $keterangan = null): array
    {
        if (empty($guruIds)) {
            return $this->errorResponse('Pilih minimal satu guru');
        }

        $rules = [
            'hari'         => 'required|in_list[senin,selasa,rabu,kamis,jumat,sabtu]',
            'tahun_ajaran' => 'required',
            'semester'     => 'required|in_list[ganjil,genap]',
        ];

        $validationData = ['hari' => $hari, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester];
        if (!$this->validate($validationData, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        return $this->executeInTransaction(function () use ($guruIds, $hari, $tahunAjaran, $semester, $keterangan) {
            // Single query: get all already-assigned (active) guru_ids for this day, tahun ajaran & semester
            $alreadyAssigned = $this->guruPiketModel->select('guru_id')
                ->where('hari', $hari)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->whereIn('guru_id', $guruIds)
                ->findAll();
            $assignedIds = array_flip(array_column($alreadyAssigned, 'guru_id'));

            // Separate: new insert vs restore soft-deleted
            $toInsert = [];
            foreach ($guruIds as $guruId) {
                if (isset($assignedIds[$guruId])) {
                    continue;
                }
                $toInsert[] = [
                    'guru_id'      => $guruId,
                    'tahun_ajaran' => $tahunAjaran,
                    'semester'     => $semester,
                    'hari'         => $hari,
                    'keterangan'   => $keterangan,
                    'is_active'    => 1,
                    'created_at'   => date('Y-m-d H:i:s'),
                ];
            }

            // Check soft-deleted records and restore them instead of insert
            $skippedCount = count($guruIds) - count($toInsert);
            $successCount = 0;

            if (!empty($toInsert)) {
                $guruIdsToInsert = array_column($toInsert, 'guru_id');
                $deletedRecords = $this->guruPiketModel->onlyDeleted()
                    ->select('id, guru_id')
                    ->where('hari', $hari)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->where('semester', $semester)
                    ->whereIn('guru_id', $guruIdsToInsert)
                    ->findAll();

                $restoredIds = [];
                foreach ($deletedRecords as $record) {
                    $restoredIds[] = $record['guru_id'];
                    $this->guruPiketModel->update($record['id'], [
                        'keterangan'   => $keterangan,
                        'is_active'    => 1,
                        'deleted_at'   => null,
                    ]);
                    $successCount++;
                }

                // Insert only gurus that were NOT restored
                $toInsert = array_filter($toInsert, fn($item) => !in_array($item['guru_id'], $restoredIds));
                if (!empty($toInsert)) {
                    $this->guruPiketModel->insertBatch(array_values($toInsert));
                    $successCount += count($toInsert);
                }
            }

            $this->log('info', "Bulk assign piket: hari={$hari}, semester={$semester}, success={$successCount}, skipped={$skippedCount}");

            return [
                'success_count' => $successCount,
                'skipped_count' => $skippedCount,
                'errors'        => [],
            ];
        });
    }

    /**
     * Get available guru for a specific day
     */
    public function getAvailableGuru(string $hari, string $tahunAjaran, string $semester, ?int $excludeId = null): array
    {
        try {
            $available = $this->guruPiketModel->getAvailableGuru($hari, $tahunAjaran, $semester, $excludeId);
            return $this->successResponse($available);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get available guru: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data guru tersedia');
        }
    }

    /**
     * Get guru dropdown list
     */
    public function getGuruDropdown(): array
    {
        try {
            $dropdown = $this->guruPiketModel->getGuruDropdown();
            return $this->successResponse($dropdown);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get guru dropdown: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data guru');
        }
    }
}
