<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="h-full px-4 md:px-1">
    <?= render_flash_message() ?>

    <!-- Breadcrumb -->
    <nav class="mb-4 text-sm text-gray-500">
        <a href="<?= base_url('ketua-jurusan/dashboard') ?>" class="hover:text-blue-600">Dashboard</a>
        <span class="mx-2">/</span>
        <a href="<?= base_url('ketua-jurusan/siswa-pkl') ?>" class="hover:text-blue-600">Siswa PKL</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium"><?= esc($siswa['nama_lengkap']) ?></span>
    </nav>

    <!-- Student Profile Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 border-2 border-blue-100 flex items-center justify-center font-bold text-xl shadow">
                <?= strtoupper(substr(esc($siswa['nama_lengkap']), 0, 2)) ?>
            </div>
            <div class="flex-1">
                <h1 class="text-xl font-bold text-gray-800"><?= esc($siswa['nama_lengkap']) ?></h1>
                <p class="text-sm text-gray-500">NIS: <?= esc($siswa['nis']) ?> — <?= esc($siswa['nama_kelas']) ?></p>
                <?php if ($pkl_info): ?>
                    <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-500">
                        <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded-full">
                            <i class="fas fa-building mr-1"></i> <?= esc($pkl_info['nama_perusahaan']) ?>
                        </span>
                        <?php if (!empty($pkl_info['kota'])): ?>
                            <span class="bg-green-50 text-green-700 px-2 py-1 rounded-full">
                                <i class="fas fa-map-marker-alt mr-1"></i> <?= esc($pkl_info['kota']) ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($pkl_info['nama_pembimbing'])): ?>
                            <span class="bg-purple-50 text-purple-700 px-2 py-1 rounded-full">
                                <i class="fas fa-user-tie mr-1"></i> <?= esc($pkl_info['nama_pembimbing']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <a href="<?= base_url('ketua-jurusan/siswa-pkl') ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <?php if (empty($tasks)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-200">
        <i class="fas fa-clipboard-list text-3xl text-gray-400 mb-3"></i>
        <h3 class="text-lg font-semibold text-gray-700">Belum Ada Task</h3>
        <p class="text-gray-500">Siswa belum memiliki task PKL</p>
    </div>
    <?php else: ?>

    <!-- Tasks Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800"><i class="fas fa-list-check mr-2 text-blue-500"></i>Daftar Task PKL</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Judul Task</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kategori</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Progress</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Approved</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($tasks as $i => $task):
                        $totalProg = max((int)($task['total_progress'] ?? 0), 1);
                        $approvedProg = (int)($task['approved_count'] ?? 0);
                        $persen = round(($approvedProg / $totalProg) * 100);
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4 text-sm text-gray-500"><?= $i + 1 ?></td>
                        <td class="px-5 py-4 text-sm font-medium text-gray-800"><?= esc($task['judul']) ?></td>
                        <td class="px-5 py-4">
                            <?php if (!empty($task['kategori_nama'])): ?>
                                <span class="px-2 py-0.5 text-xs font-medium rounded bg-purple-100 text-purple-800"><?= esc($task['kategori_nama']) ?></span>
                            <?php else: ?>
                                <span class="text-gray-400 text-xs">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2 justify-center">
                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: <?= $persen ?>%"></div>
                                </div>
                                <span class="text-xs text-gray-600 font-medium"><?= $persen ?>%</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center text-sm">
                            <span class="font-medium text-green-600"><?= $approvedProg ?></span>
                            <span class="text-gray-400">/</span>
                            <span class="text-gray-600"><?= (int)($task['total_progress'] ?? 0) ?></span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2 py-0.5 text-xs font-medium rounded <?= $task['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' ?>">
                                <?= $task['status'] === 'active' ? 'Aktif' : ucfirst($task['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
