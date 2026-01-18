<?= $this->extend('templates/mobile_layout') ?>

<?= $this->section('content') ?>

<!-- Modern Mobile Header with Gradient -->
<div class="bg-gradient-to-br from-indigo-600 via-blue-600 to-cyan-500 rounded-3xl shadow-lg p-4 mb-4 text-white">
    <div class="flex items-center justify-between mb-3">
        <div class="flex-1">
            <h1 class="text-xl font-bold mb-1">Dashboard Wakakur</h1>
            <p class="text-xs text-indigo-100 flex items-center">
                <i class="fas fa-user-tie mr-1.5 text-sm"></i>
                <?= esc($guru['nama_lengkap']) ?>
            </p>
        </div>
        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
            <i class="fas fa-shield-alt text-white text-xl"></i>
        </div>
    </div>
    <div class="flex items-center justify-between text-xs bg-white bg-opacity-10 rounded-xl px-3 py-2 backdrop-blur-sm">
        <span class="flex items-center">
            <i class="fas fa-calendar mr-1.5"></i>
            <?= date('d M Y') ?>
        </span>
        <span class="flex items-center">
            <i class="fas fa-clock mr-1.5"></i>
            <?= date('H:i') ?> WIB
        </span>
    </div>
</div>

<?= render_flash_message() ?>

<!-- Enhanced Overview Stats Grid -->
<div class="grid grid-cols-2 gap-3 mb-4">
    <!-- Total Kelas -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-4 text-white transform active:scale-95 transition-transform">
        <div class="flex items-start justify-between mb-2">
            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-school text-lg"></i>
            </div>
            <span class="text-2xl font-bold"><?= $totalKelas ?></span>
        </div>
        <p class="text-xs font-medium text-blue-100">Total Kelas</p>
    </div>
    
    <!-- Total Siswa -->
    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg p-4 text-white transform active:scale-95 transition-transform">
        <div class="flex items-start justify-between mb-2">
            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-graduate text-lg"></i>
            </div>
            <span class="text-2xl font-bold"><?= $totalSiswa ?></span>
        </div>
        <p class="text-xs font-medium text-green-100">Total Siswa</p>
    </div>
    
    <!-- Total Guru -->
    <div class="bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl shadow-lg p-4 text-white transform active:scale-95 transition-transform">
        <div class="flex items-start justify-between mb-2">
            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-lg"></i>
            </div>
            <span class="text-2xl font-bold"><?= $totalGuru ?></span>
        </div>
        <p class="text-xs font-medium text-purple-100">Total Guru</p>
    </div>
    
    <!-- Absensi Hari Ini -->
    <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-lg p-4 text-white transform active:scale-95 transition-transform">
        <div class="flex items-start justify-between mb-2">
            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-clipboard-check text-lg"></i>
            </div>
            <span class="text-2xl font-bold"><?= $absensiHariIni ?></span>
        </div>
        <p class="text-xs font-medium text-amber-100">Absensi Hari Ini</p>
    </div>
</div>

<!-- Aktivitas Mengajar Card -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4">
    <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-4 py-3">
        <h6 class="text-white font-semibold text-sm flex items-center">
            <i class="fas fa-chalkboard-teacher mr-2"></i>
            Aktivitas Mengajar
        </h6>
    </div>
    <div class="p-4">
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-3 text-center border border-blue-200">
                <div class="text-2xl font-bold text-blue-600 mb-1"><?= $totalJadwalMengajar ?></div>
                <div class="text-xs text-gray-600 font-medium">Jadwal</div>
            </div>
            <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl p-3 text-center border border-cyan-200">
                <div class="text-2xl font-bold text-cyan-600 mb-1"><?= $kelasYangDiajar ?></div>
                <div class="text-xs text-gray-600 font-medium">Kelas</div>
            </div>
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-3 text-center shadow-md">
                <div class="text-2xl font-bold text-white mb-1"><?= $absensiGuru ?></div>
                <div class="text-xs text-indigo-100 font-medium">Absensi</div>
            </div>
        </div>
    </div>
</div>

<!-- Wali Kelas Stats -->
<?php if ($isWaliKelas && $kelasWali): ?>
<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4">
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-4 py-3">
        <h6 class="text-white font-semibold text-sm flex items-center">
            <i class="fas fa-user-check mr-2"></i>
            Status Wali Kelas
        </h6>
    </div>
    <div class="p-4">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-lg p-3 mb-3">
            <div class="flex items-center">
                <i class="fas fa-school text-green-600 text-xl mr-2"></i>
                <div>
                    <div class="text-xs text-gray-600 font-medium">Kelas yang Diampu</div>
                    <div class="text-base font-bold text-green-700"><?= esc($kelasWali['nama_kelas']) ?></div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-200">
                <div class="text-2xl font-bold text-gray-700 mb-1"><?= $statsWali['total_siswa'] ?></div>
                <div class="text-xs text-gray-600 font-medium">Siswa</div>
            </div>
            <div class="bg-gradient-to-br from-amber-50 to-orange-100 rounded-xl p-3 text-center border border-amber-200">
                <div class="text-2xl font-bold text-amber-600 mb-1"><?= $statsWali['izin_pending'] ?></div>
                <div class="text-xs text-gray-600 font-medium">Izin Pending</div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Quick Actions with Modern Design -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4">
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-3">
        <h6 class="text-white font-semibold text-sm flex items-center">
            <i class="fas fa-bolt mr-2"></i>
            Aksi Cepat
        </h6>
    </div>
    <div class="p-4">
        <div class="grid grid-cols-2 gap-3">
            <a href="<?= base_url('wakakur/absensi') ?>" 
               class="bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-500 hover:to-blue-600 rounded-xl p-4 text-center border-2 border-blue-200 active:scale-95 transition-all group">
                <div class="w-12 h-12 bg-blue-500 group-hover:bg-white rounded-full flex items-center justify-center mx-auto mb-2 transition-colors">
                    <i class="fas fa-clipboard-check text-white group-hover:text-blue-500 text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-blue-700 group-hover:text-white transition-colors">Kelola Absensi</span>
            </a>
            
            <a href="<?= base_url('wakakur/laporan') ?>" 
               class="bg-gradient-to-br from-green-50 to-green-100 hover:from-green-500 hover:to-green-600 rounded-xl p-4 text-center border-2 border-green-200 active:scale-95 transition-all group">
                <div class="w-12 h-12 bg-green-500 group-hover:bg-white rounded-full flex items-center justify-center mx-auto mb-2 transition-colors">
                    <i class="fas fa-chart-line text-white group-hover:text-green-500 text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-green-700 group-hover:text-white transition-colors">Laporan Detail</span>
            </a>
            
            <a href="<?= base_url('wakakur/jurnal') ?>" 
               class="bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-500 hover:to-purple-600 rounded-xl p-4 text-center border-2 border-purple-200 active:scale-95 transition-all group">
                <div class="w-12 h-12 bg-purple-500 group-hover:bg-white rounded-full flex items-center justify-center mx-auto mb-2 transition-colors">
                    <i class="fas fa-book-open text-white group-hover:text-purple-500 text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-purple-700 group-hover:text-white transition-colors">Jurnal KBM</span>
            </a>
            
            <?php if ($isWaliKelas): ?>
            <a href="<?= base_url('wakakur/izin') ?>" 
               class="bg-gradient-to-br from-orange-50 to-orange-100 hover:from-orange-500 hover:to-orange-600 rounded-xl p-4 text-center border-2 border-orange-200 active:scale-95 transition-all group relative">
                <div class="w-12 h-12 bg-orange-500 group-hover:bg-white rounded-full flex items-center justify-center mx-auto mb-2 transition-colors">
                    <i class="fas fa-envelope-open-text text-white group-hover:text-orange-500 text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-orange-700 group-hover:text-white transition-colors">Kelola Izin</span>
                <?php if ($statsWali['izin_pending'] > 0): ?>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center shadow-lg animate-pulse">
                        <?= $statsWali['izin_pending'] ?>
                    </span>
                <?php endif; ?>
            </a>
            <?php else: ?>
            <a href="<?= base_url('wakakur/jadwal') ?>" 
               class="bg-gradient-to-br from-teal-50 to-teal-100 hover:from-teal-500 hover:to-teal-600 rounded-xl p-4 text-center border-2 border-teal-200 active:scale-95 transition-all group">
                <div class="w-12 h-12 bg-teal-500 group-hover:bg-white rounded-full flex items-center justify-center mx-auto mb-2 transition-colors">
                    <i class="fas fa-calendar-alt text-white group-hover:text-teal-500 text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-teal-700 group-hover:text-white transition-colors">Jadwal Mengajar</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Activities with Modern List -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4">
    <div class="bg-gradient-to-r from-cyan-500 to-blue-500 px-4 py-3">
        <h6 class="text-white font-semibold text-sm flex items-center">
            <i class="fas fa-history mr-2"></i>
            Aktivitas Terbaru
        </h6>
    </div>
    <div>
        <?php if (!empty($recentAbsensi)): ?>
            <div class="divide-y divide-gray-100">
                <?php foreach ($recentAbsensi as $abs): ?>
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h6 class="text-sm font-semibold text-gray-900 mb-1">
                                    <?= esc($abs['nama_kelas']) ?> - <?= esc($abs['nama_mapel']) ?>
                                </h6>
                                <div class="flex items-center text-xs text-gray-500 space-x-3">
                                    <span class="flex items-center">
                                        <i class="fas fa-calendar text-gray-400 mr-1"></i>
                                        <?= date('d/m/Y', strtotime($abs['tanggal'])) ?>
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-clock text-gray-400 mr-1"></i>
                                        <?= esc($abs['jam_mulai']) ?>
                                    </span>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 ml-2">
                                P<?= esc($abs['pertemuan_ke']) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-inbox text-gray-300 text-2xl"></i>
                </div>
                <p class="text-gray-500 text-sm font-medium">Belum Ada Aktivitas</p>
                <p class="text-gray-400 text-xs mt-1">Data akan muncul di sini</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
