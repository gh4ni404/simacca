<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <!-- Header Section -->
    <div class="mb-6 md:mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 flex items-center">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Manajemen Absensi
                    </span>
                </h1>
                <p class="text-sm md:text-base text-gray-600 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Kelola data absensi siswa dengan mudah dan efisien
                </p>
            </div>
            <!-- Desktop Button -->
            <div class="hidden md:block">
                <a href="<?= base_url('guru/absensi/tambah'); ?>"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-plus-circle mr-2 text-lg"></i>
                    <span>Input Absensi Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Floating Action Button -->
    <a href="<?= base_url('guru/absensi/tambah'); ?>"
        class="md:hidden fixed bottom-6 right-6 z-50 flex items-center justify-center w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-full shadow-2xl hover:shadow-3xl transform hover:scale-110 transition-all duration-200"
        title="Tambah Absensi">
        <i class="fas fa-plus text-2xl"></i>
    </a>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6 md:mb-8">
        <!-- Total Absensi Card -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-md md:shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
            <div class="p-3 md:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex-1">
                        <p class="text-xs md:text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">Total</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-800"><?= $stats['total']; ?></p>
                        <p class="text-xs text-gray-400 mt-0.5 md:mt-1 hidden md:block">Semua data</p>
                    </div>
                    <div class="absolute top-2 right-2 md:relative md:flex-shrink-0 md:ml-4">
                        <div class="p-2 md:p-4 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 shadow-lg">
                            <i class="fas fa-clipboard-list text-white text-sm md:text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-blue-400 to-blue-600"></div>
        </div>

        <!-- Hadir Card -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-md md:shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
            <div class="p-3 md:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex-1">
                        <p class="text-xs md:text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">Hadir</p>
                        <p class="text-2xl md:text-3xl font-bold text-green-600"><?= $stats['hadir']; ?></p>
                        <p class="text-xs text-gray-400 mt-0.5 md:mt-1 hidden md:block">Siswa hadir</p>
                    </div>
                    <div class="absolute top-2 right-2 md:relative md:flex-shrink-0 md:ml-4">
                        <div class="p-2 md:p-4 rounded-full bg-gradient-to-br from-green-400 to-green-600 shadow-lg">
                            <i class="fas fa-user-check text-white text-sm md:text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-green-400 to-green-600"></div>
        </div>

        <!-- Izin & Sakit Card -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-md md:shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
            <div class="p-3 md:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex-1">
                        <p class="text-xs md:text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">Izin</p>
                        <p class="text-2xl md:text-3xl font-bold text-yellow-600"><?= $stats['izin'] + $stats['sakit']; ?></p>
                        <p class="text-xs text-gray-400 mt-0.5 md:mt-1">I: <?= $stats['izin']; ?>, S: <?= $stats['sakit']; ?></p>
                    </div>
                    <div class="absolute top-2 right-2 md:relative md:flex-shrink-0 md:ml-4">
                        <div class="p-2 md:p-4 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 shadow-lg">
                            <i class="fas fa-user-clock text-white text-sm md:text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-yellow-400 to-yellow-600"></div>
        </div>

        <!-- Alpa Card -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-md md:shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
            <div class="p-3 md:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex-1">
                        <p class="text-xs md:text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">Alpa</p>
                        <p class="text-2xl md:text-3xl font-bold text-red-600"><?= $stats['alpa']; ?></p>
                        <p class="text-xs text-gray-400 mt-0.5 md:mt-1">Tanpa keterangan</p>
                    </div>
                    <div class="absolute top-2 right-2 md:relative md:flex-shrink-0 md:ml-4">
                        <div class="p-2 md:p-4 rounded-full bg-gradient-to-br from-red-400 to-red-600 shadow-lg">
                            <i class="fas fa-user-times text-white text-sm md:text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-red-400 to-red-600"></div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl md:rounded-2xl shadow-md md:shadow-lg mb-6 md:mb-8 overflow-hidden">
        <!-- Filter Header - Collapsible on Mobile -->
        <div class="flex items-center justify-between p-4 md:p-6 bg-gradient-to-r from-gray-50 to-gray-100 md:bg-white cursor-pointer md:cursor-default" 
             onclick="toggleFilter()" id="filterHeader">
            <div class="flex items-center">
                <div class="p-2 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg mr-3">
                    <i class="fas fa-filter text-white text-sm md:text-base"></i>
                </div>
                <h2 class="text-base md:text-lg font-semibold text-gray-800">Filter Data</h2>
            </div>
            <!-- Mobile toggle icon -->
            <div class="md:hidden">
                <i class="fas fa-chevron-down text-gray-600 transition-transform duration-300" id="filterToggleIcon"></i>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="get" class="hidden md:block p-4 md:p-6 pt-0 md:pt-4" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4 mb-3 md:mb-4">
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1 md:mb-2 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-blue-500 text-xs md:text-sm"></i>
                        Tanggal
                    </label>
                    <input type="date"
                        name="tanggal"
                        value="<?= $tanggal ?>"
                        class="w-full px-3 md:px-4 py-2 md:py-2.5 text-sm md:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1 md:mb-2 flex items-center">
                        <i class="fas fa-school mr-2 text-green-500 text-xs md:text-sm"></i>
                        Kelas
                    </label>
                    <select name="kelas_id"
                        class="w-full px-3 md:px-4 py-2 md:py-2.5 text-sm md:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="">Semua Kelas</option>
                        <?php foreach ($kelasOptions as $id => $nama): ?>
                            <option value="<?= $id; ?>" <?= $kelasId == $id ? 'selected' : ''; ?>>
                                <?= $nama; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-1 md:mb-2 flex items-center">
                        <i class="fas fa-search mr-2 text-purple-500 text-xs md:text-sm"></i>
                        Pencarian
                    </label>
                    <input type="text"
                        name="search"
                        value="<?= $search ?>"
                        placeholder="Cari mapel atau kelas..."
                        class="w-full px-3 md:px-4 py-2 md:py-2.5 text-sm md:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
            </div>
            <div class="flex flex-wrap gap-2 md:gap-3">
                <button type="submit"
                    class="flex-1 md:flex-none inline-flex items-center justify-center px-4 md:px-6 py-2 md:py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm md:text-base font-medium rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <i class="fas fa-filter mr-2"></i> Terapkan
                </button>
                <?php if ($tanggal || $kelasId || $search): ?>
                    <a href="<?= base_url('guru/absensi'); ?>"
                        class="flex-1 md:flex-none inline-flex items-center justify-center px-4 md:px-6 py-2 md:py-2.5 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm md:text-base font-medium rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-redo mr-2"></i> Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Desktop View: Table Section -->
    <div class="hidden md:block bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list-ul mr-2 text-blue-500"></i>
                Daftar Data Absensi
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Pertemuan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kehadiran</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($absensi)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="p-6 bg-gray-100 rounded-full mb-4">
                                        <i class="fas fa-clipboard-list text-6xl text-gray-400"></i>
                                    </div>
                                    <p class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Data Absensi</p>
                                    <p class="text-gray-500 mb-4">Mulai dengan menginput data absensi pertama Anda</p>
                                    <a href="<?= base_url('guru/absensi/tambah'); ?>"
                                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                                        <i class="fas fa-plus-circle mr-2"></i> Input Absensi Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($absensi as $item): ?>
                            <tr class="hover:bg-blue-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-700"><?= $no++; ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                            <i class="fas fa-calendar-day text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">
                                                <?php
                                                // 1. Buat alat pengaturnya (formatter)
                                                $formatter = new IntlDateFormatter(
                                                    'id_ID',
                                                    IntlDateFormatter::FULL,
                                                    IntlDateFormatter::NONE,
                                                    'Asia/Makassar',
                                                    IntlDateFormatter::GREGORIAN,
                                                    'EEEE, d MMMM y'
                                                );

                                                // 2. Gunakan alat tersebut untuk mencetak tanggal
                                                ?>
                                                <?= $formatter->format(strtotime($item['tanggal'])); ?>
                                            </div>
                                            <div class="text-xs text-gray-500 flex items-center mt-0.5">
                                                <i class="far fa-clock mr-1"></i>
                                                <?= date('H:i', strtotime($item['created_at'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900"><?= $item['nama_mapel']; ?></div>
                                    <div class="text-xs text-gray-500 flex items-center mt-0.5">
                                        <i class="fas fa-user-tie mr-1"></i>
                                        <?= $item['nama_guru']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-school mr-1.5"></i>
                                        <?= $item['nama_kelas']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm">
                                        <i class="fas fa-hashtag mr-1"></i>
                                        <?= $item['pertemuan_ke']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    // Get stats from item if available
                                    $percentage = isset($item['percentage']) ? $item['percentage'] : 0;
                                    $hadir = isset($item['hadir']) ? $item['hadir'] : 0;
                                    $total = isset($item['total_siswa']) ? $item['total_siswa'] : 0;
                                    $barColor = $percentage >= 80 ? 'bg-green-500' : ($percentage >= 60 ? 'bg-yellow-500' : 'bg-red-500');
                                    ?>
                                    <div class="w-32">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-medium text-gray-700"><?= $hadir ?>/<?= $total ?></span>
                                            <span class="text-xs font-bold text-gray-900"><?= $percentage ?>%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                            <div class="<?= $barColor ?> h-2.5 rounded-full transition-all duration-300" style="width: <?= $percentage ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center space-x-2">
                                        
                                        <?php if (is_absensi_editable($item)): ?>
                                            <a href="<?= base_url('guru/absensi/edit/' . $item['id']); ?>"
                                                class="p-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-600 rounded-lg transition-all transform hover:scale-110" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('guru/absensi/print/' . $item['id']); ?>"
                                            class="p-2 bg-purple-100 hover:bg-purple-200 text-purple-600 rounded-lg transition-all transform hover:scale-110" title="Cetak" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <?php if (is_absensi_editable($item)): ?>
                                            <a href="#"
                                                onclick="confirmDelete('<?= $item['id']; ?>')"
                                                class="p-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-all transform hover:scale-110" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile View: Cards -->
    <div class="md:hidden space-y-4">
        <?php if (empty($absensi)): ?>
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <div class="flex flex-col items-center justify-center">
                    <div class="p-6 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-clipboard-list text-6xl text-gray-400"></i>
                    </div>
                    <p class="text-xl font-semibold text-gray-600 mb-2 text-center">Belum Ada Data Absensi</p>
                    <p class="text-gray-500 mb-4 text-center">Mulai dengan menginput data absensi pertama Anda</p>
                    <a href="<?= base_url('guru/absensi/tambah'); ?>"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-plus-circle mr-2"></i> Input Absensi Pertama
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($absensi as $item): ?>
                <?php
                // Get stats from item if available
                $percentage = isset($item['percentage']) ? $item['percentage'] : 0;
                $hadir = isset($item['hadir']) ? $item['hadir'] : 0;
                $total = isset($item['total_siswa']) ? $item['total_siswa'] : 0;
                $barColor = $percentage >= 80 ? 'bg-green-500' : ($percentage >= 60 ? 'bg-yellow-500' : 'bg-red-500');
                $textColor = $percentage >= 80 ? 'text-green-600' : ($percentage >= 60 ? 'text-yellow-600' : 'text-red-600');
                
                // Format date
                $formatter = new IntlDateFormatter(
                    'id_ID',
                    IntlDateFormatter::FULL,
                    IntlDateFormatter::NONE,
                    'Asia/Makassar',
                    IntlDateFormatter::GREGORIAN,
                    'EEEE, d MMM y'
                );
                $formattedDate = $formatter->format(strtotime($item['tanggal']));
                ?>
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-all overflow-hidden border border-gray-100">
                    <!-- Header -->
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="far fa-calendar text-blue-500 mr-2"></i>
                                <span class="font-medium"><?= $formattedDate ?></span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="far fa-clock mr-1"></i>
                                <span><?= date('H:i', strtotime($item['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <!-- Mata Pelajaran -->
                        <h3 class="text-lg font-bold text-gray-900 mb-1"><?= $item['nama_mapel']; ?></h3>
                        <p class="text-sm text-gray-600 mb-3 flex items-center">
                            <i class="fas fa-user-tie mr-2 text-gray-400"></i>
                            <?= $item['nama_guru']; ?>
                        </p>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-100 rounded-lg mr-2">
                                    <i class="fas fa-school text-purple-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Kelas</p>
                                    <p class="text-sm font-semibold text-gray-800"><?= $item['nama_kelas']; ?></p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg mr-2">
                                    <i class="fas fa-hashtag text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Pertemuan</p>
                                    <p class="text-sm font-semibold text-gray-800">#<?= $item['pertemuan_ke']; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Kehadiran Stats -->
                        <div class="bg-gray-50 rounded-xl p-3 mb-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-medium text-gray-600">Kehadiran</span>
                                <span class="text-sm font-bold <?= $textColor ?>"><?= $hadir ?> / <?= $total ?> (<?= $percentage ?>%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="<?= $barColor ?> h-2 rounded-full transition-all duration-300" style="width: <?= $percentage ?>%"></div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <a href="<?= base_url('guru/absensi/print/' . $item['id']); ?>"
                                class="flex-1 flex items-center justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-all"
                                target="_blank">
                                <i class="fas fa-print mr-2"></i>
                                <span>Cetak</span>
                            </a>
                            <?php if (is_absensi_editable($item)): ?>
                                <a href="<?= base_url('guru/absensi/edit/' . $item['id']); ?>"
                                    class="flex-1 flex items-center justify-center px-4 py-2.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 font-medium rounded-lg transition-all">
                                    <i class="fas fa-edit mr-2"></i>
                                    <span>Edit</span>
                                </a>
                                <button onclick="confirmDelete('<?= $item['id']; ?>')"
                                    class="px-4 py-2.5 bg-red-100 hover:bg-red-200 text-red-600 font-medium rounded-lg transition-all">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus absensi ini?\n\nCatatan: Hanya dapat dihapus dalam 24 jam setelah dibuat.')) {
            window.location.href = '<?= base_url('guru/absensi/delete/'); ?>' + id;
        }
    }

    // Toggle filter on mobile
    function toggleFilter() {
        if (window.innerWidth < 768) { // Only on mobile
            const filterForm = document.getElementById('filterForm');
            const toggleIcon = document.getElementById('filterToggleIcon');
            
            if (filterForm.classList.contains('hidden')) {
                filterForm.classList.remove('hidden');
                toggleIcon.classList.add('rotate-180');
            } else {
                filterForm.classList.add('hidden');
                toggleIcon.classList.remove('rotate-180');
            }
        }
    }

    // Auto-show filter if there are active filters
    document.addEventListener('DOMContentLoaded', function() {
        const hasActiveFilters = <?= $tanggal || $kelasId || $search ? 'true' : 'false' ?>;
        if (hasActiveFilters && window.innerWidth < 768) {
            toggleFilter();
        }
    });
</script>
<?= $this->endSection() ?>