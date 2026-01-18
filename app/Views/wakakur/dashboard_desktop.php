<?= $this->extend('templates/desktop_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header with Gradient Background -->
<div class="bg-gradient-to-r from-indigo-600 to-blue-500 rounded-2xl shadow-lg p-6 mb-6 text-white">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold mb-2">Dashboard Wakakur</h1>
            <div class="flex items-center space-x-4 text-indigo-100">
                <span class="flex items-center">
                    <i class="fas fa-user-tie mr-2"></i>
                    <?= esc($guru['nama_lengkap']) ?>
                </span>
                <span class="flex items-center">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Wakil Kepala Kurikulum
                </span>
            </div>
        </div>
        <div class="text-right">
            <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 backdrop-blur-sm">
                <div class="text-sm font-medium">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <?= date('l, d F Y') ?>
                </div>
                <div class="text-sm">
                    <i class="fas fa-clock mr-2"></i>
                    <?= date('H:i') ?> WIB
                </div>
            </div>
        </div>
    </div>
</div>

<?= render_flash_message() ?>

<!-- Wakakur Overview Stats with Enhanced Design -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Kelas -->
    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Kelas</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $totalKelas ?></h3>
                    <p class="text-xs text-gray-400 mt-1">Seluruh sekolah</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-school text-white text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 px-6 py-3 rounded-b-xl">
            <a href="<?= base_url('admin/kelas') ?>" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center">
                Kelola Kelas <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
    </div>
    
    <!-- Total Siswa -->
    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Siswa</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $totalSiswa ?></h3>
                    <p class="text-xs text-gray-400 mt-1">Siswa aktif</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-graduate text-white text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-green-50 px-6 py-3 rounded-b-xl">
            <a href="<?= base_url('wakakur/siswa') ?>" class="text-sm text-green-600 hover:text-green-800 font-medium flex items-center">
                Lihat Data <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
    </div>
    
    <!-- Total Guru -->
    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Guru</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $totalGuru ?></h3>
                    <p class="text-xs text-gray-400 mt-1">Tenaga pengajar</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-purple-50 px-6 py-3 rounded-b-xl">
            <a href="<?= base_url('admin/guru') ?>" class="text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center">
                Data Guru <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
    </div>
    
    <!-- Absensi Hari Ini -->
    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 mb-1">Absensi Hari Ini</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $absensiHariIni ?></h3>
                    <p class="text-xs text-gray-400 mt-1">Kelas terabsen</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-clipboard-check text-white text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-amber-50 px-6 py-3 rounded-b-xl">
            <a href="<?= base_url('wakakur/absensi') ?>" class="text-sm text-amber-600 hover:text-amber-800 font-medium flex items-center">
                Lihat Detail <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
    </div>
</div>

<!-- Role-specific sections with Modern Card Design -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Guru Mapel Stats -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h5 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-chalkboard-teacher mr-3"></i>
                Aktivitas Mengajar
            </h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 text-center border border-blue-200">
                    <div class="text-3xl font-bold text-blue-600 mb-1"><?= $totalJadwalMengajar ?></div>
                    <div class="text-sm text-gray-600 font-medium">Jadwal Mengajar</div>
                </div>
                <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-lg p-4 text-center border border-cyan-200">
                    <div class="text-3xl font-bold text-cyan-600 mb-1"><?= $kelasYangDiajar ?></div>
                    <div class="text-sm text-gray-600 font-medium">Kelas Diajar</div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-4 text-center shadow-md">
                <div class="text-3xl font-bold text-white mb-1"><?= $absensiGuru ?></div>
                <div class="text-sm text-indigo-100 font-medium">Absensi Bulan Ini</div>
            </div>
        </div>
    </div>
    
    <!-- Wali Kelas Stats (if applicable) -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
            <h5 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-user-check mr-3"></i>
                Status Wali Kelas
            </h5>
        </div>
        <div class="p-6">
            <?php if ($isWaliKelas && $kelasWali): ?>
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-school text-green-600 text-2xl mr-3"></i>
                        <div>
                            <div class="text-sm text-gray-600 font-medium">Kelas yang Diampu</div>
                            <div class="text-xl font-bold text-green-700"><?= esc($kelasWali['nama_kelas']) ?></div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 text-center border border-gray-200">
                        <div class="text-3xl font-bold text-gray-700 mb-1"><?= $statsWali['total_siswa'] ?></div>
                        <div class="text-sm text-gray-600 font-medium">Total Siswa</div>
                    </div>
                    <div class="bg-gradient-to-br from-amber-50 to-orange-100 rounded-lg p-4 text-center border border-amber-200">
                        <div class="text-3xl font-bold text-amber-600 mb-1"><?= $statsWali['izin_pending'] ?></div>
                        <div class="text-sm text-gray-600 font-medium">Izin Pending</div>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-info-circle text-gray-400 text-3xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Tidak Ditugaskan Sebagai Wali Kelas</p>
                    <p class="text-sm text-gray-400 mt-2">Anda saat ini tidak memiliki tugas sebagai wali kelas</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions with Enhanced Design -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
        <h5 class="text-lg font-semibold text-white flex items-center">
            <i class="fas fa-bolt mr-3"></i>
            Aksi Cepat
        </h5>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="<?= base_url('wakakur/absensi') ?>" 
               class="group bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-500 hover:to-blue-600 rounded-xl p-6 text-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl border-2 border-blue-200 hover:border-blue-500">
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-blue-500 group-hover:bg-white rounded-full flex items-center justify-center mb-3 transition-colors duration-300">
                        <i class="fas fa-clipboard-check text-white group-hover:text-blue-500 text-2xl"></i>
                    </div>
                    <span class="text-sm font-semibold text-blue-700 group-hover:text-white transition-colors duration-300">Kelola Absensi</span>
                </div>
            </a>
            
            <a href="<?= base_url('wakakur/laporan') ?>" 
               class="group bg-gradient-to-br from-green-50 to-green-100 hover:from-green-500 hover:to-green-600 rounded-xl p-6 text-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl border-2 border-green-200 hover:border-green-500">
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-green-500 group-hover:bg-white rounded-full flex items-center justify-center mb-3 transition-colors duration-300">
                        <i class="fas fa-chart-line text-white group-hover:text-green-500 text-2xl"></i>
                    </div>
                    <span class="text-sm font-semibold text-green-700 group-hover:text-white transition-colors duration-300">Laporan Detail</span>
                </div>
            </a>
            
            <a href="<?= base_url('wakakur/jurnal') ?>" 
               class="group bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-500 hover:to-purple-600 rounded-xl p-6 text-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl border-2 border-purple-200 hover:border-purple-500">
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-purple-500 group-hover:bg-white rounded-full flex items-center justify-center mb-3 transition-colors duration-300">
                        <i class="fas fa-book-open text-white group-hover:text-purple-500 text-2xl"></i>
                    </div>
                    <span class="text-sm font-semibold text-purple-700 group-hover:text-white transition-colors duration-300">Jurnal KBM</span>
                </div>
            </a>
            
            <?php if ($isWaliKelas): ?>
            <a href="<?= base_url('wakakur/izin') ?>" 
               class="group bg-gradient-to-br from-orange-50 to-orange-100 hover:from-orange-500 hover:to-orange-600 rounded-xl p-6 text-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl border-2 border-orange-200 hover:border-orange-500 relative">
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-orange-500 group-hover:bg-white rounded-full flex items-center justify-center mb-3 transition-colors duration-300">
                        <i class="fas fa-envelope-open-text text-white group-hover:text-orange-500 text-2xl"></i>
                    </div>
                    <span class="text-sm font-semibold text-orange-700 group-hover:text-white transition-colors duration-300">Kelola Izin</span>
                    <?php if ($statsWali['izin_pending'] > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-7 h-7 flex items-center justify-center shadow-lg animate-pulse">
                            <?= $statsWali['izin_pending'] ?>
                        </span>
                    <?php endif; ?>
                </div>
            </a>
            <?php else: ?>
            <a href="<?= base_url('wakakur/jadwal') ?>" 
               class="group bg-gradient-to-br from-teal-50 to-teal-100 hover:from-teal-500 hover:to-teal-600 rounded-xl p-6 text-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl border-2 border-teal-200 hover:border-teal-500">
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-teal-500 group-hover:bg-white rounded-full flex items-center justify-center mb-3 transition-colors duration-300">
                        <i class="fas fa-calendar-alt text-white group-hover:text-teal-500 text-2xl"></i>
                    </div>
                    <span class="text-sm font-semibold text-teal-700 group-hover:text-white transition-colors duration-300">Jadwal Mengajar</span>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Activities with Modern Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-cyan-500 to-blue-500 px-6 py-4">
        <h5 class="text-lg font-semibold text-white flex items-center">
            <i class="fas fa-history mr-3"></i>
            Aktivitas Terbaru
        </h5>
    </div>
    <div class="overflow-x-auto">
        <?php if (!empty($recentAbsensi)): ?>
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pertemuan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($recentAbsensi as $abs): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                    <?= date('d/m/Y', strtotime($abs['tanggal'])) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?= esc($abs['nama_kelas']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?= esc($abs['nama_mapel']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-gray-400 mr-2"></i>
                                    <?= esc($abs['jam_mulai']) ?> - <?= esc($abs['jam_selesai']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded bg-indigo-100 text-indigo-800">
                                    Pertemuan <?= esc($abs['pertemuan_ke']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-inbox text-gray-300 text-4xl"></i>
                </div>
                <p class="text-gray-500 font-medium text-lg">Belum Ada Aktivitas Terbaru</p>
                <p class="text-gray-400 text-sm mt-2">Data absensi akan muncul di sini</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
