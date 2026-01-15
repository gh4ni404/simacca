<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;

class ProfileController extends BaseController
{
    protected $userModel;
    protected $guruModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
    }

    /**
     * Display profile page
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Login dulu ya.');
        }

        $userId = session()->get('userId');
        $role = session()->get('role');

        $data = [
            'title' => 'Profil Saya',
            'user' => $this->getUserData(),
            'userData' => $this->userModel->find($userId),
            'validation' => \Config\Services::validation()
        ];

        // Get additional data based on role
        switch ($role) {
            case 'guru_mapel':
            case 'wali_kelas':
                $guru = $this->guruModel->getGuruWithMapel($this->guruModel->where('user_id', $userId)->first()['id'] ?? null);
                $data['guru'] = $guru;
                break;

            case 'siswa':
                $siswa = $this->siswaModel->getSiswaWithKelas($this->siswaModel->where('user_id', $userId)->first()['id'] ?? null);
                $data['siswa'] = $siswa;
                break;
        }

        return view('profile/index', $data);
    }

    /**
     * Get user's full name based on role
     * 
     * @param int $userId User ID
     * @param string $role User role
     * @return string Full name or username as fallback
     */
    private function getUserFullName(int $userId, string $role): string
    {
        $fullName = '';
        
        try {
            if ($role === 'guru_mapel' || $role === 'wali_kelas') {
                // Get from guru table
                $guru = $this->guruModel->where('user_id', $userId)->first();
                if ($guru && !empty($guru['nama_lengkap'])) {
                    $fullName = $guru['nama_lengkap'];
                }
            } elseif ($role === 'siswa') {
                // Get from siswa table
                $siswa = $this->siswaModel->where('user_id', $userId)->first();
                if ($siswa && !empty($siswa['nama_lengkap'])) {
                    $fullName = $siswa['nama_lengkap'];
                }
            }
            
            // Fallback to username if no full name found
            if (empty($fullName)) {
                $user = $this->userModel->find($userId);
                $fullName = $user['username'] ?? 'User';
            }
            
        } catch (\Exception $e) {
            log_message('error', 'ProfileController getUserFullName - Error: ' . $e->getMessage());
            $fullName = 'User';
        }
        
        return $fullName;
    }
    
    /**
     * Update profile
     */
    public function update()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Login dulu dong.');
        }

        $userId = session()->get('userId');
        $role = session()->get('role');
        
        // Get current user data
        $userData = $this->userModel->find($userId);
        
        // Check if this is password change only
        $isPasswordChangeOnly = $this->request->getPost('password_change_only') === '1';

        $rules = [];
        $updateData = [];

        if ($isPasswordChangeOnly) {
            // Password change only - don't update username or email
            if ($this->request->getPost('password')) {
                $rules['password'] = 'required|min_length[6]';
                $rules['confirm_password'] = 'required|matches[password]';
            } else {
                return redirect()->back()->with('error', 'Password baru harus diisi.');
            }
        } else {
            // Profile update (username and email)
            $rules['email'] = 'permit_empty|valid_email';
            
            // Build update data for profile
            $newUsername = $this->request->getPost('username');
            $updateData['username'] = $newUsername;
            
            // Jika username berubah, validasi unique (exclude current user)
            if ($newUsername != $userData['username']) {
                $rules['username'] = 'required|is_unique[users.username,id,' . $userId . ']';
            } else {
                // Username tidak berubah, tapi tetap required
                $rules['username'] = 'required';
            }
            
            // Only update email if it's provided
            $newEmail = $this->request->getPost('email');
            if (!empty($newEmail)) {
                $updateData['email'] = $newEmail;
            }
        }

        // Validate
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle password change
        // Store plain password for email notification
        $plainPassword = null;
        if ($this->request->getPost('password')) {
            $plainPassword = $this->request->getPost('password');
            // Don't hash here - let the Model's beforeUpdate callback handle it
            $updateData['password'] = $plainPassword;
            // Track password change timestamp
            $updateData['password_changed_at'] = date('Y-m-d H:i:s');
        }

        // Log what we're about to update (for debugging)
        log_message('info', 'ProfileController update - User ID: ' . $userId);
        log_message('info', 'ProfileController update - Update data: ' . json_encode($updateData));
        log_message('info', 'ProfileController update - Is password change only: ' . ($isPasswordChangeOnly ? 'YES' : 'NO'));
        
        // Update database - skip Model validation since we already validated in controller
        $this->userModel->skipValidation(true);
        $result = $this->userModel->update($userId, $updateData);
        $this->userModel->skipValidation(false); // Reset for next use
        
        // Log result
        if ($result) {
            log_message('info', 'ProfileController update - Database update: SUCCESS');
            
            // Verify the update by fetching from database
            $verifyUser = $this->userModel->find($userId);
            log_message('info', 'ProfileController update - Verified email in DB: ' . ($verifyUser['email'] ?? 'NULL'));
        } else {
            log_message('error', 'ProfileController update - Database update: FAILED');
            log_message('error', 'ProfileController update - Errors: ' . json_encode($this->userModel->errors()));
        }

        // Update session if username or email changed
        if (isset($updateData['username'])) {
            session()->set('username', $updateData['username']);
            log_message('info', 'ProfileController update - Session username updated');
        }
        
        // Handle email change notification
        if (isset($updateData['email']) && $updateData['email'] !== $userData['email']) {
            session()->set('email', $updateData['email']);
            // Track email change timestamp
            $updateData['email_changed_at'] = date('Y-m-d H:i:s');
            log_message('info', 'ProfileController update - Session email updated to: ' . $updateData['email']);
            
            // Send notification emails
            helper('email');
            
            $oldEmail = $userData['email'];
            $newEmail = $updateData['email'];
            
            // Get user's full name based on role
            $fullName = $this->getUserFullName($userId, $role);
            
            // Send to old email (security notification)
            if (!empty($oldEmail)) {
                $oldEmailSent = send_email_change_notification($oldEmail, $fullName, $oldEmail, $newEmail, true);
                if ($oldEmailSent) {
                    log_message('info', 'ProfileController update - Email change notification sent to old email: ' . $oldEmail);
                } else {
                    log_message('error', 'ProfileController update - Failed to send notification to old email: ' . $oldEmail);
                }
            }
            
            // Send to new email (confirmation)
            $newEmailSent = send_email_change_notification($newEmail, $fullName, $oldEmail, $newEmail, false);
            if ($newEmailSent) {
                log_message('info', 'ProfileController update - Email change notification sent to new email: ' . $newEmail);
            } else {
                log_message('error', 'ProfileController update - Failed to send notification to new email: ' . $newEmail);
            }
        } elseif (isset($updateData['email'])) {
            session()->set('email', $updateData['email']);
            // If email is being set for first time (even without change), track it
            if (empty($userData['email_changed_at'])) {
                $updateData['email_changed_at'] = date('Y-m-d H:i:s');
            }
            log_message('info', 'ProfileController update - Session email updated (no change detected)');
        }

        // Send email notification if password was changed
        if ($isPasswordChangeOnly && $plainPassword && !empty($userData['email'])) {
            helper('email');
            
            // Get user's full name
            $fullName = $this->getUserFullName($userId, $role);
            
            $emailSent = send_password_changed_by_self_notification(
                $userData['email'],
                $fullName,
                $userData['username'],
                $plainPassword  // Pass plain text password
            );
            
            if ($emailSent) {
                log_message('info', 'ProfileController update - Self password change notification sent to: ' . $userData['email']);
            } else {
                log_message('warning', 'ProfileController update - Failed to send self password notification to: ' . $userData['email']);
            }
        }
        
        // Success message
        if ($isPasswordChangeOnly) {
            session()->setFlashdata('success', 'Password berhasil diubah! 🔐✨');
        } else {
            session()->setFlashdata('success', 'Profil updated! Looking good 😎✨');
        }
        
        return redirect()->back();
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Login dulu ya.');
        }

        $userId = session()->get('userId');

        // Validation rules for photo
        $rules = [
            'profile_photo' => [
                'label' => 'Foto Profil',
                'rules' => 'uploaded[profile_photo]|max_size[profile_photo,5120]|is_image[profile_photo]|mime_in[profile_photo,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Pilih foto terlebih dahulu',
                    'max_size' => 'Ukuran foto maksimal 5MB',
                    'is_image' => 'File harus berupa gambar',
                    'mime_in' => 'Format foto harus JPG, JPEG, atau PNG'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('profile_photo');

        if ($file->isValid() && !$file->hasMoved()) {
            // Get old photo to delete
            $userData = $this->userModel->find($userId);
            $oldPhoto = $userData['profile_photo'] ?? null;

            // Generate new filename
            $newName = 'profile_' . $userId . '_' . time() . '.' . $file->getExtension();

            // Move file to upload directory
            $uploadPath = WRITEPATH . 'uploads/profile/';
            
            // Create directory if not exists
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            try {
                // Move uploaded file first
                $file->move($uploadPath, $newName);
                
                $filePath = $uploadPath . $newName;
                
                // Get original file size
                $originalSize = filesize($filePath);
                
                // Optimize image (compress without losing visible quality)
                helper('image');
                $optimized = optimize_profile_photo($filePath, $filePath);
                
                if ($optimized) {
                    $newSize = filesize($filePath);
                    $savings = round((($originalSize - $newSize) / $originalSize) * 100, 2);
                    log_message('info', "Profile photo optimized: {$newName} - {$savings}% smaller");
                }

                // Update database with timestamp
                $this->userModel->update($userId, [
                    'profile_photo' => $newName,
                    'profile_photo_uploaded_at' => date('Y-m-d H:i:s')
                ]);

                // Update session
                session()->set('profile_photo', $newName);

                // Delete old photo if exists
                if ($oldPhoto && file_exists($uploadPath . $oldPhoto)) {
                    unlink($uploadPath . $oldPhoto);
                }

                session()->setFlashdata('success', 'Foto profil berhasil diupdate! 📸✨');
            } catch (\Exception $e) {
                log_message('error', 'Profile photo upload error: ' . $e->getMessage());
                session()->setFlashdata('error', 'Gagal mengupload foto. Silakan coba lagi.');
            }
        } else {
            session()->setFlashdata('error', 'File tidak valid atau sudah dipindahkan.');
        }

        return redirect()->back();
    }

    /**
     * Delete profile photo
     */
    public function deletePhoto()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Login dulu ya.');
        }

        $userId = session()->get('userId');
        $userData = $this->userModel->find($userId);
        $photo = $userData['profile_photo'] ?? null;

        if ($photo) {
            $uploadPath = WRITEPATH . 'uploads/profile/';
            $filePath = $uploadPath . $photo;

            // Delete file if exists
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Update database
            $this->userModel->update($userId, ['profile_photo' => null]);

            // Update session
            session()->set('profile_photo', null);

            session()->setFlashdata('success', 'Foto profil berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Tidak ada foto profil untuk dihapus.');
        }

        return redirect()->back();
    }
}
