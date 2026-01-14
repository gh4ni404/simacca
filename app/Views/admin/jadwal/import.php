<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle ?></h2>
        <p class="text-gray-600"><?= $pageDescription ?></p>
    </div>

    <pre>
        <?= print_r($user); ?>
    </pre>

    <!-- Import Guide -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
            <i class="fas fa-info-circle mr-2"></i> Petunjuk Import (User-Friendly dengan Dropdown!)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="font-semibold text-blue-900 mb-2">✨ Fitur Baru:</h4>
                <ul class="list-disc list-inside space-y-2 text-blue-700">
                    <li><strong>Dropdown Otomatis</strong> untuk Hari, Guru, Mapel, Kelas, Semester</li>
                    <li><strong>Tidak perlu mengingat ID</strong> - Pilih dari dropdown!</li>
                    <li><strong>5 Sheet</strong> - Template, Data Guru, Data Mapel, Data Kelas, Petunjuk</li>
                    <li><strong>Data Referensi</strong> lengkap dengan NIP dan Kode Mapel</li>
                    <li>Format: <code>Nama Guru (NIP)</code>, <code>Nama Mapel (Kode)</code></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-blue-900 mb-2">📋 Format Data:</h4>
                <ul class="list-disc list-inside space-y-2 text-blue-700">
                    <li>Format file: Excel (.xlsx atau .xls)</li>
                    <li>Format jam: HH:MM:SS (contoh: 07:00:00)</li>
                    <li>Hari: Pilih dari dropdown (Senin-Jumat)</li>
                    <li>Semester: Pilih dari dropdown (Ganjil/Genap)</li>
                    <li>Tahun Ajaran: Format YYYY/YYYY (contoh: 2023/2024)</li>
                    <li>Sistem mengecek konflik jadwal otomatis</li>
                </ul>
            </div>
        </div>

        <div class="mt-4 flex space-x-3">
            <a href="<?= base_url('admin/jadwal/download-template') ?>"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-download mr-2"></i> Download Template
            </a>
            <a href="<?= base_url('admin/jadwal') ?>"
                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Jadwal
            </a>
        </div>
    </div>

    <!-- Import Form -->
    <div class="border border-gray-200 rounded-lg p-6">
        <form action="<?= base_url('admin/jadwal/process-import') ?>" method="POST" enctype="multipart/form-data" id="importForm">
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
                                    <input id="file-upload" name="file_excel" type="file" class="sr-only" accept=".xlsx,.xls" required>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">.xlsx atau .xls (max 5MB)</p>
                            <p id="fileName" class="text-sm text-gray-900 mt-2 font-semibold"></p>
                        </div>
                    </div>
                </div>

                <!-- Import Options -->
                <div class="border-t pt-4">
                    <h4 class="font-medium text-gray-700 mb-3">Opsi Import</h4>
                    <div class="space-y-3">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="skip_duplicate" class="rounded text-indigo-600" checked>
                            <span class="ml-2 text-gray-700">Lewati jadwal konflik (guru/kelas sudah ada di waktu yang sama)</span>
                        </label>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Template Mudah Digunakan!</h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p><strong>✓ TIDAK PERLU MENGINGAT ID!</strong></p>
                                <p>• Pilih nama guru, mapel, dan kelas dari <strong>dropdown</strong></p>
                                <p>• Data referensi tersedia di sheet terpisah</p>
                                <p>• Sistem otomatis konversi nama ke ID</p>
                                <p>• Support format lama (ID) dan baru (nama) sekaligus</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Perhatian</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>• Sistem akan mengecek konflik jadwal untuk guru dan kelas</p>
                                <p>• Jika opsi "Lewati jadwal konflik" dicentang, data konflik tidak akan diimport</p>
                                <p>• Pastikan format jam benar: HH:MM:SS</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 border-t pt-6">
                    <a href="<?= base_url('admin/jadwal') ?>"
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

        if (file) {
            fileName.textContent = '📄 ' + file.name;
            submitBtn.disabled = false;

            // File size validation (5MB)
            if (file.size > 5242880) {
                alert('Ukuran file terlalu besar! Maksimal 5MB');
                e.target.value = '';
                fileName.textContent = '';
                submitBtn.disabled = true;
                return;
            }

            // File extension validation
            const extension = file.name.split('.').pop().toLowerCase();
            if (!['xlsx', 'xls'].includes(extension)) {
                alert('Format file harus Excel (.xlsx atau .xls)');
                e.target.value = '';
                fileName.textContent = '';
                submitBtn.disabled = true;
                return;
            }

        } else {
            fileName.textContent = '';
            submitBtn.disabled = true;
        }
    });

    // Form submission with confirmation
    document.getElementById('importForm').addEventListener('submit', function(e) {
        const confirmed = confirm('Apakah Anda yakin ingin melakukan import data jadwal?\n\nPastikan:\n1. Format data sudah benar\n2. ID Guru, Mapel, dan Kelas valid\n3. Tidak ada konflik jadwal (atau sudah dicentang "Lewati jadwal konflik")');
        
        if (!confirmed) {
            e.preventDefault();
            return false;
        }

        // Show loading
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
        submitBtn.disabled = true;
    });
</script>
<?= $this->endSection() ?>
