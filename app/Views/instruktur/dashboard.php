<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>

<div class="p-4 md:p-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h1 class="text-2xl font-bold mb-1">Selamat Datang, <?= esc($instruktur['nama_lengkap']); ?></h1>
                <p class="text-blue-100 text-sm mb-3">Instruktur PKL - <?= esc($tempatPkl['nama_perusahaan'] ?? '-'); ?></p>
                <div class="flex flex-wrap gap-2 text-xs">
                    <?php if (!empty($tempatPkl['kota'])): ?>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-white/20">
                        <i class="fas fa-map-marker-alt mr-1"></i> <?= esc($tempatPkl['kota']); ?>
                    </span>
                    <?php endif; ?>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-white/20">
                        <i class="fas fa-calendar mr-1"></i> <?= esc($tahunAjaran); ?>
                    </span>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="<?= base_url('instruktur/jurnal-pkl'); ?>"
                   class="inline-flex items-center px-4 py-2 bg-white text-indigo-700 rounded-lg hover:bg-blue-50 text-sm font-medium transition-colors">
                    <i class="fas fa-book mr-2"></i>Lihat Jurnal PKL
                </a>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?= $totalSiswa; ?></p>
                    <p class="text-xs text-gray-500">Siswa PKL</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-purple-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?= $statsProgress['total']; ?></p>
                    <p class="text-xs text-gray-500">Total Progress</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?= $statsProgress['submitted']; ?></p>
                    <p class="text-xs text-gray-500">Menunggu Review</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?= $statsProgress['approved']; ?></p>
                    <p class="text-xs text-gray-500">Disetujui</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Siswa PKL -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Siswa PKL</h3>
                            <p class="text-sm text-gray-500">Daftar siswa di tempat PKL Anda</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">
                            <?= $totalSiswa; ?> siswa
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (empty($siswaList)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-user-graduate text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Belum ada siswa yang ditempatkan</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($siswaList as $s): ?>
                        <a href="<?= base_url('instruktur/jurnal-pkl/siswa/' . $s['siswa_id']); ?>"
                           class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-blue-600"><?= strtoupper(substr($s['nama_lengkap'], 0, 1)); ?></span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 group-hover:text-blue-600"><?= esc($s['nama_lengkap']); ?></p>
                                    <p class="text-xs text-gray-500">NIS: <?= esc($s['nis'] ?? '-'); ?> &middot; <?= esc($s['nama_kelas'] ?? '-'); ?></p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Progress Terbaru -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Progress Terbaru</h3>
                    <p class="text-sm text-gray-500">Aktivitas terkini dari siswa PKL</p>
                </div>
                <div class="p-6">
                    <?php if (empty($recentProgress)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Belum ada progress</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($recentProgress as $p):
                            $dateObj = new DateTime($p['tanggal']);
                        ?>
                        <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center text-white text-xs font-bold
                                <?= match($p['status']) {
                                    'approved' => 'bg-green-500',
                                    'submitted' => 'bg-yellow-500',
                                    'revision' => 'bg-orange-500',
                                    default => 'bg-gray-400'
                                } ?>">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 truncate"><?= esc($p['nama_task']); ?></p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ml-2 flex-shrink-0
                                        <?= match($p['status']) {
                                            'approved' => 'bg-green-100 text-green-700',
                                            'submitted' => 'bg-yellow-100 text-yellow-700',
                                            'revision' => 'bg-orange-100 text-orange-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        } ?>">
                                        <?= match($p['status']) {
                                            'approved' => 'Disetujui',
                                            'submitted' => 'Menunggu',
                                            'revision' => 'Revisi',
                                            default => 'Draft'
                                        } ?>
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    <?= esc($p['nama_siswa']); ?> &middot;
                                    <?= $dateObj->format('d M Y'); ?>
                                </p>
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2"><?= esc($p['deskripsi']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="<?= base_url('instruktur/jurnal-pkl'); ?>" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Info Perusahaan -->
            <div class="bg-white rounded-xl shadow">
                <div class="bg-gradient-to-r from-indigo-500 to-blue-500 px-6 py-4 rounded-t-xl">
                    <h3 class="text-lg font-semibold text-white">Info Perusahaan</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-building text-gray-400 mt-0.5 w-5"></i>
                            <div>
                                <p class="text-xs text-gray-500">Nama Perusahaan</p>
                                <p class="text-sm font-medium text-gray-900"><?= esc($tempatPkl['nama_perusahaan'] ?? '-'); ?></p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt text-gray-400 mt-0.5 w-5"></i>
                            <div>
                                <p class="text-xs text-gray-500">Alamat</p>
                                <p class="text-sm font-medium text-gray-900"><?= esc($tempatPkl['alamat'] ?? '-'); ?></p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-city text-gray-400 mt-0.5 w-5"></i>
                            <div>
                                <p class="text-xs text-gray-500">Kota</p>
                                <p class="text-sm font-medium text-gray-900"><?= esc($tempatPkl['kota'] ?? '-'); ?></p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-phone text-gray-400 mt-0.5 w-5"></i>
                            <div>
                                <p class="text-xs text-gray-500">Telepon</p>
                                <p class="text-sm font-medium text-gray-900"><?= esc($tempatPkl['telepon'] ?? '-'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pembimbing Sekolah -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Pembimbing Sekolah</h3>
                </div>
                <div class="p-6">
                    <?php if (empty($pembimbingList)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-chalkboard-teacher text-3xl text-gray-300 mb-2"></i>
                        <p class="text-sm text-gray-500">Belum ada pembimbing</p>
                    </div>
                    <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($pembimbingList as $p): ?>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-tie text-indigo-600 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate"><?= esc($p['nama_guru'] ?? '-'); ?></p>
                                <p class="text-xs text-gray-500">NIP: <?= esc($p['nip'] ?? '-'); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
