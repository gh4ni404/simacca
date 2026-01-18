<?= $this->extend('templates/main_layout') ?>
<?= $this->section('styles') ?>
<style>
    .table-responsive {
        overflow-x: auto;
    }

    .badge-wali {
        background-color: #FEF3C7;
        color: #92400E;
    }

    .badge-guru {
        background-color: #DBEAFE;
        color: #1E40AF;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle; ?></h2>
            <p class="text-gray-600"><?= $pageDescription; ?></p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="<?= base_url('admin/guru/export'); ?>"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-file-export mr-2"></i> Export Excel
            </a>
            <a href="<?= base_url('admin/guru/import') ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-file-import mr-2"></i> Import Excel
            </a>
            <a href="<?= base_url('admin/guru/tambah'); ?>"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Guru
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-blue-600">Total Guru</p>
                    <p class="text-2xl font-bold text-blue-600"><?= $totalGuru; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-green-600">Wali Kelas</p>
                    <p class="text-2xl font-bold text-green-600"><?= count($waliKelas); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-purple-600">Guru Mapel</p>
                    <p class="text-2xl font-bold text-purple-600"><?= count($guruNonWali); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div class="w-full md:w-1/3 mb-4 md:mb-0">
            <div class="relative">
                <input type="text" id="searchInput"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Cari Guru...">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
            </div>
        </div>
        <div class="flex space-x-2">
            <select name="" id="filterRole"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Role</option>
                <option value="guru_mapel">Guru Mapel</option>
                <option value="wali_kelas">Wali Kelas</option>
                <option value="wakakur">Wakakur</option>
            </select>
            <select name="" id="filterStatus"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>
            <button id="resetFilter"
                class="border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50">Reset</button>
        </div>
    </div>
    <!-- Tabel -->
    <div class="table-responsive">
        <table class="min-w-full divide-y divide-gray-200" id="guruTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Guru</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($guru)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                            <p>Belum ada data guru</p>
                            <a href="<?= base_url('admin/guru/tambah'); ?>" class="text-indigo-600 hover:text-indigo-800 mt-2 inline-block">Tambah guru pertama</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($guru as $g): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"> <?= esc($g['nip']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <?php if (!empty($g['profile_photo'])): ?>
                                            <img src="<?= base_url('profile-photo/' . esc($g['profile_photo'])); ?>" 
                                                 alt="<?= esc($g['nama_lengkap']); ?>"
                                                 class="h-10 w-10 rounded-full object-cover border-2 border-indigo-200">
                                        <?php else: ?>
                                            <div class="h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                                <span class="text-indigo-600 font-semibold text-sm">
                                                    <?= strtoupper(substr($g['nama_lengkap'], 0, 2)); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= esc($g['nama_lengkap']); ?></div>
                                        <div class="text-sm text-gray-500"><?= esc($g['jenis_kelamin']) == 'L' ? 'Laki-laki' : 'Wanita'; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= esc($g['nama_mapel']) ?? '-'; ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" data-role="<?= esc($g['role']); ?>">
                                <?php if ($g['role'] === 'wakakur'): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                        <i class="fas fa-user-graduate mr-1"></i>Wakakur
                                    </span>
                                <?php elseif ($g['is_wali_kelas']): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full badge-wali"><i class="fas fa-user-tie mr-1"></i>Wali Kelas</span>
                                    <?php if ($g['kelas_id']): ?>
                                        <div class="text-xs text-gray-500 mt-1 ml-2">Kelas: <?= $g['nama_kelas']; ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full badde-guru">
                                        <i class="fas fa-chalkboard-teacher mr-1"></i> Guru Mapel
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($g['is_active']): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800"><i class="fas fa-user-tie mr-1"></i>Aktif</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-chalkboard-teacher mr-1"></i> Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <a href="<?= base_url('admin/guru/edit/' . $g['id']); ?>"
                                        class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('admin/guru/detail/' . $g['id']); ?>"
                                        class="text-green-600 hover:text-green-900" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($g['is_active']): ?>
                                        <a href="<?= base_url('admin/guru/nonaktifkan/' . $g['id']); ?>"
                                            class="text-red-600 hover:text-red-900" title="Nonaktifkan" onclick="return confirm('Nonaktifkan Guru Ini?')">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('admin/guru/aktifkan/' . $g['id']); ?>"
                                            class="text-yellow-600 hover:text-yellow-900" title="Aktifkan" onclick="return confirm('Aktifkan Guru Ini?')">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination (if needed) -->
    <?php if (count($guru) > 10): ?>
        <div class="mt-6 flex justify-between items-center">
            <div class="text-sm text-gray-700">
                Menampilkan <span class="font-medium">1-10</span> dari <span class="font-medium"><?= count($guru); ?></span> hasil
            </div>
            <div class="flex space-x-2">
                <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Sebelumnya</button>
                <button class="px-3 py-1 border border-gray-300 rounded bg-indigo-50 text-white">1</button>
                <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">2</button>
                <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Selanjutnya</button>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Konfirmasi Hapus</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus data guru <span id="guruName" class="font-semibold"></span>?
                </p>
                <p class="text-xs text-red-500 mt-2">Data yang dihapus tidak dapat dikembalikan!</p>
            </div>
            <div class="items-center px-4 py-5">
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
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#guruTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });

    // Filter funtionality
    document.getElementById('filterRole').addEventListener('change', function() {
        filterTable();
    });
    document.getElementById('filterStatus').addEventListener('change', function() {
        filterTable();
    });

    function filterTable() {
        const roleValue = document.getElementById('filterRole').value;
        const statusValue = document.getElementById('filterStatus').value;
        const rows = document.querySelectorAll('#guruTable tbody tr');

        rows.forEach(row => {
            if (roleValue === '' && statusValue === '') {
                row.style.display = '';
                return;
            }

            const roleCell = row.cells[3];
            const statusCell = row.cells[4];

            const roleData = roleCell.getAttribute('data-role');
            const isActive = statusCell.textContent.includes('Aktif');

            const roleMatch = roleValue === '' ||
                (roleValue === 'wakakur' && roleData === 'wakakur') ||
                (roleValue === 'wali_kelas' && roleData === 'wali_kelas') ||
                (roleValue === 'guru_mapel' && roleData === 'guru_mapel');

            const statusMatch = statusValue === '' ||
                (statusValue === 'active' && isActive) ||
                (statusValue === 'inactive' && !isActive);

                row.style.display = (roleMatch && statusMatch) ? '' : 'none';
        });
    }

    // reset filter
    document.getElementById('resetFilter').addEventListener('click', function() {
       document.getElementById('searchInput').value = '';
       document.getElementById('filterRole').value = '';
       document.getElementById('filterStatus').value = '';

       const rows = document.querySelectorAll('#guruTable tbody tr');
       rows.forEach(row => row.style.display = '');
    });

    // Delete Confirmation
    function confirmDelete(id, name) {
        document.getElementById('guruName').textContent = name;
        document.getElementById('deleteForm').action = `<?= base_url('admin/guru/hapus/'); ?>${id}`;
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