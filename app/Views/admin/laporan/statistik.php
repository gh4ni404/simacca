<?= $this->extend('templates/main_layout'); ?>

<?= $this->section('content'); ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Laporan Statistik</h1>
    <p class="text-gray-600">Ringkasan statistik sistem</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm text-gray-600">Total Guru</p>
        <p class="text-3xl font-bold text-gray-800"><?= (int)($stats['total_guru'] ?? 0); ?></p>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm text-gray-600">Total Siswa</p>
        <p class="text-3xl font-bold text-gray-800"><?= (int)($stats['total_siswa'] ?? 0); ?></p>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-sm text-gray-600">Total Kelas</p>
        <p class="text-3xl font-bold text-gray-800"><?= (int)($stats['total_kelas'] ?? 0); ?></p>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Kelas</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wali Kelas</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Siswa</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Absensi Bulan Ini</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($kelasSummary)): ?>
                    <?php foreach ($kelasSummary as $k): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($k['nama_kelas']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($k['wali_kelas']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900"><?= (int)$k['total_siswa']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900"><?= (int)$k['absensi_bulan_ini']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">Tidak ada data ringkasan kelas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Grafik</h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
            <h3 class="text-sm text-gray-700 mb-2">Kehadiran 7 Hari Terakhir</h3>
            <canvas id="chartLine"></canvas>
        </div>
        <div>
            <h3 class="text-sm text-gray-700 mb-2">Distribusi Kehadiran</h3>
            <canvas id="chartPie"></canvas>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartData = <?= json_encode($chartData ?? ['attendanceLine' => ['labels'=>[], 'data'=>[]], 'attendancePie' => ['labels'=>[], 'data'=>[], 'colors'=>[]]]); ?>;

const ctxLine = document.getElementById('chartLine').getContext('2d');
new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: chartData.attendanceLine.labels,
        datasets: [{
            label: 'Sesi',
            data: chartData.attendanceLine.data,
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

const ctxPie = document.getElementById('chartPie').getContext('2d');
new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: chartData.attendancePie.labels,
        datasets: [{
            data: chartData.attendancePie.data,
            backgroundColor: chartData.attendancePie.colors,
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});
</script>
<?= $this->endSection(); ?>