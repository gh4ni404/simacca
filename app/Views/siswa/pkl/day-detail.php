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
    $dateObj = new DateTime($tanggal);
    ?>

    <div class="mb-6">
        <div class="flex items-center">
            <a href="<?= base_url('siswa/jurnal-pkl'); ?>" class="mr-4 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-calendar-day mr-2 text-blue-600"></i>
                    Detail Hari
                </h1>
                <p class="text-gray-600 mt-1"><?= $hariIndo[$dateObj->format('l')] ?>, <?= $dateObj->format('d') . ' ' . $bulanIndo[(int)$dateObj->format('m')] . ' ' . $dateObj->format('Y') ?></p>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <?php if (empty($progress)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-700">Tidak Ada Aktivitas</h3>
        <p class="text-gray-500 mt-1">Belum ada aktivitas PKL di tanggal ini</p>
        <a href="<?= base_url('siswa/jurnal-pkl/tambah'); ?>"
           class="inline-flex items-center mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>Tambah Aktivitas
        </a>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
            <p class="text-sm font-medium text-gray-700"><?= count($progress) ?> aktivitas</p>
        </div>
        <div class="divide-y divide-gray-100">
            <?php foreach ($progress as $p): ?>
            <div class="p-5 hover:bg-gray-50 transition-colors">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-1">
                        <?php if ($p['status'] === 'approved'): ?>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-check text-sm"></i>
                        </span>
                        <?php elseif ($p['status'] === 'submitted'): ?>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-clock text-sm"></i>
                        </span>
                        <?php elseif ($p['status'] === 'revision'): ?>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600">
                            <i class="fas fa-edit text-sm"></i>
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-500">
                            <i class="fas fa-pen text-sm"></i>
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700"><?= esc($p['nama_task']) ?></span>
                            <?php if (!empty($p['kategori_nama'])): ?>
                            <span class="text-xs text-gray-400">/</span>
                            <span class="text-xs text-gray-500"><?= esc($p['kategori_nama']) ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="text-sm text-gray-700 leading-relaxed"><?= esc($p['deskripsi']) ?></p>

                        <?php if ($p['foto']): ?>
                        <div class="mt-3">
                            <button onclick="openLightbox('<?= base_url('files/pkl-progress/' . $p['foto']); ?>')"
                                    class="relative block rounded-xl overflow-hidden group">
                                <img src="<?= base_url('files/pkl-progress/' . $p['foto']); ?>"
                                     class="w-full max-h-48 object-cover rounded-xl" loading="lazy">
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

                        <?php if ($p['status'] !== 'approved'): ?>
                        <div class="mt-3 flex gap-2">
                            <a href="<?= base_url('siswa/jurnal-pkl/edit-progress/' . $p['id']); ?>"
                               class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-xs font-medium">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
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
