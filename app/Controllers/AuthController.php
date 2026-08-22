<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\UserRoleModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $guruModel;
    protected $siswaModel;
    protected $userRoleModel;
    protected $passwordResetTokenModel;
    protected $rememberTokenModel;
    protected $appName;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
        $this->userRoleModel = new UserRoleModel();
        $this->passwordResetTokenModel = new \App\Models\PasswordResetTokenModel();
        $this->rememberTokenModel = new \App\Models\RememberTokenModel();
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
            $error = session()->getFlashdata('error');
            if ($error) {
                // Clear session to prevent redirect loop
                session()->destroy();
                return redirect()->to('/login')->with('error', $error);
            }
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
        $isAjax = $this->request->isAJAX();

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $messages = [
            'username' => [
                'required' => 'Username wajib diisi ya 😊'
            ],
            'password' => [
                'required' => 'Password wajib diisi ya 😊'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success'    => false,
                    'message'    => $this->validator->getErrors()[0] ?? 'Validasi gagal',
                    'csrf_token' => csrf_hash(),
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->checkLogin($username, $password);

        if ($user) {
            $allRoles = $this->userRoleModel->getRolesByUserId($user['id']);
            if (empty($allRoles)) {
                $allRoles = [$user['role']];
            }

            $sessionData = [
                'user_id'       => $user['id'],
                'userId'        => $user['id'],
                'username'      => $user['username'],
                'role'          => $user['role'],
                'all_roles'     => $allRoles,
                'email'         => $user['email'],
                'profile_photo' => $user['profile_photo'] ?? null,
                'isLoggedIn'    => true,
                'loginTime'     => time(),
            ];

            $guruRoles = ['guru_mapel', 'wali_kelas', 'wakakur', 'ketua_jurusan', 'kepala_sekolah', 'tendik'];
            $hasGuruRole = count(array_intersect($guruRoles, $allRoles)) > 0;

            if ($hasGuruRole) {
                $guru = $this->guruModel->getByUserId($user['id']);
                if ($guru) {
                    $sessionData['guru_id'] = $guru['id'];
                    $sessionData['nama_lengkap'] = $guru['nama_lengkap'];
                    $sessionData['nip'] = $guru['nip'];
                    $sessionData['jurusan'] = $guru['jurusan'] ?? null;
                    $sessionData['is_ketua_jurusan'] = (bool)($guru['is_ketua_jurusan'] ?? false);

                    // Dynamic Wali Kelas check for active academic year
                    $activeTahunAjaran = get_active_tahun_ajaran();
                    $kelasModel = new \App\Models\KelasModel();
                    $activeKelas = $kelasModel->getByWaliKelas($guru['id'], $activeTahunAjaran);

                    if ($activeKelas) {
                        $sessionData['kelas_id'] = $activeKelas['id'];
                        if (!in_array('wali_kelas', $allRoles)) {
                            $allRoles[] = 'wali_kelas';
                        }
                    } else {
                        $sessionData['kelas_id'] = null;
                        // If not assigned to any class in active TA, exclude wali_kelas from session all_roles
                        $allRoles = array_values(array_diff($allRoles, ['wali_kelas']));
                        if (empty($allRoles)) {
                            $allRoles = [$user['role']];
                        }
                    }
                    $sessionData['all_roles'] = $allRoles;
                }
            }

            if (in_array('siswa', $allRoles)) {
                $siswa = $this->siswaModel->getByUserId($user['id']);
                if ($siswa) {
                    $sessionData['siswa_id'] = $siswa['id'];
                    $sessionData['nama_lengkap'] = $siswa['nama_lengkap'];
                    $sessionData['nis'] = $siswa['nis'];
                    $sessionData['kelas_id'] = $siswa['kelas_id'];
                }
            }

            if (in_array('admin', $allRoles) && !isset($sessionData['nama_lengkap'])) {
                $sessionData['nama_lengkap'] = 'Administrator';
            }

            if (in_array('instruktur', $allRoles)) {
                $instrukturModel = new \App\Models\InstrukturPklModel();
                $instruktur = $instrukturModel->where('user_id', $user['id'])->first();
                if ($instruktur) {
                    $sessionData['instruktur_id'] = $instruktur['id'];
                    $sessionData['nama_lengkap'] = $instruktur['nama_lengkap'];
                }
            }

            session()->set($sessionData);
            session()->set('last_activity', time());
            session()->regenerate(false);

            // Auto set remember me cookie (30 hari)
            $token = $this->rememberTokenModel->createToken($user['id'], 30);
            $expiry = time() + (30 * 24 * 60 * 60); // 30 hari
            setcookie('remember_me_token', $token, [
                'expires'  => $expiry,
                'path'     => '/',
                'domain'   => '',
                'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
                'httponly'  => true,
                'samesite' => 'Lax',
            ]);

            if ($isAjax) {
                return $this->response->setJSON([
                    'success'      => true,
                    'message'      => 'Login berhasil!',
                    'redirect_url' => $this->getRedirectUrl(),
                    'username'     => $sessionData['nama_lengkap'] ?? $user['username'],
                    'csrf_token'   => csrf_hash(),
                ]);
            }

            return $this->redirectToDashboard();
        }

        if ($isAjax) {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'Hmm, username atau password kayaknya salah deh',
                'csrf_token' => csrf_hash(),
            ]);
        }

        session()->setFlashdata('error', 'Hmm, username atau password kayaknya salah deh 🤔');
        return redirect()->to('/login')->withInput();
    }

    /**
     * Get redirect URL based on user roles (for AJAX response)
     */
    private function getRedirectUrl()
    {
        $allRoles = session()->get('all_roles') ?? [session()->get('role')];

        $priority = [
            'admin'          => '/admin/dashboard',
            'wakakur'        => '/wakakur/dashboard',
            'ketua_jurusan'  => '/ketua-jurusan/dashboard',
            'wali_kelas'     => '/walikelas/dashboard',
            'guru_mapel'     => '/guru/dashboard',
            'instruktur'     => '/instruktur/dashboard',
            'siswa'          => '/siswa/jurnal-pkl',
        ];

        foreach ($priority as $role => $url) {
            if (in_array($role, $allRoles)) {
                return $url;
            }
        }

        return '/';
    }

    /**
     * Redirect to dashboard based on role
     * Multi-role: uses role priority order
     */
    private function redirectToDashboard()
    {
        return redirect()->to($this->getDashboardUrl());
    }

    /**
     * Get dashboard URL based on role (returns string, not redirect)
     */
    private function getDashboardUrl(): string
    {
        $allRoles = session()->get('all_roles') ?? [session()->get('role')];

        // Priority order for redirect
        $priority = [
            'admin'          => '/admin/dashboard',
            'kepala_sekolah' => '/kepala-sekolah/dashboard',
            'wakakur'        => '/wakakur/dashboard',
            'ketua_jurusan'  => '/ketua-jurusan/dashboard',
            'wali_kelas'     => '/walikelas/dashboard',
            'guru_mapel'     => '/guru/dashboard',
            'tendik'         => '/tendik/dashboard',
            'instruktur'     => '/instruktur/dashboard',
            'siswa'          => '/siswa/jurnal-pkl',
        ];

        foreach ($priority as $role => $url) {
            if (in_array($role, $allRoles)) {
                return $url;
            }
        }

        return '/';
    }

    /**
     * Logout process
     */
    public function Logout()
    {
        // Hapus remember me token dari DB & cookie
        $token = $_COOKIE['remember_me_token'] ?? null;
        if ($token) {
            $tokenHash = hash('sha256', $token);
            $this->rememberTokenModel->deleteTokenByHash($tokenHash);
            setcookie('remember_me_token', '', [
                'expires'  => time() - 3600,
                'path'     => '/',
                'domain'   => '',
                'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
                'httponly'  => true,
                'samesite' => 'Lax',
            ]);
        }

        // Get user info before destroying session
        $username = session()->get('username');
        
        // Remove all session data
        session()->remove('user_id');
        session()->remove('userId');
        session()->remove('username');
        session()->remove('role');
        session()->remove('all_roles');
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
        session()->remove('jurusan');
        session()->remove('is_ketua_jurusan');
        
        // Destroy session completely
        session()->destroy();

        // Redirect to login page
        return redirect()->to('/login')->with('success', 'Logout berhasil ya! 👋');
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
                'required' => 'Email wajib diisi ya 😊',
                'valid_email' => 'Format email nggak valid 🤔'
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
                session()->setFlashdata('error', 'Gagal mengirim email nih 😅 Hubungi admin ya!');
            }
        } catch (\Exception $e) {
            log_message('error', 'Password reset error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Ups, ada kesalahan nih 😅 Coba lagi nanti ya.');
        }

        return redirect()->to('/login');
    }

    /**
     * Reset password page
     */
    public function resetPassword($token = null)
    {
        if (!$token) {
            return redirect()->to('/forgot-password')->with('error', 'Token nggak valid nih 🤔');
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
                'required' => 'Token nggak valid nih 🤔'
            ],
            'password' => [
                'required' => 'Password baru wajib diisi ya 😊',
                'min_length' => 'Password baru minimal 6 karakter ya'
            ],
            'confirm_password' => [
                'required' => 'Konfirmasi password wajib diisi ya 😊',
                'matches' => 'Konfirmasi password nggak sama 🤔'
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
            session()->setFlashdata('error', 'Token nggak valid atau udah expired. Request reset password lagi ya 🔄');
            return redirect()->to('/forgot-password');
        }

        // Get user by email
        $user = $this->userModel->where('email', $tokenData['email'])->first();

        if (!$user) {
            session()->setFlashdata('error', 'User nggak ketemu nih 🤔');
            return redirect()->to('/login');
        }

        try {
            // Update password - let Model's beforeUpdate callback handle hashing
            $this->userModel->update($user['id'], ['password' => $password]);

            // Mark token as used
            $this->passwordResetTokenModel->markAsUsed($token);

            session()->setFlashdata('success', 'Mantap! Password baru siap dipakai 🎉 Yuk login!');
            return redirect()->to('/login');
        } catch (\Exception $e) {
            log_message('error', 'Password reset update error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Ups, gagal reset password nih 😅 Coba lagi ya.');
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
                'required' => 'Password lama wajib diisi ya 😊'
            ],
            'new_password' => [
                'required' => 'Password baru wajib diisi ya 😊',
                'min_length' => 'Password baru minimal 6 karakter ya'
            ],
            'confirm_password' => [
                'required' => 'Konfirmasi password wajib diisi ya 😊',
                'min_length' => 'Konfirmasi password nggak sama dengan password baru 🤔'
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
            return redirect()->back()->withInput()->with('error', 'Password salah nih 🤔');
        }

        // Update password - let Model's beforeUpdate callback handle hashing
        $this->userModel->update($userId, ['password' => $newPassword]);

        // Send email notification if user has email
        if (!empty($user['email'])) {
            helper('email');
            
            // Get user's full name based on role
            $fullName = $user['username']; // Default fallback
            $role = session()->get('role');
            
            if ($role === 'guru_mapel' || $role === 'wali_kelas') {
                $guru = $this->guruModel->where('user_id', $userId)->first();
                if ($guru && !empty($guru['nama_lengkap'])) {
                    $fullName = $guru['nama_lengkap'];
                }
            } elseif ($role === 'siswa') {
                $siswa = $this->siswaModel->where('user_id', $userId)->first();
                if ($siswa && !empty($siswa['nama_lengkap'])) {
                    $fullName = $siswa['nama_lengkap'];
                }
            }
            
            $emailSent = send_password_changed_by_self_notification(
                $user['email'],
                $fullName,
                $user['username'],
                $newPassword
            );
            
            if ($emailSent) {
                log_message('info', 'AuthController processChangePassword - Password change notification sent to: ' . $user['email']);
            } else {
                log_message('warning', 'AuthController processChangePassword - Failed to send password notification to: ' . $user['email']);
            }
        }

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
