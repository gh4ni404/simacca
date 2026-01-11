<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-clipboard-check mr-2 text-green-600"></i>
                    Monitoring Absensi Kelas
                </h1>
                <p class="text-gray-600 mt-1">Monitor kehadiran siswa kelas <?= esc($kelas['nama_kelas']); ?></p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-school mr-1"></i>
                    Kelas: <span class="font-semibold"><?= esc($kelas['nama_kelas']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" action="<?= base_url('walikelas/absensi'); ?>" class="flex flex-col md:flex-row gap-4">
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
                <a href="<?= base_url('walikelas/absensi'); ?>" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-redo mr-2"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <?php if (!empty($absensiData)): ?>
    <?php 
    $totalPertemuan = count($absensiData);
    $totalHadir = 0;
    $totalSakit = 0;
    $totalIzin = 0;
    $totalAlpa = 0;
    $totalKeseluruhan = 0;
    
    foreach ($absensiData as $absen) {
        $totalHadir += $absen['detail']['hadir'];
        $totalSakit += $absen['detail']['sakit'];
        $totalIzin += $absen['detail']['izin'];
        $totalAlpa += $absen['detail']['alpa'];
        $totalKeseluruhan += $absen['detail']['total'];
    }
    
    $persentaseHadir = $totalKeseluruhan > 0 ? round(($totalHadir / $totalKeseluruhan) * 100, 1) : 0;
    ?>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-3">
                    <i class="fas fa-calendar-check text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Pertemuan</p>
                    <p class="text-2xl font-bold"><?= $totalPertemuan; ?></p>
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
                    <p class="text-2xl font-bold"><?= $totalHadir; ?></p>
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
                    <p class="text-2xl font-bold"><?= $totalSakit; ?></p>
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
                    <p class="text-2xl font-bold"><?= $totalIzin; ?></p>
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
                    <p class="text-2xl font-bold"><?= $totalAlpa; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Persentase Kehadiran -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-pie mr-2 text-blue-600"></i>
            Persentase Kehadiran Periode Ini
        </h3>
        <div class="flex items-center">
            <div class="flex-1">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Tingkat Kehadiran</span>
                    <span class="text-2xl font-bold text-gray-900"><?= $persentaseHadir; ?>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="h-4 rounded-full <?= $persentaseHadir >= 80 ? 'bg-green-500' : ($persentaseHadir >= 60 ? 'bg-yellow-500' : 'bg-red-500'); ?>" 
                         style="width: <?= $persentaseHadir; ?>%"></div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-600">
                    <span>0%</span>
                    <span>50%</span>
                    <span>100%</span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Absensi Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-list mr-2"></i>
                Daftar Absensi
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kehadiran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($absensiData)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Belum ada data absensi untuk periode ini</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($absensiData as $absen): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">
                                    <?= date('d M Y', strtotime($absen['tanggal'])); ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <?= date('l', strtotime($absen['tanggal'])); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900"><?= esc($absen['nama_mapel']); ?></div>
                                <?php if (!empty($absen['materi_pembelajaran'])): ?>
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-book-open mr-1"></i>
                                    <?= esc(substr($absen['materi_pembelajaran'], 0, 40)); ?><?= strlen($absen['materi_pembelajaran']) > 40 ? '...' : ''; ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($absen['nama_guru']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Pertemuan ke-<?= $absen['pertemuan_ke']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2 text-xs">
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        H: <?= $absen['detail']['hadir']; ?>
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-blue-100 text-blue-800">
                                        <i class="fas fa-notes-medical mr-1"></i>
                                        S: <?= $absen['detail']['sakit']; ?>
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-file-alt mr-1"></i>
                                        I: <?= $absen['detail']['izin']; ?>
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        A: <?= $absen['detail']['alpa']; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="font-semibold <?= $absen['persentase_hadir'] >= 80 ? 'text-green-600' : ($absen['persentase_hadir'] >= 60 ? 'text-yellow-600' : 'text-red-600'); ?>">
                                                <?= $absen['persentase_hadir']; ?>%
                                            </span>
                                        </div>
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full <?= $absen['persentase_hadir'] >= 80 ? 'bg-green-500' : ($absen['persentase_hadir'] >= 60 ? 'bg-yellow-500' : 'bg-red-500'); ?>" 
                                                 style="width: <?= $absen['persentase_hadir']; ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info Footer -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Informasi:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Data absensi diinput oleh guru mata pelajaran</li>
                    <li>Gunakan filter tanggal untuk melihat periode tertentu</li>
                    <li>Persentase kehadiran dihitung dari total hadir dibagi total keseluruhan</li>
                    <li>Warna indikator: <span class="font-medium text-green-700">Hijau (≥80%)</span>, 
                        <span class="font-medium text-yellow-700">Kuning (60-79%)</span>, 
                        <span class="font-medium text-red-700">Merah (<60%)</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
