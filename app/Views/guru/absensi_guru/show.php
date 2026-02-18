<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-file-alt text-blue-600"></i> Detail Absensi
            </h1>
            <p class="text-gray-600 mt-1"><?= date('l, d F Y', strtotime($absensi['tanggal'])) ?></p>
        </div>
        <div>
            <a href="<?= base_url('guru/absensi-guru?tab=dashboard') ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors inline-flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info Card -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4">
                    <h3 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>Informasi Absensi
                    </h3>
                </div>
                <div class="p-6">
                    <!-- Status and Date Header -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h6 class="text-gray-500 text-sm font-medium mb-2">Status Kehadiran</h6>
                            <?php
                            $badgeConfig = [
                                'hadir' => ['bg' => 'bg-green-500', 'icon' => 'fa-check-circle'],
                                'terlambat' => ['bg' => 'bg-yellow-500', 'icon' => 'fa-exclamation-triangle'],
                                'izin' => ['bg' => 'bg-blue-500', 'icon' => 'fa-file-alt'],
                                'sakit' => ['bg' => 'bg-purple-500', 'icon' => 'fa-medkit'],
                                'alpha' => ['bg' => 'bg-red-500', 'icon' => 'fa-times-circle']
                            ];
                            $config = $badgeConfig[$absensi['status']] ?? ['bg' => 'bg-gray-500', 'icon' => 'fa-circle'];
                            ?>
                            <span class="inline-flex items-center gap-2 px-4 py-2 <?= $config['bg'] ?> text-white rounded-lg text-lg font-semibold">
                                <i class="fas <?= $config['icon'] ?>"></i>
                                <?= ucfirst($absensi['status']) ?>
                            </span>
                        </div>
                        <div>
                            <h6 class="text-gray-500 text-sm font-medium mb-2">Tanggal</h6>
                            <p class="text-2xl font-bold text-gray-800"><?= date('d F Y', strtotime($absensi['tanggal'])) ?></p>
                            <p class="text-sm text-gray-600"><?= date('l', strtotime($absensi['tanggal'])) ?></p>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <!-- Check-In and Check-Out Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Check-In Card -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="bg-green-500 p-3 rounded-lg">
                                    <i class="fas fa-sign-in-alt text-white text-xl"></i>
                                </div>
                                <h6 class="text-green-700 font-semibold text-lg">Check-In</h6>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-1">Waktu Masuk</p>
                                <p class="text-3xl font-bold text-green-800">
                                    <?= $absensi['check_in'] ? date('H:i:s', strtotime($absensi['check_in'])) : '-' ?>
                                </p>
                            </div>

                            <?php if (isset($absensi['keterangan_masuk']) && $absensi['keterangan_masuk']): ?>
                                <div class="mb-4 p-3 bg-white rounded-lg border border-green-200">
                                    <p class="text-sm font-medium text-gray-700 mb-1">Keterangan:</p>
                                    <p class="text-gray-800"><?= esc($absensi['keterangan_masuk']) ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($absensi['latitude_check_in']) && isset($absensi['longitude_check_in']) && $absensi['latitude_check_in'] && $absensi['longitude_check_in']): ?>
                                <div class="mt-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Lokasi:</p>
                                    <p class="text-xs text-gray-600 mb-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        <?= number_format($absensi['latitude_check_in'], 6) ?>, <?= number_format($absensi['longitude_check_in'], 6) ?>
                                    </p>
                                    <a href="https://www.google.com/maps?q=<?= $absensi['latitude_check_in'] ?>,<?= $absensi['longitude_check_in'] ?>" 
                                       target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-green-500 text-green-700 rounded-lg hover:bg-green-50 transition-colors text-sm font-medium">
                                        <i class="fas fa-map-marker-alt"></i> Lihat di Maps
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Check-Out Card -->
                        <div class="bg-gradient-to-br from-<?= $absensi['check_out'] ? 'blue' : 'gray' ?>-50 to-<?= $absensi['check_out'] ? 'indigo' : 'gray' ?>-50 border-2 border-<?= $absensi['check_out'] ? 'blue' : 'gray' ?>-200 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="bg-<?= $absensi['check_out'] ? 'blue' : 'gray' ?>-500 p-3 rounded-lg">
                                    <i class="fas fa-sign-out-alt text-white text-xl"></i>
                                </div>
                                <h6 class="text-<?= $absensi['check_out'] ? 'blue' : 'gray' ?>-700 font-semibold text-lg">Check-Out</h6>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-1">Waktu Keluar</p>
                                <?php if ($absensi['check_out']): ?>
                                    <p class="text-3xl font-bold text-blue-800">
                                        <?= date('H:i:s', strtotime($absensi['check_out'])) ?>
                                    </p>
                                <?php else: ?>
                                    <p class="text-2xl font-semibold text-gray-500">Belum check-out</p>
                                <?php endif; ?>
                            </div>

                            <?php if (isset($absensi['keterangan_keluar']) && $absensi['keterangan_keluar']): ?>
                                <div class="mb-4 p-3 bg-white rounded-lg border border-blue-200">
                                    <p class="text-sm font-medium text-gray-700 mb-1">Keterangan:</p>
                                    <p class="text-gray-800"><?= esc($absensi['keterangan_keluar']) ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($absensi['latitude_check_out']) && isset($absensi['longitude_check_out']) && $absensi['latitude_check_out'] && $absensi['longitude_check_out']): ?>
                                <div class="mt-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Lokasi:</p>
                                    <p class="text-xs text-gray-600 mb-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        <?= number_format($absensi['latitude_check_out'], 6) ?>, <?= number_format($absensi['longitude_check_out'], 6) ?>
                                    </p>
                                    <a href="https://www.google.com/maps?q=<?= $absensi['latitude_check_out'] ?>,<?= $absensi['longitude_check_out'] ?>" 
                                       target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-blue-500 text-blue-700 rounded-lg hover:bg-blue-50 transition-colors text-sm font-medium">
                                        <i class="fas fa-map-marker-alt"></i> Lihat di Maps
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Duration Info -->
                    <?php if ($absensi['check_in'] && $absensi['check_out']): ?>
                        <?php
                        $masuk = strtotime($absensi['check_in']);
                        $keluar = strtotime($absensi['check_out']);
                        $diff = $keluar - $masuk;
                        $hours = floor($diff / 3600);
                        $minutes = floor(($diff % 3600) / 60);
                        $totalMinutes = ($hours * 60) + $minutes;
                        ?>
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="bg-purple-500 p-4 rounded-lg">
                                        <i class="fas fa-hourglass-half text-white text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-purple-600 font-medium mb-1">Durasi Kerja</p>
                                        <p class="text-3xl font-bold text-purple-800">
                                            <?= $hours ?> jam <?= $minutes ?> menit
                                        </p>
                                        <p class="text-sm text-purple-600 mt-1"><?= $totalMinutes ?> menit total</p>
                                    </div>
                                </div>
                                <?php if ($totalMinutes >= 480): ?>
                                    <span class="px-4 py-2 bg-green-500 text-white rounded-full text-sm font-semibold">
                                        <i class="fas fa-check-circle mr-1"></i>Target Terpenuhi
                                    </span>
                                <?php else: ?>
                                    <span class="px-4 py-2 bg-orange-500 text-white rounded-full text-sm font-semibold">
                                        <i class="fas fa-info-circle mr-1"></i>Kurang <?= 480 - $totalMinutes ?> menit
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Photo Cards -->
        <div class="space-y-6">
            <!-- Check-In Photo -->
            <?php if (isset($absensi['foto_check_in']) && $absensi['foto_check_in']): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-4">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-camera mr-2"></i>Foto Check-In
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="relative group">
                            <?php
                            // Convert storage path to URL path
                            $photoUrl = str_replace('uploads/absensi_guru/', 'files/absensi-guru/', $absensi['foto_check_in']);
                            ?>
                            <img src="<?= base_url($photoUrl) ?>" 
                                 class="w-full rounded-lg shadow-md hover:shadow-xl transition-shadow cursor-pointer object-cover" 
                                 style="max-height: 400px;"
                                 alt="Foto Check-In"
                                 onerror="this.parentElement.innerHTML='<div class=\'bg-gray-100 rounded-lg p-8 text-center\'><i class=\'fas fa-image text-gray-400 text-5xl mb-3\'></i><p class=\'text-gray-500\'>Foto tidak dapat dimuat</p></div>'"
                                 onclick="showImage('<?= base_url($photoUrl) ?>')">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg flex items-center justify-center cursor-pointer"
                                 onclick="showImage('<?= base_url($photoUrl) ?>')">
                                <i class="fas fa-search-plus text-white text-3xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-center gap-2 text-gray-600">
                            <i class="fas fa-clock"></i>
                            <span class="font-medium">
                                <?= $absensi['check_in'] ? date('H:i:s', strtotime($absensi['check_in'])) : '' ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Check-Out Photo -->
            <?php if (isset($absensi['foto_check_out']) && $absensi['foto_check_out']): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-camera mr-2"></i>Foto Check-Out
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="relative group">
                            <?php
                            // Convert storage path to URL path
                            $photoUrl = str_replace('uploads/absensi_guru/', 'files/absensi-guru/', $absensi['foto_check_out']);
                            ?>
                            <img src="<?= base_url($photoUrl) ?>" 
                                 class="w-full rounded-lg shadow-md hover:shadow-xl transition-shadow cursor-pointer object-cover" 
                                 style="max-height: 400px;"
                                 alt="Foto Check-Out"
                                 onerror="this.parentElement.innerHTML='<div class=\'bg-gray-100 rounded-lg p-8 text-center\'><i class=\'fas fa-image text-gray-400 text-5xl mb-3\'></i><p class=\'text-gray-500\'>Foto tidak dapat dimuat</p></div>'"
                                 onclick="showImage('<?= base_url($photoUrl) ?>')">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg flex items-center justify-center cursor-pointer"
                                 onclick="showImage('<?= base_url($photoUrl) ?>')">
                                <i class="fas fa-search-plus text-white text-3xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-center gap-2 text-gray-600">
                            <i class="fas fa-clock"></i>
                            <span class="font-medium">
                                <?= $absensi['check_out'] ? date('H:i:s', strtotime($absensi['check_out'])) : '' ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ((!isset($absensi['foto_check_in']) || !$absensi['foto_check_in']) && (!isset($absensi['foto_check_out']) || !$absensi['foto_check_out'])): ?>
                <div class="bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 p-8 text-center">
                    <i class="fas fa-camera text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-600 font-medium">Tidak ada foto tersedia</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade hidden" id="imageModal" tabindex="-1" role="dialog" data-modal-overlay="imageModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                <h5 class="modal-title font-semibold">Preview Foto Absensi</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeModal('imageModal');"></button>
            </div>
            <div class="modal-body text-center p-6 bg-gray-50">
                <img id="previewImage" src="" class="max-w-full h-auto rounded-lg shadow-lg" alt="Preview">
            </div>
        </div>
    </div>
</div>

<script>
function showImage(url) {
    document.getElementById('previewImage').src = url;
    openModal('imageModal');
}
</script>

<style>
/* Modal Styles */
.modal.fade {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.75);
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow-y: auto;
}

.modal.hidden {
    display: none !important;
}

.modal-dialog {
    max-width: 90%;
    margin: 1.75rem auto;
}

.modal-lg {
    max-width: 800px;
}

.modal-content {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
}

.modal-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    border-radius: 0.75rem 0.75rem 0 0;
}

.modal-body {
    padding: 1.5rem;
}
</style>

<?= $this->endSection() ?>
