<?= $this->extend(get_device_layout()) ?>

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
?>

<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-book mr-2 text-blue-600"></i>
                Jurnal PKL
            </h1>
            <p class="text-gray-600 mt-1">Catat kegiatan PKL harian Anda</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="<?= base_url('siswa/jurnal-pkl/tambah'); ?>"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Tambah Aktivitas
            </a>
        </div>
    </div>
</div>

<?= view('components/alerts') ?>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-lg bg-blue-100 text-blue-600 mr-3">
                <i class="fas fa-tasks text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Task</p>
                <p class="text-xl font-bold"><?= $stats['total_tasks'] ?? 0 ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-lg bg-green-100 text-green-600 mr-3">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Disetujui</p>
                <p class="text-xl font-bold"><?= $stats['approved'] ?? 0 ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-lg bg-yellow-100 text-yellow-600 mr-3">
                <i class="fas fa-clock text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Menunggu</p>
                <p class="text-xl font-bold"><?= ($stats['submitted'] ?? 0) + ($stats['draft'] ?? 0) + ($stats['verified_by_instruktur'] ?? 0) ?></p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-2 rounded-lg bg-orange-100 text-orange-600 mr-3">
                <i class="fas fa-edit text-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">Revisi</p>
                <p class="text-xl font-bold"><?= $stats['revision'] ?? 0 ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Tasks Aktif -->
<?php if (!empty($tasks)): ?>
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">
            <i class="fas fa-list-check mr-2 text-blue-600"></i>
            Task Aktif
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <?php foreach ($tasks as $task): ?>
                <?php
                $progressSummary = (new \App\Models\PklTaskModel())->getProgressSummary($task['id']);
                $total = $progressSummary['total'];
                if ($total > 0) {
                    $weightedSum = ($progressSummary['submitted'] * 50) + ($progressSummary['verified_by_instruktur'] * 80) + ($progressSummary['approved'] * 100);
                    $progressPct = round($weightedSum / $total);
                } else {
                    $progressPct = 0;
                }

                if ($progressPct >= 100) {
                    $barColor = 'bg-green-500';
                } elseif ($progressPct >= 80) {
                    $barColor = 'bg-blue-500';
                } elseif ($progressPct >= 50) {
                    $barColor = 'bg-yellow-500';
                } else {
                    $barColor = 'bg-gray-400';
                }

                $remaining = $total - $progressSummary['approved'];
                ?>
                <a href="<?= base_url('siswa/jurnal-pkl/task/' . $task['id']); ?>"
                    class="block bg-white rounded-lg shadow hover:shadow-md transition-shadow p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-gray-800 truncate"><?= esc($task['judul']) ?></h3>
                            <?php if (!empty($task['kategori_nama'])): ?>
                                <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full
                        <?= match ($task['kategori_nama']) {
                            'Desain' => 'bg-purple-100 text-purple-700',
                            'Programming' => 'bg-blue-100 text-blue-700',
                            'Administrasi' => 'bg-green-100 text-green-700',
                            'Marketing' => 'bg-orange-100 text-orange-700',
                            default => 'bg-gray-100 text-gray-600'
                        } ?>"><?= esc($task['kategori_nama']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span><?= $remaining ?> progress tersisa</span>
                            <span><?= $progressPct ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="<?= $barColor ?> h-1.5 rounded-full" style="width: <?= $progressPct ?>%"></div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>


<!-- Print Section -->
<div class="bg-white rounded-lg shadow p-5 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-3">
        <i class="fas fa-print mr-2 text-blue-600"></i>
        Cetak Laporan
    </h2>
    <p class="text-sm text-gray-500 mb-4">Cetak Jurnal Kegiatan PKL atau Catatan Kegiatan PKL</p>
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="<?= base_url('siswa/jurnal-pkl/cetak-jurnal/' . date('Y') . '/1'); ?>" target="_blank"
            class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
            <i class="fas fa-calendar mr-2"></i>
            Jurnal Kegiatan PKL
        </a>
        <?php if (!empty($tasks)): ?>
            <a href="<?= base_url('siswa/jurnal-pkl/cetak-catatan/' . implode('-', array_column($tasks, 'id'))); ?>"
                target="_blank"
                class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium">
                <i class="fas fa-clipboard mr-2"></i>
                Catatan Kegiatan PKL
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Hari Ini -->
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-500 to-blue-600">
        <h2 class="text-lg font-semibold text-white">
            <i class="fas fa-calendar-day mr-2"></i>
            Hari Ini
        </h2>
        <p class="text-blue-100 text-sm">
            <?= date('d') . ' ' . $bulanIndo[(int) date('m')] . ' ' . date('Y') . ' &mdash; ' . $hariIndo[date('l')] ?>
        </p>
    </div>

    <?php if (empty($todayProgress)): ?>
        <div class="p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
            </div>
            <p class="text-gray-600 font-medium">Belum ada aktivitas hari ini</p>
            <p class="text-gray-400 text-sm mt-1">Mulai catat kegiatan PKL Anda</p>
            <a href="<?= base_url('siswa/jurnal-pkl/tambah'); ?>"
                class="inline-flex items-center mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                <i class="fas fa-plus mr-2"></i>
                Tambah Aktivitas
            </a>
        </div>
    <?php else: ?>
        <div class="divide-y divide-gray-100">
            <?php foreach ($todayProgress as $p): ?>
                <div class="px-5 py-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-1">
                            <?php if ($p['status'] === 'approved'): ?>
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600">
                                    <i class="fas fa-check text-sm"></i>
                                </span>
                            <?php elseif ($p['status'] === 'verified_by_instruktur'): ?>
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600">
                                    <i class="fas fa-check-double text-sm"></i>
                                </span>
                            <?php elseif ($p['status'] === 'submitted'): ?>
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-600">
                                    <i class="fas fa-clock text-sm"></i>
                                </span>
                            <?php elseif ($p['status'] === 'revision'): ?>
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600">
                                    <i class="fas fa-edit text-sm"></i>
                                </span>
                            <?php else: ?>
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-500">
                                    <i class="fas fa-pen text-sm"></i>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs px-2 py-0.5 rounded-full
                            <?= match ($p['kategori_nama'] ?? '') {
                                'Desain' => 'bg-purple-100 text-purple-700',
                                'Programming' => 'bg-blue-100 text-blue-700',
                                'Administrasi' => 'bg-green-100 text-green-700',
                                'Marketing' => 'bg-orange-100 text-orange-700',
                                default => 'bg-gray-100 text-gray-600'
                            } ?>"><?= esc($p['kategori_nama'] ?? 'Lainnya') ?></span>
                                <span class="text-xs text-gray-400">/</span>
                                <span class="text-xs font-medium text-gray-700"><?= esc($p['nama_task']) ?></span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2"><?= esc($p['deskripsi']) ?></p>
                            <div class="flex items-center gap-3 mt-2">
                                <?php if ($p['foto']): ?>
                                    <span class="text-xs text-blue-500"><i class="fas fa-camera mr-1"></i>Foto</span>
                                <?php endif; ?>
                                <?php if ($p['catatan_pembimbing']): ?>
                                    <span class="text-xs text-orange-500"><i class="fas fa-comment mr-1"></i>Catatan</span>
                                <?php endif; ?>
                                <span class="text-xs text-gray-400">
                                    <?= match ($p['status']) {
                                        'approved' => 'Disetujui',
                                        'verified_by_instruktur' => 'Diverifikasi Instruktur',
                                        'submitted' => 'Menunggu',
                                        'revision' => 'Revisi',
                                        default => 'Draft'
                                    } ?>
                                </span>
                            </div>
                        </div>
                        <div class="flex-shrink-0 flex items-center gap-1">
                            <?php if ($p['status'] !== 'approved'): ?>
                                <form action="<?= base_url('siswa/jurnal-pkl/hapus-progress/' . $p['id']); ?>" method="POST" class="inline">
                                    <?= csrf_field(); ?>
                                    <button type="submit" onclick="return confirm('Yakin ingin menghapus progress ini?')" title="Hapus"
                                        class="px-2 py-1 bg-red-50 text-red-500 rounded-lg hover:bg-red-100 text-xs transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Timeline -->
<div class="mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-3">
        <i class="fas fa-clock mr-2 text-blue-600"></i>
        Timeline
    </h2>

    <?php if (empty($timeline)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500">Belum ada riwayat aktivitas</p>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($timeline as $day): ?>
                <?php
                $dateObj = new DateTime($day['tanggal']);
                $dayName = $hariIndo[$dateObj->format('l')];
                $dateStr = $dateObj->format('d') . ' ' . $bulanIndo[(int) $dateObj->format('m')] . ' ' . $dateObj->format('Y');
                $isToday = $day['tanggal'] === date('Y-m-d');

                $allApproved = ($day['total_aktivitas'] == $day['approved']);
                $hasRevision = ($day['revision'] > 0);
                ?>
                <a href="<?= base_url('siswa/jurnal-pkl/hari/' . $day['tanggal']); ?>"
                    class="block bg-white rounded-lg shadow hover:shadow-md transition-shadow overflow-hidden">
                    <div
                        class="px-5 py-4 flex items-center gap-4 <?= $isToday ? 'bg-blue-50 border-l-4 border-blue-500' : '' ?>">
                        <div class="flex-shrink-0 w-14 text-center">
                            <p class="text-xs text-gray-500"><?= $dayName ?></p>
                            <p class="text-2xl font-bold <?= $isToday ? 'text-blue-600' : 'text-gray-800' ?>">
                                <?= $dateObj->format('d') ?>
                            </p>
                            <p class="text-xs text-gray-400"><?= $bulanIndo[(int) $dateObj->format('m')] ?></p>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-800"><?= $day['total_aktivitas'] ?>
                                    aktivitas</span>
                                <?php if ($allApproved): ?>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <i class="fas fa-check-circle mr-1"></i>Disetujui
                                    </span>
                                <?php elseif ($hasRevision): ?>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                        <i class="fas fa-edit mr-1"></i>Revisi
                                    </span>
                                <?php else: ?>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        <i class="fas fa-clock mr-1"></i>Menunggu
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>