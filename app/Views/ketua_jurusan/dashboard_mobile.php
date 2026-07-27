<?= $this->extend('templates/mobile_layout') ?>

<?= $this->section('content') ?>

<!-- Mobile Header -->
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-lg p-4 mb-4 text-white">
    <h1 class="text-xl font-bold mb-1">Dashboard Ketua Jurusan</h1>
    <div class="flex items-center space-x-3 text-indigo-100 text-sm">
        <span><i class="fas fa-user-tie mr-1"></i> <?= esc($guru['nama_lengkap']) ?></span>
        <span><i class="fas fa-graduation-cap mr-1"></i> <?= esc($jurusan) ?></span>
    </div>
</div>

<?= render_flash_message() ?>

<!-- Stats Cards -->
<div class="grid grid-cols-2 gap-3 mb-4">
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600"><?= $stats['total_siswa_pkl'] ?></div>
            <div class="text-xs text-gray-500 font-medium">Siswa PKL</div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600"><?= $stats['total_tasks'] ?></div>
            <div class="text-xs text-gray-500 font-medium">Total Tasks</div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600"><?= $stats['approved'] ?></div>
            <div class="text-xs text-gray-500 font-medium">Disetujui</div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-amber-600"><?= $stats['persentase_approval'] ?>%</div>
            <div class="text-xs text-gray-500 font-medium">Approval</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-xl shadow p-4 mb-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">Aksi Cepat</h3>
    <div class="grid grid-cols-3 gap-3">
        <a href="<?= base_url('ketua-jurusan/jurnal-pkl') ?>" class="flex flex-col items-center p-3 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
            <i class="fas fa-book text-blue-500 text-xl mb-1"></i>
            <span class="text-xs font-medium text-blue-700">Jurnal PKL</span>
        </a>
        <a href="<?= base_url('ketua-jurusan/siswa-pkl') ?>" class="flex flex-col items-center p-3 bg-green-50 rounded-xl hover:bg-green-100 transition-colors">
            <i class="fas fa-users text-green-500 text-xl mb-1"></i>
            <span class="text-xs font-medium text-green-700">Siswa PKL</span>
        </a>
        <a href="<?= base_url('ketua-jurusan/absensi-pkl') ?>" class="flex flex-col items-center p-3 bg-amber-50 rounded-xl hover:bg-amber-100 transition-colors">
            <i class="fas fa-clipboard-check text-amber-500 text-xl mb-1"></i>
            <span class="text-xs font-medium text-amber-700">Absensi PKL</span>
        </a>
    </div>
</div>

<!-- Status Progress -->
<div class="bg-white rounded-xl shadow p-4 mb-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">Status Progress</h3>
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-green-50 rounded-lg p-3 text-center border border-green-200">
            <div class="text-xl font-bold text-green-600"><?= $stats['approved'] ?></div>
            <div class="text-xs text-gray-600">Disetujui</div>
        </div>
        <div class="bg-yellow-50 rounded-lg p-3 text-center border border-yellow-200">
            <div class="text-xl font-bold text-yellow-600"><?= $stats['submitted'] ?></div>
            <div class="text-xs text-gray-600">Menunggu</div>
        </div>
        <div class="bg-blue-50 rounded-lg p-3 text-center border border-blue-200">
            <div class="text-xl font-bold text-blue-600"><?= $stats['verified'] ?></div>
            <div class="text-xs text-gray-600">Verified Instruktur</div>
        </div>
        <div class="bg-orange-50 rounded-lg p-3 text-center border border-orange-200">
            <div class="text-xl font-bold text-orange-600"><?= $stats['revision'] ?></div>
            <div class="text-xs text-gray-600">Revisi</div>
        </div>
    </div>
</div>

<!-- Per Kelas -->
<?php if (!empty($stats['per_kelas'])): ?>
<div class="bg-white rounded-xl shadow p-4 mb-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">Statistik Per Kelas</h3>
    <div class="space-y-3">
        <?php foreach ($stats['per_kelas'] as $kelas): ?>
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <div class="flex justify-between items-center mb-1">
                    <span class="font-semibold text-gray-800 text-sm"><?= esc($kelas['nama_kelas']) ?></span>
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full"><?= $kelas['total_siswa'] ?> siswa</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-emerald-500 h-2 rounded-full" style="width: <?= $kelas['total_progress'] > 0 ? round(($kelas['approved'] / max($kelas['total_progress'], 1)) * 100) : 0 ?>%"></div>
                </div>
                <div class="text-xs text-gray-500 mt-1"><?= $kelas['total_progress'] ?> progress</div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
