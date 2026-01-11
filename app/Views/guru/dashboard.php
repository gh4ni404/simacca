<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Welcome Section -->
    <div class="mb-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Selamat Datang, <?= esc($guru['nama_lengkap'] ?? session()->get('username')); ?>!</h1>
                <p id="greetingDate" class="mt-1 text-sm opacity-80"><?= date('l, d F Y'); ?></p>
                <div class="sm:grid sm:grid-cols-1 items-center mt-2">
                    <div class="flex items-center mt-1 text-sm opacity-80">
                        <i class="fas fa-chalkboard-teacher mr-2"></i>
                        <?= isset($guru['nama_mapel']) && $guru['nama_mapel'] ? esc($guru['nama_mapel']) : 'Mata Pelajaran belum diatur' ?>
                    </div>
                    <div class="mx-4 opacity-80">|</div>
                    <div class="flex items-center text-sm opacity-80">
                        <i class="fas fa-user-graduate mr-2"></i>
                        <?= $guru['nip'] ?? 'NIP belum diatur'; ?>
                    </div>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm opacity-90">Role</p>
                            <p class="text-lg font-semibold"> <!-- <?= get_role_name(); ?> -->
                                <span class="inline-flex items-center px-3 py-2 mt-2 rounded-full text-sm font-medium <?= $guru['is_wali_kelas'] ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
                                    <i class="fas <?= $guru['is_wali_kelas'] ? 'fa-user-tie' : 'fa-chalkboard-teacher'; ?> mr-2"></i>
                                    <?= $guru['is_wali_kelas'] ? 'Wali Kelas' : 'Guru Mata Pelajaran'; ?>
                                </span>
                            </p>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas <?= $guru['is_wali_kelas'] ? 'fa-user-tie' : 'fa-chalkboard-teacher'; ?> text-xl"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Jadwal</p>
                    <p class="text-2xl font-bold"><?= $stats['total_jadwal']; ?></p>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                <?= $stats['absensi_hari_ini']; ?> absensi hari ini
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-clipboard-check text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Absensi Bulan Ini</p>
                    <p class="text-2xl font-bold"><?= $stats['absensi_bulan_ini']; ?></p>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                <i class="fas fa-chart-line mr-1"></i>
                <?= $stats['absensi_bulan_ini']; ?> pertemuan
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-book text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jurnal Bulan Ini</p>
                    <p class="text-2xl font-bold"><?= $stats['jurnal_bulan_ini']; ?></p>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                <i class="fas fa-check-circle mr-1"></i>
                <?= $stats['jurnal_bulan_ini']; ?> dokumen
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-school text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Kelas yang Diajar</p>
                    <p class="text-2xl font-bold"><?= $stats['total_kelas']; ?></p>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                <i class="fas fa-users mr-1"></i>
                <?= $stats['total_kelas']; ?> kelas berbeda
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Aksi Cepat</h3>
                    <p class="mt-1 text-sm text-gray-500">Akses fitur dengan cepat</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($quickActions as $action): ?>
                            <a href="<?= $action['url']; ?>"
                                class="<?= $action['color']; ?> text-white rounded-lg p-4 hover:shadow-md transition-all duration-200 transform hover:-translate-y-1">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-white bg-opacity-20 mr-4">
                                        <i class="<?= $action['icon']; ?> text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold"><?= $action['title']; ?></h4>
                                        <p class="text-sm text-white text-opacity-80 mt-1"><?= $action['description']; ?></p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Jadwal Hari Ini -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Jadwal Hari Ini</h3>
                        <p class="mt-1 text-sm text-gray-500"><?= date('l, d F Y'); ?></p>
                    </div>
                    <a href="<?= base_url('guru/jadwal'); ?>" class="text-sm text-blue-500 hover:text-blue-700">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="p-6">
                    <?php if (empty($jadwalHariIni)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Tidak ada jadwal hari ini</p>
                            <p class="text-sm text-gray-400 mt-2">Anda bisa beristirahat hari ini</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($jadwalHariIni as $jadwal): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900"><?= $jadwal['nama_mapel']; ?></h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fas fa-clock mr-2"></i>
                                                <?= date('H:i', strtotime($jadwal['jam_mulai'])); ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])); ?>
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fas fa-school mr-2"></i>
                                                <?= $jadwal['nama_kelas']; ?> (Kelas <?= $jadwal['tingkat']; ?>)
                                            </p>
                                        </div>
                                        <div>
                                            <a href="<?= base_url('guru/absensi/tambah?jadwal_id=' . $jadwal['id']); ?>"
                                                class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm hover:bg-blue-200 transition">
                                                <i class="fas fa-clipboard-check mr-2"></i>
                                                Input Absensi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Absensi -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Absensi Terbaru</h3>
                    <p class="mt-1 text-sm text-gray-500">5 absensi terakhir yang Anda input</p>
                </div>
                <div class="p-6">
                    <?php if (empty($recentAbsensi)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Belum ada data absensi</p>
                            <p class="text-sm text-gray-400 mt-2">Mulai input absensi untuk melihat data</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recentAbsensi as $absensi): ?>
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <div>
                                        <h4 class="font-medium text-gray-900"><?= $absensi['nama_mapel']; ?></h4>
                                        <div class="flex items-center mt-1 space-x-4">
                                            <span class="text-sm text-gray-600">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                <?= date('d/m/Y', strtotime($absensi['tanggal'])); ?>
                                            </span>
                                            <span class="text-sm text-gray-600">
                                                <i class="fas fa-school mr-1"></i>
                                                <?= $absensi['nama_kelas']; ?>
                                            </span>
                                            <span class="text-sm text-gray-600">
                                                <i class="fas fa-hashtag mr-1"></i>
                                                Pertemuan ke-<?= $absensi['pertemuan_ke']; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <a href="<?= base_url('guru/absensi/show/' . $absensi['id']); ?>"
                                        class="text-sm text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="<?= base_url('guru/absensi'); ?>" class="text-sm text-blue-500 hover:text-blue-700">
                                Lihat semua absensi <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Jadwal Minggu Ini -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Jadwal Minggu Ini</h3>
                    <p class="mt-1 text-sm text-gray-500">Jadwal mengajar Anda minggu ini</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach ($jadwalMingguIni as $hari => $jadwalList): ?>
                            <?php if (!empty($jadwalList)): ?>
                                <div class="mb-4">
                                    <h4 class="font-medium text-gray-700 mb-2"><?= $hari; ?></h4>
                                    <div class="space-y-2">
                                        <?php foreach ($jadwalList as $jadwal): ?>
                                            <div class="border-l-4 border-blue-500 pl-3 py-2 bg-blue-50">
                                                <p class="text-sm font-medium text-gray-900"><?= $jadwal['nama_mapel']; ?></p>
                                                <p class="text-xs text-gray-600">
                                                    <?= date('H:i', strtotime($jadwal['jam_mulai'])); ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])); ?>
                                                    | <?= $jadwal['nama_kelas']; ?>
                                                </p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php
                    $totalJadwalMinggu = array_sum(array_map('count', $jadwalMingguIni));
                    if ($totalJadwalMinggu === 0): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-day text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Tidak ada jadwal minggu ini</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pending Izin -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Pengajuan Izin Pending</h3>
                    <p class="mt-1 text-sm text-gray-500">Perlu persetujuan wali kelas</p>
                </div>
                <div class="p-6">
                    <?php if (empty($pendingIzin)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-4xl text-green-300 mb-4"></i>
                            <p class="text-gray-500">Tidak ada izin pending</p>
                            <p class="text-sm text-gray-400 mt-2">Semua izin telah diproses</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($pendingIzin as $izin): ?>
                                <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900"><?= $izin['nama_lengkap']; ?></h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fas fa-id-badge mr-1"></i>
                                                <?= $izin['nis']; ?> | <?= $izin['nama_kelas']; ?>
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fas fa-calendar-day mr-1"></i>
                                                <?= date('d/m/Y', strtotime($izin['tanggal'])); ?>
                                            </p>
                                            <p class="text-xs text-gray-500 mt-2 line-clamp-2">
                                                <i class="fas fa-sticky-note mr-1"></i>
                                                <?= $izin['alasan']; ?>
                                            </p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Jurnal -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Jurnal Terbaru</h3>
                    <p class="mt-1 text-sm text-gray-500">5 jurnal terakhir yang Anda buat</p>
                </div>
                <div class="p-6">
                    <?php if (empty($recentJurnal)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-book-open text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Belum ada jurnal</p>
                            <p class="text-sm text-gray-400 mt-2">Buat jurnal pertama Anda</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($recentJurnal as $jurnal): ?>
                                <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition">
                                    <h4 class="font-medium text-gray-900"><?= $jurnal['nama_mapel']; ?></h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        <?= date('d/m/Y', strtotime($jurnal['tanggal'])); ?>
                                        | <?= $jurnal['nama_kelas']; ?>
                                    </p>
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-500 line-clamp-2">
                                            <?= substr(strip_tags($jurnal['tujuan_pembelajaran']), 0, 100); ?>...
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="<?= base_url('guru/jurnal'); ?>" class="text-sm text-blue-500 hover:text-blue-700">
                                Lihat semua jurnal <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info Profile -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Info Profile</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-medium text-gray-900"><?= $guru['nama_lengkap']; ?></h4>
                            <p class="text-sm text-gray-600">NIP: <?= $guru['nip'] ?? 'Belum diatur'; ?></p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-venus-mars mr-3 w-5"></i>
                            <span><?= $guru['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-book mr-3 w-5"></i>
                            <span>
                                <?= isset($mapel['nama_mapel']) ? $mapel['nama_mapel'] : 'Belum diatur' ?>
                            </span>
                        </div>
                        <?php if (isset($guru['is_wali_kelas']) && $guru['is_wali_kelas'] == 1 && isset($guru['kelas_id']) && $guru['kelas_id'] > 0): ?>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-user-tie mr-3 w-5"></i>
                                <span>
                                    Wali Kelas
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mt-6">
                        <a href="<?= base_url('profile'); ?>"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Update time every minute
    function updateTime() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        const dateTime = now.toLocaleString('id-ID', options);
        const el = document.getElementById('greetingDate');
        if (el) el.textContent = dateTime;
    }

    // Initial update
    updateTime();
    // Update every minute
    setInterval(updateTime, 60000);

    // Auto refresh pending izin every 30 seconds
    setInterval(function() {
        fetch('<?= base_url("guru/dashboard/getPendingIzin"); ?>')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    // Update badge if exists
                    const badge = document.getElementById('pendingIzinBadge');
                    if (badge) {
                        badge.textContent = data.count;
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }, 30000);
</script>
<?= $this->endSection() ?>