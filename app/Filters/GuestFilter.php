<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class GuestFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */

    /**
     * Before filter - redirect if user is already logged in
     * Supports remember_me_token: if valid cookie exists, redirect to dashboard
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // if user is logged in, redirect to dashboard
        if (session()->get('isLoggedIn')) {
            $role = session()->get('role');

            switch ($role) {
                case 'admin':
                    return redirect()->to('/admin/dashboard');
                case 'guru_mapel':
                    return redirect()->to('/guru/dashboard');
                case 'wali_kelas':
                    return redirect()->to('/walikelas/dashboard');
                case 'siswa':
                    return redirect()->to('/siswa/jurnal-pkl');
                
                default:
                    return redirect()->to('/');
            }
        }

        // Jika ada remember_me_token cookie yang valid, redirect ke dashboard
        // (AuthFilter akan handle auto-login saat akses protected route)
        $token = $_COOKIE['remember_me_token'] ?? null;
        if ($token) {
            $rememberModel = new \App\Models\RememberTokenModel();
            $userId = $rememberModel->validateToken($token);
            if ($userId) {
                // Token valid → load user role, redirect ke dashboard yang tepat
                $userModel = new \App\Models\UserModel();
                $user = $userModel->where('id', $userId)->where('is_active', 1)->first();
                if ($user) {
                    $userRoleModel = new \App\Models\UserRoleModel();
                    $allRoles = $userRoleModel->getRolesByUserId($user['id']);
                    if (empty($allRoles)) {
                        $allRoles = [$user['role']];
                    }

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
                            return redirect()->to($url);
                        }
                    }
                }
            }
        }

        return $request;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */

    /**
     * After filter
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here if needed
        return $response;
    }
}
