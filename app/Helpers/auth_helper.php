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
                    'title' => 'Kelola Absensi',
                    'icon' => 'fas fa-clipboard-check',
                    'url' => '/admin/absensi'
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
                            'title' => 'Laporan Absensi Detail',
                            'url' => '/admin/laporan/absensi-detail'
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

if (!function_exists('get_dashboard_url')) {
    /**
     * Get dashboard URL based on user role
     */
    function get_dashboard_url($role = null)
    {
        if ($role === null) {
            $role = session()->get('role');
        }

        $dashboards = [
            'admin' => '/admin/dashboard',
            'guru_mapel' => '/guru/dashboard',
            'wali_kelas' => '/walikelas/dashboard',
            'siswa' => '/siswa/dashboard'
        ];

        return $dashboards[$role] ?? '/';
    }
}

if (!function_exists('is_absensi_editable')) {
    function is_absensi_editable($absensi) {
        if (empty($absensi) || empty($absensi['created_at'])) {
            return false;
        }

        // If admin unlocked, check against unlocked_at timestamp instead
        if (!empty($absensi['unlocked_at'])) {
            $unlockedAt = strtotime($absensi['unlocked_at']);
            $now = time();
            $diffHours = ($now - $unlockedAt) / 3600;
            return $diffHours <= 24; // Editable within 24 hours from unlock
        }

        // Otherwise, check against created_at (original behavior)
        $createdAt = strtotime($absensi['created_at']);
        $now = time();
        $diffHours = ($now - $createdAt) / 3600;
        return $diffHours <= 24; // Editable within 24 hours
    }
}

if (!function_exists('is_mobile_device')) {
    /**
     * Detect if the current device is mobile
     * 
     * @return bool
     */
    function is_mobile_device()
    {
        $request = \Config\Services::request();
        $userAgent = $request->getUserAgent();
        
        if ($userAgent->isMobile()) {
            return true;
        }
        
        // Additional mobile detection
        $mobileKeywords = [
            'Mobile', 'Android', 'iPhone', 'iPod', 'BlackBerry', 
            'Windows Phone', 'Opera Mini', 'IEMobile'
        ];
        
        $agent = $userAgent->getAgentString();
        foreach ($mobileKeywords as $keyword) {
            if (stripos($agent, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('is_tablet_device')) {
    /**
     * Detect if the current device is tablet
     * 
     * @return bool
     */
    function is_tablet_device()
    {
        $request = \Config\Services::request();
        $userAgent = $request->getUserAgent();
        
        // Check for tablet keywords
        $tabletKeywords = ['iPad', 'Android', 'Tablet', 'Kindle', 'Silk', 'PlayBook'];
        $agent = $userAgent->getAgentString();
        
        foreach ($tabletKeywords as $keyword) {
            if (stripos($agent, $keyword) !== false) {
                // Exclude phones
                if (stripos($agent, 'Mobile') === false || stripos($agent, 'iPad') !== false) {
                    return true;
                }
            }
        }
        
        return false;
    }
}

if (!function_exists('get_device_layout')) {
    /**
     * Get appropriate layout based on device type
     * 
     * @param string $defaultLayout Default layout to use (desktop_layout or mobile_layout)
     * @return string Layout name
     */
    function get_device_layout($defaultLayout = null)
    {
        // Check if user has manually set a preference in session
        $session = \Config\Services::session();
        $layoutPreference = $session->get('layout_preference');
        
        if ($layoutPreference !== null) {
            return $layoutPreference;
        }
        
        // If default layout is specified, use it
        if ($defaultLayout !== null) {
            return $defaultLayout;
        }
        
        // Auto-detect based on device
        if (is_mobile_device() && !is_tablet_device()) {
            return 'templates/mobile_layout';
        }
        
        return 'templates/desktop_layout';
    }
}

if (!function_exists('set_layout_preference')) {
    /**
     * Set user's layout preference in session
     * 
     * @param string $layout Layout preference (desktop_layout or mobile_layout)
     * @return void
     */
    function set_layout_preference($layout)
    {
        $session = \Config\Services::session();
        $session->set('layout_preference', $layout);
    }
}

if (!function_exists('clear_layout_preference')) {
    /**
     * Clear user's layout preference (return to auto-detection)
     * 
     * @return void
     */
    function clear_layout_preference()
    {
        $session = \Config\Services::session();
        $session->remove('layout_preference');
    }
}

if (!function_exists('get_device_type')) {
    /**
     * Get the device type as a string
     * 
     * @return string 'mobile', 'tablet', or 'desktop'
     */
    function get_device_type()
    {
        if (is_mobile_device() && !is_tablet_device()) {
            return 'mobile';
        } elseif (is_tablet_device()) {
            return 'tablet';
        }
        
        return 'desktop';
    }
}
