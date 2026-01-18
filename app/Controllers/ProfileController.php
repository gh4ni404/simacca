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
            case 'wakakur':
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
            if ($role === 'guru_mapel' || $role === 'wali_kelas' || $role === 'wakakur') {
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
        
        $rules = [];
        $updateData = [];
        
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
        
        // Check if password is being changed
        $isPasswordChanged = false;
        $plainPassword = null;
        if (!empty($this->request->getPost('password'))) {
            $isPasswordChanged = true;
            $plainPassword = $this->request->getPost('password');
            $rules['password'] = 'required|min_length[6]';
            $rules['confirm_password'] = 'required|matches[password]';
        }

        // Validate
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle password change
        if ($isPasswordChanged) {
            // Don't hash here - let the Model's beforeUpdate callback handle it
            $updateData['password'] = $plainPassword;
            // Track password change timestamp
            $updateData['password_changed_at'] = date('Y-m-d H:i:s');
        }

        // Handle email change tracking BEFORE database update
        if (isset($updateData['email'])) {
            if ($updateData['email'] !== $userData['email']) {
                // Email is being changed
                $updateData['email_changed_at'] = date('Y-m-d H:i:s');
            } elseif (empty($userData['email_changed_at'])) {
                // Email is being set for first time (no previous timestamp)
                $updateData['email_changed_at'] = date('Y-m-d H:i:s');
            }
        }

        // Log what we're about to update (for debugging)
        log_message('info', 'ProfileController update - User ID: ' . $userId);
        log_message('info', 'ProfileController update - Update data: ' . json_encode($updateData));
        log_message('info', 'ProfileController update - Is password changed: ' . ($isPasswordChanged ? 'YES' : 'NO'));
        
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
            log_message('info', 'ProfileController update - Verified email_changed_at: ' . ($verifyUser['email_changed_at'] ?? 'NULL'));
            log_message('info', 'ProfileController update - Verified password_changed_at: ' . ($verifyUser['password_changed_at'] ?? 'NULL'));
            
            // Clear profile completion check cache after successful update
            session()->remove('profile_completed');
        } else {
            log_message('error', 'ProfileController update - Database update: FAILED');
            log_message('error', 'ProfileController update - Errors: ' . json_encode($this->userModel->errors()));
        }

        // Check if this is first time profile edit (password AND email never changed before)
        // We check BEFORE update to see the original state
        $isFirstProfileEdit = empty($userData['password_changed_at']) 
            && empty($userData['email_changed_at']);
        
        // Get the updated user data
        $updatedUser = $this->userModel->find($userId);
        
        // Log for debugging
        log_message('info', 'ProfileController update - Is first profile edit: ' . ($isFirstProfileEdit ? 'YES' : 'NO'));
        log_message('info', 'ProfileController update - Old password_changed_at: ' . ($userData['password_changed_at'] ?? 'NULL'));
        log_message('info', 'ProfileController update - Old email_changed_at: ' . ($userData['email_changed_at'] ?? 'NULL'));
        log_message('info', 'ProfileController update - New password_changed_at: ' . ($updatedUser['password_changed_at'] ?? 'NULL'));
        log_message('info', 'ProfileController update - New email_changed_at: ' . ($updatedUser['email_changed_at'] ?? 'NULL'));

        // Update session if username or email changed
        if (isset($updateData['username'])) {
            session()->set('username', $updateData['username']);
            log_message('info', 'ProfileController update - Session username updated');
        }
        
        if (isset($updateData['email'])) {
            session()->set('email', $updateData['email']);
            log_message('info', 'ProfileController update - Session email updated to: ' . $updateData['email']);
        }
        
        // Send welcome email if this is first time user edits their profile
        // (first time changing password AND email)
        if ($isFirstProfileEdit && $isPasswordChanged && !empty($updatedUser['email'])) {
            helper('email');
            
            // Get user's full name
            $fullName = $this->getUserFullName($userId, $role);
            
            log_message('info', 'ProfileController update - Sending welcome email (first profile edit)');
            
            // Send welcome email with new password
            $welcomeSent = send_welcome_email(
                $updatedUser['email'],
                $updatedUser['username'],
                $plainPassword, // Send the new password they just set
                $role,
                $fullName,
                $updatedUser['email'] // Pass email to display in template
            );
            
            if ($welcomeSent) {
                log_message('info', 'ProfileController update - Welcome email sent to: ' . $updatedUser['email']);
            } else {
                log_message('warning', 'ProfileController update - Failed to send welcome email to: ' . $updatedUser['email']);
            }
        }
        // For non-first-edit updates, send appropriate notifications
        else {
            helper('email');
            $fullName = $this->getUserFullName($userId, $role);
            
            log_message('info', 'ProfileController update - Sending regular update notifications');
            
            // Send email change notification (only if email actually changed)
            if (isset($updateData['email']) && $updateData['email'] !== $userData['email']) {
                $oldEmail = $userData['email'];
                $newEmail = $updateData['email'];
                
                log_message('info', 'ProfileController update - Email changed from: ' . ($oldEmail ?? 'NULL') . ' to: ' . $newEmail);
                
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
            }
            
            // Send password change notification (only if password changed)
            if ($isPasswordChanged && $plainPassword && !empty($updatedUser['email'])) {
                log_message('info', 'ProfileController update - Password changed, sending notification');
                
                $emailSent = send_password_changed_by_self_notification(
                    $updatedUser['email'],
                    $fullName,
                    $updatedUser['username'],
                    $plainPassword
                );
                
                if ($emailSent) {
                    log_message('info', 'ProfileController update - Self password change notification sent to: ' . $updatedUser['email']);
                } else {
                    log_message('warning', 'ProfileController update - Failed to send self password notification to: ' . $updatedUser['email']);
                }
            }
        }
        
        // Success message and redirect to dashboard
        helper('auth');
        $dashboardUrl = get_dashboard_url($role);
        
        if ($isFirstProfileEdit && $isPasswordChanged) {
            // Special message for first time profile completion with email check reminder
            session()->setFlashdata('success', 'Selamat datang di SIMACCA! 🎉 Profil Anda telah diperbarui. Silakan periksa email Bapak/Ibu untuk informasi akun lengkap (username, email, dan password). 📧✨');
        } else {
            session()->setFlashdata('success', 'Profil berhasil diperbarui! 🎉✨');
        }
        
        return redirect()->to($dashboardUrl);
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
                
                // Clear profile completion check cache
                session()->remove('profile_completed');

                // Delete old photo if exists (with path traversal protection)
                if ($oldPhoto) {
                    // Sanitize filename to prevent path traversal
                    $oldPhoto = basename($oldPhoto);
                    $fullPath = realpath($uploadPath . $oldPhoto);
                    
                    // Verify file is within upload directory before deleting
                    if ($fullPath && strpos($fullPath, realpath($uploadPath)) === 0 && file_exists($fullPath)) {
                        @unlink($fullPath); // @ suppresses error if file already deleted
                        log_message('info', 'Deleted old profile photo: ' . $oldPhoto);
                    }
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
            
            // Sanitize filename to prevent path traversal
            $photo = basename($photo);
            $fullPath = realpath($uploadPath . $photo);

            // Verify file is within upload directory before deleting
            if ($fullPath && strpos($fullPath, realpath($uploadPath)) === 0 && file_exists($fullPath)) {
                @unlink($fullPath); // @ suppresses error if file not found
                log_message('info', 'Profile photo deleted: ' . $photo);
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
