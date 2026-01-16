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
        
        /* Desktop-specific navigation styles */
        .nav-dropdown:hover .nav-dropdown-menu {
            display: block;
        }
        
        /* Wider content area for desktop */
        .desktop-container {
            max-width: 1400px;
        }
    </style>
    <?= $this->renderSection('styles'); ?>
</head>

<body class="bg-gray-100">
    <!-- Desktop Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="desktop-container mx-auto px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-graduation-cap text-indigo-600 text-2xl mr-3"></i>
                        <span class="text-xl font-bold text-gray-800">SIMACCA</span>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="ml-10 flex space-x-4">
                        <?php if (is_logged_in()) : ?>
                            <?php $menu = get_sidebar_menu(); ?>
                            <?php foreach ($menu as $item): ?>
                                <?php if (isset($item['submenu'])) : ?>
                                    <!-- Dropdown Menu -->
                                    <div class="relative nav-dropdown group">
                                        <button class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium flex items-center">
                                            <?= $item['title']; ?>
                                            <i class="fas fa-chevron-down ml-2 text-xs"></i>
                                        </button>
                                        <div class="nav-dropdown-menu absolute hidden bg-white shadow-lg rounded-md mt-1 py-2 z-50 min-w-[200px]">
                                            <?php foreach ($item['submenu'] as $subitem): ?>
                                                <a href="<?= base_url($subitem['url']); ?>"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 whitespace-nowrap">
                                                    <?= $subitem['title']; ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <a href="<?= base_url($item['url']); ?>"
                                        class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium inline-flex items-center">
                                        <?= $item['title']; ?>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right side - User Menu -->
                <div class="flex items-center">
                    <?php if (is_logged_in()): ?>
                        <div class="relative ml-3">
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900"><?= session()->get('nama_lengkap'); ?></p>
                                    <p class="text-xs text-gray-500"><?= get_role_name(); ?></p>
                                </div>
                                <div class="relative">
                                    <button type="button" id="user-menu-button"
                                        class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <span class="sr-only">Open user menu</span>
                                        <?php if (session()->get('profile_photo')): ?>
                                            <img src="<?= base_url('profile-photo/' . esc(session()->get('profile_photo'))); ?>" 
                                                 alt="<?= esc(session()->get('nama_lengkap') ?? session()->get('username')); ?>"
                                                 class="h-10 w-10 rounded-full object-cover border-2 border-indigo-200">
                                        <?php else: ?>
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-indigo-600 font-semibold text-sm">
                                                    <?= strtoupper(substr(session()->get('nama_lengkap') ?? session()->get('username') ?? 'U', 0, 2)); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </button>

                                    <!-- Dropdown menu -->
                                    <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                        <a href="<?= base_url('profile'); ?>"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                                            <i class="fas fa-user-circle mr-2"></i> Profil
                                        </a>
                                        <a href="<?= base_url('change-password'); ?>"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                                            <i class="fas fa-key mr-2"></i> Ubah Password
                                        </a>
                                        <div class="border-t border-gray-100"></div>
                                        <a href="<?= base_url('logout'); ?>"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= base_url('login'); ?>"
                            class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium">
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-6">
        <div class="desktop-container mx-auto px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="ml-4 flex-shrink-0">
                        <?= $this->renderSection('actions'); ?>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?= render_alerts() ?>

            <!-- Content -->
            <?= $this->renderSection('content'); ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200">
        <div class="desktop-container mx-auto py-4 px-6 lg:px-8">
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

    <script>
        // User dropdown toggle
        const userMenuButton = document.getElementById('user-menu-button');
        if (userMenuButton) {
            userMenuButton.addEventListener('click', function() {
                const dropdown = document.getElementById('user-dropdown');
                if (dropdown) dropdown.classList.toggle('hidden');
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('user-dropdown');
            const button = document.getElementById('user-menu-button');
            if (!dropdown || !button) return;
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

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
