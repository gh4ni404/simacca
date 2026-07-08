<?= $this->extend('templates/main_layout') ?>
<?= $this->section('styles') ?>
<style>
    .table-responsive {
        overflow-x: auto;
    }
    .badge-pembimbing {
        background-color: #DBEAFE;
        color: #1E40AF;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle; ?></h2>
            <p class="text-gray-600"><?= $pageDescription; ?></p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="<?= base_url('admin/pembimbing-pkl/tempat-pkl'); ?>"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-building mr-2"></i> Kelola Tempat PKL
            </a>
            <a href="<?= base_url('admin/pembimbing-pkl/tambah'); ?>"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Pembimbing
            </a>
        </div>
    </div>

    <!-- Filter Tahun Ajaran -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div class="w-full md:w-1/3">
            <form method="GET" action="<?= base_url('admin/pembimbing-pkl') ?>" class="flex space-x-2">
                <select name="tahun_ajaran" id="filterTahun"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Tahun Ajaran</option>
                    <?php foreach ($tahunAjaranList as $ta): ?>
                        <option value="<?= esc($ta) ?>" <?= ($selectedTahun == $ta) ? 'selected' : '' ?>>
                            <?= esc($ta) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-filter"></i>
                </button>
                <?php if ($selectedTahun): ?>
                    <a href="<?= base_url('admin/pembimbing-pkl') ?>"
                        class="border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 flex items-center">
                        Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Guru</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tempat PKL</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kota</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Ajaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($pembimbing)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-user-graduate text-4xl text-gray-300 mb-4"></i>
                            <p>Belum ada data pembimbing PKL</p>
                            <a href="<?= base_url('admin/pembimbing-pkl/tambah'); ?>" class="text-indigo-600 hover:text-indigo-800 mt-2 inline-block">Tambah pembimbing pertama</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php foreach ($pembimbing as $p): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= esc($p['nama_guru']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($p['nip']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($p['nama_perusahaan']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($p['kota'] ?? '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full badge-pembimbing">
                                    <?= esc($p['tahun_ajaran']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <a href="<?= base_url('admin/pembimbing-pkl/edit/' . $p['id']); ?>"
                                        class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('admin/pembimbing-pkl/hapus/' . $p['id']); ?>"
                                        class="text-red-600 hover:text-red-900" title="Hapus"
                                        onclick="return confirm('Hapus data pembimbing PKL ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
