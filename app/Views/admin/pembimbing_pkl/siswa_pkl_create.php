<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle ?></h2>
        <p class="text-gray-600"><?= $pageDescription ?></p>
    </div>

    <?= view('components/alerts') ?>

    <form action="<?= base_url('admin/pembimbing-pkl/siswa-pkl/simpan') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-graduate mr-2 text-gray-600"></i> Data Penempatan
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Siswa (Kelas XII) *</label>
                        <select name="siswa_id" id="siswaSelect" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Siswa</option>
                            <?php foreach ($siswaList as $id => $nama): ?>
                                <option value="<?= $id ?>" <?= old('siswa_id') == $id ? 'selected' : '' ?>>
                                    <?= esc($nama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($validation->hasError('siswa_id')): ?>
                            <p class="text-red-600 text-xs mt-1"><?= $validation->getError('siswa_id') ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat PKL *</label>
                        <select name="tempat_pkl_id" id="tempatPklSelect" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Tempat PKL</option>
                            <?php foreach ($tempatPklList as $id => $nama): ?>
                                <option value="<?= $id ?>" <?= old('tempat_pkl_id') == $id ? 'selected' : '' ?>>
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
                        <select name="tahun_ajaran" id="tahunAjaranSelect" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Tahun Ajaran</option>
                            <?php
                            $currentYear = date('Y');
                            $startYear = $currentYear - 2;
                            for ($y = $startYear; $y <= $currentYear + 1; $y++):
                                $ta = $y . '/' . ($y + 1);
                            ?>
                                <option value="<?= $ta ?>" <?= (old('tahun_ajaran') == $ta) ? 'selected' : '' ?>>
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
                    <i class="fas fa-info-circle mr-2 text-gray-600"></i> Informasi Pembimbing
                </h3>

                <div id="pembimbingInfo" class="space-y-4 text-sm text-gray-600">
                    <p class="text-gray-500 italic">Pilih tempat PKL dan tahun ajaran untuk melihat pembimbing otomatis.</p>
                    <div id="pembimbingResult" class="hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm font-medium text-blue-800 mb-2">
                                <i class="fas fa-chalkboard-teacher mr-2"></i>Pembimbing PKL:
                            </p>
                            <ul id="pembimbingList" class="list-disc list-inside text-blue-700 space-y-1"></ul>
                        </div>
                    </div>
                    <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-sm text-green-800">
                            <i class="fas fa-check-circle mr-2"></i> Pembimbing otomatis menyesuaikan dengan tempat PKL yang dipilih.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3 border-t pt-6">
            <a href="<?= base_url('admin/pembimbing-pkl/siswa-pkl') ?>"
                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                <i class="fas fa-save mr-2"></i> Simpan Penempatan
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tempatPklSelect = document.getElementById('tempatPklSelect');
        const tahunAjaranSelect = document.getElementById('tahunAjaranSelect');

        function loadPembimbing() {
            const tempatPklId = tempatPklSelect.value;
            const tahunAjaran = tahunAjaranSelect.value;

            if (!tempatPklId || !tahunAjaran) {
                document.getElementById('pembimbingResult').classList.add('hidden');
                return;
            }

            fetch('<?= base_url('admin/pembimbing-pkl/get-pembimbing-by-tempat-pkl') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    tempat_pkl_id: tempatPklId,
                    tahun_ajaran: tahunAjaran
                })
            })
            .then(response => response.json())
            .then(data => {
                const list = document.getElementById('pembimbingList');
                const container = document.getElementById('pembimbingResult');
                list.innerHTML = '';

                if (data && data.length > 0) {
                    data.forEach(function(p) {
                        const li = document.createElement('li');
                        li.className = 'text-blue-700';
                        li.textContent = p.nama_guru + ' (' + p.nip + ')';
                        list.appendChild(li);
                    });
                    container.classList.remove('hidden');
                } else {
                    const li = document.createElement('li');
                    li.className = 'text-yellow-600';
                    li.textContent = 'Belum ada pembimbing untuk tempat PKL ini di tahun ajaran tersebut.';
                    list.appendChild(li);
                    container.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        tempatPklSelect.addEventListener('change', loadPembimbing);
        tahunAjaranSelect.addEventListener('change', loadPembimbing);
    });
</script>
<?= $this->endSection() ?>
