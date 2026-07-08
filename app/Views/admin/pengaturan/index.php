<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle ?></h2>
        <p class="text-gray-600"><?= $pageDescription ?></p>
    </div>

    <!-- Tahun Ajaran Aktif -->
    <form action="<?= base_url('admin/pengaturan/update') ?>" method="post" class="max-w-lg mb-8">
        <?= csrf_field() ?>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran Aktif</label>
            <p class="text-gray-500 text-xs mb-3">Semua data siswa, jadwal, dan kegiatan akan menggunakan tahun ajaran ini.</p>
            <select name="tahun_ajaran"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Pilih Tahun Ajaran</option>
                <?php foreach ($tahunAjaranList as $tahun): ?>
                    <option value="<?= $tahun ?>" <?= $activeTahunAjaran == $tahun ? 'selected' : '' ?>>
                        <?= $tahun ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex items-center space-x-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-save mr-2"></i> Simpan
            </button>
            <a href="<?= base_url('admin/dashboard') ?>" class="text-gray-600 hover:text-gray-800 px-4 py-2">
                Batal
            </a>
        </div>
    </form>

    <hr class="my-8">

    <!-- Rollover Tahun Ajaran -->
    <div class="max-w-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Rollover Siswa</h3>
        <p class="text-gray-500 text-sm mb-4">
            Proses otomatis menaikkan kelas siswa berdasarkan tahun ajaran baru:
        </p>
        <ul class="text-sm text-gray-600 space-y-1 mb-4 list-disc list-inside">
            <li>Siswa kelas <strong>X</strong> → naik ke <strong>XI</strong> (cocok berdasarkan jurusan)</li>
            <li>Siswa kelas <strong>XI</strong> → naik ke <strong>XII</strong> (cocok berdasarkan jurusan)</li>
            <li>Siswa kelas <strong>XII</strong> → <strong>Lulus</strong> (dinonaktifkan)</li>
            <li>Semua siswa akan diupdate <strong>tahun ajaran</strong>-nya</li>
        </ul>

        <form action="<?= base_url('admin/pengaturan/rollover') ?>" method="post" onsubmit="return confirm('Yakin akan menjalankan rollover? Data kelas siswa akan berubah dan tidak bisa dibatalkan.')">
            <?= csrf_field() ?>
            <input type="hidden" name="tahun_ajaran" value="<?= $activeTahunAjaran ?>">
            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-arrow-up mr-2"></i> Jalankan Rollover
            </button>
        </form>
    </div>

    <!-- Rollover Result -->
    <?php $rolloverResult = session()->getFlashdata('rollover_result'); ?>
    <?php if ($rolloverResult): ?>
        <div class="mt-8 max-w-2xl">
            <h4 class="text-md font-semibold text-gray-800 mb-3">Hasil Rollover</h4>
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-green-600"><?= $rolloverResult['naik_kelas'] ?></p>
                    <p class="text-sm text-green-700">Naik Kelas</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600"><?= $rolloverResult['lulus'] ?></p>
                    <p class="text-sm text-blue-700">Lulus</p>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-gray-600"><?= count($rolloverResult['skipped']) ?></p>
                    <p class="text-sm text-gray-700">Dilewati</p>
                </div>
            </div>

            <?php if (!empty($rolloverResult['updated'])): ?>
                <div class="mt-4">
                    <h5 class="text-sm font-medium text-gray-700 mb-2">Detail Perubahan:</h5>
                    <div class="max-h-60 overflow-y-auto bg-gray-50 rounded-lg p-3">
                        <?php foreach ($rolloverResult['updated'] as $item): ?>
                            <p class="text-xs text-gray-600 py-0.5"><?= esc($item) ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($rolloverResult['skipped'])): ?>
                <div class="mt-4">
                    <h5 class="text-sm font-medium text-red-700 mb-2">Siswa Dilewati (tidak ada kelas tujuan):</h5>
                    <div class="max-h-40 overflow-y-auto bg-red-50 rounded-lg p-3">
                        <?php foreach ($rolloverResult['skipped'] as $item): ?>
                            <p class="text-xs text-red-600 py-0.5"><?= esc($item) ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
