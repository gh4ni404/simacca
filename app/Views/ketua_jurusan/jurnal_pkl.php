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
    if (!$dateStr) return '-';
    $dateObj = new DateTime($dateStr);
    $dayName = $hariIndo[$dateObj->format('l')] ?? $dateObj->format('l');
    $dateFormatted = $dateObj->format('j') . ' ' . $bulanIndo[(int) $dateObj->format('m')] . ' ' . $dateObj->format('Y');
    return $dayName . ', ' . $dateFormatted;
};
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
            <span class="block text-xs font-bold text-blue-600"><?= $stats['total_progress'] ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase tracking-wider">Total</span>
        </div>
        <div>
            <span class="block text-xs font-bold text-green-600"><?= $stats['approved'] ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase tracking-wider">Setuju</span>
        </div>
        <div>
            <span
                class="block text-xs font-bold text-yellow-600"><?= ($stats['submitted'] ?? 0) + ($stats['verified_by_instruktur'] ?? 0) ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase tracking-wider">Menunggu</span>
        </div>
        <div>
            <span class="block text-xs font-bold text-orange-600"><?= $stats['revision'] ?></span>
            <span class="block text-[8px] text-gray-500 font-medium uppercase tracking-wider">Revisi</span>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="h-full">
    <div class="flex items-start gap-4 mb-4 px-4 md:px-0">
        <a href="<?= base_url('ketua-jurusan/dashboard') ?>"
           class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 shadow-sm transition-all active:scale-95 flex-shrink-0 mt-0.5">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Monitoring Jurnal PKL</h1>
            <p class="text-sm text-gray-500">Jurusan <?= esc($jurusan) ?> — Semua jurnal/progress siswa PKL</p>
        </div>
    </div>
    <?= render_flash_message() ?>

    <?php if (empty($grouped)): ?>
        <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-200 mx-4 md:mx-0">
            <div
                class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                <i class="fas fa-inbox text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-700">Belum Ada Jurnal</h3>
            <p class="text-gray-500 mt-1">Tidak ada jurnal PKL untuk filter yang dipilih</p>
        </div>
    <?php else: ?>

        <!-- Master-Detail Container -->
        <div id="master-detail-container"
            class="flex flex-col lg:flex-row gap-6 lg:h-[calc(100vh-12rem)] lg:overflow-hidden mx-4 md:mx-0">

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
                    <?php foreach ($grouped as $student):
                        $totalProgress = count($student['progress']);
                        $approvedCount = 0;
                        foreach ($student['progress'] as $p) {
                            if ($p['status'] === 'approved') $approvedCount++;
                        }
                        $pendingCount = $student['pending_count'] ?? 0;
                        ?>
                        <button type="button" onclick="selectStudent(<?= $student['siswa_id'] ?>)"
                            id="student-btn-<?= $student['siswa_id'] ?>"
                            class="student-item w-full px-4 py-3.5 flex items-center gap-3 hover:bg-gray-50/85 transition-all text-left border-l-4 border-transparent"
                            data-name="<?= strtolower(esc($student['nama_siswa'])) ?>"
                            data-nis="<?= strtolower(esc($student['nis'])) ?>">

                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                <?php if (!empty($student['profile_photo'])): ?>
                                    <img src="<?= base_url('profile-photo/' . esc($student['profile_photo'])) ?>"
                                        class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm"
                                        alt="<?= esc($student['nama_siswa']) ?>">
                                <?php else: ?>
                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold text-sm shadow-sm">
                                        <?= strtoupper(substr(esc($student['nama_siswa']), 0, 2)) ?>
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
                                    <?= esc($student['nama_siswa']) ?>
                                </h4>
                                <p class="text-xs text-gray-500 truncate mt-0.5"><?= esc($student['nis']) ?> — <?= esc($student['nama_kelas']) ?></p>
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
                    <p class="text-sm mt-1 text-gray-500">Pilih siswa di sebelah kiri untuk melihat jurnal PKL</p>
                </div>

                <!-- Student Detail Containers -->
                <?php foreach ($grouped as $student):
                    $totalProgress = count($student['progress']);
                    $approvedCount = 0;
                    foreach ($student['progress'] as $p) {
                        if ($p['status'] === 'approved') $approvedCount++;
                    }
                    $persenLengkap = $totalProgress > 0 ? round(($approvedCount / $totalProgress) * 100) : 0;
                    ?>
                    <div id="student-detail-<?= $student['siswa_id'] ?>"
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
                                    <?php if (!empty($student['profile_photo'])): ?>
                                        <img src="<?= base_url('profile-photo/' . esc($student['profile_photo'])) ?>"
                                            class="w-12 h-12 rounded-2xl object-cover border-2 border-white shadow-md"
                                            alt="<?= esc($student['nama_siswa']) ?>">
                                    <?php else: ?>
                                        <div
                                            class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg border-2 border-white shadow-md">
                                            <?= strtoupper(substr(esc($student['nama_siswa']), 0, 2)) ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="flex-grow min-w-0">
                                        <h3 class="text-base font-bold text-gray-900 leading-tight">
                                            <?= esc($student['nama_siswa']) ?>
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-1 text-xs text-gray-500">
                                            <span class="font-medium text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded">NIS:
                                                <?= esc($student['nis']) ?></span>
                                            <span class="text-gray-300">&bull;</span>
                                            <span
                                                class="flex items-center gap-1 font-medium text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded"><i
                                                    class="fas fa-school text-[10px]"></i> <?= esc($student['nama_kelas']) ?></span>
                                            <?php if (!empty($student['nama_perusahaan'])): ?>
                                                <span class="text-gray-300">&bull;</span>
                                                <span class="flex items-center gap-1 text-gray-600"><i
                                                        class="fas fa-building text-[10px] text-gray-400"></i>
                                                    <?= esc($student['nama_perusahaan']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="sm:flex-shrink-0">
                                    <a href="<?= base_url('ketua-jurusan/jurnal-pkl/detail/' . $student['siswa_id']) ?>"
                                        class="inline-flex items-center gap-1.5 text-xs bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-xl font-semibold border border-indigo-100 shadow-sm hover:bg-indigo-100 transition-all">
                                        <i class="fas fa-arrow-right"></i>
                                        Detail
                                    </a>
                                </div>
                            </div>

                            <!-- Progress summary bar -->
                            <div class="mt-4 flex items-center gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                        <span>Progress: <?= $approvedCount ?>/<?= $totalProgress ?> Disetujui</span>
                                        <span class="font-semibold text-indigo-600"><?= $persenLengkap ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-green-500 h-1.5 rounded-full" style="width: <?= $persenLengkap ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Content: Scrollable list of progress entries -->
                        <div class="flex-grow overflow-y-auto p-6 bg-gray-50/40 space-y-2 custom-scrollbar">
                            <?php if (empty($student['progress'])): ?>
                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                    <div class="w-16 h-16 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center mb-3 border border-gray-100">
                                        <i class="fas fa-inbox text-2xl"></i>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-700">Belum Ada Progress</h3>
                                    <p class="text-sm text-gray-500 mt-1">Siswa belum mengirim progress jurnal</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($student['progress'] as $prog):
                                    $statusBadge = match($prog['status']) {
                                        'approved' => 'bg-green-100 text-green-700',
                                        'submitted' => 'bg-yellow-100 text-yellow-700',
                                        'revision' => 'bg-red-100 text-red-700',
                                        'verified_by_instruktur' => 'bg-indigo-100 text-indigo-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                    $statusLabel = match($prog['status']) {
                                        'approved' => 'Disetujui',
                                        'submitted' => 'Menunggu',
                                        'revision' => 'Revisi',
                                        'verified_by_instruktur' => 'Verified',
                                        default => ucfirst($prog['status']),
                                    };
                                ?>
                                <button type="button" onclick="showProgressDetail(this)"
                                    data-nama="<?= esc($prog['nama_task']) ?>"
                                    data-tanggal="<?= $prog['tanggal'] ?>"
                                    data-tanggal-display="<?= $formatIndoDate($prog['tanggal']) ?>"
                                    data-status="<?= $prog['status'] ?>"
                                    data-status-label="<?= $statusLabel ?>"
                                    data-deskripsi="<?= esc($prog['deskripsi'] ?? '') ?>"
                                    data-langkah="<?= esc($prog['langkah_kerja'] ?? '') ?>"
                                    data-kategori="<?= esc($prog['kategori_nama'] ?? '') ?>"
                                    data-catatan-instruktur="<?= esc($prog['catatan_instruktur'] ?? '') ?>"
                                    data-catatan-pembimbing="<?= esc($prog['catatan_pembimbing'] ?? '') ?>"
                                    data-foto="<?= esc($prog['foto'] ?? '') ?>"
                                    class="w-full text-left bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3 hover:shadow-md hover:border-indigo-200 transition-all duration-200 cursor-pointer group">
                                    <div class="w-2 h-2 rounded-full flex-shrink-0 <?= match($prog['status']) {
                                        'approved' => 'bg-green-500',
                                        'submitted' => 'bg-yellow-500',
                                        'revision' => 'bg-red-500',
                                        'verified_by_instruktur' => 'bg-indigo-500',
                                        default => 'bg-gray-400',
                                    } ?>"></div>
                                    <div class="flex-grow min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate group-hover:text-indigo-600 transition-colors"><?= esc($prog['nama_task']) ?></p>
                                        <p class="text-xs text-gray-400 mt-0.5"><?= $formatIndoDate($prog['tanggal']) ?></p>
                                    </div>
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full flex-shrink-0 <?= $statusBadge ?>"><?= $statusLabel ?></span>
                                    <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-indigo-400 transition-colors flex-shrink-0"></i>
                                </button>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    function showProgressDetail(el) {
        var nama = el.getAttribute('data-nama');
        var tanggalRaw = el.getAttribute('data-tanggal');
        var tanggal = el.getAttribute('data-tanggal-display');
        var status = el.getAttribute('data-status');
        var statusLabel = el.getAttribute('data-status-label');
        var deskripsi = el.getAttribute('data-deskripsi');
        var langkah = el.getAttribute('data-langkah');
        var kategori = el.getAttribute('data-kategori');
        var catatanInstruktur = el.getAttribute('data-catatan-instruktur');
        var catatanPembimbing = el.getAttribute('data-catatan-pembimbing');
        var foto = el.getAttribute('data-foto');
        var BASE_URL = '<?= base_url() ?>';

        var badges = {
            'approved': {bg: 'bg-green-50 text-green-700 border border-green-200', icon: 'fa-check-circle'},
            'verified_by_instruktur': {bg: 'bg-indigo-50 text-indigo-700 border border-indigo-200', icon: 'fa-check-double'},
            'submitted': {bg: 'bg-yellow-50 text-yellow-700 border border-yellow-200', icon: 'fa-clock'},
            'revision': {bg: 'bg-orange-50 text-orange-700 border border-orange-200', icon: 'fa-edit'}
        };
        var badge = badges[status] || {bg: 'bg-gray-50 text-gray-600 border border-gray-200', icon: 'fa-pen'};

        var html = '<div class="text-left swal2-html-container" style="margin:0;padding:0;text-align:left;">';

        // Header: title + meta + badge
        html += '<div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 sm:gap-4 mb-4">';
        html += '<div class="min-w-0"><h3 style="font-size:1rem;font-weight:600;color:#111827;line-height:1.4;margin:0">' + nama.replace(/</g, '&lt;') + '</h3>';
        html += '<div style="margin-top:6px;display:flex;flex-wrap:wrap;align-items:center;gap:8px;font-size:0.8rem;color:#6b7280">';
        if (kategori) {
            html += '<span style="font-weight:500;color:#374151;background:#f3f4f6;padding:2px 6px;border-radius:4px">' + kategori.replace(/</g, '&lt;') + '</span>';
            html += '<span style="color:#d1d5db">&bull;</span>';
        }
        html += '<span style="display:flex;align-items:center;gap:4px"><i class="far fa-calendar"></i> ' + tanggal + '</span>';
        html += '</div></div>';
        html += '<span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:9999px;font-size:0.75rem;font-weight:600;flex-shrink:0;white-space:nowrap" class="' + badge.bg + '"><i class="fas ' + badge.icon + '" style="font-size:10px"></i> ' + statusLabel + '</span>';
        html += '</div>';

        // Langkah kerja
        if (langkah) {
            try {
                var decoded = JSON.parse(langkah);
                if (Array.isArray(decoded)) {
                    var validSteps = decoded.filter(function(v) { return v.trim() !== ''; });
                    if (validSteps.length > 0) {
                        html += '<div style="padding-top:16px;border-top:1px solid #f3f4f6">';
                        html += '<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:14px">';
                        html += '<div style="display:flex;align-items:center;gap:10px;margin-bottom:16px"><i class="fas fa-list-ol" style="color:#6366f1"></i><span style="font-size:0.875rem;font-weight:600;color:#374151">Perencanaan dan Persiapan Kerja</span><span style="font-size:0.75rem;color:#6b7280;font-weight:500;background:rgba(229,231,235,0.6);padding:2px 6px;border-radius:9999px">(' + validSteps.length + ')</span></div>';
                        html += '<ol style="position:relative;border-left:2px solid #e5e7eb;margin-left:8px;padding-left:20px">';
                        validSteps.forEach(function(step) {
                            html += '<li style="position:relative;padding-bottom:16px"><div style="position:absolute;left:-25px;top:5px;width:10px;height:10px;border-radius:50%;background:#6366f1"></div><p style="font-size:0.875rem;color:#374151;line-height:1.6;margin:0">' + step.replace(/</g, '&lt;') + '</p></li>';
                        });
                        html += '</ol></div></div>';
                    }
                }
            } catch(e) {}
        }

        // Deskripsi
        html += '<div style="padding-top:16px;border-top:1px solid #f3f4f6">';
        html += '<div style="border-radius:12px;background:#f9fafb;border:1px solid #e5e7eb;padding:16px">';
        html += '<h4 style="font-size:0.875rem;font-weight:600;color:#374151;display:flex;align-items:center;gap:8px;margin:0 0 8px 0"><i class="far fa-file-lines" style="color:#9ca3af"></i> Deskripsi Pekerjaan</h4>';
        html += '<p style="font-size:0.875rem;line-height:1.75;color:#4b5563;white-space:pre-wrap;margin:0">' + (deskripsi || '<span style="color:#9ca3af;font-style:italic">Tidak ada deskripsi</span>').replace(/</g, '&lt;') + '</p>';
        html += '</div></div>';

        // Foto
        if (foto) {
            html += '<div style="padding-top:16px;border-top:1px solid #f3f4f6">';
            html += '<h4 style="font-size:0.875rem;font-weight:600;color:#374151;display:flex;align-items:center;gap:8px;margin:0 0 12px 0"><i class="far fa-image" style="color:#9ca3af"></i> Dokumentasi</h4>';
            html += '<a href="' + BASE_URL + '/files/pkl-progress/' + foto + '" onclick="openLightbox(this.href); return false;" style="position:relative;display:inline-block;overflow:hidden;border-radius:12px;border:1px solid #e5e7eb;cursor:pointer;text-decoration:none">';
            html += '<img src="' + BASE_URL + '/files/pkl-progress/' + foto + '" style="width:100%;max-height:300px;object-fit:cover;display:block" loading="lazy" alt="Dokumentasi">';
            html += '</a></div>';
        }

        // Catatan
        if (catatanInstruktur || catatanPembimbing) {
            html += '<div style="padding-top:16px;border-top:1px solid #f3f4f6">';
            html += '<h4 style="font-size:0.875rem;font-weight:600;color:#374151;display:flex;align-items:center;gap:8px;margin:0 0 12px 0"><i class="far fa-comment-dots" style="color:#9ca3af"></i> Catatan Jurnal</h4>';
            html += '<div style="display:grid;grid-template-columns:1fr;gap:12px">';
            if (catatanInstruktur) {
                html += '<div style="background:#eef2ff;border-left:4px solid #6366f1;border-radius:0 12px 12px 0;padding:14px;box-shadow:0 1px 2px rgba(0,0,0,0.05)"><p style="font-size:10px;font-weight:700;color:#4f46e5;text-transform:uppercase;letter-spacing:0.05em;display:flex;align-items:center;gap:4px;margin:0 0 4px 0"><i class="fas fa-building" style="font-size:8px"></i> Catatan Instruktur</p><p style="font-size:0.75rem;color:#312e81;line-height:1.6;margin:0">' + catatanInstruktur.replace(/</g, '&lt;') + '</p></div>';
            }
            if (catatanPembimbing) {
                html += '<div style="background:#ecfdf5;border-left:4px solid #10b981;border-radius:0 12px 12px 0;padding:14px;box-shadow:0 1px 2px rgba(0,0,0,0.05)"><p style="font-size:10px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.05em;display:flex;align-items:center;gap:4px;margin:0 0 4px 0"><i class="fas fa-user-tie" style="font-size:8px"></i> Catatan Pembimbing</p><p style="font-size:0.75rem;color:#064e3b;line-height:1.6;margin:0">' + catatanPembimbing.replace(/</g, '&lt;') + '</p></div>';
            }
            html += '</div></div>';
        }

        html += '</div>';

        Swal.fire({
            title: '',
            html: html,
            showCloseButton: true,
            showConfirmButton: false,
            width: '640px',
            customClass: { popup: 'swal-custom-popup' },
            scrollbarPadding: false
        });
    }

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
        if (e.key === 'Escape') {
            var lb = document.getElementById('lightbox');
            if (lb && lb.classList.contains('flex')) {
                closeLightbox({ target: document.getElementById('lightbox') });
            }
        }
    });

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
<?= $this->endSection() ?>
