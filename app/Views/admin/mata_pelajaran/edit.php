<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Mata Pelajaran</h1>
        <p class="text-gray-600">Edit data mata pelajaran</p>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="<?= base_url('admin/mata-pelajaran/update/' . $mapel['id']); ?>" method="post">
            <?= csrf_field(); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kode Mapel -->
                <div>
                    <label for="kode_mapel" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Mata Pelajaran *
                    </label>
                    <input type="text"
                        id="kode_mapel"
                        name="kode_mapel"
                        value="<?= old('kode_mapel', $mapel['kode_mapel']); ?>"
                        class="w-full px-4 py-2 border <?= session('errors.kode_mapel') ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <?php if (session('errors.kode_mapel')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.kode_mapel'); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Kategori -->
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori *
                    </label>
                    <select id="kategori"
                        name="kategori"
                        class="w-full px-4 py-2 border <?= session('errors.kategori') ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Kategori</option>
                        <option value="umum" <?= (old('kategori', $mapel['kategori']) == 'umum') ? 'selected' : ''; ?>>Umum</option>
                        <option value="kejuruan" <?= (old('kategori', $mapel['kategori']) == 'kejuruan') ? 'selected' : ''; ?>>Kejuruan</option>
                    </select>
                    <?php if (session('errors.kategori')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.kategori'); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Nama Mapel -->
                <div class="md:col-span-2">
                    <label for="nama_mapel" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Mata Pelajaran *
                    </label>
                    <input type="text"
                        id="nama_mapel"
                        name="nama_mapel"
                        value="<?= old('nama_mapel', $mapel['nama_mapel']); ?>"
                        class="w-full px-4 py-2 border <?= session('errors.nama_mapel') ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <?php if (session('errors.nama_mapel')): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors.nama_mapel'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="<?= base_url('admin/mata-pelajaran'); ?>"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>