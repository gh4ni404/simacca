<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="h-full px-4 md:px-1">
    <?= render_flash_message() ?>

    <!-- Breadcrumb -->
    <nav class="mb-4 text-sm text-gray-500">
        <a href="<?= base_url('ketua-jurusan/dashboard') ?>" class="hover:text-blue-600">Dashboard</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">Siswa PKL</span>
    </nav>

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Siswa PKL</h1>
            <p class="text-sm text-gray-500">Jurusan <?= esc($jurusan) ?> — <?= count($siswaPklList) ?> siswa aktif PKL</p>
        </div>
    </div>

    <?php if (empty($siswaPklList)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-200">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-users text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-700">Belum Ada Siswa PKL</h3>
        <p class="text-gray-500 mt-1">Siswa jurusan <?= esc($jurusan) ?> belum terdaftar PKL</p>
    </div>
    <?php else: ?>

    <!-- Siswa PKL Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($siswaPklList as $siswa):
            $totalTasks = $siswa['total_tasks'] ?? 0;
            $totalProgress = $siswa['total_progress'] ?? 0;
            $approved = $siswa['approved'] ?? 0;
            $persen = $totalProgress > 0 ? round(($approved / $totalProgress) * 100) : 0;
        ?>
        <a href="<?= base_url('ketua-jurusan/siswa-pkl/detail/' . $siswa['siswa_id']) ?>"
           class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-blue-300 transition-all duration-200 overflow-hidden">
            <!-- Card Header -->
            <div class="px-5 py-4 bg-gray-50 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold text-sm">
                        <?= strtoupper(substr(esc($siswa['nama_siswa']), 0, 2)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-800 text-sm truncate"><?= esc($siswa['nama_siswa']) ?></h3>
                        <p class="text-xs text-gray-500"><?= esc($siswa['nis']) ?> — <?= esc($siswa['nama_kelas']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="px-5 py-4">
                <!-- Company Info -->
                <div class="flex items-center gap-2 mb-3 text-xs text-gray-500">
                    <i class="fas fa-building text-gray-400"></i>
                    <span class="truncate"><?= esc($siswa['nama_perusahaan'] ?? '-') ?></span>
                </div>

                <!-- Progress Stats -->
                <div class="grid grid-cols-3 gap-2 mb-3">
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-800"><?= $totalTasks ?></div>
                        <div class="text-[10px] text-gray-500 uppercase">Tasks</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-green-600"><?= $approved ?></div>
                        <div class="text-[10px] text-gray-500 uppercase">Approved</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-yellow-600"><?= $siswa['submitted'] ?? 0 ?></div>
                        <div class="text-[10px] text-gray-500 uppercase">Pending</div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: <?= $persen ?>%"></div>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-[10px] text-gray-500">Progress</span>
                    <span class="text-[10px] font-medium text-gray-700"><?= $persen ?>%</span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
