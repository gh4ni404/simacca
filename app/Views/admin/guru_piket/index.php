<?= $this->extend(get_device_layout()) ?>
<?= $this->section('styles') ?>
<style>
    .table-responsive { overflow-x: auto; }
    .hari-card { border-left: 4px solid; }
    .hari-senin { border-color: #3B82F6; }
    .hari-selasa { border-color: #10B981; }
    .hari-rabu { border-color: #F59E0B; }
    .hari-kamis { border-color: #8B5CF6; }
    .hari-jumat { border-color: #EF4444; }
    .hari-sabtu { border-color: #6366F1; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle; ?></h2>
            <p class="text-gray-600"><?= $pageDescription; ?></p>
            <p class="text-sm text-gray-500 mt-1">
                <i class="fas fa-calendar-alt mr-1"></i> Tahun Ajaran: <span class="font-semibold"><?= esc($tahunAjaran); ?></span>
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <!-- Semester Selector -->
            <div class="flex bg-gray-100 rounded-lg p-1">
                <a href="?semester=ganjil" id="btnGanjil" class="px-4 py-1.5 rounded-md text-sm font-medium transition <?= $semester === 'ganjil' ? 'bg-indigo-600 text-white shadow' : 'text-gray-600 hover:text-gray-800'; ?>">
                    <i class="fas fa-book-open mr-1"></i> Ganjil
                </a>
                <a href="?semester=genap" id="btnGenap" class="px-4 py-1.5 rounded-md text-sm font-medium transition <?= $semester === 'genap' ? 'bg-indigo-600 text-white shadow' : 'text-gray-600 hover:text-gray-800'; ?>">
                    <i class="fas fa-book mr-1"></i> Genap
                </a>
            </div>
            <a href="<?= base_url('admin/master-jobdesk'); ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium text-sm flex items-center transition">
                <i class="fas fa-tasks mr-2 text-indigo-600"></i> Master Jobdesk
            </a>
            <button onclick="openAddModalForHari(document.getElementById('addHari').value)"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Jadwal Piket
            </button>
        </div>
    </div>


    <!-- Stats -->
    <?php
    $uniqueGuru = [];
    foreach ($grouped as $hariGuru) {
        foreach ($hariGuru as $g) {
            $uniqueGuru[$g['guru_id']] = true;
        }
    }
    $activeDays = count($stats['hariStats'] ?? []);
    ?>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4 flex items-center space-x-4">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center shadow-sm">
                <i class="fas fa-clipboard-list text-white text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-blue-600 uppercase tracking-wide">Total Jadwal</p>
                <p class="text-2xl font-bold text-blue-700"><?= $stats['total'] ?? 0; ?></p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-4 flex items-center space-x-4">
            <div class="flex-shrink-0 w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center shadow-sm">
                <i class="fas fa-check-circle text-white text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-green-600 uppercase tracking-wide">Aktif</p>
                <p class="text-2xl font-bold text-green-700"><?= $stats['active'] ?? 0; ?></p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-xl p-4 flex items-center space-x-4">
            <div class="flex-shrink-0 w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center shadow-sm">
                <i class="fas fa-users text-white text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-amber-600 uppercase tracking-wide">Guru Piket</p>
                <p class="text-2xl font-bold text-amber-700"><?= count($uniqueGuru); ?></p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-4 flex items-center space-x-4">
            <div class="flex-shrink-0 w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center shadow-sm">
                <i class="fas fa-calendar-day text-white text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium text-purple-600 uppercase tracking-wide">Hari Aktif</p>
                <p class="text-2xl font-bold text-purple-700"><?= $activeDays; ?>/6</p>
            </div>
        </div>
    </div>

    <!-- Filter Hari -->
    <div class="flex flex-wrap gap-2 mb-6">
        <button onclick="filterHari('')" class="px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 filter-hari-btn" data-hari="">
            Semua Hari
        </button>
        <?php foreach ($hariList as $hari): ?>
            <button onclick="filterHari('<?= $hari; ?>')" 
                class="px-4 py-2 rounded-lg text-sm font-medium border border-gray-300 hover:bg-gray-50 filter-hari-btn" data-hari="<?= $hari; ?>">
                <?= ucfirst($hari); ?>
                <?php if (isset($stats['hariStats'][$hari])): ?>
                    <span class="ml-1 bg-gray-200 text-gray-700 px-1.5 rounded-full text-xs"><?= $stats['hariStats'][$hari]; ?></span>
                <?php endif; ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Schedule Cards by Day -->
    <div id="scheduleContainer" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <?php if (empty($grouped)): ?>
            <div class="text-center py-12 lg:col-span-2">
                <i class="fas fa-calendar-times text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Belum ada jadwal piket</p>
                <p class="text-gray-400 text-sm mt-1">Klik "Tambah Jadwal Piket" untuk mulai mengatur jadwal</p>
            </div>
        <?php else: ?>
            <?php foreach ($grouped as $hari => $guruList): ?>
                <div class="hari-section" data-hari="<?= $hari; ?>">
                    <div class="hari-card hari-<?= $hari; ?> bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-6 py-3 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800">
                                <i class="fas fa-calendar-day mr-2 text-gray-500"></i>
                                <?= ucfirst($hari); ?>
                                <span class="ml-2 bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full text-xs font-normal">
                                    <?= count($guruList); ?> guru
                                </span>
                            </h3>
                            <button onclick="openAddModalForHari('<?= $hari; ?>')"
                                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                <i class="fas fa-plus mr-1"></i> Tambah Guru
                            </button>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <?php foreach ($guruList as $piket): ?>
                                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3 border border-gray-100 <?= $piket['is_active'] ? '' : 'opacity-50'; ?>">
                                        <div class="flex items-center min-w-0 flex-1 mr-3">
                                            <div class="flex-shrink-0 h-9 w-9 rounded-full overflow-hidden mr-3">
                                                <?php if (!empty($piket['profile_photo']) && file_exists(WRITEPATH . 'uploads/profile/' . $piket['profile_photo'])): ?>
                                                    <img src="<?= base_url('profile-photo/' . esc($piket['profile_photo'])); ?>" 
                                                         alt="<?= esc($piket['nama_lengkap']); ?>"
                                                         class="h-9 w-9 object-cover rounded-full"
                                                         onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                                    <div class="hidden h-9 w-9 <?= $piket['jenis_kelamin'] === 'L' ? 'bg-blue-100' : 'bg-pink-100'; ?> rounded-full flex items-center justify-center">
                                                        <span class="<?= $piket['jenis_kelamin'] === 'L' ? 'text-blue-600' : 'text-pink-600'; ?> font-semibold text-xs">
                                                            <?= strtoupper(substr($piket['nama_lengkap'], 0, 2)); ?>
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="h-9 w-9 <?= $piket['jenis_kelamin'] === 'L' ? 'bg-blue-100' : 'bg-pink-100'; ?> rounded-full flex items-center justify-center">
                                                        <span class="<?= $piket['jenis_kelamin'] === 'L' ? 'text-blue-600' : 'text-pink-600'; ?> font-semibold text-xs">
                                                            <?= strtoupper(substr($piket['nama_lengkap'], 0, 2)); ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900 truncate" title="<?= esc($piket['nama_lengkap']); ?>"><?= esc($piket['nama_lengkap']); ?></p>
                                                <p class="text-xs text-gray-500 truncate" title="<?= esc($piket['nip']); ?>"><?= esc($piket['nip']); ?></p>
                                                <?php if (!empty($piket['keterangan'])): ?>
                                                    <p class="text-xs text-gray-400 mt-0.5 truncate" title="<?= esc($piket['keterangan']); ?>"><i class="fas fa-info-circle mr-1"></i><?= esc($piket['keterangan']); ?></p>
                                                <?php endif; ?>
                                                <button type="button" onclick="viewDetailTugas(<?= htmlspecialchars(json_encode($piket), ENT_QUOTES); ?>)" 
                                                    class="mt-1 text-[11px] font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-2 py-0.5 rounded inline-flex items-center transition-colors">
                                                    <i class="fas fa-tasks mr-1 text-indigo-500"></i> Rincian Tugas
                                                </button>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <button onclick="openEditModal(<?= htmlspecialchars(json_encode($piket), ENT_QUOTES); ?>)"
                                                class="text-indigo-600 hover:text-indigo-800 p-1" title="Edit">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
                                            <button onclick="confirmToggleStatus(<?= $piket['id']; ?>, '<?= esc($piket['nama_lengkap']); ?>', <?= $piket['is_active'] ? 'true' : 'false'; ?>)"
                                                class="<?= $piket['is_active'] ? 'text-green-600 hover:text-green-800' : 'text-yellow-600 hover:text-yellow-800'; ?> p-1"
                                                title="<?= $piket['is_active'] ? 'Nonaktifkan' : 'Aktifkan'; ?>">
                                                <i class="fas fa-<?= $piket['is_active'] ? 'toggle-on' : 'toggle-off'; ?> text-sm"></i>
                                            </button>
                                            <button onclick="confirmDelete(<?= $piket['id']; ?>, '<?= esc($piket['nama_lengkap']); ?>', '<?= ucfirst($hari); ?>')"
                                                class="text-red-600 hover:text-red-800 p-1" title="Hapus">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- ==================== MODAL TAMBAH (Mapping Multi-Guru) ==================== -->
<div id="addModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('addModal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-xl max-h-[90vh] flex flex-col transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Jadwal Piket</h3>
                <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <!-- Body -->
            <div class="px-6 py-5 overflow-y-auto flex-1">
                <form id="addForm">
                    <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                    <input type="hidden" name="semester" id="addSemester" value="<?= $semester; ?>">
                    
                    <!-- Hari -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Hari <span class="text-red-500">*</span></label>
                        <select name="hari" id="addHari" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            <?php foreach ($hariList as $h): ?>
                                <option value="<?= $h; ?>"><?= ucfirst($h); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Guru List (Checkbox Mapping) -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-semibold text-gray-700">Pilih Guru <span class="text-red-500">*</span></label>
                            <label class="flex items-center text-sm text-indigo-600 cursor-pointer hover:text-indigo-800">
                                <input type="checkbox" id="addAllGuru" class="mr-1 rounded"> Pilih Semua
                            </label>
                        </div>
                        <!-- Search -->
                        <div class="relative mb-2">
                            <input type="text" id="addSearchGuru" placeholder="Cari nama atau NIP guru..."
                                class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <i class="fas fa-search text-sm"></i>
                            </div>
                            <button type="button" id="addClearSearch" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 hidden">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                        <!-- Count -->
                        <p id="addGuruCount" class="text-xs text-gray-500 mb-2 hidden">Menampilkan <span id="addVisibleCount">0</span> dari <span id="addTotalCount">0</span> guru</p>
                        <!-- List -->
                        <div id="addGuruList" class="border border-gray-200 rounded-lg divide-y divide-gray-100 max-h-60 overflow-y-auto bg-gray-50">
                            <div class="p-4 text-center text-gray-400 text-sm">Memuat data guru...</div>
                        </div>
                        <p id="addGuruEmpty" class="text-sm text-yellow-600 mt-1 hidden"><i class="fas fa-exclamation-triangle mr-1"></i> Semua guru sudah dijadwalkan pada hari ini</p>
                        <p id="addGuruSelected" class="text-sm text-indigo-600 mt-1 hidden"><i class="fas fa-check-circle mr-1"></i> <span id="addSelectedCount">0</span> guru dipilih</p>
                    </div>

                    <!-- Master Jobdesk Mapping -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Mapping Master Jobdesk <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <select name="jobdesk_id" id="addJobdesk" onchange="onJobdeskSelectChange('addJobdesk', 'addRincianTugas')" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">-- Tanpa Mapping Master Jobdesk --</option>
                            <?php foreach ($masterJobdeskList as $mj): ?>
                                <option value="<?= $mj['id']; ?>"><?= esc($mj['nama_jobdesk']); ?> (<?= esc($mj['kode_jobdesk']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <textarea name="keterangan" id="addKeterangan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>


                    <!-- Rincian Tugas -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-semibold text-gray-700">Rincian Tugas & Tanggung Jawab <span class="text-gray-400 font-normal">(opsional)</span></label>
                            <button type="button" onclick="fillDefaultRincianTugas('addRincianTugas')" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1 bg-indigo-50 px-2 py-1 rounded transition-colors">
                                <i class="fas fa-magic text-xs"></i> Gunakan Template Standar
                            </button>
                        </div>
                        <textarea name="rincian_tugas" id="addRincianTugas" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" rows="3" placeholder="Tuliskan rincian tugas, kewajiban, peran, dan tanggung jawab..."></textarea>
                    </div>

                    <!-- Error message -->
                    <div id="addError" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm"></div>
                </form>
            </div>
            <!-- Footer -->
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 flex-shrink-0">
                <button type="button" onclick="closeModal('addModal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold transition-colors">Batal</button>
                <button type="button" onclick="submitBulkAssign()" id="addSubmitBtn" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODAL EDIT ==================== -->
<div id="editModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('editModal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-lg font-semibold text-gray-900">Edit Jadwal Piket</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <!-- Body -->
            <div class="px-6 py-5 overflow-y-auto flex-1">
                <form id="editForm" onsubmit="return submitEditForm(event)">
                    <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
                    <input type="hidden" name="id" id="editId">
                    <input type="hidden" name="semester" id="editSemester" value="<?= $semester; ?>">
                    
                    <!-- Hari -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Hari <span class="text-red-500">*</span></label>
                        <select name="hari" id="editHari" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            <?php foreach ($hariList as $h): ?>
                                <option value="<?= $h; ?>"><?= ucfirst($h); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Guru -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Guru <span class="text-red-500">*</span></label>
                        <select name="guru_id" id="editGuru" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            <option value="">-- Pilih Guru --</option>
                        </select>
                    </div>

                    <!-- Master Jobdesk Mapping -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Mapping Master Jobdesk <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <select name="jobdesk_id" id="editJobdesk" onchange="onJobdeskSelectChange('editJobdesk', 'editRincianTugas')" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="">-- Tanpa Mapping Master Jobdesk --</option>
                            <?php foreach ($masterJobdeskList as $mj): ?>
                                <option value="<?= $mj['id']; ?>"><?= esc($mj['nama_jobdesk']); ?> (<?= esc($mj['kode_jobdesk']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <textarea name="keterangan" id="editKeterangan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>


                    <!-- Rincian Tugas -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-semibold text-gray-700">Rincian Tugas & Tanggung Jawab <span class="text-gray-400 font-normal">(opsional)</span></label>
                            <button type="button" onclick="fillDefaultRincianTugas('editRincianTugas')" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1 bg-indigo-50 px-2 py-1 rounded transition-colors">
                                <i class="fas fa-magic text-xs"></i> Gunakan Template Standar
                            </button>
                        </div>
                        <textarea name="rincian_tugas" id="editRincianTugas" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" rows="3" placeholder="Tuliskan rincian tugas, kewajiban, peran, dan tanggung jawab..."></textarea>
                    </div>

                    <!-- Status -->
                    <div class="mb-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="is_active" value="1" id="editActive1" class="mr-2">
                                <span class="text-sm text-gray-700">Aktif</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="is_active" value="0" id="editActive0" class="mr-2">
                                <span class="text-sm text-gray-700">Nonaktif</span>
                            </label>
                        </div>
                    </div>

                    <!-- Error message -->
                    <div id="editError" class="mt-4 hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm"></div>
                </form>
            </div>
            <!-- Footer -->
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 flex-shrink-0">
                <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold transition-colors">Batal</button>
                <button type="submit" form="editForm" id="editSubmitBtn" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
                    <i class="fas fa-save mr-1"></i> Perbarui
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODAL DETAIL RINCIAN TUGAS ==================== -->
<div id="detailModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('detailModal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-clipboard-list text-indigo-600 mr-2"></i> Rincian Tugas Guru Piket
                </h3>
                <button onclick="closeModal('detailModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <!-- Body -->
            <div class="px-6 py-5 overflow-y-auto flex-1 space-y-4">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Guru</p>
                    <p id="detailGuruNama" class="text-base font-bold text-gray-900 mt-0.5"></p>
                    <div id="detailJobdeskBadge" class="mt-1"></div>
                </div>
                <div class="border-t border-gray-100 pt-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Rincian Panduan Tugas Piket</p>
                    <div id="detailRincianTugas" class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm text-gray-700 whitespace-pre-line leading-relaxed"></div>
                </div>
            </div>
            <!-- Footer -->
            <div class="flex justify-end px-6 py-4 border-t border-gray-200 flex-shrink-0 bg-gray-50">
                <button type="button" onclick="closeModal('detailModal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-semibold text-sm transition-colors">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== SCRIPTS ==================== -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // SweetAlert Confirmations
    function confirmDelete(id, name, hari) {
        Swal.fire({
            title: 'Hapus Jadwal Piket?',
            html: `<p style="text-align:left;margin-bottom:8px">Apakah Anda yakin ingin menghapus jadwal piket <strong>${name}</strong> pada hari <strong>${hari}</strong>?</p>
                   <div style="text-align:left;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px;font-size:13px;color:#991b1b">
                       <i class="fas fa-exclamation-triangle" style="margin-right:4px"></i>
                       <strong>PERHATIAN:</strong> Data yang dihapus tidak dapat dikembalikan.
                   </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url('admin/guru-piket/hapus/'); ?>' + id;
            }
        });
    }

    function confirmToggleStatus(id, name, isActive) {
        const action = isActive ? 'Nonaktifkan' : 'Aktifkan';
        const icon = isActive ? 'question' : 'question';
        Swal.fire({
            title: action + ' Jadwal Piket?',
            html: `<p style="text-align:left">Apakah Anda yakin ingin ${action.toLowerCase()} jadwal piket <strong>${name}</strong>?</p>`,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: isActive ? '#d97706' : '#059669',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, ' + action,
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url('admin/guru-piket/toggle-status/'); ?>' + id;
            }
        });
    }

    // Modal helpers
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Filter hari
    function filterHari(hari) {
        const sections = document.querySelectorAll('.hari-section');
        const buttons = document.querySelectorAll('.filter-hari-btn');
        buttons.forEach(btn => {
            if (btn.dataset.hari === hari) {
                btn.classList.add('bg-indigo-600', 'text-white');
                btn.classList.remove('border', 'border-gray-300');
            } else {
                btn.classList.remove('bg-indigo-600', 'text-white');
                btn.classList.add('border', 'border-gray-300');
            }
        });
        sections.forEach(section => {
            section.style.display = (hari === '' || section.dataset.hari === hari) ? '' : 'none';
        });
    }

    // CSRF token for AJAX - fetch fresh before each write request
    let csrfName = '<?= csrf_token(); ?>';
    let csrfHash = '<?= csrf_hash(); ?>';

    async function getFreshCsrfToken() {
        try {
            const res = await fetch('<?= base_url('csrf-token'); ?>', { credentials: 'same-origin' });
            const data = await res.json();
            csrfName = data.tokenName;
            csrfHash = data.tokenValue;
        } catch (e) {
            // fallback: keep current token
        }
    }

    // ==================== ADD MODAL (Bulk Mapping) ====================
    function openAddModalForHari(hari) {
        document.getElementById('addHari').value = hari;
        document.getElementById('addForm').reset();
        document.getElementById('addHari').value = hari;
        document.getElementById('addError').classList.add('hidden');
        document.getElementById('addGuruSelected').classList.add('hidden');
        document.getElementById('addAllGuru').checked = false;
        document.getElementById('addSearchGuru').value = '';
        document.getElementById('addClearSearch').classList.add('hidden');
        loadGuruCheckboxList(hari);
        openModal('addModal');
    }

    // Load guru list with checkboxes
    async function loadGuruCheckboxList(hari) {
        const container = document.getElementById('addGuruList');
        const emptyMsg = document.getElementById('addGuruEmpty');
        const selectedInfo = document.getElementById('addGuruSelected');
        const countInfo = document.getElementById('addGuruCount');
        
        container.innerHTML = '<div class="p-4 text-center text-gray-400 text-sm">Memuat data guru...</div>';
        emptyMsg.classList.add('hidden');
        selectedInfo.classList.add('hidden');
        countInfo.classList.add('hidden');

        // Fetch fresh CSRF token before request
        await getFreshCsrfToken();

        const params = new URLSearchParams();
        params.append('hari', hari);
        params.append('semester', '<?= $semester; ?>');
        params.append(csrfName, csrfHash);

        fetch('<?= base_url('admin/guru-piket/get-available-guru'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: params.toString()
        })
        .then(r => r.json())
        .then(data => {
            container.innerHTML = '';
            if (data.success && data.data && data.data.length > 0) {
                document.getElementById('addTotalCount').textContent = data.data.length;
                document.getElementById('addVisibleCount').textContent = data.data.length;
                countInfo.classList.remove('hidden');

                data.data.forEach(guru => {
                    const label = document.createElement('label');
                    label.className = 'flex items-center px-4 py-3 hover:bg-indigo-50 cursor-pointer transition-colors guru-item';
                    label.setAttribute('data-search', (guru.nama_lengkap + ' ' + guru.nip).toLowerCase());
                    const avatarHtml = guru.profile_photo
                        ? `<div class="flex-shrink-0 h-8 w-8 rounded-full overflow-hidden mr-3">
                             <img src="${'<?= base_url('profile-photo/') ?>' + guru.profile_photo}" alt="${guru.nama_lengkap}" class="h-8 w-8 object-cover rounded-full" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                             <div class="h-8 w-8 ${guru.jenis_kelamin === 'L' ? 'bg-blue-100' : 'bg-pink-100'} rounded-full flex items-center justify-center" style="display:none">
                               <span class="${guru.jenis_kelamin === 'L' ? 'text-blue-600' : 'text-pink-600'} font-semibold text-xs">${guru.nama_lengkap.substring(0, 2).toUpperCase()}</span>
                             </div>
                           </div>`
                        : `<div class="flex-shrink-0 h-8 w-8 ${guru.jenis_kelamin === 'L' ? 'bg-blue-100' : 'bg-pink-100'} rounded-full flex items-center justify-center mr-3"><span class="${guru.jenis_kelamin === 'L' ? 'text-blue-600' : 'text-pink-600'} font-semibold text-xs">${guru.nama_lengkap.substring(0, 2).toUpperCase()}</span></div>`;
                    label.innerHTML = `
                        <input type="checkbox" name="guru_ids[]" value="${guru.id}" 
                            class="guru-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-3">
                        ${avatarHtml}
                        <div>
                            <p class="text-sm font-medium text-gray-900 guru-nama">${guru.nama_lengkap}</p>
                            <p class="text-xs text-gray-500 guru-nip">${guru.nip}</p>
                        </div>
                    `;
                    container.appendChild(label);
                });

                // Handle checkbox changes
                container.querySelectorAll('.guru-checkbox').forEach(cb => {
                    cb.addEventListener('change', updateSelectedCount);
                });
            } else {
                container.innerHTML = '<div class="p-4 text-center text-gray-400 text-sm">Tidak ada guru tersedia</div>';
                emptyMsg.classList.remove('hidden');
                countInfo.classList.add('hidden');
            }
        })
        .catch(() => {
            container.innerHTML = '<div class="p-4 text-center text-red-400 text-sm">Gagal memuat data guru</div>';
        });
    }

    // Search guru
    document.getElementById('addSearchGuru').addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const items = document.querySelectorAll('#addGuruList .guru-item');
        let visibleCount = 0;
        const totalCount = items.length;

        document.getElementById('addClearSearch').classList.toggle('hidden', query === '');

        items.forEach(item => {
            const searchText = item.getAttribute('data-search');
            const match = searchText.includes(query);
            item.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        document.getElementById('addVisibleCount').textContent = visibleCount;

        // Update select all state
        const visibleCheckboxes = document.querySelectorAll('#addGuruList .guru-item:not([style*="display: none"]) .guru-checkbox');
        const allChecked = visibleCheckboxes.length > 0 && Array.from(visibleCheckboxes).every(cb => cb.checked);
        document.getElementById('addAllGuru').checked = allChecked;
    });

    // Clear search
    document.getElementById('addClearSearch').addEventListener('click', function() {
        document.getElementById('addSearchGuru').value = '';
        document.getElementById('addSearchGuru').dispatchEvent(new Event('input'));
    });

    // Update selected count
    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('.guru-checkbox:checked');
        const count = checkboxes.length;
        const selectedInfo = document.getElementById('addGuruSelected');
        const countSpan = document.getElementById('addSelectedCount');
        
        if (count > 0) {
            selectedInfo.classList.remove('hidden');
            countSpan.textContent = count;
        } else {
            selectedInfo.classList.add('hidden');
        }
    }

    // Select all / deselect all (only visible items when searching)
    document.getElementById('addAllGuru').addEventListener('change', function() {
        const query = document.getElementById('addSearchGuru').value.toLowerCase().trim();
        document.querySelectorAll('#addGuruList .guru-item').forEach(item => {
            const checkbox = item.querySelector('.guru-checkbox');
            if (query === '' || item.getAttribute('data-search').includes(query)) {
                checkbox.checked = this.checked;
            }
        });
        updateSelectedCount();
    });

    // Load guru when hari changes
    document.getElementById('addHari').addEventListener('change', function() {
        loadGuruCheckboxList(this.value);
        document.getElementById('addAllGuru').checked = false;
        document.getElementById('addSearchGuru').value = '';
        document.getElementById('addClearSearch').classList.add('hidden');
    });

    // Submit bulk assign
    async function submitBulkAssign() {
        const btn = document.getElementById('addSubmitBtn');
        const errorDiv = document.getElementById('addError');
        const hari = document.getElementById('addHari').value;
        const keterangan = document.getElementById('addKeterangan').value;
        const rincianTugas = document.getElementById('addRincianTugas').value;
        const guruIds = Array.from(document.querySelectorAll('.guru-checkbox:checked')).map(cb => cb.value);

        if (guruIds.length === 0) {
            errorDiv.textContent = 'Pilih minimal satu guru';
            errorDiv.classList.remove('hidden');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
        errorDiv.classList.add('hidden');

        // Fetch fresh CSRF token before write request
        await getFreshCsrfToken();

        const jobdeskId = document.getElementById('addJobdesk').value;
        const params = new URLSearchParams();
        params.append('hari', hari);
        params.append('semester', '<?= $semester; ?>');
        params.append('keterangan', keterangan);
        params.append('rincian_tugas', rincianTugas);
        if (jobdeskId) params.append('jobdesk_id', jobdeskId);
        params.append(csrfName, csrfHash);
        guruIds.forEach(id => params.append('guru_ids[]', id));

        fetch('<?= base_url('admin/guru-piket/bulk-assign'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: params.toString()
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeModal('addModal');
                window.location.reload();
            } else {
                errorDiv.textContent = data.message || 'Gagal menyimpan jadwal piket';
                errorDiv.classList.remove('hidden');
            }
        })
        .catch(() => {
            errorDiv.textContent = 'Terjadi kesalahan jaringan';
            errorDiv.classList.remove('hidden');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan';
        });
    }

    // ==================== EDIT MODAL ====================
    function openEditModal(piket) {
        document.getElementById('editId').value = piket.id;
        document.getElementById('editHari').value = piket.hari;
        document.getElementById('editJobdesk').value = piket.jobdesk_id || '';
        document.getElementById('editKeterangan').value = piket.keterangan || '';
        document.getElementById('editRincianTugas').value = piket.rincian_tugas || '';
        document.getElementById('editError').classList.add('hidden');
        
        if (piket.is_active == 1) {
            document.getElementById('editActive1').checked = true;
        } else {
            document.getElementById('editActive0').checked = true;
        }

        loadAvailableGuru('editGuru', piket.hari, piket.id, null, piket.guru_id);
        openModal('editModal');
    }

    document.getElementById('editHari').addEventListener('change', function() {
        const excludeId = document.getElementById('editId').value;
        loadAvailableGuru('editGuru', this.value, excludeId, null);
    });

    async function submitEditForm(e) {
        e.preventDefault();
        const form = document.getElementById('editForm');
        const btn = document.getElementById('editSubmitBtn');
        const errorDiv = document.getElementById('editError');
        const id = document.getElementById('editId').value;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memperbarui...';
        errorDiv.classList.add('hidden');

        // Fetch fresh CSRF token before write request
        await getFreshCsrfToken();

        const formData = new FormData(form);
        formData.append('hari', document.getElementById('editHari').value);
        formData.append(csrfName, csrfHash);

        fetch('<?= base_url('admin/guru-piket/update/'); ?>' + id, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeModal('editModal');
                window.location.reload();
            } else {
                errorDiv.textContent = data.message || 'Gagal memperbarui jadwal piket';
                errorDiv.classList.remove('hidden');
            }
        })
        .catch(() => {
            errorDiv.textContent = 'Terjadi kesalahan jaringan';
            errorDiv.classList.remove('hidden');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save mr-1"></i> Perbarui';
        });
        return false;
    }

    // ==================== SHARED: Load Available Guru ====================
    async function loadAvailableGuru(selectId, hari, excludeId, emptyMsgId, preselectId) {
        const select = document.getElementById(selectId);
        const emptyMsg = emptyMsgId ? document.getElementById(emptyMsgId) : null;
        
        select.innerHTML = '<option value="">Memuat data guru...</option>';
        if (emptyMsg) emptyMsg.classList.add('hidden');

        // Fetch fresh CSRF token before request
        await getFreshCsrfToken();

        const params = new URLSearchParams();
        params.append('hari', hari);
        params.append('semester', '<?= $semester; ?>');
        params.append(csrfName, csrfHash);
        if (excludeId) params.append('exclude_id', excludeId);

        fetch('<?= base_url('admin/guru-piket/get-available-guru'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: params.toString()
        })
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '<option value="">-- Pilih Guru --</option>';
            if (data.success && data.data && data.data.length > 0) {
                data.data.forEach(guru => {
                    const opt = document.createElement('option');
                    opt.value = guru.id;
                    opt.textContent = guru.nama_lengkap + ' (' + guru.nip + ')';
                    if (preselectId && guru.id == preselectId) opt.selected = true;
                    select.appendChild(opt);
                });
            } else if (emptyMsg) {
                emptyMsg.classList.remove('hidden');
            }
        })
        .catch(() => {
            select.innerHTML = '<option value="">-- Gagal memuat data --</option>';
        });
    }

    // Close modal on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[role="dialog"]:not(.hidden)').forEach(m => {
                m.classList.add('hidden');
                document.body.style.overflow = 'auto';
            });
        }
    });
    // ==================== RINCIAN TUGAS & MASTER JOBDESK HELPERS ====================
    const DEFAULT_RINCIAN_TUGAS = <?= json_encode($defaultRincianTugas); ?>;
    const MASTER_JOBDESKS = <?= json_encode($masterJobdeskList ?? []); ?>;

    function onJobdeskSelectChange(selectId, textareaId) {
        const jobdeskId = document.getElementById(selectId).value;
        const textarea = document.getElementById(textareaId);
        if (!jobdeskId) return;

        const found = MASTER_JOBDESKS.find(j => j.id == jobdeskId);
        if (found && found.rincian_tugas && textarea) {
            textarea.value = found.rincian_tugas;
        }
    }

    function fillDefaultRincianTugas(targetId) {
        const el = document.getElementById(targetId);
        if (el) {
            el.value = DEFAULT_RINCIAN_TUGAS;
        }
    }

    function viewDetailTugas(piket) {
        document.getElementById('detailGuruNama').textContent = piket.nama_lengkap + (piket.nip ? ' (NIP: ' + piket.nip + ')' : '');

        const badgeContainer = document.getElementById('detailJobdeskBadge');
        if (piket.kode_jobdesk && piket.nama_jobdesk) {
            badgeContainer.innerHTML = '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200"><i class="fas fa-tag mr-1.5 text-indigo-500"></i>' + piket.kode_jobdesk + ' - ' + piket.nama_jobdesk + '</span>';
        } else {
            badgeContainer.innerHTML = '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200"><i class="fas fa-exclamation-circle mr-1.5 text-gray-400"></i>Tanpa Master Jobdesk</span>';
        }

        document.getElementById('detailRincianTugas').textContent = piket.rincian_tugas || 'Belum ada rincian jobdesk piket yang dipetakan untuk guru ini.';
        openModal('detailModal');
    }
</script>
<?= $this->endSection() ?>

