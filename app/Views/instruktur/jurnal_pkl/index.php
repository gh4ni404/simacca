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

$formatIndoDate = function($dateStr) use ($hariIndo, $bulanIndo) {
    if (!$dateStr) return '-';
    $dateObj = new DateTime($dateStr);
    $dayName = $hariIndo[$dateObj->format('l')] ?? $dateObj->format('l');
    $dateFormatted = $dateObj->format('j') . ' ' . $bulanIndo[(int)$dateObj->format('m')] . ' ' . $dateObj->format('Y');
    return $dayName . ', ' . $dateFormatted;
};
?>

<div class="min-h-screen bg-gray-50/50 p-4 md:p-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 border-b border-gray-200/60 pb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-1 font-[Manrope]">
                <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Review Jurnal PKL</span>
            </h1>
            <p class="text-base text-gray-600 flex items-center">
                <i class="fas fa-info-circle mr-2 text-indigo-500 text-sm"></i>
                Verifikasi laporan harian siswa PKL secara langsung dari satu halaman dashboard.
            </p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.location.reload();" class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl shadow-sm hover:bg-gray-50 transition-all text-sm active:scale-95">
                <i class="fas fa-sync-alt mr-2 text-gray-500"></i> Segarkan Halaman
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
        <p class="text-gray-500 mt-1">Belum ada siswa yang ditempatkan di tempat PKL Anda untuk periode aktif.</p>
    </div>
    <?php else: ?>
        <!-- Two Column Dashboard Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- Left Side: Antrean Verifikasi Jurnal (2/3 Grid Width) -->
            <div class="lg:col-span-2 space-y-5">
                <div class="flex items-center justify-between px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-yellow-500 animate-pulse"></span>
                        <h2 class="font-bold text-lg text-gray-800 font-[Manrope]">Antrean Verifikasi Jurnal</h2>
                        <span class="bg-yellow-100 text-yellow-800 px-2.5 py-0.5 rounded-full text-xs font-extrabold"><?= sprintf('%02d', count($pendingProgress)) ?></span>
                    </div>
                </div>

                <div class="space-y-4">
                    <?php if (empty($pendingProgress)): ?>
                        <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center text-gray-500">
                            <div class="w-16 h-16 rounded-full bg-green-50 text-green-500 flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-check-circle text-2xl"></i>
                            </div>
                            <h3 class="text-base font-bold text-gray-800">Semua Laporan Selesai Ditinjau</h3>
                            <p class="text-sm text-gray-500 mt-1">Siswa Anda tidak memiliki laporan baru yang menunggu verifikasi saat ini.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pendingProgress as $p): ?>
                            <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-all">
                                <!-- Card Header: Profile, Date & Task title -->
                                <div class="flex items-start justify-between gap-4 mb-4">
                                    <div class="flex items-center gap-3">
                                        <?php if ($p['profile_photo']): ?>
                                            <img class="w-11 h-11 rounded-xl object-cover border border-gray-200" src="<?= base_url('profile-photo/' . esc($p['profile_photo'])); ?>" alt="<?= esc($p['nama_siswa']) ?>" />
                                        <?php else: ?>
                                            <div class="w-11 h-11 bg-indigo-50 border border-indigo-100/50 rounded-xl flex items-center justify-center text-indigo-600 font-bold text-base">
                                                <?= strtoupper(substr($p['nama_siswa'], 0, 2)); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h3 class="text-sm font-extrabold text-gray-800 leading-tight"><?= esc($p['nama_siswa']); ?></h3>
                                            <p class="text-xs text-gray-500 font-medium font-sans">
                                                <?= esc($p['nama_kelas'] ?? '-'); ?> &middot; NIS: <?= esc($p['nis'] ?? '-'); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-400 font-semibold bg-gray-50 border border-gray-150 px-2.5 py-1 rounded-lg">
                                        <?= $formatIndoDate($p['tanggal']) ?>
                                    </span>
                                </div>

                                <!-- Task Reference Badge -->
                                <div class="mb-3 px-3 py-2 bg-indigo-50/50 border-l-4 border-indigo-500 rounded-r-lg">
                                    <span class="text-[10px] text-indigo-700 font-extrabold uppercase tracking-wider block">Task Pekerjaan</span>
                                    <span class="text-xs font-bold text-gray-700 line-clamp-1"><?= esc($p['task_judul']) ?></span>
                                </div>

                                <!-- Progress Description -->
                                <div class="mb-4">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block mb-1">Deskripsi Hasil Kerja</span>
                                    <p class="text-sm text-gray-700 leading-relaxed bg-slate-50/60 p-3 rounded-xl border border-slate-100 break-words font-mono text-[13px]"><?= esc($p['deskripsi']); ?></p>
                                </div>

                                <!-- Photo Attachment -->
                                <?php if ($p['foto']): ?>
                                <div class="mb-4">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block mb-1.5">Foto Lampiran / Dokumentasi</span>
                                    <a href="<?= base_url('files/pkl-progress/' . $p['foto']); ?>" target="_blank" class="inline-block group relative rounded-xl overflow-hidden border border-gray-250">
                                        <img src="<?= base_url('files/pkl-progress/' . $p['foto']); ?>" class="max-h-48 rounded-xl group-hover:scale-102 transition-transform" loading="lazy">
                                    </a>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($p['catatan_pembimbing'])): ?>
                                <div class="bg-blue-50/70 border-l-2 border-blue-400 p-2.5 rounded-r-xl mb-4 text-[11px]">
                                    <span class="font-bold text-blue-700 block uppercase text-[9px] mb-0.5">Catatan Pembimbing</span>
                                    <p class="text-blue-800"><?= esc($p['catatan_pembimbing']); ?></p>
                                </div>
                                <?php endif; ?>

                                <!-- Direct Action Forms (1-Click Approval) -->
                                <div class="pt-4 border-t border-gray-150/60 flex flex-col sm:flex-row gap-3">
                                    <!-- Setujui Form -->
                                    <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']); ?>" method="POST" class="flex-1 flex gap-2">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="status" value="verified_by_instruktur">
                                        <input type="text" name="catatan_instruktur" required placeholder="Catatan persetujuan..." class="flex-1 border border-gray-300 rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-green-500 focus:border-transparent">
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1 active:scale-95 flex-shrink-0">
                                            <i class="fas fa-check"></i> Setujui
                                        </button>
                                    </form>

                                    <!-- Revisi Form -->
                                    <form action="<?= base_url('instruktur/jurnal-pkl/verifikasi-progress/' . $p['id']); ?>" method="POST" class="flex-1 flex gap-2">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="status" value="revision">
                                        <input type="text" name="catatan_instruktur" required placeholder="Catatan revisi (wajib)..." class="flex-1 border border-gray-300 rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-orange-500 focus:border-transparent">
                                        <button type="submit" onclick="return confirm('Minta revisi progress ini?')" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1 active:scale-95 flex-shrink-0">
                                            <i class="fas fa-edit"></i> Revisi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Side: Daftar Siswa (1/3 Grid Width) -->
            <div class="space-y-5">
                <div class="flex items-center justify-between px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-indigo-600"></span>
                        <h2 class="font-bold text-lg text-gray-800 font-[Manrope]">Daftar Siswa Bimbingan</h2>
                        <span class="bg-indigo-100 text-indigo-800 px-2.5 py-0.5 rounded-full text-xs font-extrabold"><?= sprintf('%02d', count($siswaList)) ?></span>
                    </div>
                </div>

                <div class="space-y-3">
                    <?php foreach ($siswaList as $s):
                        $stats = $siswaStats[$s['siswa_id']] ?? null;
                        $totalProgress = $stats ? (int)$stats['total_progress'] : 0;
                        $approvedCount = $stats ? (int)$stats['approved'] : 0;
                        $verifiedCount = $stats ? (int)$stats['verified_by_instruktur'] : 0;
                        $submittedCount = $stats ? (int)$stats['submitted'] : 0;
                        $revisionCount = $stats ? (int)$stats['revision'] : 0;
                        $lastActivity = $stats ? $stats['last_activity'] : null;
                    ?>
                        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm hover:border-indigo-400 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <?php if ($s['profile_photo']): ?>
                                        <img class="w-9 h-9 rounded-xl object-cover border border-gray-250" src="<?= base_url('profile-photo/' . esc($s['profile_photo'])); ?>" alt="<?= esc($s['nama_lengkap']) ?>" />
                                    <?php else: ?>
                                        <div class="w-9 h-9 bg-indigo-50 border border-indigo-100/50 rounded-xl flex items-center justify-center text-indigo-600 font-bold text-sm">
                                            <?= strtoupper(substr($s['nama_lengkap'], 0, 2)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h3 class="text-xs font-bold text-gray-800 leading-tight"><?= esc($s['nama_lengkap']); ?></h3>
                                        <p class="text-[10px] text-gray-500 font-medium"><?= esc($s['nama_kelas'] ?? '-'); ?></p>
                                    </div>
                                </div>
                                <a href="<?= base_url('instruktur/jurnal-pkl/siswa/' . $s['siswa_id']); ?>" class="w-7 h-7 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 hover:text-indigo-700 rounded-lg flex items-center justify-center text-xs transition-colors shadow-sm active:scale-90" title="Detail Jurnal">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>

                            <!-- Student Progress Stats Quick Summary -->
                            <div class="grid grid-cols-4 gap-1.5 text-center mt-2.5">
                                <div class="bg-blue-50/50 border border-blue-100 p-1.5 rounded-lg">
                                    <span class="text-[9px] text-blue-500 uppercase tracking-wider block font-bold">Verif</span>
                                    <span class="text-xs font-extrabold text-blue-700"><?= $verifiedCount ?></span>
                                </div>
                                <div class="bg-green-50/50 border border-green-100 p-1.5 rounded-lg">
                                    <span class="text-[9px] text-green-500 uppercase tracking-wider block font-bold">Setuju</span>
                                    <span class="text-xs font-extrabold text-green-700"><?= $approvedCount ?></span>
                                </div>
                                <div class="bg-yellow-50/50 border border-yellow-100 p-1.5 rounded-lg">
                                    <span class="text-[9px] text-yellow-500 uppercase tracking-wider block font-bold">Antre</span>
                                    <span class="text-xs font-extrabold text-yellow-700"><?= $submittedCount ?></span>
                                </div>
                                <div class="bg-orange-50/50 border border-orange-100 p-1.5 rounded-lg">
                                    <span class="text-[9px] text-orange-500 uppercase tracking-wider block font-bold">Revisi</span>
                                    <span class="text-xs font-extrabold text-orange-700"><?= $revisionCount ?></span>
                                </div>
                            </div>
                            
                            <?php if ($lastActivity): ?>
                            <div class="mt-2.5 text-[10px] text-gray-400 flex items-center gap-1 justify-end">
                                <i class="far fa-clock"></i> Aktif terakhir: <?= date('d M Y', strtotime($lastActivity)) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
