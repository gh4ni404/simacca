<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .table-row-hover { transition: all 0.2s ease; }
    .table-row-hover:hover { background-color: #f8fafc; transform: translateX(4px); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Absensi PKL</span>
                </h1>
                <p class="text-base text-gray-600 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Kelola absensi kehadiran siswa bimbingan PKL
                </p>
            </div>
            <a href="<?= base_url('guru/absensi-pkl/tambah'); ?>"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                <i class="fas fa-plus-circle mr-2 text-lg"></i>
                <span>Input Absensi Baru</span>
            </a>
        </div>
    </div>

    <?= render_flash_message() ?>

    <!-- Filter -->
    <div class="bg-white rounded-2xl shadow-lg mb-8 overflow-hidden">
        <div class="flex items-center p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <div class="p-2 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg mr-3">
                <i class="fas fa-filter text-white"></i>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Filter Data</h2>
        </div>
        <form method="get" class="p-6">
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i> Tanggal
                    </label>
                    <input type="date" name="tanggal" value="<?= esc($tanggal ?? '') ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="<?= base_url('guru/absensi-pkl'); ?>" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-all">
                    <i class="fas fa-redo mr-2"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Absensi List -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-list mr-3"></i> Daftar Absensi PKL
                    </h2>
                    <p class="text-blue-100 mt-1">Riwayat absensi kehadiran siswa PKL</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl">
                    <p class="text-sm opacity-90">Total Absensi</p>
                    <p class="text-3xl font-bold"><?= count($absensi) ?></p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <?php if (empty($absensi)): ?>
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 mb-6">
                    <i class="fas fa-clipboard-list text-5xl text-blue-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">Belum Ada Data Absensi PKL</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">Mulai dengan menginput data absensi kehadiran siswa PKL.</p>
                <a href="<?= base_url('guru/absensi-pkl/tambah') ?>" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all font-semibold shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus-circle mr-3 text-xl"></i> Input Absensi Pertama
                </a>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tempat PKL</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Total Siswa</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Hadir</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Izin</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Sakit</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Alpa</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Kehadiran</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $no = 1; foreach ($absensi as $item): ?>
                        <tr class="table-row-hover">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900"><?= $no++ ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                        <i class="fas fa-calendar-day text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900"><?= date('d/m/Y', strtotime($item['tanggal'])) ?></div>
                                        <div class="text-xs text-gray-500"><?= date('l', strtotime($item['tanggal'])) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900"><?= esc($item['nama_perusahaan'] ?? '-') ?></div>
                                <div class="text-xs text-gray-500"><?= esc($item['kota'] ?? '') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-blue-100 text-blue-800 border border-blue-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $item['total_siswa'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-green-100 text-green-800 border border-green-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $item['hadir_count'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-blue-100 text-blue-800 border border-blue-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $item['izin_count'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-yellow-100 text-yellow-800 border border-yellow-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $item['sakit_count'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center bg-red-100 text-red-800 border border-red-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $item['alpa_count'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php
                                $persen = $item['persen_kehadiran'] ?? 0;
                                $colorClass = $persen >= 80 ? 'green' : ($persen >= 60 ? 'yellow' : 'red');
                                ?>
                                <span class="inline-flex items-center justify-center bg-<?= $colorClass ?>-100 text-<?= $colorClass ?>-800 border border-<?= $colorClass ?>-200 px-3 py-1 rounded-lg text-sm font-bold">
                                    <?= $persen ?>%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex gap-2 justify-center">
                                    <a href="<?= base_url('guru/absensi-pkl/show/' . $item['id']) ?>"
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition-all shadow-sm"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                    <?php if ($item['can_edit']): ?>
                                    <a href="<?= base_url('guru/absensi-pkl/edit/' . $item['id']) ?>"
                                       class="inline-flex items-center px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded-lg transition-all shadow-sm"
                                       title="Edit">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($item['can_delete']): ?>
                                    <button onclick="confirmDelete(<?= $item['id'] ?>)"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition-all shadow-sm"
                                            title="Hapus">
                                        <i class="fas fa-trash-alt mr-1"></i> Hapus
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
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.table-row-hover');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(10px)';
        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 50);
    });
});

function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus absensi ini?')) {
        window.location.href = '<?= base_url('guru/absensi-pkl/hapus/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>
