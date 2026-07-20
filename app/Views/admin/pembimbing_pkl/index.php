<?= $this->extend(get_device_layout()) ?>
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

    <!-- Filter -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <form method="GET" action="<?= base_url('admin/pembimbing-pkl') ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Tahun Ajaran</option>
                        <?php foreach ($tahunAjaranList as $ta): ?>
                            <option value="<?= esc($ta) ?>" <?= ($selectedTahun == $ta) ? 'selected' : '' ?>>
                                <?= esc($ta) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Guru</label>
                    <select name="guru_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Guru</option>
                        <?php foreach ($guruFilterList as $g): ?>
                            <option value="<?= esc($g['id']) ?>" <?= ($selectedGuru == $g['id']) ? 'selected' : '' ?>>
                                <?= esc($g['nama_lengkap']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat PKL</label>
                    <select name="tempat_pkl_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Tempat PKL</option>
                        <?php foreach ($tempatFilterList as $t): ?>
                            <option value="<?= esc($t['id']) ?>" <?= ($selectedTempat == $t['id']) ? 'selected' : '' ?>>
                                <?= esc($t['nama_perusahaan']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                    <select name="kota"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Kota</option>
                        <?php foreach ($kotaFilterList as $k): ?>
                            <option value="<?= esc($k) ?>" <?= ($selectedKota == $k) ? 'selected' : '' ?>>
                                <?= esc($k) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <?php if ($selectedTahun || $selectedGuru || $selectedTempat || $selectedKota): ?>
                        <a href="<?= base_url('admin/pembimbing-pkl') ?>"
                            class="border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 text-sm flex items-center">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
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
