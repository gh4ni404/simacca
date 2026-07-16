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
    .kanban-shadow { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04); }
</style>

<div class="min-h-screen bg-gray-50/50 p-4 md:p-6">
    <!-- Header Section with Student Profile info -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 border-b border-gray-200/60 pb-6">
        <div class="flex items-center gap-4">
            <a href="<?= base_url('instruktur/jurnal-pkl'); ?>" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:text-indigo-600 transition-colors shadow-sm active:scale-95">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-1 font-[Manrope]"><?= esc($siswa['nama_lengkap']); ?></h1>
                <p class="text-sm text-gray-500 font-medium flex items-center gap-2">
                    <span class="font-mono">NIS: <?= esc($siswa['nis'] ?? '-'); ?></span>
                    <span class="text-gray-300">&middot;</span>
                    <span class="bg-indigo-50 text-indigo-700 px-2.5 py-0.5 rounded-full text-xs font-bold font-sans"><?= esc($siswa['nama_kelas'] ?? '-'); ?></span>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="bg-indigo-100 text-indigo-800 px-3.5 py-1.5 rounded-xl font-bold">
                Total: <?= count($tasks) ?> Task Pekerjaan
            </span>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <?php if (empty($tasks)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-100 max-w-lg mx-auto mt-12">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 mb-4">
            <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-800">Belum Ada Task</h3>
        <p class="text-gray-500 mt-1">Siswa belum membuat/merencanakan task pekerjaan apapun untuk jurnal PKL.</p>
    </div>
    <?php else: ?>
        <?php
        $activeTasks = [];
        $completedTasks = [];

        foreach ($tasks as $t) {
            if ($t['status'] === 'completed') {
                $completedTasks[] = $t;
            } else {
                $activeTasks[] = $t;
            }
        }
        ?>

        <!-- Kanban Board for Tasks -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            <!-- Column 1: Active Tasks (Sedang Dikerjakan) -->
            <div class="flex flex-col bg-slate-100/60 rounded-2xl p-4 border border-slate-200/50">
                <div class="flex items-center justify-between mb-4 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <h4 class="font-bold text-sm tracking-wider text-gray-700 uppercase">Sedang Dikerjakan</h4>
                        <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-xs font-extrabold"><?= sprintf('%02d', count($activeTasks)) ?></span>
                    </div>
                </div>

                <div class="space-y-4 max-h-[calc(100vh-240px)] overflow-y-auto custom-scrollbar pr-1">
                    <?php if (empty($activeTasks)): ?>
                        <div class="bg-white/60 border border-dashed border-gray-300 rounded-xl p-6 text-center text-gray-500 text-sm">
                            <i class="fas fa-check-circle text-green-500 mb-2 text-lg"></i>
                            <p>Tidak ada pekerjaan yang aktif.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($activeTasks as $t):
                            $progressCount = (int)$t['total_progress'];
                            $approvedCount = (int)$t['approved_count'];
                            $verifiedCount = (int)$t['verified_count'];
                        ?>
                            <a href="<?= base_url('instruktur/jurnal-pkl/task/' . $t['id']); ?>" 
                               class="block bg-white p-5 rounded-xl kanban-shadow border-l-4 border-amber-500 hover:translate-y-[-2px] hover:shadow-md transition-all group">
                                <div class="flex justify-between items-start gap-2 mb-3">
                                    <h5 class="text-gray-800 font-bold text-sm group-hover:text-indigo-600 transition-colors line-clamp-2"><?= esc($t['judul']); ?></h5>
                                    <?php if (!empty($t['kategori_nama'])): ?>
                                        <span class="flex-shrink-0 text-[10px] px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 font-bold uppercase border border-indigo-100/50"><?= esc($t['kategori_nama']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="bg-slate-50 p-2.5 rounded-lg mb-4">
                                    <div class="flex justify-between items-center text-xs text-gray-600 mb-1">
                                        <span>Progress Jurnal</span>
                                        <span class="font-bold"><?= $progressCount ?> Entri</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-[10px] text-gray-400">
                                        <span class="flex items-center gap-1"><i class="fas fa-check-double text-blue-500"></i> <?= $verifiedCount ?> diverifikasi</span>
                                        <span>&middot;</span>
                                        <span class="flex items-center gap-1"><i class="fas fa-check text-green-500"></i> <?= $approvedCount ?> disetujui</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span class="flex items-center gap-1"><i class="fas fa-spinner animate-spin text-amber-500"></i> Aktif</span>
                                    <div class="w-24">
                                        <?= render_task_progress_bar($t['status']) ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Column 2: Completed Tasks (Selesai) -->
            <div class="flex flex-col bg-slate-100/60 rounded-2xl p-4 border border-slate-200/50">
                <div class="flex items-center justify-between mb-4 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                        <h4 class="font-bold text-sm tracking-wider text-gray-700 uppercase">Selesai</h4>
                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-extrabold"><?= sprintf('%02d', count($completedTasks)) ?></span>
                    </div>
                </div>

                <div class="space-y-4 max-h-[calc(100vh-240px)] overflow-y-auto custom-scrollbar pr-1">
                    <?php if (empty($completedTasks)): ?>
                        <div class="bg-white/60 border border-dashed border-gray-300 rounded-xl p-6 text-center text-gray-500 text-sm">
                            <p>Belum ada pekerjaan yang diselesaikan.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($completedTasks as $t):
                            $progressCount = (int)$t['total_progress'];
                            $approvedCount = (int)$t['approved_count'];
                            $verifiedCount = (int)$t['verified_count'];
                        ?>
                            <a href="<?= base_url('instruktur/jurnal-pkl/task/' . $t['id']); ?>" 
                               class="block bg-white p-5 rounded-xl kanban-shadow border-l-4 border-green-500 hover:translate-y-[-2px] hover:shadow-md transition-all group">
                                <div class="flex justify-between items-start gap-2 mb-3">
                                    <h5 class="text-gray-800 font-bold text-sm group-hover:text-green-600 transition-colors line-clamp-2"><?= esc($t['judul']); ?></h5>
                                    <?php if (!empty($t['kategori_nama'])): ?>
                                        <span class="flex-shrink-0 text-[10px] px-2 py-0.5 rounded-full bg-green-50 text-green-600 font-bold uppercase border border-green-100/50"><?= esc($t['kategori_nama']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="bg-slate-50 p-2.5 rounded-lg mb-4">
                                    <div class="flex justify-between items-center text-xs text-gray-600 mb-1">
                                        <span>Progress Jurnal</span>
                                        <span class="font-bold"><?= $progressCount ?> Entri</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-[10px] text-gray-400">
                                        <span class="flex items-center gap-1"><i class="fas fa-check-double text-blue-500"></i> <?= $verifiedCount ?> diverifikasi</span>
                                        <span>&middot;</span>
                                        <span class="flex items-center gap-1"><i class="fas fa-check text-green-500"></i> <?= $approvedCount ?> disetujui</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span class="flex items-center gap-1 text-green-600 font-semibold"><i class="fas fa-check-circle"></i> Selesai</span>
                                    <div class="w-24">
                                        <?= render_task_progress_bar($t['status']) ?>
                                    </div>
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
