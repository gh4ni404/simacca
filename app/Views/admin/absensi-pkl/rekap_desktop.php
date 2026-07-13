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
            <div class="p-3 bg-gradient-to-br from-purple-500 to-blue-600 rounded-xl shadow-lg">
                <i class="fas fa-user-tie text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <span class="bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">Rekap Absensi Pembimbing</span>
                </h1>
                <p class="text-gray-600 mt-1 text-sm">
                    <i class="fas fa-user mr-1"></i> <?= esc($details['nama_pembimbing'] ?? '') ?>
                    &mdash; <?= esc($details['nama_perusahaan'] ?? '') ?>
                </p>
            </div>
        </div>
    </div>

    <?= render_flash_message() ?>

    <!-- Stat Cards -->
    <?php
    $statCards = [
        ['label' => 'Total Hari', 'value' => $statistics['total_hari'] ?? 0, 'icon' => 'calendar-alt', 'color' => 'blue'],
        ['label' => 'Hadir', 'value' => $statistics['hadir'] ?? 0, 'icon' => 'user-check', 'color' => 'green'],
        ['label' => 'Izin', 'value' => $statistics['izin'] ?? 0, 'icon' => 'file-alt', 'color' => 'blue'],
        ['label' => 'Sakit', 'value' => $statistics['sakit'] ?? 0, 'icon' => 'medkit', 'color' => 'yellow'],
        ['label' => 'Alpa', 'value' => $statistics['alpa'] ?? 0, 'icon' => 'user-times', 'color' => 'red'],
        ['label' => 'Dispen', 'value' => $statistics['dispen'] ?? 0, 'icon' => 'id-badge', 'color' => 'purple'],
    ];
    ?>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <?php foreach ($statCards as $sc): ?>
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-<?= $sc['color'] ?>-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500"><?= $sc['label'] ?></p>
                    <p class="text-2xl font-bold text-gray-800"><?= $sc['value'] ?></p>
                </div>
                <div class="p-3 bg-<?= $sc['color'] ?>-100 rounded-full">
                    <i class="fas fa-<?= $sc['icon'] ?> text-<?= $sc['color'] ?>-600"></i>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Kehadiran -->
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-emerald-500 col-span-2 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Persentase Kehadiran</p>
                    <?php $persen = $statistics['persen_kehadiran'] ?? 0; ?>
                    <p class="text-2xl font-bold <?= $persen >= 80 ? 'text-green-600' : ($persen >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $persen ?>%</p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-full">
                    <i class="fas fa-chart-line text-emerald-600"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full transition-all duration-300"
                         style="width: <?= $persen ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Pembimbing -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-info-circle mr-2 text-blue-500"></i> Informasi Pembimbing</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-gray-500">Nama Pembimbing</p>
                <p class="font-semibold text-gray-900"><?= esc($details['nama_pembimbing'] ?? '-') ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Tempat PKL</p>
                <p class="font-semibold text-gray-900"><?= esc($details['nama_perusahaan'] ?? '-') ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Siswa Bimbingan</p>
                <p class="font-semibold text-gray-900"><?= $details['total_siswa'] ?? 0 ?> siswa</p>
            </div>
        </div>
    </div>

    <!-- Absensi Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-600 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-list mr-3"></i> Daftar Absensi
                    </h2>
                    <p class="text-purple-100 mt-1">Riwayat absensi kehadiran siswa bimbingan</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl">
                    <p class="text-sm opacity-90">Total Rekapan</p>
                    <p class="text-3xl font-bold"><?= count($absensi) ?></p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <?php if (empty($absensi)): ?>
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-purple-100 to-blue-100 mb-4">
                    <i class="fas fa-clipboard-list text-4xl text-purple-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Data Absensi</h3>
                <p class="text-gray-600 text-sm">Data absensi pembimbing ini akan muncul di sini.</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Total Siswa</th>
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
                                <span class="inline-flex items-center justify-center bg-blue-100 text-blue-800 border border-blue-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $item['izin_count'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-yellow-100 text-yellow-800 border border-yellow-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $item['sakit_count'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-red-100 text-red-800 border border-red-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $item['alpa_count'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-purple-100 text-purple-800 border border-purple-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $item['dispen_count'] ?? 0 ?>
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

    <!-- Back Button -->
    <div class="mt-8">
        <a href="<?= base_url('admin/absensi-pkl'); ?>"
           class="inline-flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Monitoring
        </a>
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
