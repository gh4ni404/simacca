<?= $this->extend('templates/desktop_layout') ?>

<?= $this->section('content') ?>
<!-- Custom Styles -->
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .table-row-hover {
        transition: all 0.2s ease;
    }

    .table-row-hover:hover {
        background-color: #f8fafc;
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .stats-badge {
        transition: all 0.3s ease;
    }

    .stats-badge:hover {
        transform: scale(1.05);
    }
</style>

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

    <!-- Kelas List - Table View -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-list mr-3"></i>
                        Daftar Absensi per Kelas
                    </h2>
                    <p class="text-blue-100 mt-1">Absensi dikelompokkan berdasarkan kelas dan mata pelajaran</p>
                </div>
                <div class="stats-badge bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl">
                    <p class="text-sm opacity-90">Total Kelas</p>
                    <p class="text-3xl font-bold"><?= count($kelasSummary ?? []) ?></p>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <?php if (empty($kelasSummary)): ?>
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 mb-6">
                    <i class="fas fa-clipboard-list text-5xl text-blue-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">Belum Ada Data Absensi</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">Mulai dengan menginput data absensi pertama Anda untuk kelas yang Anda ajar.</p>
                <a href="<?= base_url('guru/absensi/tambah') ?>" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-plus-circle mr-3 text-xl"></i>
                    Input Absensi Pertama
                </a>
            </div>
            <?php else: ?>
            <!-- Table Desktop -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Mata Pelajaran</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Pertemuan</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Kehadiran</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Absensi Terakhir</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $no = 1; foreach ($kelasSummary as $kelas): ?>
                        <tr class="table-row-hover">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900"><?= $no++ ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="bg-purple-100 p-3 rounded-lg mr-3">
                                        <i class="fas fa-school text-purple-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900"><?= esc($kelas['kelas_nama']) ?></div>
                                        <div class="text-xs text-gray-500"><?= $kelas['total_siswa'] ?> siswa</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="bg-green-100 p-3 rounded-lg mr-3">
                                        <i class="fas fa-book text-green-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900"><?= esc($kelas['mata_pelajaran']) ?></div>
                                        <div class="text-xs text-gray-500">Mata Pelajaran</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="inline-flex items-center justify-center bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border-2 border-blue-200 px-4 py-2 rounded-xl">
                                    <i class="fas fa-hashtag mr-2"></i>
                                    <span class="text-lg font-bold"><?= $kelas['total_pertemuan'] ?></span>
                                    <span class="text-xs ml-1">kali</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="inline-flex items-center justify-center bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border-2 border-green-200 px-4 py-2 rounded-xl">
                                    <i class="fas fa-user-check mr-2"></i>
                                    <span class="text-lg font-bold"><?= $kelas['avg_kehadiran'] ?>%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if ($kelas['last_absensi']): ?>
                                <div class="text-sm font-semibold text-gray-900">
                                    <?php
                                    $formatter = new IntlDateFormatter(
                                        'id_ID',
                                        IntlDateFormatter::LONG,
                                        IntlDateFormatter::NONE,
                                        'Asia/Makassar',
                                        IntlDateFormatter::GREGORIAN,
                                        'd MMM y'
                                    );
                                    echo $formatter->format(strtotime($kelas['last_absensi']));
                                    ?>
                                </div>
                                <?php else: ?>
                                <span class="text-xs text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="<?= base_url('guru/absensi/kelas/' . $kelas['kelas_id']) ?>" 
                                   class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg hover:from-blue-600 hover:to-cyan-600 transition-all font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                                   title="Lihat Detail Pertemuan">
                                    <i class="fas fa-eye mr-2"></i>
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Cards Mobile -->
            <div class="md:hidden space-y-4">
                <?php $no = 1; foreach ($kelasSummary as $kelas): ?>
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border-2 border-gray-200 p-5 hover:shadow-lg transition-all">
                    <div class="flex items-start justify-between mb-4">
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">#{<?= $no++ ?>}</span>
                        <div class="flex gap-2">
                            <span class="bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 text-xs font-bold px-3 py-2 rounded-lg border border-blue-200">
                                <i class="fas fa-hashtag mr-1"></i>
                                <?= $kelas['total_pertemuan'] ?> kali
                            </span>
                            <span class="bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 text-xs font-bold px-3 py-2 rounded-lg border border-green-200">
                                <i class="fas fa-user-check mr-1"></i>
                                <?= $kelas['avg_kehadiran'] ?>%
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-4">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-school text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Kelas</p>
                                <p class="text-sm font-bold text-gray-900"><?= esc($kelas['kelas_nama']) ?></p>
                                <p class="text-xs text-gray-400"><?= $kelas['total_siswa'] ?> siswa</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="bg-green-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-book text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Mata Pelajaran</p>
                                <p class="text-sm font-bold text-gray-900"><?= esc($kelas['mata_pelajaran']) ?></p>
                            </div>
                        </div>

                        <?php if ($kelas['last_absensi']): ?>
                        <div class="flex items-center">
                            <div class="bg-gray-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-calendar text-gray-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Absensi Terakhir</p>
                                <p class="text-sm font-bold text-gray-900">
                                    <?php
                                    $formatter = new IntlDateFormatter(
                                        'id_ID',
                                        IntlDateFormatter::LONG,
                                        IntlDateFormatter::NONE,
                                        'Asia/Makassar',
                                        IntlDateFormatter::GREGORIAN,
                                        'd MMMM y'
                                    );
                                    echo $formatter->format(strtotime($kelas['last_absensi']));
                                    ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <a href="<?= base_url('guru/absensi/kelas/' . $kelas['kelas_id']) ?>" 
                       class="block w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg text-center font-semibold shadow-md hover:shadow-lg transition-all">
                        <i class="fas fa-eye mr-2"></i>
                        Lihat Detail Pertemuan
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Add fade-in animation for table rows
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
        if (confirm('Apakah Anda yakin ingin menghapus absensi ini?\n\nCatatan: Hanya dapat dihapus dalam 24 jam setelah dibuat.')) {
            window.location.href = '<?= base_url('guru/absensi/delete/'); ?>' + id;
        }
    }
</script>
<?= $this->endSection() ?>