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
            'jobdesk_id'   => 'permit_empty|integer',
            'tahun_ajaran' => 'required',
            'semester'     => 'required|in_list[ganjil,genap]',
            'hari'         => 'required|in_list[senin,selasa,rabu,kamis,jumat,sabtu]',
            'keterangan'    => 'permit_empty|string',
            'rincian_tugas' => 'permit_empty|string',
            'is_active'     => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        // Check if guru already assigned on this day
        if ($this->guruPiketModel->isGuruAssigned($data['guru_id'], $data['hari'], $data['tahun_ajaran'], $data['semester'])) {
            return $this->errorResponse('Guru ini sudah dijadwalkan piket pada hari ' . ucfirst($data['hari']));
        }

        // If jobdesk_id is provided but rincian_tugas is empty, load rincian_tugas from master jobdesk
        $rincianTugas = $data['rincian_tugas'] ?? null;
        if (!empty($data['jobdesk_id']) && empty($rincianTugas)) {
            $masterJobdeskModel = new \App\Models\MasterJobdeskPiketModel();
            $jobdesk = $masterJobdeskModel->find($data['jobdesk_id']);
            if ($jobdesk) {
                $rincianTugas = $jobdesk['rincian_tugas'];
            }
        }

        return $this->executeInTransaction(function () use ($data, $rincianTugas) {
            $piketData = [
                'guru_id'       => $data['guru_id'],
                'jobdesk_id'    => !empty($data['jobdesk_id']) ? $data['jobdesk_id'] : null,
                'tahun_ajaran'  => $data['tahun_ajaran'],
                'semester'      => $data['semester'],
                'hari'          => $data['hari'],
                'keterangan'    => $data['keterangan'] ?? null,
                'rincian_tugas' => $rincianTugas,
                'is_active'     => $data['is_active'] ?? 1,
                'created_at'    => date('Y-m-d H:i:s'),
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
            'jobdesk_id'   => 'permit_empty|integer',
            'tahun_ajaran' => 'required',
            'semester'     => 'required|in_list[ganjil,genap]',
            'hari'         => 'required|in_list[senin,selasa,rabu,kamis,jumat,sabtu]',
            'keterangan'    => 'permit_empty|string',
            'rincian_tugas' => 'permit_empty|string',
            'is_active'     => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        // Check if guru already assigned on this day (exclude current record)
        if ($this->guruPiketModel->isGuruAssigned($data['guru_id'], $data['hari'], $data['tahun_ajaran'], $data['semester'], $id)) {
            return $this->errorResponse('Guru ini sudah dijadwalkan piket pada hari ' . ucfirst($data['hari']));
        }

        // If jobdesk_id is provided but rincian_tugas is empty, load rincian_tugas from master jobdesk
        $rincianTugas = $data['rincian_tugas'] ?? null;
        if (!empty($data['jobdesk_id']) && empty($rincianTugas)) {
            $masterJobdeskModel = new \App\Models\MasterJobdeskPiketModel();
            $jobdesk = $masterJobdeskModel->find($data['jobdesk_id']);
            if ($jobdesk) {
                $rincianTugas = $jobdesk['rincian_tugas'];
            }
        }

        return $this->executeInTransaction(function () use ($id, $data, $rincianTugas) {
            $updateData = [
                'guru_id'       => $data['guru_id'],
                'jobdesk_id'    => !empty($data['jobdesk_id']) ? $data['jobdesk_id'] : null,
                'tahun_ajaran'  => $data['tahun_ajaran'],
                'semester'      => $data['semester'],
                'hari'          => $data['hari'],
                'keterangan'    => $data['keterangan'] ?? null,
                'rincian_tugas' => $rincianTugas,
                'is_active'     => $data['is_active'] ?? 1,
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
    public function bulkAssign(array $guruIds, string $hari, string $tahunAjaran, string $semester, ?string $keterangan = null, ?string $rincianTugas = null, ?int $jobdeskId = null): array
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

        if (!empty($jobdeskId) && empty($rincianTugas)) {
            $masterJobdeskModel = new \App\Models\MasterJobdeskPiketModel();
            $jobdesk = $masterJobdeskModel->find($jobdeskId);
            if ($jobdesk) {
                $rincianTugas = $jobdesk['rincian_tugas'];
            }
        }

        return $this->executeInTransaction(function () use ($guruIds, $hari, $tahunAjaran, $semester, $keterangan, $rincianTugas, $jobdeskId) {
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
                    'guru_id'       => $guruId,
                    'jobdesk_id'    => $jobdeskId,
                    'tahun_ajaran'  => $tahunAjaran,
                    'semester'      => $semester,
                    'hari'          => $hari,
                    'keterangan'    => $keterangan,
                    'rincian_tugas' => $rincianTugas,
                    'is_active'     => 1,
                    'created_at'    => date('Y-m-d H:i:s'),
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
                        'jobdesk_id'    => $jobdeskId,
                        'keterangan'    => $keterangan,
                        'rincian_tugas' => $rincianTugas,
                        'is_active'     => 1,
                        'deleted_at'    => null,
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
     * Bulk assign/map a Master Jobdesk to multiple guru utilizing existing guru_piket table
     */
    public function bulkAssignJobdesk(array $guruIds, int $jobdeskId, string $hari, string $tahunAjaran, string $semester, ?string $keterangan = null): array
    {
        $masterJobdeskModel = new \App\Models\MasterJobdeskPiketModel();
        $jobdesk = $masterJobdeskModel->find($jobdeskId);
        if (!$jobdesk) {
            return $this->errorResponse('Master jobdesk tidak ditemukan');
        }

        $rules = [
            'tahun_ajaran' => 'required',
            'semester'     => 'required|in_list[ganjil,genap]',
        ];

        $validationData = ['tahun_ajaran' => $tahunAjaran, 'semester' => $semester];
        if (!$this->validate($validationData, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        // Validate that all selected teachers already have a piket schedule in guru_piket
        if (!empty($guruIds)) {
            $unscheduledGuru = [];
            foreach ($guruIds as $guruId) {
                $hasSchedule = $this->guruPiketModel->where('guru_id', $guruId)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->where('semester', $semester)
                    ->where('is_active', 1)
                    ->first();

                if (!$hasSchedule) {
                    $guruModel = new \App\Models\GuruModel();
                    $g = $guruModel->find($guruId);
                    $unscheduledGuru[] = $g ? $g['nama_lengkap'] : "ID {$guruId}";
                }
            }

            if (!empty($unscheduledGuru)) {
                return $this->errorResponse('Validasi gagal: Guru berikut belum diatur jadwal piket shalatnya: ' . implode(', ', $unscheduledGuru) . '. Sila atur jadwal piket shalat terlebih dahulu.');
            }
        }

        return $this->executeInTransaction(function () use ($guruIds, $jobdeskId, $jobdesk, $tahunAjaran, $semester, $keterangan) {
            // Unassign teachers who were unchecked for this jobdesk in active semester
            $unassignQuery = $this->guruPiketModel->where('jobdesk_id', $jobdeskId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester);

            if (!empty($guruIds)) {
                $unassignQuery->whereNotIn('guru_id', $guruIds);
            }
            $unassignQuery->set(['jobdesk_id' => null])->update();

            $updatedCount = 0;
            $insertedCount = 0;

            if (!empty($guruIds)) {
                foreach ($guruIds as $guruId) {
                    // Update existing assignment(s) with new jobdesk and rincian_tugas
                    $existingRecords = $this->guruPiketModel->where('guru_id', $guruId)
                        ->where('tahun_ajaran', $tahunAjaran)
                        ->where('semester', $semester)
                        ->findAll();

                    if (!empty($existingRecords)) {
                        foreach ($existingRecords as $rec) {
                            $updateData = [
                                'jobdesk_id'    => $jobdeskId,
                                'rincian_tugas' => $jobdesk['rincian_tugas'],
                                'is_active'     => 1,
                            ];
                            if (!empty($keterangan)) {
                                $updateData['keterangan'] = $keterangan;
                            }
                            $this->guruPiketModel->update($rec['id'], $updateData);
                        }
                        $updatedCount++;
                    }
                }
            }

            $totalSuccess = $updatedCount + $insertedCount;
            $this->log('info', "Bulk assign jobdesk ID {$jobdeskId} to {$totalSuccess} guru");

            return $this->successResponse([
                'updated_count'  => $updatedCount,
                'inserted_count' => $insertedCount,
                'total_success'  => $totalSuccess,
            ], "Berhasil memperbarui pemetaan " . count($guruIds) . " guru ke jobdesk {$jobdesk['nama_jobdesk']}");
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

    /**
     * Get default template for rincian tugas guru piket
     */
    public function getDefaultRincianTugas(): string
    {
        return "1. Hadir dan menyambut kedatangan siswa di gerbang sekolah.\n"
             . "2. Memantau kedisiplinan dan K7 (Keamanan, Kebersihan, Ketertiban) lingkungan sekolah.\n"
             . "3. Membuka & mengelola Portal Presensi Shalat Berjamaah (Dzuhur/Ashar/Jumat).\n"
             . "4. Mengawasi ketertiban ibadah shalat serta mencatat siswa yang izin/sakit.\n"
             . "5. Menangani & mencatat presensi siswa yang terlambat atau meninggalkan sekolah.";
    }
}

