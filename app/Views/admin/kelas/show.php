<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Detail Kelas: <?= esc($kelas['nama_kelas'] ?? '-'); ?></h1>
    <p class="text-gray-600">Informasi lengkap data kelas dan siswa</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow p-6 lg:col-span-2">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Siswa</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JK</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach (($siswa ?? []) as $s): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($s['nis']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($s['nama_lengkap']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($s['jenis_kelamin']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($siswa)): ?>
                        <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada siswa pada kelas ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kelas</h2>
        <div class="space-y-2 text-sm text-gray-700">
            <div><span class="font-medium">Tingkat:</span> <?= esc($kelas['tingkat'] ?? '-'); ?></div>
            <div><span class="font-medium">Jurusan:</span> <?= esc($kelas['jurusan'] ?? '-'); ?></div>
            <div><span class="font-medium">Wali Kelas:</span> <?= esc($waliKelas['nama_lengkap'] ?? 'Belum ada'); ?></div>
            <div><span class="font-medium">Total Siswa:</span> <?= (int)($totalSiswa ?? 0); ?></div>
        </div>
        <hr class="my-4">
        <h3 class="text-md font-semibold text-gray-800 mb-2">Absensi Bulan Ini</h3>
        <div class="text-sm text-gray-700">
            <div><span class="font-medium">Total Sesi:</span> <?= (int)($absensiStats['total_sesi'] ?? 0); ?></div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>