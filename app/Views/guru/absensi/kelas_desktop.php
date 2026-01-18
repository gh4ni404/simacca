<?= $this->extend('templates/desktop_layout') ?>

<?= $this->section('content') ?>

<div class="container mx-auto px-4 py-8">
    <!-- Header with Back Button -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="<?= base_url('guru/absensi'); ?>" class="mr-4 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-school text-purple-600 mr-2"></i>
                    <?= esc($kelas['nama_kelas']); ?>
                </h1>
                <p class="text-gray-600 mt-1">Daftar Pertemuan & Absensi</p>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Total Pertemuan</p>
                    <p class="text-3xl font-bold"><?= $kelasStats['total_pertemuan']; ?></p>
                </div>
                <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                    <i class="fas fa-hashtag text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Rata-rata Kehadiran</p>
                    <p class="text-3xl font-bold"><?= $kelasStats['avg_kehadiran']; ?>%</p>
                </div>
                <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                    <i class="fas fa-user-check text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">Total Siswa</p>
                    <p class="text-3xl font-bold"><?= $kelasStats['total_siswa']; ?></p>
                </div>
                <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">Tingkat/Jurusan</p>
                    <p class="text-xl font-bold"><?= esc($kelas['tingkat'] . ' - ' . $kelas['jurusan']); ?></p>
                </div>
                <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                    <i class="fas fa-graduation-cap text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Absensi List Table -->
    <?php if (empty($absensiList)): ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden p-16">
            <?= empty_state(
                'clipboard-list',
                'Belum Ada Data Absensi',
                'Belum ada pertemuan untuk kelas ini',
                'Input Absensi',
                base_url('guru/absensi/tambah')
            ); ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-list-ul mr-2 text-purple-600"></i>
                    Daftar Pertemuan
                </h2>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Pertemuan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kehadiran</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $no = 1; ?>
                    <?php foreach ($absensiList as $item): ?>
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
                                <?php if (!empty($item['guru_pengganti_id'])): ?>
                                <div class="text-xs text-purple-600 flex items-center mt-1">
                                    <i class="fas fa-user-plus mr-1"></i>
                                    Pengganti: <?= esc($item['nama_guru_pengganti']); ?>
                                </div>
                                <?php endif; ?>
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
                                <div class="flex justify-center space-x-2">
                                    <?php if (is_absensi_editable($item)): ?>
                                        <a href="<?= base_url('guru/absensi/edit/' . $item['id']); ?>"
                                            class="p-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-600 rounded-lg transition-all transform hover:scale-110" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('guru/absensi/print/' . $item['id']); ?>"
                                        class="p-2 bg-purple-100 hover:bg-purple-200 text-purple-600 rounded-lg transition-all transform hover:scale-110" title="Cetak" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <?php if (is_absensi_editable($item)): ?>
                                        <a href="#"
                                            onclick="confirmDelete('<?= $item['id']; ?>')"
                                            class="p-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-all transform hover:scale-110" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
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
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Yakin mau hapus absen ini? Data nggak bisa dikembalikan lho!')) {
            window.location.href = '<?= base_url('guru/absensi/delete/'); ?>' + id;
        }
    }
</script>

<?= $this->endSection() ?>
