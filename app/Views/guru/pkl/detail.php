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
    $dateObj = new DateTime($progress['tanggal']);
    $dayName = $hariIndo[$dateObj->format('l')];
    $dateStr = $dateObj->format('d') . ' ' . $bulanIndo[(int)$dateObj->format('m')] . ' ' . $dateObj->format('Y');
    ?>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center">
            <a href="<?= base_url('guru/jurnal-pkl'); ?>" class="mr-4 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-clipboard-check mr-2 text-indigo-600"></i>Review Progress
                </h1>
                <p class="text-gray-600 mt-1">Verifikasi atau revisi progress siswa</p>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Detail Card -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-gray-800"><?= esc($progress['nama_task']) ?></h2>
                        <?php if (!empty($progress['kategori_nama'])): ?>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 mt-1 inline-block"><?= esc($progress['kategori_nama']) ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                        <?= match($progress['status']) {
                            'approved' => 'bg-green-100 text-green-700',
                            'verified_by_instruktur' => 'bg-blue-100 text-blue-700',
                            'submitted' => 'bg-yellow-100 text-yellow-700',
                            'revision' => 'bg-orange-100 text-orange-700',
                            default => 'bg-gray-100 text-gray-600'
                        } ?>">
                        <?= match($progress['status']) {
                            'approved' => '<i class="fas fa-check-circle mr-1"></i>Disetujui',
                            'verified_by_instruktur' => '<i class="fas fa-check-double mr-1"></i>Diverifikasi Instruktur',
                            'submitted' => '<i class="fas fa-clock mr-1"></i>Menunggu Catatan',
                            'revision' => '<i class="fas fa-edit mr-1"></i>Revisi',
                            default => '<i class="fas fa-pen mr-1"></i>Draft'
                        } ?>
                    </span>
                </div>
            </div>

            <div class="p-5">
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                    <i class="fas fa-calendar"></i>
                    <span><?= $dayName ?>, <?= $dateStr ?></span>
                </div>

                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-700 leading-relaxed"><?= esc($progress['deskripsi']) ?></p>
                </div>

                <?php if ($progress['foto']): ?>
                <div class="mt-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Foto</p>
                    <a href="<?= base_url('files/pkl-progress/' . $progress['foto']); ?>" target="_blank" class="block">
                        <img src="<?= base_url('files/pkl-progress/' . $progress['foto']); ?>"
                             class="max-h-64 rounded-xl object-cover border border-gray-200 hover:shadow-md transition-shadow">
                    </a>
                </div>
                <?php endif; ?>

                <?php if (!empty($progress['catatan_instruktur'])): ?>
                <div class="mt-4 bg-purple-50 border-l-4 border-purple-400 rounded-r-lg p-4">
                    <p class="text-xs font-semibold text-purple-700 uppercase">Catatan Instruktur</p>
                    <p class="text-sm text-purple-800 mt-1"><?= esc($progress['catatan_instruktur']) ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($progress['catatan_pembimbing']) && $progress['status'] === 'revision'): ?>
                <div class="mt-4 bg-orange-50 border-l-4 border-orange-400 rounded-r-lg p-4">
                    <p class="text-xs font-semibold text-orange-700 uppercase">Catatan Revisi Sebelumnya</p>
                    <p class="text-sm text-orange-800 mt-1"><?= esc($progress['catatan_pembimbing']) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Verify Card -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden h-fit">
            <div class="p-5 border-b border-gray-100 bg-indigo-50">
                <h3 class="font-bold text-indigo-800"><i class="fas fa-stamp mr-2"></i>Verifikasi</h3>
            </div>
            <div class="p-5">
                <?php if ($progress['status'] === 'approved'): ?>
                    <div class="text-center py-6">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check text-2xl text-green-600"></i>
                        </div>
                        <p class="text-sm font-semibold text-green-700">Sudah Disetujui</p>
                        <?php if (!empty($progress['verified_at'])): ?>
                        <p class="text-xs text-gray-500 mt-1"><?= date('d/m/Y H:i', strtotime($progress['verified_at'])) ?></p>
                        <?php endif; ?>

                        <form action="<?= base_url('guru/jurnal-pkl/batal-verifikasi/' . $progress['id']); ?>" method="POST" class="mt-4">
                            <?= csrf_field(); ?>
                            <button type="submit" onclick="return confirm('Batalkan verifikasi ini?')"
                                    class="inline-flex items-center px-4 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 text-sm">
                                <i class="fas fa-undo mr-2"></i>Batalkan Verifikasi
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                <form action="<?= base_url('guru/jurnal-pkl/verify/' . $progress['id']); ?>" method="POST">
                    <?= csrf_field(); ?>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Pembimbing <span class="text-red-500">*</span></label>
                        <textarea name="catatan" rows="4" required
                                  maxlength="200" oninput="updateCharCount(this)"
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                  placeholder="Tambahkan catatan untuk siswa..."><?= esc($progress['catatan_pembimbing'] ?? '') ?></textarea>
                        <div class="text-right text-xs text-gray-400 mt-1 char-count">0/200 karakter</div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" name="status" value="approved"
                                onclick="return confirm('Setujui progress ini?')"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium transition-colors">
                            <i class="fas fa-check mr-2"></i>Setujui
                        </button>
                        <button type="submit" name="status" value="revision"
                                onclick="return confirm('Revisi progress ini?')"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm font-medium transition-colors">
                            <i class="fas fa-edit mr-2"></i>Revisi
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function updateCharCount(el) {
    var max = 200;
    var len = el.value.length;
    var container = el.closest('form') || el.parentElement;
    var counter = container.querySelector('.char-count');
    if (!counter && container.parentElement) {
        counter = container.parentElement.querySelector('.char-count');
    }
    if (counter) {
        counter.textContent = len + '/' + max + ' karakter';
        if (len >= max) {
            counter.className = 'text-right text-xs font-bold text-red-500 mt-1 char-count';
        } else if (len >= 160) {
            counter.className = 'text-right text-xs text-orange-500 mt-1 char-count';
        } else {
            counter.className = 'text-right text-xs text-gray-400 mt-1 char-count';
        }
    }
}
document.querySelectorAll('textarea[name="catatan"], input[name="catatan"]').forEach(function(el) {
    updateCharCount(el);
    el.addEventListener('input', function() { updateCharCount(this); });
});
</script>
<?= $this->endSection() ?>
