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
                    <h4 class="font-semibold text-blue-900 mb-2 text-sm">Format Data Excel yang Mudah & Ramah:</h4>
                    <ul class="list-disc list-inside space-y-1.5 text-sm text-blue-700">
                        <li>Format file harus Excel (.xlsx atau .xls)</li>
                        <li>Kolom Wajib: <b>NIP</b> dan <b>Nama Lengkap</b></li>
                        <li><b>Role</b>: Tulis nama role yang ramah (dipisah koma jika multi-role), contoh: <i>Guru Mapel, Wali Kelas</i> atau <i>Ketua Jurusan</i></li>
                        <li><b>Mata Pelajaran</b>: Tulis nama mapel (contoh: <i>Matematika</i>, <i>Bahasa Indonesia</i>) - tidak perlu hapal ID!</li>
                        <li><b>Nama Kelas Wali</b>: Tulis nama kelas (contoh: <i>X RPL 1</i>, <i>XI DKV 2</i>) - otomatis dicocokkan pada Tahun Ajaran Aktif!</li>
                        <li><b>Jurusan Ketua</b>: Tulis singkatan jurusan (contoh: <i>DKV</i>, <i>MPLB</i>, <i>AT</i>) - wajib untuk Ketua Jurusan</li>
                        <li>Username & Password opsional: jika kosong, otomatis dibuat dari NIP.</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2 text-sm">Pilihan Role yang Didukung:</h4>
                    <ul class="list-disc list-inside space-y-1.5 text-sm text-blue-700">
                        <li><b>Guru Mapel</b> / <b>Guru</b></li>
                        <li><b>Wali Kelas</b></li>
                        <li><b>Ketua Jurusan</b> / <b>Kajur</b></li>
                        <li><b>Kepala Sekolah</b> / <b>Kepsek</b></li>
                        <li><b>Tendik</b> / <b>Staf</b> / <b>TU</b></li>
                        <li><b>Wakakur</b> / <b>Wakil Kepala Kurikulum</b></li>
                    </ul>
                </div>
            </div>

            <div class="mt-4 flex flex-col sm:flex-row gap-3">
                <a href="<?= base_url('admin/guru/download-template') ?>"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center justify-center text-sm font-semibold shadow-sm">
                    <i class="fas fa-file-excel mr-2"></i> Download Template (.xlsx)
                </a>
                <a href="<?= base_url('admin/guru') ?>"
                   class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 flex items-center justify-center text-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Guru
                </a>
            </div>
        </div>
    </details>

    <!-- Import Form -->
    <div class="border border-gray-200 rounded-lg p-4 md:p-6">
        <form action="<?= base_url('admin/guru/process-import') ?>" method="POST" enctype="multipart/form-data" id="importForm">
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
                            <span class="ml-2 text-sm md:text-base text-gray-700">Lewati data duplikat (berdasarkan NIP)</span>
                        </label>
                        <label class="inline-flex items-start">
                            <input type="checkbox" name="generate_password" class="rounded text-indigo-600 mt-0.5" checked>
                            <span class="ml-2 text-sm md:text-base text-gray-700">Generate password/username otomatis jika tidak diisi</span>
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
                                    <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">NIP</th>
                                    <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Lengkap</th>
                                    <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Mata Pelajaran</th>
                                    <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                    <th class="px-3 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Kelas / Jurusan</th>
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
                    <a href="<?= base_url('admin/guru') ?>"
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

                    function roleBadge(roleStr) {
                        if (!roleStr || roleStr === '-') return '<span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-700">-</span>';
                        var roles = String(roleStr).split(',').map(function(r){ return r.trim().toLowerCase(); });
                        var badges = roles.map(function(r) {
                            if (r === 'wali_kelas') return '<span class="px-1.5 py-0.5 text-xs rounded bg-amber-100 text-amber-800 font-semibold mr-1">Wali Kelas</span>';
                            if (r === 'wakakur') return '<span class="px-1.5 py-0.5 text-xs rounded bg-red-100 text-red-800 font-semibold mr-1">Wakakur</span>';
                            if (r === 'ketua_jurusan') return '<span class="px-1.5 py-0.5 text-xs rounded bg-purple-100 text-purple-800 font-semibold mr-1">Ketua Jurusan</span>';
                            if (r === 'kepala_sekolah') return '<span class="px-1.5 py-0.5 text-xs rounded bg-blue-100 text-blue-800 font-semibold mr-1">Kepala Sekolah</span>';
                            if (r === 'tendik') return '<span class="px-1.5 py-0.5 text-xs rounded bg-teal-100 text-teal-800 font-semibold mr-1">Tendik</span>';
                            return '<span class="px-1.5 py-0.5 text-xs rounded bg-indigo-100 text-indigo-800 font-semibold mr-1">Guru Mapel</span>';
                        });
                        return badges.join('');
                    }

                    let html = '';
                    dataRows.forEach(function(row, index) {
                        const nip = row[0] || '-';
                        const nama = row[1] || '-';
                        const role = row[6] || '-';
                        const mapel = row[7] || '-';
                        const kelas = row[8] || '';
                        const jurusan = row[9] || '';
                        const extraDetail = (kelas ? 'Kelas: ' + kelas : '') + (jurusan ? (kelas ? ' | ' : '') + 'Jurusan: ' + jurusan : '');

                        html += '<tr class="' + (index % 2 === 0 ? 'bg-white' : 'bg-gray-50') + '">'
                            + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm font-mono text-gray-900">' + escapeHtml(String(nip)) + '</td>'
                            + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm text-gray-900 font-medium">' + escapeHtml(String(nama)) + '</td>'
                            + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm text-gray-700 hidden sm:table-cell">' + escapeHtml(String(mapel)) + '</td>'
                            + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm">' + roleBadge(role) + '</td>'
                            + '<td class="px-3 md:px-4 py-2 text-xs md:text-sm text-gray-600 hidden md:table-cell">' + (extraDetail ? escapeHtml(extraDetail) : '-') + '</td>'
                            + '</tr>';
                    });

                    html += '<tr><td colspan="5" class="px-4 py-2 text-center text-xs md:text-sm text-gray-500 italic">Total ' + dataRows.length + ' baris data akan diimport</td></tr>';

                    previewBody.innerHTML = html;
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
