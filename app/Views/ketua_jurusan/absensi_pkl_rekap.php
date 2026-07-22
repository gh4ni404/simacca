<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="h-full">
    <?= render_flash_message() ?>

    <!-- Breadcrumb -->
    <nav class="mb-4 text-sm text-gray-500">
        <a href="<?= base_url('ketua-jurusan/dashboard') ?>" class="hover:text-blue-600">Dashboard</a>
        <span class="mx-2">/</span>
        <a href="<?= base_url('ketua-jurusan/absensi-pkl') ?>" class="hover:text-blue-600">Absensi PKL</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">Rekap — <?= date('d/m/Y', strtotime($absensiInfo['tanggal'])) ?></span>
    </nav>

    <!-- Header Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-800">
                    Rekap Absensi PKL — <?= date('d/m/Y', strtotime($absensiInfo['tanggal'])) ?>
                </h1>
                <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-500">
                    <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded-full">
                        <i class="fas fa-user-tie mr-1"></i> <?= esc($absensiInfo['nama_pembimbing']) ?>
                    </span>
                    <span class="bg-green-50 text-green-700 px-2 py-1 rounded-full">
                        <i class="fas fa-building mr-1"></i> <?= esc($absensiInfo['nama_perusahaan']) ?>
                    </span>
                </div>
            </div>
            <a href="<?= base_url('ketua-jurusan/absensi-pkl') ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-gray-800"><?= $stats['total'] ?></div>
            <div class="text-xs text-gray-500">Total</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-green-200 p-4 text-center">
            <div class="text-2xl font-bold text-green-600"><?= $stats['hadir'] ?></div>
            <div class="text-xs text-gray-500">Hadir</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-blue-200 p-4 text-center">
            <div class="text-2xl font-bold text-blue-600"><?= $stats['izin'] ?></div>
            <div class="text-xs text-gray-500">Izin</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-yellow-200 p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600"><?= $stats['sakit'] ?></div>
            <div class="text-xs text-gray-500">Sakit</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-red-200 p-4 text-center">
            <div class="text-2xl font-bold text-red-600"><?= $stats['alpa'] ?></div>
            <div class="text-xs text-gray-500">Alpa</div>
        </div>
    </div>

    <?php if (empty($details)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-8 text-center border border-gray-200">
        <p class="text-gray-500">Tidak ada data absensi siswa jurusan <?= esc($jurusan) ?> pada sesi ini</p>
    </div>
    <?php else: ?>

    <!-- Detail Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama Siswa</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">NIS</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kelas</th>
                        <th class="px-5 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Keterangan</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($details as $i => $detail):
                        $statusBadge = match($detail['status']) {
                            'hadir' => 'bg-green-100 text-green-800',
                            'izin' => 'bg-blue-100 text-blue-800',
                            'sakit' => 'bg-yellow-100 text-yellow-800',
                            'alpa' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                        $statusLabel = match($detail['status']) {
                            'hadir' => 'Hadir',
                            'izin' => 'Izin',
                            'sakit' => 'Sakit',
                            'alpa' => 'Alpa',
                            default => ucfirst($detail['status']),
                        };
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4 text-sm text-gray-500"><?= $i + 1 ?></td>
                        <td class="px-5 py-4 text-sm font-medium text-gray-800"><?= esc($detail['nama_siswa']) ?></td>
                        <td class="px-5 py-4 text-sm text-gray-600"><?= esc($detail['nis']) ?></td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                <?= esc($detail['nama_kelas']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded <?= $statusBadge ?>">
                                <?= $statusLabel ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600 max-w-xs truncate">
                            <?= esc($detail['keterangan'] ?? '-') ?>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-500">
                            <?= !empty($detail['waktu_absen']) ? date('H:i', strtotime($detail['waktu_absen'])) : '-' ?>
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
