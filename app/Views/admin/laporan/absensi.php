<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Laporan Absensi</h1>
    <p class="text-gray-600">Rekapitulasi absensi per periode dan kelas</p>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <form class="grid grid-cols-1 md:grid-cols-4 gap-4" method="get" action="<?= base_url('admin/laporan/absensi'); ?>">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
            <input type="date" name="from" value="<?= esc($from); ?>" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="to" value="<?= esc($to); ?>" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
            <select name="kelas_id" class="w-full border rounded-lg px-3 py-2">
                <option value="">Semua Kelas</option>
                <?php if (!empty($kelasList)): ?>
                    <?php foreach ($kelasList as $id => $nama): ?>
                        <option value="<?= $id; ?>" <?= ($kelasId == $id ? 'selected' : ''); ?>><?= esc($nama); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Terapkan</button>
        </div>
    </form>
</div>

<!-- Ringkasan -->
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Ringkasan Per Kelas</h2>
        <span class="text-sm text-gray-500">Periode: <?= date('d/m/Y', strtotime($from)); ?> - <?= date('d/m/Y', strtotime($to)); ?></span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hadir</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Izin</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sakit</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Alpa</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sesi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($summary) && is_array($summary)): ?>
                    <?php foreach ($summary as $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($row['nama_kelas'] ?? ($row['kelas'] ?? '-')); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-green-700"><?= (int)($row['hadir'] ?? 0); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-blue-700"><?= (int)($row['izin'] ?? 0); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-yellow-700"><?= (int)($row['sakit'] ?? 0); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-red-700"><?= (int)($row['alpa'] ?? 0); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900"><?= (int)($row['total_sesi'] ?? (($row['hadir'] ?? 0)+($row['izin'] ?? 0)+($row['sakit'] ?? 0)+($row['alpa'] ?? 0))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada data untuk periode ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>