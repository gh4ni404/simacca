<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?= $pageTitle ?></h1>
                <p class="mt-1 text-sm text-gray-500"><?= $pageDescription ?></p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="<?= base_url('guru/izin-guru/create') ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Ajukan Izin Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Info Card -->
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
            <div>
                <h3 class="font-semibold text-blue-900">Informasi Pengajuan Izin</h3>
                <ul class="mt-2 text-sm text-blue-800 space-y-1">
                    <li>• Ajukan izin minimal 1 hari sebelumnya (untuk izin terencana)</li>
                    <li>• Upload surat keterangan dokter untuk izin sakit lebih dari 2 hari</li>
                    <li>• Status pengajuan akan diproses oleh Wakakur</li>
                    <li>• Anda akan menerima notifikasi ketika izin disetujui/ditolak</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <?php
        $totalPending = count(array_filter($izinList, fn($i) => $i['status'] === 'pending'));
        $totalDisetujui = count(array_filter($izinList, fn($i) => $i['status'] === 'disetujui'));
        $totalDitolak = count(array_filter($izinList, fn($i) => $i['status'] === 'ditolak'));
        ?>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Pengajuan</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($izinList) ?></p>
                </div>
                <i class="fas fa-file-alt text-3xl text-gray-400"></i>
            </div>
        </div>

        <div class="bg-yellow-50 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-yellow-700">Menunggu</p>
                    <p class="text-2xl font-bold text-yellow-600"><?= $totalPending ?></p>
                </div>
                <i class="fas fa-clock text-3xl text-yellow-400"></i>
            </div>
        </div>

        <div class="bg-green-50 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-700">Disetujui</p>
                    <p class="text-2xl font-bold text-green-600"><?= $totalDisetujui ?></p>
                </div>
                <i class="fas fa-check-circle text-3xl text-green-400"></i>
            </div>
        </div>

        <div class="bg-red-50 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-700">Ditolak</p>
                    <p class="text-2xl font-bold text-red-600"><?= $totalDitolak ?></p>
                </div>
                <i class="fas fa-times-circle text-3xl text-red-400"></i>
            </div>
        </div>
    </div>

    <!-- Izin List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Riwayat Pengajuan Izin</h3>
        </div>

        <?php if (empty($izinList)): ?>
            <div class="text-center py-12">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Pengajuan Izin</h3>
                <p class="text-gray-500 mb-4">Anda belum pernah mengajukan izin</p>
                <a href="<?= base_url('guru/izin-guru/create') ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Ajukan Izin Sekarang
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Izin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diproses Oleh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($izinList as $izin): ?>
                            <tr class="hover:bg-gray-50">
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
                                        <?= esc(substr($izin['alasan'], 0, 100)) ?>
                                        <?= strlen($izin['alasan']) > 100 ? '...' : '' ?>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $izin['approver_name'] ?? '-' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="<?= base_url('guru/izin-guru/show/' . $izin['id']) ?>" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <?php if ($izin['status'] === 'pending'): ?>
                                        <button onclick="confirmDelete(<?= $izin['id'] ?>)" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Hapus Pengajuan Izin?</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus pengajuan izin ini? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="flex gap-4 px-4 py-3">
                <button onclick="closeDeleteModal()" 
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    form.action = '<?= base_url('guru/izin-guru/delete') ?>/' + id;
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>

<?= $this->endSection() ?>
