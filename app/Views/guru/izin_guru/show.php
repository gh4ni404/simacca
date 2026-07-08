<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="<?= base_url('guru/izin-guru') ?>" 
               class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?= $pageTitle ?></h1>
                <p class="mt-1 text-sm text-gray-500"><?= $pageDescription ?></p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Status Header -->
        <div class="px-6 py-4 bg-gradient-to-r 
            <?php
            switch($izin['status']) {
                case 'disetujui': echo 'from-green-500 to-green-600'; break;
                case 'ditolak': echo 'from-red-500 to-red-600'; break;
                default: echo 'from-yellow-500 to-yellow-600';
            }
            ?>">
            <div class="flex items-center justify-between text-white">
                <div>
                    <h2 class="text-lg font-semibold">Detail Pengajuan Izin</h2>
                    <p class="text-sm opacity-90">ID: #<?= $izin['id'] ?></p>
                </div>
                <div class="text-right">
                    <span class="px-4 py-2 bg-white bg-opacity-30 rounded-full text-sm font-semibold">
                        <?= ucfirst($izin['status']) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Izin</label>
                        <p class="text-lg font-semibold text-gray-900">
                            <span class="px-3 py-1 rounded-full text-sm
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
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                        <p class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-calendar mr-2 text-blue-500"></i>
                            <?= date('d F Y', strtotime($izin['tanggal_mulai'])) ?>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Selesai</label>
                        <p class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-calendar mr-2 text-blue-500"></i>
                            <?= date('d F Y', strtotime($izin['tanggal_selesai'])) ?>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Durasi</label>
                        <?php
                        $days = (strtotime($izin['tanggal_selesai']) - strtotime($izin['tanggal_mulai'])) / 86400 + 1;
                        ?>
                        <p class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-clock mr-2 text-blue-500"></i>
                            <?= $days ?> Hari
                        </p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Diajukan Pada</label>
                        <p class="text-lg font-semibold text-gray-900">
                            <?= date('d F Y, H:i', strtotime($izin['created_at'])) ?> WIB
                        </p>
                    </div>

                    <?php if ($izin['status'] !== 'pending'): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Diproses Oleh</label>
                            <p class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-user mr-2 text-blue-500"></i>
                                <?= $izin['approver_name'] ?? '-' ?>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Diproses</label>
                            <p class="text-lg font-semibold text-gray-900">
                                <?= $izin['tanggal_disetujui'] ? date('d F Y, H:i', strtotime($izin['tanggal_disetujui'])) . ' WIB' : '-' ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if ($izin['berkas']): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Berkas Pendukung</label>
                            <a href="<?= base_url('writable/uploads/izin_guru/' . $izin['berkas']) ?>" 
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-file-download mr-2"></i>
                                Lihat Berkas
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Alasan -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <label class="block text-sm font-medium text-gray-500 mb-2">Alasan/Keterangan</label>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-900 whitespace-pre-line"><?= esc($izin['alasan']) ?></p>
                </div>
            </div>

            <!-- Catatan Persetujuan -->
            <?php if ($izin['catatan_persetujuan']): ?>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <label class="block text-sm font-medium text-gray-500 mb-2">
                        Catatan dari <?= $izin['status'] === 'disetujui' ? 'Persetujuan' : 'Penolakan' ?>
                    </label>
                    <div class="bg-<?= $izin['status'] === 'disetujui' ? 'green' : 'red' ?>-50 border border-<?= $izin['status'] === 'disetujui' ? 'green' : 'red' ?>-200 rounded-lg p-4">
                        <p class="text-gray-900 whitespace-pre-line"><?= esc($izin['catatan_persetujuan']) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="mt-6 pt-6 border-t border-gray-200 flex gap-4">
                <a href="<?= base_url('guru/izin-guru') ?>"
                   class="px-6 py-3 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>

                <?php if ($izin['status'] === 'pending'): ?>
                    <button onclick="confirmDelete(<?= $izin['id'] ?>)" 
                            class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Pengajuan
                    </button>
                <?php endif; ?>
            </div>
        </div>
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
