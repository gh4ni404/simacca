<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
    <div class="flex items-start gap-4 mb-6">
        <a href="<?= base_url('instruktur/dashboard') ?>"
           class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 shadow-sm transition-all active:scale-95 flex-shrink-0 mt-0.5">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Menunggu Catatan</h1>
            <p class="text-sm text-gray-500">
                <span class="inline-flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse inline-block"></span>
                    <?= count($pendingProgress) ?> jurnal perlu ditindaklanjuti
                </span>
            </p>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <?php if (empty($pendingProgress)): ?>
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
    <div class="space-y-4 max-w-3xl mx-auto">
        <?php foreach ($pendingProgress as $p): ?>
        <div class="group bg-white border border-gray-200 rounded-2xl overflow-hidden hover:border-yellow-300 hover:shadow-md transition-all duration-200">
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

            <div class="px-4 py-3 space-y-5">
                <div>
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-widest block mb-1.5"><i class="fas fa-tasks mr-1.5 text-gray-500"></i>Nama Pekerjaan</span>
                    <div class="flex items-start gap-2.5 bg-indigo-50/80 border border-indigo-100 rounded-xl px-3 py-2.5">
                        <div class="min-w-0">
                            <span class="text-sm font-semibold text-indigo-800 leading-snug"><?= esc($p['task_judul']); ?></span>
                        </div>
                    </div>
                </div>

                <?php
                $langkahKerja = [];
                if (!empty($p['langkah_kerja'])) {
                    $decoded = json_decode($p['langkah_kerja'], true);
                    if (is_array($decoded)) {
                        $langkahKerja = array_filter($decoded, fn($v) => trim($v) !== '');
                    }
                }
                ?>

                <?php if (!empty($langkahKerja)): ?>
                <div>
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-widest block mb-1.5"><i class="fas fa-clipboard-list mr-1.5 text-gray-500"></i>Perencanaan dan Persiapan Kerja <span class="text-gray-500 font-medium normal-case text-[10px]">(<?= count($langkahKerja) ?>)</span></span>
                    <div class="bg-gray-50/50 border border-gray-200 rounded-xl p-3.5">
                        <ol class="relative border-l border-gray-200 ml-2 space-y-3">
                            <?php foreach (array_values($langkahKerja) as $step): ?>
                            <li class="ml-5">
                                <div class="absolute -left-[5px] mt-1 w-2.5 h-2.5 rounded-full bg-indigo-500"></div>
                                <p class="text-sm text-gray-700 leading-relaxed"><?= esc($step) ?></p>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
                <?php endif; ?>

                <div>
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-widest block mb-1.5"><i class="fas fa-file-alt mr-1.5 text-gray-500"></i>Deskripsi Kerja</span>
                    <p class="text-sm text-gray-700 bg-gray-50/50 border border-gray-200 rounded-xl px-3 py-2.5 leading-relaxed"><?= esc($p['deskripsi']); ?></p>
                </div>

                <?php if (!empty($p['foto'])): ?>
                <div>
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-widest block mb-1.5"><i class="fas fa-camera mr-1.5 text-gray-500"></i>Foto Dokumentasi</span>
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

                <?php if (!empty($p['catatan_pembimbing'])): ?>
                <div class="flex gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-3 py-2.5">
                    <i class="fas fa-comment-dots text-blue-400 text-xs mt-0.5 flex-shrink-0"></i>
                    <div>
                        <span class="text-xs font-bold text-blue-500 uppercase tracking-widest block leading-none mb-1">Catatan Pembimbing</span>
                        <p class="text-sm text-blue-800 leading-relaxed"><?= esc($p['catatan_pembimbing']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="px-4 pb-4 pt-1">
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 space-y-2.5">
                    <p class="text-xs font-bold text-gray-600 uppercase tracking-widest">Tindakan</p>

                    <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']); ?>" method="POST">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="status" value="verified_by_instruktur">
                        <div class="flex gap-2">
                             <input type="text" name="catatan_instruktur" required
                                    placeholder="Tulis catatan persetujuan..."
                                    maxlength="200" oninput="updateCharCount(this)"
                                    class="flex-1 min-w-0 border border-gray-200 bg-white rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent placeholder:text-gray-400 transition-all">
                             <button type="submit"
                                     class="flex-shrink-0 inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 active:scale-95 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-sm shadow-green-200">
                                 <i class="fas fa-check text-xs"></i>
                                 <span class="hidden sm:inline">Setujui</span>
                                 <span class="sm:hidden">OK</span>
                             </button>
                         </div>
                         <div class="text-right text-[10px] text-gray-400 char-count mt-1">0/200 karakter</div>
                    </form>

                    <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']); ?>" method="POST">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="status" value="revision">
                        <div class="flex gap-2">
                             <input type="text" name="catatan_instruktur" required
                                    placeholder="Tulis alasan revisi..."
                                    maxlength="200" oninput="updateCharCount(this)"
                                    class="flex-1 min-w-0 border border-gray-200 bg-white rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent placeholder:text-gray-400 transition-all">
                             <button type="submit"
                                     onclick="return confirm('Minta siswa merevisi progress ini?')"
                                     class="flex-shrink-0 inline-flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 active:scale-95 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-sm shadow-orange-200">
                                 <i class="fas fa-undo text-xs"></i>
                                 <span>Revisi</span>
                             </button>
                         </div>
                         <div class="text-right text-[10px] text-gray-400 char-count mt-1">0/200 karakter</div>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

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
    var lb = document.getElementById('lightbox');
    document.getElementById('lightboxImg').src = src;
    lb.classList.remove('hidden');
    lb.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeLightbox(e) {
    if (e && e.target !== e.currentTarget) return;
    var lb = document.getElementById('lightbox');
    lb.classList.add('hidden');
    lb.classList.remove('flex');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeLightbox(); });

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
            counter.className = 'text-right text-[10px] font-bold text-red-500 char-count mt-1';
        } else if (len >= 160) {
            counter.className = 'text-right text-[10px] text-orange-500 char-count mt-1';
        } else {
            counter.className = 'text-right text-[10px] text-gray-400 char-count mt-1';
        }
    }
}
document.querySelectorAll('input[name="catatan_instruktur"], textarea[name="catatan_instruktur"]').forEach(function(el) {
    updateCharCount(el);
    el.addEventListener('input', function() { updateCharCount(this); });
});
</script>
<?= $this->endSection() ?>
