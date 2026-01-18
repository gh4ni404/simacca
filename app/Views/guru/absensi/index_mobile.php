<?= $this->extend('templates/mobile_layout') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 mb-4 rounded-lg mx-4 shadow-md">
        <h1 class="text-xl font-bold mb-1">Manajemen Absensi</h1>
        <p class="text-sm opacity-90 flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            Kelola data absensi siswa
        </p>
    </div>

    <!-- Mobile Floating Action Button -->
    <a href="<?= base_url('guru/absensi/tambah'); ?>"
        class="fixed bottom-20 right-4 z-50 flex items-center justify-center w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full shadow-2xl active:scale-95 transition-all"
        title="Tambah Absensi">
        <i class="fas fa-plus text-2xl"></i>
    </a>

    <!-- Flash Messages -->
    <?= render_flash_message() ?>

    <div class="px-4">
        <!-- Stats Cards - 2 Column Grid -->
        <div class="grid grid-cols-2 gap-3 mb-6">
            <?= stat_card('Total', $stats['total'], 'clipboard-list', 'blue', '', '', 'compact'); ?>
            <?= stat_card('Hadir', $stats['hadir'], 'user-check', 'green', '', '', 'compact'); ?>
            <?= stat_card('Izin', $stats['izin'], 'file-alt', 'yellow', '', '', 'compact'); ?>
            <?= stat_card('Alpa', $stats['alpa'], 'user-times', 'red', '', '', 'compact'); ?>
        </div>

        <!-- Filter Section - Collapsible -->
        <div class="bg-white rounded-xl shadow-md mb-4 overflow-hidden">
            <div class="flex items-center justify-between p-4 bg-gray-50 cursor-pointer"
                onclick="toggleFilter()" id="filterHeader">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-500 rounded-lg mr-3">
                        <i class="fas fa-filter text-white text-sm"></i>
                    </div>
                    <h2 class="text-base font-semibold text-gray-800">Filter Data</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-600 transition-transform duration-300" id="filterToggleIcon"></i>
            </div>

            <form method="get" class="hidden p-4 pt-0" id="filterForm">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            Tanggal
                        </label>
                        <input type="date"
                            name="tanggal"
                            value="<?= $tanggal ?>"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-school mr-2 text-purple-500"></i>
                            Kelas
                        </label>
                        <select name="kelas_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <?php foreach ($kelasOptions as $id => $nama): ?>
                                <option value="<?= $id; ?>" <?= $kelasId == $id ? 'selected' : ''; ?>>
                                    <?= $nama; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-search mr-2 text-green-500"></i>
                            Cari
                        </label>
                        <input type="text"
                            name="search"
                            value="<?= $search ?>"
                            placeholder="Cari mata pelajaran..."
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg active:scale-95 transition-all">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>
                        <a href="<?= base_url('guru/absensi'); ?>"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-lg active:scale-95 transition-all">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Kelas List - Summary Cards -->
        <?php if (empty($kelasSummary)): ?>
            <?= empty_state(
                'clipboard-list',
                'Belum Ada Data Absensi',
                'Mulai dengan menginput data absensi pertama Anda',
                'Input Absensi',
                base_url('guru/absensi/tambah')
            ); ?>
        <?php else: ?>
            <!-- Kelas Cards -->
            <div class="space-y-4">
                <?php foreach ($kelasSummary as $kelas): ?>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                        <!-- Kelas Header -->
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-4 py-4">
                            <div class="flex items-center text-white">
                                <div class="p-2 bg-white bg-opacity-20 rounded-lg mr-3">
                                    <i class="fas fa-school text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold"><?= esc($kelas['kelas_nama']); ?></h3>
                                    <p class="text-xs opacity-90"><?= $kelas['total_siswa']; ?> siswa</p>
                                </div>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="p-4">
                            <!-- Mata Pelajaran -->
                            <div class="mb-3">
                                <p class="text-xs text-gray-500 mb-2">Mata Pelajaran</p>
                                <div class="flex flex-wrap gap-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-book mr-1"></i>
                                        <?= esc($kelas['mata_pelajaran']); ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div class="bg-blue-50 rounded-xl p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs text-gray-600 mb-1">Pertemuan</p>
                                            <p class="text-xl font-bold text-blue-600"><?= $kelas['total_pertemuan']; ?></p>
                                        </div>
                                        <div class="p-2 bg-blue-100 rounded-lg">
                                            <i class="fas fa-hashtag text-blue-600"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-green-50 rounded-xl p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs text-gray-600 mb-1">Kehadiran</p>
                                            <p class="text-xl font-bold text-green-600"><?= $kelas['avg_kehadiran']; ?>%</p>
                                        </div>
                                        <div class="p-2 bg-green-100 rounded-lg">
                                            <i class="fas fa-user-check text-green-600"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Last Absensi -->
                            <?php if ($kelas['last_absensi']): ?>
                                <div class="bg-gray-50 rounded-xl p-3 mb-3">
                                    <p class="text-xs text-gray-600 mb-1">Absensi Terakhir</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        <?php
                                        $formatter = new IntlDateFormatter(
                                            'id_ID',
                                            IntlDateFormatter::LONG,
                                            IntlDateFormatter::NONE,
                                            'Asia/Makassar',
                                            IntlDateFormatter::GREGORIAN,
                                            'd MMMM y'
                                        );
                                        echo $formatter->format(strtotime($kelas['last_absensi']));
                                        ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <!-- Action Button -->
                            <a href="<?= base_url('guru/absensi/kelas/' . $kelas['kelas_id']); ?>" 
                                class="block w-full px-4 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-center font-semibold rounded-xl transition-all active:scale-95 shadow-md">
                                <i class="fas fa-eye mr-2"></i>
                                Lihat Detail Pertemuan
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function confirmDelete(id) {
        if (confirm('Yakin mau hapus absen ini? Data nggak bisa dikembalikan lho!')) {
            window.location.href = '<?= base_url('guru/absensi/delete/'); ?>' + id;
        }
    }
</script>
<?= $this->endSection() ?>