<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    /* Accordion slide */
    .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease;
        opacity: 0;
    }
    .accordion-content.open {
        opacity: 1;
    }

    /* Chevron spin */
    .accordion-chevron {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Student card fade */
    .student-card {
        transition: opacity 0.3s ease, transform 0.3s ease, max-height 0.35s ease;
        opacity: 1;
        transform: translateY(0);
    }
    .student-card.hide {
        opacity: 0;
        transform: translateY(-8px);
        pointer-events: none;
    }

    /* Entry row fade */
    .jurnal-entry {
        transition: opacity 0.2s ease, max-height 0.3s ease, padding 0.3s ease, margin 0.3s ease;
        opacity: 1;
        max-height: 120px;
        overflow: hidden;
    }
    .jurnal-entry.hide {
        opacity: 0;
        max-height: 0;
        padding-top: 0;
        padding-bottom: 0;
    }

    /* Modal */
    #verifyModal {
        transition: opacity 0.25s ease;
        opacity: 0;
        pointer-events: none;
    }
    #verifyModal.show {
        opacity: 1;
        pointer-events: auto;
    }
    #verifyModal .modal-backdrop {
        transition: opacity 0.25s ease;
        opacity: 0;
    }
    #verifyModal.show .modal-backdrop {
        opacity: 1;
    }
    #verifyModal .modal-panel {
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.25s ease;
        transform: scale(0.95) translateY(10px);
        opacity: 0;
    }
    #verifyModal.show .modal-panel {
        transform: scale(1) translateY(0);
        opacity: 1;
    }

    /* Filter tab pill */
    .filter-tab {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Stagger entry animation on expand */
    .accordion-content.open .jurnal-entry {
        animation: fadeSlideIn 0.25s ease forwards;
    }
    .accordion-content.open .jurnal-entry:nth-child(1) { animation-delay: 0.02s; }
    .accordion-content.open .jurnal-entry:nth-child(2) { animation-delay: 0.04s; }
    .accordion-content.open .jurnal-entry:nth-child(3) { animation-delay: 0.06s; }
    .accordion-content.open .jurnal-entry:nth-child(4) { animation-delay: 0.08s; }
    .accordion-content.open .jurnal-entry:nth-child(5) { animation-delay: 0.10s; }
    .accordion-content.open .jurnal-entry:nth-child(6) { animation-delay: 0.12s; }
    .accordion-content.open .jurnal-entry:nth-child(7) { animation-delay: 0.14s; }
    .accordion-content.open .jurnal-entry:nth-child(8) { animation-delay: 0.16s; }

    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Empty state fade */
    #emptySearch, #studentList {
        transition: opacity 0.3s ease;
    }
</style>

<div class="p-6">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-check-double mr-2 text-blue-600"></i>
            Verifikasi Jurnal PKL
        </h1>
        <p class="text-gray-500 mt-1 text-sm">Tinjau dan verifikasi jurnal PKL siswa bimbingan Anda</p>
    </div>

    <?= view('components/alerts') ?>

    <?php if (empty($groupedData)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
            <i class="fas fa-check-double text-2xl text-green-500"></i>
        </div>
        <p class="text-lg font-semibold text-gray-700">Semua jurnal sudah diverifikasi</p>
        <p class="text-sm text-gray-400 mt-1">Tidak ada jurnal PKL yang perlu ditinjau saat ini</p>
    </div>
    <?php else: ?>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Siswa</p>
                    <p class="text-xl font-bold text-gray-800"><?= $stats['total_siswa'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Pending</p>
                    <p class="text-xl font-bold text-yellow-600"><?= $stats['pending'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Disetujui</p>
                    <p class="text-xl font-bold text-green-600"><?= $stats['disetujui'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation text-red-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Perlu Tindakan</p>
                    <p class="text-xl font-bold text-red-600"><?= ($stats['revisi'] ?? 0) + ($stats['ditolak'] ?? 0) + ($stats['tinjau_ulang'] ?? 0) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs + Search -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-5">
        <div class="flex gap-1 p-1 bg-gray-100 rounded-xl overflow-x-auto flex-shrink-0">
            <?php
            $tabs = [
                ['key' => 'all', 'label' => 'Semua', 'count' => $stats['total_jurnal'] ?? 0],
                ['key' => 'pending', 'label' => 'Pending', 'count' => $stats['pending'] ?? 0, 'color' => 'yellow'],
                ['key' => 'disetujui', 'label' => 'Disetujui', 'count' => $stats['disetujui'] ?? 0, 'color' => 'green'],
                ['key' => 'revisi', 'label' => 'Revisi', 'count' => $stats['revisi'] ?? 0, 'color' => 'orange'],
                ['key' => 'ditolak', 'label' => 'Ditolak', 'count' => $stats['ditolak'] ?? 0, 'color' => 'red'],
            ];
            ?>
            <?php foreach ($tabs as $tab): ?>
            <button onclick="filterByStatus('<?= $tab['key'] ?>')"
                    class="filter-tab whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-semibold <?= $tab['key'] === 'all' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700' ?>"
                    data-tab="<?= $tab['key'] ?>">
                <?= $tab['label'] ?>
                <?php if ($tab['count'] > 0): ?>
                <span class="ml-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold
                    <?= $tab['key'] === 'pending' ? 'bg-yellow-200 text-yellow-700' :
                         ($tab['key'] === 'disetujui' ? 'bg-green-200 text-green-700' :
                         ($tab['key'] === 'revisi' ? 'bg-orange-200 text-orange-700' :
                         ($tab['key'] === 'ditolak' ? 'bg-red-200 text-red-700' : 'bg-gray-200 text-gray-600'))) ?>">
                    <?= $tab['count'] ?>
                </span>
                <?php endif; ?>
            </button>
            <?php endforeach; ?>
        </div>

        <div class="relative flex-1 sm:max-w-xs">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="searchInput" placeholder="Cari nama siswa..."
                   class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                   oninput="searchSiswa(this.value)">
        </div>
    </div>

    <!-- Student Accordions -->
    <div id="studentList" class="space-y-3">
        <?php foreach ($groupedData as $siswa): ?>
        <div class="student-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
             data-siswa-id="<?= $siswa['siswa_id'] ?>"
             data-siswa-name="<?= esc(strtolower($siswa['nama_siswa'])) ?>">

            <!-- Accordion Header -->
            <button onclick="toggleAccordion(this)"
                    class="w-full flex items-center gap-3 px-4 py-3.5 text-left hover:bg-gray-50 transition-colors group">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    <?= strtoupper(substr($siswa['nama_siswa'], 0, 1)) ?>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-gray-800 truncate"><?= esc($siswa['nama_siswa']) ?></span>
                        <span class="text-[10px] text-gray-400 hidden sm:inline">NIS: <?= esc($siswa['nis']) ?></span>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs text-gray-400"><?= esc($siswa['nama_kelas']) ?></span>
                        <span class="text-[10px] text-gray-300">&bull;</span>
                        <span class="text-xs text-gray-400"><?= count($siswa['jurnal']) ?> jurnal</span>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <?php if ($siswa['pending_count'] > 0): ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                        <i class="fas fa-clock text-[10px]"></i>
                        <?= $siswa['pending_count'] ?> pending
                    </span>
                    <?php else: ?>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                        <i class="fas fa-check-circle text-[10px]"></i>
                        Selesai
                    </span>
                    <?php endif; ?>
                </div>

                <i class="fas fa-chevron-down text-gray-400 text-xs flex-shrink-0 ml-1 accordion-chevron"></i>
            </button>

            <!-- Accordion Content -->
            <div class="accordion-content border-t border-gray-100">
                <div class="divide-y divide-gray-50">
                    <?php foreach ($siswa['jurnal'] as $jurnal): ?>
                    <div class="jurnal-entry flex items-center gap-3 px-4 py-3 hover:bg-gray-50/50 transition-colors"
                         data-status="<?= $jurnal['status'] ?>">
                        <div class="flex-shrink-0 w-16 text-center">
                            <p class="text-[10px] text-gray-400 uppercase"><?= date('D', strtotime($jurnal['tanggal'])) ?></p>
                            <p class="text-sm font-bold text-gray-700"><?= date('d/m', strtotime($jurnal['tanggal'])) ?></p>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate"><?= esc($jurnal['nama_kegiatan']) ?></p>
                            <p class="text-xs text-gray-400 truncate"><?= esc($jurnal['deskripsi']) ?></p>
                        </div>

                        <?php if ($jurnal['foto']): ?>
                        <div class="flex-shrink-0" title="Ada foto dokumentasi">
                            <i class="fas fa-camera text-blue-400 text-xs"></i>
                        </div>
                        <?php endif; ?>

                        <div class="flex-shrink-0">
                            <?php if ($jurnal['status'] == 'pending'): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700">
                                <i class="fas fa-clock text-[8px]"></i>Pending
                            </span>
                            <?php elseif ($jurnal['status'] == 'disetujui'): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                <i class="fas fa-check-circle text-[8px]"></i>OK
                            </span>
                            <?php elseif ($jurnal['status'] == 'revisi'): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700">
                                <i class="fas fa-pen text-[8px]"></i>Revisi
                            </span>
                            <?php elseif ($jurnal['status'] == 'tinjau_ulang'): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">
                                <i class="fas fa-rotate text-[8px]"></i>Ulang
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">
                                <i class="fas fa-times-circle text-[8px]"></i>Tolak
                            </span>
                            <?php endif; ?>
                        </div>

                        <div class="flex-shrink-0">
                            <?php if ($jurnal['status'] === 'disetujui'): ?>
                            <form action="<?= base_url('guru/jurnal-pkl/batal-verifikasi/' . $jurnal['id']); ?>" method="POST" class="inline">
                                <?= csrf_field(); ?>
                                <button type="submit"
                                        onclick="event.stopPropagation(); return confirm('Batalkan verifikasi? Status akan kembali ke Pending.')"
                                        title="Batalkan Verifikasi"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-orange-100 text-orange-600 hover:bg-orange-200 transition-colors text-xs">
                                    <i class="fas fa-rotate-left"></i>
                                </button>
                            </form>
                            <?php else: ?>
                            <button onclick="event.stopPropagation(); openVerifyModal(<?= $jurnal['id']; ?>, '<?= esc($jurnal['nama_siswa']); ?>', '<?= esc($jurnal['nama_kegiatan']); ?>', '<?= esc(date('d M Y', strtotime($jurnal['tanggal']))); ?>', '<?= $jurnal['foto'] ? base_url('files/jurnal-pkl/' . $jurnal['foto']) : '' ?>')"
                                    title="Tinjau Jurnal"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-teal-100 text-teal-600 hover:bg-teal-200 transition-colors text-xs">
                                <i class="fas fa-file-pen"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="emptySearch" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <i class="fas fa-search text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500 font-medium">Tidak ada siswa ditemukan</p>
        <p class="text-sm text-gray-400 mt-1">Coba kata kunci lain</p>
    </div>

    <?php endif; ?>
</div>

<!-- Modal Verifikasi -->
<div id="verifyModal" class="fixed inset-0 z-[60]">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeVerifyModal()"></div>

        <form id="verifyForm" method="POST" class="modal-panel relative bg-white w-11/12 sm:w-full sm:max-w-md max-h-[85vh] sm:max-h-[90vh] flex flex-col rounded-2xl shadow-2xl overflow-hidden">
            <?= csrf_field(); ?>

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-base font-bold text-gray-800">
                    <i class="fas fa-file-pen mr-2 text-teal-600"></i>
                    Tinjau Jurnal
                </h3>
                <button type="button" onclick="closeVerifyModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition-colors text-gray-500">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                <div class="bg-gray-50 rounded-xl p-3 space-y-1">
                    <p class="text-sm"><span class="text-gray-400">Siswa:</span> <span id="verifySiswa" class="font-semibold text-gray-800"></span></p>
                    <p class="text-sm"><span class="text-gray-400">Kegiatan:</span> <span id="verifyKegiatan" class="font-semibold text-gray-800"></span></p>
                    <p class="text-sm"><span class="text-gray-400">Tanggal:</span> <span id="verifyTanggal" class="font-semibold text-gray-800"></span></p>
                </div>

                <div id="verifyFotoContainer" class="hidden">
                    <p class="text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-camera mr-1.5 text-blue-500"></i>
                        Dokumentasi
                    </p>
                    <div class="relative group cursor-zoom-in" onclick="openLightbox(document.getElementById('verifyFoto').src)">
                        <img id="verifyFoto" src="" class="w-full max-h-48 object-contain rounded-xl border border-gray-200 bg-gray-50 transition-shadow group-hover:shadow-lg">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 rounded-xl transition-colors flex items-center justify-center">
                            <span class="opacity-0 group-hover:opacity-100 transition-opacity bg-black/60 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur-sm">
                                <i class="fas fa-expand mr-1"></i>Perbesar
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keputusan <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="relative cursor-pointer rounded-xl text-gray-500 has-[:checked]:bg-green-500 has-[:checked]:shadow has-[:checked]:text-white hover:bg-green-50 hover:text-green-600 transition-all active:scale-[0.97]">
                            <input type="radio" name="status" value="disetujui" class="sr-only" required>
                            <div class="flex flex-col items-center py-3 px-2 gap-1">
                                <i class="fas fa-check-circle text-lg"></i>
                                <span class="text-xs font-bold">Setuju</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer rounded-xl text-gray-500 has-[:checked]:bg-orange-500 has-[:checked]:shadow has-[:checked]:text-white hover:bg-orange-50 hover:text-orange-600 transition-all active:scale-[0.97]">
                            <input type="radio" name="status" value="revisi" class="sr-only">
                            <div class="flex flex-col items-center py-3 px-2 gap-1">
                                <i class="fas fa-pen-to-square text-lg"></i>
                                <span class="text-xs font-bold">Revisi</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer rounded-xl text-gray-500 has-[:checked]:bg-red-500 has-[:checked]:shadow has-[:checked]:text-white hover:bg-red-50 hover:text-red-600 transition-all active:scale-[0.97]">
                            <input type="radio" name="status" value="ditolak" class="sr-only">
                            <div class="flex flex-col items-center py-3 px-2 gap-1">
                                <i class="fas fa-xmark-circle text-lg"></i>
                                <span class="text-xs font-bold">Tolak</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Catatan <span class="font-normal text-gray-400">(opsional)</span>
                    </label>
                    <textarea id="catatan" name="catatan" rows="2"
                              placeholder="Tulis catatan untuk siswa..."
                              class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none"></textarea>
                </div>
            </div>

            <div class="flex-shrink-0 border-t border-gray-200 bg-gray-50 px-5 py-3.5">
                <div class="flex gap-3">
                    <button type="button" onclick="closeVerifyModal()"
                            class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl transition-colors font-semibold text-sm active:scale-[0.98]">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl transition-all font-semibold text-sm shadow hover:shadow-lg active:scale-[0.98]">
                        <i class="fas fa-check mr-1.5"></i>Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lightbox Fullscreen -->
<div id="lightbox" class="fixed inset-0 z-[70] bg-black/90 backdrop-blur-sm flex items-center justify-center" style="display:none" onclick="if(event.target===this)closeLightbox()">
    <!-- Close btn -->
    <button onclick="closeLightbox()" class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors backdrop-blur-sm">
        <i class="fas fa-times text-lg"></i>
    </button>
    <!-- Zoom controls -->
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-10 flex items-center gap-2 bg-black/50 rounded-full px-3 py-1.5 backdrop-blur-sm">
        <button onclick="event.stopPropagation(); zoomLightbox(-0.25)" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-sm">
            <i class="fas fa-minus"></i>
        </button>
        <span id="zoomLevel" class="text-white text-xs font-semibold min-w-[40px] text-center">100%</span>
        <button onclick="event.stopPropagation(); zoomLightbox(0.25)" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-sm">
            <i class="fas fa-plus"></i>
        </button>
        <div class="w-px h-5 bg-white/20 mx-1"></div>
        <button onclick="event.stopPropagation(); resetZoom()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-xs" title="Reset">
            <i class="fas fa-expand"></i>
        </button>
    </div>
    <!-- Image -->
    <img id="lightboxImg" src="" class="max-w-[90vw] max-h-[85vh] object-contain rounded-lg transition-transform duration-300 ease-out cursor-grab active:cursor-grabbing select-none"
         draggable="false"
         onmousedown="startPan(event)" ontouchstart="startPanTouch(event)"
         style="transform:scale(1)">
</div>

<script>
// Accordion: smooth slide via max-height
function toggleAccordion(btn) {
    const card = btn.closest('.student-card');
    const content = card.querySelector('.accordion-content');
    const chevron = btn.querySelector('.accordion-chevron');
    const isOpen = content.classList.contains('open');

    if (isOpen) {
        content.style.maxHeight = content.scrollHeight + 'px';
        requestAnimationFrame(() => {
            content.style.maxHeight = '0';
            content.classList.remove('open');
        });
        chevron.style.transform = '';
    } else {
        content.classList.add('open');
        content.style.maxHeight = content.scrollHeight + 'px';
        chevron.style.transform = 'rotate(180deg)';
        content.addEventListener('transitionend', function handler() {
            if (content.classList.contains('open')) {
                content.style.maxHeight = 'none';
            }
            content.removeEventListener('transitionend', handler);
        });
    }
}

// Filter by status
function filterByStatus(status) {
    document.querySelectorAll('.filter-tab').forEach(tab => {
        const isActive = tab.dataset.tab === status;
        tab.classList.toggle('bg-white', isActive);
        tab.classList.toggle('text-gray-800', isActive);
        tab.classList.toggle('shadow-sm', isActive);
        tab.classList.toggle('text-gray-500', !isActive);
    });

    document.querySelectorAll('.student-card').forEach(card => {
        const entries = card.querySelectorAll('.jurnal-entry');
        let visibleCount = 0;

        entries.forEach(entry => {
            const match = status === 'all' || entry.dataset.status === status;
            entry.classList.toggle('hide', !match);
            if (match) visibleCount++;
        });

        if (visibleCount === 0) {
            card.classList.add('hide');
            setTimeout(() => { card.style.display = 'none'; }, 300);
        } else {
            card.style.display = '';
            requestAnimationFrame(() => card.classList.remove('hide'));

            if (status !== 'all') {
                const content = card.querySelector('.accordion-content');
                const chevron = card.querySelector('.accordion-chevron');
                if (!content.classList.contains('open')) {
                    content.classList.add('open');
                    content.style.maxHeight = 'none';
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                }
            }
        }
    });

    updateEmptyState();
}

// Search
function searchSiswa(query) {
    const q = query.toLowerCase().trim();
    document.querySelectorAll('.student-card').forEach(card => {
        const match = !q || card.dataset.siswaName.includes(q);
        if (match) {
            card.style.display = '';
            requestAnimationFrame(() => card.classList.remove('hide'));
        } else {
            card.classList.add('hide');
            setTimeout(() => { card.style.display = 'none'; }, 300);
        }
    });
    updateEmptyState();
}

function updateEmptyState() {
    setTimeout(() => {
        const visible = document.querySelectorAll('.student-card:not([style*="display: none"]):not(.hide)').length;
        document.getElementById('emptySearch').classList.toggle('hidden', visible > 0);
        document.getElementById('studentList').classList.toggle('hidden', visible === 0);
    }, 320);
}

// Modal: fade + scale
function openVerifyModal(id, siswa, kegiatan, tanggal, foto) {
    const modal = document.getElementById('verifyModal');
    document.getElementById('verifySiswa').textContent = siswa;
    document.getElementById('verifyKegiatan').textContent = kegiatan;
    document.getElementById('verifyTanggal').textContent = tanggal;
    document.getElementById('verifyForm').action = '<?= base_url('guru/jurnal-pkl/verify/'); ?>' + id;

    const fotoContainer = document.getElementById('verifyFotoContainer');
    const fotoImg = document.getElementById('verifyFoto');
    if (foto) {
        fotoImg.src = foto;
        fotoContainer.classList.remove('hidden');
    } else {
        fotoImg.src = '';
        fotoContainer.classList.add('hidden');
    }

    document.querySelectorAll('input[name="status"]').forEach(r => r.checked = false);
    document.getElementById('catatan').value = '';

    modal.classList.remove('hidden');
    requestAnimationFrame(() => modal.classList.add('show'));
}

function closeVerifyModal() {
    const modal = document.getElementById('verifyModal');
    modal.classList.remove('show');
    setTimeout(() => modal.classList.add('hidden'), 250);
}

document.getElementById('verifyModal').addEventListener('click', function(e) {
    if (e.target === this) closeVerifyModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (document.getElementById('lightbox').style.display !== 'none') {
            closeLightbox();
        } else {
            closeVerifyModal();
        }
    }
    if (document.getElementById('lightbox').style.display !== 'none') {
        if (e.key === '+' || e.key === '=') zoomLightbox(0.25);
        if (e.key === '-') zoomLightbox(-0.25);
        if (e.key === '0') resetZoom();
    }
});

// Lightbox
let currentZoom = 1;
let panX = 0, panY = 0, isPanning = false, startPanX = 0, startPanY = 0;

function openLightbox(src) {
    const lb = document.getElementById('lightbox');
    const img = document.getElementById('lightboxImg');
    img.src = src;
    currentZoom = 1;
    panX = 0;
    panY = 0;
    updateTransform();
    updateZoomLabel();
    lb.style.display = 'flex';
    lb.style.opacity = '0';
    requestAnimationFrame(() => { lb.style.opacity = '1'; });
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lb = document.getElementById('lightbox');
    lb.style.opacity = '0';
    setTimeout(() => {
        lb.style.display = 'none';
        document.body.style.overflow = '';
    }, 250);
}

function zoomLightbox(delta) {
    currentZoom = Math.min(4, Math.max(0.5, currentZoom + delta));
    if (currentZoom <= 1) { panX = 0; panY = 0; }
    updateTransform();
    updateZoomLabel();
}

function resetZoom() {
    currentZoom = 1;
    panX = 0;
    panY = 0;
    updateTransform();
    updateZoomLabel();
}

function updateTransform() {
    const img = document.getElementById('lightboxImg');
    img.style.transform = `scale(${currentZoom}) translate(${panX}px, ${panY}px)`;
}

function updateZoomLabel() {
    document.getElementById('zoomLevel').textContent = Math.round(currentZoom * 100) + '%';
}

// Mouse pan
function startPan(e) {
    if (currentZoom <= 1) return;
    isPanning = true;
    startPanX = e.clientX - panX;
    startPanY = e.clientY - panY;
    e.preventDefault();
}
document.addEventListener('mousemove', function(e) {
    if (!isPanning) return;
    panX = e.clientX - startPanX;
    panY = e.clientY - startPanY;
    updateTransform();
});
document.addEventListener('mouseup', () => { isPanning = false; });

// Touch pan
let startTouchDist = 0, startTouchZoom = 1;
function startPanTouch(e) {
    if (e.touches.length === 1 && currentZoom > 1) {
        isPanning = true;
        startPanX = e.touches[0].clientX - panX;
        startPanY = e.touches[0].clientY - panY;
    } else if (e.touches.length === 2) {
        startTouchDist = Math.hypot(e.touches[0].clientX - e.touches[1].clientX, e.touches[0].clientY - e.touches[1].clientY);
        startTouchZoom = currentZoom;
    }
}
document.addEventListener('touchmove', function(e) {
    if (document.getElementById('lightbox').style.display === 'none') return;
    if (e.touches.length === 1 && isPanning) {
        panX = e.touches[0].clientX - startPanX;
        panY = e.touches[0].clientY - startPanY;
        updateTransform();
    } else if (e.touches.length === 2) {
        const dist = Math.hypot(e.touches[0].clientX - e.touches[1].clientX, e.touches[0].clientY - e.touches[1].clientY);
        currentZoom = Math.min(4, Math.max(0.5, startTouchZoom * (dist / startTouchDist)));
        if (currentZoom <= 1) { panX = 0; panY = 0; }
        updateTransform();
        updateZoomLabel();
    }
}, { passive: false });
document.addEventListener('touchend', () => { isPanning = false; });

// Mouse wheel zoom
document.getElementById('lightbox').addEventListener('wheel', function(e) {
    if (this.style.display === 'none') return;
    e.preventDefault();
    zoomLightbox(e.deltaY < 0 ? 0.15 : -0.15);
}, { passive: false });
</script>
<?= $this->endSection() ?>
