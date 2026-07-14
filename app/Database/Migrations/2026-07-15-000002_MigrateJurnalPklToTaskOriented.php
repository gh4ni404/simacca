<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrateJurnalPklToTaskOriented extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Skip if old jurnal_pkl table doesn't exist
        if (!$db->tableExists('jurnal_pkl')) {
            return;
        }

        // Migrate existing jurnal_pkl data to pkl_tasks + pkl_progress
        $hasDeletedAt = $db->fieldExists('deleted_at', 'jurnal_pkl');
        $builder = $db->table('jurnal_pkl');
        if ($hasDeletedAt) {
            $builder->where('deleted_at', null);
        }
        $oldData = $builder->get()->getResultArray();

        if (empty($oldData)) {
            return;
        }

        // Group by siswa_id + nama_kegiatan to create tasks
        $taskMap = [];
        foreach ($oldData as $row) {
            $key = $row['siswa_id'] . '|' . strtolower(trim($row['nama_kegiatan']));
            if (!isset($taskMap[$key])) {
                $taskMap[$key] = [
                    'siswa_id' => $row['siswa_id'],
                    'judul' => $row['nama_kegiatan'],
                    'progress' => [],
                ];
            }
            $taskMap[$key]['progress'][] = $row;
        }

        $tasksTable = $db->table('pkl_tasks');
        $progressTable = $db->table('pkl_progress');

        foreach ($taskMap as $taskData) {
            $taskId = $tasksTable->insert([
                'siswa_id' => $taskData['siswa_id'],
                'judul' => $taskData['judul'],
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if (!$taskId) {
                continue;
            }

            foreach ($taskData['progress'] as $p) {
                $newStatus = match ($p['status']) {
                    'pending' => 'draft',
                    'disetujui' => 'approved',
                    'revisi' => 'revision',
                    'tinjau_ulang' => 'submitted',
                    'ditolak' => 'revision',
                    default => 'draft',
                };

                $progressTable->insert([
                    'task_id' => $taskId,
                    'tanggal' => $p['tanggal'],
                    'deskripsi' => $p['deskripsi'],
                    'foto' => $p['foto'],
                    'status' => $newStatus,
                    'catatan_pembimbing' => $p['catatan_pembimbing'],
                    'verified_by' => $p['verified_by'],
                    'verified_at' => $p['verified_at'],
                    'created_at' => $p['created_at'] ?? date('Y-m-d H:i:s'),
                    'updated_at' => $p['updated_at'] ?? date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        // Data migration is not reversible in a meaningful way
    }
}
