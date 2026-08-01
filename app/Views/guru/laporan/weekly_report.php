<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
    .stat-card { transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15); }
    .print-button { transition: all 0.3s ease; }
    .print-button:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3); }
    .status-hadir { background-color: #d1fae5; color: #065f46; }
    .status-sakit { background-color: #fef3c7; color: #92400e; }
    .status-izin { background-color: #dbeafe; color: #1e40af; }
    .status-alpa { background-color: #fee2e2; color: #991b1b; }
    .subject-tab.active { border-bottom: 3px solid #4f46e5; color: #4f46e5; font-weight: bold; }
    @media print {
        .no-print { display: none !important; }
        .print-break { page-break-before: always; }
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 animate-fade-in-up">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center flex-1">
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white p-3 rounded-xl mr-4 shadow-lg">
                        <i class="fas fa-calendar-week text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">
                            <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                Laporan Mingguan Absensi Siswa
                            </span>
                        </h1>
                        <p class="text-gray-600 mt-1">Rekap kehadiran siswa per mata pelajaran untuk guru mapel</p>
                        <?php if ($isGuruMapel): ?>
                            <span class="inline-block mt-1 px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                <i class="fas fa-chalkboard-teacher mr-1"></i> Guru Mapel
                            </span>
                        <?php endif; ?>
                        <?php if ($isWakakur): ?>
                            <span class="inline-block mt-1 ml-2 px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                <i class="fas fa-user-tie mr-1"></i> Wakakur
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($totalSessions > 0): ?>
                    <a href="<?= base_url("guru/laporan-mingguan/print?week_start={$weekStart}" . ($selectedMapelId ? "&mapel_id={$selectedMapelId}" : "")) ?>" 
                       class="no-print print-button bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg inline-flex items-center"
                       target="_blank">
                        <i class="fas fa-print mr-2"></i> Cetak Laporan
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-8 animate-fade-in-up" style="animation-delay: 0.1s;">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-filter text-indigo-600 mr-3"></i>
                Filter Laporan Mingguan
            </h2>

            <form method="get" action="<?= base_url('guru/laporan-mingguan') ?>" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Minggu (Week Start) -->
                    <div>
                        <label for="week_start" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt text-indigo-600 mr-2"></i>
                            Minggu Mulai (Senin)
                        </label>
                        <input type="date" id="week_start" name="week_start" 
                               value="<?= esc($weekStart) ?>"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-indigo-200 focus:border-indigo-500 transition-all"
                               required>
                        <p class="text-xs text-gray-500 mt-1">Laporan otomatis untuk periode Senin - Sabtu</p>
                    </div>

                    <!-- Mata Pelajaran -->
                    <div>
                        <label for="mapel_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-book text-green-600 mr-2"></i>
                            Mata Pelajaran
                        </label>
                        <select id="mapel_id" name="mapel_id" 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-indigo-200 focus:border-indigo-500 transition-all">
                            <option value="">-- Semua Mata Pelajaran --</option>
                            <?php foreach ($subjectsList as $subject): ?>
                                <option value="<?= $subject['id'] ?>" <?= $selectedMapelId == $subject['id'] ? 'selected' : '' ?>>
                                    <?= esc($subject['nama_mapel']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all flex items-center justify-center">
                            <i class="fas fa-search mr-2"></i> Tampilkan Laporan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Week Info Banner -->
        <?php if ($totalSessions > 0): ?>
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 mb-8 text-white animate-fade-in-up shadow-lg">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-xl font-bold mb-2">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Periode: <?= date('d M Y', strtotime($weekStart)) ?> - <?= date('d M Y', strtotime($weekEnd)) ?>
                        </h3>
                        <p class="text-indigo-100">
                            Total pertemuan tercatat minggu ini: <span class="font-bold text-white"><?= $totalSessions ?></span> sesi
                            <?php if ($selectedMapelId): ?>
                                <?php 
                                $selectedMapelName = 'Semua';
                                foreach ($subjectsList as $subject) {
                                    if ($subject['id'] == $selectedMapelId) {
                                        $selectedMapelName = $subject['nama_mapel'];
                                        break;
                                    }
                                }
                                ?>
                                | Mata Pelajaran: <span class="font-bold text-white"><?= esc($selectedMapelName) ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="<?= base_url('guru/laporan') ?>" class="text-white hover:text-indigo-100 font-semibold">
                            <i class="fas fa-chart-bar mr-1"></i> Lihat Laporan Umum
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($reportBySubject)): ?>
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-xl p-12 text-center animate-fade-in-up">
                <div class="text-6xl mb-4">📭</div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Belum Ada Data</h3>
                <p class="text-gray-600 mb-6">Tidak ada data absensi yang tercatat untuk periode minggu ini.</p>
                <p class="text-sm text-gray-500">Silakan pilih minggu lain atau pastikan Anda sudah mengisi absensi untuk jadwal mengajar minggu ini.</p>
            </div>
        <?php else: ?>
            <!-- Report by Subject -->
            <?php foreach ($reportBySubject as $mapelId => $report): ?>
                <div class="bg-white rounded-2xl shadow-xl mb-8 animate-fade-in-up overflow-hidden">
                    <!-- Subject Header -->
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-white bg-opacity-20 p-3 rounded-xl mr-4">
                                    <i class="fas fa-book-open text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold"><?= esc($report['nama_mapel']) ?></h3>
                                    <p class="text-indigo-100 mt-1">
                                        <?= $report['summary']['total_pertemuan'] ?> pertemuan tercatat minggu ini
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="grid grid-cols-4 gap-4 text-center">
                                    <div>
                                        <div class="text-2xl font-bold"><?= $report['summary']['total_hadir'] ?></div>
                                        <div class="text-xs text-indigo-100">Hadir</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold"><?= $report['summary']['total_sakit'] ?></div>
                                        <div class="text-xs text-indigo-100">Sakit</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold"><?= $report['summary']['total_izin'] ?></div>
                                        <div class="text-xs text-indigo-100">Izin</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold"><?= $report['summary']['total_alpa'] ?></div>
                                        <div class="text-xs text-indigo-100">Alpa</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sessions List -->
                    <div class="p-6">
                        <div class="space-y-6">
                            <?php foreach ($report['sessions'] as $session): ?>
                                <div class="border-2 border-gray-200 rounded-xl overflow-hidden hover:border-indigo-300 transition-colors">
                                    <!-- Session Header -->
                                    <div class="bg-gray-50 p-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
                                        <div class="flex items-center mb-2 md:mb-0">
                                            <div class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-lg font-bold text-sm mr-3">
                                                <i class="fas fa-calendar-day mr-1"></i>
                                                <?= date('d/m/Y', strtotime($session['tanggal'])) ?>
                                            </div>
                                            <div class="text-gray-800 font-semibold">
                                                <?= $session['hari'] ?>, <?= $session['jam'] ?>
                                            </div>
                                            <div class="ml-3 text-gray-500 text-sm">
                                                Kelas: <span class="font-semibold text-gray-700"><?= esc($session['kelas_nama']) ?></span>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                <i class="fas fa-users mr-1"></i> <?= $session['total_siswa'] ?> siswa
                                            </span>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                                Pertemuan ke-<?= $session['pertemuan_ke'] ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Session Content -->
                                    <div class="p-4">
                                        <!-- Materi -->
                                        <?php if ($session['materi'] && $session['materi'] !== '-'): ?>
                                            <div class="mb-4 bg-gray-50 p-3 rounded-lg">
                                                <div class="text-xs font-semibold text-gray-500 mb-1">
                                                    <i class="fas fa-lightbulb mr-1 text-yellow-500"></i> Materi Pembelajaran
                                                </div>
                                                <div class="text-sm text-gray-700"><?= esc($session['materi']) ?></div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Attendance Stats Bar -->
                                        <div class="flex items-center space-x-2 mb-4">
                                            <div class="flex-1 bg-gray-200 rounded-full h-4 overflow-hidden flex">
                                                <?php 
                                                $total = max($session['total_siswa'], 1);
                                                $hadirPct = ($session['hadir'] / $total) * 100;
                                                $sakitPct = ($session['sakit'] / $total) * 100;
                                                $izinPct = ($session['izin'] / $total) * 100;
                                                $alpaPct = ($session['alpa'] / $total) * 100;
                                                ?>
                                                <?php if ($hadirPct > 0): ?>
                                                    <div class="bg-green-500 h-full" style="width: <?= $hadirPct ?>%" title="Hadir: <?= $session['hadir'] ?>"></div>
                                                <?php endif; ?>
                                                <?php if ($sakitPct > 0): ?>
                                                    <div class="bg-yellow-400 h-full" style="width: <?= $sakitPct ?>%" title="Sakit: <?= $session['sakit'] ?>"></div>
                                                <?php endif; ?>
                                                <?php if ($izinPct > 0): ?>
                                                    <div class="bg-blue-400 h-full" style="width: <?= $izinPct ?>%" title="Izin: <?= $session['izin'] ?>"></div>
                                                <?php endif; ?>
                                                <?php if ($alpaPct > 0): ?>
                                                    <div class="bg-red-500 h-full" style="width: <?= $alpaPct ?>%" title="Alpa: <?= $session['alpa'] ?>"></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Stats Labels -->
                                        <div class="grid grid-cols-4 gap-2 mb-4 text-center text-xs font-semibold">
                                            <div class="bg-green-50 text-green-700 py-2 rounded-lg border border-green-200">
                                                <div class="text-lg font-bold"><?= $session['hadir'] ?></div>
                                                Hadir
                                            </div>
                                            <div class="bg-yellow-50 text-yellow-700 py-2 rounded-lg border border-yellow-200">
                                                <div class="text-lg font-bold"><?= $session['sakit'] ?></div>
                                                Sakit
                                            </div>
                                            <div class="bg-blue-50 text-blue-700 py-2 rounded-lg border border-blue-200">
                                                <div class="text-lg font-bold"><?= $session['izin'] ?></div>
                                                Izin
                                            </div>
                                            <div class="bg-red-50 text-red-700 py-2 rounded-lg border border-red-200">
                                                <div class="text-lg font-bold"><?= $session['alpa'] ?></div>
                                                Alpa
                                            </div>
                                        </div>

                                        <!-- Details Table -->
                                        <?php if (!empty($session['details'])): ?>
                                            <div class="overflow-x-auto">
                                                <table class="w-full text-sm">
                                                    <thead>
                                                        <tr class="bg-gray-100">
                                                            <th class="px-4 py-2 text-left rounded-tl-lg">No</th>
                                                            <th class="px-4 py-2 text-left">Nama Siswa</th>
                                                            <th class="px-4 py-2 text-left">NIS</th>
                                                            <th class="px-4 py-2 text-center">Status</th>
                                                            <th class="px-4 py-2 text-left rounded-tr-lg">Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($session['details'] as $idx => $detail): ?>
                                                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                                                <td class="px-4 py-3 text-gray-500"><?= $idx + 1 ?></td>
                                                                <td class="px-4 py-3 font-medium text-gray-800"><?= esc($detail['siswa_nama']) ?></td>
                                                                <td class="px-4 py-3 text-gray-600"><?= esc($detail['nis']) ?></td>
                                                                <td class="px-4 py-3 text-center">
                                                                    <span class="px-3 py-1 rounded-full text-xs font-bold status-<?= $detail['status'] ?>">
                                                                        <?= ucfirst($detail['status']) ?>
                                                                    </span>
                                                                </td>
                                                                <td class="px-4 py-3 text-gray-600 text-xs">
                                                                    <?= esc($detail['keterangan'] ?: '-') ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-4 text-gray-500">
                                                <i class="fas fa-info-circle mr-1"></i> Belum ada data detail kehadiran
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Subject Summary Footer -->
                    <div class="bg-gray-50 p-6 border-t border-gray-200">
                        <h4 class="text-sm font-bold text-gray-700 mb-3">
                            <i class="fas fa-calculator mr-1 text-indigo-600"></i> Rekapitulasi: <?= esc($report['nama_mapel']) ?>
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <div class="text-center p-3 bg-white rounded-xl border border-gray-200">
                                <div class="text-xs text-gray-500 mb-1">Total Pertemuan</div>
                                <div class="text-xl font-bold text-gray-800"><?= $report['summary']['total_pertemuan'] ?></div>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-xl border border-green-200">
                                <div class="text-xs text-green-600 mb-1">Total Hadir</div>
                                <div class="text-xl font-bold text-green-700"><?= $report['summary']['total_hadir'] ?></div>
                            </div>
                            <div class="text-center p-3 bg-yellow-50 rounded-xl border border-yellow-200">
                                <div class="text-xs text-yellow-600 mb-1">Total Sakit</div>
                                <div class="text-xl font-bold text-yellow-700"><?= $report['summary']['total_sakit'] ?></div>
                            </div>
                            <div class="text-center p-3 bg-blue-50 rounded-xl border border-blue-200">
                                <div class="text-xs text-blue-600 mb-1">Total Izin</div>
                                <div class="text-xl font-bold text-blue-700"><?= $report['summary']['total_izin'] ?></div>
                            </div>
                            <div class="text-center p-3 bg-red-50 rounded-xl border border-red-200">
                                <div class="text-xs text-red-600 mb-1">Total Alpa</div>
                                <div class="text-xl font-bold text-red-700"><?= $report['summary']['total_alpa'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
