<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-clipboard-list mr-2 text-green-600"></i>
                    Riwayat Absensi
                </h1>
                <p class="text-gray-600 mt-1">Riwayat kehadiran Anda</p>
            </div>
            <div class="mt-4 md:mt-0">
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>
                    Print Riwayat
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" action="<?= base_url('siswa/absensi'); ?>" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="<?= $startDate; ?>" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="<?= $endDate; ?>" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Filter
                </button>
                <a href="<?= base_url('siswa/absensi'); ?>" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-redo mr-2"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Section -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gray-100 text-gray-600 mr-3">
                    <i class="fas fa-calendar-check text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-2xl font-bold"><?= $summary['total']; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-3">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Hadir</p>
                    <p class="text-2xl font-bold text-green-600"><?= $summary['hadir']; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-3">
                    <i class="fas fa-notes-medical text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Sakit</p>
                    <p class="text-2xl font-bold text-blue-600"><?= $summary['sakit']; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-3">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Izin</p>
                    <p class="text-2xl font-bold text-yellow-600"><?= $summary['izin']; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-3">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Alpa</p>
                    <p class="text-2xl font-bold text-red-600"><?= $summary['alpa']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Persentase Kehadiran -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line mr-2 text-blue-600"></i>
            Persentase Kehadiran
        </h3>
        <div class="flex items-center">
            <div class="flex-1">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Tingkat Kehadiran Anda</span>
                    <span class="text-3xl font-bold <?= $persentaseKehadiran >= 80 ? 'text-green-600' : ($persentaseKehadiran >= 60 ? 'text-yellow-600' : 'text-red-600'); ?>">
                        <?= $persentaseKehadiran; ?>%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="h-4 rounded-full <?= $persentaseKehadiran >= 80 ? 'bg-green-500' : ($persentaseKehadiran >= 60 ? 'bg-yellow-500' : 'bg-red-500'); ?>" 
                         style="width: <?= $persentaseKehadiran; ?>%"></div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-600">
                    <span>0%</span>
                    <span>50%</span>
                    <span>100%</span>
                </div>
                <div class="mt-4 flex items-center">
                    <?php if ($persentaseKehadiran >= 80): ?>
                    <div class="flex items-center text-green-600">
                        <i class="fas fa-thumbs-up text-2xl mr-2"></i>
                        <div>
                            <p class="font-semibold">Kehadiran Sangat Baik!</p>
                            <p class="text-sm">Pertahankan prestasi Anda</p>
                        </div>
                    </div>
                    <?php elseif ($persentaseKehadiran >= 60): ?>
                    <div class="flex items-center text-yellow-600">
                        <i class="fas fa-exclamation-triangle text-2xl mr-2"></i>
                        <div>
                            <p class="font-semibold">Kehadiran Cukup</p>
                            <p class="text-sm">Tingkatkan kehadiran Anda untuk hasil lebih baik</p>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="flex items-center text-red-600">
                        <i class="fas fa-times-circle text-2xl mr-2"></i>
                        <div>
                            <p class="font-semibold">Kehadiran Kurang!</p>
                            <p class="text-sm">Segera tingkatkan kehadiran Anda</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Absensi Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-list mr-2"></i>
                Detail Riwayat Absensi
            </h3>
            <span class="text-sm text-gray-600">
                Periode: <?= date('d M Y', strtotime($startDate)); ?> - <?= date('d M Y', strtotime($endDate)); ?>
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertemuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($absensiData)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Belum ada riwayat absensi untuk periode ini</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($absensiData as $absen): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= date('d M Y', strtotime($absen['tanggal'])); ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?= date('l', strtotime($absen['tanggal'])); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900"><?= esc($absen['nama_mapel']); ?></div>
                                <?php if (!empty($absen['materi_pembelajaran'])): ?>
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-book-open mr-1"></i>
                                    <?= esc(substr($absen['materi_pembelajaran'], 0, 30)); ?><?= strlen($absen['materi_pembelajaran']) > 30 ? '...' : ''; ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <i class="fas fa-user-tie text-gray-400 mr-2"></i>
                                    <?= esc($absen['nama_guru']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Ke-<?= $absen['pertemuan_ke']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $statusClass = '';
                                $statusIcon = '';
                                $statusText = '';
                                
                                switch(strtolower($absen['status'])) {
                                    case 'hadir':
                                        $statusClass = 'bg-green-100 text-green-800';
                                        $statusIcon = 'fa-check-circle';
                                        $statusText = 'Hadir';
                                        break;
                                    case 'sakit':
                                        $statusClass = 'bg-blue-100 text-blue-800';
                                        $statusIcon = 'fa-notes-medical';
                                        $statusText = 'Sakit';
                                        break;
                                    case 'izin':
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                        $statusIcon = 'fa-file-alt';
                                        $statusText = 'Izin';
                                        break;
                                    case 'alpa':
                                        $statusClass = 'bg-red-100 text-red-800';
                                        $statusIcon = 'fa-times-circle';
                                        $statusText = 'Alpa';
                                        break;
                                }
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass; ?>">
                                    <i class="fas <?= $statusIcon; ?> mr-1"></i>
                                    <?= $statusText; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?= !empty($absen['keterangan']) ? esc($absen['keterangan']) : '-'; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info Footer -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4 print:hidden">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Keterangan Status:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li><span class="font-medium text-green-700">Hadir (H)</span> - Anda hadir di kelas</li>
                    <li><span class="font-medium text-blue-700">Sakit (S)</span> - Tidak hadir karena sakit</li>
                    <li><span class="font-medium text-yellow-700">Izin (I)</span> - Tidak hadir dengan izin</li>
                    <li><span class="font-medium text-red-700">Alpa (A)</span> - Tidak hadir tanpa keterangan</li>
                </ul>
                <p class="mt-2 text-xs">
                    <i class="fas fa-lightbulb mr-1"></i>
                    <strong>Tips:</strong> Jaga kehadiran minimal 80% untuk performa akademik yang baik
                </p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .print\:hidden {
        display: none !important;
    }
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}
</style>
<?= $this->endSection() ?>
