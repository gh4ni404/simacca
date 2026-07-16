<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<!-- Google Fonts & Icons for custom elements -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .glass-effect { backdrop-filter: blur(8px); background: rgba(255, 255, 255, 0.7); }
    .kanban-shadow { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04); }
</style>

<div class="min-h-screen bg-gray-50/50 p-4 md:p-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-1 font-[Manrope]">
                <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Jurnal PKL</span>
            </h1>
            <p class="text-base text-gray-600 flex items-center">
                <i class="fas fa-info-circle mr-2 text-indigo-500 text-sm"></i>
                Review dan validasi progress pekerjaan siswa PKL di perusahaan Anda.
            </p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.location.reload();" class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl shadow-sm hover:bg-gray-50 transition-all text-sm active:scale-95">
                <i class="fas fa-sync-alt mr-2 text-gray-500"></i> Refresh
            </button>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <?php if (empty($siswaList)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-100 max-w-lg mx-auto mt-12">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-50 mb-4">
            <i class="fas fa-user-graduate text-3xl text-indigo-500"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-800">Belum Ada Siswa</h3>
        <p class="text-gray-500 mt-1">Belum ada siswa yang ditempatkan di tempat PKL ini untuk periode aktif.</p>
    </div>
    <?php else: ?>
        <?php
        $needsReview = [];
        $validated = [];
        $lateSubmissions = [];

        foreach ($siswaList as $s) {
            $stats = $siswaStats[$s['siswa_id']] ?? null;
            $totalProgress = $stats ? (int)$stats['total_progress'] : 0;
            $submittedCount = $stats ? (int)$stats['submitted'] : 0;
            $lastActivity = $stats ? $stats['last_activity'] : null;
            
            // Check if late (no progress at all OR last activity > 3 days ago)
            $isLate = false;
            if ($totalProgress === 0) {
                $isLate = true;
            } elseif ($lastActivity) {
                $lastActivityTime = strtotime($lastActivity);
                if ($lastActivityTime < strtotime('-3 days')) {
                    $isLate = true;
                }
            }

            if ($submittedCount > 0) {
                $needsReview[] = $s;
            } elseif ($isLate) {
                $lateSubmissions[] = $s;
            } else {
                $validated[] = $s;
            }
        }
        ?>

        <!-- Kanban Board -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Column 1: Needs Review -->
            <div class="flex flex-col bg-slate-100/60 rounded-2xl p-4 border border-slate-200/50">
                <div class="flex items-center justify-between mb-4 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 animate-pulse"></span>
                        <h4 class="font-bold text-sm tracking-wider text-gray-700 uppercase">Perlu Ditinjau</h4>
                        <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-extrabold"><?= sprintf('%02d', count($needsReview)) ?></span>
                    </div>
                </div>

                <div class="space-y-4 max-h-[calc(100vh-280px)] overflow-y-auto custom-scrollbar pr-1">
                    <?php if (empty($needsReview)): ?>
                        <div class="bg-white/60 border border-dashed border-gray-300 rounded-xl p-6 text-center text-gray-500 text-sm">
                            <i class="fas fa-check-circle text-green-500 mb-2 text-lg"></i>
                            <p>Semua jurnal sudah ditinjau!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($needsReview as $s):
                            $stats = $siswaStats[$s['siswa_id']] ?? null;
                            $submittedCount = $stats ? (int)$stats['submitted'] : 0;
                            $lastActivity = $stats ? $stats['last_activity'] : null;
                        ?>
                            <a href="<?= base_url('instruktur/jurnal-pkl/siswa/' . $s['siswa_id']); ?>" 
                               class="block bg-white p-4 rounded-xl kanban-shadow border-l-4 border-indigo-600 hover:translate-y-[-2px] hover:shadow-md transition-all group">
                                <div class="flex items-center gap-3 mb-3">
                                    <?php if ($s['profile_photo']): ?>
                                        <img class="w-10 h-10 rounded-lg object-cover" src="<?= base_url('profile-photo/' . esc($s['profile_photo'])); ?>" alt="<?= esc($s['nama_lengkap']) ?>" />
                                    <?php else: ?>
                                        <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0 text-indigo-600 font-bold text-sm">
                                            <?= strtoupper(substr($s['nama_lengkap'], 0, 2)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <h5 class="text-gray-800 font-bold text-sm group-hover:text-indigo-600 transition-colors truncate"><?= esc($s['nama_lengkap']); ?></h5>
                                        <p class="text-xs text-gray-500 font-mono">NIS: <?= esc($s['nis'] ?? '-'); ?></p>
                                    </div>
                                </div>
                                <div class="bg-indigo-50/50 p-2.5 rounded-lg mb-3">
                                    <p class="text-xs text-indigo-800 font-semibold flex items-center gap-1.5">
                                        <i class="fas fa-clock"></i> Ada <?= $submittedCount ?> progress baru menunggu review
                                    </p>
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-gray-400">
                                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-bold uppercase"><?= esc($s['nama_kelas'] ?? '-'); ?></span>
                                    <span><?= $lastActivity ? 'Aktif: ' . date('d M Y', strtotime($lastActivity)) : '-' ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Column 2: Validated -->
            <div class="flex flex-col bg-slate-100/60 rounded-2xl p-4 border border-slate-200/50">
                <div class="flex items-center justify-between mb-4 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                        <h4 class="font-bold text-sm tracking-wider text-gray-700 uppercase">Tervalidasi</h4>
                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-extrabold"><?= sprintf('%02d', count($validated)) ?></span>
                    </div>
                </div>

                <div class="space-y-4 max-h-[calc(100vh-280px)] overflow-y-auto custom-scrollbar pr-1">
                    <?php if (empty($validated)): ?>
                        <div class="bg-white/60 border border-dashed border-gray-300 rounded-xl p-6 text-center text-gray-500 text-sm">
                            <p>Belum ada jurnal tervalidasi</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($validated as $s):
                            $stats = $siswaStats[$s['siswa_id']] ?? null;
                            $approvedCount = $stats ? (int)$stats['approved'] : 0;
                            $verifiedCount = $stats ? (int)$stats['verified_by_instruktur'] : 0;
                            $lastActivity = $stats ? $stats['last_activity'] : null;
                        ?>
                            <a href="<?= base_url('instruktur/jurnal-pkl/siswa/' . $s['siswa_id']); ?>" 
                               class="block bg-white p-4 rounded-xl kanban-shadow border-l-4 border-green-500 hover:translate-y-[-2px] hover:shadow-md transition-all group">
                                <div class="flex items-center gap-3 mb-3">
                                    <?php if ($s['profile_photo']): ?>
                                        <img class="w-10 h-10 rounded-lg object-cover" src="<?= base_url('profile-photo/' . esc($s['profile_photo'])); ?>" alt="<?= esc($s['nama_lengkap']) ?>" />
                                    <?php else: ?>
                                        <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0 text-green-600 font-bold text-sm">
                                            <?= strtoupper(substr($s['nama_lengkap'], 0, 2)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <h5 class="text-gray-800 font-bold text-sm group-hover:text-green-600 transition-colors truncate"><?= esc($s['nama_lengkap']); ?></h5>
                                        <p class="text-xs text-gray-500 font-mono">NIS: <?= esc($s['nis'] ?? '-'); ?></p>
                                    </div>
                                </div>
                                <div class="bg-green-50/50 border border-green-100/50 p-2.5 rounded-lg mb-3">
                                    <p class="text-xs text-green-700 font-semibold flex items-center gap-1.5">
                                        <i class="fas fa-check-circle"></i> <?= $verifiedCount ?> diverifikasi &middot; <?= $approvedCount ?> disetujui
                                    </p>
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-gray-400">
                                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-bold uppercase"><?= esc($s['nama_kelas'] ?? '-'); ?></span>
                                    <span><?= $lastActivity ? 'Aktif: ' . date('d M Y', strtotime($lastActivity)) : '-' ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Column 3: Late Submissions / No Progress -->
            <div class="flex flex-col bg-slate-100/60 rounded-2xl p-4 border border-slate-200/50">
                <div class="flex items-center justify-between mb-4 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                        <h4 class="font-bold text-sm tracking-wider text-gray-700 uppercase">Tidak Aktif / Terlambat</h4>
                        <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-extrabold"><?= sprintf('%02d', count($lateSubmissions)) ?></span>
                    </div>
                </div>

                <div class="space-y-4 max-h-[calc(100vh-280px)] overflow-y-auto custom-scrollbar pr-1">
                    <?php if (empty($lateSubmissions)): ?>
                        <div class="bg-white/60 border border-dashed border-gray-300 rounded-xl p-6 text-center text-gray-500 text-sm">
                            <i class="fas fa-laugh-beam text-indigo-500 mb-2 text-lg"></i>
                            <p>Semua siswa aktif mengisi jurnal!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($lateSubmissions as $s):
                            $stats = $siswaStats[$s['siswa_id']] ?? null;
                            $totalProgress = $stats ? (int)$stats['total_progress'] : 0;
                            $lastActivity = $stats ? $stats['last_activity'] : null;
                        ?>
                            <a href="<?= base_url('instruktur/jurnal-pkl/siswa/' . $s['siswa_id']); ?>" 
                               class="block bg-white p-4 rounded-xl kanban-shadow border-l-4 border-red-500 hover:translate-y-[-2px] hover:shadow-md transition-all group bg-red-50/10">
                                <div class="flex items-center gap-3 mb-3">
                                    <?php if ($s['profile_photo']): ?>
                                        <img class="w-10 h-10 rounded-lg object-cover" src="<?= base_url('profile-photo/' . esc($s['profile_photo'])); ?>" alt="<?= esc($s['nama_lengkap']) ?>" />
                                    <?php else: ?>
                                        <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0 text-red-600 font-bold text-sm">
                                            <?= strtoupper(substr($s['nama_lengkap'], 0, 2)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <h5 class="text-gray-800 font-bold text-sm group-hover:text-red-600 transition-colors truncate"><?= esc($s['nama_lengkap']); ?></h5>
                                        <p class="text-xs text-gray-500 font-mono">NIS: <?= esc($s['nis'] ?? '-'); ?></p>
                                    </div>
                                </div>
                                <div class="bg-red-50 border border-red-100/50 p-2.5 rounded-lg mb-3">
                                    <p class="text-xs text-red-700 font-semibold flex items-center gap-1.5">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        <?php if ($totalProgress === 0): ?>
                                            Belum ada progress jurnal
                                        <?php else: ?>
                                            Tidak ada aktivitas > 3 hari
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-gray-400">
                                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-bold uppercase"><?= esc($s['nama_kelas'] ?? '-'); ?></span>
                                    <span><?= $lastActivity ? 'Terakhir: ' . date('d M Y', strtotime($lastActivity)) : 'Belum aktif' ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
