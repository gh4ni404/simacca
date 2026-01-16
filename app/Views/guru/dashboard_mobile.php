<?= $this->extend('templates/mobile_layout') ?>

<?= $this->section('content') ?>
<div class="pb-20">
    <!-- Welcome Card - Compact Mobile Version -->
    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white p-4 mb-4 rounded-lg mx-4 shadow-md">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h1 class="text-lg font-bold">Selamat Datang, <?= esc($guru['nama_lengkap'] ?? session()->get('username')); ?>!</h1>
                <p class="text-xs opacity-90 mt-1"><?= date('d M Y'); ?></p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $guru['is_wali_kelas'] ? 'bg-green-400 bg-opacity-30' : 'bg-blue-400 bg-opacity-30'; ?>">
                        <i class="fas <?= $guru['is_wali_kelas'] ? 'fa-user-tie' : 'fa-chalkboard-teacher'; ?> mr-1 text-xs"></i>
                        <?= $guru['is_wali_kelas'] ? 'Wali Kelas' : 'Guru'; ?>
                    </span>
                </div>
            </div>
            <div class="h-24 w-24 rounded-full bg-white bg-opacity-20 flex items-center justify-center flex-shrink-0">
                <!-- <i class="fas fa-user-circle text-2xl"></i> -->
                <?php if (session()->get('profile_photo')): ?>
                    <img src="<?= base_url('profile-photo/' . esc(session()->get('profile_photo'))); ?>"
                        alt="<?= esc(session()->get('nama_lengkap') ?? session()->get('username')); ?>"
                        class="h-24 w-24 rounded-full object-cover border-2 border-indigo-200">
                <?php else: ?>
                    <div class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-indigo-600 font-semibold text-xs">
                            <?= strtoupper(substr(session()->get('nama_lengkap') ?? session()->get('username') ?? 'U', 0, 2)); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stats Grid - 2 Columns on Mobile -->
    <div class="grid grid-cols-2 gap-3 mb-4 px-4">
        <?= stat_card(
            'Total Jadwal', 
            $stats['total_jadwal'], 
            'calendar-alt', 
            'blue', 
            '', 
            '<i class="fas fa-clock mr-1"></i>' . $stats['absensi_hari_ini'] . ' hari ini',
            'compact'
        ); ?>

        <?= stat_card(
            'Absensi', 
            $stats['absensi_bulan_ini'], 
            'clipboard-check', 
            'green', 
            '', 
            '<i class="fas fa-chart-line mr-1"></i>bulan ini',
            'compact'
        ); ?>

        <?= stat_card(
            'Jurnal', 
            $stats['jurnal_bulan_ini'], 
            'book', 
            'purple', 
            '', 
            '<i class="fas fa-check-circle mr-1"></i>bulan ini',
            'compact'
        ); ?>

        <?= stat_card(
            'Kelas', 
            $stats['total_kelas'], 
            'school', 
            'yellow', 
            '', 
            '<i class="fas fa-users mr-1"></i>yang diajar',
            'compact'
        ); ?>
    </div>

    <!-- Quick Actions - Horizontal Scroll -->
    <div class="mb-4">
        <div class="flex items-center justify-between px-4 mb-3">
            <h3 class="text-sm font-semibold text-gray-900">Aksi Cepat</h3>
        </div>
        <div class="flex gap-3 overflow-x-auto px-4 pb-2 scrollbar-hide">
            <?php foreach ($quickActions as $action): ?>
                <a href="<?= $action['url']; ?>"
                    class="flex-shrink-0 w-32 <?= $action['color']; ?> text-white rounded-lg p-3 shadow-sm active:shadow-lg transition-shadow">
                    <div class="flex flex-col items-center text-center">
                        <div class="p-2 rounded-full bg-white bg-opacity-20 mb-2">
                            <i class="<?= $action['icon']; ?> text-lg"></i>
                        </div>
                        <p class="text-xs font-semibold"><?= $action['title']; ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Jadwal Hari Ini -->
    <div class="bg-white rounded-lg shadow-sm mb-4 mx-4">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Jadwal Hari Ini</h3>
                <p class="text-xs text-gray-500"><?= date('d M Y'); ?></p>
            </div>
            <a href="<?= base_url('guru/jadwal'); ?>" class="text-xs text-blue-500 active:text-blue-700">
                Semua <i class="fas fa-chevron-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            <?php if (empty($jadwalHariIni)): ?>
                <div class="text-center py-6">
                    <i class="fas fa-calendar-times text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Tidak ada jadwal hari ini</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($jadwalHariIni as $jadwal): ?>
                        <div class="border border-gray-200 rounded-lg p-3 active:bg-gray-50">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900"><?= $jadwal['nama_mapel']; ?></h4>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-clock mr-1"></i>
                                        <?= date('H:i', strtotime($jadwal['jam_mulai'])); ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])); ?>
                                    </p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-school mr-1"></i>
                                        <?= $jadwal['nama_kelas']; ?>
                                    </p>
                                </div>
                            </div>
                            <a href="<?= base_url('guru/absensi/tambah?jadwal_id=' . $jadwal['id']); ?>"
                                class="block w-full text-center px-3 py-2 bg-blue-500 text-white rounded-lg text-xs font-medium active:bg-blue-600">
                                <i class="fas fa-clipboard-check mr-1"></i>
                                Input Absensi
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending Izin - Mobile Optimized -->
    <?php if (!empty($pendingIzin)): ?>
        <div class="bg-white rounded-lg shadow-sm mb-4 mx-4">
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">
                    <i class="fas fa-exclamation-circle text-yellow-500 mr-1"></i>
                    Izin Pending
                </h3>
                <p class="text-xs text-gray-500"><?= count($pendingIzin); ?> perlu persetujuan</p>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    <?php foreach (array_slice($pendingIzin, 0, 3) as $izin): ?>
                        <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-3">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900"><?= $izin['nama_lengkap']; ?></h4>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-id-badge mr-1"></i><?= $izin['nis']; ?> | <?= $izin['nama_kelas']; ?>
                                    </p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-calendar-day mr-1"></i>
                                        <?= date('d/m/Y', strtotime($izin['tanggal'])); ?>
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 line-clamp-2">
                                <?= $izin['alasan']; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Recent Absensi -->
    <div class="bg-white rounded-lg shadow-sm mb-4 mx-4">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Absensi Terbaru</h3>
                <p class="text-xs text-gray-500">5 terakhir</p>
            </div>
            <a href="<?= base_url('guru/absensi'); ?>" class="text-xs text-blue-500 active:text-blue-700">
                Semua <i class="fas fa-chevron-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            <?php if (empty($recentAbsensi)): ?>
                <div class="text-center py-6">
                    <i class="fas fa-clipboard-list text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada data absensi</p>
                </div>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($recentAbsensi as $absensi): ?>
                        <a href="<?= base_url('guru/absensi/show/' . $absensi['id']); ?>"
                            class="flex items-center justify-between p-3 border border-gray-200 rounded-lg active:bg-gray-50">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900"><?= $absensi['nama_mapel']; ?></h4>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="text-xs text-gray-600">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        <?= date('d/m/Y', strtotime($absensi['tanggal'])); ?>
                                    </span>
                                    <span class="text-xs text-gray-600">
                                        <i class="fas fa-school mr-1"></i>
                                        <?= $absensi['nama_kelas']; ?>
                                    </span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Jurnal -->
    <div class="bg-white rounded-lg shadow-sm mb-4 mx-4">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Jurnal Terbaru</h3>
                <p class="text-xs text-gray-500">5 terakhir</p>
            </div>
            <a href="<?= base_url('guru/jurnal'); ?>" class="text-xs text-blue-500 active:text-blue-700">
                Semua <i class="fas fa-chevron-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            <?php if (empty($recentJurnal)): ?>
                <div class="text-center py-6">
                    <i class="fas fa-book-open text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada jurnal</p>
                </div>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($recentJurnal as $jurnal): ?>
                        <div class="border border-gray-200 rounded-lg p-3">
                            <h4 class="text-sm font-medium text-gray-900"><?= $jurnal['nama_mapel']; ?></h4>
                            <p class="text-xs text-gray-600 mt-1">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                <?= date('d/m/Y', strtotime($jurnal['tanggal'])); ?>
                                | <?= $jurnal['nama_kelas']; ?>
                            </p>
                            <p class="text-xs text-gray-500 mt-2 line-clamp-2">
                                <?= substr(strip_tags($jurnal['kegiatan_pembelajaran']), 0, 80); ?>
                                <?php if (strlen(strip_tags($jurnal['kegiatan_pembelajaran'])) > 80) echo '...'; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .line-clamp-2 {
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Auto refresh pending izin every 30 seconds
    setInterval(function() {
        fetch('<?= base_url("guru/dashboard/getPendingIzin"); ?>')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    const badge = document.getElementById('pendingIzinBadge');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.classList.remove('hidden');
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }, 30000);
</script>
<?= $this->endSection() ?>