<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center">
            <a href="<?= base_url('guru/jurnal-pkl'); ?>" class="mr-4 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-book-open mr-2 text-blue-600"></i>
                    Detail Jurnal PKL
                </h1>
                <p class="text-gray-600 mt-1"><?= esc($jurnal['nama_kegiatan']); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-600 mb-1">
                        <i class="fas fa-user mr-2 text-blue-500"></i>
                        <span class="font-medium">Nama Kegiatan:</span>
                    </p>
                    <p class="text-sm text-gray-800 ml-6"><?= esc($jurnal['nama_kegiatan']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">
                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                        <span class="font-medium">Tanggal:</span>
                    </p>
                    <p class="text-sm text-gray-800 ml-6"><?= date('d F Y', strtotime($jurnal['tanggal'])); ?></p>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-600 mb-2">
                    <i class="fas fa-align-left mr-2 text-green-500"></i>
                    <span class="font-medium">Deskripsi:</span>
                </p>
                <p class="text-sm text-gray-800 bg-gray-50 p-4 rounded-lg">
                    <?= nl2br(esc($jurnal['deskripsi'])); ?>
                </p>
            </div>

            <?php if (!empty($jurnal['foto'])): ?>
            <div class="mb-6">
                <p class="text-sm text-gray-600 mb-2">
                    <i class="fas fa-camera mr-2 text-yellow-500"></i>
                    <span class="font-medium">Foto Dokumentasi:</span>
                </p>
                <img src="<?= base_url('files/jurnal-pkl/' . $jurnal['foto']); ?>"
                     class="max-h-96 rounded-lg shadow">
            </div>
            <?php endif; ?>

            <div class="border-t border-gray-200 pt-4">
                <p class="text-sm text-gray-600 mb-1">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    <span class="font-medium">Status:</span>
                    <?php if ($jurnal['status'] == 'pending'): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 ml-2">
                            <i class="fas fa-clock mr-1"></i>Pending
                        </span>
                    <?php elseif ($jurnal['status'] == 'disetujui'): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">
                            <i class="fas fa-check-circle mr-1"></i>Disetujui
                        </span>
                    <?php elseif ($jurnal['status'] == 'revisi'): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 ml-2">
                            <i class="fas fa-edit mr-1"></i>Revisi
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                            <i class="fas fa-times-circle mr-1"></i>Ditolak
                        </span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
