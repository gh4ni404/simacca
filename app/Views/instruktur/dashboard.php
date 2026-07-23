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
             onclick="window.location.href='<?= base_url('instruktur/jurnal-pkl'); ?>'">
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
             onclick="window.location.href='<?= base_url('instruktur/jurnal-pkl/semua-progress'); ?>'">
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
             onclick="window.location.href='<?= base_url('instruktur/jurnal-pkl/pending'); ?>'">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?= $statsProgress['submitted']; ?></p>
                    <p class="text-xs text-gray-500">Menunggu Catatan Instruktur</p>
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
     class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm"
     onclick="if(event.target===this)closeMenungguReview()">
    <div id="modalMenungguReviewPanel"
         class="bg-white w-full sm:max-w-2xl sm:mx-4 sm:rounded-2xl rounded-t-3xl shadow-2xl flex flex-col overflow-hidden
                translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0
                transition-all duration-300 ease-out
                max-h-[92vh] sm:max-h-[88vh]">

        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
            <!-- Drag handle (mobile only) -->
            <div class="absolute top-2.5 left-1/2 -translate-x-1/2 w-10 h-1 bg-gray-200 rounded-full sm:hidden"></div>

            <div class="flex items-center gap-3 mt-1 sm:mt-0">
                <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 leading-tight">Menunggu Review</h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse inline-block"></span>
                            <?= count($pendingProgress) ?> jurnal perlu ditindaklanjuti
                        </span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="<?= base_url('instruktur/jurnal-pkl'); ?>"
                   class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-semibold transition-colors">
                    <i class="fas fa-external-link-alt text-[10px]"></i> Buka Halaman Penuh
                </a>
                <button onclick="closeMenungguReview()"
                        class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="overflow-y-auto flex-1 px-4 py-4 space-y-3">

            <?php if (empty($pendingProgress)): ?>
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-green-50 flex items-center justify-center mb-4">
                    <i class="fas fa-check-circle text-3xl text-green-500"></i>
                </div>
                <p class="text-base font-bold text-gray-800">Semua Sudah Ditinjau</p>
                <p class="text-sm text-gray-400 mt-1 max-w-xs">Tidak ada jurnal yang sedang menunggu verifikasi saat ini.</p>
                <a href="<?= base_url('instruktur/jurnal-pkl'); ?>"
                   class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-book-open text-xs"></i> Lihat Semua Jurnal
                </a>
            </div>

            <?php else: ?>
            <?php foreach ($pendingProgress as $idx => $p): ?>
            <div class="group bg-white border border-gray-200 rounded-2xl overflow-hidden hover:border-yellow-300 hover:shadow-md transition-all duration-200">

                <!-- Card Top: Siswa info + Tanggal -->
                <div class="flex items-center justify-between px-4 pt-4 pb-3 border-b border-gray-50">
                    <div class="flex items-center gap-3 min-w-0">
                        <?php if (!empty($p['profile_photo'])): ?>
                            <img class="w-9 h-9 rounded-xl object-cover border-2 border-white shadow-sm flex-shrink-0"
                                 src="<?= base_url('profile-photo/' . esc($p['profile_photo'])); ?>"
                                 alt="<?= esc($p['nama_siswa']) ?>">
                        <?php else: ?>
                            <div class="w-9 h-9 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                <span class="text-xs font-bold text-white"><?= strtoupper(substr($p['nama_siswa'], 0, 2)); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate"><?= esc($p['nama_siswa']); ?></p>
                            <p class="text-xs text-gray-400 truncate">
                                <?= esc($p['nama_kelas'] ?? '-'); ?>
                                <?php if (!empty($p['nis'])): ?>&nbsp;&middot;&nbsp;<?= esc($p['nis']); ?><?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                        <span class="inline-flex items-center gap-1 text-[11px] text-gray-500 bg-gray-50 border border-gray-200 px-2.5 py-1 rounded-lg">
                            <i class="far fa-calendar text-gray-400 text-[10px]"></i>
                            <?= date('d M Y', strtotime($p['tanggal'])); ?>
                        </span>
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-yellow-700 bg-yellow-50 border border-yellow-200 px-2 py-1 rounded-lg">
                            <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full animate-pulse"></span>
                            Menunggu
                        </span>
                    </div>
                </div>

                <!-- Card Body: Task + Deskripsi + Foto -->
                <div class="px-4 py-3 space-y-3">

                    <!-- Task badge -->
                    <div class="flex items-start gap-2.5 bg-indigo-50/80 border border-indigo-100 rounded-xl px-3 py-2.5">
                        <i class="fas fa-tasks text-indigo-400 text-xs mt-0.5 flex-shrink-0"></i>
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest block leading-none mb-1">Task Pekerjaan</span>
                            <span class="text-xs font-semibold text-indigo-800 leading-snug"><?= esc($p['task_judul']); ?></span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1.5">Hasil Kerja</span>
                        <p class="text-sm text-gray-700 bg-gray-50/80 border border-gray-100 rounded-xl px-3 py-2.5 leading-relaxed"><?= esc($p['deskripsi']); ?></p>
                    </div>

                    <!-- Photo (if any) -->
                    <?php if (!empty($p['foto'])): ?>
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1.5">Foto Dokumentasi</span>
                        <button type="button"
                                onclick="openLightbox('<?= base_url('files/pkl-progress/' . esc($p['foto'])); ?>')"
                                class="group/photo relative block w-full rounded-xl overflow-hidden border border-gray-200 bg-gray-50 hover:border-indigo-300 transition-colors cursor-zoom-in">
                            <img src="<?= base_url('files/pkl-progress/' . esc($p['foto'])); ?>"
                                 class="w-full max-h-44 object-cover" loading="lazy">
                            <div class="absolute inset-0 bg-black/0 group-hover/photo:bg-black/20 transition-colors flex items-center justify-center">
                                <span class="opacity-0 group-hover/photo:opacity-100 transition-opacity bg-black/60 text-white text-xs font-semibold px-3 py-1.5 rounded-lg flex items-center gap-1.5">
                                    <i class="fas fa-expand-alt text-[10px]"></i> Lihat Foto
                                </span>
                            </div>
                        </button>
                    </div>
                    <?php endif; ?>

                    <!-- Catatan Pembimbing (if any) -->
                    <?php if (!empty($p['catatan_pembimbing'])): ?>
                    <div class="flex gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-3 py-2.5">
                        <i class="fas fa-comment-dots text-blue-400 text-xs mt-0.5 flex-shrink-0"></i>
                        <div>
                            <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest block leading-none mb-1">Catatan Pembimbing</span>
                            <p class="text-xs text-blue-800 leading-relaxed"><?= esc($p['catatan_pembimbing']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>

                <!-- Card Footer: Action Forms -->
                <div class="px-4 pb-4 pt-1">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 space-y-2.5">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tindakan</p>

                        <!-- Setujui -->
                        <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']); ?>" method="POST">
                            <?= csrf_field(); ?>
                            <input type="hidden" name="status" value="verified_by_instruktur">
                            <div class="flex gap-2">
                                <input type="text" name="catatan_instruktur" required
                                       placeholder="Tulis catatan persetujuan..."
                                       class="flex-1 min-w-0 border border-gray-200 bg-white rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent placeholder:text-gray-400 transition-all">
                                <button type="submit"
                                        class="flex-shrink-0 inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 active:scale-95 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-sm shadow-green-200">
                                    <i class="fas fa-check text-xs"></i>
                                    <span class="hidden sm:inline">Setujui</span>
                                    <span class="sm:hidden">OK</span>
                                </button>
                            </div>
                        </form>

                        <!-- Revisi -->
                        <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']); ?>" method="POST">
                            <?= csrf_field(); ?>
                            <input type="hidden" name="status" value="revision">
                            <div class="flex gap-2">
                                <input type="text" name="catatan_instruktur" required
                                       placeholder="Tulis alasan revisi..."
                                       class="flex-1 min-w-0 border border-gray-200 bg-white rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent placeholder:text-gray-400 transition-all">
                                <button type="submit"
                                        onclick="return confirm('Minta siswa merevisi progress ini?')"
                                        class="flex-shrink-0 inline-flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 active:scale-95 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-sm shadow-orange-200">
                                    <i class="fas fa-undo text-xs"></i>
                                    <span>Revisi</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>

            <!-- Footer link -->
            <div class="pt-1 pb-2 text-center">
                <a href="<?= base_url('instruktur/jurnal-pkl'); ?>"
                   class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 font-semibold transition-colors">
                    Lihat semua jurnal <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function openMenungguReview() {
    const modal = document.getElementById('modalMenungguReview');
    const panel = document.getElementById('modalMenungguReviewPanel');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        panel.classList.remove('translate-y-full', 'sm:scale-95', 'sm:opacity-0');
        panel.classList.add('translate-y-0', 'sm:scale-100', 'sm:opacity-100');
    });
}
function closeMenungguReview() {
    const modal = document.getElementById('modalMenungguReview');
    const panel = document.getElementById('modalMenungguReviewPanel');
    panel.classList.add('translate-y-full', 'sm:scale-95', 'sm:opacity-0');
    panel.classList.remove('translate-y-0', 'sm:scale-100', 'sm:opacity-100');
    setTimeout(() => modal.classList.add('hidden'), 280);
}
</script>

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

<!-- Lightbox -->
<div id="lightbox" class="fixed inset-0 z-[9999] bg-black/90 hidden items-center justify-center p-4"
     onclick="closeLightbox(event)">
    <button onclick="closeLightbox()"
            class="absolute top-4 right-4 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white text-lg transition-colors">
        <i class="fas fa-times"></i>
    </button>
    <img id="lightboxImg" class="max-w-full max-h-[90vh] rounded-2xl shadow-2xl object-contain" src="">
</div>

<script>
function openLightbox(src) {
    const lb = document.getElementById('lightbox');
    document.getElementById('lightboxImg').src = src;
    lb.classList.remove('hidden');
    lb.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeLightbox(e) {
    if (e && e.target !== e.currentTarget) return;
    const lb = document.getElementById('lightbox');
    lb.classList.add('hidden');
    lb.classList.remove('flex');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeLightbox(); });
</script>

<?= $this->endSection() ?>
