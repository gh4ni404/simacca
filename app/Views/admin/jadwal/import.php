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
                        <li>Tahun ajaran otomatis dari pengaturan sistem</li>
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

                <!-- Preview Section -->
                <div id="previewSection" class="hidden">
                    <div class="border-t pt-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-700">
                                <i class="fas fa-eye mr-1"></i> Preview Data
                            </h4>
                            <span id="previewCount" class="text-xs text-gray-500"></span>
                        </div>

                        <div class="overflow-auto max-h-72 md:max-h-96 border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200" id="previewTable">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                        <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hari</th>
                                        <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jam Mulai</th>
                                        <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Jam Selesai</th>
                                        <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Guru</th>
                                        <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Mapel</th>
                                        <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                                        <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="previewBody" class="bg-white divide-y divide-gray-200">
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary -->
                        <div id="previewSummary" class="mt-3 hidden">
                            <div class="flex flex-wrap gap-3 text-xs">
                                <span class="flex items-center">
                                    <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span>
                                    <span id="countValid" class="text-gray-600">0 Siap Import</span>
                                </span>
                                <span class="flex items-center">
                                    <span class="w-2 h-2 rounded-full bg-yellow-500 mr-1"></span>
                                    <span id="countWarning" class="text-gray-600">0 Perlu Diperiksa</span>
                                </span>
                                <span class="flex items-center">
                                    <span class="w-2 h-2 rounded-full bg-red-500 mr-1"></span>
                                    <span id="countError" class="text-gray-600">0 Error</span>
                                </span>
                            </div>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    var CSRF_NAME = '<?= csrf_token() ?>';
    var CSRF_HASH = '<?= csrf_hash() ?>';

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    function formatExcelTime(value) {
        if (value === null || value === undefined || value === '') return '';
        if (typeof value === 'string') {
            if (/^\d{1,2}:\d{2}(:\d{2})?$/.test(value.trim())) return value.trim();
            var num = parseFloat(value);
            if (!isNaN(num) && num >= 0 && num < 1) {
                value = num;
            } else {
                return value.trim();
            }
        }
        if (typeof value === 'number' && value >= 0 && value < 1) {
            var totalSeconds = Math.round(value * 86400);
            var hours = Math.floor(totalSeconds / 3600);
            var minutes = Math.floor((totalSeconds % 3600) / 60);
            var seconds = totalSeconds % 60;
            return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        }
        return String(value).trim();
    }

    function formatExcelDate(value) {
        if (value === null || value === undefined || value === '') return '';
        if (typeof value === 'string') {
            if (/^\d{4}\/\d{4}$/.test(value.trim())) return value.trim();
            return value.trim();
        }
        if (typeof value === 'number' && value > 40000) {
            var date = new Date((value - 25569) * 86400 * 1000);
            var year = date.getUTCFullYear();
            return year + '/' + (year + 1);
        }
        return String(value).trim();
    }

    function getHariBadge(hari) {
        var colors = {
            'Senin': 'bg-red-100 text-red-700',
            'Selasa': 'bg-yellow-100 text-yellow-700',
            'Rabu': 'bg-green-100 text-green-700',
            'Kamis': 'bg-blue-100 text-blue-700',
            'Jumat': 'bg-purple-100 text-purple-700'
        };
        var colorClass = colors[hari] || 'bg-gray-100 text-gray-700';
        return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ' + colorClass + '">' + escapeHtml(hari) + '</span>';
    }

    function getStatusHtml(status, message) {
        if (status === 'valid') {
            return '<span class="text-green-600 font-medium"><i class="fas fa-check-circle mr-1"></i>Siap Import</span>';
        } else if (status === 'warning') {
            return '<span class="text-yellow-600 font-medium" title="' + escapeHtml(message) + '"><i class="fas fa-exclamation-triangle mr-1"></i>' + escapeHtml(message) + '</span>';
        } else {
            return '<span class="text-red-600 font-medium" title="' + escapeHtml(message) + '"><i class="fas fa-times-circle mr-1"></i>' + escapeHtml(message) + '</span>';
        }
    }

    document.getElementById('file-upload').addEventListener('change', function(e) {
        var file = e.target.files[0];
        var fileName = document.getElementById('fileName');
        var submitBtn = document.getElementById('submitBtn');
        var previewSection = document.getElementById('previewSection');
        var previewBody = document.getElementById('previewBody');
        var previewCount = document.getElementById('previewCount');
        var previewSummary = document.getElementById('previewSummary');

        if (!file) {
            fileName.textContent = '';
            submitBtn.disabled = true;
            previewSection.classList.add('hidden');
            return;
        }

        fileName.textContent = file.name;

        if (file.size > 5242880) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: 'Ukuran file maksimal 5MB!',
                confirmButtonColor: '#dc2626'
            });
            e.target.value = '';
            fileName.textContent = '';
            submitBtn.disabled = true;
            previewSection.classList.add('hidden');
            return;
        }

        var extension = file.name.split('.').pop().toLowerCase();
        if (!['xlsx', 'xls'].includes(extension)) {
            Swal.fire({
                icon: 'error',
                title: 'Format File Salah',
                text: 'Format file harus Excel (.xlsx atau .xls)!',
                confirmButtonColor: '#dc2626'
            });
            e.target.value = '';
            fileName.textContent = '';
            submitBtn.disabled = true;
            previewSection.classList.add('hidden');
            return;
        }

        previewSection.classList.remove('hidden');
        submitBtn.disabled = false;
        previewBody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center"><i class="fas fa-spinner fa-spin text-indigo-500 mr-2"></i> Membaca file Excel...</td></tr>';
        previewCount.textContent = '';
        previewSummary.classList.add('hidden');

        var reader = new FileReader();
        reader.onload = function(event) {
            try {
                var data = new Uint8Array(event.target.result);
                var workbook = XLSX.read(data, { type: 'array' });
                var sheetName = workbook.SheetNames[0];
                var worksheet = workbook.Sheets[sheetName];
                var jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                var dataRows = jsonData.slice(1).filter(function(row) {
                    return row && row.some(function(cell) { return cell !== undefined && cell !== ''; });
                });

                if (dataRows.length === 0) {
                    previewBody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500"><i class="fas fa-info-circle mr-1"></i> Tidak ada data ditemukan di file</td></tr>';
                    return;
                }

                var html = '';
                var validationData = [];

                dataRows.forEach(function(row, index) {
                    var hari = (row[0] || '').toString().trim();
                    var jamMulai = formatExcelTime(row[1]);
                    var jamSelesai = formatExcelTime(row[2]);
                    var guru = (row[3] || '').toString().trim();
                    var mapel = (row[4] || '').toString().trim();
                    var kelas = (row[5] || '').toString().trim();
                    var semester = (row[6] || '').toString().trim();

                    validationData.push({
                        row: index,
                        hari: hari,
                        jam_mulai: jamMulai,
                        jam_selesai: jamSelesai,
                        guru: guru,
                        mapel: mapel,
                        kelas: kelas,
                        semester: semester
                    });

                    var rowClass = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                    html += '<tr class="' + rowClass + '" data-row="' + index + '">'
                        + '<td class="px-3 md:px-4 py-2 text-xs text-gray-500">' + (index + 1) + '</td>'
                        + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm">' + (hari ? getHariBadge(hari) : '<span class="text-red-500">-</span>') + '</td>'
                        + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm text-gray-900">' + (jamMulai ? escapeHtml(jamMulai) : '<span class="text-red-500">-</span>') + '</td>'
                        + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm text-gray-900 hidden sm:table-cell">' + (jamSelesai ? escapeHtml(jamSelesai) : '<span class="text-red-500">-</span>') + '</td>'
                        + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm text-gray-900">' + (guru ? escapeHtml(guru) : '<span class="text-red-500">-</span>') + '</td>'
                        + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm text-gray-900 hidden md:table-cell">' + (mapel ? escapeHtml(mapel) : '<span class="text-red-500">-</span>') + '</td>'
                        + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm text-gray-900">' + (kelas ? escapeHtml(kelas) : '<span class="text-red-500">-</span>') + '</td>'
                        + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm hidden lg:table-cell status-cell"><i class="fas fa-spinner fa-spin text-gray-400 mr-1"></i> Mengecek...</td>'
                        + '</tr>';
                });

                previewBody.innerHTML = html;
                previewCount.textContent = dataRows.length + ' baris data';

                validateJadwalBatch(validationData);

            } catch (err) {
                previewBody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-red-500"><i class="fas fa-exclamation-circle mr-1"></i> Gagal membaca file: ' + escapeHtml(err.message) + '</td></tr>';
            }
        };

        reader.readAsArrayBuffer(file);
    });

    function validateJadwalBatch(data) {
        var formData = new FormData();
        formData.append(CSRF_NAME, CSRF_HASH);
        formData.append('data', JSON.stringify(data));

        fetch('<?= base_url('admin/jadwal/check-batch') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(result) {
            if (result.success && result.data && result.data.results) {
                var results = result.data.results;
                var countValid = 0;
                var countWarning = 0;
                var countError = 0;

                results.forEach(function(item) {
                    var row = document.querySelector('#previewBody tr[data-row="' + item.row + '"]');
                    if (!row) return;

                    var statusCell = row.querySelector('.status-cell');
                    if (statusCell) {
                        statusCell.innerHTML = getStatusHtml(item.status, item.message || '');
                    }

                    if (item.status === 'valid') countValid++;
                    else if (item.status === 'warning') countWarning++;
                    else countError++;
                });

                document.getElementById('countValid').textContent = countValid + ' Siap Import';
                document.getElementById('countWarning').textContent = countWarning + ' Perlu Diperiksa';
                document.getElementById('countError').textContent = countError + ' Error';
                document.getElementById('previewSummary').classList.remove('hidden');
            }
        })
        .catch(function() {
            var rows = document.querySelectorAll('#previewBody tr[data-row]');
            rows.forEach(function(row) {
                var statusCell = row.querySelector('.status-cell');
                if (statusCell) {
                    statusCell.innerHTML = '<span class="text-gray-400 text-xs"><i class="fas fa-question-circle mr-1"></i>Tidak terdeteksi</span>';
                }
            });
        });
    }

    document.getElementById('importForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Import Data Jadwal?',
            html: `
                <p style="text-align:left;margin-bottom:12px">Apakah Anda yakin ingin melakukan import data jadwal?</p>
                <div style="text-align:left;background:#f9fafb;border-radius:8px;padding:12px;font-size:13px;color:#374151">
                    <strong style="color:#6b7280">Pastikan:</strong>
                    <ul style="margin:6px 0 0 16px;padding:0;list-style:disc">
                        <li>Format data sudah benar</li>
                        <li>Guru, Mapel, dan Kelas valid</li>
                        <li>Tidak ada konflik jadwal (atau sudah centang "Lewati jadwal konflik")</li>
                    </ul>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Import',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                var submitBtn = document.getElementById('submitBtn');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
                submitBtn.disabled = true;
                document.getElementById('importForm').submit();
            }
        });
    });
</script>
<?= $this->endSection() ?>
