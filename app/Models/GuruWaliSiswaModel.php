<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruWaliSiswaModel extends Model
{
    protected $table            = 'guru_wali_siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'guru_id',
        'siswa_id',
        'tahun_ajaran',
        'keterangan',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'guru_id'      => 'required|numeric',
        'siswa_id'     => 'required|numeric',
        'tahun_ajaran' => 'required|max_length[20]',
    ];

    /**
     * Get all active students for a given academic year with their assigned Guru Wali (if any)
     */
    public function getSiswaWithGuruWali(string $tahunAjaran, array $filters = []): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('siswa s')
            ->select('
                s.id as siswa_id,
                s.nis,
                s.nama_lengkap as nama_siswa,
                s.jenis_kelamin,
                s.tahun_ajaran,
                k.id as kelas_id,
                k.nama_kelas,
                k.tingkat,
                k.jurusan,
                u_s.profile_photo as siswa_foto,
                u_s.email as siswa_email,
                gws.id as mapping_id,
                gws.keterangan as mapping_keterangan,
                gws.created_at as assigned_at,
                g.id as guru_id,
                g.nip as guru_nip,
                g.nama_lengkap as nama_guru,
                u_g.profile_photo as guru_foto,
                mp.nama_mapel
            ')
            ->join('kelas k', 'k.id = s.kelas_id', 'left')
            ->join('users u_s', 'u_s.id = s.user_id', 'left')
            ->join('guru_wali_siswa gws', "gws.siswa_id = s.id AND gws.tahun_ajaran = '{$tahunAjaran}' AND gws.deleted_at IS NULL", 'left')
            ->join('guru g', 'g.id = gws.guru_id AND g.deleted_at IS NULL', 'left')
            ->join('users u_g', 'u_g.id = g.user_id', 'left')
            ->join('mata_pelajaran mp', 'mp.id = g.mata_pelajaran_id', 'left')
            ->where('s.tahun_ajaran', $tahunAjaran)
            ->where('s.deleted_at IS NULL')
            ->where('u_s.is_active', 1);

        // Apply filters
        if (!empty($filters['kelas_id'])) {
            $builder->where('s.kelas_id', $filters['kelas_id']);
        }

        if (!empty($filters['tingkat'])) {
            $builder->where('k.tingkat', $filters['tingkat']);
        }

        if (!empty($filters['jurusan'])) {
            $builder->where('k.jurusan', $filters['jurusan']);
        }

        if (!empty($filters['guru_id'])) {
            $builder->where('gws.guru_id', $filters['guru_id']);
        }

        if (isset($filters['status'])) {
            if ($filters['status'] === 'assigned') {
                $builder->where('gws.id IS NOT NULL');
            } elseif ($filters['status'] === 'unassigned') {
                $builder->where('gws.id IS NULL');
            }
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('s.nama_lengkap', $search)
                ->orLike('s.nis', $search)
                ->orLike('g.nama_lengkap', $search)
                ->orLike('k.nama_kelas', $search)
            ->groupEnd();
        }

        return $builder->orderBy('k.tingkat ASC, k.nama_kelas ASC, s.nama_lengkap ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get summary of all teachers and the count of their assigned students
     */
    public function getGuruWaliSummary(string $tahunAjaran, ?string $search = null): array
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('guru g')
            ->select('
                g.id as guru_id,
                g.nip,
                g.nama_lengkap,
                g.jenis_kelamin,
                u.profile_photo,
                u.email,
                mp.nama_mapel,
                COUNT(gws.id) as total_siswa_wali
            ')
            ->join('users u', 'u.id = g.user_id', 'left')
            ->join('mata_pelajaran mp', 'mp.id = g.mata_pelajaran_id', 'left')
            ->join('guru_wali_siswa gws', "gws.guru_id = g.id AND gws.tahun_ajaran = '{$tahunAjaran}' AND gws.deleted_at IS NULL", 'left')
            ->where('g.deleted_at IS NULL')
            ->where('u.is_active', 1)
            ->groupBy('g.id');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('g.nama_lengkap', $search)
                ->orLike('g.nip', $search)
                ->orLike('mp.nama_mapel', $search)
            ->groupEnd();
        }

        $teachers = $builder->orderBy('total_siswa_wali DESC, g.nama_lengkap ASC')
            ->get()
            ->getResultArray();

        return $teachers;
    }

    /**
     * Get list of students assigned to a specific Guru Wali
     */
    public function getSiswaByGuru(int $guruId, string $tahunAjaran): array
    {
        $db = \Config\Database::connect();
        return $db->table('guru_wali_siswa gws')
            ->select('
                gws.id as mapping_id,
                gws.keterangan,
                gws.created_at as assigned_at,
                s.id as siswa_id,
                s.nis,
                s.nama_lengkap as nama_siswa,
                s.jenis_kelamin,
                k.nama_kelas,
                k.tingkat,
                k.jurusan,
                u.profile_photo as siswa_foto,
                u.email as siswa_email
            ')
            ->join('siswa s', 's.id = gws.siswa_id')
            ->join('kelas k', 'k.id = s.kelas_id', 'left')
            ->join('users u', 'u.id = s.user_id', 'left')
            ->where('gws.guru_id', $guruId)
            ->where('gws.tahun_ajaran', $tahunAjaran)
            ->where('gws.deleted_at IS NULL')
            ->where('s.deleted_at IS NULL')
            ->orderBy('k.nama_kelas ASC, s.nama_lengkap ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get statistics for Guru Wali monitoring
     */
    public function getStats(string $tahunAjaran): array
    {
        $db = \Config\Database::connect();

        // Total active students in this academic year
        $totalSiswa = $db->table('siswa s')
            ->join('users u', 'u.id = s.user_id', 'left')
            ->where('s.tahun_ajaran', $tahunAjaran)
            ->where('s.deleted_at IS NULL')
            ->where('u.is_active', 1)
            ->countAllResults();

        // Total assigned students
        $totalAssigned = $db->table('guru_wali_siswa gws')
            ->join('siswa s', 's.id = gws.siswa_id')
            ->where('gws.tahun_ajaran', $tahunAjaran)
            ->where('gws.deleted_at IS NULL')
            ->where('s.deleted_at IS NULL')
            ->countAllResults();

        $totalUnassigned = max(0, $totalSiswa - $totalAssigned);

        // Total distinct teachers who have at least 1 student
        $totalGuruWali = $db->table('guru_wali_siswa gws')
            ->select('COUNT(DISTINCT gws.guru_id) as total')
            ->where('gws.tahun_ajaran', $tahunAjaran)
            ->where('gws.deleted_at IS NULL')
            ->get()
            ->getRowArray()['total'] ?? 0;

        // Total active teachers available
        $totalGuruAvailable = $db->table('guru g')
            ->join('users u', 'u.id = g.user_id', 'left')
            ->where('g.deleted_at IS NULL')
            ->where('u.is_active', 1)
            ->countAllResults();

        $percentageAssigned = $totalSiswa > 0 ? round(($totalAssigned / $totalSiswa) * 100, 1) : 0;
        $avgSiswaPerGuru = $totalGuruWali > 0 ? round($totalAssigned / $totalGuruWali, 1) : 0;

        return [
            'total_siswa'          => (int) $totalSiswa,
            'total_assigned'       => (int) $totalAssigned,
            'total_unassigned'     => (int) $totalUnassigned,
            'total_guru_wali'      => (int) $totalGuruWali,
            'total_guru_available' => (int) $totalGuruAvailable,
            'percentage_assigned'  => $percentageAssigned,
            'avg_siswa_per_guru'   => $avgSiswaPerGuru,
        ];
    }

    /**
     * Assign or update a student to a Guru Wali for an academic year
     */
    public function assignSiswa(int $siswaId, int $guruId, string $tahunAjaran, ?string $keterangan = null): bool
    {
        // Check if there is an existing record (including soft-deleted)
        $existing = $this->withDeleted()
            ->where('siswa_id', $siswaId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->first();

        if ($existing) {
            return (bool) $this->update($existing['id'], [
                'guru_id'    => $guruId,
                'keterangan' => $keterangan ?? $existing['keterangan'],
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return (bool) $this->insert([
            'guru_id'      => $guruId,
            'siswa_id'     => $siswaId,
            'tahun_ajaran' => $tahunAjaran,
            'keterangan'   => $keterangan,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Unassign a student from their Guru Wali
     */
    public function unassignSiswa(int $siswaId, string $tahunAjaran): bool
    {
        return (bool) $this->where('siswa_id', $siswaId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->delete();
    }
}
