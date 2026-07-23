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
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Review Jurnal PKL</h1>
        <p class="text-sm text-gray-500">Instruktur PKL</p>
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
                    $studentProgress = $allProgress[$s['siswa_id']] ?? [];
                    $totalProgress = $stats ? (int) $stats['total_progress'] : 0;
                    $approvedCount = $stats ? (int) $stats['approved'] : 0;
                    $verifiedCount = $stats ? (int) $stats['verified_by_instruktur'] : 0;
                    ?>
                    <div id="student-detail-<?= $s['siswa_id'] ?>"
                        class="student-detail-panel hidden flex flex-col h-full overflow-hidden">

                        <!-- Panel Header -->
                        <div
                            class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 flex-shrink-0">
                            <div class="flex items-center gap-3.5">
                                <!-- Back button for mobile -->
                                <button type="button" onclick="backToList()"
                                    class="lg:hidden inline-flex items-center justify-center p-2.5 rounded-xl bg-white border border-gray-200 text-gray-600 hover:text-gray-900 shadow-sm transition-all hover:bg-gray-50">
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

                                <div>
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

                            <div class="flex items-center gap-3">
                                <div
                                    class="text-xs bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-xl font-semibold border border-indigo-100 flex items-center gap-1.5 shadow-sm">
                                    <i class="fas fa-check-circle"></i>
                                    Progress: <?= $approvedCount ?>/<?= $totalProgress ?> Disetujui
                                </div>
                            </div>
                        </div>

                        <!-- Panel Content -->
                        <div class="flex-grow overflow-y-auto p-6 bg-gray-50/40 space-y-5 custom-scrollbar">
                            <?php if (empty($studentProgress)): ?>
                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                    <div
                                        class="w-16 h-16 rounded-full bg-green-50 text-green-500 flex items-center justify-center mb-3">
                                        <i class="fas fa-check-circle text-2xl"></i>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-700">Belum Ada Progress</h3>
                                    <p class="text-sm text-gray-500 mt-1">Siswa belum mengirim progress untuk diverifikasi.</p>
                                </div>
                            <?php else: ?>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-2 mb-2">
                                    <i class="fas fa-calendar-day text-xs"></i> Riwayat Progress Harian
                                </h4>

                                <?php foreach ($studentProgress as $p):
                                    $statusBadge = match ($p['status']) {
                                        'approved' => ['bg' => 'bg-green-50 text-green-700 border-green-200', 'label' => 'Disetujui', 'icon' => 'fa-check-circle'],
                                        'verified_by_instruktur' => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'label' => 'Verified', 'icon' => 'fa-check-double'],
                                        'submitted' => ['bg' => 'bg-yellow-50 text-yellow-700 border-yellow-200', 'label' => 'Menunggu', 'icon' => 'fa-clock'],
                                        'revision' => ['bg' => 'bg-orange-50 text-orange-700 border-orange-200', 'label' => 'Revisi', 'icon' => 'fa-edit'],
                                        default => ['bg' => 'bg-gray-50 text-gray-600 border-gray-200', 'label' => 'Draft', 'icon' => 'fa-pen']
                                    };

                                    $langkahKerja = [];
                                    if (!empty($p['langkah_kerja'])) {
                                        $decoded = json_decode($p['langkah_kerja'], true);
                                        if (is_array($decoded)) {
                                            $langkahKerja = array_filter($decoded, fn($v) => trim($v) !== '');
                                        }
                                    }
                                    ?>
                                    <div
                                        class="bg-white rounded-2xl border border-gray-200 p-4 md:p-5 shadow-sm space-y-4 md:space-y-6 hover:shadow-md transition-all duration-200">
                                        <!-- Entry Header -->
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 sm:gap-4">
                                            <div class="min-w-0">
                                                <h3 class="text-base font-semibold text-gray-900 leading-snug">
                                                    <?= esc($p['task_judul']) ?>
                                                </h3>
                                                <div class="mt-1.5 flex flex-wrap items-center gap-2 text-xs md:text-sm text-gray-500">
                                                    <span class="flex items-center gap-1">
                                                        <i class="far fa-calendar"></i>
                                                        <?= $formatIndoDate($p['tanggal']) ?>
                                                    </span>
                                                    <?php if (!empty($langkahKerja)): ?>
                                                        <span class="text-gray-300">&bull;</span>
                                                        <span><?= count($langkahKerja) ?> langkah kerja</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border self-start sm:self-auto flex-shrink-0 <?= $statusBadge['bg'] ?>">
                                                <i class="fas <?= $statusBadge['icon'] ?> text-[10px]"></i>
                                                <?= $statusBadge['label'] ?>
                                            </span>
                                        </div>

                                        <!-- Langkah Kerja -->
                                        <?php if (!empty($langkahKerja)): ?>
                                            <div class="pt-4 md:pt-5 border-t border-gray-100">
                                                <div class="bg-gray-50/50 border border-gray-200 rounded-xl p-3.5">
                                                    <div class="flex items-center gap-2.5 mb-4">
                                                        <i class="fas fa-list-ol text-indigo-500"></i>
                                                        <span class="text-sm font-semibold text-gray-700">Perencanaan dan Persiapan Kerja</span>
                                                        <span class="text-xs text-gray-500 font-medium bg-gray-200/60 px-1.5 py-0.5 rounded-full">(<?= count($langkahKerja) ?>)</span>
                                                    </div>
                                                    <div class="pl-1">
                                                        <ol class="relative border-l border-gray-200 ml-2 space-y-4">
                                                            <?php foreach (array_values($langkahKerja) as $step): ?>
                                                                <li class="ml-5">
                                                                    <div class="absolute -left-[5px] mt-1.5 w-2.5 h-2.5 rounded-full bg-indigo-500"></div>
                                                                    <p class="text-sm text-gray-700 leading-relaxed"><?= esc($step) ?></p>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ol>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Description -->
                                        <div class="pt-4 md:pt-5 border-t border-gray-100">
                                            <div class="rounded-xl bg-gray-50 p-4">
                                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi Pekerjaan</h4>
                                                <p class="text-sm leading-7 text-gray-600 whitespace-pre-wrap"><?= esc($p['deskripsi']) ?>
                                                </p>
                                            </div>
                                        </div>  

                                        <!-- Photo -->
                                        <?php if ($p['foto']): ?>
                                            <div class="pt-4 md:pt-5 border-t border-gray-100">
                                                <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2 mb-3">
                                                    <i class="far fa-image text-gray-400"></i> Dokumentasi
                                                </h4>
                                                <a href="<?= base_url('files/pkl-progress/' . $p['foto']) ?>" target="_blank"
                                                    class="group relative inline-block overflow-hidden rounded-xl border border-gray-200 hover:shadow-md transition-shadow w-full md:w-auto">
                                                    <img src="<?= base_url('files/pkl-progress/' . $p['foto']) ?>"
                                                        class="w-full h-48 md:h-40 md:w-auto object-cover transition-transform duration-300 group-hover:scale-105"
                                                        loading="lazy" alt="Dokumentasi">
                                                    <div
                                                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-1.5">
                                                        <i class="fas fa-search-plus"></i> Lihat Foto
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Catatan notes -->
                                        <?php if (!empty($p['catatan_instruktur']) || !empty($p['catatan_pembimbing'])): ?>
                                            <div class="pt-4 md:pt-5 border-t border-gray-100 space-y-3.5">
                                                <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                                    <i class="far fa-comment-dots text-gray-400"></i> Catatan Jurnal
                                                </h4>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                                    <?php if (!empty($p['catatan_instruktur'])): ?>
                                                        <div class="bg-indigo-50 border-l-4 border-indigo-500 rounded-r-xl p-3.5 shadow-sm">
                                                            <p
                                                                class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider flex items-center gap-1">
                                                                <i class="fas fa-building text-[8px]"></i> Catatan Instruktur
                                                            </p>
                                                            <p class="text-xs text-indigo-900 mt-1 leading-relaxed"><?= esc($p['catatan_instruktur']) ?></p>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($p['catatan_pembimbing'])): ?>
                                                        <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl p-3.5 shadow-sm">
                                                            <p
                                                                class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider flex items-center gap-1">
                                                                <i class="fas fa-user-tie text-[8px]"></i> Catatan Pembimbing
                                                            </p>
                                                            <p class="text-xs text-emerald-900 mt-1 leading-relaxed"><?= esc($p['catatan_pembimbing']) ?></p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Action Section -->
                                        <div class="pt-4 md:pt-5 border-t border-gray-100">
                                            <?php if ($p['status'] === 'submitted'): ?>
                                                <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']) ?>"
                                                    method="POST" class="space-y-3">
                                                    <?= csrf_field() ?>
                                                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5 shadow-inner">
                                                        <label
                                                            class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">
                                                            Catatan Instruktur <span class="text-red-500">*</span>
                                                        </label>
                                                        <textarea name="catatan_instruktur" rows="2" required
                                                            class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all resize-none shadow-sm"
                                                            placeholder="Tulis catatan revisi atau catatan persetujuan..."></textarea>

                                                        <div class="flex flex-col sm:flex-row justify-end gap-2.5 mt-3">
                                                            <button type="submit" name="status" value="revision"
                                                                onclick="return confirm('Minta revisi progress ini?')"
                                                                class="w-full sm:w-auto px-4 py-2 border border-orange-200 text-orange-700 font-bold text-xs hover:bg-orange-50 hover:border-orange-300 bg-white rounded-xl shadow-sm transition-all active:scale-95 flex items-center justify-center gap-1.5">
                                                                <i class="fas fa-edit text-[10px]"></i> Minta Revisi
                                                            </button>
                                                            <button type="submit" name="status" value="verified_by_instruktur"
                                                                onclick="return confirm('Setujui progress ini?')"
                                                                class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 rounded-xl shadow-sm transition-all active:scale-95 flex items-center justify-center gap-1.5">
                                                                <i class="fas fa-check text-[10px]"></i> Setujui
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            <?php elseif (in_array($p['status'], ['verified_by_instruktur', 'revision'])): ?>
                                                <div
                                                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-indigo-50/40 border border-indigo-100 rounded-xl p-3.5 pl-4 shadow-inner">
                                                    <div class="flex items-center gap-2.5 text-xs text-indigo-800">
                                                        <i class="fas fa-check-circle text-base text-indigo-500 flex-shrink-0"></i>
                                                        <div class="min-w-0">
                                                            <span class="font-bold">Progress
                                                                <?= $p['status'] === 'verified_by_instruktur' ? 'diverifikasi' : 'direvisi' ?></span>
                                                            <?php if (!empty($p['catatan_instruktur'])): ?>
                                                                <p class="text-indigo-700 mt-0.5 italic break-words">
                                                                    "<?= esc($p['catatan_instruktur']) ?>"</p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <form
                                                        action="<?= base_url('instruktur/jurnal-pkl/batal-verifikasi-progress/' . $p['id']) ?>"
                                                        method="POST" onsubmit="saveActiveSiswa(<?= $s['siswa_id'] ?>)" class="w-full sm:w-auto">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" onclick="return confirm('Batalkan verifikasi progress ini?')"
                                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white border border-orange-200 text-orange-700 font-semibold rounded-xl hover:bg-orange-50 hover:border-orange-300 text-xs shadow-sm transition-all active:scale-95 whitespace-nowrap">
                                                            <i class="fas fa-undo text-[10px]"></i> Batalkan
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php elseif ($p['status'] === 'approved'): ?>
                                                <?php if (!empty($p['instruktur_verified_by'])): ?>
                                                    <div
                                                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-green-50/40 border border-green-100 rounded-xl p-3.5 pl-4 shadow-inner">
                                                        <div class="flex items-center gap-2.5 text-xs text-green-800">
                                                            <i class="fas fa-check-circle text-base text-green-500 flex-shrink-0"></i>
                                                            <div class="min-w-0">
                                                                <span class="font-bold">Disetujui Pembimbing</span>
                                                                <?php if (!empty($p['catatan_instruktur'])): ?>
                                                                    <p class="text-green-700 mt-0.5 italic break-words">
                                                                        "<?= esc($p['catatan_instruktur']) ?>"</p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <form
                                                            action="<?= base_url('instruktur/jurnal-pkl/batal-verifikasi-progress/' . $p['id']) ?>"
                                                            method="POST" onsubmit="saveActiveSiswa(<?= $s['siswa_id'] ?>)" class="w-full sm:w-auto">
                                                            <?= csrf_field() ?>
                                                            <button type="submit" onclick="return confirm('Batalkan verifikasi progress ini?')"
                                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white border border-orange-200 text-orange-700 font-semibold rounded-xl hover:bg-orange-50 hover:border-orange-300 text-xs shadow-sm transition-all active:scale-95 whitespace-nowrap">
                                                                <i class="fas fa-undo text-[10px]"></i> Batalkan
                                                            </button>
                                                        </form>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5 shadow-inner">
                                                        <p
                                                            class="text-[10px] font-bold text-green-600 uppercase tracking-wider mb-1.5 flex items-center gap-1">
                                                            <i class="fas fa-check-circle text-[9px]"></i> Disetujui Pembimbing — Verifikasi Instruktur
                                                        </p>
                                                        <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']) ?>"
                                                            method="POST">
                                                            <?= csrf_field() ?>
                                                            <textarea name="catatan_instruktur" rows="2" required
                                                                class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all resize-none shadow-sm"
                                                                placeholder="Tulis catatan revisi atau catatan persetujuan..."></textarea>
                                                            <div class="flex flex-col sm:flex-row justify-end gap-2.5 mt-3">
                                                                <button type="submit" name="status" value="revision"
                                                                    onclick="return confirm('Minta revisi progress ini?')"
                                                                    class="w-full sm:w-auto px-4 py-2 border border-orange-200 text-orange-700 font-bold text-xs hover:bg-orange-50 hover:border-orange-300 bg-white rounded-xl shadow-sm transition-all active:scale-95 flex items-center justify-center gap-1.5">
                                                                    <i class="fas fa-edit text-[10px]"></i> Minta Revisi
                                                                </button>
                                                                <button type="submit" name="status" value="verified_by_instruktur"
                                                                    onclick="return confirm('Setujui progress ini?')"
                                                                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 rounded-xl shadow-sm transition-all active:scale-95 flex items-center justify-center gap-1.5">
                                                                    <i class="fas fa-check text-[10px]"></i> Verifikasi
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
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