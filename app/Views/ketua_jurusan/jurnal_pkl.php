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
                        <div class="flex-grow overflow-y-auto p-6 bg-gray-50/40 space-y-4 custom-scrollbar">
                            <?php if (empty($student['progress'])): ?>
                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                    <div class="w-16 h-16 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center mb-3 border border-gray-100">
                                        <i class="fas fa-inbox text-2xl"></i>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-700">Belum Ada Progress</h3>
                                    <p class="text-sm text-gray-500 mt-1">Siswa belum mengirim progress jurnal</p>
                                </div>
                            <?php else: ?>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-2 mb-2">
                                    <i class="fas fa-calendar-day text-xs"></i> Riwayat Progress
                                </h4>
                                <?php foreach ($student['progress'] as $prog):
                                    $statusBadge = match($prog['status']) {
                                        'approved' => 'bg-green-50 text-green-700 border-green-200',
                                        'submitted' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        'revision' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'verified_by_instruktur' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        default => 'bg-gray-50 text-gray-600 border-gray-200',
                                    };
                                    $statusIcon = match($prog['status']) {
                                        'approved' => 'fa-check-circle',
                                        'submitted' => 'fa-clock',
                                        'revision' => 'fa-edit',
                                        'verified_by_instruktur' => 'fa-check-double',
                                        default => 'fa-pen',
                                    };
                                    $statusLabel = match($prog['status']) {
                                        'approved' => 'Disetujui',
                                        'submitted' => 'Menunggu',
                                        'revision' => 'Revisi',
                                        'verified_by_instruktur' => 'Verified',
                                        default => ucfirst($prog['status']),
                                    };
                                ?>
                                <div class="bg-white rounded-2xl border border-gray-200 p-4 md:p-5 shadow-sm space-y-3 hover:shadow-md transition-all duration-200">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                                        <div class="min-w-0">
                                            <h3 class="text-base font-semibold text-gray-900 leading-snug"><?= esc($prog['nama_task']) ?></h3>
                                            <div class="mt-1.5 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                                <span class="flex items-center gap-1">
                                                    <i class="far fa-calendar"></i> <?= $formatIndoDate($prog['tanggal']) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border self-start sm:self-auto flex-shrink-0 <?= $statusBadge ?>">
                                            <i class="fas <?= $statusIcon ?> text-[10px]"></i> <?= $statusLabel ?>
                                        </span>
                                    </div>
                                    <div class="rounded-xl bg-gray-50 border border-gray-200 p-4">
                                        <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2 mb-2">
                                            <i class="far fa-file-lines text-gray-400"></i> Deskripsi Pekerjaan
                                        </h4>
                                        <p class="text-sm leading-7 text-gray-600 whitespace-pre-wrap"><?= esc(mb_strimwidth($prog['deskripsi'], 0, 300, '...')) ?></p>
                                    </div>
                                    <?php if (!empty($prog['catatan_instruktur']) || !empty($prog['catatan_pembimbing'])): ?>
                                    <div class="space-y-2">
                                        <?php if (!empty($prog['catatan_instruktur'])): ?>
                                        <div class="bg-indigo-50 border-l-4 border-indigo-500 rounded-r-xl p-3 shadow-sm">
                                            <p class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider flex items-center gap-1">
                                                <i class="fas fa-building text-[8px]"></i> Catatan Instruktur
                                            </p>
                                            <p class="text-xs text-indigo-900 mt-1 leading-relaxed"><?= esc($prog['catatan_instruktur']) ?></p>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($prog['catatan_pembimbing'])): ?>
                                        <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl p-3 shadow-sm">
                                            <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider flex items-center gap-1">
                                                <i class="fas fa-user-tie text-[8px]"></i> Catatan Pembimbing
                                            </p>
                                            <p class="text-xs text-emerald-900 mt-1 leading-relaxed"><?= esc($prog['catatan_pembimbing']) ?></p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
</style>

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
