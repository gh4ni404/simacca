<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-6">
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
                <a href="<?= base_url('siswa/jurnal-pkl/tambah'); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Jurnal
                </a>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-2xl font-bold"><?= $stats['total']; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pending</p>
                    <p class="text-2xl font-bold"><?= $stats['pending']; ?></p>
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
                    <p class="text-2xl font-bold"><?= $stats['disetujui']; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4">
                    <i class="fas fa-edit text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Revisi</p>
                    <p class="text-2xl font-bold"><?= $stats['revisi']; ?></p>
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
                    <p class="text-2xl font-bold"><?= $stats['ditolak']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($weeklyData)): ?>
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
        <i class="fas fa-book-open text-6xl mb-4"></i>
        <p class="text-lg">Belum ada jurnal PKL</p>
        <p class="text-sm mt-2">Mulai catat kegiatan PKL Anda</p>
        <a href="<?= base_url('siswa/jurnal-pkl/tambah'); ?>" class="inline-flex items-center mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Tambah Jurnal Sekarang
        </a>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Minggu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Entry</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($weeklyData as $week): ?>
                <?php
                    $weekStart = date('d M', strtotime($week['tanggal_mulai']));
                    $weekEnd = date('d M Y', strtotime($week['tanggal_selesai']));
                    $allDisetujui = ($week['total_entry'] == $week['disetujui']);
                    $hasRevisi = ($week['revisi'] > 0);
                    $hasPending = ($week['pending'] > 0);
                    $canPrint = $allDisetujui && $week['total_entry'] > 0;
                    $canEdit = $hasPending || $hasRevisi;
                ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900">Minggu ke-<?= $week['minggu_ke']; ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600">
                            <?= $weekStart; ?> - <?= $weekEnd; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="text-sm font-medium text-gray-900"><?= $week['total_entry']; ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <?php if ($allDisetujui): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Semua Disetujui
                            </span>
                        <?php elseif ($hasRevisi): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-edit mr-1"></i>
                                Ada Revisi
                            </span>
                        <?php elseif ($hasPending): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>
                                Menunggu
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>
                                Ditolak
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="<?= base_url('siswa/jurnal-pkl/detail/' . $week['tahun'] . '/' . $week['minggu_ke']); ?>"
                               class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-xs font-medium">
                                <i class="fas fa-eye mr-1"></i>
                                Detail
                            </a>
                            <?php if ($canPrint): ?>
                            <a href="<?= base_url('siswa/jurnal-pkl/cetak/' . $week['tahun'] . '/' . $week['minggu_ke']); ?>"
                               target="_blank"
                               class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-xs font-medium">
                                <i class="fas fa-print mr-1"></i>
                                Cetak
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Informasi:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Jurnal PKL dicatat <strong>setiap hari</strong> selama kegiatan PKL</li>
                    <li>Setiap akhir minggu, jurnal akan diverifikasi oleh pembimbing PKL</li>
                    <li>Cetak jurnal hanya bisa dilakukan jika <strong>semua entry</strong> dalam minggu tersebut telah <strong>disetujui</strong></li>
                    <li>Jika status <strong>Revisi</strong>, silakan perbaiki jurnal dan akan diverifikasi ulang</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
