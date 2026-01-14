<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<!-- Main Container -->
<div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <span class="bg-blue-100 text-blue-600 p-3 rounded-xl">
                            <i class="fas fa-clipboard-check"></i>
                        </span>
                        <?= esc($pageTitle) ?>
                    </h1>
                    <p class="mt-2 text-gray-600"><?= esc($pageDescription) ?></p>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?= view('components/alerts') ?>
        <!-- Alert Messages -->

        <!-- Filter Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h2 class="text-white font-semibold text-lg flex items-center gap-2">
                    <i class="fas fa-filter"></i>
                    Filter Absensi
                </h2>
            </div>
            <div class="p-6">
                <form method="get" action="<?= base_url('admin/absensi') ?>" id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        
                        <!-- Tanggal Dari -->
                        <div>
                            <label for="tanggal_dari" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                                Tanggal Dari
                            </label>
                            <input type="date" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                                   id="tanggal_dari" 
                                   name="tanggal_dari" 
                                   value="<?= esc($filters['tanggal_dari']) ?>">
                        </div>

                        <!-- Tanggal Sampai -->
                        <div>
                            <label for="tanggal_sampai" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                                Tanggal Sampai
                            </label>
                            <input type="date" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                                   id="tanggal_sampai" 
                                   name="tanggal_sampai" 
                                   value="<?= esc($filters['tanggal_sampai']) ?>">
                        </div>

                        <!-- Kelas -->
                        <div>
                            <label for="kelas_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-users text-gray-400 mr-1"></i>
                                Kelas
                            </label>
                            <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                                    id="kelas_id" 
                                    name="kelas_id">
                                <option value="">Semua Kelas</option>
                                <?php foreach ($kelasList as $kelas): ?>
                                    <option value="<?= $kelas['id'] ?>" 
                                        <?= $filters['kelas_id'] == $kelas['id'] ? 'selected' : '' ?>>
                                        <?= esc($kelas['nama_kelas']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Guru -->
                        <div>
                            <label for="guru_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-chalkboard-teacher text-gray-400 mr-1"></i>
                                Guru
                            </label>
                            <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                                    id="guru_id" 
                                    name="guru_id">
                                <option value="">Semua Guru</option>
                                <?php foreach ($guruList as $guru): ?>
                                    <option value="<?= $guru['id'] ?>" 
                                        <?= $filters['guru_id'] == $guru['id'] ? 'selected' : '' ?>>
                                        <?= esc($guru['nama_lengkap']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Mata Pelajaran -->
                        <div>
                            <label for="mapel_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-book text-gray-400 mr-1"></i>
                                Mata Pelajaran
                            </label>
                            <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                                    id="mapel_id" 
                                    name="mapel_id">
                                <option value="">Semua Mapel</option>
                                <?php foreach ($mapelList as $mapel): ?>
                                    <option value="<?= $mapel['id'] ?>" 
                                        <?= $filters['mapel_id'] == $mapel['id'] ? 'selected' : '' ?>>
                                        <?= esc($mapel['nama_mapel']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status_lock" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-lock text-gray-400 mr-1"></i>
                                Status
                            </label>
                            <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                                    id="status_lock" 
                                    name="status_lock">
                                <option value="">Semua Status</option>
                                <option value="editable" <?= $filters['status_lock'] == 'editable' ? 'selected' : '' ?>>
                                    Dapat Diedit
                                </option>
                                <option value="locked" <?= $filters['status_lock'] == 'locked' ? 'selected' : '' ?>>
                                    Terkunci
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <i class="fas fa-search mr-2"></i>
                            Terapkan Filter
                        </button>
                        <a href="<?= base_url('admin/absensi') ?>" 
                           class="inline-flex items-center px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                            <i class="fas fa-redo mr-2"></i>
                            Reset Filter
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <h2 class="text-white font-semibold text-lg flex items-center gap-2">
                        <i class="fas fa-list"></i>
                        Data Absensi 
                        <span class="bg-white/20 px-3 py-1 rounded-full text-sm">
                            <?= count($absensiList) ?> record
                        </span>
                    </h2>
                    <button type="button" 
                            id="bulkUnlockBtn" 
                            disabled
                            class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                        <i class="fas fa-unlock mr-2"></i>
                        Unlock Terpilih (<span id="selectedCount">0</span>)
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" 
                                       id="checkAll" 
                                       class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Hari/Jam
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Kelas
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Guru
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Mata Pelajaran
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($absensiList)): ?>
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <i class="fas fa-inbox text-6xl mb-4"></i>
                                        <p class="text-lg font-medium text-gray-600">Tidak ada data absensi ditemukan</p>
                                        <p class="text-sm text-gray-500 mt-1">Coba sesuaikan filter Anda</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($absensiList as $absensi): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4">
                                        <?php if ($absensi['is_locked']): ?>
                                            <input type="checkbox" 
                                                   class="absensi-checkbox w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer" 
                                                   value="<?= $absensi['id'] ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-calendar text-blue-500"></i>
                                            <span class="font-medium text-gray-900">
                                                <?= date('d M Y', strtotime($absensi['tanggal'])) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm">
                                            <div class="text-gray-500 text-xs mb-1"><?= esc($absensi['hari']) ?></div>
                                            <div class="font-medium text-gray-900">
                                                <i class="far fa-clock text-gray-400"></i>
                                                <?= esc($absensi['jam_mulai']) ?> - <?= esc($absensi['jam_selesai']) ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                            <i class="fas fa-users mr-1"></i>
                                            <?= esc($absensi['nama_kelas']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm">
                                            <div class="font-medium text-gray-900">
                                                <i class="fas fa-user-tie text-gray-400 mr-1"></i>
                                                <?= esc($absensi['nama_guru']) ?>
                                            </div>
                                            <?php if ($absensi['nama_guru_pengganti']): ?>
                                                <div class="text-blue-600 text-xs mt-1">
                                                    <i class="fas fa-user-circle"></i>
                                                    Pengganti: <?= esc($absensi['nama_guru_pengganti']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-gray-900 font-medium">
                                            <?= esc($absensi['nama_mapel']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="space-y-1">
                                            <?php if ($absensi['is_editable']): ?>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    <i class="fas fa-unlock mr-1"></i>
                                                    Dapat Diedit
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    <i class="fas fa-lock mr-1"></i>
                                                    Terkunci
                                                </span>
                                            <?php endif; ?>
                                            <div class="text-xs text-gray-500">
                                                <i class="far fa-clock"></i>
                                                <?= number_format($absensi['hours_passed'], 1) ?> jam lalu
                                            </div>
                                            <?php if (!empty($absensi['unlocked_at'])): ?>
                                                <div class="text-xs text-yellow-600">
                                                    <i class="fas fa-info-circle"></i>
                                                    Pernah di-unlock
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <?php if ($absensi['is_locked']): ?>
                                            <button type="button" 
                                                    onclick="unlockAbsensi(<?= $absensi['id'] ?>, '<?= esc($absensi['nama_guru']) ?>', '<?= esc($absensi['nama_mapel']) ?>', '<?= date('d M Y', strtotime($absensi['tanggal'])) ?>')"
                                                    class="inline-flex items-center px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                                                <i class="fas fa-unlock mr-1"></i>
                                                Unlock
                                            </button>
                                        <?php else: ?>
                                            <span class="inline-flex items-center text-sm text-green-600">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Dapat diedit
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>

<script>
// Check all checkboxes
document.getElementById('checkAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.absensi-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectedCount();
});

// Update selected count
document.querySelectorAll('.absensi-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const checked = document.querySelectorAll('.absensi-checkbox:checked');
    document.getElementById('selectedCount').textContent = checked.length;
    document.getElementById('bulkUnlockBtn').disabled = checked.length === 0;
}

// Unlock single absensi
function unlockAbsensi(id, guru, mapel, tanggal) {
    if (confirm(`🔓 Unlock absensi?\n\n📚 Guru: ${guru}\n📖 Mapel: ${mapel}\n📅 Tanggal: ${tanggal}\n\n⏰ Setelah unlock, guru dapat mengedit selama 24 jam.`)) {
        window.location.href = '<?= base_url('admin/absensi/unlock/') ?>' + id;
    }
}

// Bulk unlock
document.getElementById('bulkUnlockBtn')?.addEventListener('click', function() {
    const checked = Array.from(document.querySelectorAll('.absensi-checkbox:checked')).map(cb => cb.value);
    
    if (confirm(`🔓 Unlock ${checked.length} absensi terpilih?\n\n⏰ Semua guru terkait dapat mengedit absensi mereka selama 24 jam.`)) {
        fetch('<?= base_url('admin/absensi/bulk-unlock') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'absensi_ids=' + JSON.stringify(checked)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('❌ Error: ' + error);
        });
    }
});
</script>

<?= $this->endSection() ?>
