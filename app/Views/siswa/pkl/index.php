<?= $this->extend(get_device_layout()) ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/pkl-jurnal.css') ?>">
<style>
    .pkl-timeline-line {
        left: 19px !important;
    }

    .pkl-timeline-dot {
        left: 14px !important;
    }

    .tl-card {
        transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
    }

    .tl-card:active {
        transform: scale(0.99);
    }

    .tl-card.open {
        border-color: rgba(59, 130, 246, 0.4);
        background-color: rgba(59, 130, 246, 0.02);
    }

    .tl-chevron {
        transition: transform 0.3s ease;
    }

    .tl-chevron.open {
        transform: rotate(90deg);
    }

    .tl-panel {
        transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease;
    }

    .stat-grid-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-grid-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .progress-fill-anim {
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ── Toast Notification ── */
    #toastContainer {
        position: fixed;
        top: 24px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: calc(100% - 32px);
        max-width: 400px;
        pointer-events: none;
    }

    .toast {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #fff;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        pointer-events: all;
        animation: toastIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }

    .toast.toast-error {
        background: #ef4444;
    }

    .toast.toast-success {
        background: #10b981;
    }

    .toast.toast-info {
        background: #3b82f6;
    }

    .toast-icon {
        font-size: 1rem;
        flex-shrink: 0;
    }

    @keyframes toastIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes toastOut {
        from {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        to {
            opacity: 0;
            transform: translateY(-12px) scale(0.95);
        }
    }

    /* ── Confirm Modal ── */
    #confirmOverlay {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.25s ease;
        pointer-events: none;
        padding: 16px;
    }

    #confirmOverlay.show {
        opacity: 1;
        pointer-events: all;
    }

    #confirmBox {
        background: #fff;
        border-radius: 20px;
        padding: 24px;
        width: 100%;
        max-width: 400px;
        transform: scale(0.95);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        text-align: center;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    #confirmOverlay.show #confirmBox {
        transform: scale(1);
    }

    .confirm-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 9999px;
        background: #fef2f2;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 1.5rem;
        color: #ef4444;
    }

    .confirm-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 8px;
    }

    .confirm-desc {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 24px;
        line-height: 1.5;
    }

    .confirm-actions {
        display: flex;
        gap: 12px;
    }

    .confirm-cancel {
        flex: 1;
        padding: 10px 16px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #374151;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.2s;
    }

    .confirm-cancel:hover {
        background: #f3f4f6;
    }

    .confirm-ok {
        flex: 1;
        padding: 10px 16px;
        border-radius: 10px;
        border: none;
        background: #ef4444;
        color: #fff;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        transition: background 0.2s, transform 0.15s;
    }

    .confirm-ok:hover {
        background: #dc2626;
    }

    .confirm-ok:active {
        transform: scale(0.97);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$bulanIndo = [
    1 => 'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember'
];
$hariIndo = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];
$hariSingkat = [
    'Sunday' => 'Min',
    'Monday' => 'Sen',
    'Tuesday' => 'Sel',
    'Wednesday' => 'Rab',
    'Thursday' => 'Kam',
    'Friday' => 'Jum',
    'Saturday' => 'Sab'
];

helper('setting');

?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 px-4">

    <!-- ========== LEFT & CENTER: Main Activity Feed ========== -->
    <div class="lg:col-span-8 space-y-6">

        <!-- Flash Messages -->
        <?= view('components/alerts') ?>

        <!-- Riwayat Kegiatan (Timeline) -->
        <section class="space-y-3">
            <!-- Tambah Aktivitas -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i>
                    Riwayat Kegiatan
                </h2>
                <a href="<?= base_url('siswa/jurnal-pkl/tambah'); ?>"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary text-white rounded-xl font-semibold text-sm shadow-md hover:bg-blue-600 active:scale-95 transition-all w-full sm:w-auto">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Tambah Aktivitas
                </a>
            </div>


            <?php if (empty($timeline)): ?>
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 text-center">
                    <div
                        class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center pkl-empty-icon">
                        <i class="fa-solid fa-clock-rotate-left text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-600 font-medium">Belum ada riwayat aktivitas</p>
                </div>
            <?php else: ?>
                <div class="rounded-2xl border border-gray-200 shadow-sm overflow-hidden p-2">
                    <div
                        class="pkl-timeline-container overflow-y-auto max-h-96 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-track]:rounded [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-thumb]:rounded [&::-webkit-scrollbar-thumb]:hover:bg-gray-400 [&::-webkit-scrollbar-button]:hidden [&::-webkit-scrollbar-button]:h-0 [&::-webkit-scrollbar-button]:w-0">
                        <div class="pkl-timeline-line"></div>
                        <?php foreach ($timeline as $day): ?>
                            <?php
                            $dateObj = new DateTime($day['tanggal']);
                            $dayShort = $hariSingkat[$dateObj->format('l')];
                            $dateDay = $dateObj->format('d');
                            $isToday = $day['tanggal'] === date('Y-m-d');

                            $allVerified = ($day['total_aktivitas'] == $day['fully_verified']);
                            $hasRevision = ($day['revision'] > 0);

                            $dotColor = $isToday ? 'dot-blue' : 'dot-gray';
                            $dotActive = $isToday ? 'pkl-timeline-dot-active' : '';

                            $cardBorder = $isToday ? 'border-l-4 border-l-primary' : '';
                            if ($allVerified) {
                                $statusBadge = 'Selesai';
                                $statusIcon = 'fa-circle-check text-green-500';
                            } elseif ($hasRevision) {
                                $statusBadge = 'Revisi';
                                $statusIcon = 'fa-flag text-red-500';
                            } else {
                                $statusBadge = 'Menunggu';
                                $statusIcon = 'fa-clock text-yellow-500';
                            }
                            ?>
                            <div class="relative pl-12 tl-day mb-1" data-date="<?= $day['tanggal'] ?>">
                                <div class="pkl-timeline-dot <?= $dotColor ?> <?= $dotActive ?>"></div>

                                <!-- Day Card (accordion trigger) -->
                                <div class="tl-card bg-white p-5 rounded-2xl border border-gray-200 shadow-sm <?= $cardBorder ?> flex items-center justify-between group hover:border-primary/50 transition-all cursor-pointer select-none"
                                    onclick="toggleDayAccordion(this)">
                                    <div class="flex items-center gap-5">
                                        <div class="text-center w-12 pr-5 border-r border-gray-200">
                                            <p class="text-[11px] font-semibold text-gray-500 uppercase"><?= $dayShort ?></p>
                                            <p
                                                class="text-2xl font-bold leading-none <?= $isToday ? 'text-primary' : 'text-gray-900' ?>">
                                                <?= $dateDay ?>
                                            </p>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 mb-1"><?= $day['total_aktivitas'] ?> Aktivitas
                                                Tercatat</p>
                                            <div
                                                class="flex items-center gap-1.5 text-sm <?= $hasRevision ? 'text-red-600' : 'text-gray-500' ?>">
                                                <i class="fa-solid <?= $statusIcon ?> text-xs"></i>
                                                <span>Status: <?= $statusBadge ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <i
                                        class="fa-solid fa-chevron-right text-gray-400 tl-chevron transition-transform duration-300"></i>
                                </div>

                                <!-- Accordion Panel -->
                                <div class="tl-panel overflow-hidden transition-all duration-300 ease-in-out"
                                    style="max-height: 0; opacity: 0;">
                                    <div class="mt-2 bg-white rounded-2xl border border-gray-200 shadow-sm px-4 py-2">
                                        <div class="tl-panel-body text-center">
                                            <div class="inline-flex items-center gap-2 text-gray-400 text-sm">
                                                <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                                                <span class="text-xs">Memuat...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </section>

    </div>

    <!-- ========== RIGHT SIDEBAR: Stats & Reports ========== -->
    <aside class="lg:col-span-4 space-y-6">

        <!-- Statistik Tugas -->
        <section class="space-y-4">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Statistik Tugas</h4>
            <div class="grid grid-cols-2 gap-3">
                <!-- Total -->
                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200 stat-grid-card">
                    <p class="text-[11px] font-bold text-gray-500 uppercase mb-1">Total</p>
                    <div class="flex items-end justify-between">
                        <span
                            class="text-2xl font-bold text-gray-900 leading-none pkl-stat-number"><?= str_pad($stats['total_tasks'] ?? 0, 2, '0', STR_PAD_LEFT) ?></span>
                        <i class="fa-solid fa-clipboard-question text-gray-300"></i>
                    </div>
                </div>
                <!-- Selesai -->
                <div class="bg-green-50 p-4 rounded-2xl border border-green-200 stat-grid-card">
                    <p class="text-[11px] font-bold text-green-700 uppercase mb-1">Selesai</p>
                    <div class="flex items-end justify-between">
                        <span
                            class="text-2xl font-bold text-green-600 leading-none pkl-stat-number"><?= str_pad($stats['fully_verified'] ?? 0, 2, '0', STR_PAD_LEFT) ?></span>
                        <i class="fa-solid fa-circle-check text-green-300"></i>
                    </div>
                </div>
                <!-- Antrean -->
                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200 stat-grid-card">
                    <p class="text-[11px] font-bold text-gray-500 uppercase mb-1">Antrean</p>
                    <div class="flex items-end justify-between">
                        <span
                            class="text-2xl font-bold text-gray-900 leading-none pkl-stat-number"><?= str_pad(($stats['submitted'] ?? 0) + ($stats['draft'] ?? 0) + ($stats['verified_by_instruktur'] ?? 0), 2, '0', STR_PAD_LEFT) ?></span>
                        <i class="fa-solid fa-clock text-gray-300"></i>
                    </div>
                </div>
                <!-- Revisi -->
                <div class="bg-red-50 p-4 rounded-2xl border border-red-200 stat-grid-card">
                    <p class="text-[11px] font-bold text-red-600 uppercase mb-1">Revisi</p>
                    <div class="flex items-end justify-between">
                        <span
                            class="text-2xl font-bold text-red-500 leading-none pkl-stat-number"><?= str_pad($stats['revision'] ?? 0, 2, '0', STR_PAD_LEFT) ?></span>
                        <i class="fa-solid fa-pen-to-square text-red-300"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cetak Laporan -->
        <section class="space-y-4">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Cetak Laporan</h4>
            <div class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100 flex flex-col gap-3">
                <button onclick="openCetakModal('jurnal')"
                    class="w-full py-3 px-4 bg-primary text-white rounded-xl font-semibold text-sm flex items-center justify-center gap-2 shadow-sm hover:bg-blue-600 transition-all cursor-pointer">
                    <i class="fa-solid fa-file-lines"></i>
                    Jurnal Kegiatan PKL
                </button>
                <?php if (!empty($tasks)): ?>
                    <button onclick="openCetakModal('catatan', '<?= implode('-', array_column($tasks, 'id')) ?>')"
                        class="w-full py-3 px-4 bg-white text-gray-600 border border-gray-200 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 hover:bg-gray-50 transition-all cursor-pointer">
                        <i class="fa-solid fa-list-check"></i>
                        Catatan Kegiatan PKL
                    </button>
                <?php endif; ?>
            </div>
        </section>

    </aside>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<!-- Lightbox -->
<div id="lightbox" class="fixed inset-0 z-[9999] bg-black/90 hidden items-center justify-center p-4"
    onclick="closeLightbox(event)">
    <button onclick="closeLightbox()"
        class="absolute top-4 right-4 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white text-lg transition-colors">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <img id="lightboxImg" class="max-w-full max-h-[90vh] rounded-2xl shadow-2xl object-contain" src="">
</div>

<!-- Modal Cetak Jurnal -->
<div id="modalCetakJurnal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
    onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
        <div class="px-5 pt-5 pb-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-900">Pilih Minggu</h3>
                <button onclick="document.getElementById('modalCetakJurnal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                <?= get_jurnal_pkl_start_date() ? date('d M Y', strtotime(get_jurnal_pkl_start_date())) . ' – ' . (get_jurnal_pkl_end_date() ? date('d M Y', strtotime(get_jurnal_pkl_end_date())) : '...') : 'Belum diatur' ?>
            </p>
        </div>
        <div id="weekList" class="p-4 space-y-2 max-h-80 overflow-y-auto"></div>
    </div>
</div>

<!-- Modal Error Cetak -->
<div id="modalPrintError"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
    onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
        <div class="px-5 pt-5 pb-4 text-center">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-amber-100 flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation text-xl text-amber-500"></i>
            </div>
            <h3 id="printErrorTitle" class="text-base font-bold text-gray-900 mb-1">Data Tidak Tersedia</h3>
            <p id="printErrorMessage" class="text-sm text-gray-500 leading-relaxed">Belum ada catatan kegiatan yang
                dapat dicetak.</p>
        </div>
        <div id="printErrorDetails" class="px-5 pb-4 hidden">
            <div class="bg-gray-50 rounded-xl p-3 text-left">
                <ul id="printErrorDetailsList" class="text-xs text-gray-600 space-y-1.5"></ul>
            </div>
        </div>
        <div class="px-5 pb-5">
            <button onclick="document.getElementById('modalPrintError').classList.add('hidden')"
                class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold text-sm transition-colors">
                Mengerti
            </button>
        </div>
    </div>
</div>

<script>
    var TIMELINE_DAY_URL = '<?= base_url('siswa/jurnal-pkl/hari/') ?>';
    var PKL_START_DATE = '<?= get_jurnal_pkl_start_date() ?? '' ?>';
    var PKL_END_DATE = '<?= get_jurnal_pkl_end_date() ?? '' ?>';
    var CURRENT_DATE = '<?= date('Y-m-d') ?>';
    var WEEK_READINESS_URL = '<?= base_url('siswa/jurnal-pkl/week-readiness') ?>';

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.progress-fill-anim').forEach(bar => {
            const w = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => { bar.style.width = w; }, 300);
        });

        var today = '<?= date('Y-m-d') ?>';
        var todayItem = document.querySelector('.tl-day[data-date="' + today + '"]');
        if (todayItem) {
            var card = todayItem.querySelector('.tl-card');
            if (card && !card.classList.contains('open')) {
                toggleDayAccordion(card);
            }
        }
    });

    function toggleDayAccordion(card) {
        const dayItem = card.closest('.tl-day');
        const panel = dayItem.querySelector('.tl-panel');
        const chevron = card.querySelector('.tl-chevron');
        const isOpen = card.classList.contains('open');

        if (isOpen) {
            panel.style.maxHeight = '0';
            panel.style.opacity = '0';
            card.classList.remove('open');
            chevron.classList.remove('open');
        } else {
            card.classList.add('open');
            chevron.classList.add('open');

            const body = panel.querySelector('.tl-panel-body');
            if (body.getAttribute('data-loaded') !== '1') {
                fetchDayActivities(dayItem.getAttribute('data-date'), body);
            }

            panel.style.maxHeight = '600px';
            panel.style.opacity = '1';

            setTimeout(() => {
                if (card.classList.contains('open')) {
                    panel.style.maxHeight = panel.scrollHeight + 'px';
                }
            }, 400);
        }
    }

    function fetchDayActivities(date, container) {
        fetch(TIMELINE_DAY_URL + date)
            .then(function (r) { return r.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var rows = doc.querySelectorAll('.divide-y > div');
                var dayUrl = TIMELINE_DAY_URL + date;

                if (rows.length === 0) {
                    container.innerHTML =
                        '<div class="text-center py-5">' +
                        '<div class="w-10 h-10 mx-auto mb-2 rounded-full bg-gray-100 flex items-center justify-center">' +
                        '<i class="fa-solid fa-clipboard-list text-gray-400"></i>' +
                        '</div>' +
                        '<p class="text-sm text-gray-500">Belum ada aktivitas</p>' +
                        '<a href="' + dayUrl + '" class="text-xs font-semibold text-primary hover:underline mt-2 inline-block">Lihat Halaman →</a>' +
                        '</div>';
                    markLoaded(container);
                    return;
                }

                var out = '<div class="divide-y divide-gray-100 -my-1">';

                var MAX_VISIBLE = 2;
                var visibleCount = Math.min(rows.length, MAX_VISIBLE);

                for (var i = 0; i < visibleCount; i++) {
                    var row = rows[i];
                    var icon = row.querySelector('.fa-circle-check, .fa-check, .fa-check-double, .fa-clock, .fa-edit, .fa-pen');
                    var taskEl = row.querySelector('.rounded-full.bg-blue-100');
                    var descEl = row.querySelector('.text-sm.text-gray-700');
                    var img = row.querySelector('img[src*="pkl-progress"]');
                    var noteO = row.querySelector('.bg-orange-50');
                    var noteP = row.querySelector('.bg-purple-50');
                    var editLink = row.querySelector('a[href*="edit-progress"]');

                    var sColor = 'text-gray-400';
                    var sBg = 'bg-gray-100 text-gray-500';
                    var sLabel = 'Draft';
                    var sIcon = '';
                    if (icon) {
                        if (icon.classList.contains('fa-check-double')) { sColor = 'text-blue-500'; sBg = 'bg-blue-100 text-blue-600'; sLabel = 'Proses Pembimbing'; sIcon = 'fa-chalkboard'; }
                        else if (icon.classList.contains('fa-circle-check')) { sColor = 'text-orange-500'; sBg = 'bg-orange-100 text-orange-600'; sLabel = 'Proses Instruktur'; sIcon = 'fa-industry'; }
                        else if (icon.classList.contains('fa-check')) { sColor = 'text-green-500'; sBg = 'bg-green-100 text-green-600'; sLabel = 'Selesai'; }
                        else if (icon.classList.contains('fa-clock')) { sColor = 'text-yellow-500'; sBg = 'bg-yellow-100 text-yellow-600'; sLabel = 'Menunggu'; }
                        else if (icon.classList.contains('fa-edit')) { sColor = 'text-red-500'; sBg = 'bg-red-100 text-red-600'; sLabel = 'Revisi'; }
                    }

                    var taskName = taskEl ? taskEl.textContent.trim() : 'Aktivitas';
                    var deskripsi = descEl ? descEl.textContent.trim() : '';

                    var item = '<a href="' + (editLink ? editLink.getAttribute('href') : dayUrl) + '" class="block p-3 -mx-3 rounded-xl hover:bg-gray-50 transition-colors group">' +
                        '<div class="flex items-center gap-3">' +
                        '<span class="flex-shrink-0 mt-0.5"><i class="fa-solid fa-circle ' + sColor + ' text-[8px]"></i></span>' +
                        '<div class="flex-1 min-w-0">' +
                        '<p class="text-sm font-semibold text-gray-800 truncate group-hover:text-primary transition-colors">' + taskName + '</p>' +
                        (deskripsi ? '<p class="text-xs text-gray-500 mt-0.5 truncate">' + deskripsi + '</p>' : '') +
                        '</div>' +
                        '<div class="flex items-center gap-1.5 flex-shrink-0">';

                    if (img) {
                        var imgSrc = img.getAttribute('src');
                        item += '<img src="' + imgSrc + '" class="w-8 h-8 rounded-lg object-cover border border-gray-200 shadow-sm flex-shrink-0" loading="lazy" alt="Foto">';
                    }
                    if (noteO) {
                        var noteOText = noteO.querySelector('p.text-sm') ? noteO.querySelector('p.text-sm').textContent.trim() : (noteO.textContent.trim());
                        var noteOShort = noteOText.length > 24 ? noteOText.substring(0, 24) + '…' : noteOText;
                        item += '<span class="hidden sm:flex items-center gap-1 max-w-[100px] bg-orange-50 border border-orange-100 text-orange-700 text-[10px] font-medium px-1.5 py-0.5 rounded-lg" title="' + noteOText.replace(/"/g, '&quot;') + '">' +
                            '<i class="fa-solid fa-comment text-orange-400 flex-shrink-0 text-[8px]"></i>' +
                            '<span class="truncate">' + noteOShort + '</span>' +
                            '</span>' +
                            '<span class="sm:hidden inline-flex items-center justify-center w-6 h-6 rounded bg-orange-50 text-orange-400"><i class="fa-solid fa-comment text-[9px]"></i></span>';
                    }
                    if (noteP) {
                        var notePText = noteP.querySelector('p.text-sm') ? noteP.querySelector('p.text-sm').textContent.trim() : (noteP.textContent.trim());
                        var notePShort = notePText.length > 24 ? notePText.substring(0, 24) + '…' : notePText;
                        item += '<span class="hidden sm:flex items-center gap-1 max-w-[100px] bg-purple-50 border border-purple-100 text-purple-700 text-[10px] font-medium px-1.5 py-0.5 rounded-lg" title="' + notePText.replace(/"/g, '&quot;') + '">' +
                            '<i class="fa-solid fa-comment-dots text-purple-400 flex-shrink-0 text-[8px]"></i>' +
                            '<span class="truncate">' + notePShort + '</span>' +
                            '</span>' +
                            '<span class="sm:hidden inline-flex items-center justify-center w-6 h-6 rounded bg-purple-50 text-purple-400"><i class="fa-solid fa-comment-dots text-[9px]"></i></span>';
                    }

                    item += '<span class="text-[10px] font-bold px-2 py-0.5 rounded-full ' + sBg + '" title="' + sLabel + '">' + (sIcon ? '<i class="fa-solid ' + sIcon + '"></i>' : sLabel) + '</span>' +
                        '</div>' +
                        '</div></a>';

                    out += item;
                }

                out += '</div>';

                var remaining = rows.length - MAX_VISIBLE;
                if (remaining > 0) {
                    out += '<div class="mt-3 pt-3 border-t border-gray-100 text-center">' +
                        '<a href="' + dayUrl + '" class="text-xs font-semibold text-primary hover:underline">Lihat Semua (' + remaining + ')</a>' +
                        '</div>';
                }

                container.innerHTML = out;
                container.classList.remove('text-center', 'py-6');
                markLoaded(container);
            })
            .catch(function () {
                container.innerHTML =
                    '<div class="text-center py-4">' +
                    '<i class="fa-solid fa-triangle-exclamation text-red-400 mb-1"></i>' +
                    '<p class="text-sm text-red-400">Gagal memuat aktivitas</p>' +
                    '</div>';
                markLoaded(container);
            });
    }

    function markLoaded(el) {
        el.setAttribute('data-loaded', '1');
        var panel = el.closest('.tl-panel');
        if (panel && panel.style.maxHeight !== '0') {
            setTimeout(function () { panel.style.maxHeight = panel.scrollHeight + 'px'; }, 50);
        }
    }

    var selectedWeek = null;
    var cetakType = 'jurnal';
    var catatanTaskIds = '';

    function openCetakModal(type, taskIds) {
        cetakType = type;
        catatanTaskIds = taskIds || '';
        document.getElementById('modalCetakJurnal').classList.remove('hidden');
    }

    function showPrintError(title, message, details) {
        document.getElementById('printErrorTitle').textContent = title || 'Data Tidak Tersedia';
        document.getElementById('printErrorMessage').textContent = message || 'Belum ada catatan kegiatan yang dapat dicetak.';
        var detailsContainer = document.getElementById('printErrorDetails');
        var detailsList = document.getElementById('printErrorDetailsList');
        if (details && details.length > 0) {
            detailsList.innerHTML = '';
            details.forEach(function (d) {
                var li = document.createElement('li');
                li.className = 'flex items-start gap-1.5';
                li.innerHTML = '<i class="fa-solid fa-circle-info text-gray-400 mt-0.5 flex-shrink-0" style="font-size:9px"></i><span>' + d + '</span>';
                detailsList.appendChild(li);
            });
            detailsContainer.classList.remove('hidden');
        } else {
            detailsContainer.classList.add('hidden');
        }
        document.getElementById('modalPrintError').classList.remove('hidden');
    }

    function printCetak(url, weekNum) {
        selectedWeek = weekNum;
        document.getElementById('modalCetakJurnal').classList.add('hidden');
        var iframe = document.getElementById('printFrame');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'printFrame';
            iframe.style.cssText = 'position:fixed;top:0;left:0;width:0;height:0;border:none;opacity:0';
            document.body.appendChild(iframe);
        }
        iframe.onload = function () {
            iframe.onload = null;
            setTimeout(function () {
                try {
                    var doc = iframe.contentDocument || iframe.contentWindow.document;
                    var errorMarker = doc.querySelector('.print-error-container[data-print-error]');
                    if (errorMarker) {
                        var titleEl = doc.querySelector('h2');
                        var messageEl = doc.querySelector('.text-gray-500');
                        var detailEls = doc.querySelectorAll('.text-gray-600 li span');
                        var details = [];
                        detailEls.forEach(function (el) { details.push(el.textContent.trim()); });
                        showPrintError(
                            titleEl ? titleEl.textContent.trim() : 'Data Tidak Tersedia',
                            messageEl ? messageEl.textContent.trim() : 'Belum ada catatan kegiatan yang dapat dicetak.',
                            details
                        );
                        return;
                    }
                } catch (e) { }

                var win = iframe.contentWindow;
                var done = false;
                function onDone() {
                    if (done) return;
                    done = true;
                    onPrintDialogClose();
                }
                try {
                    win.addEventListener('afterprint', onDone);
                } catch (e) { }
                var mql = win.matchMedia('print');
                if (mql.addEventListener) {
                    mql.addEventListener('change', function (e) {
                        if (!e.matches) onDone();
                    });
                } else if (mql.addListener) {
                    mql.addListener(function (e) {
                        if (!e.matches) onDone();
                    });
                }
                win.print();
            }, 300);
        };
        iframe.src = url;
    }

    function buildCetakUrl(weekNum, year) {
        var url;
        if (cetakType === 'catatan') {
            url = '<?= base_url('siswa/jurnal-pkl/cetak-catatan/') ?>' + catatanTaskIds + '/' + weekNum;
        } else {
            url = '<?= base_url('siswa/jurnal-pkl/cetak-jurnal/') ?>' + year + '/' + weekNum;
        }
        printCetak(url, weekNum);
    }

    function onPrintDialogClose() {
        var modal = document.getElementById('modalCetakJurnal');
        if (!modal) return;
        modal.classList.remove('hidden');
        if (selectedWeek) {
            var items = document.querySelectorAll('#weekList a');
            items.forEach(function (item) {
                item.classList.remove('border-primary', 'bg-primary/5');
                item.classList.add('border-gray-200');
            });
            var active = document.querySelector('#weekList a[data-week="' + selectedWeek + '"]');
            if (active) {
                active.classList.remove('border-gray-200');
                active.classList.add('border-primary', 'bg-primary/5');
                active.scrollIntoView({ block: 'center', behavior: 'smooth' });
            }
        }
    }

    // Week picker for cetak jurnal
    document.addEventListener('DOMContentLoaded', function () {
        var container = document.getElementById('weekList');
        if (!container) return;

        if (!PKL_START_DATE) {
            container.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">Belum ada pengaturan tanggal PKL</p>';
            return;
        }

        var start = new Date(PKL_START_DATE + 'T00:00:00');
        var today = new Date(CURRENT_DATE + 'T00:00:00');
        var end = PKL_END_DATE ? new Date(PKL_END_DATE + 'T00:00:00') : new Date(start);
        if (end < start) end = new Date(start);

        // Calculate weekBase (Monday of the week containing start date)
        var weekBase = new Date(start);
        var dow = weekBase.getDay();
        if (dow === 0) dow = 7;
        if (dow > 1) weekBase.setDate(weekBase.getDate() - (dow - 1));

        // Total days from weekBase to end
        var totalDays = Math.floor((end - weekBase) / (1000 * 60 * 60 * 24));
        var totalWeeks = Math.floor(totalDays / 7) + 1;

        var bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        var opts = { day: 'numeric', month: 'short' };

        var html = '<p class="text-xs text-gray-500 mb-2">Minggu ke-1: ' + start.toLocaleDateString('id-ID', opts) + ' – ...</p>';

        for (var w = 1; w <= totalWeeks; w++) {
            var wStart = new Date(weekBase);
            wStart.setDate(wStart.getDate() + (w - 1) * 7);
            var wEnd = new Date(wStart);
            wEnd.setDate(wEnd.getDate() + 6);

            // Clamp week 1 start to PKL start date
            if (w === 1 && wStart < start) wStart = new Date(start);
            // Clamp last week end to PKL end date (only when end date is explicitly set)
            if (w === totalWeeks && PKL_END_DATE && wEnd > end) wEnd = new Date(end);

            var isCurrentWeek = (today >= wStart && today <= wEnd);
            var labelStart = wStart.toLocaleDateString('id-ID', opts);
            var labelEnd = wEnd.toLocaleDateString('id-ID', opts);

            html += '<a href="javascript:void(0)" data-week="' + w + '" data-year="' + wStart.getFullYear() + '" ' +
                'class="block p-3 rounded-xl border transition-all week-item ' +
                (isCurrentWeek
                    ? 'border-primary bg-primary/5'
                    : 'border-gray-200') +
                '">' +
                '<div class="flex items-center justify-between">' +
                '<div>' +
                '<p class="text-sm font-semibold text-gray-800">Minggu ' + w + '</p>' +
                '<p class="text-xs text-gray-500">' + labelStart + ' – ' + labelEnd + '</p>' +
                '</div>' +
                '<div class="flex items-center gap-2">' +
                '<span class="week-status text-[10px] font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-400">Memuat...</span>' +
                (isCurrentWeek
                    ? '<span class="text-[10px] font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-full">Minggu Ini</span>'
                    : '<i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>') +
                '</div>' +
                '</div>' +
                '</a>';
        }

        container.innerHTML = html;

        // Fetch week readiness from backend
        fetch(WEEK_READINESS_URL)
            .then(function (r) { return r.json(); })
            .then(function (result) {
                if (!result.success) return;
                var weekData = result.data || {};

                for (var w = 1; w <= totalWeeks; w++) {
                    var item = container.querySelector('.week-item[data-week="' + w + '"]');
                    if (!item) continue;
                    var statusEl = item.querySelector('.week-status');
                    if (!statusEl) continue;

                    var data = weekData[w];
                    if (!data) {
                        statusEl.className = 'week-status text-[10px] font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-400';
                        statusEl.textContent = 'Tidak Ada Data';
                        item.classList.add('opacity-50', 'cursor-not-allowed');
                        item.classList.remove('hover:border-primary/50', 'hover:bg-gray-50');
                        item.removeAttribute('data-ready');
                        continue;
                    }

                    if (data.week_ready) {
                        statusEl.className = 'week-status text-[10px] font-bold px-2 py-0.5 rounded-full bg-green-100 text-green-700';
                        statusEl.textContent = 'Siap Cetak';
                        item.setAttribute('data-ready', 'true');
                        item.classList.add('cursor-pointer', 'hover:border-primary/50', 'hover:bg-gray-50');
                        item.classList.remove('opacity-50', 'cursor-not-allowed');
                        item.onclick = function (week, year) {
                            return function () { buildCetakUrl(week, year); };
                        }(w, item.getAttribute('data-year'));
                    } else {
                        var readyInfo = data.ready_days + '/' + data.total_workdays + ' hari';
                        statusEl.className = 'week-status text-[10px] font-medium px-2 py-0.5 rounded-full bg-red-100 text-red-600';
                        statusEl.textContent = readyInfo;
                        item.removeAttribute('data-ready');
                        item.classList.add('opacity-50', 'cursor-not-allowed');
                        item.classList.remove('cursor-pointer', 'hover:border-primary/50', 'hover:bg-gray-50');
                        item.onclick = null;
                        item.removeAttribute('href');
                    }
                }
            })
            .catch(function () {
                var statusEls = container.querySelectorAll('.week-status');
                statusEls.forEach(function (el) {
                    el.className = 'week-status text-[10px] font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-400';
                    el.textContent = 'Gagal Memuat';
                    var parent = el.closest('.week-item');
                    if (parent) {
                        parent.classList.add('opacity-50', 'cursor-not-allowed');
                        parent.classList.remove('hover:border-primary/50', 'hover:bg-gray-50');
                        parent.removeAttribute('data-ready');
                        parent.onclick = null;
                        parent.removeAttribute('href');
                    }
                });
            });
    });

    function openLightbox(src) {
        var lb = document.getElementById('lightbox');
        document.getElementById('lightboxImg').src = src;
        lb.classList.remove('hidden');
        lb.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox(e) {
        if (e && e.target !== document.getElementById('lightbox') && !e.target.classList.contains('fa-xmark') && e.target.id !== 'lightbox') return;
        var lb = document.getElementById('lightbox');
        lb.classList.add('hidden');
        lb.classList.remove('flex');
        document.body.style.overflow = '';
        document.getElementById('lightboxImg').src = '';
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLightbox({ target: document.getElementById('lightbox') });
    });

    /* ── Toast ── */
    function showToast(msg, type) {
        var c = document.getElementById('toastContainer');
        var t = document.createElement('div');
        t.className = 'toast toast-' + type;
        var icon = type === 'success' ? 'fa-circle-check' : type === 'error' ? 'fa-circle-xmark' : 'fa-circle-info';
        t.innerHTML = '<i class="fa-solid ' + icon + ' toast-icon"></i>' + msg;
        c.appendChild(t);
        setTimeout(function () {
            t.style.animation = 'toastOut 0.3s ease both';
            setTimeout(function () { t.remove(); }, 350);
        }, 3000);
    }

    /* ── Confirm Modal ── */
    function showConfirm(opts) {
        var o = document.getElementById('confirmOverlay');
        document.querySelector('.confirm-icon-wrap i').className = 'fa-solid ' + (opts.icon || 'fa-trash');
        document.querySelector('.confirm-icon-wrap').style.backgroundColor = opts.iconBg || '#fef2f2';
        document.querySelector('.confirm-icon-wrap i').style.color = opts.iconColor || '#ef4444';
        document.querySelector('.confirm-title').textContent = opts.title || 'Yakin?';
        document.querySelector('.confirm-desc').innerHTML = opts.desc || '';
        document.querySelector('.confirm-ok').innerHTML = opts.okLabel || '<i class="fa-solid fa-trash" style="margin-right:6px;"></i>Ya, Hapus!';
        document.querySelector('.confirm-ok').style.background = opts.okBg || '#ef4444';
        document.querySelector('.confirm-ok').style.boxShadow = opts.okShadow || '0 4px 12px rgba(239,68,68,0.3)';
        document.querySelector('.confirm-cancel').textContent = opts.cancelLabel || 'Batal';
        o.classList.add('show');
        if (opts.onOk) {
            document.getElementById('confirmOkBtn').onclick = function () { o.classList.remove('show'); opts.onOk(); };
        }
        document.getElementById('confirmCancelBtn').onclick = function () { o.classList.remove('show'); };
        o.onclick = function (e) { if (e.target === o) o.classList.remove('show'); };
    }

    function confirmHapus(btn) {
        var form = btn.closest('form');
        showConfirm({
            icon: 'fa-trash',
            title: 'Hapus Progress?',
            desc: 'Progress yang dihapus tidak dapat dikembalikan.',
            okLabel: '<i class="fa-solid fa-trash" style="margin-right:6px;"></i>Ya, Hapus!',
            onOk: function () { form.submit(); }
        });
    }
</script>

<!-- Toast Container -->
<div id="toastContainer"></div>

<!-- Confirm Modal -->
<div id="confirmOverlay">
    <div id="confirmBox">
        <div class="confirm-icon-wrap">
            <i class="fa-solid fa-trash"></i>
        </div>
        <p class="confirm-title">Yakin?</p>
        <p class="confirm-desc"></p>
        <div class="confirm-actions">
            <button class="confirm-cancel" id="confirmCancelBtn">Batal</button>
            <button class="confirm-ok" id="confirmOkBtn"><i class="fa-solid fa-trash" style="margin-right:6px;"></i>Ya,
                Hapus!</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>