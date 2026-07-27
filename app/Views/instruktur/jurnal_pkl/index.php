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

$formatIndoDate = function ($dateStr) use ($hariIndo, $bulanIndo) {
    if (!$dateStr)
        return '-';
    $dateObj = new DateTime($dateStr);
    $dayName = $hariIndo[$dateObj->format('l')] ?? $dateObj->format('l');
    $dateFormatted = $dateObj->format('j') . ' ' . $bulanIndo[(int) $dateObj->format('m')] . ' ' . $dateObj->format('Y');
    return $dayName . ', ' . $dateFormatted;
};

$totalStats = [
    'total_progress' => array_sum(array_column($siswaStats, 'total_progress')),
    'approved' => array_sum(array_column($siswaStats, 'approved')),
    'submitted' => array_sum(array_column($siswaStats, 'submitted')),
    'verified_by_instruktur' => array_sum(array_column($siswaStats, 'verified_by_instruktur')),
    'revision' => array_sum(array_column($siswaStats, 'revision')),
];
?>
<?= $this->extend(get_device_layout()) ?>

<?= $this->section('actions') ?>
<div class="hidden md:flex items-center gap-3 bg-gray-50 border border-gray-200 px-4 py-1.5 rounded-full shadow-sm">
    <div class="flex flex-col items-end border-r border-gray-200 pr-3">
        <span class="text-[10px] text-gray-500 uppercase font-bold"><?= $hariIndo[date('l')] ?? date('l') ?></span>
        <span class="text-xs font-semibold text-gray-800"><?= date('d') ?> <?= $bulanIndo[(int) date('m')] ?>
            <?= date('Y') ?></span>
    </div>
    <div class="flex gap-4 text-center">
        <div>
            <span class="block text-xs font-bold text-blue-600"><?= $totalStats['total_progress'] ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase tracking-wider">Total</span>
        </div>
        <div>
            <span class="block text-xs font-bold text-green-600"><?= $totalStats['approved'] ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase tracking-wider">Setuju</span>
        </div>
        <div>
            <span
                class="block text-xs font-bold text-yellow-600"><?= $totalStats['submitted'] + $totalStats['verified_by_instruktur'] ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase tracking-wider">Menunggu</span>
        </div>
        <div>
            <span class="block text-xs font-bold text-orange-600"><?= $totalStats['revision'] ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase tracking-wider">Revisi</span>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="h-full">
    <div class="flex items-start gap-4 mb-4 px-4 md:px-0">
        <a href="<?= base_url('instruktur/dashboard') ?>"
           class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 shadow-sm transition-all active:scale-95 flex-shrink-0 mt-0.5">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Review Jurnal PKL</h1>
            <p class="text-sm text-gray-500">Instruktur PKL</p>
        </div>
    </div>
    <?= view('components/alerts') ?>

    <?php if (empty($siswaList)): ?>
        <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-200">
            <div
                class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                <i class="fas fa-inbox text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-700">Belum Ada Siswa</h3>
            <p class="text-gray-500 mt-1">Belum ada siswa yang ditempatkan di tempat PKL Anda untuk periode aktif.</p>
        </div>
    <?php else: ?>

        <!-- Master-Detail Container -->
        <div id="master-detail-container"
            class="flex flex-col lg:flex-row gap-6 lg:h-[calc(100vh-12rem)] lg:overflow-hidden">

            <!-- Left Panel: Student List (Master) -->
            <div id="list-panel"
                class="w-full lg:w-80 bg-white rounded-2xl border border-gray-200 flex flex-col overflow-hidden shadow-sm flex-shrink-0 animate-fade-in">
                <!-- Search bar -->
                <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-search text-sm"></i>
                        </span>
                        <input type="text" id="searchInput" oninput="filterStudents()"
                            class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 placeholder:text-gray-400 transition-all shadow-sm"
                            placeholder="Cari siswa...">
                    </div>
                </div>

                <!-- Student list -->
                <div class="flex-grow overflow-y-auto divide-y divide-gray-100 custom-scrollbar">
                    <?php foreach ($siswaList as $s):
                        $stats = $siswaStats[$s['siswa_id']] ?? null;
                        $studentPending = array_values(array_filter($pendingProgress, fn($p) => $p['siswa_id'] == $s['siswa_id']));
                        $pendingCount = count($studentPending);
                        $totalProgress = $stats ? (int) $stats['total_progress'] : 0;
                        $approvedCount = $stats ? (int) $stats['approved'] : 0;
                        ?>
                        <button type="button" onclick="selectStudent(<?= $s['siswa_id'] ?>)"
                            id="student-btn-<?= $s['siswa_id'] ?>"
                            class="student-item w-full px-4 py-3.5 flex items-center gap-3 hover:bg-gray-50/85 transition-all text-left border-l-4 border-transparent"
                            data-name="<?= strtolower(esc($s['nama_lengkap'])) ?>" data-nis="<?= strtolower(esc($s['nis'])) ?>">

                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                <?php if (!empty($s['profile_photo'])): ?>
                                    <img src="<?= base_url('profile-photo/' . esc($s['profile_photo'])) ?>"
                                        class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm"
                                        alt="<?= esc($s['nama_lengkap']) ?>">
                                <?php else: ?>
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center font-bold text-sm shadow-sm">
                                        <?= strtoupper(substr(esc($s['nama_lengkap']), 0, 2)) ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Status dot -->
                                <?php if ($pendingCount > 0): ?>
                                    <span
                                        class="absolute bottom-0 right-0 w-3 h-3 bg-yellow-400 border-2 border-white rounded-full"></span>
                                <?php else: ?>
                                    <span
                                        class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                                <?php endif; ?>
                            </div>

                            <!-- Student details -->
                            <div class="flex-grow min-w-0">
                                <h4 class="font-semibold text-gray-800 text-sm truncate leading-snug">
                                    <?= esc($s['nama_lengkap']) ?>
                                </h4>
                                <p class="text-xs text-gray-500 truncate mt-0.5"><?= esc($s['nama_kelas'] ?? '-') ?></p>
                            </div>

                            <!-- Pending Badge / Stats -->
                            <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                <?php if ($pendingCount > 0): ?>
                                    <span
                                        class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full bg-yellow-100 text-yellow-800 text-[10px] font-bold">
                                        <?= $pendingCount ?>
                                    </span>
                                <?php endif; ?>
                                <span class="text-[10px] text-gray-400 font-medium">
                                    <?= $approvedCount ?>/<?= $totalProgress ?>
                                </span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 text-xs ml-1 flex-shrink-0"></i>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right Panel: Journal Content (Detail) -->
            <div id="detail-panel"
                class="flex-grow bg-white rounded-2xl border border-gray-200 flex flex-col overflow-hidden shadow-sm lg:h-full min-h-[400px]">

                <!-- Empty state (shown initially on desktop when no student is selected) -->
                <div id="empty-state"
                    class="flex-grow flex flex-col items-center justify-center p-12 text-center text-gray-500">
                    <div
                        class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100 shadow-inner">
                        <i class="fas fa-user-friends text-2xl text-gray-400 animate-pulse"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-700">Pilih Siswa</h3>
                    <p class="text-sm mt-1 text-gray-500">Pilih siswa di sebelah kiri untuk melihat dan mereview jurnal PKL
                    </p>
                </div>

                <!-- Student Detail Containers -->
                <?php foreach ($siswaList as $s):
                    $stats = $siswaStats[$s['siswa_id']] ?? null;
                    $totalProgress = $stats ? (int) $stats['total_progress'] : 0;
                    $approvedCount = $stats ? (int) $stats['approved'] : 0;
                    ?>
                    <div id="student-detail-<?= $s['siswa_id'] ?>"
                        class="student-detail-panel hidden flex flex-col h-full overflow-hidden">

                        <!-- Panel Header -->
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex-shrink-0">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex items-center gap-3.5">
                                    <!-- Back button for mobile -->
                                    <button type="button" onclick="backToList()"
                                        class="lg:hidden inline-flex items-center justify-center p-2.5 rounded-xl bg-white border border-gray-200 text-gray-600 hover:text-gray-900 shadow-sm transition-all hover:bg-gray-50 active:scale-95">
                                        <i class="fas fa-arrow-left"></i>
                                    </button>

                                    <!-- Avatar -->
                                    <?php if (!empty($s['profile_photo'])): ?>
                                        <img src="<?= base_url('profile-photo/' . esc($s['profile_photo'])) ?>"
                                            class="w-12 h-12 rounded-2xl object-cover border-2 border-white shadow-md"
                                            alt="<?= esc($s['nama_lengkap']) ?>">
                                    <?php else: ?>
                                        <div
                                            class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg border-2 border-white shadow-md">
                                            <?= strtoupper(substr(esc($s['nama_lengkap']), 0, 2)) ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="flex-grow min-w-0">
                                        <h3 class="text-base font-bold text-gray-900 leading-tight">
                                            <?= esc($s['nama_lengkap']) ?>
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-1 text-xs text-gray-500">
                                            <span class="font-medium text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded">NIS:
                                                <?= esc($s['nis']) ?></span>
                                            <span class="text-gray-300">&bull;</span>
                                            <span
                                                class="flex items-center gap-1 font-medium text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded"><i
                                                    class="fas fa-school text-[10px]"></i>
                                                <?= esc($s['nama_kelas'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="sm:flex-shrink-0">
                                    <div
                                        class="text-xs bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-xl font-semibold border border-indigo-100 flex items-center gap-1.5 shadow-sm">
                                        <i class="fas fa-check-circle"></i>
                                        Progress: <?= $approvedCount ?>/<?= $totalProgress ?> Disetujui
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Dropdowns -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Filter Minggu</label>
                                    <select id="weekFilter-<?= $s['siswa_id'] ?>"
                                        class="week-filter"
                                        data-siswa-id="<?= $s['siswa_id'] ?>">
                                        <option value="">-- Pilih Minggu --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Filter Aktivitas</label>
                                    <select id="taskFilter-<?= $s['siswa_id'] ?>"
                                        class="task-filter"
                                        disabled
                                        data-siswa-id="<?= $s['siswa_id'] ?>">
                                        <option value="">-- Pilih Aktivitas --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Content -->
                        <div class="flex-grow overflow-y-auto p-6 bg-gray-50/40 space-y-5 custom-scrollbar">
                            <div id="filter-placeholder-<?= $s['siswa_id'] ?>" class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center mb-3">
                                    <i class="fas fa-filter text-2xl"></i>
                                </div>
                                <h3 class="text-base font-semibold text-gray-700">Pilih Filter</h3>
                                <p class="text-sm text-gray-500 mt-1">Pilih minggu dan task untuk menampilkan data progress</p>
                            </div>
                            <div id="filter-loading-<?= $s['siswa_id'] ?>" class="hidden flex flex-col items-center justify-center py-12 text-center">
                                <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center mb-3">
                                    <i class="fas fa-spinner fa-spin text-2xl"></i>
                                </div>
                                <h3 class="text-base font-semibold text-gray-700">Memuat data...</h3>
                            </div>
                            <div id="filter-empty-<?= $s['siswa_id'] ?>" class="hidden flex flex-col items-center justify-center py-12 text-center">
                                <div class="w-16 h-16 rounded-full bg-yellow-50 text-yellow-500 flex items-center justify-center mb-3">
                                    <i class="fas fa-inbox text-2xl"></i>
                                </div>
                                <h3 class="text-base font-semibold text-gray-700">Tidak Ada Data</h3>
                                <p class="text-sm text-gray-500 mt-1">Tidak ada progress untuk filter yang dipilih</p>
                            </div>
                            <div id="filter-result-<?= $s['siswa_id'] ?>" class="hidden space-y-5"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    <?php endif; ?>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #E5E7EB;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #D1D5DB;
    }

    .student-item.active {
        background-color: #EEF2FF;
        border-color: #6366F1;
    }

    .student-item.active h4 {
        color: #4F46E5;
    }

    @keyframes lightboxIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
    #lightboxImg {
        animation: lightboxIn 0.25s ease-out;
    }
</style>

<!-- Lightbox -->
<div id="lightbox" class="fixed inset-0 z-[9999] bg-black/90 hidden items-center justify-center p-4"
    onclick="closeLightbox(event)">
    <button onclick="closeLightbox()"
        class="absolute top-4 right-4 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white text-lg transition-colors">
        <i class="fa-solid fa-xmark"></i>
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

    function saveActiveSiswa(siswaId) {
        localStorage.setItem('selected_siswa_id', siswaId);
    }

    function selectStudent(siswaId, forceShowDetailOnMobile = true) {
        localStorage.setItem('selected_siswa_id', siswaId);

        document.querySelectorAll('.student-item').forEach(function (item) {
            item.classList.remove('active');
        });
        var selectedBtn = document.getElementById('student-btn-' + siswaId);
        if (selectedBtn) selectedBtn.classList.add('active');

        var emptyState = document.getElementById('empty-state');
        if (emptyState) emptyState.classList.add('hidden');

        document.querySelectorAll('.student-detail-panel').forEach(function (panel) {
            panel.classList.add('hidden');
        });
        var activePanel = document.getElementById('student-detail-' + siswaId);
        if (activePanel) activePanel.classList.remove('hidden');

        if (window.innerWidth < 1024) {
            if (forceShowDetailOnMobile) {
                document.getElementById('list-panel').classList.add('hidden');
                document.getElementById('detail-panel').classList.remove('hidden');
            } else {
                document.getElementById('detail-panel').classList.add('hidden');
                document.getElementById('list-panel').classList.remove('hidden');
            }
        }

        var weekFilter = $('#weekFilter-' + siswaId);
        var taskFilter = $('#taskFilter-' + siswaId);

        if (weekFilter.length && !weekFilter.data('select2-loaded')) {
            weekFilter.data('select2-loaded', true);
            weekFilter.select2({
                placeholder: '-- Pilih Minggu --',
                width: '100%',
                dropdownAutoWidth: true
            });
            loadWeekInfo(siswaId);
        }

        if (taskFilter.length && !taskFilter.data('select2-loaded')) {
            taskFilter.data('select2-loaded', true);
            taskFilter.select2({
                placeholder: '-- Pilih Aktivitas --',
                width: '100%',
                dropdownAutoWidth: true
            });
        }
    }

    function backToList() {
        document.getElementById('list-panel').classList.remove('hidden');
        document.getElementById('detail-panel').classList.add('hidden');
    }

    function filterStudents() {
        var q = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('.student-item').forEach(function (card) {
            var name = card.getAttribute('data-name');
            var nis = card.getAttribute('data-nis');
            if (name.includes(q) || nis.includes(q)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var savedSiswaId = localStorage.getItem('selected_siswa_id');

        if (window.innerWidth < 1024) {
            document.getElementById('detail-panel').classList.add('hidden');
            document.getElementById('list-panel').classList.remove('hidden');
        }

        if (savedSiswaId) {
            var targetBtn = document.getElementById('student-btn-' + savedSiswaId);
            if (targetBtn && targetBtn.style.display !== 'none') {
                selectStudent(savedSiswaId, false);
                return;
            }
        }

        if (window.innerWidth >= 1024) {
            var firstBtn = document.querySelector('.student-item');
            if (firstBtn) {
                var id = firstBtn.id.replace('student-btn-', '');
                selectStudent(id);
            }
        }
    });
</script>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    var BASE_URL = '<?= base_url() ?>';
    var CSRF = '<?= csrf_token() ?>';

    function formatIndoDate(dateStr) {
        if (!dateStr) return '-';
        var days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        var months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        var d = new Date(dateStr);
        return days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }

    function getStatusBadge(status) {
        var badges = {
            'approved': {bg: 'bg-green-50 text-green-700 border-green-200', label: 'Disetujui', icon: 'fa-check-circle'},
            'verified_by_instruktur': {bg: 'bg-indigo-50 text-indigo-700 border-indigo-200', label: 'Verified', icon: 'fa-check-double'},
            'submitted': {bg: 'bg-yellow-50 text-yellow-700 border-yellow-200', label: 'Menunggu', icon: 'fa-clock'},
            'revision': {bg: 'bg-orange-50 text-orange-700 border-orange-200', label: 'Revisi', icon: 'fa-edit'}
        };
        return badges[status] || {bg: 'bg-gray-50 text-gray-600 border-gray-200', label: 'Draft', icon: 'fa-pen'};
    }

    function renderProgressCard(p, siswaId) {
        var badge = getStatusBadge(p.status);
        var langkahKerja = [];
        if (p.langkah_kerja) {
            try {
                var decoded = JSON.parse(p.langkah_kerja);
                if (Array.isArray(decoded)) {
                    langkahKerja = decoded.filter(function(v) { return v.trim() !== ''; });
                }
            } catch(e) {}
        }

        var langkahHtml = '';
        if (langkahKerja.length > 0) {
            var stepsHtml = langkahKerja.map(function(step) {
                return '<li class="ml-5"><div class="absolute -left-[5px] mt-1.5 w-2.5 h-2.5 rounded-full bg-indigo-500"></div><p class="text-sm text-gray-700 leading-relaxed">' + step.replace(/</g, '&lt;') + '</p></li>';
            }).join('');
            langkahHtml = '<div class="pt-4 md:pt-5 border-t border-gray-100"><div class="bg-gray-50/50 border border-gray-200 rounded-xl p-3.5"><div class="flex items-center gap-2.5 mb-4"><i class="fas fa-list-ol text-indigo-500"></i><span class="text-sm font-semibold text-gray-700">Perencanaan dan Persiapan Kerja</span><span class="text-xs text-gray-500 font-medium bg-gray-200/60 px-1.5 py-0.5 rounded-full">(' + langkahKerja.length + ')</span></div><div class="pl-1"><ol class="relative border-l border-gray-200 ml-2 space-y-4">' + stepsHtml + '</ol></div></div></div>';
        }

        var photoHtml = '';
        if (p.foto) {
            photoHtml = '<div class="pt-4 md:pt-5 border-t border-gray-100"><h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2 mb-3"><i class="far fa-image text-gray-400"></i> Dokumentasi</h4><a href="' + BASE_URL + '/files/pkl-progress/' + p.foto + '" onclick="openLightbox(this.href); return false;" class="group relative inline-block overflow-hidden rounded-xl border border-gray-200 hover:shadow-md transition-shadow w-full md:w-auto cursor-pointer"><img src="' + BASE_URL + '/files/pkl-progress/' + p.foto + '" class="w-full h-48 md:h-40 md:w-auto object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy" alt="Dokumentasi"><div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-1.5"><i class="fas fa-search-plus"></i> Lihat Foto</div></a></div>';
        }

        var catatanHtml = '';
        if (p.catatan_instruktur || p.catatan_pembimbing) {
            var innerCat = '';
            if (p.catatan_instruktur) {
                innerCat += '<div class="bg-indigo-50 border-l-4 border-indigo-500 rounded-r-xl p-3.5 shadow-sm"><p class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider flex items-center gap-1"><i class="fas fa-building text-[8px]"></i> Catatan Instruktur</p><p class="text-xs text-indigo-900 mt-1 leading-relaxed">' + (p.catatan_instruktur || '').replace(/</g, '&lt;') + '</p></div>';
            }
            if (p.catatan_pembimbing) {
                innerCat += '<div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl p-3.5 shadow-sm"><p class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider flex items-center gap-1"><i class="fas fa-user-tie text-[8px]"></i> Catatan Pembimbing</p><p class="text-xs text-emerald-900 mt-1 leading-relaxed">' + (p.catatan_pembimbing || '').replace(/</g, '&lt;') + '</p></div>';
            }
            catatanHtml = '<div class="pt-4 md:pt-5 border-t border-gray-100 space-y-3.5"><h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2"><i class="far fa-comment-dots text-gray-400"></i> Catatan Jurnal</h4><div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">' + innerCat + '</div></div>';
        }

        var actionHtml = '';
        if (p.status === 'submitted') {
            actionHtml = '<form action="' + BASE_URL + '/instruktur/jurnal-pkl/verifikasi-progress/' + p.id + '" method="POST" class="space-y-3"><input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>"><div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5 shadow-inner"><label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Catatan Instruktur <span class="text-red-500">*</span></label><textarea name="catatan_instruktur" rows="2" required maxlength="200" oninput="updateCharCount(this)" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all resize-none shadow-sm" placeholder="Tulis catatan revisi atau catatan persetujuan..."></textarea><div class="text-right text-[10px] text-gray-400 char-count">0/200 karakter</div><div class="flex flex-col sm:flex-row justify-end gap-2.5 mt-3"><button type="submit" name="status" value="revision" onclick="return confirm(\'Minta revisi progress ini?\')" class="w-full sm:w-auto px-4 py-2 border border-orange-200 text-orange-700 font-bold text-xs hover:bg-orange-50 hover:border-orange-300 bg-white rounded-xl shadow-sm transition-all active:scale-95 flex items-center justify-center gap-1.5"><i class="fas fa-edit text-[10px]"></i> Minta Revisi</button><button type="submit" name="status" value="verified_by_instruktur" onclick="return confirm(\'Setujui progress ini?\')" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 rounded-xl shadow-sm transition-all active:scale-95 flex items-center justify-center gap-1.5"><i class="fas fa-check text-[10px]"></i> Setujui</button></div></div></form>';
        } else if (p.status === 'verified_by_instruktur' || p.status === 'revision') {
            var labelText = p.status === 'verified_by_instruktur' ? 'diverifikasi' : 'direvisi';
            var catatan = p.status === 'verified_by_instruktur' ? (p.catatan_instruktur || '') : (p.catatan_instruktur || '');
            actionHtml = '<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-indigo-50/40 border border-indigo-100 rounded-xl p-3.5 pl-4 shadow-inner"><div class="flex items-center gap-2.5 text-xs text-indigo-800"><i class="fas fa-check-circle text-base text-indigo-500 flex-shrink-0"></i><div class="min-w-0"><span class="font-bold">Progress ' + labelText + '</span>' + (catatan ? '<p class="text-indigo-700 mt-0.5 italic break-words">"' + catatan.replace(/</g, '&lt;') + '"</p>' : '') + '</div></div><form action="' + BASE_URL + '/instruktur/jurnal-pkl/batal-verifikasi-progress/' + p.id + '" method="POST" onsubmit="saveActiveSiswa(' + siswaId + ')" class="w-full sm:w-auto"><input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>"><button type="submit" onclick="return confirm(\'Batalkan verifikasi progress ini?\')" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white border border-orange-200 text-orange-700 font-semibold rounded-xl hover:bg-orange-50 hover:border-orange-300 text-xs shadow-sm transition-all active:scale-95 whitespace-nowrap"><i class="fas fa-undo text-[10px]"></i> Batalkan</button></form></div>';
        } else if (p.status === 'approved') {
            if (p.instruktur_verified_by) {
                actionHtml = '<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-green-50/40 border border-green-100 rounded-xl p-3.5 pl-4 shadow-inner"><div class="flex items-center gap-2.5 text-xs text-green-800"><i class="fas fa-check-circle text-base text-green-500 flex-shrink-0"></i><div class="min-w-0"><span class="font-bold">Disetujui Pembimbing</span>' + (p.catatan_instruktur ? '<p class="text-green-700 mt-0.5 italic break-words">"' + p.catatan_instruktur.replace(/</g, '&lt;') + '"</p>' : '') + '</div></div><form action="' + BASE_URL + '/instruktur/jurnal-pkl/batal-verifikasi-progress/' + p.id + '" method="POST" onsubmit="saveActiveSiswa(' + siswaId + ')" class="w-full sm:w-auto"><input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>"><button type="submit" onclick="return confirm(\'Batalkan verifikasi progress ini?\')" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white border border-orange-200 text-orange-700 font-semibold rounded-xl hover:bg-orange-50 hover:border-orange-300 text-xs shadow-sm transition-all active:scale-95 whitespace-nowrap"><i class="fas fa-undo text-[10px]"></i> Batalkan</button></form></div>';
            } else {
                actionHtml = '<div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5 shadow-inner"><p class="text-[10px] font-bold text-green-600 uppercase tracking-wider mb-1.5 flex items-center gap-1"><i class="fas fa-check-circle text-[9px]"></i> Disetujui Pembimbing — Verifikasi Instruktur</p><form action="' + BASE_URL + '/instruktur/jurnal-pkl/verifikasi-progress/' + p.id + '" method="POST"><input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>"><textarea name="catatan_instruktur" rows="2" required maxlength="200" oninput="updateCharCount(this)" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all resize-none shadow-sm" placeholder="Tulis catatan revisi atau catatan persetujuan..."></textarea><div class="text-right text-[10px] text-gray-400 char-count">0/200 karakter</div><div class="flex flex-col sm:flex-row justify-end gap-2.5 mt-3"><button type="submit" name="status" value="revision" onclick="return confirm(\'Minta revisi progress ini?\')" class="w-full sm:w-auto px-4 py-2 border border-orange-200 text-orange-700 font-bold text-xs hover:bg-orange-50 hover:border-orange-300 bg-white rounded-xl shadow-sm transition-all active:scale-95 flex items-center justify-center gap-1.5"><i class="fas fa-edit text-[10px]"></i> Minta Revisi</button><button type="submit" name="status" value="verified_by_instruktur" onclick="return confirm(\'Setujui progress ini?\')" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 rounded-xl shadow-sm transition-all active:scale-95 flex items-center justify-center gap-1.5"><i class="fas fa-check text-[10]"></i> Verifikasi</button></div></form></div>';
            }
        }

        var langkahCount = langkahKerja.length > 0 ? '<span class="text-gray-300">&bull;</span><span>' + langkahKerja.length + ' langkah kerja</span>' : '';

        return '<div class="bg-white rounded-2xl border border-gray-200 p-4 md:p-5 shadow-sm space-y-4 md:space-y-6 hover:shadow-md transition-all duration-200">' +
            '<div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 sm:gap-4"><div class="min-w-0"><h3 class="text-base font-semibold text-gray-900 leading-snug">' + (p.task_judul || '').replace(/</g, '&lt;') + '</h3><div class="mt-1.5 flex flex-wrap items-center gap-2 text-xs md:text-sm text-gray-500"><span class="flex items-center gap-1"><i class="far fa-calendar"></i> ' + formatIndoDate(p.tanggal) + '</span>' + langkahCount + '</div></div><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border self-start sm:self-auto flex-shrink-0 ' + badge.bg + '"><i class="fas ' + badge.icon + ' text-[10px]"></i> ' + badge.label + '</span></div>' +
            langkahHtml +
            '<div class="pt-4 md:pt-5 border-t border-gray-100"><div class="rounded-xl bg-gray-50 border border-gray-200 p-4"><h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2 mb-2"><i class="far fa-file-lines text-gray-400"></i> Deskripsi Pekerjaan</h4><p class="text-sm leading-7 text-gray-600 whitespace-pre-wrap">' + (p.deskripsi || '').replace(/</g, '&lt;') + '</p></div></div>' +
            photoHtml + catatanHtml +
            '<div class="pt-4 md:pt-5 border-t border-gray-100">' + actionHtml + '</div></div>';
    }

    function loadFilteredProgress(siswaId) {
        var weekVal = $('#weekFilter-' + siswaId).val();
        var taskVal = $('#taskFilter-' + siswaId).val();

        var placeholder = $('#filter-placeholder-' + siswaId);
        var loading = $('#filter-loading-' + siswaId);
        var empty = $('#filter-empty-' + siswaId);
        var result = $('#filter-result-' + siswaId);

        if (!weekVal || !taskVal) {
            placeholder.removeClass('hidden');
            loading.addClass('hidden');
            empty.addClass('hidden');
            result.addClass('hidden').html('');
            return;
        }

        placeholder.addClass('hidden');
        loading.removeClass('hidden');
        empty.addClass('hidden');
        result.addClass('hidden').html('');

        $.ajax({
            url: BASE_URL + '/instruktur/jurnal-pkl/filtered-progress/' + siswaId,
            type: 'GET',
            data: { week: weekVal, task_id: taskVal },
            dataType: 'json',
            success: function(response) {
                loading.addClass('hidden');
                if (response.success && response.data.length > 0) {
                    var html = '<h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-2 mb-2"><i class="fas fa-calendar-day text-xs"></i> Riwayat Progress Harian</h4>';
                    response.data.forEach(function(p) {
                        html += renderProgressCard(p, siswaId);
                    });
                    result.html(html).removeClass('hidden');
                } else {
                    empty.removeClass('hidden');
                }
            },
            error: function() {
                loading.addClass('hidden');
                empty.removeClass('hidden');
            }
        });
    }

    function loadTasks(siswaId, week) {
        var taskFilter = $('#taskFilter-' + siswaId);

        // Show loading state
        if (taskFilter.data('select2')) {
            taskFilter.select2('destroy');
        }
        taskFilter.html('<option value="">Memuat task...</option>').prop('disabled', true);
        taskFilter.select2({
            placeholder: 'Memuat task...',
            width: '100%',
            dropdownAutoWidth: true,
            minimumResultsForSearch: -1
        });

        $.ajax({
            url: BASE_URL + '/instruktur/jurnal-pkl/tasks/' + siswaId,
            type: 'GET',
            data: { week: week },
            dataType: 'json',
            success: function(response) {
                // Destroy loading state
                taskFilter.select2('destroy');
                taskFilter.html('<option value="">-- Pilih Aktivitas --</option>');

                if (response.success && response.data.length > 0) {
                    response.data.forEach(function(task) {
                        var label = task.judul;
                        if (task.tanggal) {
                            label = formatIndoDate(task.tanggal) + ' - ' + task.judul;
                        }
                        taskFilter.append('<option value="' + task.id + '">' + label + '</option>');
                    });
                }

                taskFilter.prop('disabled', false);
                taskFilter.select2({
                    placeholder: '-- Pilih Aktivitas --',
                    width: '100%',
                    dropdownAutoWidth: true
                });

                // Restore saved task filter
                var savedTask = localStorage.getItem('filter_task_' + siswaId);
                if (savedTask && taskFilter.find('option[value="' + savedTask + '"]').length) {
                    taskFilter.val(savedTask).trigger('change');
                    loadFilteredProgress(siswaId);
                }
            },
            error: function() {
                taskFilter.select2('destroy');
                taskFilter.html('<option value="">-- Pilih Aktivitas --</option>').prop('disabled', true);
                taskFilter.select2({
                    placeholder: '-- Pilih Aktivitas --',
                    width: '100%',
                    dropdownAutoWidth: true,
                    minimumResultsForSearch: -1
                });
            }
        });
    }

    function loadWeekInfo(siswaId) {
        $.ajax({
            url: BASE_URL + '/instruktur/jurnal-pkl/week-info',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    var weekFilter = $('#weekFilter-' + siswaId);
                    response.data.forEach(function(week) {
                        weekFilter.append('<option value="' + week.week + '">' + week.label + '</option>');
                    });
                    weekFilter.select2({
                        placeholder: '-- Pilih Minggu --',
                        width: '100%',
                        dropdownAutoWidth: true
                    });

                    // Restore saved week filter
                    var savedWeek = localStorage.getItem('filter_week_' + siswaId);
                    if (savedWeek && weekFilter.find('option[value="' + savedWeek + '"]').length) {
                        weekFilter.val(savedWeek).trigger('change');
                        loadTasks(siswaId, savedWeek);
                    }
                }
            }
        });
    }

    $(document).ready(function() {
        $('.week-filter').on('change', function() {
            var siswaId = $(this).data('siswa-id');
            var taskFilter = $('#taskFilter-' + siswaId);
            var weekVal = $(this).val();

            // Save week filter to localStorage
            if (weekVal) {
                var prevWeek = localStorage.getItem('filter_week_' + siswaId);
                if (prevWeek !== weekVal) {
                    localStorage.removeItem('filter_task_' + siswaId);
                }
                localStorage.setItem('filter_week_' + siswaId, weekVal);
            } else {
                localStorage.removeItem('filter_week_' + siswaId);
                localStorage.removeItem('filter_task_' + siswaId);
            }

            if (weekVal) {
                loadTasks(siswaId, weekVal);
            } else {
                // Reset task filter
                if (taskFilter.data('select2')) {
                    taskFilter.select2('destroy');
                }
                taskFilter.html('<option value="">-- Pilih Aktivitas --</option>').prop('disabled', true);
                taskFilter.select2({
                    placeholder: '-- Pilih Aktivitas --',
                    width: '100%',
                    dropdownAutoWidth: true
                });
            }

            $('#filter-placeholder-' + siswaId).removeClass('hidden');
            $('#filter-loading-' + siswaId).addClass('hidden');
            $('#filter-empty-' + siswaId).addClass('hidden');
            $('#filter-result-' + siswaId).addClass('hidden').html('');
        });

        $('.task-filter').on('change', function() {
            var siswaId = $(this).data('siswa-id');
            var taskVal = $(this).val();

            // Save task filter to localStorage
            if (taskVal) {
                localStorage.setItem('filter_task_' + siswaId, taskVal);
            } else {
                localStorage.removeItem('filter_task_' + siswaId);
            }

            loadFilteredProgress(siswaId);
        });
    });

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
                counter.className = 'text-right text-[10px] font-bold text-red-500 char-count';
            } else if (len >= 160) {
                counter.className = 'text-right text-[10px] text-orange-500 char-count';
            } else {
                counter.className = 'text-right text-[10px] text-gray-400 char-count';
            }
        }
    }
</script>
<?= $this->endSection() ?>