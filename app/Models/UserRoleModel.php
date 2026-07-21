<?php

namespace App\Models;

use CodeIgniter\Model;

class UserRoleModel extends Model
{
    protected $table            = 'user_roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'role',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'user_id' => 'required|numeric',
        'role'    => 'required|max_length[50]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get all roles for a user
     */
    public function getRolesByUserId(int $userId): array
    {
        $roles = $this->where('user_id', $userId)->findAll();
        return array_column($roles, 'role');
    }

    /**
     * Assign a role to a user
     */
    public function assignRole(int $userId, string $role): bool
    {
        $existing = $this->where('user_id', $userId)->where('role', $role)->first();
        if ($existing) {
            return true; // Already assigned
        }

        return (bool) $this->insert([
            'user_id'    => $userId,
            'role'       => $role,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Remove a role from a user
     */
    public function removeRole(int $userId, string $role): bool
    {
        return (bool) $this->where('user_id', $userId)->where('role', $role)->delete();
    }

    /**
     * Replace all roles for a user (sync)
     */
    public function syncRoles(int $userId, array $roles): bool
    {
        $this->where('user_id', $userId)->delete();

        $batch = [];
        foreach ($roles as $role) {
            $batch[] = [
                'user_id'    => $userId,
                'role'       => $role,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        if (!empty($batch)) {
            return (bool) $this->insertBatch($batch);
        }

        return true;
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(int $userId, string $role): bool
    {
        return $this->where('user_id', $userId)->where('role', $role)->countAllResults() > 0;
    }

    /**
     * Get all user IDs that have a specific role
     */
    public function getUserIdsByRole(string $role): array
    {
        $results = $this->select('user_id')->where('role', $role)->findAll();
        return array_column($results, 'user_id');
    }
}
