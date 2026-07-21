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
        <div class="bg-white rounded-xl shadow p-5 cursor-pointer hover:shadow-md hover:bg-blue-50 transition-all"
             onclick="document.getElementById('modalDaftarSiswa').classList.remove('hidden')">
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
        <div class="bg-white rounded-xl shadow p-5 cursor-pointer hover:shadow-md hover:bg-purple-50 transition-all"
             onclick="document.getElementById('modalTotalProgress').classList.remove('hidden')">
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
        <div class="bg-white rounded-xl shadow p-5 cursor-pointer hover:shadow-md hover:bg-yellow-50 transition-all"
             onclick="document.getElementById('modalMenungguReview').classList.remove('hidden')">
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

<!-- Modal Menunggu Review -->
<div id="modalMenungguReview"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
     onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Menunggu Review</h3>
                    <p class="text-xs text-gray-500">Jurnal siswa yang perlu diverifikasi atau direvisi</p>
                </div>
            </div>
            <button onclick="document.getElementById('modalMenungguReview').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>
        <!-- Body -->
        <div class="overflow-y-auto flex-1 p-4 space-y-4">
            <?php if (empty($pendingProgress)): ?>
            <div class="text-center py-12">
                <div class="w-14 h-14 rounded-full bg-green-50 text-green-500 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <p class="font-semibold text-gray-800">Semua Sudah Ditinjau</p>
                <p class="text-gray-500 text-sm mt-1">Tidak ada jurnal yang menunggu verifikasi saat ini.</p>
            </div>
            <?php else: ?>
            <?php foreach ($pendingProgress as $p): ?>
            <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-sm transition-all">
                <!-- Card Header -->
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex items-center gap-3">
                        <?php if (!empty($p['profile_photo'])): ?>
                            <img class="w-10 h-10 rounded-xl object-cover border border-gray-200 flex-shrink-0"
                                 src="<?= base_url('profile-photo/' . esc($p['profile_photo'])); ?>"
                                 alt="<?= esc($p['nama_siswa']) ?>">
                        <?php else: ?>
                            <div class="w-10 h-10 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-indigo-600"><?= strtoupper(substr($p['nama_siswa'], 0, 2)); ?></span>
                            </div>
                        <?php endif; ?>
                        <div>
                            <p class="text-sm font-bold text-gray-900"><?= esc($p['nama_siswa']); ?></p>
                            <p class="text-xs text-gray-500"><?= esc($p['nama_kelas'] ?? '-'); ?> &middot; NIS: <?= esc($p['nis'] ?? '-'); ?></p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 bg-gray-50 border border-gray-200 px-2 py-1 rounded-lg flex-shrink-0">
                        <?= date('d M Y', strtotime($p['tanggal'])); ?>
                    </span>
                </div>

                <!-- Task Reference -->
                <div class="mb-3 px-3 py-2 bg-indigo-50/60 border-l-4 border-indigo-500 rounded-r-lg">
                    <span class="text-[10px] text-indigo-700 font-bold uppercase tracking-wider block">Task</span>
                    <span class="text-xs font-semibold text-gray-700 line-clamp-1"><?= esc($p['task_judul']); ?></span>
                </div>

                <!-- Description -->
                <p class="text-xs text-gray-700 bg-slate-50 border border-slate-100 rounded-lg p-2.5 mb-3 leading-relaxed line-clamp-3"><?= esc($p['deskripsi']); ?></p>

                <?php if (!empty($p['foto'])): ?>
                <div class="mb-3">
                    <a href="<?= base_url('files/pkl-progress/' . esc($p['foto'])); ?>" target="_blank"
                       class="inline-block group relative rounded-lg overflow-hidden border border-gray-200">
                        <img src="<?= base_url('files/pkl-progress/' . esc($p['foto'])); ?>"
                             class="max-h-36 rounded-lg group-hover:opacity-90 transition-opacity" loading="lazy">
                    </a>
                </div>
                <?php endif; ?>

                <?php if (!empty($p['catatan_pembimbing'])): ?>
                <div class="bg-blue-50 border-l-2 border-blue-400 px-3 py-2 rounded-r-lg mb-3 text-xs">
                    <span class="font-bold text-blue-700 uppercase text-[9px] block mb-0.5">Catatan Pembimbing</span>
                    <p class="text-blue-800"><?= esc($p['catatan_pembimbing']); ?></p>
                </div>
                <?php endif; ?>

                <!-- Action Forms -->
                <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row gap-2">
                    <!-- Setujui -->
                    <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']); ?>" method="POST" class="flex-1 flex gap-2">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="status" value="verified_by_instruktur">
                        <input type="text" name="catatan_instruktur" required placeholder="Catatan persetujuan..."
                               class="flex-1 min-w-0 border border-gray-300 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-green-500 focus:border-transparent">
                        <button type="submit"
                                class="flex-shrink-0 bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1 active:scale-95">
                            <i class="fas fa-check"></i> Setujui
                        </button>
                    </form>
                    <!-- Revisi -->
                    <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']); ?>" method="POST" class="flex-1 flex gap-2">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="status" value="revision">
                        <input type="text" name="catatan_instruktur" required placeholder="Alasan revisi..."
                               class="flex-1 min-w-0 border border-gray-300 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-orange-500 focus:border-transparent">
                        <button type="submit"
                                onclick="return confirm('Minta revisi progress ini?')"
                                class="flex-shrink-0 bg-orange-600 hover:bg-orange-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1 active:scale-95">
                            <i class="fas fa-edit"></i> Revisi
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Total Progress -->
<div id="modalTotalProgress"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
     onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-purple-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Total Progress</h3>
                    <p class="text-xs text-gray-500"><?= $statsProgress['total']; ?> entri progress</p>
                </div>
            </div>
            <button onclick="document.getElementById('modalTotalProgress').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>
        <!-- Body -->
        <div class="overflow-y-auto flex-1 p-4">
            <?php if (empty($allProgress)): ?>
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 text-sm">Belum ada progress</p>
            </div>
            <?php else: ?>
            <div class="space-y-2">
                <?php foreach ($allProgress as $p):
                    $dateObj = new DateTime($p['tanggal']);
                    $statusColor = match($p['status']) {
                        'approved'             => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
                        'submitted'            => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'dot' => 'bg-yellow-500'],
                        'verified_by_instruktur' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500'],
                        'revision'             => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'dot' => 'bg-orange-500'],
                        default                => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400'],
                    };
                    $statusLabel = match($p['status']) {
                        'approved'             => 'Disetujui',
                        'submitted'            => 'Menunggu',
                        'verified_by_instruktur' => 'Terverifikasi',
                        'revision'             => 'Revisi',
                        default                => 'Draft',
                    };
                ?>
                <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition border border-transparent hover:border-gray-200">
                    <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center text-white text-xs font-bold <?= $statusColor['dot'] ?>">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-gray-900 truncate"><?= esc($p['nama_task']); ?></p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium flex-shrink-0 <?= $statusColor['bg'] . ' ' . $statusColor['text'] ?>">
                                <?= $statusLabel ?>
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            <?= esc($p['nama_siswa']); ?>
                            <?php if (!empty($p['nama_kelas'])): ?>&middot; <?= esc($p['nama_kelas']); ?><?php endif; ?>
                            &middot; <?= $dateObj->format('d M Y'); ?>
                        </p>
                        <?php if (!empty($p['deskripsi'])): ?>
                        <p class="text-xs text-gray-600 mt-1 line-clamp-2"><?= esc($p['deskripsi']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Daftar Siswa PKL -->
<div id="modalDaftarSiswa"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
     onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[85vh] flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Daftar Siswa PKL</h3>
                    <p class="text-xs text-gray-500"><?= $totalSiswa; ?> siswa terdaftar</p>
                </div>
            </div>
            <button onclick="document.getElementById('modalDaftarSiswa').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>
        <!-- Body -->
        <div class="overflow-y-auto flex-1 p-4">
            <?php if (empty($siswaList)): ?>
            <div class="text-center py-12">
                <i class="fas fa-user-graduate text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 text-sm">Belum ada siswa yang ditempatkan</p>
            </div>
            <?php else: ?>
            <div class="space-y-2">
                <?php foreach ($siswaList as $s): ?>
                <a href="<?= base_url('instruktur/jurnal-pkl/siswa/' . $s['siswa_id']); ?>"
                   class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition group border border-transparent hover:border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-blue-600"><?= strtoupper(substr($s['nama_lengkap'], 0, 1)); ?></span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors"><?= esc($s['nama_lengkap']); ?></p>
                            <p class="text-xs text-gray-500">NIS: <?= esc($s['nis'] ?? '-'); ?> &middot; <?= esc($s['nama_kelas'] ?? '-'); ?></p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-blue-400 transition-colors"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
