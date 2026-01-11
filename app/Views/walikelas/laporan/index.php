<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-chart-line mr-2 text-purple-600"></i>
                    Laporan Kehadiran Kelas
                </h1>
                <p class="text-gray-600 mt-1">Laporan kehadiran siswa kelas <?= esc($kelas['nama_kelas']); ?></p>
            </div>
            <div class="mt-4 md:mt-0">
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>
                    Print Laporan
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" action="<?= base_url('walikelas/laporan'); ?>" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="<?= $startDate; ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="<?= $endDate; ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Siswa (Opsional)</label>
                    <select name="siswa_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Semua Siswa (Rekapitulasi) --</option>
                        <?php foreach ($siswaList as $s): ?>
                        <option value="<?= $s['id']; ?>" <?= $siswaId == $s['id'] ? 'selected' : ''; ?>>
                            <?= esc($s['nama_lengkap']); ?> (<?= esc($s['nis']); ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Tampilkan Laporan
                </button>
                <a href="<?= base_url('walikelas/laporan'); ?>" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-redo mr-2"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <?php if (!empty($laporan)): ?>
        <?php if ($siswaId && $summary): ?>
            <!-- Laporan Per Siswa -->
            <div class="bg-white rounded-lg shadow mb-6 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user mr-2 text-blue-600"></i>
                    Ringkasan Kehadiran
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-calendar-check text-3xl text-gray-500 mb-2"></i>
                        <p class="text-2xl font-bold text-gray-800"><?= $summary['total']; ?></p>
                        <p class="text-sm text-gray-600">Total Pertemuan</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <i class="fas fa-check-circle text-3xl text-green-500 mb-2"></i>
                        <p class="text-2xl font-bold text-gray-800"><?= $summary['hadir']; ?></p>
                        <p class="text-sm text-gray-600">Hadir</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <i class="fas fa-notes-medical text-3xl text-blue-500 mb-2"></i>
                        <p class="text-2xl font-bold text-gray-800"><?= $summary['sakit']; ?></p>
                        <p class="text-sm text-gray-600">Sakit</p>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <i class="fas fa-file-alt text-3xl text-yellow-500 mb-2"></i>
                        <p class="text-2xl font-bold text-gray-800"><?= $summary['izin']; ?></p>
                        <p class="text-sm text-gray-600">Izin</p>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <i class="fas fa-times-circle text-3xl text-red-500 mb-2"></i>
                        <p class="text-2xl font-bold text-gray-800"><?= $summary['alpa']; ?></p>
                        <p class="text-sm text-gray-600">Alpa</p>
                    </div>
                </div>

                <!-- Persentase -->
                <div class="mt-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600">Persentase Kehadiran</span>
                        <span class="text-2xl font-bold text-gray-900">
                            <?= $summary['total'] > 0 ? round(($summary['hadir'] / $summary['total']) * 100, 1) : 0; ?>%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <?php 
                        $persentase = $summary['total'] > 0 ? round(($summary['hadir'] / $summary['total']) * 100, 1) : 0;
                        ?>
                        <div class="h-4 rounded-full <?= $persentase >= 80 ? 'bg-green-500' : ($persentase >= 60 ? 'bg-yellow-500' : 'bg-red-500'); ?>" 
                             style="width: <?= $persentase; ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Detail Absensi Per Siswa -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-list mr-2"></i>
                        Detail Kehadiran
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Periode: <?= date('d M Y', strtotime($startDate)); ?> - <?= date('d M Y', strtotime($endDate)); ?>
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $no = 1; ?>
                            <?php foreach ($laporan as $l): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('d M Y', strtotime($l['tanggal'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= esc($l['nama_mapel']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusClass = '';
                                    $statusIcon = '';
                                    $statusText = '';
                                    
                                    switch(strtolower($l['status'])) {
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
                                    <?= !empty($l['keterangan']) ? esc($l['keterangan']) : '-'; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            <!-- Laporan Rekapitulasi Semua Siswa -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-table mr-2"></i>
                        Rekapitulasi Kehadiran Seluruh Siswa
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Periode: <?= date('d M Y', strtotime($startDate)); ?> - <?= date('d M Y', strtotime($endDate)); ?>
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">H</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">S</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">I</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">A</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($laporan)): ?>
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p>Belum ada data absensi untuk periode ini</p>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php $no = 1; ?>
                                <?php foreach ($laporan as $l): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?= esc($l['nis']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 flex-shrink-0 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                                <i class="fas fa-user text-blue-600 text-xs"></i>
                                            </div>
                                            <div class="text-sm font-medium text-gray-900"><?= esc($l['nama_lengkap']); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-900">
                                        <?= $l['total']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-800 text-sm font-medium">
                                            <?= $l['hadir']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-800 text-sm font-medium">
                                            <?= $l['sakit']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-yellow-100 text-yellow-800 text-sm font-medium">
                                            <?= $l['izin']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-red-800 text-sm font-medium">
                                            <?= $l['alpa']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-sm font-bold <?= $l['persentase_hadir'] >= 80 ? 'text-green-600' : ($l['persentase_hadir'] >= 60 ? 'text-yellow-600' : 'text-red-600'); ?>">
                                                <?= $l['persentase_hadir']; ?>%
                                            </span>
                                            <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                                <div class="h-2 rounded-full <?= $l['persentase_hadir'] >= 80 ? 'bg-green-500' : ($l['persentase_hadir'] >= 60 ? 'bg-yellow-500' : 'bg-red-500'); ?>" 
                                                     style="width: <?= $l['persentase_hadir']; ?>%"></div>
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

            <!-- Summary Stats -->
            <?php if (!empty($laporan)): ?>
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php
                $totalSiswa = count($laporan);
                $siswaBaik = count(array_filter($laporan, function($l) { return $l['persentase_hadir'] >= 80; }));
                $siswaCukup = count(array_filter($laporan, function($l) { return $l['persentase_hadir'] >= 60 && $l['persentase_hadir'] < 80; }));
                $siswaKurang = count(array_filter($laporan, function($l) { return $l['persentase_hadir'] < 60; }));
                ?>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600">Total Siswa</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $totalSiswa; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600">Kehadiran Baik (≥80%)</p>
                    <p class="text-2xl font-bold text-green-600"><?= $siswaBaik; ?> siswa</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600">Kehadiran Cukup (60-79%)</p>
                    <p class="text-2xl font-bold text-yellow-600"><?= $siswaCukup; ?> siswa</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600">Kehadiran Kurang (<60%)</p>
                    <p class="text-2xl font-bold text-red-600"><?= $siswaKurang; ?> siswa</p>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>

    <?php else: ?>
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            <i class="fas fa-chart-line text-6xl mb-4"></i>
            <p class="text-lg mb-2">Belum Ada Data Laporan</p>
            <p class="text-sm">Pilih periode tanggal dan klik "Tampilkan Laporan" untuk melihat data</p>
        </div>
    <?php endif; ?>

    <!-- Info Footer -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4 print:hidden">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Keterangan:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li><strong>H</strong> = Hadir, <strong>S</strong> = Sakit, <strong>I</strong> = Izin, <strong>A</strong> = Alpa</li>
                    <li>Persentase kehadiran dihitung dari jumlah hadir dibagi total pertemuan</li>
                    <li>Warna indikator: <span class="font-medium text-green-700">Hijau (≥80%)</span> = Baik, 
                        <span class="font-medium text-yellow-700">Kuning (60-79%)</span> = Cukup, 
                        <span class="font-medium text-red-700">Merah (<60%)</span> = Kurang</li>
                    <li>Laporan dapat dicetak dengan klik tombol "Print Laporan"</li>
                    <li>Pilih siswa tertentu untuk melihat detail kehadiran per mata pelajaran</li>
                </ul>
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
