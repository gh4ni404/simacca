<?= $this->extend(get_device_layout()) ?>

<?= $this->section('styles') ?>
<style>
    .kelas-card {
        transition: transform 0.2s ease-in-out;
    }
    .kelas-card:hover {
        transform: translateY(-4px);
    }
    .badge-wali {
        background-color: #FEF3C7;
        color: #92400E;
    }
    .badge-tanpa-wali {
        background-color: #FEE2E2;
        color: #991B1B;
    }
    .progress-bar {
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle ?></h2>
            <p class="text-gray-600"><?= $pageDescription ?></p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="<?= base_url('admin/kelas/statistics') ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-chart-bar mr-2"></i> Statistik
            </a>
            <a href="<?= base_url('admin/kelas/export') ?>" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-file-export mr-2"></i> Export
            </a>
            <a href="<?= base_url('admin/kelas/tambah') ?>" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Kelas
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-school text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Kelas</p>
                    <p class="text-2xl font-bold text-gray-800"><?= count($kelas) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-user-tie text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Kelas dengan Wali</p>
                    <p class="text-2xl font-bold text-gray-800">
                        <?= count($kelas) - count($kelasTanpaWali) ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Kelas Tanpa Wali</p>
                    <p class="text-2xl font-bold text-gray-800">
                        <?= count($kelasTanpaWali) ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Guru Tersedia</p>
                    <p class="text-2xl font-bold text-gray-800">
                        <?= count($guruTersedia) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Options -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div class="w-full md:w-1/3 mb-4 md:mb-0">
            <input type="text" id="searchInput" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                   placeholder="Cari kelas...">
        </div>
        <div class="flex space-x-2">
            <select id="filterTingkat" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Tingkat</option>
                <?php foreach ($tingkatList as $value => $label): ?>
                    <option value="<?= $value ?>"><?= $label ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterJurusan" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Jurusan</option>
                <?php foreach ($jurusanList as $value => $label): ?>
                    <option value="<?= $value ?>"><?= $label ?></option>
                <?php endforeach; ?>
            </select>
            <button id="resetFilter" class="border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50">
                Reset
            </button>
        </div>
    </div>

    <!-- Classes Grid View -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="kelasContainer">
        <?php if (empty($kelas)): ?>
            <div class="col-span-full">
                <div class="text-center py-12">
                    <i class="fas fa-school text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg mb-2">Belum ada data kelas</p>
                    <p class="text-gray-400 mb-6">Mulai dengan menambahkan kelas pertama</p>
                    <a href="<?= base_url('admin/kelas/tambah') ?>" 
                       class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i> Tambah Kelas Pertama
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($kelas as $k): ?>
                <div class="kelas-card bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow" 
                     data-tingkat="<?= $k['tingkat'] ?>" data-jurusan="<?= $k['jurusan'] ?>">
                    
                    <!-- Class Header -->
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold"><?= esc($k['nama_kelas']) ?></h3>
                                <p class="text-indigo-100 text-sm mt-1">
                                    <?= $k['tingkat'] ?> • <?= esc($k['jurusan']) ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 text-xs font-semibold bg-white/20 rounded-full">
                                    <?= $k['jumlah_siswa'] ?? 0 ?> Siswa
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Class Content -->
                    <div class="p-6">
                        <!-- Wali Kelas -->
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Wali Kelas</p>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-user-tie text-indigo-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">
                                        <?= $k['nama_wali_kelas'] ?? 'Belum ditugaskan' ?>
                                    </p>
                                    <?php if ($k['nama_wali_kelas']): ?>
                                        <p class="text-xs text-gray-500">Guru <?= $k['mata_pelajaran'] ?? '' ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Student Capacity -->
                        <div class="mb-6">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Kapasitas Kelas</span>
                                <span class="font-medium text-gray-800">
                                    <?= $k['jumlah_siswa'] ?? 0 ?> / 36
                                </span>
                            </div>
                            <div class="progress-bar bg-gray-200">
                                <?php 
                                $percentage = min(100, round((($k['jumlah_siswa'] ?? 0) / 36) * 100));
                                $color = $percentage < 70 ? 'bg-green-500' : ($percentage < 90 ? 'bg-yellow-500' : 'bg-red-500');
                                ?>
                                <div class="h-full <?= $color ?>" style="width: <?= $percentage ?>%"></div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex justify-between pt-4 border-t border-gray-100">
                            <a href="<?= base_url('admin/kelas/detail/' . $k['id']) ?>" 
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                            <a href="<?= base_url('admin/kelas/edit/' . $k['id']) ?>" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            <?php if ($k['jumlah_siswa'] ?? 0 > 0): ?>
                                <span class="text-gray-400 text-sm flex items-center cursor-not-allowed" title="Tidak dapat dihapus karena memiliki siswa">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </span>
                            <?php else: ?>
                                <button onclick="confirmDelete(<?= $k['id'] ?>, '<?= esc($k['nama_kelas']) ?>')" 
                                        class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
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
                    Apakah Anda yakin ingin menghapus kelas <span id="kelasName" class="font-semibold"></span>?
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

<!-- Quick Assign Wali Modal -->
<div id="assignWaliModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <i class="fas fa-user-tie text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Tugaskan Wali Kelas</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">
                    Pilih guru untuk menjadi wali kelas <span id="kelasAssignName" class="font-semibold"></span>
                </p>
                <form id="assignWaliForm" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="kelas_id" id="kelasIdInput">
                    <select name="guru_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                        <option value="">Pilih Guru</option>
                        <?php foreach ($guruTersedia as $guru): ?>
                            <option value="<?= $guru['id'] ?>">
                                <?= esc($guru['nama_lengkap']) ?> - <?= $guru['nama_mapel'] ?? '-' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="submitAssignWali()" 
                        class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    Tugaskan
                </button>
                <button onclick="closeAssignModal()" 
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
        const cards = document.querySelectorAll('.kelas-card');
        
        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });

    // Filter functionality
    document.getElementById('filterTingkat').addEventListener('change', function() {
        filterCards();
    });

    document.getElementById('filterJurusan').addEventListener('change', function() {
        filterCards();
    });

    function filterCards() {
        const tingkatValue = document.getElementById('filterTingkat').value;
        const jurusanValue = document.getElementById('filterJurusan').value;
        const cards = document.querySelectorAll('.kelas-card');
        
        cards.forEach(card => {
            const tingkat = card.getAttribute('data-tingkat');
            const jurusan = card.getAttribute('data-jurusan');
            
            const tingkatMatch = !tingkatValue || tingkat === tingkatValue;
            const jurusanMatch = !jurusanValue || jurusan === jurusanValue;
            
            card.style.display = (tingkatMatch && jurusanMatch) ? '' : 'none';
        });
    }

    // Reset filter
    document.getElementById('resetFilter').addEventListener('click', function() {
        document.getElementById('filterTingkat').value = '';
        document.getElementById('filterJurusan').value = '';
        document.getElementById('searchInput').value = '';
        
        const cards = document.querySelectorAll('.kelas-card');
        cards.forEach(card => card.style.display = '');
    });

    // Delete confirmation
    function confirmDelete(id, name) {
        document.getElementById('kelasName').textContent = name;
        document.getElementById('deleteForm').action = `<?= base_url('admin/kelas/hapus/') ?>${id}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Assign wali kelas
    function openAssignWaliModal(kelasId, kelasName) {
        document.getElementById('kelasAssignName').textContent = kelasName;
        document.getElementById('kelasIdInput').value = kelasId;
        document.getElementById('assignWaliModal').classList.remove('hidden');
    }

    function closeAssignModal() {
        document.getElementById('assignWaliModal').classList.add('hidden');
    }

    function submitAssignWali() {
        const form = document.getElementById('assignWaliForm');
        const formData = new FormData(form);
        
        fetch('<?= base_url('admin/kelas/assign-wali-kelas/') ?>' + formData.get('kelas_id'), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload(); // Reload to see changes
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
        
        closeAssignModal();
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const deleteModal = document.getElementById('deleteModal');
        const assignModal = document.getElementById('assignWaliModal');
        
        if (event.target === deleteModal) {
            closeModal();
        }
        if (event.target === assignModal) {
            closeAssignModal();
        }
    }
</script>
<?= $this->endSection() ?>