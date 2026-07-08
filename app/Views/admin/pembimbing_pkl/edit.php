<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle ?></h2>
        <p class="text-gray-600"><?= $pageDescription ?></p>
    </div>

    <?= view('components/alerts') ?>

    <form action="<?= base_url('admin/pembimbing-pkl/update/' . $pembimbing['id']) ?>" method="POST">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chalkboard-teacher mr-2 text-gray-600"></i> Data Pembimbing
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Guru *</label>
                        <select name="guru_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Guru</option>
                            <?php foreach ($guruList as $id => $nama): ?>
                                <option value="<?= $id ?>" <?= (old('guru_id', $pembimbing['guru_id']) == $id) ? 'selected' : '' ?>>
                                    <?= esc($nama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($validation->hasError('guru_id')): ?>
                            <p class="text-red-600 text-xs mt-1"><?= $validation->getError('guru_id') ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat PKL *</label>
                        <select name="tempat_pkl_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Tempat PKL</option>
                            <?php foreach ($tempatPklList as $id => $nama): ?>
                                <option value="<?= $id ?>" <?= (old('tempat_pkl_id', $pembimbing['tempat_pkl_id']) == $id) ? 'selected' : '' ?>>
                                    <?= esc($nama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($validation->hasError('tempat_pkl_id')): ?>
                            <p class="text-red-600 text-xs mt-1"><?= $validation->getError('tempat_pkl_id') ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran *</label>
                        <select name="tahun_ajaran" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Tahun Ajaran</option>
                            <?php
                            $currentYear = date('Y');
                            $startYear = $currentYear - 2;
                            for ($y = $startYear; $y <= $currentYear + 1; $y++):
                                $ta = $y . '/' . ($y + 1);
                            ?>
                                <option value="<?= $ta ?>" <?= (old('tahun_ajaran', $pembimbing['tahun_ajaran']) == $ta) ? 'selected' : '' ?>>
                                    <?= $ta ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <?php if ($validation->hasError('tahun_ajaran')): ?>
                            <p class="text-red-600 text-xs mt-1"><?= $validation->getError('tahun_ajaran') ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-gray-600"></i> Informasi
                </h3>
                <div class="space-y-4 text-sm text-gray-600">
                    <p><i class="fas fa-check-circle text-green-500 mr-2"></i> Setiap guru dapat menjadi pembimbing di lebih dari satu tempat PKL.</p>
                    <p><i class="fas fa-check-circle text-green-500 mr-2"></i> Pembagian pembimbing dapat berbeda setiap tahun ajaran.</p>
                    <p><i class="fas fa-check-circle text-green-500 mr-2"></i> Semua guru (Guru Mapel, Wali Kelas, Wakakur) bisa ditugaskan sebagai pembimbing.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3 border-t pt-6">
            <a href="<?= base_url('admin/pembimbing-pkl') ?>"
                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                <i class="fas fa-save mr-2"></i> Update Data
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
