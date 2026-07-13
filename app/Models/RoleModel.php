<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'display_name',
        'description',
        'is_active',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'name'         => 'required|is_unique[roles.name,id,{id}]',
        'display_name' => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    private static ?array $cache = null;

    public function getAllActive(): array
    {
        return $this->where('is_active', true)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getDropdown(): array
    {
        $roles = $this->getAllActive();
        $dropdown = [];
        foreach ($roles as $r) {
            $dropdown[$r['name']] = $r['display_name'];
        }
        return $dropdown;
    }

    public function getDisplayName(string $name): string
    {
        if (self::$cache === null) {
            self::$cache = $this->getDropdown();
        }
        return self::$cache[$name] ?? ucfirst(str_replace('_', ' ', $name));
    }

    public function getRoleNames(): array
    {
        if (self::$cache === null) {
            self::$cache = $this->getDropdown();
        }
        return self::$cache;
    }

    public function getByName(string $name): ?array
    {
        return $this->where('name', $name)->first();
    }

    /**
     * Pastikan role ada di tabel roles.
     * Jika belum ada, otomatis buat baru.
     */
    public function ensureRole(string $name, ?string $displayName = null, ?string $description = null): array
    {
        $existing = $this->getByName($name);
        if ($existing) {
            return $existing;
        }

        $data = [
            'name'         => $name,
            'display_name' => $displayName ?? ucfirst(str_replace('_', ' ', $name)),
            'description'  => $description ?? "Role {$name}",
            'is_active'    => true,
            'created_at'   => date('Y-m-d H:i:s'),
        ];

        $this->insert($data);

        $this->clearCache();

        return $this->getByName($name);
    }

    public function clearCache(): void
    {
        self::$cache = null;
    }
}
