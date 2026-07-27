<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-lg p-6 mb-6 text-white">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold mb-2">Dashboard Ketua Jurusan</h1>
            <div class="flex items-center space-x-4 text-indigo-100">
                <span class="flex items-center">
                    <i class="fas fa-user-tie mr-2"></i>
                    <?= esc($guru['nama_lengkap']) ?>
                </span>
                <span class="flex items-center">
                    <i class="fas fa-graduation-cap mr-2"></i>
                    Jurusan <?= esc($jurusan) ?>
                </span>
            </div>
        </div>
        <div class="text-right">
            <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 backdrop-blur-sm">
                <div class="text-sm font-medium">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <?= date('l, d F Y') ?>
                </div>
                <div class="text-sm">
                    <i class="fas fa-clock mr-2"></i>
                    <?= date('H:i') ?> WIB
                </div>
            </div>
        </div>
    </div>
</div>

<?= render_flash_message() ?>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Siswa PKL -->
    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 mb-1">Siswa PKL</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $stats['total_siswa_pkl'] ?></h3>
                    <p class="text-xs text-gray-400 mt-1">Jurusan <?= esc($jurusan) ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-graduate text-white text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 px-6 py-3 rounded-b-xl">
            <a href="<?= base_url('ketua-jurusan/siswa-pkl') ?>" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center">
                Lihat Data <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
    </div>

    <!-- Total Tasks -->
    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Tasks</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $stats['total_tasks'] ?></h3>
                    <p class="text-xs text-gray-400 mt-1">Task yang dibuat</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-list-check text-white text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-purple-50 px-6 py-3 rounded-b-xl">
            <a href="<?= base_url('ketua-jurusan/jurnal-pkl') ?>" class="text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center">
                Lihat Jurnal <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
    </div>

    <!-- Total Progress -->
    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Progress</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $stats['total_progress'] ?></h3>
                    <p class="text-xs text-gray-400 mt-1">Entri jurnal</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-book text-white text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-green-50 px-6 py-3 rounded-b-xl">
            <a href="<?= base_url('ketua-jurusan/jurnal-pkl') ?>" class="text-sm text-green-600 hover:text-green-800 font-medium flex items-center">
                Lihat Detail <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
    </div>

    <!-- Persentase Approval -->
    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 mb-1">Approval Rate</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $stats['persentase_approval'] ?>%</h3>
                    <p class="text-xs text-gray-400 mt-1">Tingkat persetujuan</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-chart-line text-white text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-amber-50 px-6 py-3 rounded-b-xl">
            <span class="text-sm text-amber-600 font-medium">
                <?= $stats['approved'] ?> disetujui / <?= $stats['total_progress'] ?> total
            </span>
        </div>
    </div>
</div>

<!-- Status Breakdown + Per Kelas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Status Breakdown -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
            <h5 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-chart-pie mr-3"></i>
                Status Progress
            </h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-green-600 mb-1"><?= $stats['approved'] ?></div>
                    <div class="text-sm text-gray-600 font-medium">Disetujui</div>
                    <div class="w-full bg-green-200 rounded-full h-2 mt-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: <?= $stats['total_progress'] > 0 ? round(($stats['approved'] / $stats['total_progress']) * 100) : 0 ?>%"></div>
                    </div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-yellow-600 mb-1"><?= $stats['submitted'] ?></div>
                    <div class="text-sm text-gray-600 font-medium">Menunggu Catatan</div>
                    <div class="w-full bg-yellow-200 rounded-full h-2 mt-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: <?= $stats['total_progress'] > 0 ? round(($stats['submitted'] / $stats['total_progress']) * 100) : 0 ?>%"></div>
                    </div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-1"><?= $stats['verified'] ?></div>
                    <div class="text-sm text-gray-600 font-medium">Verified Instruktur</div>
                    <div class="w-full bg-blue-200 rounded-full h-2 mt-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: <?= $stats['total_progress'] > 0 ? round(($stats['verified'] / $stats['total_progress']) * 100) : 0 ?>%"></div>
                    </div>
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center">
                    <div class="text-3xl font-bold text-orange-600 mb-1"><?= $stats['revision'] ?></div>
                    <div class="text-sm text-gray-600 font-medium">Revisi</div>
                    <div class="w-full bg-orange-200 rounded-full h-2 mt-2">
                        <div class="bg-orange-500 h-2 rounded-full" style="width: <?= $stats['total_progress'] > 0 ? round(($stats['revision'] / $stats['total_progress']) * 100) : 0 ?>%"></div>
                    </div>
                </div>
            </div>
            <div class="mt-4 bg-gray-50 rounded-lg p-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Draft</span>
                    <span class="font-bold text-gray-700"><?= $stats['draft'] ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Per Kelas Stats -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-4">
            <h5 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-school mr-3"></i>
                Statistik Per Kelas
            </h5>
        </div>
        <div class="p-6">
            <?php if (!empty($stats['per_kelas'])): ?>
                <div class="space-y-4">
                    <?php foreach ($stats['per_kelas'] as $kelas): ?>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex justify-between items-center mb-2">
                                <h6 class="font-semibold text-gray-800"><?= esc($kelas['nama_kelas']) ?></h6>
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-medium">
                                    <?= $kelas['total_siswa'] ?> siswa
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-emerald-500 h-3 rounded-full transition-all duration-500"
                                     style="width: <?= $kelas['total_progress'] > 0 ? round(($kelas['approved'] / max($kelas['total_progress'], 1)) * 100) : 0 ?>%"></div>
                            </div>
                            <div class="flex justify-between mt-2 text-xs text-gray-500">
                                <span><?= $kelas['total_progress'] ?> progress</span>
                                <span><?= $kelas['total_progress'] > 0 ? round(($kelas['approved'] / max($kelas['total_progress'], 1)) * 100) : 0 ?>% approved</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-school text-4xl mb-3"></i>
                    <p>Belum ada data kelas</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-violet-500 to-purple-600 px-6 py-4">
        <h5 class="text-lg font-semibold text-white flex items-center">
            <i class="fas fa-bolt mr-3"></i>
            Aksi Cepat
        </h5>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <a href="<?= base_url('ketua-jurusan/jurnal-pkl') ?>"
               class="group bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-500 hover:to-blue-600 rounded-xl p-6 text-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl border-2 border-blue-200 hover:border-blue-500">
                <div class="flex flex-col items-center">
                    <div class="w-14 h-14 bg-blue-500 group-hover:bg-white rounded-full flex items-center justify-center mb-3 transition-colors duration-300">
                        <i class="fas fa-book text-white group-hover:text-blue-500 text-xl"></i>
                    </div>
                    <span class="text-sm font-semibold text-blue-700 group-hover:text-white transition-colors duration-300">Jurnal PKL</span>
                </div>
            </a>

            <a href="<?= base_url('ketua-jurusan/siswa-pkl') ?>"
               class="group bg-gradient-to-br from-green-50 to-green-100 hover:from-green-500 hover:to-green-600 rounded-xl p-6 text-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl border-2 border-green-200 hover:border-green-500">
                <div class="flex flex-col items-center">
                    <div class="w-14 h-14 bg-green-500 group-hover:bg-white rounded-full flex items-center justify-center mb-3 transition-colors duration-300">
                        <i class="fas fa-users text-white group-hover:text-green-500 text-xl"></i>
                    </div>
                    <span class="text-sm font-semibold text-green-700 group-hover:text-white transition-colors duration-300">Siswa PKL</span>
                </div>
            </a>

            <a href="<?= base_url('ketua-jurusan/absensi-pkl') ?>"
               class="group bg-gradient-to-br from-amber-50 to-amber-100 hover:from-amber-500 hover:to-amber-600 rounded-xl p-6 text-center transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl border-2 border-amber-200 hover:border-amber-500">
                <div class="flex flex-col items-center">
                    <div class="w-14 h-14 bg-amber-500 group-hover:bg-white rounded-full flex items-center justify-center mb-3 transition-colors duration-300">
                        <i class="fas fa-clipboard-check text-white group-hover:text-amber-500 text-xl"></i>
                    </div>
                    <span class="text-sm font-semibold text-amber-700 group-hover:text-white transition-colors duration-300">Absensi PKL</span>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Recent Jurnal Activity -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-cyan-500 to-blue-500 px-6 py-4">
        <div class="flex justify-between items-center">
            <h5 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-history mr-3"></i>
                Jurnal PKL Terbaru
            </h5>
            <a href="<?= base_url('ketua-jurusan/jurnal-pkl') ?>" class="text-sm text-white bg-white bg-opacity-20 hover:bg-opacity-30 px-3 py-1 rounded-full transition-colors">
                Lihat Semua <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <?php if (!empty($recentJurnal)): ?>
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Task</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($recentJurnal as $jurnal): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                <?= date('d/m/Y', strtotime($jurnal['tanggal'])) ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= esc($jurnal['nama_siswa']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?= esc($jurnal['nama_kelas']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate"><?= esc($jurnal['nama_task']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $statusBadge = match($jurnal['status']) {
                                    'approved' => 'bg-green-100 text-green-800',
                                    'submitted' => 'bg-yellow-100 text-yellow-800',
                                    'revision' => 'bg-red-100 text-red-800',
                                    'verified' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                                $statusLabel = match($jurnal['status']) {
                                    'approved' => 'Disetujui',
                                    'submitted' => 'Menunggu',
                                    'revision' => 'Revisi',
                                    'verified' => 'Terverifikasi',
                                    default => ucfirst($jurnal['status']),
                                };
                                ?>
                                <span class="px-2 py-1 text-xs font-medium rounded <?= $statusBadge ?>">
                                    <?= $statusLabel ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-inbox text-gray-300 text-4xl"></i>
                </div>
                <p class="text-gray-500 font-medium text-lg">Belum Ada Jurnal PKL</p>
                <p class="text-gray-400 text-sm mt-2">Data jurnal akan muncul di sini</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
