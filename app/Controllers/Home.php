<?php

namespace App\Controllers;

use App\Models\DashboardModel;

class Home extends BaseController
{
    public function index()
    {
        // Redirect to login if not logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Redirect to appropriate dashboard based on role
        return $this->redirectToDashboard();
    }

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
            case 'wakakur':
                return redirect()->to('/wakakur/dashboard');
            case 'siswa':
                return redirect()->to('/siswa/dashboard');
            default:
                return redirect()->to('/login');
        }
    }
}
