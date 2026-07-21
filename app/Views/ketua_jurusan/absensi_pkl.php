<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="h-full">
    <?= render_flash_message() ?>

    <!-- Breadcrumb -->
    <nav class="mb-4 text-sm text-gray-500">
        <a href="<?= base_url('ketua-jurusan/dashboard') ?>" class="hover:text-blue-600">Dashboard</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">Absensi PKL</span>
    </nav>

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Absensi PKL</h1>
            <p class="text-sm text-gray-500">Jurusan <?= esc($jurusan) ?> — Rekap absensi PKL siswa</p>
        </div>
    </div>

    <?php if (empty($absensiList)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-200">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-clipboard-check text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-700">Belum Ada Absensi PKL</h3>
        <p class="text-gray-500 mt-1">Rekap absensi PKL akan muncul di sini</p>
    </div>
    <?php else: ?>

    <!-- Absensi Sessions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Pembimbing</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Perusahaan</th>
                        <th class="px-5 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Hadir</th>
                        <th class="px-5 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Izin</th>
                        <th class="px-5 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Sakit</th>
                        <th class="px-5 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Alpa</th>
                        <th class="px-5 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Kehadiran</th>
                        <th class="px-5 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($absensiList as $i => $absensi):
                        $stats = $absensi['stats'] ?? [];
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4 text-sm text-gray-500"><?= $i + 1 ?></td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-gray-400 mr-2 text-sm"></i>
                                <span class="text-sm font-medium text-gray-800">
                                    <?= date('d/m/Y', strtotime($absensi['tanggal'])) ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700"><?= esc($absensi['nama_pembimbing'] ?? '-') ?></td>
                        <td class="px-5 py-4 text-sm text-gray-700 max-w-xs truncate"><?= esc($absensi['nama_perusahaan'] ?? '-') ?></td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-green-100 text-green-800">
                                <?= $stats['hadir'] ?? 0 ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                <?= $stats['izin'] ?? 0 ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">
                                <?= $stats['sakit'] ?? 0 ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-red-100 text-red-800">
                                <?= $stats['alpa'] ?? 0 ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <?php
                            $total = $stats['total'] ?? 1;
                            $hadir = $stats['hadir'] ?? 0;
                            $persen = round(($hadir / max($total, 1)) * 100);
                            ?>
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-green-500 h-1.5 rounded-full" style="width: <?= $persen ?>%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-700"><?= $persen ?>%</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="<?= base_url('ketua-jurusan/absensi-pkl/rekap/' . $absensi['id']) ?>"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
