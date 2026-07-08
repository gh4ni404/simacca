<?= $this->extend(get_device_layout()) ?>

<?= $this->section('styles') ?>
<style>
    .table-responsive {
        overflow-x: auto;
    }
    .badge-active {
        background-color: #D1FAE5;
        color: #065F46;
    }
    .badge-inactive {
        background-color: #FEE2E2;
        color: #991B1B;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<script>
<?php 
$_importErrors = session()->getFlashdata('errors');
if (!empty($_importErrors)):
    foreach ((array)$_importErrors as $err): 
        echo "console.log(" . json_encode($err) . ");\n";
    endforeach; 
endif; 
?>
</script>
<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle ?></h2>
            <p class="text-gray-600"><?= $pageDescription ?></p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="<?= base_url('admin/siswa/export') ?>" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-file-export mr-2"></i> Export Excel
            </a>
            <a href="<?= base_url('admin/siswa/import') ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-file-import mr-2"></i> Import Excel
            </a>
            <a href="<?= base_url('admin/siswa/tambah') ?>" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Siswa
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
                    <p class="text-sm text-blue-600">Total Siswa</p>
                    <p class="text-2xl font-bold text-blue-800"><?= $totalSiswa ?></p>
                </div>
            </div>
        </div>
        
        <?php if (!empty($kelasSummary)): ?>
            <?php 
            // Find kelas with most students
            usort($kelasSummary, function($a, $b) {
                return $b['jumlah_siswa'] - $a['jumlah_siswa'];
            });
            $topKelas = $kelasSummary[0] ?? null;
            ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-school text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-green-600">Kelas Terbanyak</p>
                        <p class="text-xl font-bold text-green-800"><?= $topKelas['nama_kelas'] ?? '-' ?></p>
                        <p class="text-sm text-green-600"><?= $topKelas['jumlah_siswa'] ?? 0 ?> Siswa</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-male text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-purple-600">Perbandingan Gender</p>
                    <p class="text-sm text-purple-800">
                        <?php 
                        $maleCount = 0;
                        $femaleCount = 0;
                        foreach ($siswa as $s) {
                            if ($s['jenis_kelamin'] == 'L') $maleCount++;
                            else $femaleCount++;
                        }
                        ?>
                        L: <?= $maleCount ?> | P: <?= $femaleCount ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div class="w-full md:w-1/3 mb-4 md:mb-0">
            <form action="<?= base_url('admin/siswa') ?>" method="GET" class="relative">
                <input type="hidden" name="status" value="<?= esc($status ?? 'active') ?>">
                <input type="text" name="search" 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                       placeholder="Cari siswa (nama/NIS)..."
                       value="<?= esc($keyword ?? '') ?>">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
                <?php if ($keyword): ?>
                    <a href="<?= base_url('admin/siswa') ?>?status=<?= esc($status ?? 'active') ?>" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>
        <div class="flex space-x-2">
            <select id="filterKelas" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="window.location='<?= base_url('admin/siswa') ?>?kelas_id='+this.value+'&status=<?= $status ?? 'active' ?><?= $keyword ? '&search='.urlencode($keyword) : '' ?>'">
                <option value="">Semua Kelas</option>
                <?php foreach ($allKelas as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= ($kelasId ?? '') == $k['id'] ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterStatus" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="window.location='<?= base_url('admin/siswa') ?>?status='+this.value<?= $kelasId ? "+'&kelas_id=$kelasId'" : '' ?><?= $keyword ? "+'&search=".urlencode($keyword)."'" : '' ?>">
                <option value="active" <?= ($status ?? 'active') === 'active' ? 'selected' : '' ?>>Aktif</option>
                <option value="inactive" <?= ($status ?? '') === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
                <option value="all" <?= ($status ?? '') === 'all' ? 'selected' : '' ?>>Semua Status</option>
            </select>
            <a href="<?= base_url('admin/siswa') ?>" class="border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 flex items-center">
                Reset
            </a>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div id="bulkActions" class="mb-4 hidden">
        <div class="flex items-center space-x-4 p-3 bg-gray-100 rounded-lg">
            <span id="selectedCount" class="font-medium">0 siswa terpilih</span>
            <select id="bulkActionSelect" class="border border-gray-300 rounded-lg px-3 py-1">
                <option value="">Pilih Aksi</option>
                <option value="activate">Aktifkan</option>
                <option value="deactivate">Nonaktifkan</option>
                <option value="delete">Hapus</option>
            </select>
            <button onclick="performBulkAction()" class="px-4 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Terapkan
            </button>
            <button onclick="cancelBulkSelection()" class="px-4 py-1 border border-gray-300 rounded-lg hover:bg-gray-200">
                Batal
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="min-w-full divide-y divide-gray-200" id="siswaTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                        <input type="checkbox" id="selectAll" class="rounded text-indigo-600">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Ajaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($siswa)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-user-graduate text-4xl text-gray-300 mb-4"></i>
                            <p>Belum ada data siswa</p>
                            <a href="<?= base_url('admin/siswa/tambah') ?>" class="text-indigo-600 hover:text-indigo-800 mt-2 inline-block">
                                Tambah siswa pertama
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($siswa as $s): ?>
                        <tr class="hover:bg-gray-50" data-kelas="<?= $s['kelas_id'] ?>" data-status="<?= $s['is_active'] ? 'active' : 'inactive' ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="siswa-checkbox rounded text-indigo-600" value="<?= $s['id'] ?>">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= esc($s['nis']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <?php if (!empty($s['profile_photo'])): ?>
                                            <img src="<?= base_url('profile-photo/' . esc($s['profile_photo'])); ?>" 
                                                 alt="<?= esc($s['nama_lengkap']); ?>"
                                                 class="h-10 w-10 rounded-full object-cover border-2 border-indigo-200">
                                        <?php else: ?>
                                            <div class="h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                                <span class="text-indigo-600 font-semibold text-sm">
                                                    <?= strtoupper(substr($s['nama_lengkap'], 0, 2)); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= esc($s['nama_lengkap']) ?></div>
                                        <div class="text-sm text-gray-500"><?= esc($s['username']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= esc($s['nama_kelas'] ?? '-') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    <?= $s['jenis_kelamin'] == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' ?>">
                                    <?= $s['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= esc($s['tahun_ajaran']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($s['is_active']): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full badge-active">
                                        Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full badge-inactive">
                                        Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <a href="<?= base_url('admin/siswa/edit/' . $s['id']) ?>" 
                                       class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('admin/siswa/detail/' . $s['id']) ?>" 
                                       class="text-green-600 hover:text-green-900" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($s['is_active']): ?>
                                        <a href="<?= base_url('admin/siswa/nonaktifkan/' . $s['id']) ?>" 
                                           class="text-yellow-600 hover:text-yellow-900" title="Nonaktifkan"
                                           onclick="return confirm('Nonaktifkan siswa ini?')">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('admin/siswa/aktifkan/' . $s['id']) ?>" 
                                           class="text-green-600 hover:text-green-900" title="Aktifkan"
                                           onclick="return confirm('Aktifkan siswa ini?')">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                    <button onclick="confirmDelete(<?= $s['id'] ?>, '<?= esc($s['nama_lengkap']) ?>')" 
                                            class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalSiswa > $perPage): ?>
        <?php
        $totalPages = ceil($totalSiswa / $perPage);
        $startPage = max(1, (int)$currentPage - 2);
        $endPage = min($totalPages, (int)$currentPage + 2);

        $baseParams = ['status' => $status];
        if ($keyword) $baseParams['search'] = $keyword;
        if ($kelasId) $baseParams['kelas_id'] = $kelasId;

        function pageUrl($pageNum, $baseParams) {
            $params = array_merge($baseParams, ['page' => $pageNum]);
            return '?' . http_build_query($params);
        }
        ?>
        <div class="mt-6 flex justify-between items-center">
            <div class="text-sm text-gray-700">
                Menampilkan <span class="font-medium"><?= (($currentPage - 1) * $perPage) + 1 ?></span> 
                - <span class="font-medium"><?= min($currentPage * $perPage, $totalSiswa) ?></span> 
                dari <span class="font-medium"><?= $totalSiswa ?></span> hasil
            </div>
            <div class="flex space-x-2">
                <?php if ($currentPage > 1): ?>
                    <a href="<?= pageUrl($currentPage - 1, $baseParams) ?>" 
                       class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">
                        Sebelumnya
                    </a>
                <?php endif; ?>
                
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="<?= pageUrl($i, $baseParams) ?>" 
                       class="px-3 py-1 border <?= $i == $currentPage ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-300 hover:bg-gray-50' ?> rounded">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= pageUrl($currentPage + 1, $baseParams) ?>" 
                       class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">
                        Selanjutnya
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Konfirmasi Hapus</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus data siswa <span id="siswaName" class="font-semibold"></span>?
                </p>
                <p class="text-xs text-red-500 mt-2">Data yang dihapus tidak dapat dikembalikan!</p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="GET">
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Ya, Hapus
                    </button>
                </form>
                <button onclick="closeModal()" 
                        class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Form -->
<form id="bulkActionForm" action="<?= base_url('admin/siswa/bulk-action') ?>" method="POST" class="hidden">
    <?= csrf_field() ?>
    <input type="hidden" name="action" id="bulkActionInput">
    <div id="bulkIdsContainer"></div>
</form>

<script>
    // Filters handled by server-side reload via onchange

    // Bulk selection
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.siswa-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActions();
    });

    // Update bulk actions when checkboxes change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('siswa-checkbox')) {
            updateBulkActions();
        }
    });

    function updateBulkActions() {
        const selected = document.querySelectorAll('.siswa-checkbox:checked');
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');
        
        if (selected.length > 0) {
            bulkActions.classList.remove('hidden');
            selectedCount.textContent = `${selected.length} siswa terpilih`;
        } else {
            bulkActions.classList.add('hidden');
        }
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.siswa-checkbox');
        const selectAll = document.getElementById('selectAll');
        selectAll.checked = selected.length === allCheckboxes.length;
    }

    function performBulkAction() {
        const action = document.getElementById('bulkActionSelect').value;
        const selected = document.querySelectorAll('.siswa-checkbox:checked');
        
        if (!action) {
            alert('Pilih aksi terlebih dahulu');
            return;
        }
        
        if (selected.length === 0) {
            alert('Pilih minimal satu siswa');
            return;
        }
        
        // Confirm destructive actions
        if (action === 'delete' && !confirm(`Apakah Anda yakin ingin menghapus ${selected.length} siswa?`)) {
            return;
        }
        
        // Collect IDs
        const ids = Array.from(selected).map(cb => cb.value);
        
        // Set form values and submit
        document.getElementById('bulkActionInput').value = action;
        const container = document.getElementById('bulkIdsContainer');
        container.innerHTML = '';
        
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            container.appendChild(input);
        });
        
        document.getElementById('bulkActionForm').submit();
    }

    function cancelBulkSelection() {
        const checkboxes = document.querySelectorAll('.siswa-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        document.getElementById('bulkActions').classList.add('hidden');
    }

    // Delete confirmation
    function confirmDelete(id, name) {
        document.getElementById('siswaName').textContent = name;
        document.getElementById('deleteForm').action = `<?= base_url('admin/siswa/hapus/') ?>${id}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>
<?= $this->endSection() ?>