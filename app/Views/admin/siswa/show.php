<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Detail Siswa</h1>
    <p class="text-gray-600">Informasi lengkap data siswa</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow p-6 lg:col-span-2">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Profil</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
            <div>
                <div class="font-medium text-gray-500">NIS</div>
                <div class="text-gray-900"><?= esc($siswa['nis']); ?></div>
            </div>
            <div>
                <div class="font-medium text-gray-500">Nama Lengkap</div>
                <div class="text-gray-900"><?= esc($siswa['nama_lengkap']); ?></div>
            </div>
            <div>
                <div class="font-medium text-gray-500">Jenis Kelamin</div>
                <div class="text-gray-900"><?= esc($siswa['jenis_kelamin']); ?></div>
            </div>
            <div>
                <div class="font-medium text-gray-500">Kelas</div>
                <div class="text-gray-900"><?= esc($kelas['nama_kelas'] ?? '-'); ?></div>
            </div>
            <div>
                <div class="font-medium text-gray-500">Tahun Ajaran</div>
                <div class="text-gray-900"><?= esc($siswa['tahun_ajaran']); ?></div>
            </div>
            <div>
                <div class="font-medium text-gray-500">Email</div>
                <div class="text-gray-900"><?= esc($userData['email'] ?? '-'); ?></div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Statistik Absensi</h2>
        <?php $stats = $absensiStats ?? []; ?>
        <div class="space-y-2 text-sm text-gray-700">
            <div><span class="font-medium">Hadir:</span> <?= (int)($stats['hadir'] ?? 0); ?></div>
            <div><span class="font-medium">Izin:</span> <?= (int)($stats['izin'] ?? 0); ?></div>
            <div><span class="font-medium">Sakit:</span> <?= (int)($stats['sakit'] ?? 0); ?></div>
            <div><span class="font-medium">Alpa:</span> <?= (int)($stats['alpa'] ?? 0); ?></div>
            <div class="mt-2 pt-2 border-t"><span class="font-medium">Total Sesi:</span> <?= (int)($stats['total_sesi'] ?? 0); ?></div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>