<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $guruModel;
    protected $siswaModel;
    protected $passwordResetTokenModel;
    protected $appName;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
        $this->passwordResetTokenModel = new \App\Models\PasswordResetTokenModel();
        $this->appName = 'SIMACCA';
        
        // Load email helper
        helper('email');
    }

    // public function index()
    // {
    // }

    /**
     * Display login page
     */
    public function login()
    {
        // jika sudah login, redirect ke dashboard sesuai role
        if (session()->get('isLoggedIn')) {
            return $this->redirectToDashboard();
        }

        $data = [
            'title'         => 'Login - ' . $this->appName,
            'validation'    => \Config\Services::validation()
        ];

        return view('auth/login', $data);
    }

    /**
     * Process login
     */
    public function processLogin()
    {
        // Validasi input
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $messages = [
            'username' => [
                'required' => 'Username harus diisi'
            ],
            'password' => [
                'required' => 'Password harus diisi'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get Input
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Check login
        $user = $this->userModel->checkLogin($username, $password);

        if ($user) {
            // Set session data
            $sessionData = [
                'user_id'       => $user['id'],
                'userId'        => $user['id'], // Keep for backward compatibility
                'username'      => $user['username'],
                'role'          => $user['role'],
                'email'         => $user['email'],
                'profile_photo' => $user['profile_photo'] ?? null,
                'isLoggedIn'    => true,
                'loginTime'     => time(),
            ];

            // Get Additional data based on role
            switch ($user['role']) {
                case 'guru_mapel':
                case 'wali_kelas':
                    $guru = $this->guruModel->getByUserId($user['id']);
                    if ($guru) {
                        $sessionData['guru_id'] = $guru['id'];
                        $sessionData['nama_lengkap'] = $guru['nama_lengkap'];
                        $sessionData['nip'] = $guru['nip'];

                        // Jika Wali Kelas, simpan kelas_id
                        if ($user['role'] == 'wali_kelas' && $guru['kelas_id']) {
                            $sessionData['kelas_id'] = $guru['kelas_id'];
                        }
                    }
                    break;

                case 'siswa':
                    $siswa = $this->siswaModel->getByUserId($user['id']);
                    if ($siswa) {
                        $sessionData['siswa_id'] = $siswa['id'];
                        $sessionData['nama_lengkap'] = $siswa['nama_lengkap'];
                        $sessionData['nis'] = $siswa['nis'];
                        $sessionData['kelas_id'] = $siswa['kelas_id'];
                    }
                    break;

                case 'admin':
                    $sessionData['nama_lengkap'] = 'Administrator';
                    break;
            }

            // Set session first
            session()->set($sessionData);
            
            // Set initial last activity time
            session()->set('last_activity', time());
            
            // Then regenerate session ID to prevent session fixation attacks
            // Do this AFTER setting session data to prevent data loss
            session()->regenerate(false);

            // Update last login (jika ada field di database)
            // $this->userModel->updateLastLogin($user['id']);

            // Redirect to dashboard
            return $this->redirectToDashboard();
        } else {
            // Login Failed
            session()->setFlashdata('error', 'Hmm, username atau password kayaknya salah deh 🤔');
            return redirect()->to('/login')->withInput();
        }
    }

    /**
     * Redirect to dashboard based on role
     */
    private function redirectToDashboard()
    {
        $role = session()->get('role');

        switch ($role) {
            case 'admin':
                return redirect()->to('/admin/dashboard');
            case 'guru_mapel':
                return redirect()->to('/guru/dashboard');
            case 'wali_kelas':
                return redirect()->to('/walikelas/dashboard');
            case 'siswa':
                return redirect()->to('/siswa/dashboard');
            default:
                return redirect()->to('/');
        }
    }

    /**
     * Logout process
     */
    public function Logout()
    {
        // Get user info before destroying session
        $username = session()->get('username');
        
        // Remove all session data
        session()->remove('user_id');
        session()->remove('userId');
        session()->remove('username');
        session()->remove('role');
        session()->remove('email');
        session()->remove('profile_photo');
        session()->remove('isLoggedIn');
        session()->remove('loginTime');
        session()->remove('last_activity');
        session()->remove('guru_id');
        session()->remove('siswa_id');
        session()->remove('nama_lengkap');
        session()->remove('kelas_id');
        session()->remove('nip');
        session()->remove('nis');
        
        // Destroy session completely
        session()->destroy();

        // Redirect to login page
        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout');
    }

    /**
     * Forgot password page
     */
    public function forgotPassword()
    {
        $data = [
            'title' => 'Lupa Password - ' . $this->appName
        ];

        return view('auth/forgot_password', $data);
    }

    /**
     * Process forgot password
     */
    public function processForgotPassword()
    {
        // Validate input
        $rules = [
            'email' => 'required|valid_email'
        ];

        $messages = [
            'email' => [
                'required' => 'Email harus diisi',
                'valid_email' => 'Format email tidak valid'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');

        // Check if email exists in database
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            // Don't reveal if email exists or not (security best practice)
            session()->setFlashdata('success', 'Kalau email terdaftar, instruksi reset sudah dikirim 📧✨');
            return redirect()->to('/login');
        }

        try {
            // Create password reset token
            $token = $this->passwordResetTokenModel->createToken($email);

            // Send reset password email
            $emailSent = send_password_reset_email($email, $token, $user['username']);

            if ($emailSent) {
                session()->setFlashdata('success', 'Cek email ya! Instruksi reset sudah dikirim 📧✨');
            } else {
                log_message('error', 'Failed to send password reset email to: ' . $email);
                session()->setFlashdata('error', 'Gagal mengirim email. Silakan hubungi administrator.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Password reset error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan. Silakan coba lagi nanti.');
        }

        return redirect()->to('/login');
    }

    /**
     * Reset password page
     */
    public function resetPassword($token = null)
    {
        if (!$token) {
            return redirect()->to('/forgot-password')->with('error', 'Token tidak valid');
        }

        $data = [
            'title' => 'Reset Password - ' . $this->appName,
            'token' => $token
        ];

        return view('auth/reset_password', $data);
    }

    /**
     * Process reset password
     */
    public function processResetPassword()
    {
        // Validate input
        $rules = [
            'token' => 'required',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ];

        $messages = [
            'token' => [
                'required' => 'Token tidak valid'
            ],
            'password' => [
                'required' => 'Password baru harus diisi',
                'min_length' => 'Password minimal 6 karakter'
            ],
            'confirm_password' => [
                'required' => 'Konfirmasi password harus diisi',
                'matches' => 'Konfirmasi password tidak sama'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        // Verify token
        $tokenData = $this->passwordResetTokenModel->verifyToken($token);

        if (!$tokenData) {
            session()->setFlashdata('error', 'Token tidak valid atau sudah expired. Silakan request reset password lagi.');
            return redirect()->to('/forgot-password');
        }

        // Get user by email
        $user = $this->userModel->where('email', $tokenData['email'])->first();

        if (!$user) {
            session()->setFlashdata('error', 'User tidak ditemukan.');
            return redirect()->to('/login');
        }

        try {
            // Update password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $this->userModel->update($user['id'], ['password' => $hashedPassword]);

            // Mark token as used
            $this->passwordResetTokenModel->markAsUsed($token);

            session()->setFlashdata('success', 'Mantap! Password baru siap dipakai 🎉 Yuk login!');
            return redirect()->to('/login');
        } catch (\Exception $e) {
            log_message('error', 'Password reset update error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat mereset password. Silakan coba lagi.');
            return redirect()->back();
        }
    }

    /**
     * Change password page (for logged in users)
     */
    public function changePassword()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Login dulu ya biar bisa ganti password.');
        }

        $data = [
            'title' => 'Ubah Password - ' . $this->appName
        ];

        return view('auth/change_password', $data);
    }

    /**
     * Process change password
     */
    public function processChangePassword()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Login dulu ya.');
        }

        $rules = [
            'current_password'  => 'required',
            'new_password'      => 'required|min_length[6]',
            'confirm_password'  => 'required|matches[new_password]'
        ];

        $messages = [
            'current_password' => [
                'required' => 'Password saat ini harus diisi'
            ],
            'new_password' => [
                'required' => 'Password baru harus diisi',
                'min_length' => 'Password baru minimal 6 karakter'
            ],
            'confirm_password' => [
                'required' => 'Konfirmasi Password harus diisi',
                'min_length' => 'Konfirmasi password tidak sama dengan password baru'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        $userId = session()->get('userId');
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        // verify current password
        $user = $this->userModel->find($userId);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Passworword saat ini salah');
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->userModel->update($userId, ['password' => $hashedPassword]);

        session()->setFlashdata('success', 'Password updated! Jangan lupa dicatat ya 🔐✨');

        // redirect based on role
        return $this->redirectToDashboard();
    }

    /**
     * Access denied page
     */
    public function accessDenied()
    {
        $data = [
            'title' => 'Akses Ditolak - ' . $this->appName
        ];

        return view('auth/access_denied', $data);
    }
}
