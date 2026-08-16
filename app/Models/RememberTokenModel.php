<?php

namespace App\Models;

use CodeIgniter\Model;

class RememberTokenModel extends Model
{
    protected $table            = 'remember_tokens';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'token_hash', 'expires_at', 'created_at'];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Generate token baru, simpan hash ke DB, return token asli
     * 
     * @param int $userId
     * @param int $expireDays berapa hari token valid
     * @return string token asli (plain text, untuk diset ke cookie)
     */
    public function createToken(int $userId, int $expireDays = 30): string
    {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        // Hapus token lama user ini (satu user satu token aktif)
        $this->where('user_id', $userId)->delete();

        $this->insert([
            'user_id'    => $userId,
            'token_hash' => $tokenHash,
            'expires_at' => date('Y-m-d H:i:s', strtotime("+{$expireDays} days")),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $token;
    }

    /**
     * Validasi token dari cookie, return user_id jika valid
     * 
     * @param string $token plain text token dari cookie
     * @return int|null user_id jika valid, null jika tidak
     */
    public function validateToken(string $token): ?int
    {
        $tokenHash = hash('sha256', $token);

        $row = $this->where('token_hash', $tokenHash)
                     ->where('expires_at >', date('Y-m-d H:i:s'))
                     ->first();

        return $row ? (int) $row['user_id'] : null;
    }

    /**
     * Hapus token berdasarkan hash
     * 
     * @param string $tokenHash
     * @return bool
     */
    public function deleteTokenByHash(string $tokenHash): bool
    {
        return $this->where('token_hash', $tokenHash)->delete() >= 0;
    }

    /**
     * Hapus semua token milik user (saat logout)
     * 
     * @param int $userId
     * @return bool
     */
    public function deleteUserTokens(int $userId): bool
    {
        return $this->where('user_id', $userId)->delete() >= 0;
    }

    /**
     * Hapus token yang sudah expired (untuk cron cleanup)
     * 
     * @return int jumlah token yang dihapus
     */
    public function cleanupExpired(): int
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))->delete();
    }
}
