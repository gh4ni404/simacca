<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-4 md:p-6">
    <!-- Header -->
    <div class="mb-4 md:mb-6">
        <h2 class="text-xl md:text-2xl font-bold text-gray-800"><?= $pageTitle ?></h2>
        <p class="text-sm md:text-base text-gray-600"><?= $pageDescription ?></p>
    </div>

    <!-- Import Guide (Collapsible) -->
    <details class="bg-blue-50 border border-blue-200 rounded-lg mb-4 md:mb-6" open>
        <summary class="p-4 md:p-6 cursor-pointer text-blue-800 font-semibold flex items-center select-none">
            <i class="fas fa-info-circle mr-2"></i> Petunjuk Import
            <i class="fas fa-chevron-down ml-auto text-sm transition-transform details-open:rotate-180"></i>
        </summary>
        <div class="px-4 pb-4 md:px-6 md:pb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2 text-sm">Fitur Dropdown:</h4>
                    <ul class="list-disc list-inside space-y-1.5 text-sm text-blue-700">
                        <li><strong>Dropdown Otomatis</strong> untuk Hari, Guru, Mapel, Kelas, Semester</li>
                        <li><strong>Tidak perlu mengingat ID</strong> - Pilih dari dropdown!</li>
                        <li><strong>5 Sheet</strong> - Template, Data Guru, Data Mapel, Data Kelas, Petunjuk</li>
                        <li><strong>Data Referensi</strong> lengkap dengan NIP dan Kode Mapel</li>
                        <li>Format: <code class="bg-blue-100 px-1 rounded">Nama Guru (NIP)</code>, <code class="bg-blue-100 px-1 rounded">Nama Mapel (Kode)</code></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2 text-sm">Format Data:</h4>
                    <ul class="list-disc list-inside space-y-1.5 text-sm text-blue-700">
                        <li>Format file: Excel (.xlsx atau .xls)</li>
                        <li>Format jam: HH:MM:SS (contoh: 07:00:00)</li>
                        <li>Hari: Pilih dari dropdown (Senin-Jumat)</li>
                        <li>Semester: Pilih dari dropdown (Ganjil/Genap)</li>
                        <li>Tahun Ajaran: Format YYYY/YYYY (contoh: 2023/2024)</li>
                        <li>Sistem mengecek konflik jadwal otomatis</li>
                    </ul>
                </div>
            </div>

            <div class="mt-4 flex flex-col sm:flex-row gap-3">
                <a href="<?= base_url('admin/jadwal/download-template') ?>"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center text-sm">
                    <i class="fas fa-download mr-2"></i> Download Template
                </a>
                <a href="<?= base_url('admin/jadwal') ?>"
                   class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 flex items-center justify-center text-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Jadwal
                </a>
            </div>
        </div>
    </details>

    <!-- Import Form -->
    <div class="border border-gray-200 rounded-lg p-4 md:p-6">
        <form action="<?= base_url('admin/jadwal/process-import') ?>" method="POST" enctype="multipart/form-data" id="importForm">
            <?= csrf_field() ?>

            <div class="space-y-4">
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel *</label>
                    <div class="flex justify-center px-4 md:px-6 pt-4 md:pt-5 pb-4 md:pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-500 transition-colors">
                        <div class="space-y-1 text-center">
                            <i class="fas fa-file-excel text-3xl md:text-4xl text-green-500 mx-auto"></i>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                    <span>Upload file Excel</span>
                                    <input id="file-upload" name="file_excel" type="file" class="sr-only" accept=".xlsx,.xls" required>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">.xlsx atau .xls (maks 5MB)</p>
                            <p id="fileName" class="text-sm text-gray-900 mt-2 font-medium break-all"></p>
                        </div>
                    </div>
                </div>

                <!-- Import Options -->
                <div class="border-t pt-4">
                    <h4 class="font-medium text-gray-700 mb-3">Opsi Import</h4>
                    <div class="space-y-3">
                        <label class="inline-flex items-start">
                            <input type="checkbox" name="skip_duplicate" class="rounded text-indigo-600 mt-0.5" checked>
                            <span class="ml-2 text-sm md:text-base text-gray-700">Lewati jadwal konflik (guru/kelas sudah ada di waktu yang sama)</span>
                        </label>
                    </div>
                </div>

                <!-- Info & Warning boxes consolidated -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 md:p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">Template Mudah Digunakan!</h3>
                                <div class="mt-1.5 text-xs md:text-sm text-green-700 space-y-0.5">
                                    <p>Tidak perlu mengingat ID - pilih dari dropdown</p>
                                    <p>Data referensi tersedia di sheet terpisah</p>
                                    <p>Sistem otomatis konversi nama ke ID</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 md:p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Perhatian</h3>
                                <div class="mt-1.5 text-xs md:text-sm text-yellow-700 space-y-0.5">
                                    <p>Sistem mengecek konflik jadwal otomatis</p>
                                    <p>Jika "Lewati konflik" dicentang, data konflik tidak diimport</p>
                                    <p>Pastikan format jam benar: HH:MM:SS</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t pt-4 md:pt-6">
                    <a href="<?= base_url('admin/jadwal') ?>"
                       class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-center text-sm font-medium">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center justify-center text-sm font-medium"
                            id="submitBtn" disabled>
                        <i class="fas fa-upload mr-2"></i> Proses Import
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('file-upload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const fileName = document.getElementById('fileName');
        const submitBtn = document.getElementById('submitBtn');

        if (file) {
            fileName.textContent = file.name;
            submitBtn.disabled = false;

            if (file.size > 5242880) {
                alert('Ukuran file terlalu besar! Maksimal 5MB');
                e.target.value = '';
                fileName.textContent = '';
                submitBtn.disabled = true;
                return;
            }

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

    document.getElementById('importForm').addEventListener('submit', function(e) {
        const confirmed = confirm('Apakah Anda yakin ingin melakukan import data jadwal?\n\nPastikan:\n1. Format data sudah benar\n2. ID Guru, Mapel, dan Kelas valid\n3. Tidak ada konflik jadwal (atau sudah dicentang "Lewati jadwal konflik")');

        if (!confirmed) {
            e.preventDefault();
            return false;
        }

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
        submitBtn.disabled = true;
    });
</script>
<?= $this->endSection() ?>
