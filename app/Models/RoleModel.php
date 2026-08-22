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

    public function getDropdown(?array $excludeRoles = ['siswa', 'instruktur']): array
    {
        $this->ensureDefaultRoles();
        $roles = $this->getAllActive();
        $dropdown = [];
        foreach ($roles as $r) {
            if ($excludeRoles !== null && in_array($r['name'], $excludeRoles)) {
                continue;
            }
            $dropdown[$r['name']] = $r['display_name'];
        }
        return $dropdown;
    }

    public function getDisplayName(string $name): string
    {
        if (self::$cache === null) {
            self::$cache = $this->getDropdown();
        }

        $defaults = [
            'admin'          => 'Administrator',
            'guru_mapel'     => 'Guru Mata Pelajaran',
            'wali_kelas'     => 'Wali Kelas',
            'wakakur'        => 'Wakil Kepala Kurikulum',
            'siswa'          => 'Siswa',
            'instruktur'     => 'Instruktur PKL',
            'ketua_jurusan'  => 'Ketua Jurusan',
            'kepala_sekolah' => 'Kepala Sekolah',
            'tendik'         => 'Tenaga Pendidik / Staf',
        ];

        return self::$cache[$name] ?? $defaults[$name] ?? ucfirst(str_replace('_', ' ', $name));
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

        $defaults = [
            'admin'          => 'Administrator',
            'guru_mapel'     => 'Guru Mata Pelajaran',
            'wali_kelas'     => 'Wali Kelas',
            'wakakur'        => 'Wakil Kepala Kurikulum',
            'siswa'          => 'Siswa',
            'instruktur'     => 'Instruktur PKL',
            'ketua_jurusan'  => 'Ketua Jurusan',
            'kepala_sekolah' => 'Kepala Sekolah',
            'tendik'         => 'Tenaga Pendidik / Staf',
        ];

        $data = [
            'name'         => $name,
            'display_name' => $displayName ?? $defaults[$name] ?? ucfirst(str_replace('_', ' ', $name)),
            'description'  => $description ?? "Role {$name}",
            'is_active'    => true,
            'created_at'   => date('Y-m-d H:i:s'),
        ];

        $this->insert($data);

        $this->clearCache();

        return $this->getByName($name);
    }

    /**
     * Ensues all system roles exist in roles table.
     */
    public function ensureDefaultRoles(): void
    {
        $defaults = [
            'admin'          => ['Administrator', 'Akses Penuh Administrator'],
            'guru_mapel'     => ['Guru Mata Pelajaran', 'Akses Guru Mengajar & Jurnal'],
            'wali_kelas'     => ['Wali Kelas', 'Akses Wali Kelas'],
            'wakakur'        => ['Wakil Kepala Kurikulum', 'Akses Kurikulum & Monitoring Guru'],
            'siswa'          => ['Siswa', 'Akses Siswa'],
            'instruktur'     => ['Instruktur PKL', 'Akses Pembimbing Industri PKL'],
            'ketua_jurusan'  => ['Ketua Jurusan', 'Akses Ketua Program Keahlian / Jurusan'],
            'kepala_sekolah' => ['Kepala Sekolah', 'Akses Executive Monitoring Kepala Sekolah'],
            'tendik'         => ['Tenaga Pendidik / Staf', 'Akses Staf & Tenaga Pendidik / TU'],
        ];

        foreach ($defaults as $name => $info) {
            $this->ensureRole($name, $info[0], $info[1]);
        }
    }

    public function clearCache(): void
    {
        self::$cache = null;
    }
}
