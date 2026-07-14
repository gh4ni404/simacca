<?= $this->extend(get_device_layout()) ?>
<?= $this->section('styles') ?>
<style>
    .table-responsive { overflow-x: auto; }
    .bulk-action-bar { transition: all 0.2s ease; }
    .bulk-action-bar.hidden { opacity: 0; pointer-events: none; transform: translateY(-8px); }
    .bulk-action-bar.visible { opacity: 1; pointer-events: auto; transform: translateY(0); }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-tasks mr-2 text-indigo-600"></i>Master Task PKL
        </h1>
        <p class="text-gray-600 mt-1">Kelola semua task PKL siswa — nonaktifkan, aktifkan, atau hapus</p>
    </div>

    <?= view('components/alerts') ?>

    <!-- Filter & Search -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <form method="GET" action="<?= base_url('admin/pkl-task'); ?>" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Cari nama siswa, NIS, atau judul task..."
                       value="<?= esc($search ?? '') ?>"
                       class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div class="w-full sm:w-40">
                <select name="status" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Aktif</option>
                    <option value="inactive" <?= ($status ?? '') === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
            <a href="<?= base_url('admin/pkl-task'); ?>" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors">
                <i class="fas fa-undo mr-2"></i>Reset
            </a>
        </form>
    </div>

    <!-- Bulk Action Bar -->
    <div id="bulkActionBar" class="bulk-action-bar hidden mb-4 bg-indigo-50 border border-indigo-200 rounded-lg p-4 flex flex-col md:flex-row items-start md:items-center justify-between">
        <div class="flex items-center space-x-4 mb-3 md:mb-0">
            <span class="text-sm font-medium text-indigo-700">
                <i class="fas fa-check-double mr-1"></i>
                <span id="selectedCount">0</span> task dipilih
            </span>
            <div class="flex items-center space-x-2">
                <select id="bulkAction" class="border border-indigo-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                    <option value="">-- Pilih Aksi --</option>
                    <option value="nonaktifkan">Nonaktifkan</option>
                    <option value="aktifkan">Aktifkan</option>
                    <option value="hapus">Hapus</option>
                </select>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button type="button" id="btnApplyBulk" onclick="confirmBulkAction()"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <i class="fas fa-paper-plane mr-1"></i> Terapkan
            </button>
            <button type="button" onclick="clearSelection()"
                class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-4 py-1.5 rounded-lg text-sm font-medium">
                <i class="fas fa-times mr-1"></i> Batal
            </button>
        </div>
    </div>

    <!-- Bulk Form -->
    <form id="bulkForm" method="POST" action="<?= base_url('admin/pkl-task/bulk-action'); ?>" style="display:none;">
        <?= csrf_field() ?>
        <input type="hidden" name="bulk_action" id="bulkActionInput">
        <div id="bulkInputs"></div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                        <th class="px-4 py-3 text-center w-10">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" onchange="toggleSelectAll(this)">
                        </th>
                        <th class="px-4 py-3 text-left">Siswa</th>
                        <th class="px-4 py-3 text-left">Task</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Progress</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($tasks)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2 block"></i>
                            Tidak ada task ditemukan
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                    <?php
                        $summary = (new \App\Models\PklTaskModel())->getProgressSummary($task['id']);
                        $totalProgress = $summary['total'];
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" name="ids[]" value="<?= $task['id'] ?>"
                                   class="row-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                   onchange="onRowCheckboxChange()">
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800"><?= esc($task['nama_lengkap']) ?></div>
                            <div class="text-xs text-gray-500"><?= esc($task['nis']) ?> &middot; <?= esc($task['nama_kelas'] ?? '-') ?></div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800"><?= esc($task['judul']) ?></div>
                            <?php if (!empty($task['estimasi'])): ?>
                            <div class="text-xs text-gray-500">Estimasi: <?= esc($task['estimasi']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?= esc($task['kategori_nama'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($task['status'] === 'active'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600"><?= $totalProgress ?></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <?php if ($task['status'] === 'active'): ?>
                                <form action="<?= base_url('admin/pkl-task/nonaktifkan/' . $task['id']); ?>" method="POST" class="inline" onsubmit="return confirm('Nonaktifkan task ini? Task tidak akan muncul di dropdown siswa.')">
                                    <?= csrf_field(); ?>
                                    <button type="submit" class="p-1.5 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors" title="Nonaktifkan"><i class="fas fa-ban text-xs"></i></button>
                                </form>
                                <?php else: ?>
                                <form action="<?= base_url('admin/pkl-task/aktifkan/' . $task['id']); ?>" method="POST" class="inline" onsubmit="return confirm('Aktifkan kembali task ini?')">
                                    <?= csrf_field(); ?>
                                    <button type="submit" class="p-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors" title="Aktifkan"><i class="fas fa-check text-xs"></i></button>
                                </form>
                                <?php endif; ?>
                                <form action="<?= base_url('admin/pkl-task/hapus/' . $task['id']); ?>" method="POST" class="inline" onsubmit="return confirm('Yakin hapus task ini? Semua progress terkait juga akan dihapus (soft delete).')">
                                    <?= csrf_field(); ?>
                                    <button type="submit" class="p-1.5 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors" title="Hapus"><i class="fas fa-trash text-xs"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeConfirmModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 relative z-10">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Konfirmasi</h3>
            <p class="text-gray-600 text-center mb-6" id="confirmMessage">Apakah Anda yakin?</p>
            <div class="flex justify-center space-x-3">
                <button type="button" onclick="closeConfirmModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Batal</button>
                <button type="button" onclick="executeBulkAction()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium"><i class="fas fa-trash mr-1"></i> Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<script>
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
    const labels = { nonaktifkan: 'dinonaktifkan', aktifkan: 'diaktifkan', hapus: 'dihapus' };
    document.getElementById('confirmMessage').textContent =
        'Apakah Anda yakin ingin ' + labels[action] + ' ' + checked.length + ' task yang dipilih?';
    document.getElementById('confirmModal').classList.remove('hidden');
}
function closeConfirmModal() { document.getElementById('confirmModal').classList.add('hidden'); }
function executeBulkAction() {
    const action = document.getElementById('bulkAction').value;
    const checked = document.querySelectorAll('.row-checkbox:checked');
    const box = document.getElementById('bulkInputs');
    box.innerHTML = '';
    checked.forEach(cb => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
        box.appendChild(inp);
    });
    document.getElementById('bulkActionInput').value = action;
    document.getElementById('bulkForm').submit();
}
</script>
<?= $this->endSection() ?>