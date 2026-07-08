<?= $this->extend(get_device_layout()) ?>
<?= $this->section('styles') ?>
<style>
    .table-responsive { overflow-x: auto; }
    .badge-pkl { background-color: #DBEAFE; color: #1E40AF; }
    .badge-ditempatkan { background-color: #D1FAE5; color: #065F46; }
    .badge-belum { background-color: #FEF3C7; color: #92400E; }
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
            <a href="<?= base_url('admin/pembimbing-pkl/siswa-pkl/batch'); ?>"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-users mr-2"></i> Tempatkan Batch
            </a>
            <a href="<?= base_url('admin/pembimbing-pkl/siswa-pkl/tambah'); ?>"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Tempatkan Siswa
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-blue-600">Total Siswa XII</p>
                    <p class="text-2xl font-bold text-blue-600"><?= $stats['totalSiswaXII'] ?? 0; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-green-600">Sudah Ditempatkan</p>
                    <p class="text-2xl font-bold text-green-600"><?= $stats['sudahDitempatkan'] ?? 0; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-yellow-600">Belum Ditempatkan</p>
                    <p class="text-2xl font-bold text-yellow-600"><?= $stats['belumDitempatkan'] ?? 0; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tahun Ajaran -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div class="w-full md:w-1/3">
            <form method="GET" action="<?= base_url('admin/pembimbing-pkl/siswa-pkl') ?>" class="flex space-x-2">
                <select name="tahun_ajaran"
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
                    <a href="<?= base_url('admin/pembimbing-pkl/siswa-pkl') ?>"
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tempat PKL</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembimbing</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Ajaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($siswaPkl)): ?>
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-user-graduate text-4xl text-gray-300 mb-4"></i>
                            <p>Belum ada penempatan siswa PKL</p>
                            <a href="<?= base_url('admin/pembimbing-pkl/siswa-pkl/tambah'); ?>" class="text-indigo-600 hover:text-indigo-800 mt-2 inline-block">Tempatkan siswa sekarang</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php foreach ($siswaPkl as $sp): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= esc($sp['nama_siswa']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($sp['nis']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($sp['nama_kelas'] ?? '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($sp['nama_perusahaan'] ?? '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($sp['nama_pembimbing'] ?? '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full badge-pkl">
                                    <?= esc($sp['tahun_ajaran']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($sp['nama_perusahaan']): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full badge-ditempatkan">
                                        <i class="fas fa-check mr-1"></i> Ditempatkan
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full badge-belum">
                                        <i class="fas fa-clock mr-1"></i> Belum
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <a href="<?= base_url('admin/pembimbing-pkl/siswa-pkl/hapus/' . $sp['id']); ?>"
                                        class="text-red-600 hover:text-red-900" title="Hapus"
                                        onclick="return confirm('Hapus penempatan PKL untuk <?= esc($sp['nama_siswa'], 'js') ?>?')">
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
