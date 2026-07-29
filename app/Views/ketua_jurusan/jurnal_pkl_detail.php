<?php
$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
?>
<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="h-full px-4 md:px-1">
    <?= render_flash_message() ?>

    <!-- Breadcrumb -->
    <nav class="mb-4 text-sm text-gray-500">
        <a href="<?= base_url('ketua-jurusan/dashboard') ?>" class="hover:text-blue-600">Dashboard</a>
        <span class="mx-2">/</span>
        <a href="<?= base_url('ketua-jurusan/jurnal-pkl') ?>" class="hover:text-blue-600">Jurnal PKL</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium"><?= esc($siswa['nama_lengkap']) ?></span>
    </nav>

    <!-- Student Profile Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-4">
            <?php if (!empty($siswa['profile_photo'])): ?>
                <img src="<?= base_url('profile-photo/' . esc($siswa['profile_photo'])) ?>"
                     class="w-16 h-16 rounded-full object-cover border-2 border-gray-200 shadow"
                     alt="<?= esc($siswa['nama_lengkap']) ?>">
            <?php else: ?>
                <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 border-2 border-blue-100 flex items-center justify-center font-bold text-xl shadow">
                    <?= strtoupper(substr(esc($siswa['nama_lengkap']), 0, 2)) ?>
                </div>
            <?php endif; ?>
            <div class="flex-1">
                <h1 class="text-xl font-bold text-gray-800"><?= esc($siswa['nama_lengkap']) ?></h1>
                <p class="text-sm text-gray-500">NIS: <?= esc($siswa['nis']) ?> — <?= esc($siswa['nama_kelas']) ?></p>
                <?php if ($pkl_info): ?>
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-building mr-1"></i> <?= esc($pkl_info['nama_perusahaan']) ?>
                        <?php if (!empty($pkl_info['kota'])): ?>
                            — <?= esc($pkl_info['kota']) ?>
                        <?php endif; ?>
                        <?php if (!empty($pkl_info['nama_pembimbing'])): ?>
                            | <i class="fas fa-user-tie mr-1"></i> <?= esc($pkl_info['nama_pembimbing']) ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>
            <a href="<?= base_url('ketua-jurusan/jurnal-pkl') ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <?php if (empty($tasks)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-200">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-700">Belum Ada Task</h3>
        <p class="text-gray-500 mt-1">Siswa belum memiliki task PKL</p>
    </div>
    <?php else: ?>

    <!-- Tasks and Progress -->
    <div class="space-y-6">
        <?php foreach ($tasks as $task):
            $progressList = $progressByTask[$task['id']] ?? [];
        ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Task Header -->
            <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold text-gray-800"><?= esc($task['judul']) ?></h3>
                            <?php if (!empty($task['kategori_nama'])): ?>
                                <span class="px-2 py-0.5 text-xs font-medium rounded bg-purple-100 text-purple-800">
                                    <?= esc($task['kategori_nama']) ?>
                                </span>
                            <?php endif; ?>
                            <span class="px-2 py-0.5 text-xs font-medium rounded <?= $task['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' ?>">
                                <?= $task['status'] === 'active' ? 'Aktif' : ucfirst($task['status']) ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-4 mt-1 text-xs text-gray-500">
                            <span><i class="fas fa-book mr-1"></i> <?= (int)($task['total_progress'] ?? 0) ?> progress</span>
                            <span class="text-green-600"><i class="fas fa-check mr-1"></i> <?= (int)($task['approved_count'] ?? 0) ?> approved</span>
                            <span class="text-yellow-600"><i class="fas fa-clock mr-1"></i> <?= (int)($task['submitted_count'] ?? 0) ?> menunggu</span>
                        </div>
                    </div>
                    <!-- Progress Bar -->
                    <div class="text-right">
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <?php
                            $totalProg = max((int)($task['total_progress'] ?? 0), 1);
                            $approvedProg = (int)($task['approved_count'] ?? 0);
                            ?>
                            <div class="bg-green-500 h-2 rounded-full" style="width: <?= round(($approvedProg / $totalProg) * 100) ?>%"></div>
                        </div>
                        <span class="text-xs text-gray-500 mt-1 inline-block"><?= round(($approvedProg / $totalProg) * 100) ?>% selesai</span>
                    </div>
                </div>
            </div>

            <!-- Progress Timeline -->
            <div class="divide-y divide-gray-100">
                <?php if (empty($progressList)): ?>
                    <div class="px-5 py-6 text-center text-sm text-gray-400">
                        <i class="fas fa-inbox mr-1"></i> Belum ada progress
                    </div>
                <?php else: ?>
                    <?php foreach ($progressList as $prog): ?>
                    <div class="px-5 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-3">
                            <!-- Status Dot -->
                            <div class="mt-1 flex-shrink-0">
                                <?php
                                $dotColor = match($prog['status']) {
                                    'approved' => 'bg-green-500',
                                    'submitted' => 'bg-yellow-500',
                                    'revision' => 'bg-red-500',
                                    'verified' => 'bg-blue-500',
                                    default => 'bg-gray-400',
                                };
                                ?>
                                <div class="w-3 h-3 rounded-full <?= $dotColor ?>"></div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($prog['tanggal'])) ?></span>
                                    <?php
                                    $statusBadge = match($prog['status']) {
                                        'approved' => 'bg-green-100 text-green-800',
                                        'submitted' => 'bg-yellow-100 text-yellow-800',
                                        'revision' => 'bg-red-100 text-red-800',
                                        'verified' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                    $statusLabel = match($prog['status']) {
                                        'approved' => 'Disetujui',
                                        'submitted' => 'Menunggu',
                                        'revision' => 'Revisi',
                                        'verified' => 'Terverifikasi',
                                        default => ucfirst($prog['status']),
                                    };
                                    ?>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded <?= $statusBadge ?>"><?= $statusLabel ?></span>
                                </div>
                                <p class="text-sm text-gray-700"><?= nl2br(esc($prog['deskripsi'])) ?></p>

                                <?php if (!empty($prog['foto'])): ?>
                                    <div class="mt-2">
                                        <a href="<?= base_url('files/pkl-progress/' . esc($prog['foto'])) ?>" target="_blank">
                                            <img src="<?= base_url('files/pkl-progress/' . esc($prog['foto'])) ?>"
                                                 class="w-32 h-24 object-cover rounded-lg border border-gray-200 hover:shadow-md transition-shadow"
                                                 alt="Foto Progress">
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($prog['catatan_pembimbing'])): ?>
                                    <div class="mt-2 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2">
                                        <p class="text-xs text-blue-600 font-medium"><i class="fas fa-comment-dots mr-1"></i> Catatan Pembimbing:</p>
                                        <p class="text-xs text-blue-800"><?= esc($prog['catatan_pembimbing']) ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($prog['catatan_instruktur'])): ?>
                                    <div class="mt-2 bg-purple-50 border border-purple-200 rounded-lg px-3 py-2">
                                        <p class="text-xs text-purple-600 font-medium"><i class="fas fa-comment-dots mr-1"></i> Catatan Instruktur:</p>
                                        <p class="text-xs text-purple-800"><?= esc($prog['catatan_instruktur']) ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php
                                $pembimbingVerified = !empty($prog['verified_by']) && !empty($prog['catatan_pembimbing']);
                                $instrukturVerified = !empty($prog['instruktur_verified_by']) && !empty($prog['catatan_instruktur']);
                                $pembimbingHasRecord = !empty($prog['verified_by']);
                                $instrukturHasRecord = !empty($prog['instruktur_verified_by']);
                                $isApproved = $prog['status'] === 'approved';
                                $needsAction = $isApproved && (!$pembimbingVerified || !$instrukturVerified);
                                ?>

                                <?php if ($needsAction): ?>
                                    <div class="mt-3 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                                        <p class="text-xs text-amber-700 font-medium mb-2">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> 
                                            Jurnal ini disetujui namun belum memiliki catatan dari:
                                        </p>
                                        <div class="flex flex-wrap gap-2 mb-2">
                                            <?php if (!$pembimbingVerified): ?>
                                                <span class="px-2 py-0.5 text-xs font-medium rounded bg-orange-100 text-orange-700">
                                                    <i class="fas fa-user-tie mr-1"></i> Pembimbing
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!$instrukturVerified): ?>
                                                <span class="px-2 py-0.5 text-xs font-medium rounded bg-indigo-100 text-indigo-700">
                                                    <i class="fas fa-building mr-1"></i> Instruktur
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Form Aksi per Role -->
                                        <div class="bg-white border border-gray-200 rounded-lg p-3 mb-2 space-y-3">
                                            <!-- Aksi Pembimbing -->
                                            <?php if (!$pembimbingVerified): ?>
                                                <?php if (!$pembimbingHasRecord && empty($prog['catatan_pembimbing'])): ?>
                                                    <!-- Belum ada verifikasi & belum ada catatan → Verifikasi + Catatan -->
                                                    <form action="<?= base_url('ketua-jurusan/jurnal-pkl/tambah-catatan/' . $prog['id']) ?>" method="POST">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="role" value="pembimbing">
                                                        <input type="hidden" name="action" value="verify">
                                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                                            <span class="text-xs text-orange-700 font-medium flex items-center gap-1">
                                                                <i class="fas fa-user-tie"></i> Pembimbing:
                                                            </span>
                                                            <input type="text" name="catatan" required maxlength="200" placeholder="Catatan verifikasi..." class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                                            <button type="submit" onclick="return confirm('Verifikasi jurnal ini atas nama pembimbing?')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors whitespace-nowrap">
                                                                <i class="fas fa-check mr-1"></i> Verifikasi
                                                            </button>
                                                        </div>
                                                    </form>
                                                <?php elseif (!$pembimbingHasRecord && !empty($prog['catatan_pembimbing'])): ?>
                                                    <!-- Belum ada verifikasi tapi catatan sudah ada → Verifikasi saja -->
                                                    <form action="<?= base_url('ketua-jurusan/jurnal-pkl/tambah-catatan/' . $prog['id']) ?>" method="POST">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="role" value="pembimbing">
                                                        <input type="hidden" name="action" value="verify">
                                                        <input type="hidden" name="catatan" value="<?= esc($prog['catatan_pembimbing']) ?>">
                                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                                            <span class="text-xs text-orange-700 font-medium flex items-center gap-1">
                                                                <i class="fas fa-user-tie"></i> Pembimbing:
                                                            </span>
                                                            <span class="flex-1 text-xs text-gray-500 italic">Catatan sudah ada, cukup verifikasi</span>
                                                            <button type="submit" onclick="return confirm('Verifikasi jurnal ini atas nama pembimbing?')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors whitespace-nowrap">
                                                                <i class="fas fa-check mr-1"></i> Verifikasi
                                                            </button>
                                                        </div>
                                                    </form>
                                                <?php else: ?>
                                                    <!-- Sudah verifikasi tapi belum ada catatan → Tambah Catatan -->
                                                    <form action="<?= base_url('ketua-jurusan/jurnal-pkl/tambah-catatan/' . $prog['id']) ?>" method="POST">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="role" value="pembimbing">
                                                        <input type="hidden" name="action" value="add_catatan">
                                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                                            <span class="text-xs text-orange-700 font-medium flex items-center gap-1">
                                                                <i class="fas fa-user-tie"></i> Pembimbing:
                                                            </span>
                                                            <input type="text" name="catatan" required maxlength="200" placeholder="Tambah catatan..." class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                                            <button type="submit" onclick="return confirm('Tambah catatan atas nama pembimbing?')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors whitespace-nowrap">
                                                                <i class="fas fa-plus mr-1"></i> Tambah
                                                            </button>
                                                        </div>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <!-- Aksi Instruktur -->
                                            <?php if (!$instrukturVerified): ?>
                                                <?php if (!$instrukturHasRecord && empty($prog['catatan_instruktur'])): ?>
                                                    <!-- Belum ada verifikasi & belum ada catatan → Verifikasi + Catatan -->
                                                    <form action="<?= base_url('ketua-jurusan/jurnal-pkl/tambah-catatan/' . $prog['id']) ?>" method="POST">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="role" value="instruktur">
                                                        <input type="hidden" name="action" value="verify">
                                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                                            <span class="text-xs text-indigo-700 font-medium flex items-center gap-1">
                                                                <i class="fas fa-building"></i> Instruktur:
                                                            </span>
                                                            <input type="text" name="catatan" required maxlength="200" placeholder="Catatan verifikasi..." class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                                            <button type="submit" onclick="return confirm('Verifikasi jurnal ini atas nama instruktur?')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors whitespace-nowrap">
                                                                <i class="fas fa-check mr-1"></i> Verifikasi
                                                            </button>
                                                        </div>
                                                    </form>
                                                <?php elseif (!$instrukturHasRecord && !empty($prog['catatan_instruktur'])): ?>
                                                    <!-- Belum ada verifikasi tapi catatan sudah ada → Verifikasi saja -->
                                                    <form action="<?= base_url('ketua-jurusan/jurnal-pkl/tambah-catatan/' . $prog['id']) ?>" method="POST">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="role" value="instruktur">
                                                        <input type="hidden" name="action" value="verify">
                                                        <input type="hidden" name="catatan" value="<?= esc($prog['catatan_instruktur']) ?>">
                                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                                            <span class="text-xs text-indigo-700 font-medium flex items-center gap-1">
                                                                <i class="fas fa-building"></i> Instruktur:
                                                            </span>
                                                            <span class="flex-1 text-xs text-gray-500 italic">Catatan sudah ada, cukup verifikasi</span>
                                                            <button type="submit" onclick="return confirm('Verifikasi jurnal ini atas nama instruktur?')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors whitespace-nowrap">
                                                                <i class="fas fa-check mr-1"></i> Verifikasi
                                                            </button>
                                                        </div>
                                                    </form>
                                                <?php else: ?>
                                                    <!-- Sudah verifikasi tapi belum ada catatan → Tambah Catatan -->
                                                    <form action="<?= base_url('ketua-jurusan/jurnal-pkl/tambah-catatan/' . $prog['id']) ?>" method="POST">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="role" value="instruktur">
                                                        <input type="hidden" name="action" value="add_catatan">
                                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                                            <span class="text-xs text-indigo-700 font-medium flex items-center gap-1">
                                                                <i class="fas fa-building"></i> Instruktur:
                                                            </span>
                                                            <input type="text" name="catatan" required maxlength="200" placeholder="Tambah catatan..." class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                                            <button type="submit" onclick="return confirm('Tambah catatan atas nama instruktur?')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors whitespace-nowrap">
                                                                <i class="fas fa-plus mr-1"></i> Tambah
                                                            </button>
                                                        </div>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Form Batal Verifikasi -->
                                        <form action="<?= base_url('ketua-jurusan/jurnal-pkl/batal-verifikasi/' . $prog['id']) ?>" method="POST" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" onclick="return confirm('Batalkan verifikasi jurnal ini? Status akan dikembalikan ke menunggu verifikasi.')" class="inline-flex items-center gap-1 px-2 py-1 bg-white border border-orange-200 text-orange-700 text-xs font-medium rounded-lg hover:bg-orange-50 transition-colors">
                                                <i class="fas fa-undo text-[10px]"></i> Batal Verifikasi
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
