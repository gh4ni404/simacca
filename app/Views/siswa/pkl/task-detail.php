<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
<?php helper('setting');
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

    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl shadow-lg p-6 mb-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
                <a href="<?= base_url('siswa/jurnal-pkl'); ?>"
                   class="flex items-center justify-center w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold"><?= esc($task['judul']) ?></h1>
                    <?php if (!empty($task['kategori_nama'])): ?>
                    <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full bg-white/20"><?= esc($task['kategori_nama']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex items-center gap-3">
                <?php if ($task['status'] === 'active'): ?>
                <form action="<?= base_url('siswa/jurnal-pkl/selesaikan-task/' . $task['id']); ?>" method="POST" class="inline">
                    <?= csrf_field(); ?>
                    <button type="submit" onclick="return confirm('Yakin ingin menyelesaikan task ini? Status task akan berubah menjadi selesai.')"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                        <i class="fas fa-check-circle mr-2"></i>Selesaikan Task
                    </button>
                </form>
                <?php elseif ($task['status'] === 'completed'): ?>
                <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium">
                    <i class="fas fa-check-circle mr-2"></i>Task Selesai
                </span>
                <?php endif; ?>
                <?php if (!empty($progress)): ?>
                <a href="<?= base_url('siswa/jurnal-pkl/cetak-catatan/' . $task['id']); ?>"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-white text-blue-700 rounded-lg hover:bg-blue-50 transition-colors text-sm font-medium">
                    <i class="fas fa-print mr-2"></i>Cetak Catatan
                </a>
                <?php endif; ?>
            </div>
        </div>
        <!-- Progress Bar -->
        <div class="mt-4 bg-white/10 rounded-xl p-4">
            <?= render_task_progress_bar($task['status'], 'lg') ?>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <?php if (empty($progress)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-700">Belum Ada Progress</h3>
        <p class="text-gray-500 mt-1">Mulai catat progress untuk task ini</p>
        <a href="<?= base_url('siswa/jurnal-pkl/tambah'); ?>"
           class="inline-flex items-center mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>Tambah Aktivitas
        </a>
    </div>
    <?php else: ?>
    <div class="relative">
        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200 hidden md:block"></div>

        <div class="space-y-4">
            <?php foreach ($progress as $i => $p):
                $dateObj = new DateTime($p['tanggal']);
                $dayName = $hariIndo[$dateObj->format('l')];
                $dateStr = $dateObj->format('j') . ' ' . $bulanIndo[(int)$dateObj->format('m')] . ' ' . $dateObj->format('Y');

                $ds = get_pkl_progress_display_status($p);
                $st = get_pkl_status_style($ds);
                $borderClass = match($ds) {
                    'completed' => 'border-l-green-500',
                    'pending_instruktur' => 'border-l-orange-500',
                    'pending_pembimbing' => 'border-l-blue-500',
                    'submitted' => 'border-l-yellow-500',
                    'revision' => 'border-l-orange-500',
                    default => 'border-l-gray-300'
                };
            ?>
            <div class="relative">
                <div class="hidden md:flex absolute left-4 -top-1 w-4 h-4 rounded-full border-4 border-white shadow z-10
                    <?= match($ds) {
                        'completed' => 'bg-green-500',
                        'pending_instruktur' => 'bg-orange-500',
                        'pending_pembimbing' => 'bg-blue-500',
                        'submitted' => 'bg-yellow-500',
                        'revision' => 'bg-orange-500',
                        default => 'bg-gray-400'
                    }; ?>">
                </div>

                <div class="md:ml-12 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-800"><?= $dayName ?>, <?= $dateStr ?></span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $st['bg'] ?>" title="<?= $st['label'] ?>">
                                <?php if ($st['badge_icon']): ?><i class="fas <?= $st['badge_icon'] ?>"></i><?php else: ?><i class="fas <?= $st['icon'] ?> mr-1"></i><?= $st['label'] ?><?php endif; ?>
                            </span>
                        </div>

                        <p class="text-sm text-gray-700 leading-relaxed"><?= esc($p['deskripsi']) ?></p>

                        <?php if ($p['foto']): ?>
                        <div class="mt-3">
                            <button onclick="openLightbox('<?= base_url('files/pkl-progress/' . $p['foto']); ?>')"
                                    class="relative block rounded-xl overflow-hidden group">
                                <img src="<?= base_url('files/pkl-progress/' . $p['foto']); ?>"
                                     class="w-full max-h-48 object-cover rounded-xl" loading="lazy">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors rounded-xl flex items-center justify-center">
                                    <span class="opacity-0 group-hover:opacity-100 text-white text-sm bg-black/60 px-3 py-1 rounded-lg transition-opacity">
                                        <i class="fas fa-expand mr-1"></i>Perbesar
                                    </span>
                                </div>
                            </button>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($p['catatan_pembimbing'])): ?>
                        <div class="mt-3 bg-orange-50 border-l-4 border-orange-400 rounded-r-lg p-3">
                            <p class="text-xs font-semibold text-orange-700 uppercase">Catatan Pembimbing</p>
                            <p class="text-sm text-orange-800 mt-1"><?= esc($p['catatan_pembimbing']) ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($p['catatan_instruktur'])): ?>
                        <div class="mt-3 bg-purple-50 border-l-4 border-purple-400 rounded-r-lg p-3">
                            <p class="text-xs font-semibold text-purple-700 uppercase">Catatan Instruktur</p>
                            <p class="text-sm text-purple-800 mt-1"><?= esc($p['catatan_instruktur']) ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if (get_pkl_progress_display_status($p) !== 'completed'): ?>
                        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-2">
                            <form action="<?= base_url('siswa/jurnal-pkl/hapus-progress/' . $p['id']); ?>" method="POST" class="inline">
                                <?= csrf_field(); ?>
                                <button type="submit" onclick="return confirm('Yakin hapus?')"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-xs font-medium">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<div id="lightbox" class="fixed inset-0 z-[9999] bg-black/90 hidden items-center justify-center p-4" onclick="closeLightbox(event)">
    <button onclick="closeLightbox()" class="absolute top-4 right-4 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white text-lg">
        <i class="fas fa-times"></i>
    </button>
    <img id="lightboxImg" class="max-w-full max-h-[90vh] rounded-2xl shadow-2xl" src="">
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
    document.getElementById('lightbox').classList.add('hidden');
    document.getElementById('lightbox').classList.remove('flex');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeLightbox(); });
</script>
<?= $this->endSection() ?>
