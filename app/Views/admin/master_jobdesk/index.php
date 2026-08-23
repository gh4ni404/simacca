<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50/30 p-4 md:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        <i class="fas fa-tasks mr-3"></i>
                        Master Jobdesk Guru Piket
                    </span>
                </h1>
                <p class="text-gray-600 mt-1">Kelola master template rincian tugas piket guru</p>
            </div>
            <div>
                <button onclick="openCreateModal()" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl shadow-md transition">
                    <i class="fas fa-plus mr-2"></i> Tambah Master Jobdesk
                </button>
            </div>
        </div>

        <?= view('components/alerts') ?>

        <!-- List Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-list mr-2 text-indigo-600"></i> Daftar Template Jobdesk Piket
                </h2>
                <span class="text-xs text-gray-500">Total: <?= count($jobdeskList) ?> Jobdesk</span>
            </div>

            <?php if (empty($jobdeskList)): ?>
                <div class="p-12 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                    <p class="text-sm font-medium">Belum ada master jobdesk yang dibuat.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-4">Kode</th>
                                <th class="px-5 py-4">Nama Jobdesk</th>
                                <th class="px-5 py-4">Rincian Panduan Tugas</th>
                                <th class="px-5 py-4 text-center">Status</th>
                                <th class="px-5 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($jobdeskList as $row): ?>
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-5 py-4 font-bold text-indigo-600 whitespace-nowrap">
                                        <?= esc($row['kode_jobdesk']) ?>
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-gray-800">
                                        <?= esc($row['nama_jobdesk']) ?>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-xs text-gray-700 whitespace-pre-line leading-relaxed max-w-xl">
                                            <?= esc($row['rincian_tugas']) ?>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center gap-1">
                                            <?php if ($row['is_active']): ?>
                                                <span class="px-2.5 py-0.5 text-[11px] font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2.5 py-0.5 text-[11px] font-semibold rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                                                    Non-aktif
                                                </span>
                                            <?php endif; ?>

                                            <span class="px-2.5 py-0.5 text-[11px] font-semibold rounded-full <?= $row['total_guru'] > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-gray-100 text-gray-500' ?>">
                                                <i class="fas fa-user-check mr-1"></i> <?= $row['total_guru'] ?> Guru
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button onclick='openBulkMappingModal(<?= json_encode($row) ?>)' class="px-2.5 py-1.5 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm transition flex items-center gap-1" title="Mapping Guru ke Jobdesk Ini">
                                                <i class="fas fa-user-plus"></i> Mapping Guru
                                            </button>
                                            <button onclick='openEditModal(<?= json_encode($row) ?>)' class="p-2 text-gray-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg text-xs transition" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="<?= base_url('admin/master-jobdesk/toggle-status/' . $row['id']) ?>" class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg text-xs transition" title="Toggle Status">
                                                <i class="fas fa-power-off"></i>
                                            </a>
                                            <a href="<?= base_url('admin/master-jobdesk/hapus/' . $row['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus jobdesk ini?');" class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg text-xs transition" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Form Master Jobdesk -->
<div id="jobdeskModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="closeMasterJobdeskModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="relative bg-white rounded-2xl max-w-xl w-full p-6 shadow-xl border border-gray-100 animate-fade-in z-10 pointer-events-auto">
            <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                <h3 id="modalTitle" class="text-lg font-bold text-gray-800">Tambah Master Jobdesk</h3>
                <button type="button" onclick="closeMasterJobdeskModal()" class="text-gray-400 hover:text-gray-600 p-1 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="jobdeskForm" action="<?= base_url('admin/master-jobdesk/simpan') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" id="jobdesk_id" name="id">

                <div>
                    <label for="kode_jobdesk" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kode Jobdesk <span class="text-red-500">*</span></label>
                    <input type="text" id="kode_jobdesk" name="kode_jobdesk" required placeholder="Contoh: JOB-GERBANG" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                <div>
                    <label for="nama_jobdesk" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Jobdesk <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_jobdesk" name="nama_jobdesk" required placeholder="Contoh: Piket Gerbang & Kedisiplinan" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                <div>
                    <label for="rincian_tugas" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Rincian Panduan Tugas <span class="text-red-500">*</span></label>
                    <textarea id="rincian_tugas" name="rincian_tugas" rows="5" required placeholder="Tuliskan butir-butir rincian tugas piket..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                </div>

                <div>
                    <label for="is_active" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status Keaktifan</label>
                    <select id="is_active" name="is_active" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="1">Aktif</option>
                        <option value="0">Non-aktif</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeMasterJobdeskModal()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-md transition">
                        Simpan Jobdesk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Bulk Mapping Guru ke Jobdesk -->
<div id="bulkMappingModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="closeBulkMappingModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="relative bg-white rounded-2xl max-w-xl w-full p-6 shadow-xl border border-gray-100 animate-fade-in z-10 pointer-events-auto max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3 flex-shrink-0">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Bulk Mapping Guru ke Jobdesk</h3>
                    <p id="mappingJobdeskTitle" class="text-xs text-indigo-600 font-semibold mt-0.5"></p>
                </div>
                <button type="button" onclick="closeBulkMappingModal()" class="text-gray-400 hover:text-gray-600 p-1 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="<?= base_url('admin/master-jobdesk/bulk-assign') ?>" method="POST" class="space-y-4 overflow-y-auto flex-1 pr-1">
                <?= csrf_field() ?>
                <input type="hidden" id="mapping_jobdesk_id" name="jobdesk_id">

                <!-- Select Teachers -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-semibold text-gray-700 uppercase">Pilih Guru yang Bertugas <span class="text-red-500">*</span></label>
                        <label class="flex items-center text-xs font-semibold text-indigo-600 cursor-pointer hover:text-indigo-800">
                            <input type="checkbox" id="selectAllGuru" onchange="toggleSelectAllGuru(this)" class="mr-1 rounded text-indigo-600 focus:ring-indigo-500"> Pilih Semua
                        </label>
                    </div>

                    <!-- Search Guru Input -->
                    <div class="relative mb-2">
                        <input type="text" id="searchGuruInput" onkeyup="filterGuruList()" placeholder="Cari nama guru atau NIP..." class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                    </div>

                    <div class="border border-gray-200 rounded-xl divide-y divide-gray-100 max-h-56 overflow-y-auto bg-gray-50/50 p-2 space-y-1">
                        <?php foreach ($guruList as $guru): ?>
                            <label class="guru-item flex items-center justify-between p-2 rounded-xl hover:bg-white transition cursor-pointer border border-transparent hover:border-gray-100" data-search="<?= esc(strtolower($guru['nama_lengkap'] . ' ' . $guru['nip'])) ?>">
                                <div class="flex items-center">
                                    <input type="checkbox" name="guru_ids[]" value="<?= $guru['id'] ?>" class="guru-checkbox rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4 mr-3">
                                    <div>
                                        <div class="text-xs font-bold text-gray-800"><?= esc($guru['nama_lengkap']) ?></div>
                                        <div class="text-[11px] text-gray-500">NIP: <?= esc($guru['nip'] ?: '-') ?></div>
                                    </div>
                                </div>
                                <div class="guru-status-badge ml-2 flex-shrink-0"></div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Catatan / Keterangan -->
                <div>
                    <label for="mapping_keterangan" class="block text-xs font-semibold text-gray-700 uppercase mb-1">Catatan Keterangan (Opsional)</label>
                    <textarea id="mapping_keterangan" name="keterangan" rows="2" placeholder="Catatan tambahan penugasan..." class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 text-xs"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 flex-shrink-0">
                    <button type="button" onclick="closeBulkMappingModal()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-md transition">
                        <i class="fas fa-check mr-1.5"></i> Simpan Mapping Guru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const GURU_JOBDESK_MAP = <?= json_encode($guruJobdeskMap ?? (object)[]) ?>;
const SCHEDULED_GURU_MAP = <?= json_encode($scheduledGuruMap ?? (object)[]) ?>;

function openCreateModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Master Jobdesk';
    document.getElementById('jobdeskForm').action = '<?= base_url('admin/master-jobdesk/simpan') ?>';
    document.getElementById('jobdesk_id').value = '';
    document.getElementById('kode_jobdesk').value = '';
    document.getElementById('nama_jobdesk').value = '';
    document.getElementById('rincian_tugas').value = '';
    document.getElementById('is_active').value = '1';
    document.getElementById('jobdeskModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openEditModal(data) {
    document.getElementById('modalTitle').innerText = 'Edit Master Jobdesk';
    document.getElementById('jobdeskForm').action = '<?= base_url('admin/master-jobdesk/update/') ?>' + data.id;
    document.getElementById('jobdesk_id').value = data.id;
    document.getElementById('kode_jobdesk').value = data.kode_jobdesk;
    document.getElementById('nama_jobdesk').value = data.nama_jobdesk;
    document.getElementById('rincian_tugas').value = data.rincian_tugas;
    document.getElementById('is_active').value = data.is_active;
    document.getElementById('jobdeskModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openBulkMappingModal(data) {
    document.getElementById('mapping_jobdesk_id').value = data.id;
    document.getElementById('mappingJobdeskTitle').innerText = data.nama_jobdesk + ' (' + data.kode_jobdesk + ')';
    document.getElementById('searchGuruInput').value = '';

    const assignedGuruIds = data.assigned_guru_ids || [];
    const currentJobdeskId = parseInt(data.id);

    // Sync teacher checkboxes and eliminate teachers assigned to other jobdesks
    const items = document.querySelectorAll('.guru-item');
    let visibleCount = 0;
    let checkedCount = 0;

    items.forEach(item => {
        const cb = item.querySelector('.guru-checkbox');
        const guruId = parseInt(cb.value);
        const badgeEl = item.querySelector('.guru-status-badge');
        const existingJobdeskId = GURU_JOBDESK_MAP[guruId];
        const isScheduled = !!SCHEDULED_GURU_MAP[guruId];

        if (!isScheduled) {
            // NOT scheduled in guru_piket -> SHOW DISABLED with Warning Badge
            item.style.display = '';
            item.classList.remove('hidden-other-jobdesk');
            item.classList.add('opacity-65');
            cb.checked = false;
            cb.disabled = true;
            badgeEl.innerHTML = '<span class="text-[10px] bg-red-50 text-red-600 font-medium px-2 py-0.5 rounded-full border border-red-200" title="Guru harus memiliki jadwal Piket Shalat terlebih dahulu"><i class="fas fa-exclamation-circle mr-1 text-red-500"></i>Belum diatur Jadwal Piket</span>';
        } else if (assignedGuruIds.includes(guruId)) {
            // Mapped to THIS jobdesk -> SHOW & CHECK
            item.style.display = '';
            item.classList.remove('hidden-other-jobdesk', 'opacity-65');
            cb.checked = true;
            cb.disabled = false;
            badgeEl.innerHTML = '<span class="text-[10px] bg-emerald-100 text-emerald-800 font-semibold px-2 py-0.5 rounded-full"><i class="fas fa-check-circle mr-1 text-emerald-600"></i>Terpetakan di jobdesk ini</span>';
            visibleCount++;
            checkedCount++;
        } else if (existingJobdeskId && existingJobdeskId !== currentJobdeskId) {
            // Mapped to ANOTHER jobdesk -> ELIMINATE / HIDE
            item.style.display = 'none';
            item.classList.add('hidden-other-jobdesk');
            cb.checked = false;
            cb.disabled = true;
        } else {
            // Scheduled & NOT mapped -> SHOW & UNCHECK
            item.style.display = '';
            item.classList.remove('hidden-other-jobdesk', 'opacity-65');
            cb.checked = false;
            cb.disabled = false;
            badgeEl.innerHTML = '<span class="text-[10px] bg-gray-100 text-gray-500 font-normal px-2 py-0.5 rounded-full">Tersedia</span>';
            visibleCount++;
        }
    });

    document.getElementById('selectAllGuru').checked = (checkedCount === visibleCount && visibleCount > 0);

    filterGuruList();

    document.getElementById('bulkMappingModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeMasterJobdeskModal() {
    document.getElementById('jobdeskModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function closeBulkMappingModal() {
    document.getElementById('bulkMappingModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function closeModal(id) {
    if (id === 'bulkMappingModal') {
        closeBulkMappingModal();
    } else if (id) {
        const targetModal = document.getElementById(id);
        if (targetModal) targetModal.classList.add('hidden');
    } else {
        closeMasterJobdeskModal();
    }
    document.body.style.overflow = 'auto';
}

function filterGuruList() {
    const query = document.getElementById('searchGuruInput').value.toLowerCase();
    const items = document.querySelectorAll('.guru-item:not(.hidden-other-jobdesk)');
    items.forEach(item => {
        const searchText = item.getAttribute('data-search') || '';
        if (searchText.includes(query)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

function toggleSelectAllGuru(masterCheckbox) {
    const visibleItems = document.querySelectorAll('.guru-item:not(.hidden-other-jobdesk)');
    visibleItems.forEach(item => {
        if (item.style.display !== 'none') {
            const cb = item.querySelector('.guru-checkbox');
            if (cb && !cb.disabled) {
                cb.checked = masterCheckbox.checked;
            }
        }
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeMasterJobdeskModal();
        closeBulkMappingModal();
    }
});
</script>
<?= $this->endSection() ?>
