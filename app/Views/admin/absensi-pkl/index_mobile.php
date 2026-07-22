<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 mb-4 rounded-b-lg mx-0 shadow-md">
        <h1 class="text-xl font-bold mb-1">
            <i class="fas fa-clipboard-check mr-2"></i> Monitoring Absensi PKL
        </h1>
        <p class="text-sm opacity-90">Pantau kehadiran siswa PKL seluruh pembimbing</p>
    </div>

    <div class="px-4">
        <?= render_flash_message() ?>

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
                            <i class="fas fa-user-tie mr-2 text-purple-500"></i>
                            Pembimbing
                        </label>
                        <select name="pembimbing_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Pembimbing</option>
                            <?php foreach ($pembimbingOptions as $id => $nama): ?>
                            <option value="<?= $id ?>" <?= ($filters['pembimbing_id'] ?? '') == $id ? 'selected' : '' ?>><?= esc($nama) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            Dari Tanggal
                        </label>
                        <input type="date" name="date_from" value="<?= esc($filters['date_from'] ?? '') ?>"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            Sampai Tanggal
                        </label>
                        <input type="date" name="date_to" value="<?= esc($filters['date_to'] ?? '') ?>"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg active:scale-95 transition-all">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>
                        <a href="<?= base_url('admin/absensi-pkl'); ?>"
                           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-lg active:scale-95 transition-all">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Stats Grid -->
        <?php
        $totalKehadiran = $globalStats['total'] ?? 0;
        $totalHadir = $globalStats['hadir'] ?? 0;
        $totalIzin = $globalStats['izin'] ?? 0;
        $totalSakit = $globalStats['sakit'] ?? 0;
        $totalAlpa = $globalStats['alpa'] ?? 0;
        $persenGlobal = $globalStats['persen_kehadiran'] ?? 0;
        ?>
        <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="bg-white rounded-xl shadow-md p-3 text-center border-t-2 border-blue-500">
                <p class="text-xs text-gray-500">Total</p>
                <p class="text-xl font-bold text-gray-800"><?= $totalKehadiran ?></p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-3 text-center border-t-2 border-green-500">
                <p class="text-xs text-gray-500">Hadir</p>
                <p class="text-xl font-bold text-green-600"><?= $totalHadir ?></p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-3 text-center border-t-2 border-blue-500">
                <p class="text-xs text-gray-500">Izin</p>
                <p class="text-xl font-bold text-blue-600"><?= $totalIzin ?></p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-3 text-center border-t-2 border-yellow-500">
                <p class="text-xs text-gray-500">Sakit</p>
                <p class="text-xl font-bold text-yellow-600"><?= $totalSakit ?></p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-3 text-center border-t-2 border-red-500">
                <p class="text-xs text-gray-500">Alpa</p>
                <p class="text-xl font-bold text-red-600"><?= $totalAlpa ?></p>
            </div>
        </div>

        <!-- Kehadiran Global -->
        <div class="bg-white rounded-xl shadow-md p-4 mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-chart-line text-emerald-500 mr-2"></i> Kehadiran Global
                </span>
                <strong class="text-lg <?= $persenGlobal >= 80 ? 'text-green-600' : ($persenGlobal >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $persenGlobal ?>%</strong>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full transition-all duration-300"
                     style="width: <?= $persenGlobal ?>%"></div>
            </div>
        </div>

        <!-- Rekap per Pembimbing -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-4">
            <div class="bg-gradient-to-r from-purple-500 to-blue-600 px-4 py-3">
                <h2 class="text-white font-bold text-sm flex items-center">
                    <i class="fas fa-user-tie mr-2"></i>
                    Rekap per Pembimbing (<?= count($rekapPembimbing) ?>)
                </h2>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if (empty($rekapPembimbing)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-user-tie text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Belum ada data pembimbing</p>
                </div>
                <?php else: ?>
                    <?php foreach ($rekapPembimbing as $rp): ?>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center mr-3">
                                    <span class="text-white font-bold text-sm"><?= substr($rp['nama_pembimbing'], 0, 1) ?></span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900"><?= esc($rp['nama_pembimbing']) ?></p>
                                    <p class="text-xs text-gray-500"><?= esc($rp['nama_perusahaan'] ?? '-') ?></p>
                                </div>
                            </div>
                            <?php
                            $persen = $rp['persen_kehadiran'] ?? 0;
                            $badgeColor = $persen >= 80 ? 'green' : ($persen >= 60 ? 'yellow' : 'red');
                            ?>
                            <span class="px-2 py-1 bg-<?= $badgeColor ?>-100 text-<?= $badgeColor ?>-800 text-xs font-bold rounded-full">
                                <?= $persen ?>%
                            </span>
                        </div>
                        <div class="grid grid-cols-5 gap-2 mb-3 mt-2">
                            <div class="text-center p-1 bg-green-50 rounded-lg">
                                <p class="text-xs text-gray-500">Hadir</p>
                                <p class="text-sm font-bold text-green-600"><?= $rp['hadir'] ?? 0 ?></p>
                            </div>
                            <div class="text-center p-1 bg-blue-50 rounded-lg">
                                <p class="text-xs text-gray-500">Izin</p>
                                <p class="text-sm font-bold text-blue-600"><?= $rp['izin'] ?? 0 ?></p>
                            </div>
                            <div class="text-center p-1 bg-yellow-50 rounded-lg">
                                <p class="text-xs text-gray-500">Sakit</p>
                                <p class="text-sm font-bold text-yellow-600"><?= $rp['sakit'] ?? 0 ?></p>
                            </div>
                            <div class="text-center p-1 bg-red-50 rounded-lg">
                                <p class="text-xs text-gray-500">Alpa</p>
                                <p class="text-sm font-bold text-red-600"><?= $rp['alpa'] ?? 0 ?></p>
                            </div>
                        </div>
                        <a href="<?= base_url('admin/absensi-pkl/rekap/' . $rp['pembimbing_pkl_id']) ?>"
                           class="flex items-center justify-center w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl active:scale-98 transition-all shadow-sm">
                            <i class="fas fa-eye mr-2"></i> Lihat Detail
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Riwayat Absensi -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-4">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-4 py-3">
                <h2 class="text-white font-bold text-sm flex items-center">
                    <i class="fas fa-history mr-2"></i>
                    Riwayat Absensi (<?= count($absensi) ?>)
                </h2>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if (empty($absensi)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-clipboard-list text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Belum ada data absensi</p>
                </div>
                <?php else: ?>
                    <?php $no = 1; foreach ($absensi as $item): ?>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <i class="fas fa-calendar-day text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900"><?= date('d/m/Y', strtotime($item['tanggal'])) ?></p>
                                    <p class="text-xs text-gray-500"><?= date('l', strtotime($item['tanggal'])) ?></p>
                                </div>
                            </div>
                            <?php
                            $persen = $item['persen_kehadiran'] ?? 0;
                            $badgeColor = $persen >= 80 ? 'green' : ($persen >= 60 ? 'yellow' : 'red');
                            ?>
                            <span class="px-2 py-1 bg-<?= $badgeColor ?>-100 text-<?= $badgeColor ?>-800 text-xs font-bold rounded-full">
                                <?= $persen ?>%
                            </span>
                        </div>
                        <div class="mt-2 space-y-1">
                            <div class="flex items-center text-sm text-gray-700">
                                <i class="fas fa-user-tie text-gray-400 mr-2 w-4 text-center"></i>
                                <span class="font-medium"><?= esc($item['nama_pembimbing'] ?? '-') ?></span>
                            </div>
                            <div class="flex items-center text-sm text-gray-700">
                                <i class="fas fa-building text-gray-400 mr-2 w-4 text-center"></i>
                                <span><?= esc($item['nama_perusahaan'] ?? '-') ?></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mt-3">
                            <div class="flex items-center text-xs">
                                <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded-full font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i><?= $item['hadir_count'] ?? 0 ?> Hadir
                                </span>
                                <span class="text-gray-300 mx-1">/</span>
                                <span class="text-gray-600 font-semibold"><?= $item['total_siswa'] ?? 0 ?> Total</span>
                            </div>
                            <a href="<?= base_url('admin/absensi-pkl/show/' . $item['id']) ?>"
                               class="ml-auto flex items-center justify-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg active:scale-95 transition-all shadow-sm">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleFilter() {
    const form = document.getElementById('filterForm');
    const icon = document.getElementById('filterToggleIcon');
    form.classList.toggle('hidden');
    icon.style.transform = form.classList.contains('hidden') ? '' : 'rotate(180deg)';
}

document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.bg-white.rounded-xl');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px)';
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 50);
    });
});
</script>
<?= $this->endSection() ?>
