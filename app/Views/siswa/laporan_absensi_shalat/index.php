<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">
        <i class="fas fa-mosque text-green-600 mr-2"></i>Riwayat Absensi Shalat
    </h1>
    <p class="text-gray-600">Riwayat kehadiran shalat kamu</p>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <form class="grid grid-cols-1 md:grid-cols-3 gap-4" method="get" action="<?= base_url('siswa/laporan/absensi-shalat'); ?>">
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
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Kehadiran</p>
                <p class="text-2xl font-bold text-gray-800"><?= $totalHadir; ?> kali</p>
            </div>
            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Periode</p>
                <p class="text-lg font-bold text-gray-800"><?= date('d/m/Y', strtotime($from)); ?> - <?= date('d/m/Y', strtotime($to)); ?></p>
                <p class="text-xs text-gray-400"><?= $hariBerlalu; ?> hari</p>
            </div>
            <i class="fas fa-calendar text-blue-500 text-2xl"></i>
        </div>
    </div>
</div>

<!-- Detail Riwayat -->
<?php if (!empty($rekap)): ?>
    <?php foreach ($rekap as $date => $rows): ?>
    <div class="bg-white rounded-xl shadow p-6 mb-4">
        <h3 class="text-sm font-semibold text-gray-600 mb-3">
            <i class="fas fa-calendar-day text-orange-500 mr-1"></i>
            <?= date('l, d F Y', strtotime($date)); ?>
            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                <?= count($rows); ?>x hadir
            </span>
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Waktu Sesi</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Waktu Absen</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($rows as $i => $row): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?= $i + 1; ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-700">
                            <?= date('H:i', strtotime($row['waktu_sesi'])); ?>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-green-700 font-semibold">
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
        <p class="text-gray-500">Belum ada riwayat absensi shalat untuk periode ini.</p>
        <a href="<?= base_url('siswa/absensi-shalat/scan'); ?>" class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            <i class="fas fa-qrcode mr-2"></i>Scan Sekarang
        </a>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
