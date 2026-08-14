<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .table-row-hover { transition: all 0.2s ease; }
    .table-row-hover:hover { background-color: #f8fafc; transform: translateX(4px); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-3 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl shadow-lg">
                <i class="fas fa-clipboard-check text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Monitoring Absensi PKL</span>
                </h1>
                <p class="text-gray-600 mt-1 text-sm">
                    <i class="fas fa-info-circle mr-1"></i> Pantau kehadiran siswa PKL seluruh pembimbing
                </p>
            </div>
        </div>
    </div>

    <?= render_flash_message() ?>

    <!-- Main Content: Sidebar + Table -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Left Sidebar: Statistik -->
        <?php
        $totalKehadiran = $globalStats['total'] ?? 0;
        $totalHadir = $globalStats['hadir'] ?? 0;
        $totalIzin = $globalStats['izin'] ?? 0;
        $totalSakit = $globalStats['sakit'] ?? 0;
        $totalAlpa = $globalStats['alpa'] ?? 0;
        $persenGlobal = $globalStats['persen_kehadiran'] ?? 0;
        ?>
        <div class="lg:w-72 xl:w-80 flex-shrink-0">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-5">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i> Statistik Global
                    </h3>
                </div>
                <div class="p-5 space-y-3">
                    <!-- Total Kehadiran -->
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                <i class="fas fa-users text-blue-600"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">Total Kehadiran</span>
                        </div>
                        <span class="text-lg font-bold text-gray-800"><?= $totalKehadiran ?></span>
                    </div>
                    <!-- Hadir -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-lg mr-3">
                                <i class="fas fa-user-check text-green-600"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">Hadir</span>
                        </div>
                        <span class="text-lg font-bold text-green-600"><?= $totalHadir ?></span>
                    </div>
                    <!-- Izin -->
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                <i class="fas fa-file-alt text-blue-600"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">Izin</span>
                        </div>
                        <span class="text-lg font-bold text-blue-600"><?= $totalIzin ?></span>
                    </div>
                    <!-- Sakit -->
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                                <i class="fas fa-medkit text-yellow-600"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">Sakit</span>
                        </div>
                        <span class="text-lg font-bold text-yellow-600"><?= $totalSakit ?></span>
                    </div>
                    <!-- Alpa -->
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="p-2 bg-red-100 rounded-lg mr-3">
                                <i class="fas fa-user-times text-red-600"></i>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">Alpa</span>
                        </div>
                        <span class="text-lg font-bold text-red-600"><?= $totalAlpa ?></span>
                    </div>
                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-2"></div>
                    <!-- Persentase -->
                    <div class="p-3 bg-gradient-to-r from-emerald-50 to-emerald-100 rounded-xl">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-700">Persentase</span>
                            <span class="text-lg font-bold <?= $persenGlobal >= 80 ? 'text-green-600' : ($persenGlobal >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $persenGlobal ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full transition-all duration-300"
                                 style="width: <?= $persenGlobal ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content: Tabel Rekap -->
        <div class="flex-1 min-w-0">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-600 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <i class="fas fa-user-tie mr-3"></i> Rekap per Pembimbing
                            </h2>
                            <p class="text-purple-100 mt-1">Ringkasan kehadiran per pembimbing PKL</p>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm text-white px-5 py-2 rounded-xl">
                            <p class="text-xs opacity-90">Total</p>
                            <p class="text-2xl font-bold"><?= count($rekapPembimbing) ?></p>
                        </div>
                    </div>
                    <form method="get" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <select name="pembimbing_id" class="w-full px-3 py-2 text-sm border border-white/30 rounded-lg bg-white/10 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50 [&>option]:text-gray-800 [&>option]:bg-white">
                                <option value="">Semua Pembimbing</option>
                                <?php foreach ($pembimbingOptions as $id => $nama): ?>
                                <option value="<?= $id ?>" <?= ($filters['pembimbing_id'] ?? '') == $id ? 'selected' : '' ?>><?= esc($nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <input type="date" name="date_from" value="<?= esc($filters['date_from'] ?? '') ?>"
                                   class="w-full px-3 py-2 text-sm border border-white/30 rounded-lg bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                        </div>
                        <div>
                            <input type="date" name="date_to" value="<?= esc($filters['date_to'] ?? '') ?>"
                                   class="w-full px-3 py-2 text-sm border border-white/30 rounded-lg bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-semibold rounded-lg transition-all backdrop-blur-sm">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                            <a href="<?= base_url('admin/absensi-pkl'); ?>" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm font-semibold rounded-lg transition-all">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <div class="p-5">
                    <?php if (empty($rekapPembimbing)): ?>
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-purple-100 to-blue-100 mb-4">
                            <i class="fas fa-user-tie text-4xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Data Pembimbing</h3>
                        <p class="text-gray-600 text-sm">Data rekap pembimbing akan muncul di sini.</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pembimbing</th>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tempat PKL</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Hari</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Hadir</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Izin</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Sakit</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Alpa</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Kehadiran</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $no = 1; foreach ($rekapPembimbing as $rp): ?>
                                <tr class="table-row-hover">
                                    <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-gray-900"><?= $no++ ?></td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center">
                                            <div class="h-9 w-9 rounded-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center mr-3">
                                                <span class="text-white font-bold text-sm"><?= substr($rp['nama_pembimbing'], 0, 1) ?></span>
                                            </div>
                                            <div class="text-sm font-bold text-gray-900"><?= esc($rp['nama_pembimbing']) ?></div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-sm text-gray-900"><?= esc($rp['nama_perusahaan'] ?? '-') ?></div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center bg-blue-100 text-blue-800 px-2 py-1 rounded-lg text-sm font-bold">
                                            <?= $rp['total_hari'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center bg-green-100 text-green-800 px-2 py-1 rounded-lg text-sm font-bold">
                                            <?= $rp['hadir'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center bg-blue-100 text-blue-800 px-2 py-1 rounded-lg text-sm font-bold">
                                            <?= $rp['izin'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center bg-yellow-100 text-yellow-800 px-2 py-1 rounded-lg text-sm font-bold">
                                            <?= $rp['sakit'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center bg-red-100 text-red-800 px-2 py-1 rounded-lg text-sm font-bold">
                                            <?= $rp['alpa'] ?? 0 ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <?php
                                        $persen = $rp['persen_kehadiran'] ?? 0;
                                        $colorClass = $persen >= 80 ? 'green' : ($persen >= 60 ? 'yellow' : 'red');
                                        ?>
                                        <span class="inline-flex items-center justify-center bg-<?= $colorClass ?>-100 text-<?= $colorClass ?>-800 px-2 py-1 rounded-lg text-sm font-bold">
                                            <?= $persen ?>%
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <a href="<?= base_url('admin/absensi-pkl/rekap/' . $rp['pembimbing_pkl_id']) ?>"
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition-all shadow-sm">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.table-row-hover');
    rows.forEach((row, index) => {
        row.style.animation = `fadeInUp 0.3s ease ${index * 30}ms both`;
    });
});
</script>
<?= $this->endSection() ?>
