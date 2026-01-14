<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetTokenModel extends Model
{
    protected $table            = 'password_reset_tokens';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['email', 'token', 'created_at', 'expires_at', 'used_at'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Create a new password reset token
     * 
     * @param string $email
     * @return string Token
     */
    public function createToken(string $email): string
    {
        // Generate secure random token
        $token = bin2hex(random_bytes(32));
        
        // Delete any existing tokens for this email
        $this->where('email', $email)->delete();
        
        // Create new token with 1 hour expiration
        $this->insert([
            'email'      => $email,
            'token'      => hash('sha256', $token), // Store hashed token
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        ]);
        
        return $token; // Return plain token for email
    }

    /**
     * Verify and get token data
     * 
     * @param string $token
     * @return array|null
     */
    public function verifyToken(string $token): ?array
    {
        $hashedToken = hash('sha256', $token);
        
        $tokenData = $this->where('token', $hashedToken)
                          ->where('used_at', null)
                          ->where('expires_at >', date('Y-m-d H:i:s'))
                          ->first();
        
        return $tokenData;
    }

    /**
     * Mark token as used
     * 
     * @param string $token
     * @return bool
     */
    public function markAsUsed(string $token): bool
    {
        $hashedToken = hash('sha256', $token);
        
        return $this->where('token', $hashedToken)
                    ->set('used_at', date('Y-m-d H:i:s'))
                    ->update();
    }

    /**
     * Clean up expired tokens (should be run via cron job)
     * 
     * @return int Number of deleted tokens
     */
    public function cleanupExpired(): int
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))
                    ->delete();
    }

    /**
     * Clean up used tokens older than 24 hours
     * 
     * @return int Number of deleted tokens
     */
    public function cleanupUsed(): int
    {
        return $this->where('used_at IS NOT NULL')
                    ->where('used_at <', date('Y-m-d H:i:s', strtotime('-24 hours')))
                    ->delete();
    }
}
