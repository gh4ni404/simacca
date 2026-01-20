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
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-md p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 mb-4">
                    <i class="fas fa-clipboard-list text-4xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Data Absensi</h3>
                <p class="text-gray-600 text-sm mb-4">Mulai dengan menginput data absensi pertama Anda untuk kelas yang Anda ajar.</p>
                <a href="<?= base_url('guru/absensi/tambah') ?>" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold shadow-lg active:scale-95 transition-all">
                    <i class="fas fa-plus-circle mr-2 text-lg"></i>
                    Input Absensi
                </a>
            </div>
        <?php else: ?>
            <!-- Kelas Cards -->
            <div class="space-y-4">
                <?php foreach ($kelasSummary as $kelas): ?>
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-5 active:shadow-md transition-all duration-200">
                        <!-- Card Header -->
                        <div class="border-b border-gray-300 pb-2 mb-3">
                            <div class="flex items-start justify-between mb-1">
                                <div class="flex-1 mr-3">
                                    <h3 class="text-lg font-bold text-gray-900 mb-1"><?= esc($kelas['mata_pelajaran']) ?></h3>
                                </div>
                                <span class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-xl text-sm font-semibold flex-shrink-0">
                                    <?= esc($kelas['kelas_nama']) ?>
                                </span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <?php if (!empty($kelas['hari'])): ?>
                                        <i class="far fa-clock"></i>
                                        <span class="font-medium"><?= esc($kelas['hari']) ?></span>
                                        <span class="text-gray-300">•</span>
                                    <?php endif; ?>
                                    <span><?= substr($kelas['jam_mulai'] ?? '00:00', 0, 5) ?> - <?= substr($kelas['jam_selesai'] ?? '00:00', 0, 5) ?></span>
                                    <span class="text-gray-300">•</span>
                                    <span>Pertemuan #<?= $kelas['total_pertemuan'] ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Kehadiran Section -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Kehadiran Siswa</span>
                                <span class="text-lg font-bold text-emerald-600"><?= $kelas['avg_kehadiran'] ?>%</span>
                            </div>
                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-emerald-500 to-emerald-400 h-2.5 rounded-full transition-all duration-300"
                                    style="width: <?= $kelas['avg_kehadiran'] ?>%"></div>
                            </div>
                            <div class="mt-1.5 text-xs text-gray-400">
                                <?= $kelas['total_siswa'] ?> siswa terdaftar
                            </div>
                        </div>

                        <!-- Footer with Action -->
                        <div class="pt-3 border-t border-gray-100">
                            <a href="<?= base_url('guru/absensi/kelas/' . $kelas['kelas_id']) ?>"
                                class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl active:scale-98 transition-all shadow-sm">
                                <i class="fas fa-eye"></i>
                                <span>Lihat Detail</span>
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