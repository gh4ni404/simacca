<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * AbsensiGuruModel
 * 
 * Model for teacher attendance with self check-in/check-out system.
 * Handles selfie validation, work duration tracking, and auto-status calculation.
 * 
 * Business Rules:
 * - Auto hadir: check_in <= 07:15:00
 * - Auto terlambat: check_in > 07:15:00
 * - Auto alpha: no check_in by 10:00:00
 * - Minimum work duration: 8 hours (480 minutes)
 * 
 * @package App\Models
 * @author SIMACCA Team
 * @version 2.0.0
 */
class AbsensiGuruModel extends Model
{
    protected $table            = 'absensi_guru';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'guru_id',
        'tanggal',
        'status',
        'check_in',
        'check_out',
        'durasi_menit',
        'foto_check_in',
        'foto_check_out',
        'latitude_check_in',
        'longitude_check_in',
        'latitude_check_out',
        'longitude_check_out',
        'early_checkout',
        'early_checkout_reason',
        'catatan',
        'set_by_wakakur',
        'set_by_user_id',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'guru_id'   => 'required|numeric',
        'tanggal'   => 'required|valid_date',
        'status'    => 'required|in_list[hadir,terlambat,izin,sakit,alpha,cuti]',
    ];
    protected $validationMessages   = [
        'guru_id' => [
            'required' => 'Guru harus dipilih',
            'numeric'  => 'Guru ID tidak valid',
        ],
        'tanggal' => [
            'required'   => 'Tanggal harus diisi',
            'valid_date' => 'Format tanggal tidak valid',
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list'  => 'Status tidak valid',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['calculateDuration'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Calculate duration before update
     */
    protected function calculateDuration(array $data)
    {
        if (isset($data['data']['check_in']) && isset($data['data']['check_out'])) {
            $checkIn = strtotime($data['data']['check_in']);
            $checkOut = strtotime($data['data']['check_out']);
            
            if ($checkOut > $checkIn) {
                $durasiMenit = ($checkOut - $checkIn) / 60;
                $data['data']['durasi_menit'] = (int)$durasiMenit;
                
                // Check if early checkout (less than 8 hours = 480 minutes)
                if ($durasiMenit < 480) {
                    $data['data']['early_checkout'] = 1;
                }
            }
        }

        return $data;
    }

    /**
     * Check-in guru
     * Auto-calculate status based on time
     */
    public function checkIn($guruId, $tanggal, $checkInTime, $fotoPath = null, $latitude = null, $longitude = null)
    {
        // Calculate status based on check-in time
        $status = (strtotime($checkInTime) <= strtotime('07:15:00')) ? 'hadir' : 'terlambat';

        $data = [
            'guru_id'            => $guruId,
            'tanggal'            => $tanggal,
            'status'             => $status,
            'check_in'           => $checkInTime,
            'foto_check_in'      => $fotoPath,
            'latitude_check_in'  => $latitude,
            'longitude_check_in' => $longitude,
            'created_by'         => session()->get('user_id'),
        ];

        return $this->insert($data);
    }

    /**
     * Check-out guru
     * Calculate work duration
     */
    public function checkOut($absensiId, $checkOutTime, $fotoPath = null, $latitude = null, $longitude = null, $earlyCheckoutReason = null)
    {
        $absensi = $this->find($absensiId);
        
        if (!$absensi) {
            return false;
        }

        // Calculate duration
        $checkIn = strtotime($absensi['check_in']);
        $checkOut = strtotime($checkOutTime);
        $durasiMenit = ($checkOut - $checkIn) / 60;

        $data = [
            'check_out'           => $checkOutTime,
            'foto_check_out'      => $fotoPath,
            'latitude_check_out'  => $latitude,
            'longitude_check_out' => $longitude,
            'durasi_menit'        => (int)$durasiMenit,
        ];

        // Check if early checkout
        if ($durasiMenit < 480) {
            $data['early_checkout'] = 1;
            $data['early_checkout_reason'] = $earlyCheckoutReason;
        }

        return $this->update($absensiId, $data);
    }

    /**
     * Get today's attendance for a guru
     */
    public function getTodayAttendance($guruId, $tanggal = null)
    {
        if (!$tanggal) {
            $tanggal = date('Y-m-d');
        }

        return $this->where('guru_id', $guruId)
            ->where('tanggal', $tanggal)
            ->first();
    }

    /**
     * Get all today's attendance (for Wakakur monitoring)
     */
    public function getAllTodayAttendance($tanggal = null)
    {
        if (!$tanggal) {
            $tanggal = date('Y-m-d');
        }

        return $this->select('absensi_guru.*, guru.nama_lengkap, guru.nip, mata_pelajaran.nama_mapel')
            ->join('guru', 'guru.id = absensi_guru.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left')
            ->where('absensi_guru.tanggal', $tanggal)
            ->orderBy('guru.nama_lengkap', 'ASC')
            ->findAll();
    }

    /**
     * Get monthly attendance for a guru
     */
    public function getMonthlyAttendance($guruId, $bulan, $tahun)
    {
        return $this->where('guru_id', $guruId)
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->orderBy('tanggal', 'ASC')
            ->findAll();
    }

    /**
     * Get attendance statistics
     */
    public function getStatistics($guruId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('
            COUNT(*) as total,
            SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN status = "terlambat" THEN 1 ELSE 0 END) as terlambat,
            SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
            SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
            SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as alpha,
            SUM(CASE WHEN status = "cuti" THEN 1 ELSE 0 END) as cuti,
            ROUND(AVG(durasi_menit), 0) as avg_durasi_menit
        ');

        if ($guruId) {
            $builder->where('guru_id', $guruId);
        }

        if ($startDate) {
            $builder->where('tanggal >=', $startDate);
        }

        if ($endDate) {
            $builder->where('tanggal <=', $endDate);
        }

        return $builder->first();
    }

    /**
     * Calculate status (helper for auto-alpha at 10:00)
     * To be called by scheduled task
     */
    public function calculateStatus($absensiId)
    {
        $absensi = $this->find($absensiId);
        
        if (!$absensi || $absensi['check_in']) {
            return false;
        }

        // If no check_in and current time >= 10:00, set to alpha
        if (time() >= strtotime($absensi['tanggal'] . ' 10:00:00')) {
            return $this->update($absensiId, ['status' => 'alpha']);
        }

        return false;
    }

    /**
     * Get attendance for export (Excel/PDF)
     */
    public function getForExport($filters = [])
    {
        $builder = $this->select('
            absensi_guru.*,
            guru.nama_lengkap,
            guru.nip,
            mata_pelajaran.nama_mapel,
            users.username as set_by_username
        ')
        ->join('guru', 'guru.id = absensi_guru.guru_id')
        ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left')
        ->join('users', 'users.id = absensi_guru.set_by_user_id', 'left');

        // Apply filters
        if (!empty($filters['guru_id'])) {
            $builder->where('absensi_guru.guru_id', $filters['guru_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('absensi_guru.status', $filters['status']);
        }

        if (!empty($filters['start_date'])) {
            $builder->where('absensi_guru.tanggal >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('absensi_guru.tanggal <=', $filters['end_date']);
        }

        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(absensi_guru.tanggal)', $filters['bulan']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(absensi_guru.tanggal)', $filters['tahun']);
        }

        return $builder->orderBy('absensi_guru.tanggal', 'DESC')
            ->orderBy('guru.nama_lengkap', 'ASC')
            ->findAll();
    }

    /**
     * Check if attendance exists for guru on specific date
     */
    public function exists($guruId, $tanggal)
    {
        return $this->where('guru_id', $guruId)
            ->where('tanggal', $tanggal)
            ->countAllResults() > 0;
    }

    /**
     * Manual set status by Wakakur
     */
    public function manualSetStatus($guruId, $tanggal, $status, $catatan = null, $setByUserId = null)
    {
        // Check if record exists
        $existing = $this->getTodayAttendance($guruId, $tanggal);

        $data = [
            'status'         => $status,
            'catatan'        => $catatan,
            'set_by_wakakur' => 1,
            'set_by_user_id' => $setByUserId ?? session()->get('user_id'),
        ];

        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            $data['guru_id'] = $guruId;
            $data['tanggal'] = $tanggal;
            $data['created_by'] = $setByUserId ?? session()->get('user_id');
            return $this->insert($data);
        }
    }
}
