<?= $this->extend(get_device_layout()) ?>
<?= $this->section('styles') ?>
<style>
    .table-scroll {
        overflow-y: auto;
        max-height: 400px;
        border-bottom: 1px solid #E5E7EB;
    }
    .table-scroll thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .badge-sudah { background-color: #D1FAE5; color: #065F46; }
    .badge-belum { background-color: #FEF3C7; color: #92400E; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle ?></h2>
        <p class="text-gray-600"><?= $pageDescription ?></p>
    </div>

    <?= view('components/alerts') ?>

    <form action="<?= base_url('admin/pembimbing-pkl/siswa-pkl/batch-simpan') ?>" method="POST" id="batchForm">
        <?= csrf_field() ?>

        <!-- Pilih Tempat PKL -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat PKL *</label>
                <select name="tempat_pkl_id" id="filterTempatPkl" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Pilih Tempat PKL</option>
                    <?php foreach ($tempatPklList as $id => $nama): ?>
                        <option value="<?= $id ?>" <?= $selectedTempatPkl == $id ? 'selected' : '' ?>>
                            <?= esc($nama) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Info Pembimbing -->
        <div id="pembimbingInfo" class="mb-6 hidden">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm font-medium text-blue-800">
                    <i class="fas fa-chalkboard-teacher mr-2"></i>Pembimbing untuk tempat PKL ini:
                    <span id="pembimbingNama" class="font-semibold">-</span>
                </p>
            </div>
        </div>

        <!-- Tabel Siswa -->
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="selectAll"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Pilih Semua</span>
                    </label>
                    <span class="text-sm text-gray-500" id="selectedCount">0 dipilih</span>
                </div>
                <div class="flex space-x-2">
                    <input type="text" id="searchSiswa" placeholder="Cari siswa..."
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="table-scroll">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-10">
                                <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tempat PKL Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($siswaList)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                    <p>Tidak ada siswa kelas XII yang aktif</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; ?>
                            <?php foreach ($siswaList as $s): ?>
                                <tr class="hover:bg-gray-50 siswa-row <?= $s['status'] == 'sudah' && $s['tempat_pkl_id'] != $selectedTempatPkl ? 'opacity-50' : '' ?>"
                                    data-status="<?= $s['status'] ?>"
                                    data-tempat="<?= $s['tempat_pkl_id'] ?>">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <input type="checkbox" name="siswa_ids[]" value="<?= $s['id'] ?>"
                                            class="siswa-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            <?= ($s['status'] == 'sudah' && $s['tempat_pkl_id'] != $selectedTempatPkl) ? 'disabled' : '' ?>>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900"><?= $no++; ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?= esc($s['nis']); ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($s['nama_lengkap']); ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?= esc($s['nama_kelas']); ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <?php if ($s['status'] == 'sudah'): ?>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full badge-sudah">
                                                <i class="fas fa-check mr-1"></i>Sudah
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full badge-belum">
                                                <i class="fas fa-clock mr-1"></i>Belum
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <?= esc($s['nama_perusahaan'] ?: '-'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-xs text-gray-500 mt-2">
            <i class="fas fa-info-circle mr-1"></i>
            Siswa yang sudah ditempatkan di tempat PKL lain tidak dapat dipilih.
        </p>

        <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
            <a href="<?= base_url('admin/pembimbing-pkl/siswa-pkl') ?>"
                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                <i class="fas fa-save mr-2"></i> Simpan Semua
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const selectAllHeader = document.getElementById('selectAllHeader');
        const checkboxes = document.querySelectorAll('.siswa-checkbox:not(:disabled)');
        const selectedCount = document.getElementById('selectedCount');
        const searchInput = document.getElementById('searchSiswa');

        function updateCount() {
            const checked = document.querySelectorAll('.siswa-checkbox:checked').length;
            selectedCount.textContent = checked + ' dipilih';
        }

        function toggleSelectAll(source) {
            checkboxes.forEach(function(cb) {
                cb.checked = source.checked;
            });
            if (selectAll !== source) selectAll.checked = source.checked;
            if (selectAllHeader !== source) selectAllHeader.checked = source.checked;
            updateCount();
        }

        selectAll.addEventListener('change', function() {
            toggleSelectAll(this);
        });
        selectAllHeader.addEventListener('change', function() {
            toggleSelectAll(this);
        });

        checkboxes.forEach(function(cb) {
            cb.addEventListener('change', updateCount);
        });

        searchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.siswa-row').forEach(function(row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });

        updateCount();

        const tempatPklSelect = document.getElementById('filterTempatPkl');
        const tahunAjaran = '<?= get_active_tahun_ajaran() ?>';
        const pembimbingInfo = document.getElementById('pembimbingInfo');
        const pembimbingNama = document.getElementById('pembimbingNama');

        function loadPembimbing() {
            const tempatPklId = tempatPklSelect.value;

            if (!tempatPklId) {
                pembimbingInfo.classList.add('hidden');
                return;
            }

            fetch('<?= base_url('admin/pembimbing-pkl/get-pembimbing-by-tempat-pkl') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({
                    tempat_pkl_id: tempatPklId,
                    tahun_ajaran: tahunAjaran
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const names = data.map(function(p) { return p.nama_guru; });
                    pembimbingNama.textContent = names.join(', ');
                    pembimbingInfo.classList.remove('hidden');
                } else {
                    pembimbingNama.textContent = 'Belum ada pembimbing untuk tahun ini';
                    pembimbingInfo.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        tempatPklSelect.addEventListener('change', loadPembimbing);

        if (tempatPklSelect.value) {
            loadPembimbing();
        }
    });
</script>
<?= $this->endSection() ?>
