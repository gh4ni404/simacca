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
    <header class="relative z-20 h-20 px-6 sm:px-8 flex items-center justify-between glass-panel border-b border-white/10 shrink-0 gap-4">
        <!-- Left: Brand & School Logo -->
        <div class="flex items-center gap-4 shrink-0">
            <?php if (!empty($logoSekolah)): ?>
                <img src="<?= base_url('files/logo/' . $logoSekolah) ?>" alt="Logo" class="w-12 h-12 object-contain drop-shadow-md rounded-lg">
            <?php else: ?>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            <?php endif; ?>

            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-lg sm:text-xl font-black tracking-tight text-white uppercase font-sans">
                        <?= esc($namaSekolah); ?>
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500/20 text-rose-400 border border-rose-500/30">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        LIVE SHOW
                    </span>
                </div>
                <p class="text-xs text-slate-400 font-medium">
                    <?= esc($alamatSekolah); ?> • <span class="text-cyan-400 font-semibold"><i class="fa-solid fa-shuffle text-[10px] mr-1"></i>Random Showcase (7 Hari Terakhir)</span>
                </p>
            </div>
        </div>

        <!-- Center: Live HUD Stats Chips (Relocated from Sidebar) -->
        <div class="hidden xl:flex items-center gap-3 shrink-0">
            <!-- Stat 1: Presensi -->
            <div class="flex items-center gap-3 px-4 py-2 rounded-2xl bg-slate-900/80 border border-blue-500/30 shadow-lg backdrop-blur-md">
                <div class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-sm shadow">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div class="text-left">
                    <div class="text-[10px] uppercase font-bold text-blue-400 tracking-wider">Presensi Hari Ini</div>
                    <div class="text-base font-black text-white font-mono leading-tight mt-0.5">
                        <span id="statAttendance"><?= esc($stats['attendance_rate'] ?? 98); ?>%</span>
                    </div>
                </div>
            </div>

            <!-- Stat 2: PKL -->
            <div class="flex items-center gap-3 px-4 py-2 rounded-2xl bg-slate-900/80 border border-emerald-500/30 shadow-lg backdrop-blur-md">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm shadow">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div class="text-left">
                    <div class="text-[10px] uppercase font-bold text-emerald-400 tracking-wider">Siswa PKL (DUDI)</div>
                    <div class="text-base font-black text-white font-mono leading-tight mt-0.5">
                        <span id="statPkl"><?= esc($stats['active_pkl'] ?? 0); ?></span> <span class="text-[10px] text-slate-400 font-normal">Siswa</span>
                    </div>
                </div>
            </div>

            <!-- Stat 3: Dokumentasi -->
            <div class="flex items-center gap-3 px-4 py-2 rounded-2xl bg-slate-900/80 border border-purple-500/30 shadow-lg backdrop-blur-md">
                <div class="w-8 h-8 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-sm shadow">
                    <i class="fa-solid fa-camera"></i>
                </div>
                <div class="text-left">
                    <div class="text-[10px] uppercase font-bold text-purple-400 tracking-wider">Dokumentasi</div>
                    <div class="text-base font-black text-white font-mono leading-tight mt-0.5">
                        <span id="statDocs"><?= esc($stats['total_docs_7days'] ?? 0); ?></span> <span class="text-[10px] text-slate-400 font-normal">Foto</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Realtime Digital Clock & Controls -->
        <div class="flex items-center gap-6 shrink-0">
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
    <!-- 2. MAIN WORKSPACE (FULL-WIDTH CINEMA MODE) -->
    <!-- ========================================== -->
    <main class="relative z-10 flex-1 min-h-0 px-4 sm:px-6 py-3.5 flex flex-col overflow-hidden">

        <!-- ====================================== -->
        <!-- FULL-SCREEN HERO SHOWCASE STAGE        -->
        <!-- ====================================== -->
        <section class="w-full flex-1 min-h-0 relative rounded-2xl overflow-hidden glass-panel border border-white/10 group shadow-2xl flex flex-col justify-end">

            <!-- Ambient Backdrop Mirror (Blurred) -->
            <div class="absolute inset-0 z-0 overflow-hidden">
                <img id="heroBackdropImg" src="" alt="Backdrop" class="w-full h-full object-cover filter blur-2xl brightness-[0.35] scale-125 transition-all duration-1000">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
            </div>

            <!-- Foreground Hero Media Stage (Full Height Canvas) -->
            <div class="absolute inset-0 z-10 flex items-center justify-center overflow-hidden p-3 sm:p-5">
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

            <!-- Seamless Lower-Third Gradient Overlay (Like User's Reference Image) -->
            <div class="relative z-20 w-full pt-16 pb-5 px-6 sm:px-10 bg-gradient-to-t from-[#060911]/95 via-[#060911]/80 to-transparent">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-5">
                    
                    <!-- Left: Profile Avatar + 3-Line Structured Activity Info -->
                    <div class="flex items-start sm:items-center gap-4 sm:gap-6 flex-1 min-w-0">
                        <!-- Profile Avatar directly alongside 3-Line Info -->
                        <div class="relative shrink-0">
                            <img id="heroUploaderAvatar" src="<?= base_url('assets/img/default-avatar.png') ?>" alt="Foto Pengunggah" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover border-2 border-cyan-400/80 shadow-[0_4px_25px_rgba(0,0,0,0.8)] bg-slate-900">
                            <span id="heroUploaderRoleIcon" class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center border-2 border-slate-900 shadow">
                                <i class="fa-solid fa-user-tie"></i>
                            </span>
                        </div>

                        <!-- 3-Line Structured Activity Info -->
                        <div class="flex-1 min-w-0">
                            <!-- Top Metadata Badges -->
                            <div class="flex items-center gap-2.5 mb-2 flex-wrap">
                                <span id="heroBadge" class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider glass-badge border shadow-sm">
                                    <i id="heroBadgeIcon" class="fa-solid fa-shield-halved"></i>
                                    <span id="heroBadgeLabel">Piket & Ketertiban</span>
                                </span>

                                <span id="slideCounterBadge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold bg-white/10 text-cyan-300 border border-white/10 shadow-sm">
                                    <i class="fa-solid fa-layer-group text-[10px]"></i>
                                    <span id="slideCounter">1 / 1</span>
                                </span>
                                
                                <span class="flex items-center gap-1.5 text-xs font-semibold text-slate-300">
                                    <i class="fa-regular fa-clock text-cyan-400"></i>
                                    <span id="heroTimeAgo">Baru saja</span>
                                </span>

                                <span class="flex items-center gap-1.5 text-xs font-semibold text-slate-300">
                                    <i class="fa-solid fa-location-dot text-rose-400"></i>
                                    <span id="heroLocation">Area Sekolah</span>
                                </span>
                            </div>

                            <!-- Line 1: Judul Modul / Aktivitas Utama (Besar, Bold, Putih dengan Drop Shadow) -->
                            <h2 id="heroTitle" class="text-2xl sm:text-3xl md:text-4xl font-black text-white tracking-tight leading-tight drop-shadow-[0_2px_10px_rgba(0,0,0,0.85)]">
                                Jurnal Piket Guru
                            </h2>

                            <!-- Line 2: Kegiatan / Deskripsi Rincian (Sesuai Referensi Gambar) -->
                            <p id="heroActivity" class="text-base sm:text-lg md:text-xl font-medium text-slate-100 mt-1.5 drop-shadow-[0_2px_8px_rgba(0,0,0,0.85)] leading-relaxed">
                                <span class="font-bold text-cyan-300">Kegiatan:</span> <span id="heroActivityText">Rutinitas pembakaran sampah</span>
                            </p>

                            <!-- Line 3: Penanggung Jawab / Pelaksana (Sesuai Referensi Gambar) -->
                            <p id="heroPic" class="text-sm sm:text-base font-normal text-slate-200 mt-1 drop-shadow-[0_2px_8px_rgba(0,0,0,0.85)] flex items-center gap-2 flex-wrap">
                                <span><span class="font-bold text-cyan-400">Penanggung Jawab:</span> <span id="heroPicText">Ashar, S.P.</span></span>
                                <span class="text-slate-400">•</span>
                                <span id="heroFormattedDate" class="text-xs sm:text-sm text-slate-300">04 September 2026</span>
                            </p>
                        </div>
                    </div>

                    <!-- Right Group: Relocated SIMACCA Portal QR Badge -->
                    <div class="hidden sm:flex items-center gap-2.5 p-2.5 rounded-2xl bg-black/40 backdrop-blur-md border border-white/10 shrink-0 shadow-lg">
                        <div class="w-11 h-11 rounded-xl bg-white p-1 flex items-center justify-center shrink-0 shadow">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode(base_url()) ?>&color=0b1120" alt="SIMACCA QR" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white flex items-center gap-1.5">
                                <span>SIMACCA Online</span>
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            </div>
                            <div class="text-[10px] text-slate-400">Scan Presensi & Jurnal</div>
                        </div>
                    </div>
                </div>

                <!-- Slide Animated Progress Bar -->
                <div class="w-full bg-white/10 rounded-full h-1.5 mt-4 overflow-hidden border border-white/5">
                    <div id="slideProgressBar" class="h-full bg-gradient-to-r from-blue-500 via-cyan-400 to-emerald-400 progress-bar-fill shadow-[0_0_12px_rgba(6,182,212,0.8)]" style="width: 0%;"></div>
                </div>
            </div>
        </section>

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
            const liveClockEl          = document.getElementById('liveClock');
            const liveDateEl           = document.getElementById('liveDate');
            const heroMainImg          = document.getElementById('heroMainImg');
            const heroBackdropImg      = document.getElementById('heroBackdropImg');
            const heroEmptyState       = document.getElementById('heroEmptyState');
            const heroBadge            = document.getElementById('heroBadge');
            const heroBadgeIcon        = document.getElementById('heroBadgeIcon');
            const heroBadgeLabel       = document.getElementById('heroBadgeLabel');
            const slideCounter         = document.getElementById('slideCounter');
            const heroTimeAgo          = document.getElementById('heroTimeAgo');
            const heroLocation         = document.getElementById('heroLocation');
            const heroTitle            = document.getElementById('heroTitle');
            const heroActivityText     = document.getElementById('heroActivityText');
            const heroPicText          = document.getElementById('heroPicText');
            const heroUploaderAvatar   = document.getElementById('heroUploaderAvatar');
            const heroUploaderRoleIcon = document.getElementById('heroUploaderRoleIcon');
            const heroFormattedDate    = document.getElementById('heroFormattedDate');
            const slideProgressBar     = document.getElementById('slideProgressBar');
            const statAttendance       = document.getElementById('statAttendance');
            const statPkl              = document.getElementById('statPkl');
            const statDocs             = document.getElementById('statDocs');
            const btnPlayPause         = document.getElementById('btnPlayPause');
            const iconPlayPause        = document.getElementById('iconPlayPause');
            const btnPrev              = document.getElementById('btnPrev');
            const btnNext              = document.getElementById('btnNext');
            const btnFullscreen        = document.getElementById('btnFullscreen');
            const tickerTrack          = document.getElementById('tickerTrack');

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
                    if (slideCounter) slideCounter.textContent = '0 / 0';
                    return;
                }

                heroEmptyState.classList.add('hidden');
                heroMainImg.classList.remove('hidden');
                heroBackdropImg.style.display = 'block';

                currentIndex = (index + feedItems.length) % feedItems.length;
                const item = feedItems[currentIndex];

                // Update Slide Counter Badge
                if (slideCounter) {
                    slideCounter.textContent = `${currentIndex + 1} / ${feedItems.length}`;
                }

                // Handle missing image gracefully
                const heroFallbackSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="800" height="450" viewBox="0 0 800 450"><rect width="800" height="450" fill="%230b1120"/><rect x="20" y="20" width="760" height="410" rx="16" fill="%230f172a" stroke="%231e293b" stroke-width="2"/><circle cx="400" cy="200" r="48" fill="%231e293b"/><path d="M380 185 L420 185 L420 225 L380 225 Z" fill="none" stroke="%2338bdf8" stroke-width="3"/><circle cx="400" cy="205" r="8" fill="%2338bdf8"/><text x="400" y="280" text-anchor="middle" fill="%2394a3b8" font-family="sans-serif" font-size="16" font-weight="600">${encodeURIComponent(item.headline_title || item.category_label || 'DOKUMENTASI')}</text></svg>`;

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

                // Meta text updates (Clean 3-Line Hierarchy matching User's Mockup)
                if (heroTimeAgo) heroTimeAgo.textContent = item.time_ago || 'Hari ini';
                if (heroLocation) heroLocation.textContent = item.location || 'SIMACCA';
                if (heroTitle) heroTitle.textContent = item.headline_title || item.title || 'Jurnal Dokumentasi Aktivitas';
                if (heroActivityText) heroActivityText.textContent = item.activity_text || item.title || item.description || 'Aktivitas pembelajaran dan lingkungan sekolah.';
                if (heroPicText) heroPicText.textContent = item.pic_text || item.uploader_name || 'Civitas Sekolah';
                if (heroFormattedDate) heroFormattedDate.textContent = item.formatted_date || '';

                // Uploader Avatar & Role Icon
                if (heroUploaderAvatar) {
                    if (item.uploader_photo) {
                        heroUploaderAvatar.src = item.uploader_photo;
                    } else {
                        heroUploaderAvatar.src = DEFAULT_AVATAR + encodeURIComponent(item.uploader_name || 'SIMACCA');
                    }
                }

                if (heroUploaderRoleIcon) {
                    let roleIconClass = 'fa-solid fa-user-tie';
                    let roleBg = 'bg-blue-600';
                    if (item.category === 'pkl') {
                        roleIconClass = 'fa-solid fa-user-graduate';
                        roleBg = 'bg-emerald-600';
                    } else if (item.category === 'piket') {
                        roleIconClass = 'fa-solid fa-shield-halved';
                        roleBg = 'bg-amber-600';
                    } else if (item.category === 'wali') {
                        roleIconClass = 'fa-solid fa-user-group';
                        roleBg = 'bg-purple-600';
                    } else if (item.is_pengganti) {
                        roleIconClass = 'fa-solid fa-people-arrows';
                        roleBg = 'bg-cyan-600';
                    }
                    heroUploaderRoleIcon.className = `absolute -bottom-1 -right-1 w-6 h-6 rounded-full ${roleBg} text-white text-xs flex items-center justify-center border-2 border-slate-900 shadow`;
                    heroUploaderRoleIcon.innerHTML = `<i class="${roleIconClass}"></i>`;
                }

                // Preload ahead
                preloadNextImages();

                // Reset Progress Bar
                resetProgressBar();
            }

            // 4. Progress Bar & Slide Timer Engine
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

            // 5. Silent Background Data Synchronization (No Flicker / Zero Reload)
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
                            if (slideCounter) slideCounter.textContent = `${currentIndex + 1} / ${feedItems.length}`;
                        }
                    }
                } catch (err) {
                    console.warn('Background sync failed silently (offline fallback mode active):', err);
                }
            }

            // 6. Screen Wake Lock (Keeps Smart TV Screen Awake)
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

            // 7. Fullscreen Toggle
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

            // 8. Event Listeners & Keyboard Shortcuts
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
