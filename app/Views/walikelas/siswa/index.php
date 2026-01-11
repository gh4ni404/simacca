<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-users mr-2 text-blue-600"></i>
                    Data Siswa Kelas
                </h1>
                <p class="text-gray-600 mt-1">Daftar siswa di kelas <?= esc($kelas['nama_kelas']); ?></p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-school mr-1"></i>
                    Kelas: <span class="font-semibold"><?= esc($kelas['nama_kelas']); ?></span>
                    <span class="mx-2">•</span>
                    <i class="fas fa-users mr-1"></i>
                    Total: <span class="font-semibold"><?= count($siswa); ?> siswa</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-3">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Siswa</p>
                    <p class="text-2xl font-bold"><?= count($siswa); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-3">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Siswa Aktif</p>
                    <p class="text-2xl font-bold">
                        <?php 
                        $aktif = array_filter($siswa, function($s) { return $s['is_active'] == 1; });
                        echo count($aktif);
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-3">
                    <i class="fas fa-mars text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Laki-laki</p>
                    <p class="text-2xl font-bold">
                        <?php 
                        $laki = array_filter($siswa, function($s) { return $s['jenis_kelamin'] == 'L'; });
                        echo count($laki);
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-pink-100 text-pink-600 mr-3">
                    <i class="fas fa-venus text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Perempuan</p>
                    <p class="text-2xl font-bold">
                        <?php 
                        $perempuan = array_filter($siswa, function($s) { return $s['jenis_kelamin'] == 'P'; });
                        echo count($perempuan);
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Cari siswa (nama atau NIS)..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div class="flex gap-2">
                <select id="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
                <select id="filterGender" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Jenis Kelamin</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Siswa Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kehadiran Bulan Ini</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="siswaTableBody">
                    <?php if (empty($siswa)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Belum ada data siswa</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($siswa as $s): ?>
                        <tr class="hover:bg-gray-50 transition-colors siswa-row" 
                            data-nama="<?= strtolower(esc($s['nama_lengkap'])); ?>"
                            data-nis="<?= esc($s['nis']); ?>"
                            data-status="<?= $s['is_active']; ?>"
                            data-gender="<?= $s['jenis_kelamin']; ?>">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($s['nis']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= esc($s['nama_lengkap']); ?></div>
                                        <div class="text-xs text-gray-500"><?= esc($s['email'] ?? '-'); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium <?= $s['jenis_kelamin'] == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800'; ?>">
                                    <i class="fas <?= $s['jenis_kelamin'] == 'L' ? 'fa-mars' : 'fa-venus'; ?> mr-1"></i>
                                    <?= $s['jenis_kelamin'] == 'L' ? 'L' : 'P'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($s['username']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($s['is_active']): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Aktif
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Tidak Aktif
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="text-gray-600">Kehadiran</span>
                                            <span class="font-semibold text-gray-900"><?= $s['persentase_hadir']; ?>%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full <?= $s['persentase_hadir'] >= 80 ? 'bg-green-500' : ($s['persentase_hadir'] >= 60 ? 'bg-yellow-500' : 'bg-red-500'); ?>" 
                                                 style="width: <?= $s['persentase_hadir']; ?>%"></div>
                                        </div>
                                        <div class="flex gap-2 mt-1 text-xs">
                                            <span class="text-green-600" title="Hadir">H: <?= $s['kehadiran']['hadir']; ?></span>
                                            <span class="text-blue-600" title="Sakit">S: <?= $s['kehadiran']['sakit']; ?></span>
                                            <span class="text-yellow-600" title="Izin">I: <?= $s['kehadiran']['izin']; ?></span>
                                            <span class="text-red-600" title="Alpa">A: <?= $s['kehadiran']['alpa']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info Footer -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Informasi Kehadiran:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li><span class="font-medium text-green-700">Hijau (≥80%)</span>: Kehadiran sangat baik</li>
                    <li><span class="font-medium text-yellow-700">Kuning (60-79%)</span>: Kehadiran cukup, perlu perhatian</li>
                    <li><span class="font-medium text-red-700">Merah (<60%)</span>: Kehadiran kurang, perlu tindakan</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterStatus = document.getElementById('filterStatus');
    const filterGender = document.getElementById('filterGender');
    const rows = document.querySelectorAll('.siswa-row');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusFilter = filterStatus.value;
        const genderFilter = filterGender.value;

        rows.forEach(row => {
            const nama = row.dataset.nama;
            const nis = row.dataset.nis;
            const status = row.dataset.status;
            const gender = row.dataset.gender;

            const matchSearch = nama.includes(searchTerm) || nis.includes(searchTerm);
            const matchStatus = !statusFilter || status === statusFilter;
            const matchGender = !genderFilter || gender === genderFilter;

            if (matchSearch && matchStatus && matchGender) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterTable);
    filterStatus.addEventListener('change', filterTable);
    filterGender.addEventListener('change', filterTable);
});
</script>
<?= $this->endSection() ?>
