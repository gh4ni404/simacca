<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .table-row-hover { transition: all 0.2s ease; }
    .table-row-hover:hover { background-color: #f8fafc; transform: translateX(4px); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="mb-8">
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

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-lg mb-8 overflow-hidden">
        <div class="flex items-center p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <div class="p-2 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg mr-3">
                <i class="fas fa-filter text-white"></i>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Filter Data</h2>
        </div>
        <form method="get" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-tie mr-2 text-purple-500"></i> Pembimbing
                    </label>
                    <select name="pembimbing_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Pembimbing</option>
                        <?php foreach ($pembimbingOptions as $id => $nama): ?>
                        <option value="<?= $id ?>" <?= ($filters['pembimbing_id'] ?? '') == $id ? 'selected' : '' ?>><?= esc($nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i> Dari Tanggal
                    </label>
                    <input type="date" name="date_from" value="<?= esc($filters['date_from'] ?? '') ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i> Sampai Tanggal
                    </label>
                    <input type="date" name="date_to" value="<?= esc($filters['date_to'] ?? '') ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                        <i class="fas fa-search mr-2"></i> Filter
                    </button>
                    <a href="<?= base_url('admin/absensi-pkl'); ?>" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-all">
                        <i class="fas fa-redo mr-2"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Global Stats -->
    <?php
    $totalKehadiran = $globalStats['total'] ?? 0;
    $totalHadir = $globalStats['hadir'] ?? 0;
    $totalIzin = $globalStats['izin'] ?? 0;
    $totalSakit = $globalStats['sakit'] ?? 0;
    $totalAlpa = $globalStats['alpa'] ?? 0;
    $totalDispen = $globalStats['dispen'] ?? 0;
    $persenGlobal = $globalStats['persen_kehadiran'] ?? 0;
    ?>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Kehadiran</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $totalKehadiran ?></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Hadir</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $totalHadir ?></p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Izin</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $totalIzin ?></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-file-alt text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Sakit</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $totalSakit ?></p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-medkit text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Alpa</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $totalAlpa ?></p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-user-times text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Dispen</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $totalDispen ?></p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-id-badge text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-emerald-500 col-span-2 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Persentase Kehadiran Global</p>
                    <p class="text-2xl font-bold <?= $persenGlobal >= 80 ? 'text-green-600' : ($persenGlobal >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $persenGlobal ?>%</p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-full">
                    <i class="fas fa-chart-line text-emerald-600"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full transition-all duration-300"
                         style="width: <?= $persenGlobal ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rekap per Pembimbing -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-600 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-user-tie mr-3"></i> Rekap per Pembimbing
                    </h2>
                    <p class="text-purple-100 mt-1">Ringkasan kehadiran per pembimbing PKL</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl">
                    <p class="text-sm opacity-90">Total Pembimbing</p>
                    <p class="text-3xl font-bold"><?= count($rekapPembimbing) ?></p>
                </div>
            </div>
        </div>

        <div class="p-6">
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
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pembimbing</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tempat PKL</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Total Hari</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Hadir</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Izin</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Sakit</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Alpa</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Dispen</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Kehadiran</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $no = 1; foreach ($rekapPembimbing as $rp): ?>
                        <tr class="table-row-hover">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900"><?= $no++ ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center mr-3">
                                        <span class="text-white font-bold text-sm"><?= substr($rp['nama_pembimbing'], 0, 1) ?></span>
                                    </div>
                                    <div class="text-sm font-bold text-gray-900"><?= esc($rp['nama_pembimbing']) ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?= esc($rp['nama_perusahaan'] ?? '-') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-blue-100 text-blue-800 border border-blue-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $rp['total_hari'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-green-100 text-green-800 border border-green-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $rp['hadir'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-blue-100 text-blue-800 border border-blue-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $rp['izin'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-yellow-100 text-yellow-800 border border-yellow-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $rp['sakit'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-red-100 text-red-800 border border-red-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $rp['alpa'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-purple-100 text-purple-800 border border-purple-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $rp['dispen'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php
                                $persen = $rp['persen_kehadiran'] ?? 0;
                                $colorClass = $persen >= 80 ? 'green' : ($persen >= 60 ? 'yellow' : 'red');
                                ?>
                                <span class="inline-flex items-center justify-center bg-<?= $colorClass ?>-100 text-<?= $colorClass ?>-800 border border-<?= $colorClass ?>-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $persen ?>%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="<?= base_url('admin/absensi-pkl/rekap/' . $rp['pembimbing_id']) ?>"
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

    <!-- Riwayat Absensi -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-history mr-3"></i> Riwayat Absensi
                    </h2>
                    <p class="text-blue-100 mt-1">Semua pencatatan absensi PKL</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl">
                    <p class="text-sm opacity-90">Total Absensi</p>
                    <p class="text-3xl font-bold"><?= count($absensi) ?></p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <?php if (empty($absensi)): ?>
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 mb-4">
                    <i class="fas fa-clipboard-list text-4xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Data Absensi</h3>
                <p class="text-gray-600 text-sm">Data riwayat absensi akan muncul di sini.</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pembimbing</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Perusahaan</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Total Siswa</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Hadir</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Kehadiran</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $no = 1; foreach ($absensi as $item): ?>
                        <tr class="table-row-hover">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900"><?= $no++ ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                        <i class="fas fa-calendar-day text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900"><?= date('d/m/Y', strtotime($item['tanggal'])) ?></div>
                                        <div class="text-xs text-gray-500"><?= date('l', strtotime($item['tanggal'])) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900"><?= esc($item['nama_pembimbing'] ?? '-') ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?= esc($item['nama_perusahaan'] ?? '-') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-blue-100 text-blue-800 border border-blue-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $item['total_siswa'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-green-100 text-green-800 border border-green-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $item['hadir_count'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php
                                $persen = $item['persen_kehadiran'] ?? 0;
                                $colorClass = $persen >= 80 ? 'green' : ($persen >= 60 ? 'yellow' : 'red');
                                ?>
                                <span class="inline-flex items-center justify-center bg-<?= $colorClass ?>-100 text-<?= $colorClass ?>-800 border border-<?= $colorClass ?>-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $persen ?>%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="<?= base_url('admin/absensi-pkl/show/' . $item['id']) ?>"
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.table-row-hover');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(10px)';
        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 40);
    });
});
</script>
<?= $this->endSection() ?>
