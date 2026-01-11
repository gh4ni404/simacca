<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Welcome Section -->
    <div class="mb-6 bg-gradient-to-r from-green-500 to-teal-600 rounded-xl p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Selamat Datang, <?= esc($guru['nama_lengkap']); ?>!</h1>
                <p class="mt-1 text-sm opacity-80"><?= date('l, d F Y'); ?></p>
                <div class="flex items-center mt-2">
                    <div class="flex items-center text-sm opacity-80">
                        <i class="fas fa-user-tie mr-2"></i>
                        Wali Kelas
                    </div>
                    <div class="mx-4 opacity-80">|</div>
                    <div class="flex items-center text-sm opacity-80">
                        <i class="fas fa-school mr-2"></i>
                        <?= esc($kelas['nama_kelas']); ?>
                    </div>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="h-16 w-16 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fas fa-user-tie text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Siswa</p>
                    <p class="text-2xl font-bold"><?= $stats['total_siswa']; ?></p>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                <i class="fas fa-check-circle mr-1 text-green-500"></i>
                <?= $stats['siswa_aktif']; ?> siswa aktif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-clipboard-check text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Absensi Bulan Ini</p>
                    <p class="text-2xl font-bold"><?= $stats['total_absensi_bulan_ini']; ?></p>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                <i class="fas fa-calendar-alt mr-1"></i>
                Pertemuan tercatat
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-envelope text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Izin Pending</p>
                    <p class="text-2xl font-bold"><?= count($izinPending); ?></p>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                Menunggu persetujuan
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-percentage text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tingkat Kehadiran</p>
                    <p class="text-2xl font-bold">
                        <?php 
                        if ($kehadiranStats['total'] > 0) {
                            echo round(($kehadiranStats['hadir'] / $kehadiranStats['total']) * 100, 1);
                        } else {
                            echo 0;
                        }
                        ?>%
                    </p>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                <i class="fas fa-chart-line mr-1"></i>
                Bulan ini
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Statistik Kehadiran -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                        Statistik Kehadiran Bulan Ini
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <i class="fas fa-check-circle text-3xl text-green-500 mb-2"></i>
                            <p class="text-2xl font-bold text-gray-800"><?= $kehadiranStats['hadir'] ?? 0; ?></p>
                            <p class="text-sm text-gray-600">Hadir</p>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <i class="fas fa-notes-medical text-3xl text-blue-500 mb-2"></i>
                            <p class="text-2xl font-bold text-gray-800"><?= $kehadiranStats['sakit'] ?? 0; ?></p>
                            <p class="text-sm text-gray-600">Sakit</p>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <i class="fas fa-file-alt text-3xl text-yellow-500 mb-2"></i>
                            <p class="text-2xl font-bold text-gray-800"><?= $kehadiranStats['izin'] ?? 0; ?></p>
                            <p class="text-sm text-gray-600">Izin</p>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <i class="fas fa-times-circle text-3xl text-red-500 mb-2"></i>
                            <p class="text-2xl font-bold text-gray-800"><?= $kehadiranStats['alpa'] ?? 0; ?></p>
                            <p class="text-sm text-gray-600">Alpa</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Siswa dengan Masalah Kehadiran -->
            <?php if (!empty($siswaAlpa)): ?>
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>
                        Siswa Perlu Perhatian
                    </h2>
                    <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">
                        Alpa ≥ 3x
                    </span>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        <?php foreach ($siswaAlpa as $siswa): ?>
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-red-200 flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-red-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800"><?= esc($siswa['nama_lengkap']); ?></p>
                                    <p class="text-xs text-gray-600"><?= esc($siswa['nis']); ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-200 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    <?= $siswa['total_alpa']; ?>x Alpa
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Recent Absensi -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-history mr-2 text-purple-500"></i>
                        Absensi Terbaru
                    </h2>
                    <a href="<?= base_url('walikelas/absensi'); ?>" class="text-sm text-blue-600 hover:text-blue-800">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="p-4">
                    <?php if (empty($recentAbsensi)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Belum ada data absensi</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-2">
                        <?php foreach ($recentAbsensi as $absen): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800"><?= esc($absen['nama_mapel']); ?></p>
                                <p class="text-xs text-gray-600">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <?= date('d M Y', strtotime($absen['tanggal'])); ?>
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-user mr-1"></i>
                                    <?= esc($absen['nama_guru']); ?>
                                </p>
                            </div>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                Pertemuan ke-<?= $absen['pertemuan_ke']; ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                        Quick Actions
                    </h2>
                </div>
                <div class="p-4 space-y-2">
                    <a href="<?= base_url('walikelas/siswa'); ?>" class="block p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-users text-blue-600 text-xl mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-800">Data Siswa</p>
                                <p class="text-xs text-gray-600">Lihat data siswa kelas</p>
                            </div>
                        </div>
                    </a>
                    <a href="<?= base_url('walikelas/absensi'); ?>" class="block p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-clipboard-check text-green-600 text-xl mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-800">Monitor Absensi</p>
                                <p class="text-xs text-gray-600">Lihat absensi kelas</p>
                            </div>
                        </div>
                    </a>
                    <a href="<?= base_url('walikelas/izin'); ?>" class="block p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-yellow-600 text-xl mr-3"></i>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">Persetujuan Izin</p>
                                <p class="text-xs text-gray-600">Kelola izin siswa</p>
                            </div>
                            <?php if (count($izinPending) > 0): ?>
                            <span class="bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center">
                                <?= count($izinPending); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <a href="<?= base_url('walikelas/laporan'); ?>" class="block p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-chart-line text-purple-600 text-xl mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-800">Laporan</p>
                                <p class="text-xs text-gray-600">Lihat laporan kehadiran</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Izin Pending -->
            <?php if (!empty($izinPending)): ?>
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-clock mr-2 text-yellow-500"></i>
                        Izin Menunggu
                    </h2>
                    <a href="<?= base_url('walikelas/izin'); ?>" class="text-sm text-blue-600 hover:text-blue-800">
                        Lihat Semua
                    </a>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        <?php $count = 0; ?>
                        <?php foreach ($izinPending as $izin): ?>
                            <?php if ($count >= 5) break; ?>
                            <div class="p-3 bg-yellow-50 rounded-lg">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-800"><?= esc($izin['nama_lengkap']); ?></p>
                                        <p class="text-xs text-gray-600 mt-1">
                                            <i class="fas fa-calendar mr-1"></i>
                                            <?= date('d M Y', strtotime($izin['tanggal'])); ?>
                                        </p>
                                        <p class="text-xs text-gray-700 mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <?= esc($izin['jenis_izin']); ?> - <?= esc(substr($izin['alasan'], 0, 30)); ?><?= strlen($izin['alasan']) > 30 ? '...' : ''; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php $count++; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Info Kelas -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Informasi Kelas
                    </h2>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Nama Kelas</span>
                            <span class="font-medium text-gray-800"><?= esc($kelas['nama_kelas']); ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Tingkat</span>
                            <span class="font-medium text-gray-800">Kelas <?= esc($kelas['tingkat']); ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Jurusan</span>
                            <span class="font-medium text-gray-800"><?= esc($kelas['jurusan']); ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">Jumlah Siswa</span>
                            <span class="font-medium text-gray-800"><?= $stats['total_siswa']; ?> siswa</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
