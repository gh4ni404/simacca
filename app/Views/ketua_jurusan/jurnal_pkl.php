<?php
$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
?>
<?= $this->extend(get_device_layout()) ?>

<?= $this->section('actions') ?>
<div class="hidden md:flex items-center gap-3 bg-gray-50 border border-gray-200 px-4 py-1.5 rounded-full shadow-sm">
    <div class="flex flex-col items-end border-r border-gray-200 pr-3">
        <span class="text-xs font-semibold text-gray-800"><?= date('d') ?> <?= $bulanIndo[(int)date('m')] ?> <?= date('Y') ?></span>
    </div>
    <div class="flex gap-4 text-center">
        <div>
            <span class="block text-xs font-bold text-blue-600"><?= $stats['total_progress'] ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase">Total</span>
        </div>
        <div>
            <span class="block text-xs font-bold text-green-600"><?= $stats['approved'] ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase">Setuju</span>
        </div>
        <div>
            <span class="block text-xs font-bold text-yellow-600"><?= ($stats['submitted'] ?? 0) + ($stats['verified_by_instruktur'] ?? 0) ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase">Menunggu</span>
        </div>
        <div>
            <span class="block text-xs font-bold text-orange-600"><?= $stats['revision'] ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase">Revisi</span>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="h-full px-4 md:px-1">
    <?= render_flash_message() ?>

    <!-- Breadcrumb -->
    <nav class="mb-4 text-sm text-gray-500">
        <a href="<?= base_url('ketua-jurusan/dashboard') ?>" class="hover:text-blue-600">Dashboard</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">Jurnal PKL</span>
    </nav>

    <!-- Page Title -->
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Monitoring Jurnal PKL</h1>
        <p class="text-sm text-gray-500">Jurusan <?= esc($jurusan) ?> — Semua jurnal/progress siswa PKL</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="<?= base_url('ketua-jurusan/jurnal-pkl') ?>" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Kelas</label>
                <select name="kelas_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kelas</option>
                    <?php foreach ($kelasList as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= ($filters['kelas_id'] ?? '') == $k['id'] ? 'selected' : '' ?>>
                            <?= esc($k['nama_kelas']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="submitted" <?= ($filters['status'] ?? '') === 'submitted' ? 'selected' : '' ?>>Menunggu</option>
                    <option value="verified_by_instruktur" <?= ($filters['status'] ?? '') === 'verified_by_instruktur' ? 'selected' : '' ?>>Verified Instruktur</option>
                    <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="revision" <?= ($filters['status'] ?? '') === 'revision' ? 'selected' : '' ?>>Revisi</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="tanggal_start" value="<?= $filters['tanggal_start'] ?? '' ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="tanggal_end" value="<?= $filters['tanggal_end'] ?? '' ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex-1">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="<?= base_url('ketua-jurusan/jurnal-pkl') ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <?php if (empty($grouped)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-200">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
            <i class="fas fa-inbox text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-700">Belum Ada Jurnal</h3>
        <p class="text-gray-500 mt-1">Tidak ada jurnal PKL untuk filter yang dipilih</p>
    </div>
    <?php else: ?>

    <!-- Student List -->
    <div class="space-y-4">
        <?php foreach ($grouped as $student):
            $totalProgress = count($student['progress']);
            $approvedCount = 0;
            foreach ($student['progress'] as $p) {
                if ($p['status'] === 'approved') $approvedCount++;
            }
            $persenLengkap = $totalProgress > 0 ? round(($approvedCount / $totalProgress) * 100) : 0;
        ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Student Header -->
            <div class="px-5 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <?php if (!empty($student['profile_photo'])): ?>
                        <img src="<?= base_url('profile-photo/' . esc($student['profile_photo'])) ?>"
                             class="w-10 h-10 rounded-full object-cover border border-gray-200"
                             alt="<?= esc($student['nama_siswa']) ?>">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold text-sm">
                            <?= strtoupper(substr(esc($student['nama_siswa']), 0, 2)) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h3 class="font-semibold text-gray-800"><?= esc($student['nama_siswa']) ?></h3>
                        <p class="text-xs text-gray-500"><?= esc($student['nis']) ?> — <?= esc($student['nama_kelas']) ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right text-xs text-gray-500">
                        <div><?= $approvedCount ?>/<?= $totalProgress ?> approved</div>
                        <div class="w-20 bg-gray-200 rounded-full h-1.5 mt-1">
                            <div class="bg-green-500 h-1.5 rounded-full" style="width: <?= $persenLengkap ?>%"></div>
                        </div>
                    </div>
                    <a href="<?= base_url('ketua-jurusan/jurnal-pkl/detail/' . $student['siswa_id']) ?>"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                        Detail <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Progress Entries -->
            <div class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                <?php foreach (array_slice($student['progress'], 0, 5) as $prog): ?>
                <div class="px-5 py-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($prog['tanggal'])) ?></span>
                                <span class="text-sm font-medium text-gray-800 truncate"><?= esc($prog['nama_task']) ?></span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5 truncate"><?= esc(mb_strimwidth($prog['deskripsi'], 0, 100, '...')) ?></p>
                        </div>
                        <div class="ml-3 flex-shrink-0">
                            <?php
                            $statusBadge = match($prog['status']) {
                                'approved' => 'bg-green-100 text-green-800',
                                'submitted' => 'bg-yellow-100 text-yellow-800',
                                'revision' => 'bg-red-100 text-red-800',
                                'verified_by_instruktur' => 'bg-blue-100 text-blue-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                            $statusLabel = match($prog['status']) {
                                'approved' => 'Disetujui',
                                'submitted' => 'Menunggu',
                                'revision' => 'Revisi',
                                'verified_by_instruktur' => 'Verified',
                                default => ucfirst($prog['status']),
                            };
                            ?>
                            <span class="px-2 py-0.5 text-xs font-medium rounded <?= $statusBadge ?>"><?= $statusLabel ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if ($totalProgress > 5): ?>
                <div class="px-5 py-2 text-center text-xs text-blue-600 font-medium bg-blue-50">
                    +<?= $totalProgress - 5 ?> entri lainnya
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
