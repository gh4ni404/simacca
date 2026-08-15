<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Mobile Header -->
    <div class="bg-gradient-to-r from-purple-500 to-blue-600 px-4 py-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <h1 class="text-xl font-bold text-white">Rekap Absensi Pembimbing</h1>
                <p class="text-purple-100 text-xs mt-1">
                    <?= esc($details['nama_pembimbing'] ?? '') ?> &mdash; <?= esc($details['nama_perusahaan'] ?? '') ?>
                </p>
            </div>
            <a href="<?= base_url('admin/absensi-pkl') ?>" class="p-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition-all">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="px-4 pt-4">
        <?= render_flash_message() ?>

        <!-- Info Pembimbing + Stats -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-4">
            <div class="bg-gradient-to-r from-purple-500 to-blue-600 px-4 py-3">
                <h2 class="text-white font-bold text-sm flex items-center">
                    <i class="fas fa-user-tie mr-2"></i>
                    <?= esc($details['nama_pembimbing'] ?? '') ?>
                </h2>
                <p class="text-purple-100 text-xs mt-0.5"><?= esc($details['nama_perusahaan'] ?? '') ?> &bull; <?= $details['total_siswa'] ?? 0 ?> siswa</p>
            </div>
            <div class="p-4">
                <?php
                $persen = $statistics['persen_kehadiran'] ?? 0;
                ?>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-semibold text-gray-700">Persentase Kehadiran</span>
                    <strong class="text-lg <?= $persen >= 80 ? 'text-green-600' : ($persen >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $persen ?>%</strong>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden mb-4">
                    <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full transition-all duration-300"
                         style="width: <?= $persen ?>%"></div>
                </div>
                <?php
                $statCards = [
                    ['label' => 'Total', 'value' => $statistics['total_hari'] ?? 0, 'color' => 'blue'],
                    ['label' => 'Hadir', 'value' => $statistics['hadir'] ?? 0, 'color' => 'green'],
                    ['label' => 'Izin', 'value' => $statistics['izin'] ?? 0, 'color' => 'blue'],
                    ['label' => 'Sakit', 'value' => $statistics['sakit'] ?? 0, 'color' => 'yellow'],
                    ['label' => 'Alpa', 'value' => $statistics['alpa'] ?? 0, 'color' => 'red'],
                ];
                ?>
                <div class="grid grid-cols-5 gap-2">
                    <?php foreach ($statCards as $sc): ?>
                    <div class="text-center p-2 bg-<?= $sc['color'] ?>-50 rounded-lg">
                        <p class="text-xs text-gray-500"><?= $sc['label'] ?></p>
                        <p class="text-sm font-bold text-<?= $sc['color'] ?>-600"><?= $sc['value'] ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Absensi List -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-4">
            <div class="bg-gradient-to-r from-purple-500 to-blue-600 px-4 py-3">
                <h2 class="text-white font-bold text-sm flex items-center">
                    <i class="fas fa-user-tie mr-2"></i>
                    Rekap Absensi (<?= count($absensi) ?>)
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
                        <div class="grid grid-cols-5 gap-2 mb-3 mt-2">
                            <div class="text-center p-1 bg-green-50 rounded-lg">
                                <p class="text-xs text-gray-500">Hadir</p>
                                <p class="text-sm font-bold text-green-600"><?= $item['hadir_count'] ?? 0 ?></p>
                            </div>
                            <div class="text-center p-1 bg-blue-50 rounded-lg">
                                <p class="text-xs text-gray-500">Izin</p>
                                <p class="text-sm font-bold text-blue-600"><?= $item['izin_count'] ?? 0 ?></p>
                            </div>
                            <div class="text-center p-1 bg-yellow-50 rounded-lg">
                                <p class="text-xs text-gray-500">Sakit</p>
                                <p class="text-sm font-bold text-yellow-600"><?= $item['sakit_count'] ?? 0 ?></p>
                            </div>
                            <div class="text-center p-1 bg-red-50 rounded-lg">
                                <p class="text-xs text-gray-500">Alpa</p>
                                <p class="text-sm font-bold text-red-600"><?= $item['alpa_count'] ?? 0 ?></p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-xs text-gray-600">
                                <span class="font-semibold"><?= $item['total_siswa'] ?? 0 ?> siswa</span>
                            </div>
                            <a href="<?= base_url('admin/absensi-pkl/detail/' . $item['id']) ?>"
                               class="flex items-center justify-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg active:scale-95 transition-all shadow-sm">
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
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.bg-white.rounded-xl, .bg-white.rounded-2xl');
    cards.forEach((card, index) => {
        card.style.animation = `fadeInUp 0.3s ease ${index * 40}ms both`;
    });
});
</script>
<?= $this->endSection() ?>
