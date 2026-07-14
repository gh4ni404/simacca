<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">

    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-list-check mr-2 text-blue-600"></i>
                    Master Task
                </h1>
                <p class="text-gray-600 mt-1">Kelola template task untuk siswa di tempat PKL ini</p>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Tambah Template -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow sticky top-4">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-500 to-blue-600">
                    <h2 class="text-lg font-semibold text-white">
                        <i class="fas fa-plus-circle mr-2"></i>Tambah Template
                    </h2>
                </div>
                <div class="p-5">
                    <form action="<?= base_url('instruktur/task-template/simpan'); ?>" method="POST">
                        <?= csrf_field(); ?>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Task <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="judul"
                                   value="<?= old('judul'); ?>"
                                   placeholder="Contoh: Desain Brosur"
                                   maxlength="255" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <select name="kategori_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ((new \App\Models\PklCategoryModel())->getDropdown() as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= old('kategori_id') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= esc($cat['nama']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi</label>
                            <input type="text" name="estimasi"
                                   value="<?= old('estimasi'); ?>"
                                   placeholder="Contoh: 3 hari, 1 minggu"
                                   maxlength="30"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <button type="submit"
                                class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm">
                            <i class="fas fa-save mr-2"></i>Simpan Template
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- List Templates -->
        <div class="lg:col-span-2">
            <?php if (empty($templates)): ?>
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-700">Belum Ada Template</h3>
                <p class="text-gray-500 mt-1">Buat template task pertama untuk siswa Anda</p>
            </div>
            <?php else: ?>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-700"><?= count($templates) ?> template task</p>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php foreach ($templates as $t): ?>
                    <div class="px-5 py-4 hover:bg-gray-50 transition-colors" id="template-<?= $t['id'] ?>">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-gray-800"><?= esc($t['judul']) ?></h3>
                                <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                    <?php if (!empty($t['kategori_nama'])): ?>
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                        <?= match($t['kategori_nama']) {
                                            'Desain' => 'bg-purple-100 text-purple-700',
                                            'Programming' => 'bg-blue-100 text-blue-700',
                                            'Administrasi' => 'bg-green-100 text-green-700',
                                            'Marketing' => 'bg-orange-100 text-orange-700',
                                            default => 'bg-gray-100 text-gray-600'
                                        } ?>"><?= esc($t['kategori_nama']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($t['estimasi'])): ?>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                        <i class="fas fa-clock mr-1"></i><?= esc($t['estimasi']) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex-shrink-0 flex items-center gap-1">
                                <!-- Edit Button -->
                                <button onclick="openEditModal(<?= $t['id'] ?>, '<?= esc($t['judul']) ?>', <?= $t['kategori_id'] ?? 'null' ?>, '<?= esc($t['estimasi'] ?? '') ?>')"
                                        class="px-2 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 text-xs transition-colors"
                                        title="Edit">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <!-- Delete Button -->
                                <a href="<?= base_url('instruktur/task-template/hapus/' . $t['id']); ?>"
                                   onclick="return confirm('Hapus template ini?')"
                                   class="px-2 py-1.5 bg-red-50 text-red-500 rounded-lg hover:bg-red-100 text-xs transition-colors"
                                   title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-[9999] bg-black/50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Edit Template Task</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form id="editForm" method="POST">
            <?= csrf_field(); ?>
            <div class="p-5 space-y-4">
                <input type="hidden" name="_method" value="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Task <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" id="edit_judul"
                           maxlength="255" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="kategori_id" id="edit_kategori"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ((new \App\Models\PklCategoryModel())->getDropdown() as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= esc($cat['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi</label>
                    <input type="text" name="estimasi" id="edit_estimasi"
                           placeholder="Contoh: 3 hari, 1 minggu"
                           maxlength="30"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 flex gap-3">
                <button type="button" onclick="closeEditModal()"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    <i class="fas fa-save mr-1"></i>Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, judul, kategoriId, estimasi) {
    document.getElementById('editForm').action = '<?= base_url('instruktur/task-template/update/') ?>' + id;
    document.getElementById('edit_judul').value = judul;
    document.getElementById('edit_kategori').value = kategoriId || '';
    document.getElementById('edit_estimasi').value = estimasi || '';
    const modal = document.getElementById('editModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
<?= $this->endSection() ?>
