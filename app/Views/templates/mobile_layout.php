<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title><?= $title ?? 'Sistem Absensi'; ?> - <?= get_role_name(); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.ico') ?>">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#6B7280',
                        success: '#10B981',
                        warning: '#F59E0B',
                        danger: '#EF4444',
                        info: '#3ABFF8'
                    }
                }
            }
        }
    </script>
    <style>
        /* Mobile-optimized UI helpers */
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Mobile-friendly buttons */
        .btn { 
            display:inline-flex; 
            align-items:center; 
            justify-content:center;
            gap:.5rem; 
            padding:.75rem 1.25rem; 
            border-radius:.75rem; 
            font-weight:600;
            min-height: 44px; /* iOS touch target */
            touch-action: manipulation;
        }
        .btn-primary { background:#3B82F6; color:#fff; }
        .btn-primary:active { background:#2563EB; }
        .btn-secondary { background:#E5E7EB; color:#111827; }
        .btn-secondary:active { background:#D1D5DB; }
        .btn-danger { background:#EF4444; color:#fff; }
        .btn-danger:active { background:#DC2626; }
        
        .badge { display:inline-flex; align-items:center; padding:.25rem .625rem; font-size:.875rem; border-radius:9999px; }
        .badge-green { background:#D1FAE5; color:#065F46; }
        .badge-yellow { background:#FEF3C7; color:#92400E; }
        .badge-red { background:#FEE2E2; color:#991B1B; }
        
        .card { background:#fff; border-radius:1rem; box-shadow:0 1px 3px rgba(0,0,0,0.1); margin-bottom:1rem; }
        .card-header { padding:1rem; border-bottom:1px solid #E5E7EB; font-weight:600; }
        .card-body { padding:1rem; }
        
        .flash { display:flex; align-items:flex-start; gap:.75rem; border-radius:.75rem; padding:1rem; margin-bottom:1rem; }
        .flash-success { background:#ECFDF5; color:#065F46; border:1px solid #A7F3D0; }
        .flash-error { background:#FEF2F2; color:#991B1B; border:1px solid #FECACA; }
        .flash-warn { background:#FFFBEB; color:#92400E; border:1px solid #FDE68A; }
        .flash .close { margin-left:auto; color:inherit; cursor:pointer; }
        
        /* Mobile-specific navigation */
        .mobile-nav {
            position: sticky;
            top: 0;
            z-index: 40;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .mobile-menu {
            max-height: calc(100vh - 64px);
            overflow-y: auto;
        }
        
        /* Bottom navigation for mobile */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid #E5E7EB;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
            z-index: 50;
            padding-bottom: env(safe-area-inset-bottom);
        }
        
        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            color: #6B7280;
            font-size: 0.75rem;
            text-decoration: none;
            min-height: 56px;
        }
        
        .bottom-nav-item.active {
            color: #3B82F6;
        }
        
        .bottom-nav-item i {
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }
        
        /* Content padding for bottom nav */
        .mobile-content {
            padding-bottom: 80px;
        }
        
        /* Swipe gestures */
        .swipeable {
            touch-action: pan-y;
        }
        
        /* Tap highlights */
        * {
            -webkit-tap-highlight-color: rgba(59, 130, 246, 0.1);
        }
    </style>
    <?= $this->renderSection('styles'); ?>
</head>

<body class="bg-gray-100">
    <!-- Mobile Top Navigation -->
    <nav class="mobile-nav">
        <div class="px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo & Title -->
                <div class="flex items-center space-x-2">
                    <i class="fas fa-graduation-cap text-indigo-600 text-xl"></i>
                    <span class="text-lg font-bold text-gray-800">SIMACCA</span>
                </div>

                <!-- Right side buttons -->
                <div class="flex items-center space-x-2">
                    <?php if (is_logged_in()): ?>
                        <!-- User profile button -->
                        <button type="button" id="mobile-user-menu-button"
                            class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <?php if (session()->get('profile_photo')): ?>
                                <img src="<?= base_url('profile-photo/' . esc(session()->get('profile_photo'))); ?>" 
                                     alt="<?= esc(session()->get('nama_lengkap') ?? session()->get('username')); ?>"
                                     class="h-9 w-9 rounded-full object-cover border-2 border-indigo-200">
                            <?php else: ?>
                                <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-600 font-semibold text-xs">
                                        <?= strtoupper(substr(session()->get('nama_lengkap') ?? session()->get('username') ?? 'U', 0, 2)); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </button>
                        
                        <!-- Menu button -->
                        <button type="button" id="mobile-menu-button"
                            class="p-2 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-100 focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    <?php else: ?>
                        <a href="<?= base_url('login'); ?>"
                            class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium">
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Slide-out -->
        <div id="mobile-menu" class="hidden">
            <div class="fixed inset-0 z-50">
                <!-- Backdrop -->
                <div id="mobile-menu-backdrop" class="fixed inset-0 bg-black bg-opacity-50"></div>
                
                <!-- Menu panel -->
                <div class="fixed top-0 right-0 w-80 max-w-full h-full bg-white shadow-xl mobile-menu">
                    <!-- Menu header -->
                    <div class="flex justify-between items-center p-4 border-b">
                        <div>
                            <p class="text-sm font-medium text-gray-900"><?= session()->get('nama_lengkap'); ?></p>
                            <p class="text-xs text-gray-500"><?= get_role_name(); ?></p>
                        </div>
                        <button type="button" id="mobile-menu-close" class="p-2 text-gray-500">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Menu items -->
                    <div class="overflow-y-auto p-4">
                        <?php if (is_logged_in()): ?>
                            <?php $menu = get_sidebar_menu(); ?>
                            <div class="space-y-2">
                                <?php foreach ($menu as $item): ?>
                                    <?php if (isset($item['submenu'])): ?>
                                        <div class="mb-4">
                                            <div class="px-3 py-2 font-semibold text-gray-700 text-sm uppercase tracking-wide">
                                                <?= $item['title']; ?>
                                            </div>
                                            <?php foreach ($item['submenu'] as $subitem): ?>
                                                <a href="<?= base_url($subitem['url']); ?>"
                                                    class="block px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg">
                                                    <?= $subitem['title']; ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <a href="<?= base_url($item['url']); ?>"
                                            class="block px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg font-medium">
                                            <?= $item['title']; ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- User actions -->
                            <div class="mt-6 pt-6 border-t space-y-2">
                                <a href="<?= base_url('profile'); ?>"
                                    class="block px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg">
                                    <i class="fas fa-user-circle mr-2"></i> Profil
                                </a>
                                <a href="<?= base_url('change-password'); ?>"
                                    class="block px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg">
                                    <i class="fas fa-key mr-2"></i> Ubah Password
                                </a>
                                <a href="<?= base_url('logout'); ?>"
                                    class="block px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- User Quick Info (collapsible) -->
    <?php if (is_logged_in()): ?>
    <div id="user-info-panel" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-3 hidden">
        <div class="flex items-center space-x-3">
            <?php if (session()->get('profile_photo')): ?>
                <img src="<?= base_url('profile-photo/' . esc(session()->get('profile_photo'))); ?>" 
                     class="h-12 w-12 rounded-full object-cover border-2 border-white">
            <?php else: ?>
                <div class="h-12 w-12 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                    <span class="text-white font-semibold">
                        <?= strtoupper(substr(session()->get('nama_lengkap') ?? session()->get('username') ?? 'U', 0, 2)); ?>
                    </span>
                </div>
            <?php endif; ?>
            <div class="flex-1">
                <p class="font-semibold"><?= session()->get('nama_lengkap'); ?></p>
                <p class="text-sm opacity-90"><?= get_role_name(); ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="mobile-content">
        <div class="px-4 py-4">
            <!-- Page Header -->
            <div class="mb-4">
                <?= $this->renderSection('actions'); ?>
            </div>

            <!-- Flash Messages -->
            <?= render_alerts() ?>

            <!-- Content -->
            <?= $this->renderSection('content'); ?>
        </div>
    </main>

    <!-- Bottom Navigation -->
    <?php if (is_logged_in()): ?>
    <nav class="bottom-nav">
        <div class="flex justify-around items-center">
            <?php 
            $role = session()->get('role');
            $currentUrl = uri_string();
            
            // Define bottom nav items based on role
            $bottomNavItems = [];
            if ($role === 'admin') {
                $bottomNavItems = [
                    ['url' => 'admin/dashboard', 'icon' => 'fas fa-home', 'label' => 'Beranda'],
                    ['url' => 'admin/guru', 'icon' => 'fas fa-users', 'label' => 'Guru'],
                    ['url' => 'admin/siswa', 'icon' => 'fas fa-user-graduate', 'label' => 'Siswa'],
                    ['url' => 'admin/laporan/absensi', 'icon' => 'fas fa-chart-bar', 'label' => 'Laporan'],
                ];
            } elseif ($role === 'guru_mapel') {
                $bottomNavItems = [
                    ['url' => 'guru/dashboard', 'icon' => 'fas fa-home', 'label' => 'Beranda'],
                    ['url' => 'guru/absensi', 'icon' => 'fas fa-clipboard-check', 'label' => 'Absensi'],
                    ['url' => 'guru/jurnal', 'icon' => 'fas fa-book', 'label' => 'Jurnal'],
                    ['url' => 'guru/laporan', 'icon' => 'fas fa-chart-bar', 'label' => 'Laporan'],
                ];
            } elseif ($role === 'wali_kelas') {
                $bottomNavItems = [
                    ['url' => 'walikelas/dashboard', 'icon' => 'fas fa-home', 'label' => 'Beranda'],
                    ['url' => 'walikelas/siswa', 'icon' => 'fas fa-users', 'label' => 'Siswa'],
                    ['url' => 'walikelas/absensi', 'icon' => 'fas fa-clipboard-check', 'label' => 'Absensi'],
                    ['url' => 'walikelas/izin', 'icon' => 'fas fa-check-circle', 'label' => 'Izin'],
                ];
            } elseif ($role === 'siswa') {
                $bottomNavItems = [
                    ['url' => 'siswa/dashboard', 'icon' => 'fas fa-home', 'label' => 'Beranda'],
                    ['url' => 'siswa/jadwal', 'icon' => 'fas fa-calendar', 'label' => 'Jadwal'],
                    ['url' => 'siswa/absensi', 'icon' => 'fas fa-clipboard-check', 'label' => 'Absensi'],
                    ['url' => 'siswa/profil', 'icon' => 'fas fa-user', 'label' => 'Profil'],
                ];
            }
            
            foreach ($bottomNavItems as $item):
                $isActive = strpos($currentUrl, $item['url']) !== false;
                $activeClass = $isActive ? 'active' : '';
            ?>
                <a href="<?= base_url($item['url']); ?>" class="bottom-nav-item <?= $activeClass ?>">
                    <i class="<?= $item['icon'] ?>"></i>
                    <span><?= $item['label'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>
    <?php endif; ?>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuClose = document.getElementById('mobile-menu-close');
        const mobileMenuBackdrop = document.getElementById('mobile-menu-backdrop');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.remove('hidden');
            });
        }
        
        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
            });
        }
        
        if (mobileMenuBackdrop) {
            mobileMenuBackdrop.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
            });
        }
        
        // User info panel toggle
        const mobileUserMenuButton = document.getElementById('mobile-user-menu-button');
        const userInfoPanel = document.getElementById('user-info-panel');
        
        if (mobileUserMenuButton && userInfoPanel) {
            mobileUserMenuButton.addEventListener('click', function() {
                userInfoPanel.classList.toggle('hidden');
            });
        }

        // Flash close buttons
        document.querySelectorAll('.flash .close').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const wrap = e.currentTarget.closest('.flash');
                if (!wrap) return;
                wrap.style.transition = 'opacity 0.2s';
                wrap.style.opacity = 0;
                setTimeout(() => wrap.remove(), 200);
            });
        });

        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            const flashMessages = document.querySelectorAll('.flash');
            flashMessages.forEach(message => {
                message.style.transition = 'opacity 0.5s';
                message.style.opacity = '0';
                setTimeout(() => message.remove(), 500);
            });
        }, 5000);
        
        // Prevent double-tap zoom on buttons
        document.querySelectorAll('.btn, .bottom-nav-item').forEach(element => {
            element.addEventListener('touchend', function(e) {
                e.preventDefault();
                this.click();
            }, { passive: false });
        });
    </script>

    <?= $this->renderSection('scripts'); ?>

    <!-- flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize flatpickr for time pickers with mobile settings
            flatpickr('.timepicker', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 1,
                allowInput: true,
                // Mobile-friendly settings
                disableMobile: false,
                clickOpens: true
            });

            // Time validation
            function toMinutes(hhmm) {
                if (!hhmm) return null;
                const parts = hhmm.split(':');
                if (parts.length !== 2) return null;
                const h = parseInt(parts[0], 10);
                const m = parseInt(parts[1], 10);
                if (Number.isNaN(h) || Number.isNaN(m)) return null;
                return h * 60 + m;
            }

            const jamMulaiEl = document.getElementById('jam_mulai');
            const jamSelesaiEl = document.getElementById('jam_selesai');

            if (jamMulaiEl && jamSelesaiEl) {
                function validateRange() {
                    const jm = toMinutes(jamMulaiEl.value);
                    const js = toMinutes(jamSelesaiEl.value);
                    if (jm !== null && js !== null && js <= jm) {
                        alert('Jam selesai harus lebih besar dari jam mulai!');
                        jamSelesaiEl.value = '';
                        if (jamSelesaiEl._flatpickr) jamSelesaiEl._flatpickr.clear();
                    }
                }

                jamMulaiEl.addEventListener('change', validateRange);
                jamSelesaiEl.addEventListener('change', validateRange);

                const form = jamMulaiEl.closest('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const jm = toMinutes(jamMulaiEl.value);
                        const js = toMinutes(jamSelesaiEl.value);
                        if (jm === null || js === null) {
                            e.preventDefault();
                            alert('Mohon isi jam dengan format HH:MM (24 jam).');
                        } else if (js <= jm) {
                            e.preventDefault();
                            alert('Jam selesai harus lebih besar dari jam mulai!');
                        }
                    });
                }
            }
        });
    </script>

    <!-- Modal Helper Scripts -->
    <?= modal_scripts() ?>

</body>

</html>
