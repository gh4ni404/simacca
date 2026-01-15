<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username',
        'password',
        'role',
        'email',
        'profile_photo',
        'is_active',
        'created_at',
        'password_changed_at',
        'email_changed_at',
        'profile_photo_uploaded_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'username'      => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
        'password'      => 'required|min_length[6]',
        'role'          => 'required|in_list[admin,guru_mapel,wali_kelas,siswa]',
        'email'         => 'valid_email',
        'is_active'     => 'permit_empty|in_list[0,1]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Hash Password sebelum insert/update
     * Only hash if password is not already hashed
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $password = $data['data']['password'];
            
            // Check if password is already hashed (bcrypt hashes start with $2y$)
            // If not hashed yet, hash it
            if (!preg_match('/^\$2[ayb]\$.{56}$/', $password)) {
                $data['data']['password'] = password_hash($password, PASSWORD_DEFAULT);
                log_message('info', 'UserModel hashPassword - Password hashed for user');
            } else {
                log_message('info', 'UserModel hashPassword - Password already hashed, skipping');
            }
        }
        return $data;
    }

    /**
     * Cek Login user
     */
    public function checkLogin($username, $password)
    {
        $user = $this->where('username', $username)
            ->where('is_active', 1)
            ->first();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Get User by role
     */
    public function getByRole($role)
    {
        return $this->where('role',  $role)
            ->where('is_active', 1)
            ->findAll();
    }

    /**
     * Update last login (Jika diperlukan nanti)
     */
    public function updateLastLogin($userId)
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get user dengan join ke tabel guru/siswa
     */
    public function getUserWithDetail($userId)
    {
        $user = $this->find($userId);

        if (!$user) {
            return null;
        }

        // Load model yang diperlukan
        switch ($user['role']) {
            case 'guru_mapel':
            case 'wali_kelas':
                # code...
                $guruModel = new GuruModel();
                $user['detail'] = $guruModel->where('user_id', $userId)->first();
                break;

            case 'siswa':
                $siswaModel = new SiswaModel();
                $user['detail'] = $siswaModel->where('user_id', $userId)->first();
                break;

            default:
                # code...
                break;
        }
        return $user;
    }

    public function getRecentUsers($limit = 5) {
        return $this->orderBy('created_at', 'DESC')
        ->limit($limit)
        ->findAll();
    }

    /**
     * Check if user needs to complete profile
     * User needs to complete profile if they haven't changed password, email, or uploaded photo
     * 
     * @param int $userId User ID
     * @return bool True if profile needs completion, false otherwise
     */
    public function needsProfileCompletion($userId)
    {
        $user = $this->find($userId);
        
        if (!$user) {
            return false;
        }

        // Check if any of the tracking fields are null
        // User needs to complete profile if they haven't:
        // 1. Changed their password
        // 2. Set/changed their email
        // 3. Uploaded profile photo
        return empty($user['password_changed_at']) 
            || empty($user['email_changed_at']) 
            || empty($user['profile_photo_uploaded_at']);
    }
}
