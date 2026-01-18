<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle ?></h2>
        <p class="text-gray-600"><?= $pageDescription ?></p>
    </div>

    <!-- Import Guide -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
            <i class="fas fa-info-circle mr-2"></i> Petunjuk Import
        </h3>
        <ul class="list-disc list-inside space-y-2 text-blue-700">
            <li>Format file harus Excel (.xlsx atau .xls)</li>
            <li>Pastikan format kolom sesuai template</li>
            <li>Kolom wajib: NIP, Nama Lengkap, Jenis Kelamin, role</li>
            <li>Kolom optional: Email, Mata Pelajaran</li>
            <li>Jika username/password tidak diisi, akan digenerate otomatis</li>
            <li>Download template untuk panduan format data</li>

            <li>Jangan ubah nama kolom</li>
            <li>Jenis kelamin hanya <b>L</b> atau <b>P</b></li>
            <li>Role: <b>guru_mapel</b>, <b>wali_kelas</b>, atau <b>wakakur</b></li>
            <li>IS_WALI_KELAS: 1 (ya) / 0 (tidak)</li>
            <li>Kosongkan KELAS_ID jika bukan wali kelas</li>
        </ul>

        <div class="mt-4 flex space-x-3">
            <a href="<?= base_url('admin/guru/download-template') ?>"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-download mr-2"></i> Download Template
            </a>
            <a href="<?= base_url('admin/guru') ?>"
                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar guru
            </a>
        </div>
    </div>

    <!-- Import Form -->
    <div class="border border-gray-200 rounded-lg p-6">
        <form action="<?= base_url('admin/guru/process-import') ?>" method="POST" enctype="multipart/form-data" id="importForm">
            <?= csrf_field() ?>

            <div class="space-y-4">
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel *</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-500 transition-colors">
                        <div class="space-y-1 text-center">
                            <i class="fas fa-file-excel text-4xl text-green-500 mx-auto"></i>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                    <span>Upload file Excel</span>
                                    <input id="file-upload" name="file_excel" type="file" class="sr-only" accept=".xlsx,.xls">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">.xlsx atau .xls</p>
                            <p id="fileName" class="text-sm text-gray-900 mt-2"></p>
                        </div>
                    </div>
                </div>

                <!-- Import Options -->
                <div class="border-t pt-4">
                    <h4 class="font-medium text-gray-700 mb-3">Opsi Import</h4>
                    <div class="space-y-3">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="skip_duplicate" class="rounded text-indigo-600" checked>
                            <span class="ml-2 text-gray-700">Lewati data duplikat (berdasarkan NIP)</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="generate_password" class="rounded text-indigo-600">
                            <span class="ml-2 text-gray-700">Generate password otomatis untuk data tanpa password</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="update_existing" class="rounded text-indigo-600">
                            <span class="ml-2 text-gray-700">Update data yang sudah ada (berdasarkan NIP)</span>
                        </label>
                    </div>
                </div>

                <!-- Preview Table (hidden initially) -->
                <div id="previewSection" class="hidden">
                    <h4 class="font-medium text-gray-700 mb-3">Preview Data</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="previewTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">NIP</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Guru</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mata Pelajaran</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody id="previewBody" class="bg-white divide-y divide-gray-200">
                                <!-- Preview rows will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 border-t pt-6">
                    <a href="<?= base_url('admin/guru') ?>"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center"
                        id="submitBtn" disabled>
                        <i class="fas fa-upload mr-2"></i> Proses Import
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // File upload handler
    document.getElementById('file-upload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const fileName = document.getElementById('fileName');
        const submitBtn = document.getElementById('submitBtn');
        const previewSection = document.getElementById('previewSection');

        if (file) {
            fileName.textContent = file.name;
            submitBtn.disabled = false;

            // Simple file validation
            const extension = file.name.split('.').pop().toLowerCase();
            if (!['xlsx', 'xls'].includes(extension)) {
                alert('Format file harus Excel (.xlsx atau .xls)');
                submitBtn.disabled = true;
                return;
            }

            // Show preview section
            previewSection.classList.remove('hidden');

            // Preview data (simplified - in real app you might want to use SheetJS)
            // For now, we'll just show a placeholder
            const previewBody = document.getElementById('previewBody');
            previewBody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Membaca data dari file...
                    </td>
                </tr>
            `;

            // In a real application, you would parse the Excel file here
            // using a library like SheetJS and populate the preview table

        } else {
            fileName.textContent = '';
            submitBtn.disabled = true;
            previewSection.classList.add('hidden');
        }
    });

    // Form submission with confirmation
    document.getElementById('importForm').addEventListener('submit', function(e) {
        if (!confirm('Apakah Anda yakin ingin melakukan import data? Pastikan data sudah benar.')) {
            e.preventDefault();
        }
    });
</script>
<?= $this->endSection() ?>