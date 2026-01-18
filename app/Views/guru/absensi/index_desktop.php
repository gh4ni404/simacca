<?= $this->extend('templates/desktop_layout') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Manajemen Absensi
                    </span>
                </h1>
                <p class="text-base text-gray-600 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Kelola data absensi siswa dengan mudah dan efisien
                </p>
            </div>
            <div>
                <a href="<?= base_url('guru/absensi/tambah'); ?>"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-plus-circle mr-2 text-lg"></i>
                    <span>Input Absensi Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= render_flash_message() ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <?= stat_card('Total Absensi', $stats['total'], 'clipboard-list', 'blue', '', '<i class="fas fa-database mr-1"></i>Semua data'); ?>
        <?= stat_card('Hadir', $stats['hadir'], 'user-check', 'green', '', '<i class="fas fa-check-circle mr-1"></i>Kehadiran'); ?>
        <?= stat_card('Izin', $stats['izin'], 'file-alt', 'yellow', '', '<i class="fas fa-envelope mr-1"></i>Dengan keterangan'); ?>
        <?= stat_card('Alpa', $stats['alpa'], 'user-times', 'red', '', '<i class="fas fa-exclamation-circle mr-1"></i>Tanpa keterangan'); ?>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-lg mb-8 overflow-hidden">
        <div class="flex items-center p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <div class="p-2 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg mr-3">
                <i class="fas fa-filter text-white"></i>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Filter Data</h2>
        </div>

        <form method="get" class="p-6">
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                        Tanggal
                    </label>
                    <input type="date"
                        name="tanggal"
                        value="<?= $tanggal ?>"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-school mr-2 text-purple-500"></i>
                        Kelas
                    </label>
                    <select name="kelas_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <?php foreach ($kelasOptions as $id => $nama): ?>
                            <option value="<?= $id; ?>" <?= $kelasId == $id ? 'selected' : ''; ?>>
                                <?= $nama; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-search mr-2 text-green-500"></i>
                        Cari Mata Pelajaran
                    </label>
                    <input type="text"
                        name="search"
                        value="<?= $search ?>"
                        placeholder="Ketik nama mata pelajaran..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                    <i class="fas fa-search mr-2"></i>
                    Filter Data
                </button>
                <a href="<?= base_url('guru/absensi'); ?>"
                    class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-all">
                    <i class="fas fa-redo mr-2"></i>
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    <!-- Kelas List - Summary Cards -->
    <?php if (empty($kelasSummary)): ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden p-16">
            <?= empty_state(
                'clipboard-list',
                'Belum Ada Data Absensi',
                'Mulai dengan menginput data absensi pertama Anda',
                'Input Absensi Pertama',
                base_url('guru/absensi/tambah')
            ); ?>
        </div>
    <?php else: ?>
        <!-- Kelas Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($kelasSummary as $kelas): ?>
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <!-- Kelas Header -->
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                        <div class="flex items-center text-white">
                            <div class="p-3 bg-white bg-opacity-20 rounded-xl mr-3">
                                <i class="fas fa-school text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold"><?= esc($kelas['kelas_nama']); ?></h3>
                                <p class="text-sm opacity-90"><?= $kelas['total_siswa']; ?> siswa</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="p-6">
                        <!-- Mata Pelajaran -->
                        <div class="mb-4">
                            <p class="text-xs text-gray-500 mb-2">Mata Pelajaran</p>
                            <div class="flex flex-wrap gap-1">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-book mr-1"></i>
                                    <?= esc($kelas['mata_pelajaran']); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-blue-50 rounded-xl p-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-600 mb-1">Pertemuan</p>
                                        <p class="text-2xl font-bold text-blue-600"><?= $kelas['total_pertemuan']; ?></p>
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
                                        <p class="text-2xl font-bold text-green-600"><?= $kelas['avg_kehadiran']; ?>%</p>
                                    </div>
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <i class="fas fa-user-check text-green-600"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Last Absensi -->
                        <?php if ($kelas['last_absensi']): ?>
                            <div class="bg-gray-50 rounded-xl p-3 mb-4">
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
                            class="block w-full px-4 py-3 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white text-center font-semibold rounded-xl transition-all transform hover:scale-105 shadow-md">
                            <i class="fas fa-eye mr-2"></i>
                            Lihat Detail Pertemuan
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus absensi ini?\n\nCatatan: Hanya dapat dihapus dalam 24 jam setelah dibuat.')) {
            window.location.href = '<?= base_url('guru/absensi/delete/'); ?>' + id;
        }
    }
</script>
<?= $this->endSection() ?>