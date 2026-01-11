<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-envelope mr-2 text-yellow-600"></i>
                    Pengajuan Izin
                </h1>
                <p class="text-gray-600 mt-1">Kelola pengajuan izin Anda</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="<?= base_url('siswa/izin/tambah'); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Ajukan Izin Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Message -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
        <i class="fas fa-check-circle text-xl mr-3"></i>
        <span><?= session()->getFlashdata('success'); ?></span>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
        <i class="fas fa-exclamation-circle text-xl mr-3"></i>
        <span><?= session()->getFlashdata('error'); ?></span>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pending</p>
                    <p class="text-2xl font-bold"><?= $countPending; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Disetujui</p>
                    <p class="text-2xl font-bold"><?= $countDisetujui; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Ditolak</p>
                    <p class="text-2xl font-bold"><?= $countDitolak; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="<?= base_url('siswa/izin'); ?>" 
                   class="<?= !$status ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    <i class="fas fa-list mr-2"></i>
                    Semua Izin
                    <span class="ml-2 bg-gray-200 text-gray-700 py-0.5 px-2 rounded-full text-xs">
                        <?= count($izinData); ?>
                    </span>
                </a>
                <a href="<?= base_url('siswa/izin?status=pending'); ?>" 
                   class="<?= $status == 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    <i class="fas fa-clock mr-2"></i>
                    Pending
                    <span class="ml-2 bg-yellow-200 text-yellow-700 py-0.5 px-2 rounded-full text-xs">
                        <?= $countPending; ?>
                    </span>
                </a>
                <a href="<?= base_url('siswa/izin?status=disetujui'); ?>" 
                   class="<?= $status == 'disetujui' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    Disetujui
                    <span class="ml-2 bg-green-200 text-green-700 py-0.5 px-2 rounded-full text-xs">
                        <?= $countDisetujui; ?>
                    </span>
                </a>
                <a href="<?= base_url('siswa/izin?status=ditolak'); ?>" 
                   class="<?= $status == 'ditolak' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    <i class="fas fa-times-circle mr-2"></i>
                    Ditolak
                    <span class="ml-2 bg-red-200 text-red-700 py-0.5 px-2 rounded-full text-xs">
                        <?= $countDitolak; ?>
                    </span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Izin List -->
    <div class="space-y-4">
        <?php if (empty($izinData)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            <i class="fas fa-inbox text-6xl mb-4"></i>
            <p class="text-lg">Tidak ada data izin</p>
            <p class="text-sm mt-2">
                <?php if ($status == 'pending'): ?>
                    Tidak ada izin yang menunggu persetujuan
                <?php elseif ($status == 'disetujui'): ?>
                    Belum ada izin yang disetujui
                <?php elseif ($status == 'ditolak'): ?>
                    Belum ada izin yang ditolak
                <?php else: ?>
                    Anda belum pernah mengajukan izin
                <?php endif; ?>
            </p>
            <a href="<?= base_url('siswa/izin/tambah'); ?>" class="inline-flex items-center mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Ajukan Izin Sekarang
            </a>
        </div>
        <?php else: ?>
            <?php foreach ($izinData as $izin): ?>
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <h3 class="text-lg font-semibold text-gray-800 capitalize"><?= esc($izin['jenis_izin']); ?></h3>
                                <span class="ml-3">
                                    <?php if ($izin['status'] == 'pending'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Menunggu Persetujuan
                                    </span>
                                    <?php elseif ($izin['status'] == 'disetujui'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Disetujui
                                    </span>
                                    <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Ditolak
                                    </span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Diajukan pada: <?= date('d F Y H:i', strtotime($izin['created_at'])); ?>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">
                                <i class="fas fa-calendar-day mr-2 text-blue-500"></i>
                                <span class="font-medium">Tanggal Izin:</span>
                            </p>
                            <p class="text-sm text-gray-800 ml-6">
                                <?= date('d F Y', strtotime($izin['tanggal'])); ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">
                                <i class="fas fa-tag mr-2 text-purple-500"></i>
                                <span class="font-medium">Jenis Izin:</span>
                            </p>
                            <p class="text-sm text-gray-800 ml-6 capitalize">
                                <?= esc($izin['jenis_izin']); ?>
                            </p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-comment-alt mr-2 text-green-500"></i>
                            <span class="font-medium">Alasan:</span>
                        </p>
                        <p class="text-sm text-gray-800 ml-6 bg-gray-50 p-3 rounded-lg">
                            <?= esc($izin['alasan']); ?>
                        </p>
                    </div>

                    <?php if (!empty($izin['berkas'])): ?>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-paperclip mr-2 text-yellow-500"></i>
                            <span class="font-medium">Dokumen Pendukung:</span>
                        </p>
                        <a href="<?= base_url('writable/uploads/izin/' . $izin['berkas']); ?>" target="_blank" 
                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 ml-6">
                            <i class="fas fa-file mr-2"></i>
                            Lihat Dokumen
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if ($izin['status'] != 'pending'): ?>
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 text-lg mr-3 mt-1"></i>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-700 mb-1">
                                    Status Persetujuan:
                                    <?php if ($izin['status'] == 'disetujui'): ?>
                                        <span class="text-green-600">Izin Anda telah disetujui</span>
                                    <?php else: ?>
                                        <span class="text-red-600">Izin Anda ditolak</span>
                                    <?php endif; ?>
                                </p>
                                <?php if (!empty($izin['catatan'])): ?>
                                <p class="text-sm text-gray-600 mt-1">
                                    <span class="font-medium">Catatan Wali Kelas:</span>
                                    <span class="block mt-1 ml-4 bg-blue-50 p-2 rounded"><?= esc($izin['catatan']); ?></span>
                                </p>
                                <?php endif; ?>
                                <?php if (!empty($izin['disetujui_oleh'])): ?>
                                <p class="text-xs text-gray-500 mt-2">
                                    Diproses oleh: <?= esc($izin['approved_by_username']); ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Info Footer -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Informasi:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Ajukan izin maksimal <strong>1 hari sebelum</strong> tanggal izin</li>
                    <li>Pastikan mengisi alasan dengan jelas dan lengkap</li>
                    <li>Upload dokumen pendukung (surat keterangan) jika diperlukan</li>
                    <li>Izin akan diproses oleh wali kelas Anda</li>
                    <li>Anda akan menerima notifikasi saat izin disetujui/ditolak</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
