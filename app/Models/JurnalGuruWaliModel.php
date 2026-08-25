<?php

namespace App\Models;

use CodeIgniter\Model;

class JurnalGuruWaliModel extends Model
{
    protected $table            = 'jurnal_guru_wali';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'guru_id',
        'siswa_id',
        'tahun_ajaran',
        'tanggal',
        'jenis_bimbingan',
        'catatan',
        'tindak_lanjut',
        'foto_dokumentasi',
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
        'guru_id'         => 'required|numeric',
        'siswa_id'        => 'required|numeric',
        'tanggal'         => 'required|valid_date',
        'jenis_bimbingan' => 'required',
        'catatan'         => 'required',
    ];

    /**
     * Get journal entries for a specific Guru with student & class info
     */
    public function getJurnalByGuru(int $guruId, array $filters = []): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('jurnal_guru_wali jgw')
            ->select('
                jgw.id,
                jgw.guru_id,
                jgw.siswa_id,
                jgw.tahun_ajaran,
                jgw.tanggal,
                jgw.jenis_bimbingan,
                jgw.catatan,
                jgw.tindak_lanjut,
                jgw.foto_dokumentasi,
                jgw.created_at,
                s.nis,
                s.nama_lengkap as nama_siswa,
                s.jenis_kelamin,
                k.nama_kelas,
                k.tingkat,
                k.jurusan,
                u_s.profile_photo as siswa_foto,
                g.nama_lengkap as nama_guru,
                g.nip as guru_nip
            ')
            ->join('siswa s', 's.id = jgw.siswa_id')
            ->join('kelas k', 'k.id = s.kelas_id', 'left')
            ->join('users u_s', 'u_s.id = s.user_id', 'left')
            ->join('guru g', 'g.id = jgw.guru_id', 'left')
            ->where('jgw.guru_id', $guruId)
            ->where('jgw.deleted_at IS NULL');

        if (!empty($filters['siswa_id'])) {
            $builder->where('jgw.siswa_id', (int) $filters['siswa_id']);
        }

        if (!empty($filters['jenis_bimbingan'])) {
            $builder->where('jgw.jenis_bimbingan', $filters['jenis_bimbingan']);
        }

        if (!empty($filters['start_date'])) {
            $builder->where('jgw.tanggal >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('jgw.tanggal <=', $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('s.nama_lengkap', $search)
                ->orLike('s.nis', $search)
                ->orLike('jgw.catatan', $search)
                ->orLike('jgw.tindak_lanjut', $search)
                ->orLike('jgw.jenis_bimbingan', $search)
            ->groupEnd();
        }

        return $builder->orderBy('jgw.tanggal DESC, jgw.id DESC')
            ->get()
            ->getResultArray();
    }
}
