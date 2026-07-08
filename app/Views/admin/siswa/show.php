<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Detail Siswa</h1>
    <p class="text-gray-600">Informasi lengkap data siswa</p>
</div>

<!-- Profile Card -->
<div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-8 text-white mb-6">
    <div class="flex flex-col md:flex-row items-center">
        <!-- Avatar -->
        <div class="mb-6 md:mb-0 md:mr-8">
            <?php if (!empty($userData['profile_photo'])): ?>
                <img src="<?= base_url('profile-photo/' . esc($userData['profile_photo'])); ?>" 
                     alt="<?= esc($siswa['nama_lengkap']); ?>"
                     class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg">
            <?php else: ?>
                <div class="h-32 w-32 rounded-full bg-white/20 flex items-center justify-center border-4 border-white shadow-lg">
                    <span class="text-5xl font-bold">
                        <?= strtoupper(substr($siswa['nama_lengkap'], 0, 2)); ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Info -->
        <div class="flex-1 text-center md:text-left">
            <h2 class="text-3xl font-bold mb-2"><?= esc($siswa['nama_lengkap']); ?></h2>
            <div class="flex flex-col md:flex-row md:items-center md:space-x-4 text-sm opacity-90">
                <div class="flex items-center justify-center md:justify-start mb-2 md:mb-0">
                    <i class="fas fa-id-card mr-2"></i>
                    <span>NIS: <?= esc($siswa['nis']); ?></span>
                </div>
                <div class="flex items-center justify-center md:justify-start mb-2 md:mb-0">
                    <i class="fas fa-school mr-2"></i>
                    <span><?= esc($kelas['nama_kelas'] ?? '-'); ?></span>
                </div>
                <div class="flex items-center justify-center md:justify-start">
                    <i class="fas <?= $siswa['jenis_kelamin'] == 'L' ? 'fa-mars' : 'fa-venus' ?> mr-2"></i>
                    <span><?= $siswa['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></span>
                </div>
            </div>
            <?php if ($userData['is_active']): ?>
                <div class="mt-3">
                    <span class="inline-block bg-green-500 text-white px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-check-circle mr-1"></i>
                        Aktif
                    </span>
                </div>
            <?php else: ?>
                <div class="mt-3">
                    <span class="inline-block bg-red-500 text-white px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-times-circle mr-1"></i>
                        Tidak Aktif
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 md:mt-0 flex flex-col space-y-2">
            <a href="<?= base_url('admin/siswa/edit/' . $siswa['id']); ?>" 
               class="px-4 py-2 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors flex items-center justify-center">
                <i class="fas fa-edit mr-2"></i>
                Edit Data
            </a>
            <a href="<?= base_url('admin/siswa'); ?>" 
               class="px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>
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