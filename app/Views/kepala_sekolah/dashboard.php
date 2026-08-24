<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header Hero Banner -->
    <div class="bg-gradient-to-r from-blue-800 via-indigo-800 to-purple-900 rounded-2xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 w-1/3 opacity-10 flex items-center justify-center pointer-events-none">
            <i class="fas fa-school text-9xl"></i>
        </div>
        <div class="relative z-10">
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider text-blue-100 flex items-center gap-1.5">
                    <i class="fas fa-crown text-yellow-300"></i> Executive Portal
                </span>
                <span class="px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-blue-100 flex items-center gap-1.5">
                    <i class="fas fa-calendar-alt"></i> TA <?= esc($activeTA) ?>
                </span>
                <span class="px-3 py-1 bg-emerald-500/20 backdrop-blur-md text-emerald-200 border border-emerald-400/30 rounded-full text-xs font-medium">
                    <i class="fas fa-circle text-[8px] mr-1 text-emerald-400"></i> Real-time Monitoring
                </span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
                <?= get_greeting() ?>, <?= esc($user['nama_lengkap'] ?? $kepalaSekolahNama ?? 'Kepala Sekolah') ?>
            </h1>
            <p class="text-blue-100 text-sm mt-1 max-w-2xl">
                Selamat datang di Executive Dashboard SIMACCA. Pusat pengawasan integritas presensi, kegiatan pembelajaran, dan performa operasional sekolah.
            </p>
        </div>
    </div>

    <!-- Stats Grid (5 Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Card 1: Total Siswa -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Total Siswa Aktif</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1"><?= number_format($stats['total_siswa']) ?></h3>
                <p class="text-xs text-blue-600 mt-1 font-medium"><i class="fas fa-check-circle mr-1"></i> TA <?= esc($activeTA) ?></p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>

        <!-- Card 2: Total Guru -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Guru & Pendidik</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1"><?= number_format($stats['total_guru']) ?></h3>
                <p class="text-xs text-indigo-600 mt-1 font-medium"><i class="fas fa-chalkboard-teacher mr-1"></i> Tenaga Kerja</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                <i class="fas fa-users-cog"></i>
            </div>
        </div>

        <!-- Card 3: Rombongan Belajar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Rombel / Kelas</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1"><?= number_format($stats['total_kelas']) ?></h3>
                <p class="text-xs text-purple-600 mt-1 font-medium"><i class="fas fa-door-open mr-1"></i> Kelas Terdaftar</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                <i class="fas fa-school"></i>
            </div>
        </div>

        <!-- Card 4: Siswa Hadir Today -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Siswa Hadir Hari Ini</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1"><?= number_format($stats['siswa_hadir_today']) ?></h3>
                <p class="text-xs text-emerald-600 mt-1 font-medium">
                    <i class="fas fa-clipboard-check mr-1"></i> Real-time Presensi
                </p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                <i class="fas fa-user-check"></i>
            </div>
        </div>

        <!-- Card 5: Guru Hadir Today -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between transition-all hover:shadow-md">
            <div>
                <p class="text-xs uppercase font-semibold text-gray-500 tracking-wider">Guru Hadir Hari Ini</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1"><?= number_format($stats['guru_hadir_today']) ?></h3>
                <p class="text-xs text-amber-600 mt-1 font-medium">
                    <i class="fas fa-user-clock mr-1"></i> Presensi Guru
                </p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                <i class="fas fa-id-badge"></i>
            </div>
        </div>
    </div>

    <!-- Attendance Status Breakdown Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-emerald-50/70 border border-emerald-100 rounded-xl p-4 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-500 text-white flex items-center justify-center font-bold text-lg">
                <i class="fas fa-check"></i>
            </div>
            <div>
                <p class="text-xs text-emerald-700 font-medium">Hadir Hari Ini</p>
                <p class="text-xl font-extrabold text-emerald-900"><?= number_format($stats['siswa_hadir_today']) ?></p>
            </div>
        </div>
        <div class="bg-blue-50/70 border border-blue-100 rounded-xl p-4 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-blue-500 text-white flex items-center justify-center font-bold text-lg">
                <i class="fas fa-envelope-open-text"></i>
            </div>
            <div>
                <p class="text-xs text-blue-700 font-medium">Izin Hari Ini</p>
                <p class="text-xl font-extrabold text-blue-900"><?= number_format($stats['siswa_izin_today']) ?></p>
            </div>
        </div>
        <div class="bg-amber-50/70 border border-amber-100 rounded-xl p-4 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-amber-500 text-white flex items-center justify-center font-bold text-lg">
                <i class="fas fa-notes-medical"></i>
            </div>
            <div>
                <p class="text-xs text-amber-700 font-medium">Sakit Hari Ini</p>
                <p class="text-xl font-extrabold text-amber-900"><?= number_format($stats['siswa_sakit_today']) ?></p>
            </div>
        </div>
        <div class="bg-rose-50/70 border border-rose-100 rounded-xl p-4 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-rose-500 text-white flex items-center justify-center font-bold text-lg">
                <i class="fas fa-user-times"></i>
            </div>
            <div>
                <p class="text-xs text-rose-700 font-medium">Tanpa Keterangan / Alpa</p>
                <p class="text-xl font-extrabold text-rose-900"><?= number_format($stats['siswa_alpa_today']) ?></p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart 1: Donut Chart Distribusi Kehadiran -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-pie text-indigo-600 mr-2"></i> Distribusi Kehadiran Bulan Ini
                    </h3>
                    <span class="text-xs text-gray-400">Bulanan</span>
                </div>
                <div class="relative h-56 flex items-center justify-center">
                    <canvas id="attendancePieChart"></canvas>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-gray-100 text-xs">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                    <span class="text-gray-600">Hadir: <?= number_format($chartData['attendancePie']['data'][0] ?? 0) ?></span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
                    <span class="text-gray-600">Izin: <?= number_format($chartData['attendancePie']['data'][1] ?? 0) ?></span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                    <span class="text-gray-600">Sakit: <?= number_format($chartData['attendancePie']['data'][2] ?? 0) ?></span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span>
                    <span class="text-gray-600">Alpa: <?= number_format($chartData['attendancePie']['data'][3] ?? 0) ?></span>
                </div>
            </div>
        </div>

        <!-- Chart 2: Trend Kehadiran 7 Hari Terakhir -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i> Trend Kehadiran (7 Hari Terakhir)
                    </h3>
                    <span class="text-xs text-gray-400">Harian</span>
                </div>
                <div class="relative h-60">
                    <canvas id="attendanceTrendChart"></canvas>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span><i class="fas fa-info-circle text-blue-500 mr-1"></i> Data dihitung berdasarkan total sesi presensi yang tercatat</span>
                <a href="<?= base_url('admin/laporan/statistik') ?>" class="text-indigo-600 font-semibold hover:underline">Lihat Detail Statistik &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Quick Navigation & Executive Duty Monitoring -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Navigasi Utama Eksekutif -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center justify-between">
                <span><i class="fas fa-th-large text-indigo-600 mr-2"></i> Access Center Eksekutif</span>
                <span class="text-xs text-gray-400 font-normal">Pilih modul untuk pemantauan</span>
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <a href="<?= base_url('admin/guru') ?>" class="p-3.5 bg-slate-50 hover:bg-indigo-50 border border-slate-200/60 hover:border-indigo-200 rounded-xl transition-all group flex flex-col justify-between">
                    <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 block">Data Guru & Staf</span>
                        <span class="text-[11px] text-gray-400">Monitoring Pendidik</span>
                    </div>
                </a>

                <a href="<?= base_url('admin/siswa') ?>" class="p-3.5 bg-slate-50 hover:bg-blue-50 border border-slate-200/60 hover:border-blue-200 rounded-xl transition-all group flex flex-col justify-between">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 block">Data Peserta Didik</span>
                        <span class="text-[11px] text-gray-400">Daftar Siswa & Kelas</span>
                    </div>
                </a>

                <a href="<?= base_url('admin/absensi-guru') ?>" class="p-3.5 bg-slate-50 hover:bg-amber-50 border border-slate-200/60 hover:border-amber-200 rounded-xl transition-all group flex flex-col justify-between">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 block">Presensi Guru</span>
                        <span class="text-[11px] text-gray-400">Log Check-in/out</span>
                    </div>
                </a>

                <a href="<?= base_url('admin/absensi') ?>" class="p-3.5 bg-slate-50 hover:bg-emerald-50 border border-slate-200/60 hover:border-emerald-200 rounded-xl transition-all group flex flex-col justify-between">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 block">Absensi Kelas</span>
                        <span class="text-[11px] text-gray-400">Monitoring Per Kelas</span>
                    </div>
                </a>

                <a href="<?= base_url('admin/jurnal-piket') ?>" class="p-3.5 bg-slate-50 hover:bg-rose-50 border border-slate-200/60 hover:border-rose-200 rounded-xl transition-all group flex flex-col justify-between">
                    <div class="w-9 h-9 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 block">Jurnal Piket</span>
                        <span class="text-[11px] text-gray-400">Laporan Guru Piket</span>
                    </div>
                </a>

                <a href="<?= base_url('admin/absensi-pkl') ?>" class="p-3.5 bg-slate-50 hover:bg-purple-50 border border-slate-200/60 hover:border-purple-200 rounded-xl transition-all group flex flex-col justify-between">
                    <div class="w-9 h-9 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 block">Monitoring PKL</span>
                        <span class="text-[11px] text-gray-400">Presensi & Jurnal PKL</span>
                    </div>
                </a>

                <a href="<?= base_url('admin/laporan/absensi-shalat') ?>" class="p-3.5 bg-slate-50 hover:bg-teal-50 border border-slate-200/60 hover:border-teal-200 rounded-xl transition-all group flex flex-col justify-between">
                    <div class="w-9 h-9 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center text-lg mb-2 group-hover:scale-110 transition-transform">
                        <i class="fas fa-mosque"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 block">Absensi Shalat</span>
                        <span class="text-[11px] text-gray-400">Monitoring Ibadah</span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Monitoring Guru Piket & Informasi Sekolah -->
        <div class="space-y-6">
            <!-- Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-id-card text-blue-600 mr-2"></i> Profil Eksekutif Sekolah
                </h3>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-center p-2.5 bg-gray-50 rounded-lg">
                        <span class="text-gray-500">Kepala Sekolah</span>
                        <span class="font-bold text-gray-800"><?= esc($kepalaSekolahNama ?: $user['nama_lengkap']) ?></span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 bg-gray-50 rounded-lg">
                        <span class="text-gray-500">NIP Kepala Sekolah</span>
                        <span class="font-mono text-gray-800 font-semibold"><?= esc($kepalaSekolahNip ?: '-') ?></span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 bg-gray-50 rounded-lg">
                        <span class="text-gray-500">Tahun Ajaran</span>
                        <span class="font-bold text-indigo-600"><?= esc($activeTA) ?></span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 bg-gray-50 rounded-lg">
                        <span class="text-gray-500">Tempat PKL Aktif</span>
                        <span class="font-bold text-purple-600"><?= number_format($stats['total_tempat_pkl']) ?> Lokasi</span>
                    </div>
                </div>
            </div>

            <!-- Guru Piket Log Today -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-bold text-gray-800 flex items-center">
                        <i class="fas fa-clipboard-check text-rose-600 mr-2"></i> Status Piket Hari Ini
                    </h3>
                    <a href="<?= base_url('admin/jurnal-piket') ?>" class="text-[11px] text-indigo-600 font-semibold hover:underline">Semua &rarr;</a>
                </div>

                <?php if (!empty($piketHariIni)): ?>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        <?php foreach ($piketHariIni as $piket): ?>
                            <div class="p-2.5 bg-gray-50 rounded-lg border border-gray-100 flex items-start justify-between text-xs">
                                <div>
                                    <p class="font-semibold text-gray-800"><?= esc($piket['nama_guru'] ?? 'Guru Piket') ?></p>
                                    <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1"><?= esc($piket['catatan'] ?? 'Melaksanakan tugas piket') ?></p>
                                </div>
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] rounded font-bold">Terisi</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-4 bg-amber-50/60 border border-amber-100 rounded-xl text-center">
                        <i class="fas fa-info-circle text-amber-500 text-lg mb-1"></i>
                        <p class="text-xs font-semibold text-amber-800">Belum Ada Jurnal Piket Hari Ini</p>
                        <p class="text-[11px] text-amber-600 mt-0.5">Guru piket yang bertugas belum menginput laporan piket harian.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Data dari backend
        const chartData = <?= json_encode($chartData) ?>;

        // 1. Donut Chart - Kehadiran Bulanan
        const pieCtx = document.getElementById('attendancePieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: chartData.attendancePie.labels,
                datasets: [{
                    data: chartData.attendancePie.data,
                    backgroundColor: chartData.attendancePie.colors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '70%'
            }
        });

        // 2. Line Chart - Trend 7 Hari Terakhir
        const trendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: chartData.attendanceLine.labels,
                datasets: [{
                    label: 'Jumlah Presensi Sesi',
                    data: chartData.attendanceLine.data,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#2563EB',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#F3F4F6'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>

