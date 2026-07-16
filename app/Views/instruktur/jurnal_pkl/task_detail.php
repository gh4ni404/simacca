<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
    <?php
    $bulanIndo = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $hariIndo = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    ?>

    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center">
                <a href="<?= base_url('instruktur/jurnal-pkl/siswa/' . $task['siswa_id']); ?>" class="mr-4 text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800"><?= esc($task['judul']); ?></h1>
                    <p class="text-gray-600 mt-1">
                        <?= esc($task['nama_siswa']); ?> &middot; NIS: <?= esc($task['nis'] ?? '-'); ?>
                        <?php if (!empty($task['kategori_nama'])): ?>
                        &middot; <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700"><?= esc($task['kategori_nama']); ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <?php if ($task['status'] === 'active'): ?>
                <span class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg text-sm font-medium">
                    <i class="fas fa-spinner mr-2"></i>Sedang Dikerjakan
                </span>
                <?php elseif ($task['status'] === 'completed'): ?>
                <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium">
                    <i class="fas fa-check-circle mr-2"></i>Selesai
                </span>
                <?php endif; ?>
            </div>
        </div>
        <!-- Progress Bar -->
        <div class="mt-4">
            <?= render_task_progress_bar($task['status'], 'lg') ?>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <?php if (empty($progress)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3"></i>
        <h3 class="text-lg font-semibold text-gray-700">Belum Ada Progress</h3>
        <p class="text-gray-500 mt-1">Siswa belum mencatat progress untuk task ini</p>
    </div>
    <?php else: ?>
    <div class="relative">
        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200 hidden md:block"></div>

        <div class="space-y-4">
            <?php foreach ($progress as $p):
                $dateObj = new DateTime($p['tanggal']);
                $dayName = $hariIndo[$dateObj->format('l')];
                $dateStr = $dateObj->format('j') . ' ' . $bulanIndo[(int)$dateObj->format('m')] . ' ' . $dateObj->format('Y');

                $borderClass = match($p['status']) {
                    'approved' => 'border-l-green-500',
                    'verified_by_instruktur' => 'border-l-blue-500',
                    'submitted' => 'border-l-yellow-500',
                    'revision' => 'border-l-orange-500',
                    default => 'border-l-gray-300'
                };
            ?>
            <div class="relative">
                <div class="hidden md:flex absolute left-4 -top-1 w-4 h-4 rounded-full border-4 border-white shadow z-10
                    <?= match($p['status']) {
                        'approved' => 'bg-green-500',
                        'verified_by_instruktur' => 'bg-blue-500',
                        'submitted' => 'bg-yellow-500',
                        'revision' => 'bg-orange-500',
                        default => 'bg-gray-400'
                    }; ?>">
                </div>

                <div class="md:ml-12 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-800"><?= $dayName ?>, <?= $dateStr ?></span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?= match($p['status']) {
                                    'approved' => 'bg-green-100 text-green-700',
                                    'verified_by_instruktur' => 'bg-blue-100 text-blue-700',
                                    'submitted' => 'bg-yellow-100 text-yellow-700',
                                    'revision' => 'bg-orange-100 text-orange-700',
                                    default => 'bg-gray-100 text-gray-600'
                                } ?>">
                                <?= match($p['status']) {
                                    'approved' => '<i class="fas fa-check-circle mr-1"></i>Disetujui Pembimbing',
                                    'verified_by_instruktur' => '<i class="fas fa-check-double mr-1"></i>Diverifikasi',
                                    'submitted' => '<i class="fas fa-clock mr-1"></i>Menunggu',
                                    'revision' => '<i class="fas fa-edit mr-1"></i>Revisi',
                                    default => '<i class="fas fa-pen mr-1"></i>Draft'
                                } ?>
                            </span>
                        </div>

                        <p class="text-sm text-gray-700 leading-relaxed"><?= esc($p['deskripsi']); ?></p>

                        <?php if ($p['foto']): ?>
                        <div class="mt-3">
                            <a href="<?= base_url('files/pkl-progress/' . $p['foto']); ?>" target="_blank" class="block">
                                <img src="<?= base_url('files/pkl-progress/' . $p['foto']); ?>"
                                     class="w-full max-h-48 object-cover rounded-xl" loading="lazy">
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($p['catatan_pembimbing'])): ?>
                        <div class="mt-3 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg p-3">
                            <p class="text-xs font-semibold text-blue-700 uppercase">Catatan Pembimbing</p>
                            <p class="text-sm text-blue-800 mt-1"><?= esc($p['catatan_pembimbing']); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($p['catatan_instruktur'])): ?>
                        <div class="mt-3 bg-purple-50 border-l-4 border-purple-400 rounded-r-lg p-3">
                            <p class="text-xs font-semibold text-purple-700 uppercase">Catatan Instruktur</p>
                            <p class="text-sm text-purple-800 mt-1"><?= esc($p['catatan_instruktur']); ?></p>
                        </div>
                        <?php endif; ?>

                        <!-- Aksi Instruktur -->
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <?php if ($p['status'] === 'submitted'): ?>
                            <div class="flex flex-col gap-2">
                                <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']); ?>" method="POST" class="flex items-center gap-2">
                                    <?= csrf_field(); ?>
                                    <input type="text" name="catatan_instruktur"
                                           value="<?= esc($p['catatan_instruktur'] ?? ''); ?>"
                                           placeholder="Catatan (opsional)..."
                                           class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    <input type="hidden" name="status" value="verified_by_instruktur">
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm transition-colors">
                                        <i class="fas fa-check mr-1"></i>Setujui
                                    </button>
                                </form>
                                <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']); ?>" method="POST" class="flex items-center gap-2">
                                    <?= csrf_field(); ?>
                                    <input type="text" name="catatan_instruktur"
                                           value="<?= esc($p['catatan_instruktur'] ?? ''); ?>"
                                           placeholder="Catatan revisi..."
                                           class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    <input type="hidden" name="status" value="revision">
                                    <button type="submit" onclick="return confirm('Minta revisi progress ini?')"
                                            class="inline-flex items-center px-3 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm transition-colors">
                                        <i class="fas fa-edit mr-1"></i>Revisi
                                    </button>
                                </form>
                            </div>
                            <?php elseif (in_array($p['status'], ['verified_by_instruktur', 'revision'])): ?>
                            <div class="flex items-center justify-between">
                                <?php if ($p['catatan_instruktur']): ?>
                                <span class="text-xs text-gray-500"><i class="fas fa-comment mr-1"></i><?= esc($p['catatan_instruktur']) ?></span>
                                <?php endif; ?>
                                <form action="<?= base_url('instruktur/jurnal-pkl/batal-verifikasi-progress/' . $p['id']); ?>" method="POST" class="inline">
                                    <?= csrf_field(); ?>
                                    <button type="submit" onclick="return confirm('Batalkan verifikasi progress ini?')"
                                            class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-xs transition-colors">
                                        <i class="fas fa-undo mr-1"></i>Batalkan
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                            <!-- Draft: hanya form catatan -->
                            <form action="<?= base_url('instruktur/jurnal-pkl/catatan/' . $p['id']); ?>" method="POST">
                                <?= csrf_field(); ?>
                                <div class="flex gap-2">
                                    <input type="text" name="catatan_instruktur"
                                           value="<?= esc($p['catatan_instruktur'] ?? ''); ?>"
                                           placeholder="Tambah catatan..."
                                           class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm transition-colors">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
