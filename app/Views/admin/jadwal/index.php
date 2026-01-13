<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Jadwal Mengajar</h1>
            <p class="text-gray-600">Kelola jadwal mengajar guru</p>
        </div>
        <div class="flex space-x-2">
            <a href="<?= base_url('admin/jadwal/import'); ?>"
                class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-file-import mr-2"></i> Import
            </a>
            <a href="<?= base_url('admin/jadwal/export'); ?>"
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-file-excel mr-2"></i> Export
            </a>
            <a href="<?= base_url('admin/jadwal/tambah'); ?>"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Jadwal
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="get" class="flex flex-col md:flex-row md:items-center md:space-x-4">
            <div class="flex-1 mb-4 md:mb-0">
                <div class="relative">
                    <input type="text"
                        name="search"
                        value="<?= $search ?>"
                        placeholder="Cari guru, mapel, atau kelas..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 md:mb-0">
                <div>
                    <select name="semester" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Semester</option>
                        <?php foreach ($semesterList as $key => $value): ?>
                            <option value="<?= $key; ?>" <?= $semester == $key ? 'selected' : ''; ?>>
                                <?= $value; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <select name="tahun_ajaran" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tahun Ajaran</option>
                        <?php foreach ($tahunAjaranList as $key => $value): ?>
                            <option value="<?= $key; ?>" <?= $tahunAjaran == $key ? 'selected' : ''; ?>>
                                <?= $value; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <select name="per_page" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="10" <?= $perPage == 10 ? 'selected' : ''; ?>>10 per halaman</option>
                        <option value="25" <?= $perPage == 25 ? 'selected' : ''; ?>>25 per halaman</option>
                        <option value="50" <?= $perPage == 50 ? 'selected' : ''; ?>>50 per halaman</option>
                        <option value="100" <?= $perPage == 100 ? 'selected' : ''; ?>>100 per halaman</option>
                    </select>
                </div>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Filter
                </button>
                <?php if ($search || $semester || $tahunAjaran): ?>
                    <a href="<?= base_url('admin/jadwal'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <?php
        $totalJadwal = $jadwal['pager']->getTotal() ?? 0;
        $jadwalData = $jadwal['jadwal'] ?? [];

        // Count by hari
        $countByHari = [];
        foreach ($hariList as $hari) {
            $countByHari[$hari] = 0;
        }

        foreach ($jadwalData as $item) {
            if (isset($countByHari[$item['hari']])) {
                $countByHari[$item['hari']]++;
            }
        }
        ?>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Jadwal</p>
                    <p class="text-2xl font-bold"><?= $totalJadwal ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Guru Aktif</p>
                    <p class="text-2xl font-bold">
                        <?= count(array_unique(array_column($jadwalData, 'guru_id'))) ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-school text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Kelas</p>
                    <p class="text-2xl font-bold">
                        <?= count(array_unique(array_column($jadwalData, 'kelas_id'))) ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jam/Minggu</p>
                    <p class="text-2xl font-bold">
                        <?= $totalJadwal * 2 ?> <!-- Asumsi 2 jam per pertemuan -->
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($jadwal['jadwal'])): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-calendar-times text-4xl mb-2"></i>
                                <p class="text-lg">Tidak ada data jadwal</p>
                                <a href="<?= base_url('admin/jadwal/tambah'); ?>" class="text-blue-500 hover:text-blue-700 mt-2 inline-block">
                                    <i class="fas fa-plus mr-1"></i> Tambah jadwal pertama
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = (($jadwal['pager']->getCurrentPage() - 1) * $perPage) + 1; ?>
                        <?php foreach ($jadwal['jadwal'] as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $no++; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        <?= $item['hari'] == 'Senin' ? 'bg-red-100 text-red-800' : ($item['hari'] == 'Selasa' ? 'bg-yellow-100 text-yellow-800' : ($item['hari'] == 'Rabu' ? 'bg-green-100 text-green-800' : ($item['hari'] == 'Kamis' ? 'bg-blue-100 text-blue-800' : ($item['hari'] == 'Jumat' ? 'bg-purple-100 text-purple-800' :
                                                            'bg-gray-100 text-gray-800')))) ?>">
                                        <?= $item['hari']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= date('H:i', strtotime($item['jam_mulai'])) ?> - <?= date('H:i', strtotime($item['jam_selesai'])) ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?= $item['semester'] ?> - <?= $item['tahun_ajaran'] ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $item['nama_kelas']; ?></div>
                                    <div class="text-sm text-gray-500"><?= $item['tingkat'] ?> - <?= $item['jurusan'] ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600 text-xs"></i>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900"><?= $item['nama_guru']; ?></div>
                                            <div class="text-xs text-gray-500"><?= $item['nip']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $item['nama_mapel']; ?></div>
                                    <div class="text-sm text-gray-500"><?= $item['kode_mapel']; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?= base_url('admin/jadwal/edit/' . $item['id']); ?>"
                                        class="text-yellow-600 hover:text-yellow-900 mr-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="#"
                                        onclick="confirmDelete('<?= $item['id']; ?>', '<?= $item['nama_mapel']; ?>')"
                                        class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($jadwal['pager']->getPageCount() > 1): ?>
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <?= $jadwal['pager']->links(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function confirmDelete(id, name) {
        if (confirm(`Apakah Anda yakin ingin menghapus jadwal "${name}"?\n\nPERHATIAN: Jika sudah ada absensi, jadwal tidak dapat dihapus.`)) {
            window.location.href = '<?= base_url('admin/jadwal/hapus/'); ?>' + id;
        }
    }
</script>
<?= $this->endSection() ?>