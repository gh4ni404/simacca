<?= $this->extend(get_device_layout()) ?>
<?= $this->section('styles') ?>
<style>
    .table-responsive { overflow-x: auto; }
    .badge-pkl { background-color: #DBEAFE; color: #1E40AF; }
    .badge-ditempatkan { background-color: #D1FAE5; color: #065F46; }
    .badge-belum { background-color: #FEF3C7; color: #92400E; }
    .bulk-action-bar { transition: all 0.2s ease; }
    .bulk-action-bar.hidden { opacity: 0; pointer-events: none; transform: translateY(-8px); }
    .bulk-action-bar.visible { opacity: 1; pointer-events: auto; transform: translateY(0); }
    .batch-modal { transition: opacity 0.2s ease; }
    .batch-modal.hidden { opacity: 0; pointer-events: none; }
    .batch-modal.visible { opacity: 1; pointer-events: auto; }
    .batch-modal .modal-panel { transition: transform 0.2s ease; }
    .batch-modal.hidden .modal-panel { transform: translateY(16px); }
    .batch-modal.visible .modal-panel { transform: translateY(0); }
    .batch-table-scroll { overflow-y: auto; max-height: 320px; border-bottom: 1px solid #E5E7EB; }
    .batch-table-scroll thead th { position: sticky; top: 0; z-index: 10; background: #F9FAFB; }
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
            <button type="button" onclick="openBatchModal()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-users mr-2"></i> Tempatkan Batch
            </button>
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

    <!-- Filter -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <form method="GET" action="<?= base_url('admin/pembimbing-pkl/siswa-pkl') ?>">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <select name="kelas"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Kelas</option>
                        <?php foreach ($kelasFilterList as $k): ?>
                            <option value="<?= esc($k) ?>" <?= ($selectedKelas == $k) ? 'selected' : '' ?>>
                                <?= esc($k) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pembimbing PKL</label>
                    <select name="pembimbing_pkl_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="">Semua Pembimbing</option>
                        <?php foreach ($pembimbingFilterList as $p): ?>
                            <option value="<?= esc($p['id']) ?>" <?= ($selectedPembimbing == $p['id']) ? 'selected' : '' ?>>
                                <?= esc($p['nama_lengkap']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <?php if ($selectedTahun || $selectedTempat || $selectedKelas || $selectedPembimbing): ?>
                        <a href="<?= base_url('admin/pembimbing-pkl/siswa-pkl') ?>"
                            class="border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 text-sm flex items-center">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Action Bar -->
    <div id="bulkActionBar" class="bulk-action-bar hidden mb-4 bg-indigo-50 border border-indigo-200 rounded-lg p-4 flex flex-col md:flex-row items-start md:items-center justify-between">
        <div class="flex items-center space-x-4 mb-3 md:mb-0">
            <span class="text-sm font-medium text-indigo-700">
                <i class="fas fa-check-double mr-1"></i>
                <span id="selectedCount">0</span> siswa dipilih
            </span>
            <div class="flex items-center space-x-2">
                <select id="bulkAction"
                    class="border border-indigo-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                    <option value="">-- Pilih Aksi --</option>
                    <option value="delete">Hapus</option>
                </select>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button type="button" id="btnApplyBulk"
                onclick="confirmBulkAction()"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                disabled>
                <i class="fas fa-paper-plane mr-1"></i> Terapkan
            </button>
            <button type="button" id="btnCancelBulk"
                onclick="clearSelection()"
                class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-4 py-1.5 rounded-lg text-sm font-medium">
                <i class="fas fa-times mr-1"></i> Batal
            </button>
        </div>
    </div>

    <!-- Bulk Delete Form -->
    <form id="bulkDeleteForm" method="POST" action="<?= base_url('admin/pembimbing-pkl/siswa-pkl/bulk-hapus') ?>" style="display:none;">
        <?= csrf_field() ?>
        <div id="bulkDeleteInputs"></div>
    </form>

    <div class="table-responsive">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" onclick="toggleSelectAll(this)">
                    </th>
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
                        <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-user-graduate text-4xl text-gray-300 mb-4"></i>
                            <p>Belum ada penempatan siswa PKL</p>
                            <a href="<?= base_url('admin/pembimbing-pkl/siswa-pkl/tambah'); ?>" class="text-indigo-600 hover:text-indigo-800 mt-2 inline-block">Tempatkan siswa sekarang</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php foreach ($siswaPkl as $sp): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="ids[]" value="<?= $sp['id']; ?>"
                                    class="row-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                    onchange="onRowCheckboxChange()">
                            </td>
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

<!-- Bulk Delete Confirmation Modal -->
<div id="bulkConfirmModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeBulkModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 relative z-10">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Konfirmasi Hapus</h3>
            <p class="text-gray-600 text-center mb-6" id="bulkConfirmMessage">
                Apakah Anda yakin ingin menghapus data yang dipilih?
            </p>
            <div class="flex justify-center space-x-3">
                <button type="button" onclick="closeBulkModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                    Batal
                </button>
                <button type="button" onclick="executeBulkDelete()"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
                    <i class="fas fa-trash mr-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Batch Placement Modal -->
<div id="batchModal" class="batch-modal hidden fixed inset-0 z-50">
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeBatchModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="modal-panel bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col relative z-10">

            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Batch Penempatan Siswa PKL</h3>
                    <p class="text-sm text-gray-500">Pilih banyak siswa sekaligus untuk ditempatkan</p>
                </div>
                <button type="button" onclick="closeBatchModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="flex-1 overflow-y-auto px-6 py-4">

                <!-- Tempat PKL Select -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat PKL *</label>
                    <select id="batchTempatPkl" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="">Pilih Tempat PKL</option>
                        <?php foreach ($tempatPklList as $id => $nama): ?>
                            <option value="<?= $id ?>"><?= esc($nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Pembimbing Select Dropdown -->
                <div id="batchPembimbingContainer" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pembimbing PKL *</label>
                    <select id="batchPembimbingSelect" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="">Pilih Pembimbing PKL</option>
                    </select>
                </div>

                <!-- Info Pembimbing (Fallback / Warning) -->
                <div id="batchPembimbingInfo" class="mb-4 hidden">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <p class="text-sm text-red-800 font-semibold">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <span id="batchPembimbingNama">Belum ada pembimbing</span>
                        </p>
                    </div>
                </div>

                <!-- Toolbar: Select All + Search -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="batchSelectAll"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Pilih Semua</span>
                        </label>
                        <span class="text-sm text-gray-500" id="batchSelectedCount">0 dipilih</span>
                    </div>
                    <input type="text" id="batchSearch" placeholder="Cari nama / NIS..."
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm w-56 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Student Table -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="batch-table-scroll">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-10">
                                        <input type="checkbox" id="batchSelectAllHeader"
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Siswa</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                                </tr>
                            </thead>
                            <tbody id="batchStudentBody" class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                        <p class="text-sm">Memuat data siswa...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between px-6 py-4 border-t bg-gray-50 rounded-b-xl">
                <p class="text-xs text-gray-400" id="batchEmptyMsg"></p>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeBatchModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">
                        Batal
                    </button>
                    <button type="button" id="btnBatchSubmit" onclick="submitBatchForm()"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
/* ===== MAIN TABLE: Bulk Action ===== */
function toggleSelectAll(el) {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = el.checked);
    updateBulkBar();
}
function onRowCheckboxChange() {
    const cbs = document.querySelectorAll('.row-checkbox');
    const sa = document.getElementById('selectAll');
    const all = cbs.length > 0 && [...cbs].every(cb => cb.checked);
    sa.checked = all;
    sa.indeterminate = !all && [...cbs].some(cb => cb.checked);
    updateBulkBar();
}
function updateBulkBar() {
    const checked = document.querySelectorAll('.row-checkbox:checked');
    const bar = document.getElementById('bulkActionBar');
    document.getElementById('selectedCount').textContent = checked.length;
    if (checked.length > 0) {
        bar.classList.remove('hidden'); bar.classList.add('visible');
    } else {
        bar.classList.add('hidden'); bar.classList.remove('visible');
        document.getElementById('bulkAction').value = '';
        document.getElementById('btnApplyBulk').disabled = true;
    }
}
document.getElementById('bulkAction').addEventListener('change', function() {
    document.getElementById('btnApplyBulk').disabled = !this.value;
});
function clearSelection() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    document.getElementById('selectAll').indeterminate = false;
    document.getElementById('bulkAction').value = '';
    document.getElementById('btnApplyBulk').disabled = true;
    updateBulkBar();
}
function confirmBulkAction() {
    const action = document.getElementById('bulkAction').value;
    const checked = document.querySelectorAll('.row-checkbox:checked');
    if (!action || checked.length === 0) return;
    if (action === 'delete') {
        document.getElementById('bulkConfirmMessage').textContent =
            'Apakah Anda yakin ingin menghapus ' + checked.length + ' penempatan siswa PKL yang dipilih?';
        document.getElementById('bulkConfirmModal').classList.remove('hidden');
    }
}
function closeBulkModal() { document.getElementById('bulkConfirmModal').classList.add('hidden'); }
function executeBulkDelete() {
    const checked = document.querySelectorAll('.row-checkbox:checked');
    const box = document.getElementById('bulkDeleteInputs');
    box.innerHTML = '';
    checked.forEach(cb => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
        box.appendChild(inp);
    });
    document.getElementById('bulkDeleteForm').submit();
}

/* ===== BATCH PLACEMENT MODAL ===== */
let batchSiswaData = [];

function openBatchModal() {
    const modal = document.getElementById('batchModal');
    modal.classList.remove('hidden');
    modal.classList.add('visible');
    document.body.style.overflow = 'hidden';

    document.getElementById('batchTempatPkl').value = '';
    document.getElementById('batchPembimbingInfo').classList.add('hidden');
    document.getElementById('batchSearch').value = '';
    document.getElementById('batchSelectedCount').textContent = '0 dipilih';
    document.getElementById('batchSelectAll').checked = false;
    document.getElementById('batchSelectAllHeader').checked = false;
    document.getElementById('btnBatchSubmit').disabled = true;
    document.getElementById('batchEmptyMsg').textContent = '';

    loadBatchStudents();
}

function closeBatchModal() {
    const modal = document.getElementById('batchModal');
    modal.classList.add('hidden');
    modal.classList.remove('visible');
    document.body.style.overflow = '';
}

function loadBatchStudents() {
    const tbody = document.getElementById('batchStudentBody');
    tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">' +
        '<i class="fas fa-spinner fa-spin text-2xl mb-2"></i><p class="text-sm">Memuat data siswa...</p></td></tr>';

    fetch('<?= base_url('admin/pembimbing-pkl/get-siswa-xii-unplaced') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        }
    })
    .then(r => r.json())
    .then(result => {
        batchSiswaData = result.data || [];
        renderBatchTable(batchSiswaData);
    })
    .catch(() => {
        tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-red-400">' +
            '<i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p class="text-sm">Gagal memuat data siswa</p></td></tr>';
    });
}

function renderBatchTable(list) {
    const tbody = document.getElementById('batchStudentBody');
    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">' +
            '<i class="fas fa-users text-4xl text-gray-300 mb-2"></i>' +
            '<p class="text-sm">Tidak ada siswa kelas XII yang belum ditempatkan</p></td></tr>';
        document.getElementById('batchEmptyMsg').textContent = 'Semua siswa sudah ditempatkan.';
        return;
    }
    let html = '';
    list.forEach((s, i) => {
        html += '<tr class="hover:bg-gray-50 batch-row">' +
            '<td class="px-4 py-3 whitespace-nowrap">' +
                '<input type="checkbox" class="batch-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" data-id="' + s.id + '" onchange="onBatchCheckboxChange()">' +
            '</td>' +
            '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">' + (i + 1) + '</td>' +
            '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(s.nis || '') + '</td>' +
            '<td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">' + escapeHtml(s.nama_lengkap || '') + '</td>' +
            '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(s.nama_kelas || '-') + '</td>' +
            '</tr>';
    });
    tbody.innerHTML = html;
    document.getElementById('batchEmptyMsg').textContent = list.length + ' siswa tersedia.';
}

function escapeHtml(t) {
    const d = document.createElement('div'); d.textContent = t; return d.innerHTML;
}

/* Batch select all */
document.getElementById('batchSelectAll').addEventListener('change', function() {
    const checked = this.checked;
    document.querySelectorAll('.batch-checkbox').forEach(cb => cb.checked = checked);
    document.getElementById('batchSelectAllHeader').checked = checked;
    updateBatchCount();
});
document.getElementById('batchSelectAllHeader').addEventListener('change', function() {
    const checked = this.checked;
    document.querySelectorAll('.batch-checkbox').forEach(cb => cb.checked = checked);
    document.getElementById('batchSelectAll').checked = checked;
    updateBatchCount();
});

function onBatchCheckboxChange() {
    const cbs = document.querySelectorAll('.batch-checkbox');
    const all = cbs.length > 0 && [...cbs].every(cb => cb.checked);
    document.getElementById('batchSelectAll').checked = all;
    document.getElementById('batchSelectAllHeader').checked = all;
    updateBatchCount();
}

function updateBatchCount() {
    const n = document.querySelectorAll('.batch-checkbox:checked').length;
    document.getElementById('batchSelectedCount').textContent = n + ' dipilih';
    
    const tempatPklSelect = document.getElementById('batchTempatPkl');
    const pembimbingSelect = document.getElementById('batchPembimbingSelect');
    
    // Enabled submit only if:
    // 1. At least 1 student is selected
    // 2. A place is selected
    // 3. A mentor is selected AND the mentor dropdown is not hidden or is required
    const isPembimbingRequired = pembimbingSelect.hasAttribute('required');
    const isPembimbingSelected = !isPembimbingRequired || pembimbingSelect.value !== "";
    
    document.getElementById('btnBatchSubmit').disabled = n === 0 || !tempatPklSelect.value || !isPembimbingSelected;
}

/* Batch search */
document.getElementById('batchSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.batch-row').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

/* Listen to pembimbing selection change to re-evaluate submit eligibility */
document.getElementById('batchPembimbingSelect').addEventListener('change', function() {
    updateBatchCount();
});

/* Pembimbing info & dropdown */
document.getElementById('batchTempatPkl').addEventListener('change', function() {
    const select = document.getElementById('batchPembimbingSelect');
    const container = document.getElementById('batchPembimbingContainer');
    const info = document.getElementById('batchPembimbingInfo');
    const nameEl = document.getElementById('batchPembimbingNama');
    
    select.innerHTML = '<option value="">Pilih Pembimbing PKL</option>';
    
    if (!this.value) { 
        container.classList.add('hidden'); 
        info.classList.add('hidden'); 
        updateBatchCount();
        return; 
    }

    fetch('<?= base_url('admin/pembimbing-pkl/get-pembimbing-by-tempat-pkl') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        },
        body: JSON.stringify({ tempat_pkl_id: this.value, tahun_ajaran: '<?= get_active_tahun_ajaran() ?>' })
    })
    .then(r => r.json())
    .then(data => {
        if (data && data.length > 0) {
            data.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.nama_guru + ' (' + p.nip + ')';
                select.appendChild(opt);
            });
            container.classList.remove('hidden');
            info.classList.add('hidden');
            select.setAttribute('required', 'required');
        } else {
            container.classList.add('hidden');
            nameEl.textContent = 'Belum ada pembimbing untuk tempat PKL ini. Silakan tambahkan pembimbing PKL terlebih dahulu.';
            info.classList.remove('hidden');
            select.removeAttribute('required');
            alert('Peringatan: Tidak dapat menempatkan siswa karena belum ada pembimbing PKL yang ditugaskan ke lokasi ini.');
        }
        updateBatchCount();
    })
    .catch(() => { 
        container.classList.add('hidden');
        nameEl.textContent = 'Gagal memuat pembimbing'; 
        info.classList.remove('hidden');
        updateBatchCount();
    });
});

/* Submit batch form via AJAX */
function submitBatchForm() {
    const tempatPklId = document.getElementById('batchTempatPkl').value;
    const pembimbingPklId = document.getElementById('batchPembimbingSelect').value;
    const checked = document.querySelectorAll('.batch-checkbox:checked');
    if (!tempatPklId || !pembimbingPklId || checked.length === 0) return;

    const siswaIds = [...checked].map(cb => cb.dataset.id);

    const btn = document.getElementById('btnBatchSubmit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= base_url('admin/pembimbing-pkl/siswa-pkl/batch-simpan') ?>';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden'; csrfInput.name = '<?= csrf_token() ?>'; csrfInput.value = '<?= csrf_hash() ?>';
    form.appendChild(csrfInput);

    const tpInput = document.createElement('input');
    tpInput.type = 'hidden'; tpInput.name = 'tempat_pkl_id'; tpInput.value = tempatPklId;
    form.appendChild(tpInput);

    const pmInput = document.createElement('input');
    pmInput.type = 'hidden'; pmInput.name = 'pembimbing_pkl_id'; pmInput.value = pembimbingPklId;
    form.appendChild(pmInput);

    siswaIds.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'siswa_ids[]'; inp.value = id;
        form.appendChild(inp);
    });

    document.body.appendChild(form);
    form.submit();
}
</script>
<?= $this->endSection() ?>
