<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        /* Desktop-optimized UI helpers */
        .btn { display:inline-flex; align-items:center; gap:.5rem; padding:.5rem 1rem; border-radius:.5rem; font-weight:600; }
        .btn-primary { background:#3B82F6; color:#fff; }
        .btn-primary:hover { background:#2563EB; }
        .btn-secondary { background:#E5E7EB; color:#111827; }
        .btn-secondary:hover { background:#D1D5DB; }
        .btn-danger { background:#EF4444; color:#fff; }
        .btn-danger:hover { background:#DC2626; }
        .badge { display:inline-flex; align-items:center; padding:.125rem .5rem; font-size:.75rem; border-radius:9999px; }
        .badge-green { background:#D1FAE5; color:#065F46; }
        .badge-yellow { background:#FEF3C7; color:#92400E; }
        .badge-red { background:#FEE2E2; color:#991B1B; }
        .card { background:#fff; border-radius:.75rem; box-shadow:0 1px 2px rgba(0,0,0,0.05); }
        .card-header { padding:1rem 1.5rem; border-bottom:1px solid #E5E7EB; }
        .card-body { padding:1.5rem; }
        .chart-container { position:relative; height:300px; }
        .breadcrumb a { color:#6B7280; }
        .breadcrumb a:hover { color:#111827; }
        .flash { display:flex; align-items:flex-start; gap:.75rem; border-radius:.5rem; padding:.75rem 1rem; }
        .flash-success { background:#ECFDF5; color:#065F46; border:1px solid #A7F3D0; }
        .flash-error { background:#FEF2F2; color:#991B1B; border:1px solid #FECACA; }
        .flash-warn { background:#FFFBEB; color:#92400E; border:1px solid #FDE68A; }
        .flash .close { margin-left:auto; color:inherit; cursor:pointer; }
        
        /* Sidebar — clean, minimal design */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 0.625rem 0.75rem;
            margin-bottom: 2px;
            border-radius: 0.5rem;
            color: #374151;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s;
            cursor: pointer;
        }
        .sidebar-item:hover {
            background: #F3F4F6;
            color: #3B82F6;
        }
        .sidebar-item.active {
            background: #EFF6FF;
            color: #3B82F6;
            font-weight: 600;
        }
        .sidebar-item i:first-child {
            width: 1.25rem;
            font-size: 1rem;
            flex-shrink: 0;
            color: #9CA3AF;
        }
        .sidebar-item.active i:first-child,
        .sidebar-item:hover i:first-child {
            color: #3B82F6;
        }
        .sidebar-item span {
            margin-left: 0.75rem;
            flex: 1;
        }
        .sidebar-chevron {
            font-size: 0.75rem;
            color: #9CA3AF;
            transition: transform 0.2s;
            flex-shrink: 0;
        }
        .sidebar-chevron.open {
            transform: rotate(180deg);
        }
        /* Submenu: collapsible with vertical border line */
        .sidebar-sub {
            overflow: hidden;
            transition: max-height 0.25s ease;
        }
        .sidebar-sub.closed {
            max-height: 0 !important;
        }
        .sidebar-sub-inner {
            margin-left: 1.75rem;
            padding-left: 0.75rem;
            border-left: 2px solid #CBD5E1;
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
        }
        .sidebar-sub a {
            display: block;
            padding: 0.5rem 0.75rem;
            margin-bottom: 1px;
            border-radius: 0.25rem;
            color: #6B7280;
            font-size: 0.8125rem;
            transition: all 0.15s;
        }
        .sidebar-sub a:hover {
            color: #3B82F6;
        }
        .sidebar-sub a.active {
            color: #3B82F6;
            font-weight: 600;
        }

        /* Fluid container for desktop */
        .desktop-container {
            width: 100%;
        }
    </style>
    <?= $this->renderSection('styles'); ?>
</head>

<body class="bg-gray-100">
    <?php if (is_logged_in()): ?>
    <!-- Sidebar + Main Layout -->
    <div class="flex">
        <!-- Sidebar -->
        <aside class="sidebar bg-white border-r border-gray-200 fixed left-0 top-0 z-40">
            <!-- Header: Logo + App Name -->
            <div class="flex items-center gap-3 px-5 h-16 border-b border-gray-100 flex-shrink-0">
                <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-graduation-cap text-white text-sm"></i>
                </div>
                <span class="text-base font-bold text-gray-900 tracking-tight">SIMACCA</span>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto px-3 py-4">
                <?php $menu = get_sidebar_menu(); ?>
                <?php $currentUrl = uri_string(); ?>
                <?php foreach ($menu as $item): ?>
                    <?php if (isset($item['submenu'])): ?>
                        <?php
                        $isParentActive = false;
                        if (isset($item['active'])) {
                            foreach ($item['active'] as $a) {
                                if (strpos($currentUrl, $a) !== false) { $isParentActive = true; break; }
                            }
                        }
                        if (!$isParentActive) {
                            foreach ($item['submenu'] as $sub) {
                                if (strpos($currentUrl, ltrim($sub['url'], '/')) !== false) {
                                    $isParentActive = true;
                                    break;
                                }
                            }
                        }
                        ?>
                        <div class="sidebar-group mb-0.5" data-active="<?= $isParentActive ? '1' : '0' ?>">
                            <button class="sidebar-item w-full text-left <?= $isParentActive ? 'active' : '' ?> sidebar-dropdown-btn">
                                <i class="<?= $item['icon'] ?? 'fas fa-circle' ?>"></i>
                                <span><?= $item['title'] ?></span>
                                <i class="fas fa-chevron-down sidebar-chevron <?= $isParentActive ? 'open' : '' ?>"></i>
                            </button>
                            <div class="sidebar-sub <?= $isParentActive ? '' : 'closed' ?>" style="max-height: <?= $isParentActive ? '500px' : '0' ?>">
                                <div class="sidebar-sub-inner">
                                    <?php foreach ($item['submenu'] as $sub): ?>
                                        <?php
                                        $subUrl = ltrim($sub['url'], '/');
                                        $isSubActive = $currentUrl === $subUrl;
                                        ?>
                                        <a href="<?= base_url($sub['url']); ?>" class="<?= $isSubActive ? 'active' : '' ?>">
                                            <?= $sub['title']; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php
                        $url = ltrim($item['url'], '/');
                        $isActive = false;
                        if (isset($item['active'])) {
                            foreach ($item['active'] as $a) {
                                if (strpos($currentUrl, $a) !== false) { $isActive = true; break; }
                            }
                        } else {
                            $isActive = strpos($currentUrl, $url) !== false;
                        }
                        ?>
                        <a href="<?= base_url($item['url']); ?>" class="sidebar-item <?= $isActive ? 'active' : '' ?>">
                            <i class="<?= $item['icon'] ?? 'fas fa-circle' ?>"></i>
                            <span><?= $item['title']; ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>

            <!-- Footer: User info + account toggle -->
            <div class="flex-shrink-0 border-t border-gray-100 px-3 py-3">
                <div class="relative">
                    <button type="button" id="sidebar-user-btn"
                        class="flex items-center w-full px-2 py-2 rounded-lg hover:bg-gray-50 transition-colors text-left">
                        <?php if (session()->get('profile_photo')): ?>
                            <img src="<?= base_url('profile-photo/' . esc(session()->get('profile_photo'))); ?>" 
                                 alt="<?= esc(session()->get('nama_lengkap') ?? session()->get('username')); ?>"
                                 class="h-8 w-8 rounded-full object-cover flex-shrink-0">
                        <?php else: ?>
                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center flex-shrink-0">
                                <span class="text-white font-semibold text-xs">
                                    <?= strtoupper(substr(session()->get('nama_lengkap') ?? session()->get('username') ?? 'U', 0, 2)); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate leading-tight"><?= session()->get('nama_lengkap'); ?></p>
                            <p class="text-xs text-gray-400 truncate leading-tight"><?= get_role_name(); ?></p>
                        </div>
                        <i class="fas fa-chevron-up text-xs text-gray-400 flex-shrink-0 sidebar-user-chevron"></i>
                    </button>
                    <!-- Dropdown -->
                    <div id="sidebar-user-dropdown" class="hidden absolute bottom-full left-0 right-0 mb-1 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                        <a href="<?= base_url('profile'); ?>"
                            class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-user-circle mr-2 text-gray-400 w-4"></i> Profil
                        </a>
                        <a href="<?= base_url('change-password'); ?>"
                            class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-key mr-2 text-gray-400 w-4"></i> Ubah Password
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="<?= base_url('logout'); ?>"
                            class="flex items-center px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt mr-2 text-red-400 w-4"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="ml-[260px] flex-1 min-h-screen">
            <!-- Top Bar -->
            <div class="sticky top-0 bg-white border-b border-gray-200 z-30">
                <div class="desktop-container mx-auto px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900"><?= $title ?? 'Dashboard'; ?></h1>
                            <p class="text-xs text-gray-500"><?= get_role_name(); ?></p>
                        </div>
                        <div class="flex items-center gap-4">
                            <?= $this->renderSection('actions'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="desktop-container mx-auto px-6 lg:px-8 py-6">
                <!-- Flash Messages -->
                <?= render_alerts() ?>

                <!-- Content -->
                <?= $this->renderSection('content'); ?>
            </div>

            <!-- Footer -->
            <footer class="border-t border-gray-200 bg-white">
                <div class="desktop-container mx-auto px-6 lg:px-8 py-4">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            <p>&copy; <?= date('Y'); ?> SIMACCA. All rights reserved.</p>
                        </div>
                        <div class="text-sm text-gray-500">
                            <p>v1.0.0 - <?= get_role_name(); ?> (Desktop View)</p>
                        </div>
                    </div>
                </div>
            </footer>
        </main>
    </div>
    <?php else: ?>
    <!-- Not logged in - simple layout -->
    <main class="py-6">
        <div class="desktop-container mx-auto px-6 lg:px-8">
            <?= $this->renderSection('content'); ?>
        </div>
    </main>
    <?php endif; ?>

    <script>
        // Sidebar dropdown toggle
        document.querySelectorAll('.sidebar-dropdown-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const group = this.closest('.sidebar-group');
                const sub = group.querySelector('.sidebar-sub');
                const chevron = this.querySelector('.sidebar-chevron');
                const isOpen = !sub.classList.contains('closed');
                
                if (isOpen) {
                    sub.classList.add('closed');
                    sub.style.maxHeight = '0';
                    if (chevron) chevron.classList.remove('open');
                } else {
                    sub.classList.remove('closed');
                    sub.style.maxHeight = sub.scrollHeight + 'px';
                    if (chevron) chevron.classList.add('open');
                }
            });
        });

        // User dropdown toggle (footer)
        const userBtn = document.getElementById('sidebar-user-btn');
        const userDropdown = document.getElementById('sidebar-user-dropdown');
        const userChevron = document.querySelector('.sidebar-user-chevron');
        if (userBtn && userDropdown) {
            userBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = !userDropdown.classList.contains('hidden');
                userDropdown.classList.toggle('hidden');
                if (userChevron) {
                    userChevron.classList.toggle('fa-chevron-up', isOpen);
                    userChevron.classList.toggle('fa-chevron-down', !isOpen);
                }
            });
            document.addEventListener('click', function(event) {
                if (!userBtn.contains(event.target) && !userDropdown.contains(event.target)) {
                    userDropdown.classList.add('hidden');
                    if (userChevron) {
                        userChevron.classList.add('fa-chevron-up');
                        userChevron.classList.remove('fa-chevron-down');
                    }
                }
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
    </script>

    <?= $this->renderSection('scripts'); ?>

    <!-- flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize flatpickr for time pickers
            flatpickr('.timepicker', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 1,
                allowInput: true
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
