<?= $this->extend('templates/desktop_layout') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Manajemen Absensi
                    </span>
                </h1>
                <p class="text-base text-gray-600 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Kelola data absensi siswa dengan mudah dan efisien
                </p>
            </div>
            <div>
                <a href="<?= base_url('guru/absensi/tambah'); ?>"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                    <i class="fas fa-plus-circle mr-2 text-lg"></i>
                    <span>Input Absensi Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= render_flash_message() ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <?= stat_card('Total Absensi', $stats['total'], 'clipboard-list', 'blue', '', '<i class="fas fa-database mr-1"></i>Semua data'); ?>
        <?= stat_card('Hadir', $stats['hadir'], 'user-check', 'green', '', '<i class="fas fa-check-circle mr-1"></i>Kehadiran'); ?>
        <?= stat_card('Izin', $stats['izin'], 'file-alt', 'yellow', '', '<i class="fas fa-envelope mr-1"></i>Dengan keterangan'); ?>
        <?= stat_card('Alpa', $stats['alpa'], 'user-times', 'red', '', '<i class="fas fa-exclamation-circle mr-1"></i>Tanpa keterangan'); ?>
    </div>

    <!-- Filter Section -->
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
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                        Tanggal
                    </label>
                    <input type="date"
                        name="tanggal"
                        value="<?= $tanggal ?>"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-school mr-2 text-purple-500"></i>
                        Kelas
                    </label>
                    <select name="kelas_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <?php foreach ($kelasOptions as $id => $nama): ?>
                            <option value="<?= $id; ?>" <?= $kelasId == $id ? 'selected' : ''; ?>>
                                <?= $nama; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-search mr-2 text-green-500"></i>
                        Cari Mata Pelajaran
                    </label>
                    <input type="text"
                        name="search"
                        value="<?= $search ?>"
                        placeholder="Ketik nama mata pelajaran..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                    <i class="fas fa-search mr-2"></i>
                    Filter Data
                </button>
                <a href="<?= base_url('guru/absensi'); ?>"
                    class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-all">
                    <i class="fas fa-redo mr-2"></i>
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    <!-- Absensi Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <?php if (empty($absensi)): ?>
            <div class="p-16">
                <?= empty_state(
                    'clipboard-list', 
                    'Belum Ada Data Absensi', 
                    'Mulai dengan menginput data absensi pertama Anda',
                    'Input Absensi Pertama',
                    base_url('guru/absensi/tambah')
                ); ?>
            </div>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Pertemuan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kehadiran</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $no = 1; ?>
                    <?php foreach ($absensi as $item): ?>
                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-700"><?= $no++; ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                        <i class="fas fa-calendar-day text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            <?php
                                            $formatter = new IntlDateFormatter(
                                                'id_ID',
                                                IntlDateFormatter::FULL,
                                                IntlDateFormatter::NONE,
                                                'Asia/Makassar',
                                                IntlDateFormatter::GREGORIAN,
                                                'EEEE, d MMMM y'
                                            );
                                            echo $formatter->format(strtotime($item['tanggal']));
                                            ?>
                                        </div>
                                        <div class="text-xs text-gray-500 flex items-center mt-0.5">
                                            <i class="far fa-clock mr-1"></i>
                                            <?= date('H:i', strtotime($item['created_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900"><?= esc($item['nama_mapel']); ?></div>
                                <div class="text-xs text-gray-500 flex items-center mt-0.5">
                                    <i class="fas fa-user-tie mr-1"></i>
                                    <?= esc($item['nama_guru']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-school mr-1.5"></i>
                                    <?= esc($item['nama_kelas']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm">
                                    <i class="fas fa-hashtag mr-1"></i>
                                    <?= $item['pertemuan_ke']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $percentage = isset($item['percentage']) ? $item['percentage'] : 0;
                                $hadir = isset($item['hadir']) ? $item['hadir'] : 0;
                                $total = isset($item['total_siswa']) ? $item['total_siswa'] : 0;
                                $barColor = $percentage >= 80 ? 'bg-green-500' : ($percentage >= 60 ? 'bg-yellow-500' : 'bg-red-500');
                                ?>
                                <div class="w-32">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-medium text-gray-700"><?= $hadir ?>/<?= $total ?></span>
                                        <span class="text-xs font-bold text-gray-900"><?= $percentage ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                        <div class="<?= $barColor ?> h-2.5 rounded-full transition-all duration-300" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="<?= base_url('guru/absensi/show/' . $item['id']); ?>"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-semibold rounded-lg transition-all"
                                        title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($item['can_edit']): ?>
                                        <a href="<?= base_url('guru/absensi/edit/' . $item['id']); ?>"
                                            class="inline-flex items-center px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 text-xs font-semibold rounded-lg transition-all"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($item['can_delete']): ?>
                                        <button onclick="confirmDelete(<?= $item['id']; ?>)"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-semibold rounded-lg transition-all"
                                            title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus absensi ini?\n\nCatatan: Hanya dapat dihapus dalam 24 jam setelah dibuat.')) {
        window.location.href = '<?= base_url('guru/absensi/delete/'); ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>
