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
            <ul class="list-disc list-inside space-y-1.5 md:space-y-2 text-sm md:text-base text-blue-700">
                <li>Format file harus Excel (.xlsx atau .xls)</li>
                <li>Pastikan format kolom sesuai template</li>
                <li>Kolom wajib: NIS, Nama Lengkap, Jenis Kelamin, Kelas, Tahun Ajaran</li>
                <li>Kolom optional: Email, Username, Password</li>
                <li>Jika username/password tidak diisi, akan digenerate otomatis</li>
                <li>Download template untuk panduan format data</li>
            </ul>

            <div class="mt-4 flex flex-col sm:flex-row gap-3">
                <a href="<?= base_url('admin/siswa/download-template') ?>"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center text-sm">
                    <i class="fas fa-download mr-2"></i> Download Template
                </a>
                <a href="<?= base_url('admin/siswa') ?>"
                   class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 flex items-center justify-center text-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Siswa
                </a>
            </div>
        </div>
    </details>

    <!-- Import Form -->
    <div class="border border-gray-200 rounded-lg p-4 md:p-6">
        <form action="<?= base_url('admin/siswa/process-import') ?>" method="POST" enctype="multipart/form-data" id="importForm">
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
                                    <input id="file-upload" name="file_excel" type="file" class="sr-only" accept=".xlsx,.xls">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">.xlsx atau .xls</p>
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
                            <span class="ml-2 text-sm md:text-base text-gray-700">Lewati data duplikat (berdasarkan NIS)</span>
                        </label>
                        <label class="inline-flex items-start">
                            <input type="checkbox" name="generate_password" class="rounded text-indigo-600 mt-0.5">
                            <span class="ml-2 text-sm md:text-base text-gray-700">Generate password otomatis untuk data tanpa password</span>
                        </label>
                        <label class="inline-flex items-start">
                            <input type="checkbox" name="update_existing" class="rounded text-indigo-600 mt-0.5">
                            <span class="ml-2 text-sm md:text-base text-gray-700">Update data yang sudah ada (berdasarkan NIS)</span>
                        </label>
                    </div>
                </div>

                <!-- Preview Table (hidden initially) -->
                <div id="previewSection" class="hidden">
                    <h4 class="font-medium text-gray-700 mb-3">Preview Data</h4>
                    <div class="overflow-auto max-h-72 md:max-h-96 border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200" id="previewTable">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                                    <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                    <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Gender</th>
                                    <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                                    <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Status</th>
                                </tr>
                            </thead>
                            <tbody id="previewBody" class="bg-white divide-y divide-gray-200">
                                <!-- Preview rows will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-gray-400 mt-1.5 text-right">Geser ke samping untuk melihat semua kolom</p>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t pt-4 md:pt-6">
                    <a href="<?= base_url('admin/siswa') ?>"
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

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    document.getElementById('file-upload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const fileName = document.getElementById('fileName');
        const submitBtn = document.getElementById('submitBtn');
        const previewSection = document.getElementById('previewSection');

        if (file) {
            fileName.textContent = file.name;
            submitBtn.disabled = false;

            const extension = file.name.split('.').pop().toLowerCase();
            if (!['xlsx', 'xls'].includes(extension)) {
                alert('Format file harus Excel (.xlsx atau .xls)');
                submitBtn.disabled = true;
                return;
            }

            previewSection.classList.remove('hidden');

            const previewBody = document.getElementById('previewBody');
            previewBody.innerHTML = '<tr><td colspan="5" class="px-4 py-4 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Membaca data dari file...</td></tr>';

            const reader = new FileReader();
            reader.onload = function(event) {
                try {
                    const data = new Uint8Array(event.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const sheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[sheetName];
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                    const dataRows = jsonData.slice(1).filter(row => row.some(cell => cell !== undefined && cell !== ''));

                    if (dataRows.length === 0) {
                        previewBody.innerHTML = '<tr><td colspan="5" class="px-4 py-4 text-center text-gray-500"><i class="fas fa-exclamation-triangle mr-2"></i> Tidak ada data ditemukan dalam file</td></tr>';
                        return;
                    }

                        let html = '';
                        const nisList = [];

                        dataRows.forEach(function(row, index) {
                            const nis = row[1] || '-';
                            const nama = row[2] || '-';
                            const gender = row[3] || '-';
                            const kelas = row[4] || '-';
                            const genderBadge = gender === 'L'
                                ? '<span class="badge badge-green">L</span>'
                                : (gender === 'P' ? '<span class="badge badge-yellow">P</span>' : escapeHtml(String(gender)));

                            if (nis && nis !== '-') {
                                nisList.push(String(nis));
                            }

                            html += '<tr class="' + (index % 2 === 0 ? 'bg-white' : 'bg-gray-50') + '" data-nis="' + escapeHtml(String(nis)) + '">'
                                + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm text-gray-900">' + escapeHtml(String(nis)) + '</td>'
                                + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm text-gray-900">' + escapeHtml(String(nama)) + '</td>'
                                + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm hidden sm:table-cell">' + genderBadge + '</td>'
                                + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm text-gray-900">' + escapeHtml(String(kelas)) + '</td>'
                                + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm hidden md:table-cell status-cell"><i class="fas fa-spinner fa-spin mr-1"></i> Mengecek...</td>'
                                + '</tr>';
                        });

                        html += '<tr id="summaryRow"><td colspan="5" class="px-4 py-2 text-center text-xs md:text-sm text-gray-500 italic">Total ' + dataRows.length + ' baris data</td></tr>';

                        previewBody.innerHTML = html;

                        // Batch check NIS status via AJAX
                        if (nisList.length > 0) {
                            var queryString = nisList.map(function(n) { return 'nis_list[]=' + encodeURIComponent(n); }).join('&');

                            fetch('<?= base_url('admin/siswa/check-nis-batch') ?>?' + queryString, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(function(res) { return res.json(); })
                            .then(function(result) {
                                if (result.success && result.data && result.data.results) {
                                    var rows = document.querySelectorAll('#previewBody tr[data-nis]');
                                    var newCount = 0;
                                    var activeCount = 0;
                                    var inactiveCount = 0;
                                    var deletedCount = 0;

                                    rows.forEach(function(row) {
                                        var nis = row.getAttribute('data-nis');
                                        var statusCell = row.querySelector('.status-cell');
                                        var statusData = result.data.results[nis];

                                        if (statusData) {
                                            if (statusData.status === 'new') {
                                                statusCell.innerHTML = '<span class="text-green-600 font-medium">Siap Import</span>';
                                                newCount++;
                                            } else if (statusData.status === 'active') {
                                                statusCell.innerHTML = '<span class="text-yellow-600 font-medium">Sudah Aktif</span>';
                                                activeCount++;
                                            } else if (statusData.status === 'inactive') {
                                                statusCell.innerHTML = '<span class="text-orange-600 font-medium">Nonaktif (Akan Diaktifkan)</span>';
                                                inactiveCount++;
                                            } else if (statusData.status === 'deleted') {
                                                statusCell.innerHTML = '<span class="text-red-600 font-medium" title="Data akan dipulihkan darihapus">' + escapeHtml(statusData.label) + '</span>';
                                                deletedCount++;
                                            }
                                        } else {
                                            statusCell.innerHTML = '<span class="text-green-600 font-medium">Siap Import</span>';
                                            newCount++;
                                        }
                                    });

                                    var summaryText = 'Total ' + dataRows.length + ' baris data';
                                    var parts = [];
                                    if (newCount > 0) parts.push(newCount + ' Baru');
                                    if (deletedCount > 0) parts.push(deletedCount + ' Dihapus (Akan Dipulihkan)');
                                    if (activeCount > 0) parts.push(activeCount + ' Sudah Aktif');
                                    if (inactiveCount > 0) parts.push(inactiveCount + ' Nonaktif');
                                    if (parts.length > 0) {
                                        summaryText += ' &mdash; ' + parts.join(', ');
                                    }

                                    document.querySelector('#summaryRow td').innerHTML = summaryText;

                                    // Disable submit only if ALL rows are active (no new/deleted/inactive)
                                    if (activeCount > 0 && newCount === 0 && inactiveCount === 0 && deletedCount === 0) {
                                        document.getElementById('submitBtn').disabled = true;
                                        document.getElementById('submitBtn').title = 'Tidak ada data baru untuk diimport';
                                    }
                                }
                            })
                            .catch(function() {
                                // Fallback: mark all as Siap Import on error
                                var rows = document.querySelectorAll('#previewBody tr[data-nis]');
                                rows.forEach(function(row) {
                                    var statusCell = row.querySelector('.status-cell');
                                    statusCell.innerHTML = '<span class="text-green-600 font-medium">Siap Import</span>';
                                });
                            });
                        } else {
                            var rows = document.querySelectorAll('#previewBody tr[data-nis]');
                            rows.forEach(function(row) {
                                var statusCell = row.querySelector('.status-cell');
                                statusCell.innerHTML = '<span class="text-green-600 font-medium">Siap Import</span>';
                            });
                        }
                } catch (err) {
                    previewBody.innerHTML = '<tr><td colspan="5" class="px-4 py-4 text-center text-red-500"><i class="fas fa-exclamation-circle mr-2"></i> Gagal membaca file: ' + escapeHtml(err.message) + '</td></tr>';
                }
            };
            reader.readAsArrayBuffer(file);

        } else {
            fileName.textContent = '';
            submitBtn.disabled = true;
            previewSection.classList.add('hidden');
        }
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    document.getElementById('importForm').addEventListener('submit', function(e) {
        if (!confirm('Apakah Anda yakin ingin melakukan import data? Pastikan data sudah benar.')) {
            e.preventDefault();
        }
    });
</script>
<?= $this->endSection() ?>
