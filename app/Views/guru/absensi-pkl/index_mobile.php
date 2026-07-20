<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 mb-4 rounded-lg shadow-md">
        <h1 class="text-xl font-bold mb-1">Absensi PKL</h1>
        <p class="text-sm opacity-90 flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            Kelola absensi kehadiran siswa bimbingan PKL
        </p>
    </div>

    <!-- Mobile Floating Action Button -->
    <a href="<?= base_url('guru/absensi-pkl/tambah'); ?>"
        class="fixed bottom-20 right-4 z-50 flex items-center justify-center w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full shadow-2xl active:scale-95 transition-all"
        title="Input Absensi Baru">
        <i class="fas fa-plus text-2xl"></i>
    </a>

    <!-- Flash Messages -->
    <?= render_flash_message() ?>

    <div class="px-4">
        <!-- Stats Cards - 2 Column Grid -->
        <div class="grid grid-cols-2 gap-3 mb-6">
            <?= stat_card('Total', count($absensi), 'clipboard-list', 'blue', '', '', 'compact'); ?>
            <?= stat_card('Hadir', array_sum(array_column($absensi, 'hadir_count')), 'user-check', 'green', '', '', 'compact'); ?>
            <?= stat_card('Izin', array_sum(array_column($absensi, 'izin_count')), 'file-alt', 'blue', '', '', 'compact'); ?>
            <?= stat_card('Sakit', array_sum(array_column($absensi, 'sakit_count')), 'medkit', 'yellow', '', '', 'compact'); ?>
            <?= stat_card('Alpa', array_sum(array_column($absensi, 'alpa_count')), 'user-times', 'red', '', '', 'compact'); ?>
            <?= stat_card('Dispen', array_sum(array_column($absensi, 'dispen_count')), 'id-badge', 'purple', '', '', 'compact'); ?>
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
                            value="<?= esc($tanggal ?? '') ?>"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg active:scale-95 transition-all">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>
                        <a href="<?= base_url('guru/absensi-pkl'); ?>"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-lg active:scale-95 transition-all">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Absensi List -->
        <?php if (empty($absensi)): ?>
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-md p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 mb-4">
                    <i class="fas fa-clipboard-list text-4xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Data Absensi PKL</h3>
                <p class="text-gray-600 text-sm mb-4">Mulai dengan menginput data absensi kehadiran siswa PKL.</p>
                <a href="<?= base_url('guru/absensi-pkl/tambah') ?>" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold shadow-lg active:scale-95 transition-all">
                    <i class="fas fa-plus-circle mr-2 text-lg"></i>
                    Input Absensi Pertama
                </a>
            </div>
        <?php else: ?>
            <!-- Absensi Cards -->
            <div class="space-y-4">
                <?php $no = 1; foreach ($absensi as $item): ?>
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-5 active:shadow-md transition-all duration-200">
                        <!-- Card Header -->
                        <div class="border-b border-gray-200 pb-3 mb-3">
                            <div class="flex items-start justify-between mb-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-calendar-day text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900"><?= date('d/m/Y', strtotime($item['tanggal'])) ?></p>
                                        <p class="text-xs text-gray-500"><?= date('l', strtotime($item['tanggal'])) ?></p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-xl text-sm font-semibold flex-shrink-0">
                                    <?= $item['total_siswa'] ?? 0 ?> Siswa
                                </span>
                            </div>
                            <div class="mt-2">
                                <p class="text-sm font-semibold text-gray-800"><?= esc($item['nama_perusahaan'] ?? '-') ?></p>
                                <?php if (!empty($item['kota'])): ?>
                                    <p class="text-xs text-gray-500"><?= esc($item['kota']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Status Badges -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-800 rounded-lg text-xs font-bold">
                                <i class="fas fa-check-circle mr-1"></i> Hadir <?= $item['hadir_count'] ?? 0 ?>
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 bg-blue-100 text-blue-800 rounded-lg text-xs font-bold">
                                <i class="fas fa-file-alt mr-1"></i> Izin <?= $item['izin_count'] ?? 0 ?>
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 bg-yellow-100 text-yellow-800 rounded-lg text-xs font-bold">
                                <i class="fas fa-medkit mr-1"></i> Sakit <?= $item['sakit_count'] ?? 0 ?>
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-800 rounded-lg text-xs font-bold">
                                <i class="fas fa-times-circle mr-1"></i> Alpa <?= $item['alpa_count'] ?? 0 ?>
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 bg-purple-100 text-purple-800 rounded-lg text-xs font-bold">
                                <i class="fas fa-id-badge mr-1"></i> Dispen <?= $item['dispen_count'] ?? 0 ?>
                            </span>
                        </div>

                        <!-- Kehadiran -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Kehadiran</span>
                                <?php
                                $persen = $item['persen_kehadiran'] ?? 0;
                                $colorClass = $persen >= 80 ? 'green' : ($persen >= 60 ? 'yellow' : 'red');
                                ?>
                                <span class="text-lg font-bold text-<?= $colorClass ?>-600"><?= $persen ?>%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-<?= $colorClass ?>-500 to-<?= $colorClass ?>-400 h-2.5 rounded-full transition-all duration-300"
                                    style="width: <?= $persen ?>%"></div>
                            </div>
                        </div>

                        <!-- Footer with Action -->
                        <div class="pt-3 border-t border-gray-100 flex gap-2">
                            <a href="<?= base_url('guru/absensi-pkl/show/' . $item['id']) ?>"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl active:scale-98 transition-all shadow-sm">
                                <i class="fas fa-eye"></i>
                                <span>Detail</span>
                            </a>
                            <?php if ($item['can_edit']): ?>
                            <a href="<?= base_url('guru/absensi-pkl/edit/' . $item['id']) ?>"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-xl active:scale-98 transition-all shadow-sm">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </a>
                            <?php endif; ?>
                            <?php if ($item['can_delete']): ?>
                            <button onclick="confirmDelete(<?= $item['id'] ?>)"
                                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl active:scale-95 transition-all shadow-sm">
                                <i class="fas fa-trash-alt"></i>
                                <span>Hapus</span>
                            </button>
                            <?php endif; ?>
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
    function toggleFilter() {
        const form = document.getElementById('filterForm');
        const icon = document.getElementById('filterToggleIcon');
        form.classList.toggle('hidden');
        icon.style.transform = form.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus absensi ini?')) {
            window.location.href = '<?= base_url('guru/absensi-pkl/hapus/') ?>' + id;
        }
    }
</script>
<?= $this->endSection() ?>
