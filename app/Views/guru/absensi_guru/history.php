<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-blue-600"></i> Riwayat Absensi
            </h1>
            <p class="text-gray-600 mt-1">Histori kehadiran Anda</p>
        </div>
        <div>
            <a href="<?= base_url('guru/absensi-guru') ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors inline-flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Monthly Statistics -->
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

<?= $this->endSection() ?>
