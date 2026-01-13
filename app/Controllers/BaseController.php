<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;
    protected $request;
    protected $helpers = ['form', 'url', 'session', 'auth'];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn()
    {
        return session()->get('isLoggedIn') === true;
    }

    /**
     * Get current user data
     */
    protected function getUserData()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id'            => session()->get('user_id') ?? session()->get('userId'),
            'username'      => session()->get('username'),
            'role'          => session()->get('role'),
            'email'         => session()->get('email'),
            'nama_lengkap'  => session()->get('nama_lengkap'),
            // Add other session data based on role
            'guru_id'       => session()->get('guru_id'),
            'siswa_id'      => session()->get('siswa_id'),
            'nip'           => session()->get('nip'),
            'nis'           => session()->get('nis'),
            'kelas_id'      => session()->get('kelas_id')
        ];
    }

    /**
     * Check  if user has specific role
     */
    protected function hasRole($role)
    {
        $userRole = session()->get('role');

        // Jika paramater adalah array, cek apakah user role ada dalam array
        if (is_array($role)) {
            return in_array($userRole, $role);
        }

        // Jika paramater adalah string, cek apakah sama
        return $userRole === $role;
    }

    /**
     * Redirect if user doesn't have required role
     */
    protected function requireRole($role)
    {
        if (!$this->hasRole($role)) {
            return redirect()->to('/access-denied')->with('error', 'Akses ditolak');
        }

        return true;
    }

    /**
     * Get current role with Indonesian translation
     */

    protected function getRoleName() {
        $role = session()->get('role');

        $roleNames = [
            'admin'         => 'Administrator',
            'guru_mapel'    => 'Guru Mata Pelajaran',
            'wali_kelas'    => 'Wali Kelas',
            'siswa'         => 'Siswa'
        ];

        // Ensure $role is a valid string before accessing array
        if (empty($role) || !is_string($role)) {
            return 'Unknown';
        }

        return $roleNames[$role] ?? 'Unknown';
    }

    protected function isAbsensiEditable($absensi)
    {
        // Check if $absensi is array and has created_at key
        if (!isset($absensi['created_at'])) {
            return false;
        }

        $createdAt = strtotime($absensi['created_at']);
        $now = time();
        $diffHours = ($now - $createdAt) / 3600;

        // Allow editing within 24 hours
        return $diffHours <= 24;
    }
}
