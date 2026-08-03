<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .table-row-hover { transition: all 0.2s ease; }
    .table-row-hover:hover { background-color: #f8fafc; transform: translateX(4px); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
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
                <?= button_link('primary', 'Input Absensi Baru', 'plus-circle', base_url('guru/absensi/tambah'), ['class' => 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-lg hover:shadow-xl transform hover:-translate-y-1 text-lg']) ?>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

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
                <?= form_input('tanggal', 'Tanggal', $tanggal, ['type' => 'date']) ?>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-school mr-2 text-purple-500"></i>
                        Kelas
                    </label>
                    <select name="kelas_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <?php foreach ($kelasOptions as $id => $nama): ?>
                            <option value="<?= $id; ?>" <?= $kelasId == $id ? 'selected' : ''; ?>><?= $nama; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?= form_input('search', 'Cari Mata Pelajaran', $search, ['placeholder' => 'Ketik nama mata pelajaran...']) ?>
            </div>
            <div class="flex gap-3">
                <?= button('primary', 'Filter Data', 'search', ['type' => 'submit', 'class' => 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg']) ?>
                <?= button_link('secondary', 'Reset Filter', 'redo', base_url('guru/absensi')) ?>
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
                <div class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl">
                    <p class="text-sm opacity-90">Total Kelas</p>
                    <p class="text-3xl font-bold"><?= count($kelasSummary ?? []) ?></p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <?php if (empty($kelasSummary)): ?>
                <?= empty_state('clipboard-list', 'Belum Ada Data Absensi', 'Mulai dengan menginput data absensi pertama Anda untuk kelas yang Anda ajar.', 'Input Absensi Pertama', base_url('guru/absensi/tambah')) ?>
            <?php else: ?>
                <!-- Table Desktop -->
                <div class="hidden md:block overflow-x-auto">
                    <?= table_start() ?>
                        <?= table_header(['No', 'Kelas', 'Mata Pelajaran', 'Pertemuan', 'Kehadiran', 'Absensi Terakhir', 'Aksi']) ?>
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
                                    $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Asia/Makassar', IntlDateFormatter::GREGORIAN, 'd MMM y');
                                    echo $formatter->format(strtotime($kelas['last_absensi']));
                                    ?>
                                </div>
                                <?php else: ?>
                                <span class="text-xs text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?= button_link('info', 'Lihat Detail', 'eye', base_url('guru/absensi/kelas/' . $kelas['kelas_id'] . '?mapel_id=' . $kelas['mata_pelajaran_id']), ['class' => 'bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 shadow-md hover:shadow-lg transform hover:-translate-y-0.5']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?= table_end() ?>
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
                                        $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Asia/Makassar', IntlDateFormatter::GREGORIAN, 'd MMMM y');
                                        echo $formatter->format(strtotime($kelas['last_absensi']));
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?= button_link('info', 'Lihat Detail Pertemuan', 'eye', base_url('guru/absensi/kelas/' . $kelas['kelas_id']), ['class' => 'w-full justify-center bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 shadow-md']) ?>
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
