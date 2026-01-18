<?= $this->extend('templates/mobile_layout') ?>

<?= $this->section('content') ?>

<div class="min-h-screen bg-gray-50">
    <!-- Header with Back Button -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-4 py-6 mb-4">
        <div class="flex items-center mb-4">
            <a href="<?= base_url('guru/absensi'); ?>" class="mr-3 p-2 bg-white bg-opacity-20 rounded-lg">
                <i class="fas fa-arrow-left text-white"></i>
            </a>
            <div class="text-white flex-1">
                <h1 class="text-xl font-bold"><?= esc($kelas['nama_kelas']); ?></h1>
                <p class="text-sm opacity-90">Daftar Pertemuan</p>
            </div>
        </div>

        <!-- Kelas Info -->
        <div class="bg-white bg-opacity-20 rounded-xl p-3">
            <div class="grid grid-cols-2 gap-2 text-white text-sm">
                <div>
                    <p class="opacity-75">Tingkat</p>
                    <p class="font-semibold"><?= esc($kelas['tingkat']); ?></p>
                </div>
                <div>
                    <p class="opacity-75">Jurusan</p>
                    <p class="font-semibold"><?= esc($kelas['jurusan']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 pb-4">
        <?= view('components/alerts') ?>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-md p-4 text-white">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs opacity-90">Pertemuan</p>
                    <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                        <i class="fas fa-hashtag text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold"><?= $kelasStats['total_pertemuan']; ?></p>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-md p-4 text-white">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs opacity-90">Kehadiran</p>
                    <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                        <i class="fas fa-user-check text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold"><?= $kelasStats['avg_kehadiran']; ?>%</p>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-md p-4 text-white">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs opacity-90">Total Siswa</p>
                    <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                        <i class="fas fa-users text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold"><?= $kelasStats['total_siswa']; ?></p>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-md p-4 text-white">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs opacity-90">Total Hadir</p>
                    <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                        <i class="fas fa-check-circle text-sm"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold"><?= $kelasStats['total_hadir']; ?></p>
            </div>
        </div>

        <!-- Pertemuan List -->
        <?php if (empty($absensiList)): ?>
            <div class="bg-white rounded-xl p-8 text-center">
                <?= empty_state(
                    'clipboard-list',
                    'Belum Ada Data',
                    'Belum ada pertemuan untuk kelas ini',
                    'Input Absensi',
                    base_url('guru/absensi/tambah')
                ); ?>
            </div>
        <?php else: ?>
            <div class="mb-3">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-list-ul mr-2 text-purple-600"></i>
                    Daftar Pertemuan
                </h2>
            </div>

            <div class="space-y-3">
                <?php foreach ($absensiList as $item): ?>
                    <?php
                    $percentage = isset($item['percentage']) ? $item['percentage'] : 0;
                    $hadir = isset($item['hadir']) ? $item['hadir'] : 0;
                    $total = isset($item['total_siswa']) ? $item['total_siswa'] : 0;
                    $barColor = $percentage >= 80 ? 'bg-green-500' : ($percentage >= 60 ? 'bg-yellow-500' : 'bg-red-500');

                    $formatter = new IntlDateFormatter(
                        'id_ID',
                        IntlDateFormatter::FULL,
                        IntlDateFormatter::NONE,
                        'Asia/Makassar',
                        IntlDateFormatter::GREGORIAN,
                        'EEE, d MMM y'
                    );
                    $formattedDate = $formatter->format(strtotime($item['tanggal']));
                    ?>
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                        <!-- Header -->
                        <div class="p-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span class="flex items-center">
                                    <i class="far fa-calendar text-blue-500 mr-1"></i>
                                    <?= $formattedDate ?>
                                </span>
                                <span class="flex items-center">
                                    <i class="far fa-clock mr-1"></i>
                                    <?= date('H:i', strtotime($item['created_at'])); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-3">
                            <h3 class="text-base font-bold text-gray-900 mb-1"><?= esc($item['nama_mapel']); ?></h3>
                            <p class="text-xs text-gray-600 mb-2 flex items-center">
                                <i class="fas fa-user-tie mr-1 text-gray-400"></i>
                                <?= esc($item['nama_guru']); ?>
                            </p>
                            <?php if (!empty($item['guru_pengganti_id'])): ?>
                            <p class="text-xs text-purple-600 mb-2 flex items-center">
                                <i class="fas fa-user-plus mr-1"></i>
                                Pengganti: <?= esc($item['nama_guru_pengganti']); ?>
                            </p>
                            <?php endif; ?>

                            <!-- Info Grid -->
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <div class="flex items-center">
                                    <div class="p-1.5 bg-blue-100 rounded-lg mr-2">
                                        <i class="fas fa-hashtag text-blue-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Pertemuan</p>
                                        <p class="text-sm font-semibold text-gray-900"><?= $item['pertemuan_ke']; ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <div class="p-1.5 bg-green-100 rounded-lg mr-2">
                                        <i class="fas fa-user-check text-green-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Kehadiran</p>
                                        <p class="text-sm font-semibold text-gray-900"><?= $percentage ?>%</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Attendance Progress -->
                            <div class="bg-gray-50 rounded-lg p-2 mb-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium text-gray-700">Progress</span>
                                    <span class="text-xs font-bold text-gray-900"><?= $hadir ?>/<?= $total ?> siswa</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="<?= $barColor ?> h-2 rounded-full transition-all" style="width: <?= $percentage ?>%"></div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="<?= base_url('guru/absensi/print/' . $item['id']); ?>"
                                    class="flex-1 px-3 py-2 bg-purple-100 hover:bg-purple-200 text-purple-600 text-xs font-semibold rounded-lg text-center transition-all active:scale-95" title="Cetak" target="_blank">
                                    <i class="fas fa-print"></i> Print
                                </a>
                                <?php if (is_absensi_editable($item)): ?>
                                    <a href="<?= base_url('guru/absensi/edit/' . $item['id']); ?>"
                                        class="flex-1 px-3 py-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-600 rounded-lg text-center transition-all active:scale-95" title="Edit">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                <?php endif; ?>
                                <?php if (is_absensi_editable($item)): ?>
                                    <button onclick="confirmDelete(<?= $item['id']; ?>)"
                                        class="px-3 py-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-all active:scale-95" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Yakin mau hapus absen ini? Data nggak bisa dikembalikan lho!')) {
            window.location.href = '<?= base_url('guru/absensi/delete/'); ?>' + id;
        }
    }
</script>

<?= $this->endSection() ?>
