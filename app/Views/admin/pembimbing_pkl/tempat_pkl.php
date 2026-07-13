<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle; ?></h2>
            <p class="text-gray-600"><?= $pageDescription; ?></p>
        </div>
        <div class="mt-4 md:mt-0">
            <button onclick="openCreateModal()"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Tempat PKL
            </button>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <div class="table-responsive">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Perusahaan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kota</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instruktur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($tempatPkl)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-building text-4xl text-gray-300 mb-4"></i>
                            <p>Belum ada data tempat PKL</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php foreach ($tempatPkl as $t): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($t['nama_perusahaan']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?= esc($t['alamat'] ?? '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($t['kota'] ?? '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($t['kontak'] ?? '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($t['telepon'] ?? '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if (!empty($t['nama_instruktur'])): ?>
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user-tie text-indigo-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900 block leading-tight"><?= esc($t['nama_instruktur']); ?></span>
                                            <?php if (!empty($t['email_instruktur'])): ?>
                                                <span class="text-xs text-gray-400"><?= esc($t['email_instruktur']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                        <i class="fas fa-user-slash mr-1"></i> Belum ada
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <button onclick='openEditModal(<?= json_encode([
                                        'id' => $t['id'],
                                        'nama_perusahaan' => $t['nama_perusahaan'],
                                        'alamat' => $t['alamat'] ?? '',
                                        'kota' => $t['kota'] ?? '',
                                        'kontak' => $t['kontak'] ?? '',
                                        'telepon' => $t['telepon'] ?? '',
                                        'instruktur_id' => $t['instruktur_id'] ?? '',
                                        'nama_instruktur' => $t['nama_instruktur'] ?? '',
                                        'email_instruktur' => $t['email_instruktur'] ?? '',
                                        'telepon_instruktur' => $t['telepon_instruktur'] ?? '',
                                        'username_instruktur' => $t['username_instruktur'] ?? '',
                                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>)'
                                        class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?= base_url('admin/pembimbing-pkl/tempat-pkl/hapus/' . $t['id']); ?>"
                                        class="text-red-600 hover:text-red-900" title="Hapus"
                                        onclick="return confirm('Hapus tempat PKL ini? Semua data terkait (pembimbing, instruktur, siswa) juga akan terhapus.')">
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

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-xl rounded-xl bg-white my-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-indigo-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Tempat PKL</h3>
                    <p class="text-sm text-gray-500">Lengkapi data perusahaan dan instruktur</p>
                </div>
            </div>
            <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form action="<?= base_url('admin/pembimbing-pkl/tempat-pkl/simpan') ?>" method="POST" id="createForm">
            <?= csrf_field() ?>

            <!-- Section: Data Perusahaan -->
            <div class="mb-6">
                <div class="flex items-center space-x-2 mb-3">
                    <i class="fas fa-industry text-gray-400"></i>
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Data Perusahaan</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_perusahaan" required placeholder="PT. Contoh Perusahaan"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="alamat" rows="2" placeholder="Jl. Contoh No. 123, Kota ..."
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <input type="text" name="kota" placeholder="Jakarta"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input type="text" name="telepon" placeholder="021-1234567"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Person</label>
                        <input type="text" name="kontak" placeholder="Nama kontak person di perusahaan"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center">
                    <label class="flex items-center space-x-2 bg-white px-4 cursor-pointer" onclick="event.stopPropagation()">
                        <input type="checkbox" id="toggleInstrukturCreate" onchange="toggleInstrukturSection('create')"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Tambah Instruktur PKL</span>
                    </label>
                </div>
            </div>

            <!-- Section: Data Instruktur -->
            <div id="instrukturFieldsCreate" class="hidden mb-6">
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-100 rounded-xl p-5">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-tie text-indigo-600 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Data Instruktur PKL</h4>
                            <p class="text-xs text-gray-500">Akun akan dibuat untuk login instruktur</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="instruktur_nama" id="create_instruktur_nama" placeholder="Nama lengkap instruktur"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="instruktur_email" id="create_instruktur_email" placeholder="email@perusahaan.com"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                            <input type="text" name="instruktur_telepon" id="create_instruktur_telepon" placeholder="0812-xxxx-xxxx"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="instruktur_username" id="create_instruktur_username" placeholder="otomatis dari email"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="instruktur_password" id="create_instruktur_password" value="instruktur123" placeholder="instruktur123"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white pr-10">
                                <button type="button" onclick="togglePasswordVisibility('create_instruktur_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1"><i class="fas fa-info-circle"></i> Default: instruktur123</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal('createModal')"
                    class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-xl rounded-xl bg-white my-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-edit text-amber-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Tempat PKL</h3>
                    <p class="text-sm text-gray-500">Perbarui data perusahaan dan instruktur</p>
                </div>
            </div>
            <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form id="editForm" method="POST">
            <?= csrf_field() ?>

            <!-- Section: Data Perusahaan -->
            <div class="mb-6">
                <div class="flex items-center space-x-2 mb-3">
                    <i class="fas fa-industry text-gray-400"></i>
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Data Perusahaan</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_perusahaan" id="edit_nama_perusahaan" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="alamat" id="edit_alamat" rows="2"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <input type="text" name="kota" id="edit_kota"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input type="text" name="telepon" id="edit_telepon"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Person</label>
                        <input type="text" name="kontak" id="edit_kontak"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>
            </div>

            <!-- Divider with Toggle -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center">
                    <label class="flex items-center space-x-2 bg-white px-4 cursor-pointer" onclick="event.stopPropagation()">
                        <input type="checkbox" id="toggleInstrukturEdit" onchange="toggleInstrukturSection('edit')"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700 whitespace-nowrap" id="editInstrukturLabel">Tambah Instruktur PKL</span>
                    </label>
                </div>
            </div>

            <!-- Section: Data Instruktur -->
            <div id="instrukturFieldsEdit" class="hidden mb-6">
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-100 rounded-xl p-5">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-tie text-indigo-600 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Data Instruktur PKL</h4>
                            <p class="text-xs text-gray-500" id="editInstrukturHint">Akun akan dibuat untuk login instruktur</p>
                        </div>
                    </div>

                    <input type="hidden" name="instruktur_id" id="edit_instruktur_id">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="instruktur_nama" id="edit_instruktur_nama"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="instruktur_email" id="edit_instruktur_email"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                            <input type="text" name="instruktur_telepon" id="edit_instruktur_telepon"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="instruktur_username" id="edit_instruktur_username"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <input type="password" name="instruktur_password" id="edit_instruktur_password" placeholder="Kosongkan jika tidak diubah"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white pr-10">
                                <button type="button" onclick="togglePasswordVisibility('edit_instruktur_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1"><i class="fas fa-info-circle"></i> Kosongkan jika tidak ingin mengubah password</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModal('editModal')"
                    class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Update</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createForm').reset();
        const section = document.getElementById('instrukturFieldsCreate');
        section.classList.add('hidden');
        document.getElementById('toggleInstrukturCreate').checked = false;
        document.getElementById('createModal').classList.remove('hidden');
    }

    function openEditModal(data) {
        document.getElementById('edit_nama_perusahaan').value = data.nama_perusahaan || '';
        document.getElementById('edit_alamat').value = data.alamat || '';
        document.getElementById('edit_kota').value = data.kota || '';
        document.getElementById('edit_kontak').value = data.kontak || '';
        document.getElementById('edit_telepon').value = data.telepon || '';
        document.getElementById('editForm').action = '<?= base_url('admin/pembimbing-pkl/tempat-pkl/update/') ?>' + data.id;

        const hasInstruktur = data.nama_instruktur && data.nama_instruktur.length > 0;
        const checkbox = document.getElementById('toggleInstrukturEdit');
        const section = document.getElementById('instrukturFieldsEdit');
        const label = document.getElementById('editInstrukturLabel');
        const hint = document.getElementById('editInstrukturHint');

        if (hasInstruktur) {
            checkbox.checked = true;
            section.classList.remove('hidden');
            label.textContent = 'Instruktur PKL Terdaftar';
            hint.textContent = 'Kosongkan semua field instruktur untuk menghapus instruktur';
        } else {
            checkbox.checked = false;
            section.classList.add('hidden');
            label.textContent = 'Tambah Instruktur PKL';
            hint.textContent = 'Akun akan dibuat untuk login instruktur';
        }

        document.getElementById('edit_instruktur_id').value = data.instruktur_id || '';
        document.getElementById('edit_instruktur_nama').value = data.nama_instruktur || '';
        document.getElementById('edit_instruktur_email').value = data.email_instruktur || '';
        document.getElementById('edit_instruktur_telepon').value = data.telepon_instruktur || '';
        document.getElementById('edit_instruktur_username').value = data.username_instruktur || '';
        document.getElementById('edit_instruktur_password').value = '';

        document.getElementById('editModal').classList.remove('hidden');
    }

    function toggleInstrukturSection(type) {
        const prefix = type === 'create' ? 'Create' : 'Edit';
        const checkbox = document.getElementById('toggleInstruktur' + prefix);
        const section = document.getElementById('instrukturFields' + prefix);

        if (type === 'edit') {
            const label = document.getElementById('editInstrukturLabel');
            const hint = document.getElementById('editInstrukturHint');
            if (checkbox.checked) {
                label.textContent = 'Instruktur PKL Terdaftar';
                hint.textContent = 'Kosongkan semua field instruktur untuk menghapus instruktur';
            } else {
                label.textContent = 'Tambah Instruktur PKL';
                hint.textContent = 'Akun akan dibuat untuk login instruktur';
            }
        }

        if (checkbox.checked) {
            section.classList.remove('hidden');
        } else {
            section.classList.add('hidden');
        }
    }

    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('bg-opacity-50')) {
            event.target.classList.add('hidden');
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pairs = [
            { email: 'create_instruktur_email', username: 'create_instruktur_username' },
            { email: 'edit_instruktur_email', username: 'edit_instruktur_username' },
        ];

        pairs.forEach(function(pair) {
            const emailInput = document.getElementById(pair.email);
            const usernameInput = document.getElementById(pair.username);
            if (emailInput && usernameInput) {
                emailInput.addEventListener('input', function() {
                    if (!usernameInput.dataset.manual) {
                        usernameInput.value = this.value.split('@')[0];
                    }
                });
                usernameInput.addEventListener('input', function() {
                    this.dataset.manual = this.value.length > 0 ? '1' : '';
                });
            }
        });
    });
</script>
<?= $this->endSection() ?>
