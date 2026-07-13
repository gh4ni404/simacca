<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 mb-4 rounded-b-lg mx-0 shadow-md">
        <h1 class="text-xl font-bold mb-1">
            <i class="fas fa-clipboard-check mr-2"></i> Rekap Absensi PKL
        </h1>
        <p class="text-sm opacity-90">Riwayat kehadiran PKL Anda</p>
    </div>

    <div class="px-4">
        <?= render_flash_message() ?>

        <?php
        $totalHari = $statistik['total_hari'] ?? 0;
        $totalHadir = $statistik['hadir'] ?? 0;
        $totalIzin = $statistik['izin'] ?? 0;
        $totalSakit = $statistik['sakit'] ?? 0;
        $totalAlpa = $statistik['alpa'] ?? 0;
        $totalDispen = $statistik['dispen'] ?? 0;
        $persenKehadiran = $statistik['persen_kehadiran'] ?? 0;
        ?>

        <!-- Stats Grid -->
        <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="bg-white rounded-xl shadow-md p-3 text-center border-t-2 border-blue-500">
                <p class="text-xs text-gray-500">Total</p>
                <p class="text-xl font-bold text-gray-800"><?= $totalHari ?></p>
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
            <div class="bg-white rounded-xl shadow-md p-3 text-center border-t-2 border-purple-500">
                <p class="text-xs text-gray-500">Dispen</p>
                <p class="text-xl font-bold text-purple-600"><?= $totalDispen ?></p>
            </div>
        </div>

        <!-- Kehadiran Progress -->
        <div class="bg-white rounded-xl shadow-md p-4 mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-chart-line text-emerald-500 mr-2"></i> Persentase Kehadiran
                </span>
                <strong class="text-lg <?= $persenKehadiran >= 80 ? 'text-green-600' : ($persenKehadiran >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $persenKehadiran ?>%</strong>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full transition-all duration-300"
                     style="width: <?= $persenKehadiran ?>%"></div>
            </div>
        </div>

        <?php if (empty($rekap)): ?>
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-md p-8 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 mb-4">
                <i class="fas fa-clipboard-list text-4xl text-blue-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Data Absensi PKL</h3>
            <p class="text-gray-600 text-sm">Rekap absensi PKL Anda akan muncul di sini setelah guru pembimbing melakukan pencatatan kehadiran.</p>
        </div>
        <?php else: ?>
            <?php foreach ($groupedByMonth as $month => $items): ?>
            <!-- Month Section -->
            <div class="mb-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="p-2 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg">
                        <i class="fas fa-calendar-alt text-white text-xs"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-800"><?= $month ?></h3>
                    <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2 py-0.5 rounded-full"><?= count($items) ?> hari</span>
                </div>

                <div class="space-y-3">
                    <?php
                    $no = 1;
                    foreach ($items['items'] as $item):
                        $badgeClass = '';
                        $icon = '';
                        switch($item['status']) {
                            case 'hadir':
                                $badgeClass = 'bg-green-100 text-green-800 border-green-300';
                                $icon = 'fa-check-circle';
                                break;
                            case 'izin':
                                $badgeClass = 'bg-blue-100 text-blue-800 border-blue-300';
                                $icon = 'fa-file-alt';
                                break;
                            case 'sakit':
                                $badgeClass = 'bg-yellow-100 text-yellow-800 border-yellow-300';
                                $icon = 'fa-medkit';
                                break;
                            case 'alpa':
                                $badgeClass = 'bg-red-100 text-red-800 border-red-300';
                                $icon = 'fa-user-times';
                                break;
                            case 'dispen':
                                $badgeClass = 'bg-purple-100 text-purple-800 border-purple-300';
                                $icon = 'fa-id-badge';
                                break;
                        }
                    ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <i class="fas fa-calendar-day text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <?php
                                    $fmt = new IntlDateFormatter('id_ID', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
                                    $date = new DateTime($item['tanggal']);
                                    ?>
                                    <p class="text-sm font-bold text-gray-900"><?= $fmt->format($date) ?></p>
                                    <p class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($item['tanggal'])) ?></p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border-2 <?= $badgeClass ?>">
                                <i class="fas <?= $icon ?> mr-1"></i>
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </div>
                        <div class="mt-2 space-y-1">
                            <div class="flex items-center text-sm text-gray-700">
                                <i class="fas fa-building text-gray-400 mr-2 w-4 text-center"></i>
                                <span class="font-medium"><?= esc($item['nama_perusahaan'] ?? '-') ?></span>
                            </div>
                            <div class="flex items-center text-sm text-gray-700">
                                <i class="fas fa-user-tie text-gray-400 mr-2 w-4 text-center"></i>
                                <span><?= esc($item['nama_pembimbing'] ?? '-') ?></span>
                            </div>
                            <?php if (!empty($item['keterangan'])): ?>
                            <div class="flex items-start text-sm text-gray-600 mt-2 p-2 bg-gray-50 rounded-lg">
                                <i class="fas fa-sticky-note text-gray-400 mr-2 mt-0.5"></i>
                                <span class="italic"><?= esc($item['keterangan']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.bg-white.rounded-xl.shadow-sm');
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
