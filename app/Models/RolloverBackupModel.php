<?php

namespace App\Models;

use CodeIgniter\Model;

class RolloverBackupModel extends Model
{
    protected $table            = 'rollover_backup';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'history_id',
        'siswa_id',
        'user_id',
        'old_kelas_id',
        'old_tahun_ajaran',
        'old_is_active',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    /**
     * Get backup data for a specific rollover history
     */
    public function getByHistoryId(int $historyId): array
    {
        return $this->where('history_id', $historyId)->findAll();
    }

    /**
     * Insert backup data in batch for a rollover history
     */
    public function insertBatchForHistory(int $historyId, array $changes): bool
    {
        $data = [];
        foreach ($changes as $item) {
            $data[] = [
                'history_id'        => $historyId,
                'siswa_id'          => $item['siswa_id'],
                'user_id'           => $item['user_id'],
                'old_kelas_id'      => $item['kelas_id'],
                'old_tahun_ajaran'  => $item['tahun_ajaran'],
                'old_is_active'     => $item['is_active'],
                'created_at'        => date('Y-m-d H:i:s'),
            ];
        }

        return $this->insertBatch($data) !== false;
    }

    /**
     * Get history IDs that have backup data
     */
    public function getHistoryIdsWithBackup(): array
    {
        $results = $this->select('history_id')
            ->groupBy('history_id')
            ->findAll();

        return array_column($results, 'history_id');
    }

    /**
     * Delete backup data for a specific rollover history
     */
    public function deleteByHistoryId(int $historyId): bool
    {
        return $this->where('history_id', $historyId)->delete();
    }

    /**
     * Cleanup old reverted backups older than specified days
     */
    public function cleanupOldReverted(int $days = 365): int
    {
        $db = \Config\Database::connect();
        $result = $db->query("
            DELETE rb FROM rollover_backup rb
            JOIN rollover_history rh ON rh.id = rb.history_id
            WHERE rh.reverted_at IS NOT NULL
            AND rh.reverted_at < DATE_SUB(NOW(), INTERVAL ? DAY)
        ", [$days]);

        return $result->affectedRows();
    }
}
