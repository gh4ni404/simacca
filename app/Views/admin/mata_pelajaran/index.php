<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Mata Pelajaran</h1>
            <p class="text-gray-600">Kelola data mata pelajaran</p>
        </div>
        <div>
            <a href="<?= base_url('admin/mata-pelajaran/tambah'); ?>"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Mata Pelajaran
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
                        placeholder="Cari kode atau nama mata pelajaran..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <label class="mr-2 text-gray-700">Per Halaman:</label>
                    <select name="per_page" class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="10" <?= $perPage == 10 ? 'selected' : ''; ?>>10</option>
                        <option value="25" <?= $perPage == 25 ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?= $perPage == 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?= $perPage == 100 ? 'selected' : ''; ?>>100</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Cari
                </button>
                <?php if ($search): ?>
                    <a href="<?= base_url('admin/mata-pelajaran'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <?php
        $totalUmum = 0;
        $totalKejuruan = 0;

        foreach ($stats as $stat) {
            if ($stat['kategori'] == 'umum') {
                $totalUmum = $stat['total'];
            } else if ($stat['kategori'] == 'kejuruan') {
                $totalKejuruan = $stat['total'];
            }
        }
        $totalAll = $totalUmum + $totalKejuruan;
        ?>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-book text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Mata Pelajaran</p>
                    <p class="text-2xl font-bold"><?= $totalAll ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-graduation-cap text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Kejuruan / Umum</p>
                    <p class="text-2xl font-bold"><?= $totalKejuruan ?> / <?= $totalUmum ?></p>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Mapel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mata Pelajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($mapel['mapel'])): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p class="text-lg">Tidak ada data mata pelajaran</p>
                                <a href="<?= base_url('admin/mata-pelajaran/tambah'); ?>" class="text-blue-500 hover:text-blue-700 mt-2 inline-block">
                                    <i class="fas fa-plus mr-1"></i> Tambah data pertama
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = (($mapel['pager']->getCurrentPage() - 1) * $perPage) + 1; ?>
                        <?php foreach ($mapel['mapel'] as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $no++; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?= $item['kode_mapel']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?= $item['nama_mapel']; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        <?= $item['kategori'] == 'umum' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800'; ?>">
                                        <?= ucfirst($item['kategori']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?= base_url('admin/mata-pelajaran/edit/' . $item['id']); ?>"
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
        <?php if ($mapel['pager']->getPageCount() > 1): ?>
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <?= $mapel['pager']->links(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function confirmDelete(id, name) {
        if (confirm(`Apakah Anda yakin ingin menghapus mata pelajaran "${name}"?`)) {
            window.location.href = '<?= base_url('admin/mata-pelajaran/hapus/'); ?>' + id;
        }
    }
</script>
<?= $this->endSection() ?>