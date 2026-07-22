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

<?php
$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$hariIndo = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];

// Helper to format date
$formatIndoDate = function($dateStr) use ($hariIndo, $bulanIndo) {
    $dateObj = new DateTime($dateStr);
    $dayName = $hariIndo[$dateObj->format('l')] ?? $dateObj->format('l');
    $dateFormatted = $dateObj->format('j') . ' ' . $bulanIndo[(int)$dateObj->format('m')] . ' ' . $dateObj->format('Y');
    return $dayName . ', ' . $dateFormatted;
};
?>

<div class="min-h-screen bg-gray-50/50 p-4 md:p-6">
    <!-- Header with Task Information -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 border-b border-gray-200/60 pb-6">
        <div class="flex items-center gap-4">
            <a href="<?= base_url('instruktur/jurnal-pkl/siswa/' . $task['siswa_id']); ?>" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:text-indigo-600 transition-colors shadow-sm active:scale-95">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-1 font-[Manrope]"><?= esc($task['judul']); ?></h1>
                <p class="text-sm text-gray-500 font-medium flex items-center gap-2">
                    <span>Siswa: <strong class="text-gray-700"><?= esc($task['nama_siswa']); ?></strong></span>
                    <span class="text-gray-300">&middot;</span>
                    <?php if (!empty($task['kategori_nama'])): ?>
                        <span class="bg-indigo-50 text-indigo-700 px-2.5 py-0.5 rounded-full text-xs font-bold font-sans"><?= esc($task['kategori_nama']); ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <?php if ($task['status'] === 'active'): ?>
            <span class="inline-flex items-center px-4 py-2 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl text-sm font-semibold">
                <i class="fas fa-spinner mr-2 animate-spin"></i>Sedang Dikerjakan
            </span>
            <?php elseif ($task['status'] === 'completed'): ?>
            <span class="inline-flex items-center px-4 py-2 bg-green-50 text-green-700 border border-green-200 rounded-xl text-sm font-semibold">
                <i class="fas fa-check-circle mr-2"></i>Selesai
            </span>
            <?php endif; ?>
            <div class="w-32 hidden md:block">
                <?= render_task_progress_bar($task['status'], 'lg') ?>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <?php if (empty($progress)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-100 max-w-lg mx-auto mt-12">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 mb-4">
            <i class="fas fa-tasks text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-800">Belum Ada Progress</h3>
        <p class="text-gray-500 mt-1">Siswa belum mengunggah/menginput progress kerja apapun untuk task ini.</p>
    </div>
    <?php else: ?>
        <?php
        $pendingReview = [];
        $revisions = [];
        $completed = [];

        foreach ($progress as $p) {
            if ($p['status'] === 'submitted') {
                $pendingReview[] = $p;
            } elseif ($p['status'] === 'revision') {
                $revisions[] = $p;
            } else {
                $completed[] = $p;
            }
        }
        ?>

        <!-- Kanban Board for Progress Reports -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            <!-- Column 1: Menunggu Review -->
            <div class="flex flex-col bg-slate-100/60 rounded-2xl p-4 border border-slate-200/50">
                <div class="flex items-center justify-between mb-4 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-500 animate-pulse"></span>
                        <h4 class="font-bold text-sm tracking-wider text-gray-700 uppercase">Menunggu Review</h4>
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs font-extrabold"><?= sprintf('%02d', count($pendingReview)) ?></span>
                    </div>
                </div>

                <div class="space-y-4 max-h-[calc(100vh-240px)] overflow-y-auto custom-scrollbar pr-1">
                    <?php if (empty($pendingReview)): ?>
                        <div class="bg-white/60 border border-dashed border-gray-300 rounded-xl p-6 text-center text-gray-500 text-sm">
                            <i class="fas fa-check text-green-500 mb-1"></i>
                            <p>Tidak ada progress yang perlu ditinjau.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pendingReview as $p): ?>
                            <div class="bg-white p-4 rounded-xl kanban-shadow border-l-4 border-yellow-500 hover:shadow-md transition-all">
                                <div class="flex justify-between items-center text-xs text-gray-500 mb-2">
                                    <span class="font-semibold text-gray-800"><?= $formatIndoDate($p['tanggal']) ?></span>
                                    <span class="bg-yellow-50 text-yellow-700 px-2 py-0.5 rounded font-bold uppercase text-[9px]"><i class="fas fa-clock mr-1"></i>Menunggu</span>
                                </div>
                                <p class="text-xs text-gray-700 leading-relaxed mb-3 break-words"><?= esc($p['deskripsi']); ?></p>

                                <?php if ($p['foto']): ?>
                                <div class="mb-3">
                                    <a href="<?= base_url('files/pkl-progress/' . $p['foto']); ?>" target="_blank" class="group block relative rounded-lg overflow-hidden border border-gray-200">
                                        <img src="<?= base_url('files/pkl-progress/' . $p['foto']); ?>" class="w-full h-24 object-cover group-hover:scale-105 transition-transform" loading="lazy">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white text-[10px] font-bold"><i class="fas fa-search-plus mr-1"></i> Lihat Foto</div>
                                    </a>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($p['catatan_pembimbing'])): ?>
                                <div class="bg-blue-50 border-l-2 border-blue-400 p-2 rounded-r mb-3 text-[11px]">
                                    <span class="font-bold text-blue-700 block uppercase text-[9px] mb-0.5">Catatan Pembimbing</span>
                                    <p class="text-blue-800"><?= esc($p['catatan_pembimbing']); ?></p>
                                </div>
                                <?php endif; ?>

                                <!-- Approve / Revise Form -->
                                <div class="pt-3 border-t border-gray-100 space-y-3">
                                    <!-- Setujui Form -->
                                    <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']); ?>" method="POST" class="space-y-2">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="status" value="verified_by_instruktur">
                                        <div class="flex gap-2">
                                            <input type="text" name="catatan_instruktur" required placeholder="Catatan persetujuan..." class="flex-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-indigo-500 focus:border-transparent">
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all"><i class="fas fa-check mr-1"></i>Setujui</button>
                                        </div>
                                    </form>

                                    <!-- Revisi Form -->
                                    <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']); ?>" method="POST" class="space-y-2">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="status" value="revision">
                                        <div class="flex gap-2">
                                            <input type="text" name="catatan_instruktur" required placeholder="Alasan revisi..." class="flex-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-orange-500 focus:border-transparent">
                                            <button type="submit" onclick="return confirm('Minta revisi progress ini?')" class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all"><i class="fas fa-edit mr-1"></i>Revisi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Column 2: Perlu Perbaikan (Revision) -->
            <div class="flex flex-col bg-slate-100/60 rounded-2xl p-4 border border-slate-200/50">
                <div class="flex items-center justify-between mb-4 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                        <h4 class="font-bold text-sm tracking-wider text-gray-700 uppercase">Perlu Perbaikan</h4>
                        <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full text-xs font-extrabold"><?= sprintf('%02d', count($revisions)) ?></span>
                    </div>
                </div>

                <div class="space-y-4 max-h-[calc(100vh-240px)] overflow-y-auto custom-scrollbar pr-1">
                    <?php if (empty($revisions)): ?>
                        <div class="bg-white/60 border border-dashed border-gray-300 rounded-xl p-6 text-center text-gray-500 text-sm">
                            <p>Tidak ada progress yang sedang direvisi.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($revisions as $p): ?>
                            <div class="bg-white p-4 rounded-xl kanban-shadow border-l-4 border-orange-500 hover:shadow-md transition-all">
                                <div class="flex justify-between items-center text-xs text-gray-500 mb-2">
                                    <span class="font-semibold text-gray-800"><?= $formatIndoDate($p['tanggal']) ?></span>
                                    <span class="bg-orange-50 text-orange-700 px-2 py-0.5 rounded font-bold uppercase text-[9px]"><i class="fas fa-edit mr-1"></i>Revisi</span>
                                </div>
                                <p class="text-xs text-gray-700 leading-relaxed mb-3 break-words"><?= esc($p['deskripsi']); ?></p>

                                <?php if ($p['foto']): ?>
                                <div class="mb-3">
                                    <a href="<?= base_url('files/pkl-progress/' . $p['foto']); ?>" target="_blank" class="group block relative rounded-lg overflow-hidden border border-gray-200">
                                        <img src="<?= base_url('files/pkl-progress/' . $p['foto']); ?>" class="w-full h-24 object-cover group-hover:scale-105 transition-transform" loading="lazy">
                                    </a>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($p['catatan_instruktur'])): ?>
                                <div class="bg-orange-50 border-l-2 border-orange-400 p-2 rounded-r mb-3 text-[11px]">
                                    <span class="font-bold text-orange-700 block uppercase text-[9px] mb-0.5">Alasan Revisi</span>
                                    <p class="text-orange-850"><?= esc($p['catatan_instruktur']); ?></p>
                                </div>
                                <?php endif; ?>

                                <div class="pt-3 border-t border-gray-100 flex justify-end">
                                    <form action="<?= base_url('instruktur/jurnal-pkl/batal-verifikasi-progress/' . $p['id']); ?>" method="POST" class="inline">
                                        <?= csrf_field(); ?>
                                        <button type="submit" onclick="return confirm('Batalkan status revisi progress ini?')" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-250 hover:text-gray-900 text-xs font-bold transition-all"><i class="fas fa-undo mr-1"></i>Batalkan</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Column 3: Diverifikasi & Disetujui -->
            <div class="flex flex-col bg-slate-100/60 rounded-2xl p-4 border border-slate-200/50">
                <div class="flex items-center justify-between mb-4 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                        <h4 class="font-bold text-sm tracking-wider text-gray-700 uppercase">Tervalidasi</h4>
                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-extrabold"><?= sprintf('%02d', count($completed)) ?></span>
                    </div>
                </div>

                <div class="space-y-4 max-h-[calc(100vh-240px)] overflow-y-auto custom-scrollbar pr-1">
                    <?php if (empty($completed)): ?>
                        <div class="bg-white/60 border border-dashed border-gray-300 rounded-xl p-6 text-center text-gray-500 text-sm">
                            <p>Belum ada progress yang disetujui.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($completed as $p): ?>
                            <?php 
                            $isApproved = ($p['status'] === 'approved');
                            $borderColor = $isApproved ? 'border-green-500' : 'border-blue-500';
                            $badgeColor = $isApproved ? 'bg-green-50 text-green-700 border-green-200' : 'bg-blue-50 text-blue-700 border-blue-200';
                            $badgeText = $isApproved ? 'Approved (Pembimbing)' : 'Diverifikasi';
                            ?>
                            <div class="bg-white p-4 rounded-xl kanban-shadow border-l-4 <?= $borderColor ?> hover:shadow-md transition-all">
                                <div class="flex justify-between items-center text-xs text-gray-500 mb-2">
                                    <span class="font-semibold text-gray-800"><?= $formatIndoDate($p['tanggal']) ?></span>
                                    <span class="border <?= $badgeColor ?> px-2 py-0.5 rounded font-bold uppercase text-[8px]"><i class="fas <?= $isApproved ? 'fa-check-double' : 'fa-check' ?> mr-1"></i><?= $badgeText ?></span>
                                </div>
                                <p class="text-xs text-gray-700 leading-relaxed mb-3 break-words"><?= esc($p['deskripsi']); ?></p>

                                <?php if ($p['foto']): ?>
                                <div class="mb-3">
                                    <a href="<?= base_url('files/pkl-progress/' . $p['foto']); ?>" target="_blank" class="group block relative rounded-lg overflow-hidden border border-gray-200">
                                        <img src="<?= base_url('files/pkl-progress/' . $p['foto']); ?>" class="w-full h-24 object-cover" loading="lazy">
                                    </a>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($p['catatan_instruktur'])): ?>
                                <div class="bg-purple-50 border-l-2 border-purple-400 p-2 rounded-r mb-3 text-[11px]">
                                    <span class="font-bold text-purple-700 block uppercase text-[9px] mb-0.5">Catatan Instruktur</span>
                                    <p class="text-purple-800"><?= esc($p['catatan_instruktur']); ?></p>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($p['catatan_pembimbing'])): ?>
                                <div class="bg-blue-50 border-l-2 border-blue-400 p-2 rounded-r mb-3 text-[11px]">
                                    <span class="font-bold text-blue-700 block uppercase text-[9px] mb-0.5">Catatan Pembimbing</span>
                                    <p class="text-blue-800"><?= esc($p['catatan_pembimbing']); ?></p>
                                </div>
                                <?php endif; ?>

                                <div class="pt-3 border-t border-gray-100">
                                    <?php if ($p['status'] === 'verified_by_instruktur'): ?>
                                        <form action="<?= base_url('instruktur/jurnal-pkl/batal-verifikasi-progress/' . $p['id']); ?>" method="POST" class="flex justify-end">
                                            <?= csrf_field(); ?>
                                            <button type="submit" onclick="return confirm('Batalkan verifikasi progress ini?')" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-250 hover:text-gray-900 text-xs font-bold transition-all"><i class="fas fa-undo mr-1"></i>Batalkan</button>
                                        </form>
                                    <?php elseif ($p['status'] === 'approved' && empty($p['instruktur_verified_by'])): ?>
                                        <!-- Approved by pembimbing, need instructor verification -->
                                        <div class="w-full space-y-2">
                                            <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']); ?>" method="POST" class="space-y-2">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="status" value="verified_by_instruktur">
                                                <div class="flex gap-2">
                                                    <input type="text" name="catatan_instruktur" required placeholder="Catatan verifikasi..." class="flex-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-indigo-500 focus:border-transparent">
                                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all"><i class="fas fa-check mr-1"></i>Verifikasi</button>
                                                </div>
                                            </form>
                                            <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']); ?>" method="POST" class="space-y-2">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="status" value="revision">
                                                <div class="flex gap-2">
                                                    <input type="text" name="catatan_instruktur" required placeholder="Alasan revisi..." class="flex-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-orange-500 focus:border-transparent">
                                                    <button type="submit" onclick="return confirm('Minta revisi progress ini?')" class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all"><i class="fas fa-edit mr-1"></i>Revisi</button>
                                                </div>
                                            </form>
                                        </div>
                                    <?php elseif ($p['status'] === 'approved'): ?>
                                        <!-- Verified by instructor, approved by pembimbing -->
                                        <div class="flex gap-2">
                                            <form action="<?= base_url('instruktur/jurnal-pkl/catatan/' . $p['id']); ?>" method="POST" class="flex-1">
                                                <?= csrf_field(); ?>
                                                <div class="flex gap-2">
                                                    <input type="text" name="catatan_instruktur" value="<?= esc($p['catatan_instruktur'] ?? ''); ?>" required placeholder="Perbarui catatan..." class="flex-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-indigo-500 focus:border-transparent">
                                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-2.5 py-1.5 rounded-lg text-xs font-bold transition-all" title="Simpan Catatan"><i class="fas fa-save"></i></button>
                                                </div>
                                            </form>
                                            <form action="<?= base_url('instruktur/jurnal-pkl/batal-verifikasi-progress/' . $p['id']); ?>" method="POST" class="inline">
                                                <?= csrf_field(); ?>
                                                <button type="submit" onclick="return confirm('Batalkan verifikasi instruktur? Approval pembimbing tetap berlaku.')" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-250 hover:text-gray-900 text-xs font-bold transition-all"><i class="fas fa-undo mr-1"></i></button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <!-- Already verified by both: Only update catatan -->
                                        <form action="<?= base_url('instruktur/jurnal-pkl/catatan/' . $p['id']); ?>" method="POST" class="w-full flex justify-end">
                                            <?= csrf_field(); ?>
                                            <div class="flex gap-2">
                                                <input type="text" name="catatan_instruktur" value="<?= esc($p['catatan_instruktur'] ?? ''); ?>" required placeholder="Perbarui catatan..." class="flex-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-indigo-500 focus:border-transparent">
                                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-2.5 py-1.5 rounded-lg text-xs font-bold transition-all" title="Simpan Catatan"><i class="fas fa-save"></i></button>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
