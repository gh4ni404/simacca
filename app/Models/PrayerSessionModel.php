<?php

namespace App\Models;

use CodeIgniter\Model;

class PrayerSessionModel extends Model
{
    protected $table            = 'prayer_sessions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'token',
        'guru_piket_id',
        'is_active',
        'created_at',
        'expires_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules      = [
        'token'          => 'required|max_length[64]',
        'guru_piket_id'  => 'required|integer',
        'expires_at'     => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Generate a new session token and deactivate old ones for this guru_piket
     */
    public function generateNewToken(int $guruPiketId): array
    {
        $db = \Config\Database::connect();

        // Check if there's already an active session — update it, don't create new
        $existing = $db->table('prayer_sessions')
            ->where('guru_piket_id', $guruPiketId)
            ->where('is_active', 1)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRowArray();

        $token = bin2hex(random_bytes(32));
        $now = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 seconds'));

        if ($existing) {
            // Reuse existing session — same prayer_session_id
            $db->table('prayer_sessions')
                ->where('id', $existing['id'])
                ->update([
                    'token'      => $token,
                    'expires_at' => $expiresAt,
                    'created_at' => $now,
                ]);
        } else {
            // No active session — create new
            $db->table('prayer_sessions')->insert([
                'token'          => $token,
                'guru_piket_id'  => $guruPiketId,
                'is_active'      => 1,
                'created_at'     => $now,
                'expires_at'     => $expiresAt,
            ]);
        }

        return [
            'token'      => $token,
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * Validate a token: check if it exists, is active, and not expired
     */
    public function validateToken(string $token): ?array
    {
        $session = $this->where('token', $token)
            ->where('is_active', 1)
            ->first();

        if (!$session) {
            return null;
        }

        // Check expiry
        $now = time();
        $expires = strtotime($session['expires_at']);

        if ($now > $expires) {
            // Token expired, deactivate it
            $db = \Config\Database::connect();
            $db->table('prayer_sessions')
                ->where('id', $session['id'])
                ->update(['is_active' => 0]);
            return null;
        }

        return $session;
    }

    /**
     * Get active session for a guru piket
     */
    public function getActiveSession(int $guruPiketId): ?array
    {
        return $this->where('guru_piket_id', $guruPiketId)
            ->where('is_active', 1)
            ->first();
    }

    /**
     * Get today's session with attendance stats
     */
    public function getTodaySessionsWithStats(): array
    {
        $today = date('Y-m-d');

        $builder = $this->select('prayer_sessions.*, guru.nama_lengkap as nama_guru')
            ->join('guru_piket', 'guru_piket.id = prayer_sessions.guru_piket_id')
            ->join('guru', 'guru.id = guru_piket.guru_id')
            ->where('DATE(prayer_sessions.created_at)', $today)
            ->orderBy('prayer_sessions.created_at', 'DESC');

        $sessions = $builder->findAll();

        // Get attendance count for each session
        $db = \Config\Database::connect();
        $sessionIds = array_column($sessions, 'id');

        if (!empty($sessionIds)) {
            $counts = $db->table('absensi_shalat')
                ->select('prayer_session_id, COUNT(id) as total_hadir')
                ->whereIn('prayer_session_id', $sessionIds)
                ->groupBy('prayer_session_id')
                ->get()
                ->getResultArray();

            $countMap = array_column($counts, 'total_hadir', 'prayer_session_id');

            foreach ($sessions as &$session) {
                $session['total_hadir'] = $countMap[$session['id']] ?? 0;
            }
        }

        return $sessions;
    }

    /**
     * Deactivate all sessions for a guru piket
     */
    public function deactivateAll(int $guruPiketId): void
    {
        $db = \Config\Database::connect();
        $db->table('prayer_sessions')
            ->where('guru_piket_id', $guruPiketId)
            ->where('is_active', 1)
            ->update(['is_active' => 0]);
    }
}
