<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
    <?php
    $hariIndo = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    $bulanIndo = [
        'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
        'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
        'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
        'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
    ];
    ?>

    <?php if (!empty($entries)): $start = new DateTime(); $start->setISODate($tahun, $minggu); $weekEnd = clone $start; $weekEnd->modify('+6 days'); ?>
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl shadow-lg p-6 mb-8 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
                <a href="<?= base_url('siswa/jurnal-pkl'); ?>"
                   class="flex items-center justify-center w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold">Jurnal PKL</h1>
                    <p class="text-blue-100 text-sm mt-1">
                        <i class="fas fa-calendar-week mr-1"></i>
                        Minggu ke-<?= $minggu; ?> &mdash;
                        <?= $start->format('j') . ' ' . $bulanIndo[$start->format('F')]; ?> &ndash;
                        <?= $weekEnd->format('j') . ' ' . $bulanIndo[$weekEnd->format('F')] . ' ' . $weekEnd->format('Y'); ?>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-4 md:mt-0">
                <div class="text-center px-4 py-2 bg-white/15 rounded-xl">
                    <p class="text-xs text-blue-200">Total Entry</p>
                    <p class="text-2xl font-bold"><?= count($entries); ?></p>
                </div>
                <?php if ($allDisetujui): ?>
                <a href="<?= base_url('siswa/jurnal-pkl/cetak/' . $tahun . '/' . $minggu); ?>"
                   target="_blank"
                   class="inline-flex items-center px-5 py-3 bg-white text-blue-700 rounded-xl hover:bg-blue-50 transition-colors font-semibold shadow-lg">
                    <i class="fas fa-print mr-2"></i>
                    Cetak Jurnal
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <?php
            $stats = ['disetujui' => 0, 'pending' => 0, 'revisi' => 0, 'ditolak' => 0];
            foreach ($entries as $e) { $stats[$e['status']] = ($stats[$e['status']] ?? 0) + 1; }
            $progress = count($entries) > 0 ? round(($stats['disetujui'] / count($entries)) * 100) : 0;
            ?>
            <div class="flex-1 min-w-[200px] bg-white/15 rounded-lg p-3">
                <div class="flex justify-between text-sm mb-1">
                    <span>Progress Verifikasi</span>
                    <span class="font-semibold"><?= $stats['disetujui']; ?>/<?= count($entries); ?> (<?= $progress; ?>%)</span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-2.5">
                    <div class="bg-green-400 h-2.5 rounded-full transition-all duration-500" style="width: <?= $progress; ?>%"></div>
                </div>
            </div>
            <div class="flex gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-500/20 text-green-200 text-sm">
                    <i class="fas fa-check-circle text-xs"></i> <?= $stats['disetujui']; ?> Disetujui
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-yellow-500/20 text-yellow-200 text-sm">
                    <i class="fas fa-clock text-xs"></i> <?= $stats['pending']; ?> Pending
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-500/20 text-orange-200 text-sm">
                    <i class="fas fa-edit text-xs"></i> <?= $stats['revisi']; ?> Revisi
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-500/20 text-red-200 text-sm">
                    <i class="fas fa-times-circle text-xs"></i> <?= $stats['ditolak']; ?> Ditolak
                </span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?= view('components/alerts') ?>

    <?php if (empty($entries)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-book-open text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-700 mb-1">Belum Ada Jurnal</h3>
        <p class="text-gray-500 mb-4">Tidak ada jurnal PKL di minggu ini</p>
        <a href="<?= base_url('siswa/jurnal-pkl/tambah'); ?>"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Tambah Jurnal
        </a>
    </div>
    <?php else: ?>
    <div class="relative">
        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200 hidden md:block"></div>

        <div class="space-y-6">
            <?php foreach ($entries as $i => $entry):
                $hariInggris = date('l', strtotime($entry['tanggal']));
                $namaHari = $hariIndo[$hariInggris];
                $tgl = date('j', strtotime($entry['tanggal']));
                $bln = $bulanIndo[date('F', strtotime($entry['tanggal']))];
                $thn = date('Y', strtotime($entry['tanggal']));

                $borderClass = match($entry['status']) {
                    'disetujui' => 'border-l-green-500',
                    'pending' => 'border-l-yellow-500',
                    'revisi' => 'border-l-orange-500',
                    'ditolak' => 'border-l-red-500',
                    default => 'border-l-gray-300'
                };
            ?>
            <div class="relative">
                <div class="hidden md:flex absolute left-4 -top-1 w-4 h-4 rounded-full border-4 border-white shadow z-10
                    <?= match($entry['status']) {
                        'disetujui' => 'bg-green-500',
                        'pending' => 'bg-yellow-500',
                        'revisi' => 'bg-orange-500',
                        'ditolak' => 'bg-red-500',
                        default => 'bg-gray-400'
                    }; ?>">
                </div>

                <div class="md:ml-12 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="md:hidden flex items-center gap-2 px-5 pt-4 pb-2">
                        <span class="w-2.5 h-2.5 rounded-full
                            <?= match($entry['status']) {
                                'disetujui' => 'bg-green-500',
                                'pending' => 'bg-yellow-500',
                                'revisi' => 'bg-orange-500',
                                'ditolak' => 'bg-red-500',
                                default => 'bg-gray-400'
                            }; ?>">
                        </span>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Hari ke-<?= $i + 1; ?></span>
                    </div>

                    <div class="md:flex">
                        <div class="hidden md:flex flex-col items-center justify-center min-w-[100px] bg-gray-50 p-4 border-r border-gray-100">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Hari ke-<?= $i + 1; ?></span>
                            <span class="text-3xl font-bold text-gray-800 mt-1"><?= $tgl; ?></span>
                            <span class="text-sm font-medium text-gray-600"><?= $bln; ?></span>
                            <span class="text-xs text-gray-400"><?= $namaHari; ?></span>
                        </div>

                        <div class="flex-1 p-5">
                            <div class="flex flex-col md:flex-row gap-6">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 md:hidden">
                                        <span class="text-sm font-semibold text-gray-700"><?= $namaHari; ?>, <?= $tgl; ?> <?= $bln; ?> <?= $thn; ?></span>
                                        <span class="ml-auto">
                                            <?php if ($entry['status'] == 'pending'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                <i class="fas fa-clock mr-1"></i>Pending
                                            </span>
                                            <?php elseif ($entry['status'] == 'disetujui'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <i class="fas fa-check-circle mr-1"></i>Disetujui
                                            </span>
                                            <?php elseif ($entry['status'] == 'revisi'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                                <i class="fas fa-edit mr-1"></i>Revisi
                                            </span>
                                            <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                <i class="fas fa-times-circle mr-1"></i>Ditolak
                                            </span>
                                            <?php endif; ?>
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-semibold text-gray-800 truncate"><?= esc($entry['nama_kegiatan']); ?></h3>
                                        <span class="hidden md:inline-flex ml-auto">
                                            <?php if ($entry['status'] == 'pending'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                <i class="fas fa-clock mr-1"></i>Pending
                                            </span>
                                            <?php elseif ($entry['status'] == 'disetujui'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <i class="fas fa-check-circle mr-1"></i>Disetujui
                                            </span>
                                            <?php elseif ($entry['status'] == 'revisi'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                                <i class="fas fa-edit mr-1"></i>Revisi
                                            </span>
                                            <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                <i class="fas fa-times-circle mr-1"></i>Ditolak
                                            </span>
                                            <?php endif; ?>
                                        </span>
                                    </div>

                                    <div class="hidden md:flex items-center gap-4 mt-1 text-xs text-gray-400">
                                        <span><i class="far fa-calendar mr-1"></i><?= $namaHari; ?>, <?= $tgl; ?> <?= $bln; ?> <?= $thn; ?></span>
                                        <?php if ($entry['verified_at']): ?>
                                        <span><i class="far fa-check-circle mr-1"></i>Diverifikasi <?= date('d M H:i', strtotime($entry['verified_at'])); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mt-3">
                                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line"><?= esc($entry['deskripsi']); ?></p>
                                    </div>

                                    <?php if (!empty($entry['catatan_pembimbing'])): ?>
                                    <div class="mt-4 bg-orange-50 border-l-4 border-orange-400 rounded-r-xl p-4">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-comment-dots text-orange-500 text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold text-orange-700 uppercase tracking-wider">Catatan Pembimbing</p>
                                                <p class="text-sm text-orange-800 mt-1"><?= esc($entry['catatan_pembimbing']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($entry['status'] != 'disetujui'): ?>
                                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-2">
                                        <a href="<?= base_url('siswa/jurnal-pkl/edit/' . $entry['id']); ?>"
                                           class="inline-flex items-center px-3.5 py-2 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition-colors text-sm font-medium">
                                            <i class="fas fa-edit mr-1.5"></i>
                                            Edit
                                        </a>
                                        <a href="<?= base_url('siswa/jurnal-pkl/hapus/' . $entry['id']); ?>"
                                           onclick="return confirm('Yakin ingin menghapus jurnal ini?')"
                                           class="inline-flex items-center px-3.5 py-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-colors text-sm font-medium">
                                            <i class="fas fa-trash mr-1.5"></i>
                                            Hapus
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($entry['foto'])): ?>
                                <div class="flex-shrink-0">
                                    <button onclick="openLightbox('<?= base_url('files/jurnal-pkl/' . $entry['foto']); ?>')"
                                            class="group relative block w-full md:w-56 rounded-xl overflow-hidden">
                                        <div class="aspect-[4/3] md:aspect-[4/3]">
                                            <img src="<?= base_url('files/jurnal-pkl/' . $entry['foto']); ?>"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                 loading="lazy">
                                        </div>
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center">
                                            <span class="opacity-0 group-hover:opacity-100 text-white text-sm font-medium bg-black/60 px-3 py-1.5 rounded-lg transition-opacity backdrop-blur-sm">
                                                <i class="fas fa-expand mr-1"></i>Perbesar
                                            </span>
                                        </div>
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<div id="lightbox" class="fixed inset-0 z-[9999] bg-black/90 hidden items-center justify-center p-4"
     onclick="closeLightbox(event)">
    <button onclick="closeLightbox()"
            class="absolute top-4 right-4 w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white text-2xl transition-colors">
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
    const lb = document.getElementById('lightbox');
    lb.classList.add('hidden');
    lb.classList.remove('flex');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
});
</script>
<?= $this->endSection() ?>
