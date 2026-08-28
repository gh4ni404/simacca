<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-fingerprint text-blue-600 mr-2"></i>Absensi Guru
            </h2>
            <p class="text-gray-600">Check-in dan check-out kehadiran harian</p>
        </div>
    </div>
</div>

<!-- Flash Messages -->
<?= view('components/alerts') ?>

<!-- Tab Navigation -->
<div class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden">
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px" role="tablist">
            <button type="button"
                class="tab-button flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors"
                data-tab="dashboard"
                role="tab">
                <i class="fas fa-th mr-2"></i>Dashboard
            </button>
            <button type="button"
                class="tab-button flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors"
                data-tab="statistics"
                role="tab">
                <i class="fas fa-chart-bar mr-2"></i>Statistik
            </button>
        </nav>
    </div>
</div>

<!-- Tab Content Container -->
<div class="tab-content-container">
    <!-- Dashboard Tab -->
    <div class="tab-content" id="dashboard-tab">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Check-in/Check-out -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Today's Status Card -->
                <div class="bg-white rounded-xl shadow">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 rounded-t-xl">
                        <h3 class="text-lg font-semibold">
                            <i class="fas fa-calendar-day mr-2"></i>Status Hari Ini - <?= date('d F Y') ?>
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php if ($hasCheckedIn && $todayAbsensi): ?>
                            <!-- Already Checked In -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <!-- Check-In Card -->
                                <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-300 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h4 class="text-green-700 font-semibold flex items-center text-lg">
                                                <i class="fas fa-sign-in-alt mr-2"></i>Check-In
                                            </h4>
                                            <span class="px-3 py-1 mt-2 inline-block bg-<?= $todayAbsensi['status'] == 'hadir' ? 'green' : 'yellow' ?>-500 text-white text-xs font-semibold rounded-full">
                                                <?= ucfirst($todayAbsensi['status']) ?>
                                            </span>
                                        </div>
                                        <div class="bg-green-200 p-3 rounded-full">
                                            <i class="fas fa-check-circle text-green-700 text-2xl"></i>
                                        </div>
                                    </div>
                                    <p class="text-4xl font-bold text-green-800 mb-4"><?= date('H:i', strtotime($todayAbsensi['check_in'])) ?></p>
                                    <div class="flex items-center gap-2 text-sm text-green-700 mb-3">
                                        <i class="fas fa-clock"></i>
                                        <span><?= date('d F Y', strtotime($todayAbsensi['tanggal'])) ?></span>
                                    </div>
                                    <?php if (isset($todayAbsensi['foto_check_in']) && $todayAbsensi['foto_check_in']): ?>
                                        <?php
                                        // Convert storage path to URL path
                                        $photoUrl = str_replace('uploads/absensi_guru/', 'files/absensi-guru/', $todayAbsensi['foto_check_in']);
                                        ?>
                                        <button type="button" class="w-full px-4 py-2 bg-white border-2 border-green-500 text-green-700 rounded-lg hover:bg-green-50 transition-colors font-medium"
                                            onclick="showImage('<?= base_url($photoUrl) ?>')">
                                            <i class="fas fa-image mr-2"></i>Lihat Foto
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <!-- Check-Out Card -->
                                <div class="bg-gradient-to-br from-<?= $hasCheckedOut ? 'blue' : 'orange' ?>-50 to-<?= $hasCheckedOut ? 'blue' : 'orange' ?>-100 border-2 border-<?= $hasCheckedOut ? 'blue' : 'orange' ?>-300 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h4 class="text-<?= $hasCheckedOut ? 'blue' : 'orange' ?>-700 font-semibold flex items-center text-lg">
                                                <i class="fas fa-sign-out-alt mr-2"></i>Check-Out
                                            </h4>
                                            <?php if ($hasCheckedOut): ?>
                                                <span class="px-3 py-1 mt-2 inline-block bg-blue-500 text-white text-xs font-semibold rounded-full">
                                                    Selesai
                                                </span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 mt-2 inline-block bg-orange-500 text-white text-xs font-semibold rounded-full">
                                                    Pending
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="bg-<?= $hasCheckedOut ? 'blue' : 'orange' ?>-200 p-3 rounded-full">
                                            <i class="fas fa-<?= $hasCheckedOut ? 'check-circle' : 'clock' ?> text-<?= $hasCheckedOut ? 'blue' : 'orange' ?>-700 text-2xl"></i>
                                        </div>
                                    </div>

                                    <?php if ($hasCheckedOut): ?>
                                        <p class="text-4xl font-bold text-blue-800 mb-4"><?= date('H:i', strtotime($todayAbsensi['check_out'])) ?></p>
                                        <div class="flex items-center gap-2 text-sm text-blue-700 mb-3">
                                            <i class="fas fa-clock"></i>
                                            <span><?= date('d F Y', strtotime($todayAbsensi['tanggal'])) ?></span>
                                        </div>
                                        <?php if (isset($todayAbsensi['foto_check_out']) && $todayAbsensi['foto_check_out']): ?>
                                            <?php
                                            // Convert storage path to URL path
                                            $photoUrl = str_replace('uploads/absensi_guru/', 'files/absensi-guru/', $todayAbsensi['foto_check_out']);
                                            ?>
                                            <button type="button" class="w-full px-4 py-2 bg-white border-2 border-blue-500 text-blue-700 rounded-lg hover:bg-blue-50 transition-colors font-medium"
                                                onclick="showImage('<?= base_url($photoUrl) ?>')">
                                                <i class="fas fa-image mr-2"></i>Lihat Foto
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-2xl font-semibold text-orange-600 mb-4">--:--</p>
                                        <p class="text-sm text-orange-700 mb-4">Belum melakukan check-out</p>
                                        <button type="button" class="w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all font-medium shadow-md hover:shadow-lg" id="btnCheckOut">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Check-Out Sekarang
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Duration Info (if checked out) -->
                            <?php if ($hasCheckedOut): ?>
                                <?php
                                $masuk = strtotime($todayAbsensi['check_in']);
                                $keluar = strtotime($todayAbsensi['check_out']);
                                $diff = $keluar - $masuk;
                                $hours = floor($diff / 3600);
                                $minutes = floor(($diff % 3600) / 60);
                                ?>
                                <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl p-4 mb-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-purple-200 p-3 rounded-full">
                                                <i class="fas fa-hourglass-half text-purple-700 text-xl"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-purple-600 font-medium">Durasi Kerja Hari Ini</p>
                                                <p class="text-2xl font-bold text-purple-800"><?= $hours ?> jam <?= $minutes ?> menit</p>
                                            </div>
                                        </div>
                                        <?php if (($hours * 60 + $minutes) >= 480): ?>
                                            <span class="px-4 py-2 bg-green-500 text-white rounded-full text-sm font-semibold">
                                                <i class="fas fa-check-circle mr-1"></i>Target Terpenuhi
                                            </span>
                                        <?php else: ?>
                                            <span class="px-4 py-2 bg-orange-500 text-white rounded-full text-sm font-semibold">
                                                <i class="fas fa-info-circle mr-1"></i>Kurang dari Target
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Keterangan Section -->
                            <?php if ((isset($todayAbsensi['keterangan_masuk']) && $todayAbsensi['keterangan_masuk']) || ($hasCheckedOut && isset($todayAbsensi['keterangan_keluar']) && $todayAbsensi['keterangan_keluar'])): ?>
                                <div class="space-y-3">
                                    <?php if (isset($todayAbsensi['keterangan_masuk']) && $todayAbsensi['keterangan_masuk']): ?>
                                        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
                                            <div class="flex items-start gap-3">
                                                <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                                                <div>
                                                    <p class="font-semibold text-blue-800 mb-1">Keterangan Check-In</p>
                                                    <p class="text-blue-700"><?= esc($todayAbsensi['keterangan_masuk']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($hasCheckedOut && isset($todayAbsensi['keterangan_keluar']) && $todayAbsensi['keterangan_keluar']): ?>
                                        <div class="bg-indigo-50 border-l-4 border-indigo-500 rounded-lg p-4">
                                            <div class="flex items-start gap-3">
                                                <i class="fas fa-info-circle text-indigo-500 mt-1"></i>
                                                <div>
                                                    <p class="font-semibold text-indigo-800 mb-1">Keterangan Check-Out</p>
                                                    <p class="text-indigo-700"><?= esc($todayAbsensi['keterangan_keluar']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <!-- Not Checked In Yet -->
                            <div class="text-center py-8">
                                <div class="mb-6 relative inline-block">
                                    <div class="absolute inset-0 bg-blue-200 rounded-full blur-2xl opacity-50 animate-pulse"></div>
                                    <i class="fas fa-clock relative text-blue-500 text-7xl mb-4"></i>
                                </div>
                                <h4 class="text-2xl font-bold text-gray-800 mb-3">Belum Check-In Hari Ini</h4>
                                <p class="text-gray-600 mb-6 max-w-md mx-auto">Silakan lakukan check-in untuk mencatat kehadiran Anda. Pastikan Anda check-in sebelum <strong>07:15</strong> untuk status hadir tepat waktu.</p>

                                <div class="flex items-center justify-center gap-4 mb-6 text-sm">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <span class="text-gray-600">
                                            < 07:15=Hadir</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                        <span class="text-gray-600">07:15 - 10:00 = Terlambat</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                        <span class="text-gray-600">> 10:00 = Alpha</span>
                                    </div>
                                </div>

                                <button type="button" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 duration-200" id="btnCheckIn">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Check-In Sekarang
                                </button>

                                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg inline-block">
                                    <p class="text-sm text-yellow-800">
                                        <i class="fas fa-lightbulb mr-2"></i>
                                        <strong>Tips:</strong> Pastikan kamera dan lokasi aktif untuk proses check-in
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-4">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-filter mr-2"></i>Filter Data
                        </h3>
                    </div>
                    <div class="p-6">
                        <form method="GET" action="<?= base_url('guru/absensi-guru/history') ?>">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                                    <select name="bulan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?= $i ?>" <?= ($filters['bulan'] ?? '') == $i ? 'selected' : '' ?>>
                                                <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                                    <select name="tahun" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <?php
                                        $currentYear = date('Y');
                                        for ($y = $currentYear; $y >= $currentYear - 5; $y--):
                                        ?>
                                            <option value="<?= $y ?>" <?= ($filters['tahun'] ?? '') == $y ? 'selected' : '' ?>><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="">Semua Status</option>
                                        <option value="hadir" <?= ($filters['status'] ?? '') == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                        <option value="terlambat" <?= ($filters['status'] ?? '') == 'terlambat' ? 'selected' : '' ?>>Terlambat</option>
                                        <option value="izin" <?= ($filters['status'] ?? '') == 'izin' ? 'selected' : '' ?>>Izin</option>
                                        <option value="sakit" <?= ($filters['status'] ?? '') == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                                        <option value="alpha" <?= ($filters['status'] ?? '') == 'alpha' ? 'selected' : '' ?>>Alpha</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                                    <div class="flex gap-2">
                                        <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all font-medium shadow-md hover:shadow-lg inline-flex items-center justify-center gap-2">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="<?= base_url('guru/absensi-guru/history') ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors inline-flex items-center justify-center">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- History Table -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-6 py-4">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-list mr-2"></i>Daftar Absensi
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php if (empty($absensiList)): ?>
                            <div class="text-center py-12">
                                <div class="bg-gray-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-inbox text-gray-400 text-5xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Data</h3>
                                <p class="text-gray-500">Belum ada riwayat absensi untuk periode yang dipilih</p>
                            </div>
                        <?php else: ?>
                            <!-- Desktop Table View -->
                            <div class="hidden md:block overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam Masuk</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam Keluar</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Durasi</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <?php
                                        $no = 1;
                                        foreach ($absensiList as $absensi):
                                            // Calculate duration
                                            $durasi = '-';
                                            if ($absensi['check_in'] && $absensi['check_out']) {
                                                $masuk = strtotime($absensi['check_in']);
                                                $keluar = strtotime($absensi['check_out']);
                                                $diff = $keluar - $masuk;
                                                $hours = floor($diff / 3600);
                                                $minutes = floor(($diff % 3600) / 60);
                                                $durasi = sprintf('%d jam %d menit', $hours, $minutes);
                                            }

                                            $badgeConfig = [
                                                'hadir' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                                'terlambat' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                                'izin' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                                                'sakit' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
                                                'alpha' => ['bg' => 'bg-red-100', 'text' => 'text-red-700']
                                            ];
                                            $config = $badgeConfig[$absensi['status']] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'];
                                        ?>
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-4 text-sm text-gray-900 font-medium"><?= $no++ ?></td>
                                                <td class="px-4 py-4">
                                                    <p class="text-sm font-semibold text-gray-900"><?= date('d F Y', strtotime($absensi['tanggal'])) ?></p>
                                                    <p class="text-xs text-gray-500"><?= date('l', strtotime($absensi['tanggal'])) ?></p>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <?php if ($absensi['check_in']): ?>
                                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                                                            <?= date('H:i', strtotime($absensi['check_in'])) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-sm">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <?php if ($absensi['check_out']): ?>
                                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                                            <?= date('H:i', strtotime($absensi['check_out'])) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm">Belum</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-4 py-4 text-center text-sm text-gray-600"><?= $durasi ?></td>
                                                <td class="px-4 py-4 text-center">
                                                    <span class="px-3 py-1 <?= $config['bg'] ?> <?= $config['text'] ?> rounded-full text-sm font-semibold">
                                                        <?= ucfirst($absensi['status']) ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <a href="<?= base_url('guru/absensi-guru/show/' . $absensi['id']) ?>"
                                                        class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors text-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="md:hidden space-y-4">
                                <?php
                                $no = 1;
                                foreach ($absensiList as $absensi):
                                    $durasi = '-';
                                    if ($absensi['check_in'] && $absensi['check_out']) {
                                        $masuk = strtotime($absensi['check_in']);
                                        $keluar = strtotime($absensi['check_out']);
                                        $diff = $keluar - $masuk;
                                        $hours = floor($diff / 3600);
                                        $minutes = floor(($diff % 3600) / 60);
                                        $durasi = sprintf('%d jam %d menit', $hours, $minutes);
                                    }

                                    $badgeConfig = [
                                        'hadir' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'fa-check-circle'],
                                        'terlambat' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-exclamation-triangle'],
                                        'izin' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-file-alt'],
                                        'sakit' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' => 'fa-medkit'],
                                        'alpha' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-times-circle']
                                    ];
                                    $config = $badgeConfig[$absensi['status']] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-circle'];
                                ?>
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                        <div class="flex items-start justify-between mb-3">
                                            <div>
                                                <p class="font-semibold text-gray-900"><?= date('d F Y', strtotime($absensi['tanggal'])) ?></p>
                                                <p class="text-xs text-gray-500"><?= date('l', strtotime($absensi['tanggal'])) ?></p>
                                            </div>
                                            <span class="px-3 py-1 <?= $config['bg'] ?> <?= $config['text'] ?> rounded-full text-sm font-semibold">
                                                <i class="fas <?= $config['icon'] ?> mr-1"></i><?= ucfirst($absensi['status']) ?>
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3 mb-3">
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Check-In</p>
                                                <p class="font-semibold text-gray-900"><?= $absensi['check_in'] ? date('H:i', strtotime($absensi['check_in'])) : '-' ?></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 mb-1">Check-Out</p>
                                                <p class="font-semibold text-gray-900"><?= $absensi['check_out'] ? date('H:i', strtotime($absensi['check_out'])) : 'Belum' ?></p>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                            <p class="text-sm text-gray-600"><i class="fas fa-clock mr-1"></i><?= $durasi ?></p>
                                            <a href="<?= base_url('guru/absensi-guru/show/' . $absensi['id']) ?>"
                                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors text-sm font-medium">
                                                <i class="fas fa-eye mr-1"></i>Detail
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($pager): ?>
                                <div class="mt-6">
                                    <?= $pager->links() ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Statistics & History -->
            <div class="space-y-6">
                <!-- Recent History -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-history mr-2"></i>Riwayat
                            </h3>
                            <button type="button" onclick="switchTab('history')" class="px-3 py-1 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg transition-all text-xs font-medium">
                                Semua <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <?php if (empty($recentHistory)): ?>
                            <div class="text-center py-6">
                                <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 text-sm">Belum ada riwayat</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-2">
                                <?php foreach ($recentHistory as $history): ?>
                                    <?php
                                    $badgeConfig = [
                                        'hadir' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'fa-check-circle'],
                                        'terlambat' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-exclamation-triangle'],
                                        'izin' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-file-alt'],
                                        'sakit' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' => 'fa-medkit'],
                                        'alpha' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-times-circle']
                                    ];
                                    $config = $badgeConfig[$history['status']] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-circle'];
                                    ?>
                                    <div class="p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="font-semibold text-gray-800 text-sm"><?= date('d M', strtotime($history['tanggal'])) ?></p>
                                            <span class="px-2 py-1 <?= $config['bg'] ?> <?= $config['text'] ?> rounded-full text-xs font-semibold">
                                                <i class="fas <?= $config['icon'] ?> mr-1"></i><?= ucfirst($history['status']) ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs">
                                            <div class="flex items-center gap-1">
                                                <i class="fas fa-sign-in-alt text-green-600"></i>
                                                <span class="text-gray-700"><?= $history['check_in'] ? date('H:i', strtotime($history['check_in'])) : '-' ?></span>
                                            </div>
                                            <span class="text-gray-400">|</span>
                                            <div class="flex items-center gap-1">
                                                <i class="fas fa-sign-out-alt text-blue-600"></i>
                                                <span class="text-gray-700"><?= $history['check_out'] ? date('H:i', strtotime($history['check_out'])) : '-' ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Monthly Stats -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-6 py-4">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-chart-pie mr-2"></i>Statistik Bulan Ini
                        </h3>
                        <p class="text-sm opacity-90 mt-1"><?= date('F Y') ?></p>
                    </div>
                    <div class="p-6">
                        <?php
                        $total = ($monthlyStats['total_hadir'] ?? 0) + ($monthlyStats['total_terlambat'] ?? 0) +
                            ($monthlyStats['total_izin'] ?? 0) + ($monthlyStats['total_sakit'] ?? 0);
                        $total = $total > 0 ? $total : 1; // Prevent division by zero
                        ?>

                        <!-- Hadir -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <span class="text-gray-700 font-medium">Hadir</span>
                                </div>
                                <span class="text-2xl font-bold text-green-600"><?= $monthlyStats['total_hadir'] ?? 0 ?></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-green-400 to-green-600 h-2.5 rounded-full transition-all duration-500"
                                    style="width: <?= round((($monthlyStats['total_hadir'] ?? 0) / $total) * 100) ?>%"></div>
                            </div>
                        </div>

                        <!-- Terlambat -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                    <span class="text-gray-700 font-medium">Terlambat</span>
                                </div>
                                <span class="text-2xl font-bold text-yellow-600"><?= $monthlyStats['total_terlambat'] ?? 0 ?></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 h-2.5 rounded-full transition-all duration-500"
                                    style="width: <?= round((($monthlyStats['total_terlambat'] ?? 0) / $total) * 100) ?>%"></div>
                            </div>
                        </div>

                        <!-- Izin -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    <span class="text-gray-700 font-medium">Izin</span>
                                </div>
                                <span class="text-2xl font-bold text-blue-600"><?= $monthlyStats['total_izin'] ?? 0 ?></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2.5 rounded-full transition-all duration-500"
                                    style="width: <?= round((($monthlyStats['total_izin'] ?? 0) / $total) * 100) ?>%"></div>
                            </div>
                        </div>

                        <!-- Sakit -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                    <span class="text-gray-700 font-medium">Sakit</span>
                                </div>
                                <span class="text-2xl font-bold text-purple-600"><?= $monthlyStats['total_sakit'] ?? 0 ?></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-purple-400 to-purple-600 h-2.5 rounded-full transition-all duration-500"
                                    style="width: <?= round((($monthlyStats['total_sakit'] ?? 0) / $total) * 100) ?>%"></div>
                            </div>
                        </div>

                        <!-- Total Summary -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 font-medium">Total Kehadiran</span>
                                <span class="text-3xl font-bold text-gray-800"><?= $total ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white px-6 py-4">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>Informasi Penting
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="bg-blue-100 p-2 rounded-lg">
                                    <i class="fas fa-clock text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Waktu Check-In</p>
                                    <p class="text-sm text-gray-600">06:00 - 10:00 WITA</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="bg-green-100 p-2 rounded-lg">
                                    <i class="fas fa-check text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Batas Tepat Waktu</p>
                                    <p class="text-sm text-gray-600">Sebelum 07:15 WITA</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="bg-yellow-100 p-2 rounded-lg">
                                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Batas Terlambat</p>
                                    <p class="text-sm text-gray-600">07:15 - 10:00 WITA</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="bg-purple-100 p-2 rounded-lg">
                                    <i class="fas fa-hourglass-half text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Jam Kerja Minimum</p>
                                    <p class="text-sm text-gray-600">8 jam (480 menit)</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-lg">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-lightbulb text-amber-600 mt-1"></i>
                                <div>
                                    <p class="font-semibold text-amber-800 mb-1">Tips Kehadiran</p>
                                    <p class="text-sm text-amber-700">
                                        Check-in sebelum 07:15 untuk status hadir tepat waktu.
                                        Pastikan GPS dan kamera aktif untuk proses absensi.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Dashboard Tab -->

    <!-- History Tab -->
    <div class="tab-content hidden" id="history-tab">
        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-4">
                <h3 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-filter mr-2"></i>Filter Data
                </h3>
            </div>
            <div class="p-6">
                <form method="GET" action="<?= base_url('guru/absensi-guru') ?>" id="historyFilterForm">
                    <input type="hidden" name="tab" value="history">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                            <select name="bulan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= $i ?>" <?= ($filters['bulan'] ?? '') == $i ? 'selected' : '' ?>>
                                        <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                            <select name="tahun" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <?php
                                $currentYear = date('Y');
                                for ($y = $currentYear; $y >= $currentYear - 5; $y--):
                                ?>
                                    <option value="<?= $y ?>" <?= ($filters['tahun'] ?? '') == $y ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Semua Status</option>
                                <option value="hadir" <?= ($filters['status'] ?? '') == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                <option value="terlambat" <?= ($filters['status'] ?? '') == 'terlambat' ? 'selected' : '' ?>>Terlambat</option>
                                <option value="izin" <?= ($filters['status'] ?? '') == 'izin' ? 'selected' : '' ?>>Izin</option>
                                <option value="sakit" <?= ($filters['status'] ?? '') == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                                <option value="alpha" <?= ($filters['status'] ?? '') == 'alpha' ? 'selected' : '' ?>>Alpha</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all font-medium shadow-md hover:shadow-lg inline-flex items-center justify-center gap-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="<?= base_url('guru/absensi-guru?tab=history') ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors inline-flex items-center justify-center">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- History List -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-6 py-4">
                <h3 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-list mr-2"></i>Daftar Absensi
                </h3>
            </div>
            <div class="p-6">
                <?php if (empty($absensiList)): ?>
                    <div class="text-center py-12">
                        <div class="bg-gray-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-gray-400 text-5xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Data</h3>
                        <p class="text-gray-500">Belum ada riwayat absensi untuk periode yang dipilih</p>
                    </div>
                <?php else: ?>
                    <!-- Desktop Table View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b-2 border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam Masuk</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam Keluar</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Durasi</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php
                                $no = 1;
                                foreach ($absensiList as $absensi):
                                    $durasi = '-';
                                    if ($absensi['check_in'] && $absensi['check_out']) {
                                        $masuk = strtotime($absensi['check_in']);
                                        $keluar = strtotime($absensi['check_out']);
                                        $diff = $keluar - $masuk;
                                        $hours = floor($diff / 3600);
                                        $minutes = floor(($diff % 3600) / 60);
                                        $durasi = sprintf('%d jam %d menit', $hours, $minutes);
                                    }

                                    $badgeConfig = [
                                        'hadir' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                        'terlambat' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                        'izin' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                                        'sakit' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
                                        'alpha' => ['bg' => 'bg-red-100', 'text' => 'text-red-700']
                                    ];
                                    $config = $badgeConfig[$absensi['status']] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'];
                                ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4 text-sm text-gray-900 font-medium"><?= $no++ ?></td>
                                        <td class="px-4 py-4">
                                            <p class="text-sm font-semibold text-gray-900"><?= date('d F Y', strtotime($absensi['tanggal'])) ?></p>
                                            <p class="text-xs text-gray-500"><?= date('l', strtotime($absensi['tanggal'])) ?></p>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <?php if ($absensi['check_in']): ?>
                                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                                                    <?= date('H:i', strtotime($absensi['check_in'])) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-sm">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <?php if ($absensi['check_out']): ?>
                                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                                    <?= date('H:i', strtotime($absensi['check_out'])) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm">Belum</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4 text-center text-sm text-gray-600"><?= $durasi ?></td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="px-3 py-1 <?= $config['bg'] ?> <?= $config['text'] ?> rounded-full text-sm font-semibold">
                                                <?= ucfirst($absensi['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <a href="<?= base_url('guru/absensi-guru/show/' . $absensi['id']) ?>"
                                                class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors text-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="md:hidden space-y-4">
                        <?php
                        $no = 1;
                        foreach ($absensiList as $absensi):
                            $durasi = '-';
                            if ($absensi['check_in'] && $absensi['check_out']) {
                                $masuk = strtotime($absensi['check_in']);
                                $keluar = strtotime($absensi['check_out']);
                                $diff = $keluar - $masuk;
                                $hours = floor($diff / 3600);
                                $minutes = floor(($diff % 3600) / 60);
                                $durasi = sprintf('%d jam %d menit', $hours, $minutes);
                            }

                            $badgeConfig = [
                                'hadir' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'fa-check-circle'],
                                'terlambat' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-exclamation-triangle'],
                                'izin' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-file-alt'],
                                'sakit' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' => 'fa-medkit'],
                                'alpha' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-times-circle']
                            ];
                            $config = $badgeConfig[$absensi['status']] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-circle'];
                        ?>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <p class="font-semibold text-gray-900"><?= date('d F Y', strtotime($absensi['tanggal'])) ?></p>
                                        <p class="text-xs text-gray-500"><?= date('l', strtotime($absensi['tanggal'])) ?></p>
                                    </div>
                                    <span class="px-3 py-1 <?= $config['bg'] ?> <?= $config['text'] ?> rounded-full text-sm font-semibold">
                                        <i class="fas <?= $config['icon'] ?> mr-1"></i><?= ucfirst($absensi['status']) ?>
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Check-In</p>
                                        <p class="font-semibold text-gray-900"><?= $absensi['check_in'] ? date('H:i', strtotime($absensi['check_in'])) : '-' ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Check-Out</p>
                                        <p class="font-semibold text-gray-900"><?= $absensi['check_out'] ? date('H:i', strtotime($absensi['check_out'])) : 'Belum' ?></p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                    <p class="text-sm text-gray-600"><i class="fas fa-clock mr-1"></i><?= $durasi ?></p>
                                    <a href="<?= base_url('guru/absensi-guru/show/' . $absensi['id']) ?>"
                                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors text-sm font-medium">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($pager): ?>
                        <div class="mt-6">
                            <?= $pager->links() ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- End History Tab -->

    <!-- Statistics Tab -->
    <div class="tab-content hidden" id="statistics-tab">
        <!-- Period Info -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Statistik Kehadiran</h2>
                    <p class="text-blue-100">Periode: <?= date('F Y', mktime(0, 0, 0, $filters['bulan'], 1, $filters['tahun'])) ?></p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                    <i class="fas fa-chart-line text-5xl"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
            <!-- Hadir -->
            <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-green-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wide mb-1">Hadir</p>
                        <p class="text-3xl font-bold text-gray-800"><?= $monthlyStats['total_hadir'] ?? 0 ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Terlambat -->
            <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-yellow-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wide mb-1">Terlambat</p>
                        <p class="text-3xl font-bold text-gray-800"><?= $monthlyStats['total_terlambat'] ?? 0 ?></p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Izin -->
            <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-blue-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-1">Izin</p>
                        <p class="text-3xl font-bold text-gray-800"><?= $monthlyStats['total_izin'] ?? 0 ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Sakit -->
            <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-purple-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-purple-600 uppercase tracking-wide mb-1">Sakit</p>
                        <p class="text-3xl font-bold text-gray-800"><?= $monthlyStats['total_sakit'] ?? 0 ?></p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-medkit text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Alpha -->
            <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-red-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-red-600 uppercase tracking-wide mb-1">Alpha</p>
                        <p class="text-3xl font-bold text-gray-800"><?= $monthlyStats['total_alpha'] ?? 0 ?></p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-lg">
                        <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <?php
        $total = ($monthlyStats['total_hadir'] ?? 0) + ($monthlyStats['total_terlambat'] ?? 0) +
                 ($monthlyStats['total_izin'] ?? 0) + ($monthlyStats['total_sakit'] ?? 0) + ($monthlyStats['total_alpha'] ?? 0);
        ?>

        <?php if ($total > 0): ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Pie Chart -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-pie text-blue-600 mr-2"></i>Distribusi Kehadiran
                    </h3>
                    <div class="relative" style="height: 300px;">
                        <canvas id="attendancePieChart"></canvas>
                    </div>
                </div>

                <!-- Doughnut Chart -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-donut text-purple-600 mr-2"></i>Persentase Status
                    </h3>
                    <div class="relative" style="height: 300px;">
                        <canvas id="attendanceDoughnutChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Bar Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-green-600 mr-2"></i>Perbandingan Status Kehadiran
                </h3>
                <div class="relative" style="height: 350px;">
                    <canvas id="attendanceBarChart"></canvas>
                </div>
            </div>

            <!-- Progress Bars with Details -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Detail Statistik</h3>
                <div class="space-y-4">
                    <?php
                    $stats = [
                        ['label' => 'Hadir', 'value' => $monthlyStats['total_hadir'] ?? 0, 'color' => 'green', 'icon' => 'fa-check-circle'],
                        ['label' => 'Terlambat', 'value' => $monthlyStats['total_terlambat'] ?? 0, 'color' => 'yellow', 'icon' => 'fa-exclamation-triangle'],
                        ['label' => 'Izin', 'value' => $monthlyStats['total_izin'] ?? 0, 'color' => 'blue', 'icon' => 'fa-file-alt'],
                        ['label' => 'Sakit', 'value' => $monthlyStats['total_sakit'] ?? 0, 'color' => 'purple', 'icon' => 'fa-medkit'],
                        ['label' => 'Alpha', 'value' => $monthlyStats['total_alpha'] ?? 0, 'color' => 'red', 'icon' => 'fa-times-circle'],
                    ];

                    foreach ($stats as $stat):
                        $percentage = round(($stat['value'] / $total) * 100, 1);
                    ?>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas <?= $stat['icon'] ?> text-<?= $stat['color'] ?>-600"></i>
                                    <span class="text-sm font-medium text-gray-700"><?= $stat['label'] ?></span>
                                </div>
                                <span class="text-sm font-bold text-<?= $stat['color'] ?>-600"><?= $stat['value'] ?> hari (<?= $percentage ?>%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-<?= $stat['color'] ?>-400 to-<?= $stat['color'] ?>-600 h-3 rounded-full transition-all duration-500"
                                     style="width: <?= $percentage ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-600 font-medium mb-1">Total Hari Kerja</p>
                            <p class="text-4xl font-bold text-blue-800"><?= $total ?></p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <p class="text-sm text-green-600 font-medium mb-1">Tingkat Kehadiran</p>
                            <p class="text-4xl font-bold text-green-800"><?= round((($monthlyStats['total_hadir'] ?? 0) / $total) * 100, 1) ?>%</p>
                        </div>
                        <div class="text-center p-4 bg-orange-50 rounded-lg">
                            <p class="text-sm text-orange-600 font-medium mb-1">Keterlambatan</p>
                            <p class="text-4xl font-bold text-orange-800"><?= round((($monthlyStats['total_terlambat'] ?? 0) / $total) * 100, 1) ?>%</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-lg p-12">
                <div class="text-center">
                    <i class="fas fa-chart-bar text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-2xl font-semibold text-gray-700 mb-2">Belum Ada Data</h3>
                    <p class="text-gray-500">Belum ada data statistik untuk periode ini</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <!-- End Statistics Tab -->
</div>

<script>
    // Tab Switching Logic
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    function switchTab(tabName) {
        // Update buttons
        tabButtons.forEach(btn => {
            const isActive = btn.dataset.tab === tabName;
            btn.classList.toggle('border-blue-500', isActive);
            btn.classList.toggle('text-blue-600', isActive);
            btn.classList.toggle('border-transparent', !isActive);
            btn.classList.toggle('text-gray-500', !isActive);
            btn.classList.toggle('hover:text-gray-700', !isActive);
            btn.classList.toggle('hover:border-gray-300', !isActive);
        });

        // Update content
        tabContents.forEach(content => {
            content.classList.toggle('hidden', content.id !== `${tabName}-tab`);
        });

        // Update URL without reload
        const newUrl = new URL(window.location);
        newUrl.searchParams.set('tab', tabName);
        window.history.pushState({}, '', newUrl);

        // Scroll to top when switching tabs
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Get tab from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'dashboard';

        // Set initial tab
        switchTab(activeTab);

        // Add click handlers
        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => switchTab(btn.dataset.tab));
        });
    });
</script>

<!-- Check-In Modal -->
<div class="modal fade hidden" id="checkInModal" tabindex="-1" role="dialog" data-modal-overlay="checkInModal">
    <div class="modal-dialog">
        <div class="modal-content bg-white rounded-2xl shadow-2xl overflow-hidden max-w-2xl w-full mx-4" onclick="event.stopPropagation()">
            <!-- Enhanced Header with Gradient -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="bg-white bg-opacity-20 backdrop-blur-sm p-3 rounded-xl">
                            <i class="fas fa-sign-in-alt text-2xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-1">Check-In Kehadiran</h3>
                            <p class="text-blue-100 text-sm">Catat kehadiran Anda hari ini</p>
                        </div>
                    </div>
                    <button type="button" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-all" onclick="closeModal('checkInModal'); cameraCheckIn.reset();" aria-label="Close">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <form id="checkInForm" enctype="multipart/form-data">
                <div class="p-6 bg-gradient-to-b from-slate-50 to-white max-h-[70vh] overflow-y-auto">
                    <!-- Info Banner -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-blue-900 mb-2">Tips Check-In:</h4>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check text-blue-600 text-xs mt-1"></i>
                                        <span>Pastikan wajah terlihat jelas pada foto</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check text-blue-600 text-xs mt-1"></i>
                                        <span>Check-in sebelum <strong>07:15</strong> untuk status tepat waktu</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check text-blue-600 text-xs mt-1"></i>
                                        <span>Aktifkan lokasi GPS untuk validasi kehadiran</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Camera Section -->
                    <div class="mb-6">
                        <label class="flex items-center gap-2 font-semibold text-gray-800 mb-4">
                            <i class="fas fa-camera text-blue-600 text-lg"></i>
                            <span>Foto Selfie</span>
                            <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">Wajib</span>
                        </label>

                        <!-- Camera/Upload Toggle with Tailwind -->
                        <div class="flex rounded-xl overflow-hidden shadow-sm border-2 border-gray-200 mb-4">
                            <input type="radio" class="hidden" name="photoMethodCheckIn" id="cameraCheckIn" checked>
                            <label class="flex-1 py-3 px-4 text-center cursor-pointer transition-all font-medium bg-white hover:bg-blue-50 border-r border-gray-200 photo-method-label" for="cameraCheckIn" data-active="bg-blue-600 text-white">
                                <i class="fas fa-camera mr-2"></i>
                                <span class="hidden sm:inline">Ambil </span>Foto
                            </label>

                            <input type="radio" class="hidden" name="photoMethodCheckIn" id="uploadCheckIn">
                            <label class="flex-1 py-3 px-4 text-center cursor-pointer transition-all font-medium bg-white hover:bg-blue-50 photo-method-label" for="uploadCheckIn" data-active="bg-blue-600 text-white">
                                <i class="fas fa-upload mr-2"></i>
                                Upload<span class="hidden sm:inline"> File</span>
                            </label>
                        </div>

                        <!-- Camera Interface with Tailwind -->
                        <div id="cameraContainerCheckIn" class="camera-container">
                            <div class="relative bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl overflow-hidden shadow-xl" style="min-height: 320px;">
                                <video id="videoCheckIn" class="w-full rounded-2xl" autoplay playsinline style="display: none;"></video>
                                <canvas id="canvasCheckIn" class="w-full rounded-2xl" style="display: none;"></canvas>
                                <div id="cameraPlaceholderCheckIn" class="flex items-center justify-center h-full absolute inset-0">
                                    <div class="text-center p-6">
                                        <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-camera text-white text-4xl"></i>
                                        </div>
                                        <p class="text-white font-semibold text-lg mb-2">Siap Mengambil Foto?</p>
                                        <p class="text-gray-300 text-sm">Klik tombol di bawah untuk mengaktifkan kamera</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <button type="button" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5" id="startCameraCheckIn">
                                    <i class="fas fa-video mr-2"></i>
                                    <span class="hidden sm:inline">Aktifkan </span>Kamera
                                </button>
                                <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5" id="captureCheckIn" style="display: none;">
                                    <i class="fas fa-camera-retro mr-2"></i>
                                    Ambil Foto
                                </button>
                                <button type="button" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5" id="retakeCheckIn" style="display: none;">
                                    <i class="fas fa-redo mr-2"></i>
                                    Ulangi
                                </button>
                            </div>
                        </div>

                        <!-- File Upload Interface with Tailwind -->
                        <div id="uploadContainerCheckIn" class="upload-container" style="display: none;">
                            <div class="border-2 border-dashed border-blue-300 rounded-2xl p-8 bg-gradient-to-br from-blue-50 to-indigo-50 hover:border-blue-400 hover:bg-blue-100 transition-all cursor-pointer">
                                <input type="file" class="hidden" id="fotoCheckIn" name="foto" accept="image/*" capture="user">
                                <label for="fotoCheckIn" class="cursor-pointer">
                                    <div class="text-center">
                                        <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-cloud-upload-alt text-blue-600 text-3xl"></i>
                                        </div>
                                        <p class="text-base text-gray-700 font-semibold mb-1">
                                            Upload foto selfie untuk check-in
                                        </p>
                                        <p class="text-sm text-gray-500">Format: JPG, PNG (Max 1MB)</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <input type="hidden" name="foto_base64" id="fotoBase64CheckIn">
                    </div>

                    <!-- Notes Section with Tailwind -->
                    <div class="mb-6">
                        <label class="flex items-center gap-2 font-semibold text-gray-800 mb-3">
                            <i class="fas fa-sticky-note text-gray-500 text-lg"></i>
                            <span>Keterangan</span>
                            <span class="text-xs text-gray-500 font-normal">(Opsional)</span>
                        </label>
                        <textarea 
                            class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all resize-none" 
                            name="keterangan_masuk" 
                            rows="3" 
                            placeholder="Tambahkan catatan jika diperlukan (misal: kondisi kesehatan, informasi tambahan)"></textarea>
                        <p class="text-xs text-gray-500 mt-2 flex items-start gap-2">
                            <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                            <span>Contoh: "Merasa kurang sehat" atau "Terlambat karena macet"</span>
                        </p>
                    </div>
                    
                    <!-- Hidden GPS Fields -->
                    <input type="hidden" name="latitude" id="latitudeCheckIn">
                    <input type="hidden" name="longitude" id="longitudeCheckIn">
                </div>
                
                <!-- Footer with Tailwind -->
                <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row gap-3 justify-end border-t border-gray-200">
                    <button 
                        type="button" 
                        class="w-full sm:w-auto px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-all shadow-sm hover:shadow" 
                        onclick="closeModal('checkInModal'); cameraCheckIn.reset();">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5" 
                        id="btnSubmitCheckIn">
                        <i class="fas fa-check-circle mr-2"></i>Submit Check-In
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Check-Out Modal -->
<div class="modal fade hidden" id="checkOutModal" tabindex="-1" role="dialog" data-modal-overlay="checkOutModal">
    <div class="modal-dialog">
        <div class="modal-content bg-white rounded-2xl shadow-2xl overflow-hidden max-w-2xl w-full mx-4" onclick="event.stopPropagation()">
            <!-- Enhanced Header with Gradient -->
            <div class="bg-gradient-to-r from-teal-600 to-cyan-600 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="bg-white bg-opacity-20 backdrop-blur-sm p-3 rounded-xl">
                            <i class="fas fa-sign-out-alt text-2xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white mb-1">Check-Out Kehadiran</h3>
                            <p class="text-teal-100 text-sm">Selesaikan kehadiran Anda hari ini</p>
                        </div>
                    </div>
                    <button type="button" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-all" onclick="closeModal('checkOutModal'); cameraCheckOut.reset();" aria-label="Close">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <form id="checkOutForm" enctype="multipart/form-data">
                <div class="p-6 bg-gradient-to-b from-teal-50 to-white max-h-[70vh] overflow-y-auto">
                    <!-- Info Banner -->
                    <div class="bg-gradient-to-r from-teal-50 to-cyan-50 border-l-4 border-teal-500 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-teal-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-teal-900 mb-2">Tips Check-Out:</h4>
                                <ul class="text-sm text-teal-800 space-y-1">
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check text-teal-600 text-xs mt-1"></i>
                                        <span>Pastikan semua pekerjaan sudah selesai</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check text-teal-600 text-xs mt-1"></i>
                                        <span>Foto dan keterangan bersifat opsional</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check text-teal-600 text-xs mt-1"></i>
                                        <span>Validasi lokasi GPS akan tetap dilakukan</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Camera Section -->
                    <div class="mb-6">
                        <label class="flex items-center gap-2 font-semibold text-gray-800 mb-4">
                            <i class="fas fa-camera text-teal-600 text-lg"></i>
                            <span>Foto Selfie</span>
                            <span class="bg-gray-400 text-white text-xs font-bold px-2 py-1 rounded-full">Opsional</span>
                        </label>

                        <!-- Camera/Upload Toggle with Tailwind -->
                        <div class="flex rounded-xl overflow-hidden shadow-sm border-2 border-gray-200 mb-4">
                            <input type="radio" class="hidden" name="photoMethodCheckOut" id="cameraCheckOut" checked>
                            <label class="flex-1 py-3 px-4 text-center cursor-pointer transition-all font-medium bg-white hover:bg-teal-50 border-r border-gray-200 photo-method-label" for="cameraCheckOut" data-active="bg-teal-600 text-white">
                                <i class="fas fa-camera mr-2"></i>
                                <span class="hidden sm:inline">Ambil </span>Foto
                            </label>

                            <input type="radio" class="hidden" name="photoMethodCheckOut" id="uploadCheckOut">
                            <label class="flex-1 py-3 px-4 text-center cursor-pointer transition-all font-medium bg-white hover:bg-teal-50 photo-method-label" for="uploadCheckOut" data-active="bg-teal-600 text-white">
                                <i class="fas fa-upload mr-2"></i>
                                Upload<span class="hidden sm:inline"> File</span>
                            </label>
                        </div>

                        <!-- Camera Interface with Tailwind -->
                        <div id="cameraContainerCheckOut" class="camera-container">
                            <div class="relative bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl overflow-hidden shadow-xl" style="min-height: 320px;">
                                <video id="videoCheckOut" class="w-full rounded-2xl" autoplay playsinline style="display: none;"></video>
                                <canvas id="canvasCheckOut" class="w-full rounded-2xl" style="display: none;"></canvas>
                                <div id="cameraPlaceholderCheckOut" class="flex items-center justify-center h-full absolute inset-0">
                                    <div class="text-center p-6">
                                        <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-camera text-white text-4xl"></i>
                                        </div>
                                        <p class="text-white font-semibold text-lg mb-2">Siap Mengambil Foto?</p>
                                        <p class="text-gray-300 text-sm">Klik tombol di bawah untuk mengaktifkan kamera</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <button type="button" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5" id="startCameraCheckOut">
                                    <i class="fas fa-video mr-2"></i>
                                    <span class="hidden sm:inline">Aktifkan </span>Kamera
                                </button>
                                <button type="button" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5" id="captureCheckOut" style="display: none;">
                                    <i class="fas fa-camera-retro mr-2"></i>
                                    Ambil Foto
                                </button>
                                <button type="button" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5" id="retakeCheckOut" style="display: none;">
                                    <i class="fas fa-redo mr-2"></i>
                                    Ulangi
                                </button>
                            </div>
                        </div>

                        <!-- File Upload Interface with Tailwind -->
                        <div id="uploadContainerCheckOut" class="upload-container" style="display: none;">
                            <div class="border-2 border-dashed border-teal-300 rounded-2xl p-8 bg-gradient-to-br from-teal-50 to-cyan-50 hover:border-teal-400 hover:bg-teal-100 transition-all cursor-pointer">
                                <input type="file" class="hidden" id="fotoCheckOut" name="foto" accept="image/*" capture="user">
                                <label for="fotoCheckOut" class="cursor-pointer">
                                    <div class="text-center">
                                        <div class="bg-teal-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-cloud-upload-alt text-teal-600 text-3xl"></i>
                                        </div>
                                        <p class="text-base text-gray-700 font-semibold mb-1">
                                            Upload foto untuk check-out (opsional)
                                        </p>
                                        <p class="text-sm text-gray-500">Format: JPG, PNG (Max 1MB)</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <input type="hidden" name="foto_base64" id="fotoBase64CheckOut">
                    </div>

                    <!-- Notes Section with Tailwind -->
                    <div class="mb-6">
                        <label class="flex items-center gap-2 font-semibold text-gray-800 mb-3">
                            <i class="fas fa-sticky-note text-gray-500 text-lg"></i>
                            <span>Keterangan</span>
                            <span class="text-xs text-gray-500 font-normal">(Opsional)</span>
                        </label>
                        <textarea 
                            class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-teal-200 focus:border-teal-500 transition-all resize-none" 
                            name="keterangan_keluar" 
                            rows="3" 
                            placeholder="Tambahkan catatan jika diperlukan (misal: ringkasan pekerjaan hari ini)"></textarea>
                        <p class="text-xs text-gray-500 mt-2 flex items-start gap-2">
                            <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                            <span>Contoh: "Selesai mengajar 6 jam pelajaran" atau "Mengikuti rapat koordinasi"</span>
                        </p>
                    </div>
                    
                    <!-- Hidden GPS Fields -->
                    <input type="hidden" name="latitude" id="latitudeCheckOut">
                    <input type="hidden" name="longitude" id="longitudeCheckOut">
                </div>
                
                <!-- Footer with Tailwind -->
                <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row gap-3 justify-end border-t border-gray-200">
                    <button 
                        type="button" 
                        class="w-full sm:w-auto px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-all shadow-sm hover:shadow" 
                        onclick="closeModal('checkOutModal'); cameraCheckOut.reset();">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5" 
                        id="btnSubmitCheckOut">
                        <i class="fas fa-check-circle mr-2"></i>Submit Check-Out
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade hidden" id="imageModal" tabindex="-1" role="dialog" data-modal-overlay="imageModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h5 class="modal-title">Foto Absensi</h5>
                <button type="button" class="btn-close" onclick="closeModal('imageModal');"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" class="img-fluid" alt="Foto Absensi">
            </div>
        </div>
    </div>
</div>

<script>
    // Camera handling class
    class CameraHandler {
        constructor(prefix) {
            this.prefix = prefix;
            this.video = document.getElementById(`video${prefix}`);
            this.canvas = document.getElementById(`canvas${prefix}`);
            this.placeholder = document.getElementById(`cameraPlaceholder${prefix}`);
            this.startBtn = document.getElementById(`startCamera${prefix}`);
            this.captureBtn = document.getElementById(`capture${prefix}`);
            this.retakeBtn = document.getElementById(`retake${prefix}`);
            this.base64Input = document.getElementById(`fotoBase64${prefix}`);
            this.stream = null;
            this.photoTaken = false;

            this.initEventListeners();
        }

        initEventListeners() {
            this.startBtn?.addEventListener('click', () => this.startCamera());
            this.captureBtn?.addEventListener('click', () => this.capturePhoto());
            this.retakeBtn?.addEventListener('click', () => this.retakePhoto());
        }

        async startCamera() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: {
                            ideal: 1280
                        },
                        height: {
                            ideal: 720
                        }
                    }
                });

                this.video.srcObject = this.stream;
                this.video.style.display = 'block';
                this.placeholder.style.display = 'none';
                this.startBtn.style.display = 'none';
                this.captureBtn.style.display = 'block';

            } catch (error) {
                console.error('Error accessing camera:', error);
                alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin kamera.');
            }
        }

        capturePhoto() {
            // Set canvas size to match video
            this.canvas.width = this.video.videoWidth;
            this.canvas.height = this.video.videoHeight;

            // Draw video frame to canvas
            const context = this.canvas.getContext('2d');
            context.drawImage(this.video, 0, 0);

            // Get base64 image
            const imageData = this.canvas.toDataURL('image/jpeg', 0.8);
            this.base64Input.value = imageData;

            // Stop camera stream
            this.stopCamera();

            // Show captured image
            this.canvas.style.display = 'block';
            this.video.style.display = 'none';
            this.captureBtn.style.display = 'none';
            this.retakeBtn.style.display = 'block';

            this.photoTaken = true;
        }

        retakePhoto() {
            this.canvas.style.display = 'none';
            this.base64Input.value = '';
            this.photoTaken = false;
            this.startCamera();
        }

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
        }

        reset() {
            this.stopCamera();
            this.video.style.display = 'none';
            this.canvas.style.display = 'none';
            this.placeholder.style.display = 'flex';
            this.startBtn.style.display = 'block';
            this.captureBtn.style.display = 'none';
            this.retakeBtn.style.display = 'none';
            this.base64Input.value = '';
            this.photoTaken = false;
        }

        hasPhoto() {
            return this.photoTaken || this.base64Input.value !== '';
        }
    }

    // Initialize camera handlers
    const cameraCheckIn = new CameraHandler('CheckIn');
    const cameraCheckOut = new CameraHandler('CheckOut');

    // Toggle between camera and file upload
    document.getElementById('cameraCheckIn')?.addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('cameraContainerCheckIn').style.display = 'block';
            document.getElementById('uploadContainerCheckIn').style.display = 'none';
        }
    });

    document.getElementById('uploadCheckIn')?.addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('cameraContainerCheckIn').style.display = 'none';
            document.getElementById('uploadContainerCheckIn').style.display = 'block';
            cameraCheckIn.reset();
        }
    });

    document.getElementById('cameraCheckOut')?.addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('cameraContainerCheckOut').style.display = 'block';
            document.getElementById('uploadContainerCheckOut').style.display = 'none';
        }
    });

    document.getElementById('uploadCheckOut')?.addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('cameraContainerCheckOut').style.display = 'none';
            document.getElementById('uploadContainerCheckOut').style.display = 'block';
            cameraCheckOut.reset();
        }
    });

    // Clean up camera on modal close - handled by close button events
    // No Bootstrap modal events needed

    document.addEventListener('DOMContentLoaded', function() {
        // Modals are now handled by the modal_scripts() helper
        // No need to initialize Bootstrap modals

        // Check-In Button
        const btnCheckIn = document.getElementById('btnCheckIn');
        if (btnCheckIn) {
            btnCheckIn.addEventListener('click', function() {
                getLocation('checkIn');
                openModal('checkInModal');
            });
        }

        // Check-Out Button
        const btnCheckOut = document.getElementById('btnCheckOut');
        if (btnCheckOut) {
            btnCheckOut.addEventListener('click', function() {
                getLocation('checkOut');
                openModal('checkOutModal');
            });
        }

        // Check-In Form Submit
        document.getElementById('checkInForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate photo
            const usingCamera = document.getElementById('cameraCheckIn').checked;
            const usingUpload = document.getElementById('uploadCheckIn').checked;

            if (usingCamera && !cameraCheckIn.hasPhoto()) {
                alert('Silakan ambil foto selfie terlebih dahulu');
                return;
            }

            if (usingUpload && !document.getElementById('fotoCheckIn').files.length) {
                alert('Silakan pilih file foto terlebih dahulu');
                return;
            }

            const formData = new FormData(this);
            submitAbsensi('check-in', formData);
        });

        // Check-Out Form Submit
        document.getElementById('checkOutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            submitAbsensi('check-out', formData);
        });

        // Get Geolocation
        function getLocation(type) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        if (type === 'checkIn') {
                            document.getElementById('latitudeCheckIn').value = position.coords.latitude;
                            document.getElementById('longitudeCheckIn').value = position.coords.longitude;
                        } else {
                            document.getElementById('latitudeCheckOut').value = position.coords.latitude;
                            document.getElementById('longitudeCheckOut').value = position.coords.longitude;
                        }
                    },
                    function(error) {
                        console.log('Geolocation error:', error);
                    }
                );
            }
        }

        // Submit Absensi
        // Get CSRF token from meta tag or cookie
        function getCsrfToken() {
            return '<?= csrf_hash() ?>';
        }

        function submitAbsensi(type, formData) {
            const url = type === 'check-in' ? '<?= base_url('guru/absensi-guru/check-in') ?>' : '<?= base_url('guru/absensi-guru/check-out') ?>';
            const btn = type === 'check-in' ? document.getElementById('btnSubmitCheckIn') : document.getElementById('btnSubmitCheckOut');

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                        btn.disabled = false;
                        btn.innerHTML = type === 'check-in' ? '<i class="fas fa-save"></i> Submit Check-In' : '<i class="fas fa-save"></i> Submit Check-Out';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat submit');
                    btn.disabled = false;
                    btn.innerHTML = type === 'check-in' ? '<i class="fas fa-save"></i> Submit Check-In' : '<i class="fas fa-save"></i> Submit Check-Out';
                });
        }
    });

    // Simple Modal Functions (Original)
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = document.querySelectorAll('.modal:not(.hidden)');
            modals.forEach(modal => {
                closeModal(modal.id);
            });
        }
    });

    // Close modal when clicking on overlay
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });

    // Show Image
    function showImage(url) {
        document.getElementById('previewImage').src = url;
        openModal('imageModal');
    }

    // Toggle button active states for photo method
    document.querySelectorAll('input[name="photoMethodCheckIn"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('label[for="cameraCheckIn"], label[for="uploadCheckIn"]').forEach(label => {
                label.classList.remove('bg-blue-600', 'text-white');
                label.classList.add('bg-white');
            });
            if (this.checked) {
                const label = document.querySelector(`label[for="${this.id}"]`);
                label.classList.remove('bg-white');
                label.classList.add('bg-blue-600', 'text-white');
            }
        });
    });

    document.querySelectorAll('input[name="photoMethodCheckOut"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('label[for="cameraCheckOut"], label[for="uploadCheckOut"]').forEach(label => {
                label.classList.remove('bg-teal-600', 'text-white');
                label.classList.add('bg-white');
            });
            if (this.checked) {
                const label = document.querySelector(`label[for="${this.id}"]`);
                label.classList.remove('bg-white');
                label.classList.add('bg-teal-600', 'text-white');
            }
        });
    });

    // Set initial active state for check-in
    document.addEventListener('DOMContentLoaded', function() {
        const checkInLabel = document.querySelector('label[for="cameraCheckIn"]');
        if (checkInLabel) {
            checkInLabel.classList.add('bg-blue-600', 'text-white');
        }
        const checkOutLabel = document.querySelector('label[for="cameraCheckOut"]');
        if (checkOutLabel) {
            checkOutLabel.classList.add('bg-teal-600', 'text-white');
        }
    });
</script>

<style>
    /* Modal Styles with Tailwind */
    .modal.fade {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(2px);
        z-index: 1050;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow-y: auto;
        padding: 1rem;
    }

    .modal.hidden {
        display: none !important;
    }

    .modal-dialog {
        width: 100%;
        max-width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }

    /* Enhanced button group styles */
    .btn-group .btn-check:checked + label {
        background-color: var(--bs-primary);
        color: white;
        border-color: var(--bs-primary);
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }

    .btn-outline-primary:hover:not(:disabled):not(.disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .btn-outline-info:hover:not(:disabled):not(.disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.4);
    }

    /* Camera container enhancements */
    .camera-container {
        position: relative;
    }

    .camera-container video,
    .camera-container canvas {
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    }

    /* Upload container hover effect */
    .upload-container .border-dashed {
        transition: all 0.3s ease;
    }

    .upload-container .border-dashed:hover {
        border-color: #3b82f6;
        background-color: #eff6ff;
        transform: scale(1.01);
    }

    /* Badge styling */
    .badge {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.375rem;
    }

    /* Form control enhancements */
    .form-control:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        border-color: #3b82f6;
    }

    /* Button transition effects */
    .btn {
        transition: all 0.2s ease-in-out;
    }

    .btn:hover:not(:disabled) {
        transform: translateY(-2px);
    }

    .btn:active:not(:disabled) {
        transform: translateY(0);
    }

    /* Alert banner styling */
    .alert {
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    /* Modal header enhancements */
    .modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: none;
    }

    .modal-header .flex {
        width: 100%;
    }

    .modal-header .bg-white.bg-opacity-20 {
        backdrop-filter: blur(10px);
    }

    /* Modal body gradient background */
    .modal-body {
        padding: 1.5rem;
    }

    /* Modal footer enhancements */
    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: none;
    }

    .modal-dialog {
        max-width: 90%;
        margin: 1.75rem auto;
    }

    .modal-lg {
        max-width: 800px;
    }

    .modal-content {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }

    /* Existing Styles */
    /* Mobile Responsive Improvements */
    @media (max-width: 575.98px) {

        /* Reduce camera container height on mobile */
        #cameraPlaceholderCheckIn,
        #cameraPlaceholderCheckOut {
            min-height: 200px !important;
        }

        /* Full width buttons on mobile */
        .modal-footer button {
            font-size: 0.95rem;
            padding: 0.6rem 1rem;
        }

        /* Larger touch targets */
        .btn-group label {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }

        /* Stack camera buttons vertically on small screens */
        .d-grid.gap-2 {
            row-gap: 0.5rem;
        }

        /* Better spacing for camera buttons */
        .d-grid button {
            padding: 0.65rem 1rem;
            font-size: 0.9rem;
        }

        /* Adjust video and canvas for mobile */
        #videoCheckIn,
        #canvasCheckIn,
        #videoCheckOut,
        #canvasCheckOut {
            max-height: 300px;
            object-fit: cover;
        }

        /* Modal title adjustment */
        .modal-title {
            font-size: 1.1rem;
        }

        /* Form labels */
        .form-label {
            font-size: 0.95rem;
            margin-bottom: 0.4rem;
        }

        /* Textarea adjustment */
        textarea.form-control {
            font-size: 0.9rem;
        }
    }

    @media (min-width: 576px) {

        /* Desktop gap between buttons */
        .d-sm-flex {
            gap: 0.5rem;
        }
    }

    /* Camera container improvements */
    .camera-container {
        position: relative;
    }

    .camera-container video,
    .camera-container canvas {
        border-radius: 0.375rem;
        background: #000;
    }

    /* Touch-friendly button sizing */
    .btn-group label {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-group label:active {
        transform: scale(0.98);
    }

    /* Modal fullscreen improvements */
    @media (max-width: 575.98px) {
        .modal-fullscreen-sm-down .modal-body {
            padding: 1rem;
        }

        .modal-fullscreen-sm-down .modal-header {
            padding: 0.75rem 1rem;
        }

        .modal-fullscreen-sm-down .modal-footer {
            padding: 0.75rem 1rem;
        }
    }

    /* Spinner animation */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .fa-spinner.fa-spin {
        animation: spin 1s linear infinite;
    }

</style>

<?= $this->endSection() ?>