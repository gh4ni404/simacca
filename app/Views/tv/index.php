<!DOCTYPE html>
<html lang="id" class="dark h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'SIMACCA TV Live Showcase'); ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Tailwind Setup -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        tvDark: '#070B14',
                        tvSurface: '#0D1424',
                        tvCard: 'rgba(15, 23, 42, 0.75)',
                        tvBorder: 'rgba(255, 255, 255, 0.08)',
                        brandBlue: '#2563EB',
                        brandEmerald: '#059669',
                        brandAmber: '#D97706',
                        brandPurple: '#7C3AED',
                        brandCyan: '#0891B2',
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'marquee': 'marquee 35s linear infinite',
                    },
                    keyframes: {
                        marquee: {
                            '0%': { transform: 'translateX(0%)' },
                            '100%': { transform: 'translateX(-50%)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Base Reset & Custom TV Styles */
        * {
            user-select: none;
            -webkit-user-select: none;
            box-sizing: border-box;
        }

        body {
            background-color: #060911;
            color: #F8FAFC;
            overflow: hidden;
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
        }

        /* Glassmorphism Classes */
        .glass-panel {
            background: rgba(13, 20, 36, 0.72);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }

        .glass-card {
            background: rgba(18, 28, 51, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.07);
        }

        .glass-badge {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* Ken Burns Zoom & Pan Keyframe */
        @keyframes kenBurns1 {
            0% {
                transform: scale(1.0) translate(0, 0);
            }
            50% {
                transform: scale(1.06) translate(-1%, -1%);
            }
            100% {
                transform: scale(1.1) translate(1%, -1.5%);
            }
        }

        @keyframes kenBurns2 {
            0% {
                transform: scale(1.08) translate(1%, 1%);
            }
            50% {
                transform: scale(1.02) translate(-0.5%, 0.5%);
            }
            100% {
                transform: scale(1.09) translate(-1%, -1%);
            }
        }

        .ken-burns-active-1 {
            animation: kenBurns1 10s ease-in-out infinite alternate;
        }

        .ken-burns-active-2 {
            animation: kenBurns2 10s ease-in-out infinite alternate;
        }

        /* Ambient Glow & Shadow Effects */
        .glow-blue {
            box-shadow: 0 0 35px rgba(59, 130, 246, 0.25);
        }

        .glow-emerald {
            box-shadow: 0 0 35px rgba(16, 185, 129, 0.25);
        }

        .glow-amber {
            box-shadow: 0 0 35px rgba(245, 158, 11, 0.25);
        }

        .glow-purple {
            box-shadow: 0 0 35px rgba(139, 92, 246, 0.25);
        }

        /* Custom Scrollbar for sidebar queue */
        ::-webkit-scrollbar {
            width: 4px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 9999px;
        }

        /* Dynamic Progress Bar */
        .progress-bar-fill {
            transition: width 0.1s linear;
        }

        /* Hide Controls after inactivity */
        .tv-controls {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        body:hover .tv-controls {
            opacity: 1;
        }

        /* Line clamp utilities */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body class="h-screen w-screen flex flex-col justify-between select-none relative font-sans antialiased text-slate-100 bg-[#060911]">

    <!-- Background Atmospheric Lighting Grid -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden opacity-30">
        <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-blue-600/20 rounded-full blur-[140px]"></div>
        <div class="absolute top-1/2 -right-40 w-[550px] h-[550px] bg-emerald-600/15 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-40 left-1/3 w-[500px] h-[500px] bg-purple-600/15 rounded-full blur-[140px]"></div>
    </div>

    <!-- ========================================== -->
    <!-- 1. TOP BAR (HEADER)                       -->
    <!-- ========================================== -->
    <header class="relative z-20 h-20 px-6 sm:px-8 flex items-center justify-between glass-panel border-b border-white/10 shrink-0">
        <!-- Left: Brand & School Logo -->
        <div class="flex items-center gap-4">
            <?php if (!empty($logoSekolah)): ?>
                <img src="<?= base_url('files/logo/' . $logoSekolah) ?>" alt="Logo" class="w-12 h-12 object-contain drop-shadow-md rounded-lg">
            <?php else: ?>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            <?php endif; ?>

            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl font-black tracking-tight text-white uppercase font-sans">
                        <?= esc($namaSekolah); ?>
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500/20 text-rose-400 border border-rose-500/30">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        LIVE SHOW
                    </span>
                </div>
                <p class="text-xs text-slate-400 font-medium">
                    <?= esc($alamatSekolah); ?> • <span class="text-cyan-400 font-semibold"><i class="fa-solid fa-shuffle text-[10px] mr-1"></i>Random Showcase (Hari Ini s/d 7 Hari Terakhir)</span>
                </p>
            </div>
        </div>

        <!-- Right: Realtime Digital Clock & Controls -->
        <div class="flex items-center gap-6">
            <!-- Digital Clock Display -->
            <div class="text-right">
                <div id="liveClock" class="text-2xl sm:text-3xl font-bold font-mono text-cyan-400 tracking-wider drop-shadow-[0_0_12px_rgba(6,182,212,0.4)]">
                    --:--:-- WIB
                </div>
                <div id="liveDate" class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    Memuat Tanggal...
                </div>
            </div>

            <!-- TV Float Controls (On Hover / Idle Auto-Hide) -->
            <div class="tv-controls flex items-center gap-2 pl-4 border-l border-white/10">
                <button id="btnPrev" title="Foto Sebelumnya" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-white flex items-center justify-center transition border border-white/10">
                    <i class="fa-solid fa-chevron-left text-sm"></i>
                </button>
                <button id="btnPlayPause" title="Jeda / Putar" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-white flex items-center justify-center transition border border-white/10">
                    <i id="iconPlayPause" class="fa-solid fa-pause text-sm"></i>
                </button>
                <button id="btnNext" title="Foto Berikutnya" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-white flex items-center justify-center transition border border-white/10">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </button>
                <button id="btnFullscreen" title="Layar Penuh (F11)" class="w-10 h-10 rounded-xl bg-blue-600/30 hover:bg-blue-600/50 active:scale-95 text-cyan-300 flex items-center justify-center transition border border-blue-500/30">
                    <i class="fa-solid fa-expand text-sm"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- ========================================== -->
    <!-- 2. MAIN WORKSPACE (SPLIT 68% : 32%)       -->
    <!-- ========================================== -->
    <main class="relative z-10 flex-1 px-6 py-4 grid grid-cols-12 gap-6 overflow-hidden items-stretch">

        <!-- ====================================== -->
        <!-- LEFT: HERO SHOWCASE STAGE (COL-SPAN-8) -->
        <!-- ====================================== -->
        <section class="col-span-12 lg:col-span-8 flex flex-col justify-between relative rounded-2xl overflow-hidden glass-panel border border-white/10 group shadow-2xl">

            <!-- Ambient Backdrop Mirror (Blurred) -->
            <div class="absolute inset-0 z-0 overflow-hidden">
                <img id="heroBackdropImg" src="" alt="Backdrop" class="w-full h-full object-cover filter blur-2xl brightness-[0.35] scale-125 transition-all duration-1000">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
            </div>

            <!-- Foreground Hero Media Stage -->
            <div class="relative z-10 w-full flex-1 flex items-center justify-center overflow-hidden p-3 sm:p-5">
                <div class="relative w-full h-full rounded-xl overflow-hidden shadow-2xl flex items-center justify-center bg-black/40 border border-white/5">
                    <img id="heroMainImg" src="" alt="Dokumentasi SIMACCA" class="w-full h-full object-contain transition-all duration-700">

                    <!-- Empty State Placeholder (when no images exist) -->
                    <div id="heroEmptyState" class="hidden absolute inset-0 flex flex-col items-center justify-center text-center p-8 bg-slate-900/90 backdrop-blur-md">
                        <div class="w-24 h-24 rounded-full bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-blue-400 text-4xl mb-4 animate-bounce">
                            <i class="fa-solid fa-images"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2">Belum Ada Foto Dokumentasi 7 Hari Terakhir</h3>
                        <p class="text-sm text-slate-400 max-w-md">Foto kegiatan KBM, laporan PKL siswa, piket guru, dan bimbingan wali akan otomatis tayang di sini saat diunggah ke SIMACCA.</p>
                    </div>
                </div>
            </div>

            <!-- Floating Glassmorphism Meta Card -->
            <div class="relative z-20 mx-4 mb-3 sm:mx-6 sm:mb-4 p-5 sm:p-6 rounded-2xl glass-card border border-white/15 shadow-2xl transition-all duration-500">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    
                    <!-- Left Details -->
                    <div class="flex-1 min-w-0">
                        <!-- Category Badge & Relative Time -->
                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                            <span id="heroBadge" class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider glass-badge border shadow-sm">
                                <i id="heroBadgeIcon" class="fa-solid fa-chalkboard-user"></i>
                                <span id="heroBadgeLabel">KBM & Praktik</span>
                            </span>
                            
                            <span class="flex items-center gap-1.5 text-xs font-semibold text-slate-400">
                                <i class="fa-regular fa-clock text-cyan-400"></i>
                                <span id="heroTimeAgo">Baru saja</span>
                            </span>

                            <span class="flex items-center gap-1.5 text-xs font-semibold text-slate-400">
                                <i class="fa-solid fa-location-dot text-rose-400"></i>
                                <span id="heroLocation">Area Sekolah</span>
                            </span>
                        </div>

                        <!-- Main Activity Title -->
                        <h2 id="heroTitle" class="text-xl sm:text-2xl md:text-3xl font-extrabold text-white tracking-tight leading-snug line-clamp-2 drop-shadow-sm">
                            Memuat Dokumentasi Aktivitas...
                        </h2>

                        <!-- Subtitle (Mapel / DUDI) -->
                        <p id="heroSubtitle" class="text-sm sm:text-base font-semibold text-cyan-300 mt-1 flex items-center gap-2">
                            <i class="fa-solid fa-tag text-xs opacity-75"></i>
                            <span id="heroSubtitleText">SMK Unggulan</span>
                        </p>

                        <!-- Description Snippet -->
                        <p id="heroDesc" class="text-xs sm:text-sm text-slate-300 mt-2 line-clamp-2 leading-relaxed">
                            Deskripsi ringkasan aktivitas dokumentasi siswa dan guru.
                        </p>
                    </div>

                    <!-- Right Uploader Chip (Author info) -->
                    <div class="flex items-center gap-3 shrink-0 p-3 rounded-xl bg-white/5 border border-white/10 md:min-w-[220px]">
                        <div class="relative">
                            <img id="heroUploaderAvatar" src="<?= base_url('assets/img/default-avatar.png') ?>" alt="Uploader" class="w-12 h-12 rounded-full object-cover border-2 border-cyan-400/50 shadow-md">
                            <span id="heroUploaderRoleIcon" class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] flex items-center justify-center border border-white shadow">
                                <i class="fa-solid fa-user-tie"></i>
                            </span>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-cyan-400" id="heroUploaderRole">
                                Guru Pengampu
                            </div>
                            <div class="text-sm font-bold text-white truncate" id="heroUploaderName">
                                Pengunggah Foto
                            </div>
                            <div class="text-xs text-slate-400 truncate" id="heroFormattedDate">
                                04 September 2026
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide Animated Progress Bar -->
                <div class="w-full bg-slate-800/80 rounded-full h-1.5 mt-4 overflow-hidden border border-white/5">
                    <div id="slideProgressBar" class="h-full bg-gradient-to-r from-blue-500 via-cyan-400 to-emerald-400 progress-bar-fill shadow-[0_0_12px_rgba(6,182,212,0.8)]" style="width: 0%;"></div>
                </div>
            </div>
        </section>

        <!-- ====================================== -->
        <!-- RIGHT: COMMAND CENTER SIDEBAR (COL-4) -->
        <!-- ====================================== -->
        <aside class="col-span-12 lg:col-span-4 flex flex-col gap-4 overflow-hidden">

            <!-- 1. Top Mini Stats Widgets -->
            <div class="grid grid-cols-3 gap-3 shrink-0">
                <!-- Stat 1: Kehadiran Hari Ini -->
                <div class="p-3.5 rounded-2xl glass-card flex flex-col justify-between border-t-2 border-t-blue-500 shadow-lg">
                    <div class="flex items-center justify-between text-blue-400 text-xs font-bold uppercase">
                        <span>Presensi</span>
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div class="mt-2">
                        <div id="statAttendance" class="text-2xl font-black text-white font-mono">
                            <?= esc($stats['attendance_rate'] ?? 98); ?>%
                        </div>
                        <div class="text-[10px] text-slate-400 truncate">Hadir Hari Ini</div>
                    </div>
                </div>

                <!-- Stat 2: Siswa PKL Aktif -->
                <div class="p-3.5 rounded-2xl glass-card flex flex-col justify-between border-t-2 border-t-emerald-500 shadow-lg">
                    <div class="flex items-center justify-between text-emerald-400 text-xs font-bold uppercase">
                        <span>PKL</span>
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div class="mt-2">
                        <div id="statPkl" class="text-2xl font-black text-white font-mono">
                            <?= esc($stats['active_pkl'] ?? 0); ?>
                        </div>
                        <div class="text-[10px] text-slate-400 truncate">Siswa di Industri</div>
                    </div>
                </div>

                <!-- Stat 3: Total Foto 7 Hari -->
                <div class="p-3.5 rounded-2xl glass-card flex flex-col justify-between border-t-2 border-t-purple-500 shadow-lg">
                    <div class="flex items-center justify-between text-purple-400 text-xs font-bold uppercase">
                        <span>Dokumentasi</span>
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <div class="mt-2">
                        <div id="statDocs" class="text-2xl font-black text-white font-mono">
                            <?= esc($stats['total_docs_7days'] ?? 0); ?>
                        </div>
                        <div class="text-[10px] text-slate-400 truncate">Foto (7 Hari)</div>
                    </div>
                </div>
            </div>

            <!-- 2. Upcoming Slides / Up Next Feed Queue -->
            <div class="flex-1 rounded-2xl glass-panel p-4 flex flex-col overflow-hidden border border-white/10 shadow-xl min-h-0">
                <!-- Queue Header -->
                <div class="flex items-center justify-between pb-3 border-b border-white/10 shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Antrean Tayang Feed</h3>
                    </div>
                    <span id="queueCounter" class="text-xs font-mono font-bold text-cyan-400 px-2 py-0.5 rounded bg-cyan-950/60 border border-cyan-500/30">
                        0 / 0
                    </span>
                </div>

                <!-- Scrollable Item Cards Container -->
                <div id="queueListContainer" class="flex-1 overflow-y-auto mt-3 space-y-2.5 pr-1">
                    <!-- Dynamic cards injected via JavaScript -->
                </div>
            </div>

            <!-- 3. Bottom SIMACCA Portal QR & Motto -->
            <div class="p-4 rounded-2xl glass-card flex items-center justify-between gap-4 border border-white/10 shrink-0 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white p-1.5 flex items-center justify-center shrink-0 shadow">
                        <!-- Embedded QR code vector pointing to app root -->
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode(base_url()) ?>&color=0b1120" alt="SIMACCA QR" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white">SIMACCA Online</div>
                        <div class="text-[11px] text-slate-400">Scan untuk presensi & upload jurnal</div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-400 bg-emerald-950/60 px-2 py-1 rounded-lg border border-emerald-500/30">
                        <i class="fa-solid fa-wifi text-[9px]"></i> Online Sync
                    </span>
                </div>
            </div>

        </aside>

    </main>

    <!-- ========================================== -->
    <!-- 3. BOTTOM TICKER BAR (RUNNING TEXT)       -->
    <!-- ========================================== -->
    <footer class="relative z-20 h-12 bg-slate-950/90 backdrop-blur-lg border-t border-white/10 flex items-center overflow-hidden shrink-0">
        <!-- Label Badge -->
        <div class="h-full px-5 bg-gradient-to-r from-blue-700 to-cyan-600 flex items-center gap-2 text-white font-bold text-xs uppercase tracking-wider shrink-0 z-10 shadow-lg">
            <i class="fa-solid fa-bullhorn text-xs"></i>
            <span>INFO SEKOLAH</span>
        </div>

        <!-- Marquee Infinite Track -->
        <div class="flex-1 overflow-hidden relative flex items-center">
            <div id="tickerTrack" class="inline-flex whitespace-nowrap animate-marquee items-center text-sm font-medium text-slate-200">
                <!-- Ticker items injected via JS or PHP fallback -->
                <?php foreach ($tickers as $ticker): ?>
                    <span class="inline-flex items-center gap-3 mx-6">
                        <span class="text-cyan-400">•</span>
                        <span><?= esc($ticker) ?></span>
                    </span>
                <?php endforeach; ?>
                <!-- Duplicate for seamless infinite loop -->
                <?php foreach ($tickers as $ticker): ?>
                    <span class="inline-flex items-center gap-3 mx-6">
                        <span class="text-cyan-400">•</span>
                        <span><?= esc($ticker) ?></span>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Timezone Indicator -->
        <div class="px-4 text-xs font-mono font-bold text-slate-400 border-l border-white/10 shrink-0">
            WIB (UTC+7)
        </div>
    </footer>

    <!-- ========================================== -->
    <!-- JAVASCRIPT ENGINE FOR AUTO PLAY & SYNC     -->
    <!-- ========================================== -->
    <script>
        (function() {
            // Configuration Tokens
            const SLIDE_DURATION_MS = 9000; // 9 seconds per slide (approved default)
            const SYNC_INTERVAL_MS  = 45000; // Sync new photos every 45 seconds
            const DEFAULT_AVATAR    = 'https://ui-avatars.com/api/?background=1e293b&color=38bdf8&name=';

            // Global State
            let feedItems = [];
            let currentIndex = 0;
            let slideTimer = null;
            let progressTimer = null;
            let isPaused = false;
            let slideStartTime = Date.now();
            let preloadedImages = new Map();

            // Bootstrapped Data from PHP
            try {
                const initialPayload = <?= $initialFeed ?: '{"items":[],"stats":{},"tickers":[]}' ?>;
                feedItems = initialPayload.items || [];
            } catch (e) {
                console.error('Error parsing initial payload:', e);
                feedItems = [];
            }

            // DOM Element References
            const liveClockEl        = document.getElementById('liveClock');
            const liveDateEl         = document.getElementById('liveDate');
            const heroMainImg        = document.getElementById('heroMainImg');
            const heroBackdropImg    = document.getElementById('heroBackdropImg');
            const heroEmptyState     = document.getElementById('heroEmptyState');
            const heroBadge          = document.getElementById('heroBadge');
            const heroBadgeIcon      = document.getElementById('heroBadgeIcon');
            const heroBadgeLabel     = document.getElementById('heroBadgeLabel');
            const heroTimeAgo        = document.getElementById('heroTimeAgo');
            const heroLocation       = document.getElementById('heroLocation');
            const heroTitle          = document.getElementById('heroTitle');
            const heroSubtitleText   = document.getElementById('heroSubtitleText');
            const heroDesc           = document.getElementById('heroDesc');
            const heroUploaderAvatar = document.getElementById('heroUploaderAvatar');
            const heroUploaderRole   = document.getElementById('heroUploaderRole');
            const heroUploaderName   = document.getElementById('heroUploaderName');
            const heroFormattedDate  = document.getElementById('heroFormattedDate');
            const slideProgressBar   = document.getElementById('slideProgressBar');
            const queueListContainer = document.getElementById('queueListContainer');
            const queueCounter       = document.getElementById('queueCounter');
            const statAttendance     = document.getElementById('statAttendance');
            const statPkl            = document.getElementById('statPkl');
            const statDocs           = document.getElementById('statDocs');
            const btnPlayPause       = document.getElementById('btnPlayPause');
            const iconPlayPause      = document.getElementById('iconPlayPause');
            const btnPrev            = document.getElementById('btnPrev');
            const btnNext            = document.getElementById('btnNext');
            const btnFullscreen      = document.getElementById('btnFullscreen');
            const tickerTrack        = document.getElementById('tickerTrack');

            // 1. Realtime Digital Clock Engine
            function updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                liveClockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;

                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const dayName = days[now.getDay()];
                const dayNum = now.getDate();
                const monthName = months[now.getMonth()];
                const year = now.getFullYear();

                liveDateEl.textContent = `${dayName}, ${dayNum} ${monthName} ${year}`;
            }
            setInterval(updateClock, 1000);
            updateClock();

            // 2. Image Preloader Engine (Eliminates White Blink)
            function preloadImage(url) {
                if (!url || preloadedImages.has(url)) return;
                const img = new Image();
                img.src = url;
                img.onload = () => preloadedImages.set(url, true);
            }

            function preloadNextImages() {
                if (feedItems.length <= 1) return;
                const nextIdx1 = (currentIndex + 1) % feedItems.length;
                const nextIdx2 = (currentIndex + 2) % feedItems.length;
                preloadImage(feedItems[nextIdx1]?.photo_url);
                preloadImage(feedItems[nextIdx2]?.photo_url);
            }

            // 3. Render Current Slide
            function renderSlide(index, withAnimation = true) {
                if (!feedItems || feedItems.length === 0) {
                    heroEmptyState.classList.remove('hidden');
                    heroMainImg.classList.add('hidden');
                    heroBackdropImg.style.display = 'none';
                    queueCounter.textContent = '0 / 0';
                    return;
                }

                heroEmptyState.classList.add('hidden');
                heroMainImg.classList.remove('hidden');
                heroBackdropImg.style.display = 'block';

                currentIndex = (index + feedItems.length) % feedItems.length;
                const item = feedItems[currentIndex];

                // Update Queue Counter
                queueCounter.textContent = `${currentIndex + 1} / ${feedItems.length}`;

                // Handle missing image gracefully
                const heroFallbackSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="800" height="450" viewBox="0 0 800 450"><rect width="800" height="450" fill="%230b1120"/><rect x="20" y="20" width="760" height="410" rx="16" fill="%230f172a" stroke="%231e293b" stroke-width="2"/><circle cx="400" cy="200" r="48" fill="%231e293b"/><path d="M380 185 L420 185 L420 225 L380 225 Z" fill="none" stroke="%2338bdf8" stroke-width="3"/><circle cx="400" cy="205" r="8" fill="%2338bdf8"/><text x="400" y="280" text-anchor="middle" fill="%2394a3b8" font-family="sans-serif" font-size="16" font-weight="600">${encodeURIComponent(item.category_label || 'DOKUMENTASI')}</text></svg>`;

                heroMainImg.onerror = function() {
                    this.onerror = null;
                    this.src = heroFallbackSvg;
                    heroBackdropImg.style.opacity = '0';
                };
                heroBackdropImg.onerror = function() {
                    this.onerror = null;
                    this.src = '';
                };

                // Smooth Animation Class Switch (Ken Burns alternate)
                if (withAnimation) {
                    heroMainImg.style.opacity = '0';
                    setTimeout(() => {
                        heroBackdropImg.style.opacity = '1';
                        heroMainImg.src = item.photo_url;
                        heroBackdropImg.src = item.photo_url;
                        heroMainImg.style.opacity = '1';
                        
                        // Toggle Ken Burns alternating classes
                        if (currentIndex % 2 === 0) {
                            heroMainImg.className = 'w-full h-full object-contain ken-burns-active-1 transition-all duration-700';
                        } else {
                            heroMainImg.className = 'w-full h-full object-contain ken-burns-active-2 transition-all duration-700';
                        }
                    }, 250);
                } else {
                    heroBackdropImg.style.opacity = '1';
                    heroMainImg.src = item.photo_url;
                    heroBackdropImg.src = item.photo_url;
                }

                // Update Category Badge
                heroBadge.style.backgroundColor = item.badge_bg || 'rgba(59, 130, 246, 0.2)';
                heroBadge.style.borderColor = item.badge_border || 'rgba(59, 130, 246, 0.5)';
                heroBadge.style.color = item.badge_color || '#3B82F6';
                heroBadgeLabel.textContent = item.category_label || 'Dokumentasi';
                heroBadgeIcon.className = `fa-solid ${item.badge_icon || 'fa-camera'}`;

                // Meta text updates
                heroTimeAgo.textContent = item.time_ago || 'Hari ini';
                heroLocation.textContent = item.location || 'SIMACCA';
                heroTitle.textContent = item.title || 'Dokumentasi Aktivitas';
                heroSubtitleText.textContent = item.subtitle || 'SMK SIMACCA';
                heroDesc.textContent = item.description || 'Aktivitas pembelajaran dan praktik kejuruan.';

                // Uploader Info
                heroUploaderName.textContent = item.uploader_name || 'Pengguna SIMACCA';
                heroUploaderRole.textContent = item.uploader_role || 'Civitas Sekolah';
                heroFormattedDate.textContent = item.formatted_date || '';

                if (item.uploader_photo) {
                    heroUploaderAvatar.src = item.uploader_photo;
                } else {
                    heroUploaderAvatar.src = DEFAULT_AVATAR + encodeURIComponent(item.uploader_name || 'SIMACCA');
                }

                // Render Sidebar Queue Cards
                renderQueueList();

                // Preload ahead
                preloadNextImages();

                // Reset Progress Bar
                resetProgressBar();
            }

            // 4. Render Sidebar Up Next Cards
            function renderQueueList() {
                if (!queueListContainer) return;

                queueListContainer.innerHTML = feedItems.map((item, idx) => {
                    const isCurrent = idx === currentIndex;
                    const cardClass = isCurrent 
                        ? 'bg-blue-600/25 border-cyan-400/50 shadow-lg scale-[1.02]' 
                        : 'bg-white/5 hover:bg-white/10 border-white/5';
                    const activePill = isCurrent 
                        ? '<span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse shrink-0"></span>' 
                        : '';

                    return `
                        <div data-index="${idx}" class="queue-item p-2.5 rounded-xl transition-all duration-300 flex items-center gap-3 cursor-pointer border ${cardClass}">
                            <div class="relative w-14 h-14 rounded-lg overflow-hidden shrink-0 bg-slate-900 border border-white/10 flex items-center justify-center">
                                <img src="${item.photo_url}" alt="" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                <i class="fa-solid ${item.badge_icon || 'fa-image'} text-slate-500 text-lg hidden"></i>
                                <span class="absolute top-0 right-0 px-1 py-0.5 text-[8px] font-bold uppercase rounded-bl shadow text-white" style="background-color: ${item.badge_color || '#3B82F6'};">
                                    ${item.category || 'INFO'}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    ${activePill}
                                    <span class="text-[11px] font-bold text-slate-400 truncate">${item.category_label || ''}</span>
                                </div>
                                <div class="text-xs font-bold text-white truncate mt-0.5">${item.title || ''}</div>
                                <div class="text-[10px] text-cyan-300 truncate">${item.uploader_name || ''} • ${item.time_ago || ''}</div>
                            </div>
                        </div>
                    `;
                }).join('');

                // Auto scroll active item into view smoothly
                const activeCard = queueListContainer.children[currentIndex];
                if (activeCard) {
                    activeCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }

            // Click delegation on sidebar queue cards
            queueListContainer?.addEventListener('click', (e) => {
                const card = e.target.closest('.queue-item');
                if (card && card.dataset.index !== undefined) {
                    const idx = parseInt(card.dataset.index, 10);
                    renderSlide(idx);
                    startSlideTimer();
                }
            });

            // 5. Progress Bar & Slide Timer Engine
            function resetProgressBar() {
                slideProgressBar.style.width = '0%';
                slideStartTime = Date.now();
            }

            function updateProgress() {
                if (isPaused || feedItems.length <= 1) return;
                const elapsed = Date.now() - slideStartTime;
                const percent = Math.min(100, (elapsed / SLIDE_DURATION_MS) * 100);
                slideProgressBar.style.width = percent + '%';

                if (elapsed >= SLIDE_DURATION_MS) {
                    nextSlide();
                }
            }

            function startSlideTimer() {
                clearInterval(progressTimer);
                slideStartTime = Date.now();
                progressTimer = setInterval(updateProgress, 50);
            }

            function nextSlide() {
                renderSlide(currentIndex + 1);
                startSlideTimer();
            }

            function prevSlide() {
                renderSlide(currentIndex - 1);
                startSlideTimer();
            }

            function togglePlayPause() {
                isPaused = !isPaused;
                if (isPaused) {
                    iconPlayPause.className = 'fa-solid fa-play text-sm';
                    btnPlayPause.classList.add('bg-emerald-600/40', 'border-emerald-500/50');
                } else {
                    iconPlayPause.className = 'fa-solid fa-pause text-sm';
                    btnPlayPause.classList.remove('bg-emerald-600/40', 'border-emerald-500/50');
                    slideStartTime = Date.now();
                }
            }

            // 6. Silent Background Data Synchronization (No Flicker / Zero Reload)
            async function syncBackgroundFeed() {
                try {
                    const response = await fetch('<?= base_url('tv/feed') ?>', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!response.ok) return;

                    const data = await response.json();
                    if (data && data.status === 'success' && Array.isArray(data.items)) {
                        // Check if new items exist
                        const currentFirstId = feedItems[0]?.id;
                        const newFirstId = data.items[0]?.id;

                        feedItems = data.items;

                        // Update Stats
                        if (data.stats) {
                            if (statAttendance && data.stats.attendance_rate !== undefined) {
                                statAttendance.textContent = `${data.stats.attendance_rate}%`;
                            }
                            if (statPkl && data.stats.active_pkl !== undefined) {
                                statPkl.textContent = data.stats.active_pkl;
                            }
                            if (statDocs && data.stats.total_docs_7days !== undefined) {
                                statDocs.textContent = data.stats.total_docs_7days;
                            }
                        }

                        // Update Tickers if provided
                        if (data.tickers && Array.isArray(data.tickers) && data.tickers.length > 0) {
                            const tickerHtml = data.tickers.map(t => `
                                <span class="inline-flex items-center gap-3 mx-6">
                                    <span class="text-cyan-400">•</span>
                                    <span>${t}</span>
                                </span>
                            `).join('');
                            tickerTrack.innerHTML = tickerHtml + tickerHtml; // double for loop
                        }

                        // If brand new photo arrived at top, jump to it smoothly
                        if (newFirstId && newFirstId !== currentFirstId) {
                            renderSlide(0);
                        } else {
                            renderQueueList();
                        }
                    }
                } catch (err) {
                    console.warn('Background sync failed silently (offline fallback mode active):', err);
                }
            }

            // 7. Screen Wake Lock (Keeps Smart TV Screen Awake)
            let wakeLock = null;
            async function requestWakeLock() {
                try {
                    if ('wakeLock' in navigator) {
                        wakeLock = await navigator.wakeLock.request('screen');
                        wakeLock.addEventListener('release', () => {
                            console.log('Screen Wake Lock was released');
                        });
                    }
                } catch (err) {
                    console.warn(`Wake lock error: ${err.name}, ${err.message}`);
                }
            }
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    requestWakeLock();
                }
            });
            requestWakeLock();

            // 8. Fullscreen Toggle
            function toggleFullscreen() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch((err) => {
                        console.warn(`Error attempting fullscreen: ${err.message}`);
                    });
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                }
            }

            // 9. Event Listeners & Keyboard Shortcuts
            btnPlayPause?.addEventListener('click', togglePlayPause);
            btnNext?.addEventListener('click', nextSlide);
            btnPrev?.addEventListener('click', prevSlide);
            btnFullscreen?.addEventListener('click', toggleFullscreen);

            document.addEventListener('keydown', (e) => {
                if (e.key === ' ' || e.code === 'Space') {
                    e.preventDefault();
                    togglePlayPause();
                } else if (e.key === 'ArrowRight') {
                    nextSlide();
                } else if (e.key === 'ArrowLeft') {
                    prevSlide();
                } else if (e.key === 'f' || e.key === 'F') {
                    toggleFullscreen();
                }
            });

            // Start Initial Display & Interval Schedulers
            renderSlide(0, false);
            startSlideTimer();
            setInterval(syncBackgroundFeed, SYNC_INTERVAL_MS);

        })();
    </script>
</body>

</html>
