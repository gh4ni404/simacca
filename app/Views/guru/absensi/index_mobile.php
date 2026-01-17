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

        <!-- Absensi List - Card Based -->
        <?php if (empty($absensi)): ?>
            <?= empty_state(
                'clipboard-list', 
                'Belum Ada Data Absensi', 
                'Mulai dengan menginput data absensi pertama Anda',
                'Input Absensi',
                base_url('guru/absensi/tambah')
            ); ?>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($absensi as $item): ?>
                    <?php
                    $percentage = isset($item['percentage']) ? $item['percentage'] : 0;
                    $hadir = isset($item['hadir']) ? $item['hadir'] : 0;
                    $total = isset($item['total_siswa']) ? $item['total_siswa'] : 0;
                    $barColor = $percentage >= 80 ? 'bg-green-500' : ($percentage >= 60 ? 'bg-yellow-500' : 'bg-red-500');
                    
                    $formatter = new IntlDateFormatter(
                        'id_ID',
                        IntlDateFormatter::FULL,
                        IntlDateFormatter::NONE,
                        'Asia/Makassar',
                        IntlDateFormatter::GREGORIAN,
                        'EEE, d MMM y'
                    );
                    $formattedDate = $formatter->format(strtotime($item['tanggal']));
                    ?>
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <!-- Header -->
                        <div class="p-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span class="flex items-center">
                                    <i class="far fa-calendar text-blue-500 mr-1"></i>
                                    <?= $formattedDate ?>
                                </span>
                                <span class="flex items-center">
                                    <i class="far fa-clock mr-1"></i>
                                    <?= date('H:i', strtotime($item['created_at'])); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-3">
                            <h3 class="text-base font-bold text-gray-900 mb-1"><?= esc($item['nama_mapel']); ?></h3>
                            <p class="text-xs text-gray-600 mb-2 flex items-center">
                                <i class="fas fa-user-tie mr-1 text-gray-400"></i>
                                <?= esc($item['nama_guru']); ?>
                            </p>

                            <!-- Info Grid -->
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <div class="flex items-center">
                                    <div class="p-1.5 bg-purple-100 rounded-lg mr-2">
                                        <i class="fas fa-school text-purple-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Kelas</p>
                                        <p class="text-sm font-semibold text-gray-900"><?= esc($item['nama_kelas']); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <div class="p-1.5 bg-blue-100 rounded-lg mr-2">
                                        <i class="fas fa-hashtag text-blue-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Pertemuan</p>
                                        <p class="text-sm font-semibold text-gray-900"><?= $item['pertemuan_ke']; ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Attendance Progress -->
                            <div class="bg-gray-50 rounded-lg p-2 mb-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium text-gray-700">Kehadiran</span>
                                    <span class="text-xs font-bold text-gray-900"><?= $hadir ?>/<?= $total ?> (<?= $percentage ?>%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="<?= $barColor ?> h-2 rounded-full transition-all" style="width: <?= $percentage ?>%"></div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="<?= base_url('guru/absensi/show/' . $item['id']); ?>"
                                    class="flex-1 px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-semibold rounded-lg text-center active:scale-95 transition-all">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                                <?php if ($item['can_edit']): ?>
                                    <a href="<?= base_url('guru/absensi/edit/' . $item['id']); ?>"
                                        class="flex-1 px-3 py-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-600 text-xs font-semibold rounded-lg text-center active:scale-95 transition-all">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                <?php endif; ?>
                                <?php if ($item['can_delete']): ?>
                                    <button onclick="confirmDelete(<?= $item['id']; ?>)"
                                        class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold rounded-lg active:scale-95 transition-all">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
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
    if (confirm('Apakah Anda yakin ingin menghapus absensi ini?\n\nCatatan: Hanya dapat dihapus dalam 24 jam setelah dibuat.')) {
        window.location.href = '<?= base_url('guru/absensi/delete/'); ?>' + id;
    }
}

function toggleFilter() {
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

// Auto-show filter if there are active filters
document.addEventListener('DOMContentLoaded', function() {
    const hasActiveFilters = <?= $tanggal || $kelasId || $search ? 'true' : 'false' ?>;
    if (hasActiveFilters) {
        toggleFilter();
    }
});
</script>
<?= $this->endSection() ?>
