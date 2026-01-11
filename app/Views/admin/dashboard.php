<?= $this->extend('templates/main_layout') ?>

<?= $this->section('styles') ?>
<style>
    .stat-card:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease-in-out;
    }

    .chart-container {
        position: relative;
        height: 300px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Welcome Section -->
<div class="mb-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white">
    <div class="flex flex-col md:flex-row justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold"><?= get_greeting(); ?>, <?= session()->get('nama_lengkap'); ?>!</h2>
            <p class="mt-2 opacity-90">Selamat datang di Sistem Monitoring Absensi dan Catatan Cara Ajar SMKN 8 BONE</p>
            <p class="mt-1 text-sm opacity-80">Terakhir login: <?= date('d M Y H:i'); ?></p>
        </div>
        <div class="mt-4 md:mt-0">
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-sm opacity-90">Role</p>
                    <p class="text-lg font-semibold"><?= get_role_name(); ?></p>
                </div>
                <div class="h-12 w-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fas fa-user-shield text-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Guru -->
    <a href="<?= base_url('admin/guru'); ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
        <div class="stat-card bg-white rounded-xl shadow p-6 border-l-4 border-blue-500 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Guru</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['total_guru']; ?></p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-chalkboard-teacher text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
            </div>
        </div>
    </a>

    <!-- Total Siswa -->
    <a href="<?= base_url('admin/siswa'); ?>" class="text-green-600 hover:text-green-800 text-sm font-medium">
        <div class="stat-card bg-white rounded-xl shadow p-6 border-l-4 border-green-500 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Siswa</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['total_siswa']; ?></p>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
            </div>
        </div>
    </a>
    <!-- Total Kelas -->
    <a href="<?= base_url('admin/kelas'); ?>" class="text-purple-600 hover:text-purple-600 text-sm font-medium">
        <div class="stat-card bg-white rounded-xl shadow p-6 border-l-4 border-purple-500 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Kelas</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['total_kelas']; ?></p>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-school text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
            </div>
        </div>
    </a>
    <!-- Izin Pending -->
    <a href="#" class="text-red-600 hover:text-red-800 text-sm font-medium">
        <div class="stat-card bg-white rounded-xl shadow p-6 border-l-4 border-red-500 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Izin Menunggu</p>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['izin_pending']; ?></p>
                </div>
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                Tinjau <i class="fas fa-arrow-right ml-1"></i>
            </div>
        </div>
    </a>
</div>

<!-- Quick Links -->
<div class="bg-white rounded-xl shadow p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-6">Aksi Cepat</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="<?= base_url('admin/guru/tambah'); ?>" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <p class="font-medium text-blue-800">Tambah Guru</p>
                <p class="text-sm text-blue-600">Input data guru Baru</p>
            </div>
        </a>
        <a href="<?= base_url('admin/siswa/tambah'); ?>" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <p class="font-medium text-green-800">Tambah Siswa</p>
                <p class="text-sm text-green-600">Input data siswa baru</p>
            </div>
        </a>
        <a href="<?= base_url('admin/jadwal/tambah'); ?>" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <div>
                <p class="font-medium text-purple-800">Buat Jadwal</p>
                <p class="text-sm text-purple-600">Atur jadwal mengajar</p>
            </div>
        </a>
        <a href="<?= base_url('admin/laporan/absensi'); ?>" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                <i class="fas fa-file-export"></i>
            </div>
            <div>
                <p class="font-medium text-yellow-800">Export Laporan</p>
                <p class="text-sm text-yellow-600">Download data absensi</p>
            </div>
        </a>
    </div>
</div>

<!-- Chart and Analytics -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Kehadiran 7 Hari terakhir -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Kehadiran 7 Hari Terakhir</h3>
            <span class="text-sm text-gray-500">Sesi Absensi</span>
        </div>
        <div class="chart-container">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    <!-- Distribusi Kehadiran Bulan ini -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Distribusi Kehadiran</h3>
            <span class="text-sm text-gray-500">Bulan <?= date('F Y'); ?></span>
        </div>
        <div class="chart-container">
            <canvas id="attendancePieChart"></canvas>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-2">
            <?php $colors = ['bg-green-500', 'bg-blue-500', 'bg-yellow-500', 'bg-red-500'] ?>
            <?php $labels = ['Hadir', 'Izin', 'Sakit', 'Alpa'] ?>
            <?php for ($i = 0; $i < 4; $i++): ?>
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full <?= $colors[$i]; ?> mr-2"></div>
                    <span class="text-sm text-gray-600"><?= $labels[$i]; ?></span>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Absensi terbaru -->
    <div class="bg-white rounded-xl shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Absensi Terbaru</h3>
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">Lihat Semua</a>
            </div>
        </div>
        <div class="p-6">
            <?php if (!empty($recentAbsensi)): ?>
                <div class="space-y-4">
                    <?php foreach ($recentAbsensi as $absensi): ?>
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-clipboard-check text-indigo-600"></i>
                                </div>
                                <div>

                                    <p class="font-medium text-gray-800"><?= esc($absensi['nama_mapel']); ?></p>
                                    <p class="text-sm text-gray-600"><?= esc($absensi['nama_guru']); ?> ● <?= esc($absensi['nama_kelas']); ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800"><?= date('d M', strtotime($absensi['tanggal'])); ?></p>
                                <p class="text-xs text-gray-500"><?= date('H:i', strtotime($absensi['created_at'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Belum ada Data Absensi</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Izin Menunggu -->
    <div class="bg-white rounded-xl shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Izin Menunggu Pesetujuan</h3>
                <a href="#" class="text-sm text-red-600 hover:text-red-800">Tinjau Semua</a>
            </div>
        </div>
        <div class="p-6">
            <?php if (!empty($pendingIzin)): ?>
                <div class="space-y-4">
                    <?php foreach ($pendingIzin as $izin): ?>
                        <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-gray-800"><?= esc($izin['nama_lengkap']); ?></p>
                                    <p class="text-sm text-gray-600">NIS: <?= esc($izin['nis']); ?> ● <?= esc($izin['nama_kelas']); ?></p>
                                    <p class="text-sm text-gray-600 mt-1"><?= esc($izin['alasan']); ?></p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                    <?= ucfirst($izin['jenis_izin']); ?>
                                </span>
                            </div>
                            <div class="mt-3 flex justify-between items-center">
                                <span class="text-sm text-gray-500"><?= date('d M Y', strtotime($izin['tanggal'])); ?></span>
                                <div class="space-x-2">
                                    <button class="text-xs px-3 py-1 bg-green-100 text-green-800 rounded-full hover:bg-green-200">Setujui</button>
                                    <button class="text-xs px-3 py-1 bg-red-100 text-red-800 rounded-full hover:bg-red-200">Tolak</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Tidak ada izin menunggu</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Kelas Sumary -->
<div class="bg-white rounded-xl shadow mb-8">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Ringkasan Kelas</h3>
            <a href="<?= base_url('admin/kelas'); ?>" class="text-sm text-indigo-600 hover:text-indigo-800">Lihat Semua</a>
        </div>
    </div>
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wali Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Absensi Hari Ini</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Absensi Bulan Ini</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($kelasSummary as $kelas): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-chalkboard text-indigo-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= esc($kelas['nama_kelas']); ?></div>
                                        <div class="'text-sm text-gray-500"><?= esc($kelas['tingkat']); ?> ● <?= esc($kelas['jurusan']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= esc($kelas['wali_kelas']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-900"><?= $kelas['total_siswa']; ?> Siswa</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= $kelas['absensi_hari_ini']; ?> Sesi</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= $kelas['absensi_bulan_ini']; ?> Sesi</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Lihat</a>
                                <a href="#" class="text-green-600 hover:textgreen-900">Rekap</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialize Charts
    document.addEventListener('DOMContentLoaded', function() {
        // Attendance Line Chart
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chartData['attendanceLine']['labels']); ?>,
                datasets: [{
                    label: 'Sesi Absensi',
                    data: <?= json_encode($chartData['attendanceLine']['data']); ?>,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130,246,0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Attendance Pie Chart
        const pieCtx = document.getElementById('attendancePieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: <?= json_encode($chartData['attendancePie']['labels']); ?>,
                datasets: [{
                    data: <?= json_encode($chartData['attendancePie']['data']); ?>,
                    backgroundColor: <?= json_encode($chartData['attendancePie']['colors']); ?>,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: false
                }
            }
        });
    });

    // Quick Actions
    function performQuickAction(action) {
        fetch('<?= base_url("admin/dashboard/quick-action"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    if (action === 'refresh_stats') {
                        // Reload page to update stats
                        location.reload();
                    }
                } else {
                    alert('Error: ' + data.message)
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
    }
</script>
<?= $this->endSection() ?>