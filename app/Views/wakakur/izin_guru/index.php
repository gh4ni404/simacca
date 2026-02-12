<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900"><?= $pageTitle ?></h1>
        <p class="mt-1 text-sm text-gray-500"><?= $pageDescription ?></p>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Pengajuan</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['total'] ?></p>
                </div>
                <i class="fas fa-file-alt text-3xl text-gray-400"></i>
            </div>
        </div>

        <div class="bg-yellow-50 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-yellow-700">Menunggu Approval</p>
                    <p class="text-2xl font-bold text-yellow-600"><?= $stats['pending'] ?></p>
                </div>
                <i class="fas fa-clock text-3xl text-yellow-400"></i>
            </div>
        </div>

        <div class="bg-green-50 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-700">Disetujui</p>
                    <p class="text-2xl font-bold text-green-600"><?= $stats['disetujui'] ?></p>
                </div>
                <i class="fas fa-check-circle text-3xl text-green-400"></i>
            </div>
        </div>

        <div class="bg-red-50 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-700">Ditolak</p>
                    <p class="text-2xl font-bold text-red-600"><?= $stats['ditolak'] ?></p>
                </div>
                <i class="fas fa-times-circle text-3xl text-red-400"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" action="<?= base_url('wakakur/izin-guru') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="all" <?= $currentStatus === 'all' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                    <option value="disetujui" <?= $currentStatus === 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="ditolak" <?= $currentStatus === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                </select>
            </div>

            <div>
                <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <select name="bulan" id="bulan" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" 
                                <?= $currentBulan == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div>
                <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select name="tahun" id="tahun" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <?php for ($y = date('Y'); $y >= date('Y') - 2; $y--): ?>
                        <option value="<?= $y ?>" <?= $currentTahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" 
                        class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Izin List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Pengajuan Izin Guru</h3>
        </div>

        <?php if (empty($izinList)): ?>
            <div class="text-center py-12">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Tidak Ada Data</h3>
                <p class="text-gray-500">Belum ada pengajuan izin untuk filter yang dipilih</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($izinList as $izin): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= esc($izin['guru_nama']) ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                NIP: <?= esc($izin['nip']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= date('d M Y', strtotime($izin['tanggal_mulai'])) ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        s/d <?= date('d M Y', strtotime($izin['tanggal_selesai'])) ?>
                                    </div>
                                    <?php
                                    $days = (strtotime($izin['tanggal_selesai']) - strtotime($izin['tanggal_mulai'])) / 86400 + 1;
                                    ?>
                                    <div class="text-xs text-gray-500">
                                        (<?= $days ?> hari)
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        <?php
                                        switch($izin['jenis_izin']) {
                                            case 'sakit': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'cuti': echo 'bg-purple-100 text-purple-800'; break;
                                            case 'dinas_luar': echo 'bg-blue-100 text-blue-800'; break;
                                            default: echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= ucfirst(str_replace('_', ' ', $izin['jenis_izin'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 line-clamp-2">
                                        <?= esc(substr($izin['alasan'], 0, 80)) ?>
                                        <?= strlen($izin['alasan']) > 80 ? '...' : '' ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusClass = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'disetujui' => 'bg-green-100 text-green-800',
                                        'ditolak' => 'bg-red-100 text-red-800',
                                    ];
                                    ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass[$izin['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                        <?= ucfirst($izin['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2">
                                        <button onclick="viewDetail(<?= $izin['id'] ?>)" 
                                                class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($izin['status'] === 'pending'): ?>
                                            <button onclick="approveIzin(<?= $izin['id'] ?>, '<?= esc($izin['guru_nama']) ?>')" 
                                                    class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                            <button onclick="rejectIzin(<?= $izin['id'] ?>, '<?= esc($izin['guru_nama']) ?>')" 
                                                    class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4 text-center">Setujui Izin Guru?</h3>
            <div class="mt-2 px-7 py-3">
                <p id="approveGuruName" class="text-sm text-gray-600 text-center mb-4"></p>
                <form id="approveForm" method="POST">
                    <?= csrf_field() ?>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="catatan_persetujuan" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                              placeholder="Tambahkan catatan persetujuan..."></textarea>
                    <div class="flex gap-4 mt-4">
                        <button type="button" onclick="closeApproveModal()" 
                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Setujui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4 text-center">Tolak Izin Guru?</h3>
            <div class="mt-2 px-7 py-3">
                <p id="rejectGuruName" class="text-sm text-gray-600 text-center mb-4"></p>
                <form id="rejectForm" method="POST">
                    <?= csrf_field() ?>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="catatan_persetujuan" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                              placeholder="Jelaskan alasan penolakan..."></textarea>
                    <div class="flex gap-4 mt-4">
                        <button type="button" onclick="closeRejectModal()" 
                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function viewDetail(id) {
    window.location.href = '<?= base_url('wakakur/izin-guru/show') ?>/' + id;
}

function approveIzin(id, guruName) {
    const modal = document.getElementById('approveModal');
    const form = document.getElementById('approveForm');
    const nameEl = document.getElementById('approveGuruName');
    
    form.action = '<?= base_url('wakakur/izin-guru/approve') ?>/' + id;
    nameEl.textContent = 'Menyetujui izin untuk: ' + guruName;
    modal.classList.remove('hidden');
}

function closeApproveModal() {
    const modal = document.getElementById('approveModal');
    modal.classList.add('hidden');
}

function rejectIzin(id, guruName) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const nameEl = document.getElementById('rejectGuruName');
    
    form.action = '<?= base_url('wakakur/izin-guru/reject') ?>/' + id;
    nameEl.textContent = 'Menolak izin untuk: ' + guruName;
    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('approveModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeApproveModal();
});

document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>

<?= $this->endSection() ?>
