<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-book mr-2 text-indigo-600"></i>Jurnal PKL
        </h1>
        <p class="text-gray-600 mt-1">Progress pekerjaan siswa PKL di perusahaan Anda</p>
    </div>

    <?= view('components/alerts') ?>

    <?php if (empty($siswaList)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <i class="fas fa-user-graduate text-4xl text-gray-300 mb-3"></i>
        <h3 class="text-lg font-semibold text-gray-700">Belum Ada Siswa</h3>
        <p class="text-gray-500 mt-1">Belum ada siswa yang ditempatkan di tempat PKL ini</p>
    </div>
    <?php else: ?>
    <div class="space-y-3">
        <?php foreach ($siswaList as $s):
            $stats = $siswaStats[$s['siswa_id']] ?? null;
            $totalProgress = $stats ? (int)$stats['total_progress'] : 0;
            $approvedCount = $stats ? (int)$stats['approved'] : 0;
            $submittedCount = $stats ? (int)$stats['submitted'] : 0;
            $revisionCount = $stats ? (int)$stats['revision'] : 0;
            $lastActivity = $stats ? $stats['last_activity'] : null;
        ?>
        <a href="<?= base_url('instruktur/jurnal-pkl/siswa/' . $s['siswa_id']); ?>"
           class="block bg-white rounded-xl shadow-sm hover:shadow-md transition p-5 group">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-lg font-bold text-indigo-600"><?= strtoupper(substr($s['nama_lengkap'], 0, 1)); ?></span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 group-hover:text-indigo-600"><?= esc($s['nama_lengkap']); ?></h3>
                        <p class="text-xs text-gray-500">NIS: <?= esc($s['nis'] ?? '-'); ?> &middot; <?= esc($s['nama_kelas'] ?? '-'); ?></p>
                        <?php if ($lastActivity): ?>
                        <p class="text-xs text-gray-400 mt-1">Aktivitas terakhir: <?= date('d M Y', strtotime($lastActivity)); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <?php if ($totalProgress > 0): ?>
                    <div class="hidden sm:flex items-center gap-2 text-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-700" title="Disetujui">
                            <i class="fas fa-check mr-1"></i><?= $approvedCount ?>
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700" title="Menunggu">
                            <i class="fas fa-clock mr-1"></i><?= $submittedCount ?>
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-orange-100 text-orange-700" title="Revisi">
                            <i class="fas fa-edit mr-1"></i><?= $revisionCount ?>
                        </span>
                    </div>
                    <?php else: ?>
                    <span class="text-xs text-gray-400">Belum ada progress</span>
                    <?php endif; ?>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
