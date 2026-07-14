<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
    <?php
    $bulanIndo = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    ?>

    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 rounded-2xl shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-white/20">
                <i class="fas fa-archive text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold">Arsip Jurnal PKL</h1>
                <p class="text-purple-100 text-sm mt-1">Rekapan dan riwayat jurnal PKL seluruh siswa</p>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <!-- Filter -->
    <form method="GET" class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="<?= esc($filters['start_date']) ?>"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="<?= esc($filters['end_date']) ?>"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Siswa</label>
                <select name="siswa_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Semua Siswa</option>
                    <?php foreach ($siswaList as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $filters['siswa_id'] == $s['id'] ? 'selected' : '' ?>>
                        <?= esc($s['nama_lengkap']) ?> (<?= esc($s['nis']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium transition-colors">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </div>
    </form>

    <?php if (!empty($stats) && $stats['total_entries'] > 0): ?>
    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-gray-800"><?= $stats['total_entries'] ?></div>
            <div class="text-xs text-gray-500 mt-1">Total Entry</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-gray-800"><?= $stats['total_siswa'] ?></div>
            <div class="text-xs text-gray-500 mt-1">Siswa Aktif</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-gray-800"><?= $stats['total_tasks'] ?></div>
            <div class="text-xs text-gray-500 mt-1">Total Task</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 text-center border-l-4 border-green-400">
            <div class="text-2xl font-bold text-green-600"><?= $stats['total_approved'] ?></div>
            <div class="text-xs text-gray-500 mt-1">Disetujui</div>
        </div>
    </div>

    <!-- Perusahaan breakdown -->
    <?php if (!empty($byTempatPkl)): ?>
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
        <h3 class="font-bold text-gray-800 mb-3"><i class="fas fa-building mr-2 text-purple-600"></i>Perusahaan</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2 px-3 text-gray-500 font-medium">Perusahaan</th>
                        <th class="text-center py-2 px-3 text-gray-500 font-medium">Siswa</th>
                        <th class="text-center py-2 px-3 text-gray-500 font-medium">Progress</th>
                        <th class="text-center py-2 px-3 text-gray-500 font-medium">Disetujui</th>
                        <th class="text-center py-2 px-3 text-gray-500 font-medium">Menunggu</th>
                        <th class="text-center py-2 px-3 text-gray-500 font-medium">Revisi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($byTempatPkl as $tp): ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2.5 px-3">
                            <div class="font-medium text-gray-800"><?= esc($tp['nama_perusahaan']) ?></div>
                            <div class="text-xs text-gray-500"><?= esc($tp['kota'] ?? '') ?></div>
                        </td>
                        <td class="py-2.5 px-3 text-center text-gray-700"><?= $tp['total_siswa'] ?></td>
                        <td class="py-2.5 px-3 text-center text-gray-700"><?= $tp['total_progress'] ?></td>
                        <td class="py-2.5 px-3 text-center text-green-600 font-medium"><?= $tp['approved'] ?></td>
                        <td class="py-2.5 px-3 text-center text-yellow-600"><?= $tp['submitted'] ?></td>
                        <td class="py-2.5 px-3 text-center text-orange-600"><?= $tp['revision'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Kelas breakdown -->
    <?php if (!empty($byKelas)): ?>
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
        <h3 class="font-bold text-gray-800 mb-3"><i class="fas fa-school mr-2 text-purple-600"></i>Kelas</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2 px-3 text-gray-500 font-medium">Kelas</th>
                        <th class="text-center py-2 px-3 text-gray-500 font-medium">Siswa</th>
                        <th class="text-center py-2 px-3 text-gray-500 font-medium">Progress</th>
                        <th class="text-center py-2 px-3 text-gray-500 font-medium">Disetujui</th>
                        <th class="text-center py-2 px-3 text-gray-500 font-medium">Menunggu</th>
                        <th class="text-center py-2 px-3 text-gray-500 font-medium">Revisi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($byKelas as $k): ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2.5 px-3 font-medium text-gray-800"><?= esc($k['nama_kelas']) ?></td>
                        <td class="py-2.5 px-3 text-center text-gray-700"><?= $k['total_siswa'] ?></td>
                        <td class="py-2.5 px-3 text-center text-gray-700"><?= $k['total_progress'] ?></td>
                        <td class="py-2.5 px-3 text-center text-green-600 font-medium"><?= $k['approved'] ?></td>
                        <td class="py-2.5 px-3 text-center text-yellow-600"><?= $k['submitted'] ?></td>
                        <td class="py-2.5 px-3 text-center text-orange-600"><?= $k['revision'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Per Siswa Summary -->
    <?php if (!empty($summary)): ?>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-bold text-gray-800"><i class="fas fa-users mr-2 text-purple-600"></i>Rekap Per Siswa</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">Nama Siswa</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">Kelas</th>
                        <th class="text-left py-3 px-4 text-gray-500 font-medium">Perusahaan</th>
                        <th class="text-center py-3 px-4 text-gray-500 font-medium">Task</th>
                        <th class="text-center py-3 px-4 text-gray-500 font-medium">Progress</th>
                        <th class="text-center py-3 px-4 text-gray-500 font-medium">Disetujui</th>
                        <th class="text-center py-3 px-4 text-gray-500 font-medium">Menunggu</th>
                        <th class="text-center py-3 px-4 text-gray-500 font-medium">Revisi</th>
                        <th class="text-center py-3 px-4 text-gray-500 font-medium">Periode</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($summary as $row): ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <div class="font-medium text-gray-800"><?= esc($row['nama_lengkap']) ?></div>
                            <div class="text-xs text-gray-500">NIS: <?= esc($row['nis']) ?></div>
                        </td>
                        <td class="py-3 px-4 text-gray-700"><?= esc($row['nama_kelas']) ?></td>
                        <td class="py-3 px-4 text-gray-700 text-xs"><?= esc($row['nama_perusahaan'] ?? '-') ?></td>
                        <td class="py-3 px-4 text-center text-gray-700"><?= $row['total_tasks'] ?></td>
                        <td class="py-3 px-4 text-center text-gray-700"><?= $row['total_progress'] ?></td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-medium"><?= $row['approved'] ?></span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-medium"><?= $row['submitted'] ?></span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded-full bg-orange-100 text-orange-700 text-xs font-medium"><?= $row['revision'] ?></span>
                        </td>
                        <td class="py-3 px-4 text-center text-xs text-gray-500">
                            <?php
                            $t1 = new DateTime($row['tanggal_pertama']);
                            $t2 = new DateTime($row['tanggal_terakhir']);
                            echo $t1->format('d/m') . ' - ' . $t2->format('d/m');
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-search text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-700">Tidak Ada Data</h3>
        <p class="text-gray-500 mt-1">Tidak ada data arsip untuk filter yang dipilih</p>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
