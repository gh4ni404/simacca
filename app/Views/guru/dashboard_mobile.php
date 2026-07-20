<?= $this->extend('templates/mobile_layout') ?>

<?= $this->section('content') ?>
<div class="px-4 pb-20">
    <!-- Welcome Card - Compact Mobile Version -->
    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white p-4 mb-4 rounded-lg shadow-md">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h1 class="text-lg font-bold">Selamat Datang, <?= esc($guru['nama_lengkap'] ?? session()->get('username')); ?>!</h1>
                <p class="text-xs opacity-90 mt-1"><?= date('d M Y'); ?></p>
                <div class="flex items-center gap-2 mt-2">
                    <?php if ($isPembimbingPkl): ?>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-400 bg-opacity-30">
                            <i class="fas fa-building mr-1 text-xs"></i>
                            Pembimbing PKL
                        </span>
                    <?php endif; ?>
                    <?php if ($guru['is_wali_kelas']): ?>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-400 bg-opacity-30">
                            <i class="fas fa-user-tie mr-1 text-xs"></i>
                            Wali Kelas
                        </span>
                    <?php endif; ?>
                    <?php if (!$guru['is_wali_kelas'] && !$isPembimbingPkl): ?>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-400 bg-opacity-30">
                            <i class="fas fa-chalkboard-teacher mr-1 text-xs"></i>
                            Guru
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="h-24 w-24 rounded-full bg-white bg-opacity-20 flex items-center justify-center flex-shrink-0">
                <!-- <i class="fas fa-user-circle text-2xl"></i> -->
                <?php if (session()->get('profile_photo')): ?>
                    <img src="<?= base_url('profile-photo/' . esc(session()->get('profile_photo'))); ?>"
                        alt="<?= esc(session()->get('nama_lengkap') ?? session()->get('username')); ?>"
                        class="h-24 w-24 rounded-full object-cover border-2 border-indigo-200">
                <?php else: ?>
                    <div class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-indigo-600 font-semibold text-xs">
                            <?= strtoupper(substr(session()->get('nama_lengkap') ?? session()->get('username') ?? 'U', 0, 2)); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stats Grid - 2 Columns on Mobile -->
    <div class="grid grid-cols-2 gap-3 mb-4">
        <?php if ($isPembimbingPkl): ?>
            <?= stat_card(
                'Siswa PKL', 
                $pklStats['total_siswa'], 
                'user-graduate', 
                'blue', 
                '', 
                '<i class="fas fa-building mr-1"></i>under bimbingan',
                'compact'
            ); ?>

            <?= stat_card(
                'Absensi PKL', 
                $pklStats['absensi_bulan_ini'], 
                'clipboard-check', 
                'green', 
                '', 
                '<i class="fas fa-chart-line mr-1"></i>bulan ini',
                'compact'
            ); ?>

            <?= stat_card(
                'Jurnal Pending', 
                $pklStats['jurnal_pending'], 
                'clock', 
                'orange', 
                '', 
                '<i class="fas fa-exclamation-circle mr-1"></i>perlu review',
                'compact'
            ); ?>

            <?= stat_card(
                'Kehadiran', 
                $pklStats['persen_kehadiran'] . '%', 
                'chart-pie', 
                'purple', 
                '', 
                '<i class="fas fa-check-circle mr-1"></i>persentase',
                'compact'
            ); ?>
        <?php else: ?>
            <?= stat_card(
                'Total Jadwal', 
                $stats['total_jadwal'], 
                'calendar-alt', 
                'blue', 
                '', 
                '<i class="fas fa-clock mr-1"></i>' . $stats['absensi_hari_ini'] . ' hari ini',
                'compact'
            ); ?>

            <?= stat_card(
                'Absensi', 
                $stats['absensi_bulan_ini'], 
                'clipboard-check', 
                'green', 
                '', 
                '<i class="fas fa-chart-line mr-1"></i>bulan ini',
                'compact'
            ); ?>

            <?= stat_card(
                'Jurnal', 
                $stats['jurnal_bulan_ini'], 
                'book', 
                'purple', 
                '', 
                '<i class="fas fa-check-circle mr-1"></i>bulan ini',
                'compact'
            ); ?>

            <?= stat_card(
                'Kelas', 
                $stats['total_kelas'], 
                'school', 
                'yellow', 
                '', 
                '<i class="fas fa-users mr-1"></i>yang diajar',
                'compact'
            ); ?>
        <?php endif; ?>
    </div>

    <!-- Absensi Guru Widget - Mobile -->
    <div class="mb-4 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-4 py-3">
            <h3 class="text-sm font-semibold text-white flex items-center">
                <i class="fas fa-user-check mr-2"></i>
                Absensi Guru Hari Ini
            </h3>
        </div>
        <div class="p-4">
            <?php if ($absensiGuruToday): ?>
                <div class="space-y-3">
                    <!-- Check In Status -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Check In</p>
                                <p class="text-xl font-bold text-green-600">
                                    <?= $absensiGuruToday['check_in'] ? date('H:i', strtotime($absensiGuruToday['check_in'])) : '-'; ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?= $absensiGuruToday['status'] === 'hadir' ? 'Tepat waktu' : ucfirst($absensiGuruToday['status']); ?>
                                </p>
                            </div>
                            <i class="fas fa-sign-in-alt text-2xl text-green-400"></i>
                        </div>
                    </div>

                    <!-- Check Out Status -->
                    <div class="bg-<?= $absensiGuruToday['check_out'] ? 'blue' : 'gray'; ?>-50 border border-<?= $absensiGuruToday['check_out'] ? 'blue' : 'gray'; ?>-200 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Check Out</p>
                                <p class="text-xl font-bold text-<?= $absensiGuruToday['check_out'] ? 'blue' : 'gray'; ?>-600">
                                    <?= $absensiGuruToday['check_out'] ? date('H:i', strtotime($absensiGuruToday['check_out'])) : 'Belum'; ?>
                                </p>
                                <?php if ($absensiGuruToday['check_out']): ?>
                                    <?php
                                    $checkIn = strtotime($absensiGuruToday['check_in']);
                                    $checkOut = strtotime($absensiGuruToday['check_out']);
                                    $durasi = ($checkOut - $checkIn) / 3600;
                                    ?>
                                    <p class="text-xs text-gray-500 mt-1">Durasi: <?= number_format($durasi, 1); ?> jam</p>
                                <?php else: ?>
                                    <p class="text-xs text-gray-500 mt-1">Sedang bertugas</p>
                                <?php endif; ?>
                            </div>
                            <i class="fas fa-sign-out-alt text-2xl text-<?= $absensiGuruToday['check_out'] ? 'blue' : 'gray'; ?>-400"></i>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="text-center">
                        <?php if (!$absensiGuruToday['check_out']): ?>
                            <a href="<?= base_url('guru/absensi-guru'); ?>" 
                               class="block bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white px-4 py-3 rounded-lg font-semibold transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Check Out Sekarang
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('guru/absensi-guru'); ?>" 
                               class="block bg-gray-500 hover:bg-gray-600 active:bg-gray-700 text-white px-4 py-3 rounded-lg font-semibold transition-colors">
                                <i class="fas fa-history mr-2"></i>
                                Lihat Riwayat
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-6">
                    <i class="fas fa-calendar-times text-5xl text-gray-300 mb-3"></i>
                    <h4 class="text-base font-semibold text-gray-700 mb-2">Belum Check In</h4>
                    <p class="text-sm text-gray-500 mb-4">Lakukan check in untuk mencatat kehadiran</p>
                    <a href="<?= base_url('guru/absensi-guru'); ?>" 
                       class="block bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Check In Sekarang
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions - Horizontal Scroll -->
    <div class="mb-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-900">Aksi Cepat</h3>
        </div>
        <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
            <?php foreach ($quickActions as $action): ?>
                <a href="<?= $action['url']; ?>"
                    class="flex-shrink-0 w-32 <?= $action['color']; ?> text-white rounded-lg p-3 shadow-sm active:shadow-lg transition-shadow">
                    <div class="flex flex-col items-center text-center">
                        <div class="p-2 rounded-full bg-white bg-opacity-20 mb-2">
                            <i class="<?= $action['icon']; ?> text-lg"></i>
                        </div>
                        <p class="text-xs font-semibold"><?= $action['title']; ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Jadwal Hari Ini / Siswa Bimbingan -->
    <?php if ($isPembimbingPkl): ?>
    <div class="bg-white rounded-lg shadow-sm mb-4">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Siswa Bimbingan</h3>
                <p class="text-xs text-gray-500"><?= count($siswaPklList); ?> siswa aktif</p>
            </div>
            <a href="<?= base_url('guru/absensi-pkl'); ?>" class="text-xs text-blue-500 active:text-blue-700">
                Semua <i class="fas fa-chevron-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            <?php if (empty($siswaPklList)): ?>
                <div class="text-center py-6">
                    <i class="fas fa-building text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada siswa PKL</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach (array_slice($siswaPklList, 0, 5) as $siswa): ?>
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900"><?= $siswa['nama_lengkap']; ?></h4>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-id-badge mr-1"></i>
                                        <?= $siswa['nis']; ?> | <?= $siswa['nama_kelas']; ?>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-building mr-1"></i>
                                        <?= $siswa['nama_perusahaan']; ?>
                                        <?php if (!empty($siswa['kota'])): ?>
                                            <span class="text-gray-400">-</span> <?= $siswa['kota']; ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-lg shadow-sm mb-4">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Jadwal Hari Ini</h3>
                <p class="text-xs text-gray-500"><?= date('d M Y'); ?></p>
            </div>
            <a href="<?= base_url('guru/jadwal'); ?>" class="text-xs text-blue-500 active:text-blue-700">
                Semua <i class="fas fa-chevron-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            <?php if (empty($jadwalHariIni)): ?>
                <div class="text-center py-6">
                    <i class="fas fa-calendar-times text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Tidak ada jadwal hari ini</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($jadwalHariIni as $jadwal): ?>
                        <div class="border border-gray-200 rounded-lg p-3 active:bg-gray-50">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900"><?= $jadwal['nama_mapel']; ?></h4>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-clock mr-1"></i>
                                        <?= date('H:i', strtotime($jadwal['jam_mulai'])); ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])); ?>
                                    </p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-school mr-1"></i>
                                        <?= $jadwal['nama_kelas']; ?>
                                    </p>
                                </div>
                            </div>
                            <a href="<?= base_url('guru/absensi/tambah?jadwal_id=' . $jadwal['id']); ?>"
                                class="block w-full text-center px-3 py-2 bg-blue-500 text-white rounded-lg text-xs font-medium active:bg-blue-600">
                                <i class="fas fa-clipboard-check mr-1"></i>
                                Input Absensi
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Pending Izin (only for non-PKL) -->
    <?php if (!$isPembimbingPkl && !empty($pendingIzin)): ?>
    <div class="bg-white rounded-lg shadow-sm mb-4">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">
                    <i class="fas fa-exclamation-circle text-yellow-500 mr-1"></i>
                    Izin Pending
                </h3>
                <p class="text-xs text-gray-500"><?= count($pendingIzin); ?> perlu persetujuan</p>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    <?php foreach (array_slice($pendingIzin, 0, 3) as $izin): ?>
                        <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-3">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900"><?= $izin['nama_lengkap']; ?></h4>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-id-badge mr-1"></i><?= $izin['nis']; ?> | <?= $izin['nama_kelas']; ?>
                                    </p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-calendar-day mr-1"></i>
                                        <?= date('d/m/Y', strtotime($izin['tanggal'])); ?>
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 line-clamp-2">
                                <?= $izin['alasan']; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Recent Absensi -->
    <?php if ($isPembimbingPkl): ?>
    <div class="bg-white rounded-lg shadow-sm mb-4">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Absensi PKL Terbaru</h3>
                <p class="text-xs text-gray-500">5 terakhir</p>
            </div>
            <a href="<?= base_url('guru/absensi-pkl'); ?>" class="text-xs text-blue-500 active:text-blue-700">
                Semua <i class="fas fa-chevron-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            <?php if (empty($recentAbsensiPkl)): ?>
                <div class="text-center py-6">
                    <i class="fas fa-clipboard-list text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada absensi PKL</p>
                </div>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($recentAbsensiPkl as $absensi): ?>
                        <a href="<?= base_url('guru/absensi-pkl/show/' . $absensi['id']); ?>"
                            class="flex items-center justify-between p-3 border border-gray-200 rounded-lg active:bg-gray-50">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900"><?= $absensi['nama_perusahaan']; ?></h4>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="text-xs text-gray-600">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        <?= date('d/m/Y', strtotime($absensi['tanggal'])); ?>
                                    </span>
                                    <span class="text-xs text-gray-600">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        <?= $absensi['kota']; ?>
                                    </span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-lg shadow-sm mb-4">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Absensi Terbaru</h3>
                <p class="text-xs text-gray-500">5 terakhir</p>
            </div>
            <a href="<?= base_url('guru/absensi'); ?>" class="text-xs text-blue-500 active:text-blue-700">
                Semua <i class="fas fa-chevron-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            <?php if (empty($recentAbsensi)): ?>
                <div class="text-center py-6">
                    <i class="fas fa-clipboard-list text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada data absensi</p>
                </div>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($recentAbsensi as $absensi): ?>
                        <a href="<?= base_url('guru/absensi/show/' . $absensi['id']); ?>"
                            class="flex items-center justify-between p-3 border border-gray-200 rounded-lg active:bg-gray-50">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900"><?= $absensi['nama_mapel']; ?></h4>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="text-xs text-gray-600">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        <?= date('d/m/Y', strtotime($absensi['tanggal'])); ?>
                                    </span>
                                    <span class="text-xs text-gray-600">
                                        <i class="fas fa-school mr-1"></i>
                                        <?= $absensi['nama_kelas']; ?>
                                    </span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Jurnal -->
    <?php if ($isPembimbingPkl): ?>
    <div class="bg-white rounded-lg shadow-sm mb-4">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Jurnal Perlu Verifikasi</h3>
                <p class="text-xs text-gray-500">Dari siswa PKL</p>
            </div>
            <a href="<?= base_url('guru/jurnal-pkl'); ?>" class="text-xs text-blue-500 active:text-blue-700">
                Semua <i class="fas fa-chevron-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            <?php if (empty($recentJurnalPkl)): ?>
                <div class="text-center py-6">
                    <i class="fas fa-check-double text-3xl text-green-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Semua jurnal sudah terverifikasi</p>
                </div>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($recentJurnalPkl as $jurnal): ?>
                        <a href="<?= base_url('guru/jurnal-pkl'); ?>"
                            class="block border border-gray-200 rounded-lg p-3 active:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900"><?= $jurnal['nama_siswa']; ?></h4>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-tasks mr-1"></i>
                                        <?= $jurnal['nama_task']; ?>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        <?= date('d/m/Y', strtotime($jurnal['tanggal'])); ?>
                                        <?php if (!empty($jurnal['kategori_nama'])): ?>
                                            | <?= $jurnal['kategori_nama']; ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    <?= $jurnal['status'] === 'revision' ? 'bg-red-100 text-red-800' :
                                         ($jurnal['status'] === 'submitted' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'); ?>">
                                    <?= $jurnal['status'] === 'revision' ? 'Revisi' :
                                         ($jurnal['status'] === 'submitted' ? 'Baru' : 'Review'); ?>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-lg shadow-sm mb-4">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Jurnal Terbaru</h3>
                <p class="text-xs text-gray-500">5 terakhir</p>
            </div>
            <a href="<?= base_url('guru/jurnal'); ?>" class="text-xs text-blue-500 active:text-blue-700">
                Semua <i class="fas fa-chevron-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            <?php if (empty($recentJurnal)): ?>
                <div class="text-center py-6">
                    <i class="fas fa-book-open text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada jurnal</p>
                </div>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($recentJurnal as $jurnal): ?>
                        <div class="border border-gray-200 rounded-lg p-3">
                            <h4 class="text-sm font-medium text-gray-900"><?= $jurnal['nama_mapel']; ?></h4>
                            <p class="text-xs text-gray-600 mt-1">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                <?= date('d/m/Y', strtotime($jurnal['tanggal'])); ?>
                                | <?= $jurnal['nama_kelas']; ?>
                            </p>
                            <p class="text-xs text-gray-500 mt-2 line-clamp-2">
                                <?= substr(strip_tags($jurnal['kegiatan_pembelajaran']), 0, 80); ?>
                                <?php if (strlen(strip_tags($jurnal['kegiatan_pembelajaran'])) > 80) echo '...'; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .line-clamp-2 {
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Auto refresh pending izin every 30 seconds
    setInterval(function() {
        fetch('<?= base_url("guru/dashboard/getPendingIzin"); ?>')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    const badge = document.getElementById('pendingIzinBadge');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.classList.remove('hidden');
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }, 30000);
</script>
<?= $this->endSection() ?>