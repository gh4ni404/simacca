<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Welcome Section -->
    <div class="mb-6 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Selamat Datang, <?= esc($siswa['nama_lengkap']); ?>!</h1>
                <p class="mt-1 text-sm opacity-80"><?= date('l, d F Y'); ?></p>
                <div class="flex items-center mt-2">
                    <div class="flex items-center text-sm opacity-80">
                        <i class="fas fa-user-graduate mr-2"></i>
                        Siswa
                    </div>
                    <div class="mx-4 opacity-80">|</div>
                    <div class="flex items-center text-sm opacity-80">
                        <i class="fas fa-school mr-2"></i>
                        <?= esc($siswa['nama_kelas']); ?>
                    </div>
                    <div class="mx-4 opacity-80">|</div>
                    <div class="flex items-center text-sm opacity-80">
                        <i class="fas fa-id-card mr-2"></i>
                        NIS: <?= esc($siswa['nis']); ?>
                    </div>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="h-16 w-16 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fas fa-user-graduate text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Kehadiran Bulan Ini</p>
                    <p class="text-2xl font-bold"><?= $persentaseKehadiran; ?>%</p>
                </div>
            </div>
            <div class="mt-2">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full <?= $persentaseKehadiran >= 80 ? 'bg-green-500' : ($persentaseKehadiran >= 60 ? 'bg-yellow-500' : 'bg-red-500'); ?>" 
                         style="width: <?= $persentaseKehadiran; ?>%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-calendar-check text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Pertemuan</p>
                    <p class="text-2xl font-bold"><?= $kehadiran['total']; ?></p>
                </div>
            </div>
            <div class="mt-2 flex gap-2 text-xs">
                <span class="text-green-600">H: <?= $kehadiran['hadir']; ?></span>
                <span class="text-blue-600">S: <?= $kehadiran['sakit']; ?></span>
                <span class="text-yellow-600">I: <?= $kehadiran['izin']; ?></span>
                <span class="text-red-600">A: <?= $kehadiran['alpa']; ?></span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Izin Pending</p>
                    <p class="text-2xl font-bold"><?= $izinPending; ?></p>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                <i class="fas fa-hourglass-half mr-1"></i>
                Menunggu persetujuan
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-check-double text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Izin Disetujui</p>
                    <p class="text-2xl font-bold"><?= $izinDisetujui; ?></p>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                <i class="fas fa-thumbs-up mr-1"></i>
                Total disetujui
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Jadwal Hari Ini -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-calendar-day mr-2 text-blue-500"></i>
                        Jadwal Pelajaran Hari Ini
                    </h2>
                    <a href="<?= base_url('siswa/jadwal'); ?>" class="text-sm text-blue-600 hover:text-blue-800">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="p-4">
                    <?php if (empty($jadwalHariIni)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-calendar-times text-4xl mb-2"></i>
                        <p>Tidak ada jadwal untuk hari ini</p>
                        <p class="text-sm mt-1">Selamat berlibur! 🎉</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($jadwalHariIni as $jadwal): ?>
                        <div class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex-shrink-0 w-16 text-center">
                                <div class="text-sm font-bold text-blue-600">
                                    <?= date('H:i', strtotime($jadwal['jam_mulai'])); ?>
                                </div>
                                <div class="text-xs text-gray-600">
                                    <?= date('H:i', strtotime($jadwal['jam_selesai'])); ?>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="font-semibold text-gray-800"><?= esc($jadwal['nama_mapel']); ?></h3>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-user-tie mr-1"></i>
                                    <?= esc($jadwal['nama_guru']); ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-200 text-blue-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    <?php 
                                    $start = strtotime($jadwal['jam_mulai']);
                                    $end = strtotime($jadwal['jam_selesai']);
                                    $duration = ($end - $start) / 60;
                                    echo $duration . ' menit';
                                    ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Statistik Kehadiran -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-chart-bar mr-2 text-green-500"></i>
                        Statistik Kehadiran Bulan Ini
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <i class="fas fa-check-circle text-3xl text-green-500 mb-2"></i>
                            <p class="text-2xl font-bold text-gray-800"><?= $kehadiran['hadir'] ?? 0; ?></p>
                            <p class="text-sm text-gray-600">Hadir</p>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <i class="fas fa-notes-medical text-3xl text-blue-500 mb-2"></i>
                            <p class="text-2xl font-bold text-gray-800"><?= $kehadiran['sakit'] ?? 0; ?></p>
                            <p class="text-sm text-gray-600">Sakit</p>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <i class="fas fa-file-alt text-3xl text-yellow-500 mb-2"></i>
                            <p class="text-2xl font-bold text-gray-800"><?= $kehadiran['izin'] ?? 0; ?></p>
                            <p class="text-sm text-gray-600">Izin</p>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <i class="fas fa-times-circle text-3xl text-red-500 mb-2"></i>
                            <p class="text-2xl font-bold text-gray-800"><?= $kehadiran['alpa'] ?? 0; ?></p>
                            <p class="text-sm text-gray-600">Alpa</p>
                        </div>
                    </div>

                    <?php if ($kehadiran['alpa'] >= 3): ?>
                    <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl mr-3 mt-1"></i>
                            <div>
                                <p class="text-sm font-semibold text-red-800">Peringatan!</p>
                                <p class="text-sm text-red-700 mt-1">
                                    Anda memiliki <?= $kehadiran['alpa']; ?>x Alpa bulan ini. Mohon tingkatkan kehadiran Anda!
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Riwayat Absensi Terbaru -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-history mr-2 text-purple-500"></i>
                        Riwayat Absensi Terbaru
                    </h2>
                    <a href="<?= base_url('siswa/absensi'); ?>" class="text-sm text-blue-600 hover:text-blue-800">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="p-4">
                    <?php if (empty($recentAbsensi)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Belum ada riwayat absensi</p>
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
                                    Pertemuan ke-<?= $absen['pertemuan_ke']; ?>
                                </p>
                            </div>
                            <div>
                                <?php
                                $statusClass = '';
                                $statusIcon = '';
                                $statusText = '';
                                
                                switch(strtolower($absen['status'])) {
                                    case 'hadir':
                                        $statusClass = 'bg-green-100 text-green-800';
                                        $statusIcon = 'fa-check-circle';
                                        $statusText = 'Hadir';
                                        break;
                                    case 'sakit':
                                        $statusClass = 'bg-blue-100 text-blue-800';
                                        $statusIcon = 'fa-notes-medical';
                                        $statusText = 'Sakit';
                                        break;
                                    case 'izin':
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                        $statusIcon = 'fa-file-alt';
                                        $statusText = 'Izin';
                                        break;
                                    case 'alpa':
                                        $statusClass = 'bg-red-100 text-red-800';
                                        $statusIcon = 'fa-times-circle';
                                        $statusText = 'Alpa';
                                        break;
                                }
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass; ?>">
                                    <i class="fas <?= $statusIcon; ?> mr-1"></i>
                                    <?= $statusText; ?>
                                </span>
                            </div>
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
                    <a href="<?= base_url('siswa/jadwal'); ?>" class="block p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-blue-600 text-xl mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-800">Jadwal Pelajaran</p>
                                <p class="text-xs text-gray-600">Lihat jadwal lengkap</p>
                            </div>
                        </div>
                    </a>
                    <a href="<?= base_url('siswa/absensi'); ?>" class="block p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-clipboard-list text-green-600 text-xl mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-800">Riwayat Absensi</p>
                                <p class="text-xs text-gray-600">Lihat riwayat kehadiran</p>
                            </div>
                        </div>
                    </a>
                    <a href="<?= base_url('siswa/izin/tambah'); ?>" class="block p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-paper-plane text-yellow-600 text-xl mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-800">Ajukan Izin</p>
                                <p class="text-xs text-gray-600">Buat pengajuan izin baru</p>
                            </div>
                        </div>
                    </a>
                    <a href="<?= base_url('siswa/profil'); ?>" class="block p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <i class="fas fa-user-circle text-purple-600 text-xl mr-3"></i>
                            <div>
                                <p class="font-medium text-gray-800">Profil Saya</p>
                                <p class="text-xs text-gray-600">Lihat & edit profil</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Info Pribadi -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-id-card mr-2 text-blue-500"></i>
                        Informasi Pribadi
                    </h2>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">NIS</span>
                            <span class="font-medium text-gray-800"><?= esc($siswa['nis']); ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">NISN</span>
                            <span class="font-medium text-gray-800"><?= esc($siswa['nisn'] ?? '-'); ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Kelas</span>
                            <span class="font-medium text-gray-800"><?= esc($siswa['nama_kelas']); ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Jenis Kelamin</span>
                            <span class="font-medium text-gray-800"><?= $siswa['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">Email</span>
                            <span class="font-medium text-gray-800 text-sm"><?= esc($siswa['email'] ?? '-'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow p-4 text-white">
                <div class="flex items-start">
                    <i class="fas fa-lightbulb text-2xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold mb-2">Tips Kehadiran</h3>
                        <ul class="text-sm space-y-1 opacity-90">
                            <li>• Jaga kehadiran minimal 80%</li>
                            <li>• Ajukan izin jika berhalangan</li>
                            <li>• Cek jadwal setiap hari</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
