<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
    <div class="mb-6">
        <div class="flex items-center">
            <a href="<?= base_url('instruktur/jurnal-pkl'); ?>" class="mr-4 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800"><?= esc($siswa['nama_lengkap']); ?></h1>
                <p class="text-gray-600 mt-1">NIS: <?= esc($siswa['nis'] ?? '-'); ?> &middot; <?= esc($siswa['nama_kelas'] ?? '-'); ?></p>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <?php if (empty($tasks)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i>
        <h3 class="text-lg font-semibold text-gray-700">Belum Ada Task</h3>
        <p class="text-gray-500 mt-1">Siswa belum membuat task pekerjaan</p>
    </div>
    <?php else: ?>
    <div class="space-y-3">
        <?php foreach ($tasks as $t):
            $progressCount = (int)$t['total_progress'];
            $approvedCount = (int)$t['approved_count'];
        ?>
        <a href="<?= base_url('instruktur/jurnal-pkl/task/' . $t['id']); ?>"
           class="block bg-white rounded-xl shadow-sm hover:shadow-md transition p-5 group">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-semibold text-gray-800 group-hover:text-indigo-600"><?= esc($t['judul']); ?></h3>
                        <?php if (!empty($t['kategori_nama'])): ?>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700"><?= esc($t['kategori_nama']); ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        <?= $progressCount ?> progress
                        <?php if ($progressCount > 0): ?>
                        &middot; <?= $approvedCount ?> disetujui
                        <?php endif; ?>
                    </p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 ml-3"></i>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
