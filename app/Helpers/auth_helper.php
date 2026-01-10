<?php
if (!function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     */
    function is_logged_in()
    {
        $session = \Config\Services::session();
        return $session->get('isLoggedIn') === true;
    }
}

if (!function_exists('get_user_data')) {
    /**
     * Get current user data from session
     */
    function get_user_data()
    {
        $session = \Config\Services::session();

        if (!is_logged_in()) {
            return null;
        }

        return [
            'id'            => $session->get('userId'),
            'username'      => $session->get('username'),
            'role'          => $session->get('role'),
            'email'         => $session->get('email'),
            'nama_lengkap'  => $session->get('nama_lengkap'),
            'guru_id'       => $session->get('guru_id'),
            'siswa_id'      => $session->get('siswa_id'),
            'nip'           => $session->get('nip'),
            'nis'           => $session->get('nis'),
            'kelas_id'      => $session->get('kelas_id'),
        ];
    }
}

if (!function_exists('has_role')) {
    /**
     * Check if user has specific role
     */
    function has_role($role)
    {
        $session = \Config\Services::session();
        $userRole = $session->get('role');

        if (is_array($role)) {
            return in_array($userRole, $role);
        }

        return $userRole === $role;
    }
}

if (!function_exists('require_role')) {
    /**
     * Redirect if user doesn't have required role
     */
    function require_role($role)
    {
        if (!has_role($role)) {
            return redirect()->to('/access-denied')->with('error', 'Akses ditolak');
        }

        return true;
    }
}

if (!function_exists('get_role_name')) {
    /**
     * Get role name in indonesian
     */
    function get_role_name()
    {
        $session = \Config\Services::session();
        $role = $session->get('role');

        $roleNames = [
            'admin'         => 'Administrator',
            'guru_mapel'    => 'Guru Mata Pelajaran',
            'wali_kelas'    => 'Wali Kelas',
            'siswa'         => 'Siswa'
        ];

        return $roleNames[$role] ?? 'Unknown';
    }
}

if (!function_exists('get_greeting')) {
    /**
     * Get greeting based on time
     */
    function get_greeting()
    {
        $hour = date('H');

        if ($hour >= 5 && $hour < 11) {
            return 'Selamat Pagi';
        } else if ($hour >= 11 && $hour < 15) {
            return 'Selamat Siang';
        } else if ($hour >= 15 && $hour < 19) {
            return 'Selamat Sore';
        } else {
            return 'Selamat Malam';
        }
    }
}

if (!function_exists('check_access')) {
    /**
     * Check access for menu items
     */
    function check_access($allowedRoles = [])
    {
        if (empty($allowedRoles)) {
            return true;
        }

        $userRole = session()->get('role');
        return in_array($userRole, $allowedRoles);
    }
}

if (!function_exists('get_sidebar_menu')) {
    /**
     * Get sidebar menu based on user role
     */
    function get_sidebar_menu()
    {
        $userRole = session()->get('role');

        $menus = [
            'admin' => [
                [
                    'title' => 'Dashboard',
                    'icon' => 'fas fa-tachometer-alt',
                    'url' => '/admin/dashboard',
                    'active' => ['admin/dashboard']
                ],
                [
                    'title' => 'Manajemen User',
                    'icon' => 'fas fa-users',
                    'url' => '#',
                    'submenu' => [
                        [
                            'title' => 'Data Guru',
                            'url' => '/admin/guru',
                        ],
                        [
                            'title' => 'Data Siswa',
                            'url' => '/admin/siswa'
                        ],
                        [
                            'title' => 'Data Kelas',
                            'url' => '/admin/kelas'
                        ],
                    ]
                ],
                [
                    'title' => 'Mata Pelajaran',
                    'url' => '/admin/mata-pelajaran'
                ],
                [
                    'title' => 'Jadwal Mengajar',
                    'icon' => 'fas fa-calendar-alt',
                    'url' => '/admin/jadwal'
                ],
                [
                    'title' => 'Laporan',
                    'icon' => 'fas fa-chart-bar',
                    'url' => '#',
                    'submenu' => [
                        [
                            'title' => 'Rekap Absensi',
                            'url' => '/admin/laporan/absensi'
                        ],
                        [
                            'title' => 'Statistik',
                            'url' => '/admin/laporan/statistik'
                        ]
                    ]
                ]
            ],
            'guru_mapel' => [
                [
                    'title' => 'Dashboard',
                    'icon' => 'fas fa=tachometer-alt',
                    'url' => '/guru/dashboard',
                    'active' => ['guru/dashboard']
                ],
                [
                    'title' => 'Absensi Siswa',
                    'icon' => 'fas fa-clipboard-check',
                    'url' => '/guru/absensi'
                ],
                [
                    'title' => 'Jurnal KBM',
                    'icon' => 'fas fa-book',
                    'url' => '/guru/jurnal'
                ],
                [
                    'title' => 'Laporan',
                    'icon' => 'fas fa-chart-bar',
                    'url' => '/guru/laporan'
                ]
            ],
            'wali_kelas' => [
                [
                    'title' => 'Dashboard',
                    'icon' => 'fas fa-tachometer-alt',
                    'url' => '/walikelas/dashboard',
                    'active' => ['walikelas/dashboard']
                ],
                [
                    'title' => 'Data Siswa',
                    'icon' => 'fas fa-users',
                    'url' => '/walikelas/siswa'
                ],
                [
                    'title' => 'Monitoring Absensi',
                    'icon' => 'fas fa-clipboard-check',
                    'url' => '/walikelas/absensi'
                ],
                [
                    'title' => 'Persetujuan Izin',
                    'icon' => 'fas fa-check-circle',
                    'url' => 'walikelas/izin'
                ],
                [
                    'title' => 'Laporan',
                    'icon' => 'fas fa-chart-bar',
                    'url' => '/walikelas/laporan'
                ]
            ],
            'siswa' => [
                [
                    'title' => 'Dashboard',
                    'icon' => 'fas fa-tachometer-alt',
                    'url' => '/siswa/dashboard',
                    'active' => ['siswa/dashboard']
                ],
                [
                    'title' => 'Jadwal Pelajaran',
                    'icon' => 'fas fa-calender-alt',
                    'url' => '/siswa/jadwal'
                ],
                [
                    'title' => 'Absensi',
                    'icon' => 'fas fa-clipboard-check',
                    'url' => '/siswa/absensi'
                ],
                [
                    'title' => 'Profil',
                    'icon' => 'fas fa-user',
                    'url' => '/siswa/profil'
                ]
            ]
        ];

        return $menus[$userRole] ?? [];
    }
}

if (!function_exists('get_status_badge')) {
    /**
     * Get Status badge HTML
     */
    function get_status_badge($status)
    {
        $badges = [
            'active' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'inactive' => 'bg-gray-100 text-gray-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'rejected' => 'bg-red-100 text-red-800',
            'hadir' => 'bg-green-100 text-green-800',
            'izin' => 'bg-blue-100 text-blue-800',
            'sakit' => 'bg-yellow-100 text-yellow-800',
            'alpa' => 'bg-red-100 text-red-800',
        ];

        $class = $badges[$status] ?? 'bg-gray-100 text-gray-800';
        return '<span class="px-2 py-1 text-xs font-medium rounded full ' . $class . '">' . ucfirst($status) . '</span>';
    }
}

if (!function_exists('is_absensi_editable')) {
    function is_absensi_editable($absensi) {
        if (empty($absensi) || empty($absensi['created_at'])) {
            return false;
        }

        $createdAt = strtotime($absensi['created_at']);
        $now = time();
        $diffHours = ($now - $createdAt) / 3600;
        return $diffHours <= 24; // Editable within 24 hours
    }
}
