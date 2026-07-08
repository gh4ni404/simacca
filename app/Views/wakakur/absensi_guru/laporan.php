<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-chart-bar text-blue-600 mr-2"></i>Laporan Absensi Guru
            </h2>
            <p class="text-gray-600">Laporan historis kehadiran guru</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <a href="<?= base_url('wakakur/absensi-guru') ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center" id="btnExport">
                <i class="fas fa-file-excel mr-2"></i> Export Excel
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="text-center">
                <p class="text-sm text-blue-600 uppercase font-semibold mb-1">Total Record</p>
                <p class="text-2xl font-bold text-blue-800"><?= $stats['total_records'] ?? 0 ?></p>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="text-center">
                <p class="text-sm text-green-600 uppercase font-semibold mb-1">Hadir</p>
                <p class="text-2xl font-bold text-green-800"><?= $stats['total_hadir'] ?? 0 ?></p>
            </div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="text-center">
                <p class="text-sm text-yellow-600 uppercase font-semibold mb-1">Terlambat</p>
                <p class="text-2xl font-bold text-yellow-800"><?= $stats['total_terlambat'] ?? 0 ?></p>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="text-center">
                <p class="text-sm text-blue-600 uppercase font-semibold mb-1">Izin</p>
                <p class="text-2xl font-bold text-blue-800"><?= $stats['total_izin'] ?? 0 ?></p>
            </div>
        </div>
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
            <div class="text-center">
                <p class="text-sm text-indigo-600 uppercase font-semibold mb-1">Sakit</p>
                <p class="text-2xl font-bold text-indigo-800"><?= $stats['total_sakit'] ?? 0 ?></p>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="text-center">
                <p class="text-sm text-red-600 uppercase font-semibold mb-1">Alpha</p>
                <p class="text-2xl font-bold text-red-800"><?= $stats['total_alpha'] ?? 0 ?></p>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-filter text-blue-600 mr-2"></i>
            <h3 class="text-lg font-semibold text-gray-800">Filter Laporan</h3>
        </div>
        <form method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="<?= $filters['tanggal_mulai'] ?? '' ?>"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" value="<?= $filters['tanggal_akhir'] ?? '' ?>"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Guru</label>
                <select name="guru_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Guru</option>
                    <?php foreach ($guruList as $guru): ?>
                        <option value="<?= $guru['id'] ?>" <?= ($filters['guru_id'] ?? '') == $guru['id'] ? 'selected' : '' ?>>
                            <?= esc($guru['nama_lengkap']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="hadir" <?= ($filters['status'] ?? '') == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                    <option value="terlambat" <?= ($filters['status'] ?? '') == 'terlambat' ? 'selected' : '' ?>>Terlambat</option>
                    <option value="izin" <?= ($filters['status'] ?? '') == 'izin' ? 'selected' : '' ?>>Izin</option>
                    <option value="sakit" <?= ($filters['status'] ?? '') == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                    <option value="alpha" <?= ($filters['status'] ?? '') == 'alpha' ? 'selected' : '' ?>>Alpha</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                <?php if (!empty($filters['tanggal_mulai']) || !empty($filters['tanggal_akhir']) || !empty($filters['guru_id']) || !empty($filters['status'])): ?>
                    <a href="<?= base_url('wakakur/absensi-guru/laporan') ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Guru</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($absensiList)): ?>
                    <?php foreach ($absensiList as $index => $absensi): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $index + 1 ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= date('d/m/Y', strtotime($absensi['tanggal'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= esc($absensi['nama_guru']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if ($absensi['check_in']): ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <?= date('H:i', strtotime($absensi['check_in'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if ($absensi['check_out']): ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?= date('H:i', strtotime($absensi['check_out'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php
                                $badgeColors = [
                                    'hadir' => 'bg-green-100 text-green-800',
                                    'terlambat' => 'bg-yellow-100 text-yellow-800',
                                    'izin' => 'bg-blue-100 text-blue-800',
                                    'sakit' => 'bg-indigo-100 text-indigo-800',
                                    'alpha' => 'bg-red-100 text-red-800'
                                ];
                                $colorClass = $badgeColors[$absensi['status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $colorClass ?>">
                                    <?= ucfirst($absensi['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?= esc($absensi['keterangan_masuk'] ?? '-') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 block"></i>
                            <p>Tidak ada data absensi</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pager && count($absensiList) > 0): ?>
        <div class="mt-6">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('btnExport').addEventListener('click', function() {
    // Get current filter values
    const urlParams = new URLSearchParams(window.location.search);
    const params = {
        tanggal_mulai: urlParams.get('tanggal_mulai') || '',
        tanggal_akhir: urlParams.get('tanggal_akhir') || '',
        guru_id: urlParams.get('guru_id') || '',
        status: urlParams.get('status') || ''
    };
    
    // Build query string
    const queryString = Object.keys(params)
        .filter(key => params[key])
        .map(key => `${key}=${encodeURIComponent(params[key])}`)
        .join('&');
    
    // Redirect to export URL
    window.location.href = `<?= base_url('wakakur/absensi-guru/export-excel') ?>?${queryString}`;
});
</script>

<?= $this->endSection() ?>
