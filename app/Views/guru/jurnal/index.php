<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-book-open mr-2 text-indigo-600"></i>
                    Jurnal Kegiatan Belajar Mengajar
                </h1>
                <p class="text-gray-600 mt-1">Kelola dan pantau jurnal KBM Anda</p>
            </div>
            <div class="mt-4 md:mt-0">
                <nav class="text-sm text-gray-600">
                    <a href="<?= base_url('guru/dashboard') ?>" class="hover:text-indigo-600">
                        <i class="fas fa-home mr-1"></i>Dashboard
                    </a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-800 font-medium">Jurnal KBM</span>
                </nav>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center animate-fade-in">
        <i class="fas fa-check-circle text-xl mr-3"></i>
        <span><?= session()->getFlashdata('success') ?></span>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center animate-fade-in">
        <i class="fas fa-exclamation-circle text-xl mr-3"></i>
        <span><?= session()->getFlashdata('error') ?></span>
    </div>
    <?php endif; ?>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-t-lg">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-filter mr-2"></i>
                Filter Jurnal
            </h2>
        </div>
        <div class="p-6">
            <form method="GET" action="<?= base_url('guru/jurnal') ?>" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-1 text-indigo-500"></i>
                        Tanggal Mulai
                    </label>
                    <input type="date" 
                           name="start_date" 
                           value="<?= $startDate ?? '' ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-check mr-1 text-indigo-500"></i>
                        Tanggal Akhir
                    </label>
                    <input type="date" 
                           name="end_date" 
                           value="<?= $endDate ?? '' ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                    <a href="<?= base_url('guru/jurnal') ?>" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Jurnal List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-book-open mr-2"></i>
                    Daftar Jurnal KBM
                </h2>
                <span class="text-sm opacity-90">
                    Total: <?= count($jurnal ?? []) ?> jurnal
                </span>
            </div>
        </div>
        
        <div class="p-6">
            <?php if (empty($jurnal)): ?>
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-100 mb-4">
                    <i class="fas fa-book-open text-4xl text-indigo-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Belum Ada Jurnal KBM</h3>
                <p class="text-gray-600 mb-4">Silakan buat jurnal setelah melakukan absensi siswa</p>
                <a href="<?= base_url('guru/absensi') ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Ke Halaman Absensi
                </a>
            </div>
            <?php else: ?>
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan Pembelajaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $no = 1; foreach ($jurnal as $j): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++ ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-calendar-alt text-indigo-500 mr-2"></i>
                                    <span class="font-medium text-gray-900"><?= date('d/m/Y', strtotime($j['tanggal'])) ?></span>
                                </div>
                                <div class="text-xs text-gray-500 ml-6">
                                    <?= date('l', strtotime($j['tanggal'])) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-book text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= esc($j['nama_mapel']) ?></div>
                                        <div class="text-xs text-gray-500">Pertemuan ke-<?= $j['pertemuan_ke'] ?? '-' ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-users mr-1"></i>
                                    <?= esc($j['nama_kelas']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-md">
                                    <?= esc(substr($j['tujuan_pembelajaran'], 0, 100)) ?><?= strlen($j['tujuan_pembelajaran']) > 100 ? '...' : '' ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="<?= base_url('guru/jurnal/edit/' . $j['id']) ?>" 
                                   class="inline-flex items-center px-3 py-1.5 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Info Footer -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Informasi:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Jurnal KBM dibuat setelah melakukan absensi siswa</li>
                    <li>Gunakan filter tanggal untuk mencari jurnal periode tertentu</li>
                    <li>Pastikan melengkapi semua informasi jurnal dengan detail</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
