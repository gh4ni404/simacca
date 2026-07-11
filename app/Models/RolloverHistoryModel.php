<?php

namespace App\Models;

use CodeIgniter\Model;

class RolloverHistoryModel extends Model
{
    protected $table            = 'rollover_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'from_year',
        'to_year',
        'total_students',
        'naik_kelas',
        'lulus',
        'skipped_count',
        'reverted_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all rollover history ordered by newest first
     */
    public function getAllHistory(): array
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get the latest active (non-reverted) rollover
     */
    public function getLatestActive(): ?array
    {
        return $this->where('reverted_at IS NULL')
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /**
     * Get active rollover count
     */
    public function countActive(): int
    {
        return $this->where('reverted_at IS NULL')->countAllResults();
    }

    /**
     * Mark a rollover as reverted
     */
    public function markReverted(int $id): bool
    {
        return $this->update($id, ['reverted_at' => date('Y-m-d H:i:s')]);
    }
}
