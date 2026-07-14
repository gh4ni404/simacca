<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-tags mr-2 text-purple-600"></i>Kategori PKL
        </h1>
        <p class="text-gray-600 mt-1">Kelola master data kategori pekerjaan PKL</p>
    </div>

    <?= view('components/alerts') ?>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Add Category Form -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4"><i class="fas fa-plus-circle mr-2 text-purple-600"></i>Tambah Kategori</h3>
            <form action="<?= base_url('admin/pkl-categories/simpan'); ?>" method="POST">
                <?= csrf_field(); ?>
                <div class="flex gap-3">
                    <input type="text" name="nama" required
                           placeholder="Nama kategori baru..."
                           class="flex-1 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Tambah
                    </button>
                </div>
            </form>
        </div>

        <!-- Category List -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800"><i class="fas fa-list mr-2 text-purple-600"></i>Daftar Kategori</h3>
            </div>

            <?php if (empty($categories)): ?>
            <div class="p-8 text-center">
                <i class="fas fa-tags text-3xl text-gray-300 mb-2"></i>
                <p class="text-gray-500 text-sm">Belum ada kategori</p>
            </div>
            <?php else: ?>
            <div class="divide-y divide-gray-100">
                <?php foreach ($categories as $cat): ?>
                <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tag text-purple-600 text-xs"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-800"><?= esc($cat['nama']); ?></span>
                    </div>
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="editCategory(<?= $cat['id']; ?>, '<?= esc($cat['nama']); ?>')"
                                class="text-xs text-blue-600 hover:text-blue-800 px-2 py-1">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="<?= base_url('admin/pkl-categories/hapus/' . $cat['id']); ?>" method="POST" class="inline"
                              onsubmit="return confirm('Hapus kategori ini?')">
                            <?= csrf_field(); ?>
                            <button type="submit" class="text-xs text-red-600 hover:text-red-800 px-2 py-1">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-[9999] bg-black/50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Edit Kategori</h3>
        </div>
        <form id="editForm" method="POST">
            <?= csrf_field(); ?>
            <div class="p-6">
                <input type="hidden" name="_method" value="POST">
                <input type="text" id="editNama" name="nama" required
                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal()"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCategory(id, nama) {
    document.getElementById('editForm').action = '<?= base_url("admin/pkl-categories/update/") ?>' + id;
    document.getElementById('editNama').value = nama;
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');
}
function closeModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
<?= $this->endSection() ?>
