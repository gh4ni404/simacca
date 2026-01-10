<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Input Absensi</h1>
        <p class="text-gray-600">Isi absensi siswa untuk pertemuan pembelajaran</p>
    </div>

    <!-- Flash Message -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="<?= base_url('guru/absensi/simpan'); ?>" method="post" id="absensiForm">
            <?= csrf_field(); ?>

            <!-- Jadwal Selection -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pilih Jadwal</h3>

                <?php if ($jadwal): ?>
                    <!-- Selected Jadwal -->
                    <div class="border border-green-200 bg-green-50 rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-medium text-gray-900"><?= $jadwal['nama_mapel']; ?></h4>
                                <div class="text-sm text-gray-600 mt-1">
                                    <i class="fas fa-calendar-alt mr-2"></i> <?= $jadwal['hari']; ?>
                                    <i class="fas fa-clock ml-4 mr-2"></i> <?= date('H:i', strtotime($jadwal['jam_mulai'])); ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])); ?>
                                    <i class="fas fa-school ml-4 mr-2"></i> <?= $jadwal['nama_kelas']; ?>
                                </div>
                            </div>
                            <a href="<?= base_url('guru/absensi/tambah'); ?>" class="text-sm text-blue-500 hover:text-blue-700">
                                Ganti Jadwal
                            </a>
                        </div>
                    </div>

                    <input type="hidden" name="jadwal_mengajar_id" value="<?= $jadwal['id']; ?>">
                <?php else: ?>
                    <!-- Jadwal Selection Form -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="hari" class="block text-sm font-medium text-gray-700 mb-2">
                                Hari
                            </label>
                            <select id="hari" name="hari" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Hari</option>
                                <?php foreach ($hariList as $key => $value): ?>
                                    <option value="<?= $key; ?>"><?= $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="jadwal_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Jadwal
                            </label>
                            <select id="jadwal_id" name="jadwal_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Jadwal</option>
                            </select>
                        </div>
                        <div>
                            <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Absensi
                            </label>
                            <input type="date"
                                id="tanggal"
                                name="tanggal"
                                value="<?= $tanggal; ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                min="<?= date('Y-m-d', strtotime('-7 days')); ?>"
                                max="<?= date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>
                    </div>

                    <!-- Jadwal Hari Ini -->
                    <?php if (!empty($jadwalHariIni)): ?>
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 mb-2">Jadwal hari ini (<?= date('l, d F Y'); ?>):</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <?php foreach ($jadwalHariIni as $jadwalItem): ?>
                                    <a href="<?= base_url('guru/absensi/tambah?jadwal_id=' . $jadwalItem['id'] . '&tanggal=' . $tanggal); ?>"
                                        class="border border-gray-300 rounded-lg p-3 hover:bg-gray-50 transition">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <h4 class="font-medium text-gray-900"><?= $jadwalItem['nama_mapel']; ?></h4>
                                                <p class="text-sm text-gray-600">
                                                    <?= date('H:i', strtotime($jadwalItem['jam_mulai'])); ?> - <?= date('H:i', strtotime($jadwalItem['jam_selesai'])); ?>
                                                    | <?= $jadwalItem['nama_kelas']; ?>
                                                </p>
                                            </div>
                                            <i class="fas fa-arrow-right text-blue-500"></i>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if ($jadwal): ?>
                <!-- Absensi Details -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Data Absensi</h3>
                        <div class="text-sm text-gray-600">
                            Tanggal: <span class="font-medium"><?= date('d/m/Y', strtotime($tanggal)); ?></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="pertemuan_ke" class="block text-sm font-medium text-gray-700 mb-2">
                                Pertemuan Ke-*
                            </label>
                            <input type="number"
                                id="pertemuan_ke"
                                name="pertemuan_ke"
                                value="<?= $pertemuanKe; ?>"
                                min="1"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div>
                            <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Absensi*
                            </label>
                            <input type="date"
                                id="tanggal"
                                name="tanggal"
                                value="<?= $tanggal; ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="materi_pembelajaran" class="block text-sm font-medium text-gray-700 mb-2">
                            Materi Pembelajaran
                        </label>
                        <textarea id="materi_pembelajaran"
                            name="materi_pembelajaran"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Isi materi yang diajarkan pada pertemuan ini..."></textarea>
                    </div>

                    <!-- Approved Izin Info -->
                    <?php if (!empty($approvedIzin)): ?>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">
                                <i class="fas fa-info-circle mr-2"></i> Informasi Izin yang Disetujui
                            </h4>
                            <p class="text-sm text-blue-700 mb-2">Siswa berikut telah mengajukan izin dan disetujui:</p>
                            <ul class="text-sm text-blue-600 list-disc list-inside">
                                <?php foreach ($approvedIzin as $izin): ?>
                                    <li><?= $izin['nama_lengkap']; ?> (<?= $izin['nis']; ?>) - <?= ucfirst($izin['jenis_izin']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Students Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="siswaTableBody">
                                <!-- Will be populated by AJAX -->
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                                        <p>Memuat data siswa...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <a href="<?= base_url('guru/absensi'); ?>"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    <div class="flex space-x-3">
                        <button type="submit"
                            name="next_action"
                            value="list"
                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition flex items-center">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                        <button type="submit"
                            name="next_action"
                            value="jurnal"
                            class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition flex items-center">
                            <i class="fas fa-book mr-2"></i> Simpan & Buat Jurnal
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if ($jadwal): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kelasId = '<?= $jadwal["kelas_id"]; ?>';
            const tanggal = document.getElementById('tanggal').value;

            // Load siswa data
            loadSiswaData(kelasId, tanggal);

            // Update siswa data when tanggal changes
            document.getElementById('tanggal').addEventListener('change', function() {
                loadSiswaData(kelasId, this.value);
            });
        });

        function loadSiswaData(kelasId, tanggal) {
            const tableBody = document.getElementById('siswaTableBody');

            tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                <p>Memuat data siswa...</p>
            </td>
        </tr>
    `;

            fetch(`<?= base_url('guru/absensi/getSiswaByKelas'); ?>?kelas_id=${kelasId}&tanggal=${tanggal}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderSiswaTable(data.siswa, data.approvedIzin, data.statusOptions);
                    } else {
                        tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-red-500">
                            <i class="fas fa-exclamation-triangle text-xl mb-2"></i>
                            <p>${data.message || 'Gagal memuat data siswa'}</p>
                        </td>
                    </tr>
                `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-red-500">
                        <i class="fas fa-exclamation-triangle text-xl mb-2"></i>
                        <p>Terjadi kesalahan saat memuat data</p>
                    </td>
                </tr>
            `;
                });
        }

        function renderSiswaTable(siswaList, approvedIzin, statusOptions) {
            const tableBody = document.getElementById('siswaTableBody');

            if (!siswaList || siswaList.length === 0) {
                tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                    <i class="fas fa-users-slash text-xl mb-2"></i>
                    <p>Tidak ada siswa di kelas ini</p>
                </td>
            </tr>
        `;
                return;
            }

            let html = '';
            let approvedIzinMap = {};

            // Create map of approved izin
            approvedIzin.forEach(izin => {
                approvedIzinMap[izin.siswa_id] = {
                    jenis: izin.jenis_izin,
                    alasan: izin.alasan
                };
            });

            siswaList.forEach((siswa, index) => {
                const isApprovedIzin = approvedIzinMap[siswa.id];
                const defaultStatus = isApprovedIzin ? 'izin' : 'hadir';
                const defaultKeterangan = isApprovedIzin ? `Izin ${isApprovedIzin.jenis}: ${isApprovedIzin.alasan}` : '';

                html += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">${index + 1}</td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">${siswa.nis || '-'}</td>
                <td class="px-4 py-4">
                    <div class="text-sm font-medium text-gray-900">${siswa.nama_lengkap}</div>
                    ${isApprovedIzin ? `
                    <div class="text-xs text-blue-600 mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Izin disetujui
                    </div>
                    ` : ''}
                </td>
                <td class="px-4 py-4">
                    <select name="siswa[${siswa.id}][status]" 
                            class="w-full px-3 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 status-select"
                            data-siswa-id="${siswa.id}"
                            onchange="updateStatusColor(this)">
                        ${Object.entries(statusOptions).map(([value, option]) => `
                            <option value="${value}" ${value === defaultStatus ? 'selected' : ''}>
                                ${option.label}
                            </option>
                        `).join('')}
                    </select>
                </td>
                <td class="px-4 py-4">
                    <input type="text" 
                           name="siswa[${siswa.id}][keterangan]" 
                           value="${defaultKeterangan}"
                           placeholder="Opsional"
                           class="w-full px-3 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </td>
            </tr>
        `;
            });

            tableBody.innerHTML = html;

            // Apply initial status colors
            document.querySelectorAll('.status-select').forEach(select => {
                updateStatusColor(select);
            });
        }

        function updateStatusColor(select) {
            const status = select.value;
            const colors = {
                'hadir': 'border-green-500 bg-green-50',
                'izin': 'border-blue-500 bg-blue-50',
                'sakit': 'border-yellow-500 bg-yellow-50',
                'alpa': 'border-red-500 bg-red-50'
            };

            // Remove all status classes
            select.classList.remove('border-green-500', 'bg-green-50',
                'border-blue-500', 'bg-blue-50',
                'border-yellow-500', 'bg-yellow-50',
                'border-red-500', 'bg-red-50');

            // Add new status classes
            if (colors[status]) {
                const [borderClass, bgClass] = colors[status].split(' ');
                select.classList.add(borderClass, bgClass);
            }
        }
    </script>
<?php endif; ?>
<script>
    // Handle jadwal selection
    document.getElementById('hari').addEventListener('change', function() {
        const hari = this.value;
        const jadwalSelect = document.getElementById('jadwal_id');
        console.log(hari);

        if (!hari) {
            jadwalSelect.innerHTML = '<option value="">Pilih Hari terlebih dahulu</option>';
            return;
        }

        jadwalSelect.innerHTML = '<option value="">Memuat jadwal...</option>';

        fetch(`<?= base_url('guru/absensi/getJadwalByHari'); ?>?hari=${hari}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.jadwal.length > 0) {
                    let options = '<option value="">Pilih Jadwal</option>';
                    data.jadwal.forEach(jadwal => {
                        const waktu = `${jadwal.jam_mulai.substr(0, 5)} - ${jadwal.jam_selesai.substr(0, 5)}`;
                        options += `<option value="${jadwal.id}">${jadwal.nama_mapel} - ${jadwal.nama_kelas} (${waktu})</option>`;
                    });
                    jadwalSelect.innerHTML = options;

                    // auto-redirect when a jadwal is selected
                    jadwalSelect.addEventListener('change', function() {
                        const selected = this.value;
                        if (!selected) return;
                        const tanggalVal = document.getElementById('tanggal') ? document.getElementById('tanggal').value : '';
                        const targetUrl = '<?= base_url('guru/absensi/tambah'); ?>?jadwal_id=' + encodeURIComponent(selected) + (tanggalVal ? '&tanggal=' + encodeURIComponent(tanggalVal) : '');
                        window.location.href = targetUrl;
                    });
                } else {
                    jadwalSelect.innerHTML = '<option value="">Tidak ada jadwal untuk hari ini</option>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                jadwalSelect.innerHTML = '<option value="">Error loading data</option>';
            });
    });
</script>
<?= $this->endSection() ?>