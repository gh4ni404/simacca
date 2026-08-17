<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">
        <i class="fas fa-mosque text-green-600 mr-2"></i>Laporan Absensi Shalat
    </h1>
    <p class="text-gray-600">Rekap kehadiran siswa saat piket shalat</p>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <form class="grid grid-cols-1 md:grid-cols-3 gap-4" method="get" action="<?= base_url('guru/laporan/absensi-shalat'); ?>">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
            <input type="date" name="from" value="<?= esc($from); ?>" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="to" value="<?= esc($to); ?>" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div class="flex items-end">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fas fa-filter mr-1"></i>Terapkan
            </button>
        </div>
    </form>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Sesi Piket</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalSesi; ?></p>
            </div>
            <i class="fas fa-clock text-green-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Kehadiran</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalKehadiran; ?></p>
            </div>
            <i class="fas fa-user-check text-blue-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Siswa Unik</p>
                <p class="text-2xl font-bold text-gray-800"><?= $siswaUnik; ?></p>
            </div>
            <i class="fas fa-users text-purple-500 text-2xl"></i>
        </div>
    </div>
</div>

<!-- Detail Rekap -->
<?php if (!empty($rekap)): ?>
    <?php foreach ($rekap as $date => $rows): ?>
    <div class="bg-white rounded-xl shadow p-6 mb-4">
        <h3 class="text-sm font-semibold text-gray-600 mb-3">
            <i class="fas fa-calendar-day text-orange-500 mr-1"></i>
            <?= date('l, d F Y', strtotime($date)); ?>
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Siswa</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Guru Piket</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Waktu Absen</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($rows as $i => $row): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?= $i + 1; ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= esc($row['nis']); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($row['nama_lengkap']); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= esc($row['nama_kelas']); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?= esc($row['nama_guru'] ?? '-'); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-500">
                            <?= date('H:i', strtotime($row['waktu_absen'])); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="bg-white rounded-xl shadow p-8 text-center">
        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">Belum ada data absensi shalat untuk periode ini.</p>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
