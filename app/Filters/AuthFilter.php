<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Before filter - check if user is logged in
     * Supports auto-login via remember_me_token cookie
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            // Coba auto-login dari remember_me_token cookie
            $token = $_COOKIE['remember_me_token'] ?? null;
            if ($token) {
                $autoLoggedIn = $this->autoLoginFromCookie($token);
                if ($autoLoggedIn) {
                    return $request; // Auto-login sukses, lanjut request
                }
                // Token invalid/expired → hapus cookie
                $this->clearRememberCookie();
            }

            // Save intended URL for redirect after login (only for non-AJAX requests)
            if (!$request->isAJAX()) {
                session()->set('redirect_url', current_url());
            }

            // Redirect to login page
            return redirect()->to('/login')->with('error', 'Login dulu dong 🔐');
        }

        // Update last activity time to keep session alive
        $lastActivity = session()->get('last_activity');
        $currentTime = time();
        
        // Update last activity every 5 minutes to extend session
        if (!$lastActivity || ($currentTime - $lastActivity) > 300) {
            session()->set('last_activity', $currentTime);
        }

        return $request;
    }

    /**
     * Auto-login user dari remember_me_token cookie
     * 
     * @param string $token plain text token dari cookie
     * @return bool true jika auto-login berhasil
     */
    private function autoLoginFromCookie(string $token): bool
    {
        $rememberModel = new \App\Models\RememberTokenModel();
        $userId = $rememberModel->validateToken($token);

        if (!$userId) {
            return false;
        }

        // Load user data
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('id', $userId)->where('is_active', 1)->first();

        if (!$user) {
            return false;
        }

        // Load roles
        $userRoleModel = new \App\Models\UserRoleModel();
        $allRoles = $userRoleModel->getRolesByUserId($user['id']);
        if (empty($allRoles)) {
            $allRoles = [$user['role']];
        }

        // Build session data (sama seperti processLogin)
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

        // Load data spesifik berdasarkan role
        $guruRoles = ['guru_mapel', 'wali_kelas', 'wakakur', 'ketua_jurusan'];
        $hasGuruRole = count(array_intersect($guruRoles, $allRoles)) > 0;

        if ($hasGuruRole) {
            $guruModel = new \App\Models\GuruModel();
            $guru = $guruModel->getByUserId($user['id']);
            if ($guru) {
                $sessionData['guru_id'] = $guru['id'];
                $sessionData['nama_lengkap'] = $guru['nama_lengkap'];
                $sessionData['nip'] = $guru['nip'];
                $sessionData['jurusan'] = $guru['jurusan'] ?? null;
                $sessionData['is_ketua_jurusan'] = $guru['is_ketua_jurusan'] ?? false;
                if (in_array('wali_kelas', $allRoles) && $guru['kelas_id']) {
                    $sessionData['kelas_id'] = $guru['kelas_id'];
                }
            }
        }

        if (in_array('siswa', $allRoles)) {
            $siswaModel = new \App\Models\SiswaModel();
            $siswa = $siswaModel->getByUserId($user['id']);
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

        // Set session
        session()->set($sessionData);
        session()->set('last_activity', time());
        session()->regenerate(false);

        // Rotate token: hapus lama, buat baru
        $newToken = $rememberModel->createToken($user['id'], 30);
        $expiry = time() + (30 * 24 * 60 * 60);
        setcookie('remember_me_token', $newToken, [
            'expires'  => $expiry,
            'path'     => '/',
            'domain'   => '',
            'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
            'httponly'  => true,
            'samesite' => 'Lax',
        ]);

        log_message('info', "Auto-login successful for user: {$user['username']} (ID: {$user['id']})");

        return true;
    }

    /**
     * Hapus cookie remember_me_token
     */
    private function clearRememberCookie(): void
    {
        setcookie('remember_me_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'domain'   => '',
            'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
            'httponly'  => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * After filter
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
