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
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Rekap Absensi PKL</span>
                </h1>
                <p class="text-gray-600 mt-1 text-sm">
                    <i class="fas fa-info-circle mr-1"></i> Riwayat kehadiran PKL Anda
                </p>
            </div>
        </div>
    </div>

    <?= render_flash_message() ?>

    <!-- Stat Cards -->
    <?php
    $totalHari = $statistik['total_hari'] ?? 0;
    $totalHadir = $statistik['hadir'] ?? 0;
    $totalIzin = $statistik['izin'] ?? 0;
    $totalSakit = $statistik['sakit'] ?? 0;
    $totalAlpa = $statistik['alpa'] ?? 0;
    $totalDispen = $statistik['dispen'] ?? 0;
    $persenKehadiran = $statistik['persen_kehadiran'] ?? 0;
    ?>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Hari</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $totalHari ?></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
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
                    <p class="text-sm text-gray-500">Persentase Kehadiran</p>
                    <p class="text-2xl font-bold <?= $persenKehadiran >= 80 ? 'text-green-600' : ($persenKehadiran >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $persenKehadiran ?>%</p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-full">
                    <i class="fas fa-chart-line text-emerald-600"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full transition-all duration-300"
                         style="width: <?= $persenKehadiran ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rekap Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-list mr-3"></i> Rekap Absensi PKL
                    </h2>
                    <p class="text-blue-100 mt-1">Daftar kehadiran selama masa PKL</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl">
                    <p class="text-sm opacity-90">Total Rekapan</p>
                    <p class="text-3xl font-bold"><?= count($rekap) ?></p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <?php if (empty($rekap)): ?>
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 mb-6">
                    <i class="fas fa-clipboard-list text-5xl text-blue-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">Belum Ada Data Absensi PKL</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">Rekap absensi PKL Anda akan muncul di sini setelah guru pembimbing melakukan pencatatan kehadiran.</p>
            </div>
            <?php else: ?>
                <?php foreach ($groupedByMonth as $month => $items): ?>
                <div class="mb-6 last:mb-0">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="p-2 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg">
                            <i class="fas fa-calendar-alt text-white text-sm"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800"><?= $month ?></h3>
                        <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full"><?= count($items) ?> hari</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Perusahaan</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pembimbing</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $no = 1;
                                $statusClasses = [
                                    'hadir'  => 'bg-green-100 text-green-800',
                                    'izin'   => 'bg-blue-100 text-blue-800',
                                    'sakit'  => 'bg-yellow-100 text-yellow-800',
                                    'alpa'   => 'bg-red-100 text-red-800',
                                    'dispen' => 'bg-purple-100 text-purple-800',
                                ];
                                foreach ($items['items'] as $item):
                                ?>
                                <tr class="table-row-hover">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900"><?= $no++ ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                                <i class="fas fa-calendar-day text-blue-600"></i>
                                            </div>
                                            <div>
                                                <?php
                                                $fmt = new IntlDateFormatter('id_ID', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
                                                $date = new DateTime($item['tanggal']);
                                                ?>
                                                <div class="text-sm font-bold text-gray-900"><?= $fmt->format($date) ?></div>
                                                <div class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($item['tanggal'])) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900"><?= esc($item['nama_perusahaan'] ?? '-') ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900"><?= esc($item['nama_pembimbing'] ?? '-') ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $statusClasses[$item['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                            <i class="fas <?= $item['status'] === 'hadir' ? 'fa-check-circle' : ($item['status'] === 'izin' ? 'fa-file-alt' : ($item['status'] === 'sakit' ? 'fa-medkit' : ($item['status'] === 'alpa' ? 'fa-user-times' : 'fa-id-badge'))) ?> mr-1"></i>
                                            <?= ucfirst($item['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?= esc($item['keterangan'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>
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
        }, index * 30);
    });
});
</script>
<?= $this->endSection() ?>
