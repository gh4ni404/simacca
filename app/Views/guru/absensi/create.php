<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                <i class="fas fa-user-check text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Input Absensi Siswa
                    </span>
                </h1>
                <p class="text-gray-600 flex items-center mt-1">
                    <i class="fas fa-info-circle mr-2 text-blue-500 text-sm"></i>
                    <span class="text-sm">Catat kehadiran siswa untuk pertemuan pembelajaran</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Main Form Container -->
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <form action="<?= base_url('guru/absensi/simpan'); ?>" method="post" id="absensiForm">
            <?= csrf_field(); ?>

            <!-- Jadwal Selection Section -->
            <div class="mb-8">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg mr-3">
                        <i class="fas fa-calendar-check text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Pilih Jadwal Mengajar</h3>
                </div>

                <?php if ($jadwal): ?>
                    <!-- Selected Jadwal Card -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-300 rounded-xl p-6 mb-6 shadow-md">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-3">
                                    <div class="p-2 bg-green-500 rounded-lg mr-3">
                                        <i class="fas fa-check-circle text-white"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-semibold text-green-700 uppercase tracking-wide">Jadwal Dipilih</span>
                                        <h4 class="text-xl font-bold text-gray-900"><?= $jadwal['nama_mapel']; ?></h4>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                    <div class="flex items-center text-gray-700">
                                        <div class="p-2 bg-white rounded-lg mr-2 shadow-sm">
                                            <i class="fas fa-calendar-alt text-blue-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Hari</p>
                                            <p class="font-semibold"><?= $jadwal['hari']; ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-gray-700">
                                        <div class="p-2 bg-white rounded-lg mr-2 shadow-sm">
                                            <i class="fas fa-clock text-purple-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Waktu</p>
                                            <p class="font-semibold"><?= date('H:i', strtotime($jadwal['jam_mulai'])); ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])); ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-gray-700">
                                        <div class="p-2 bg-white rounded-lg mr-2 shadow-sm">
                                            <i class="fas fa-school text-green-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Kelas</p>
                                            <p class="font-semibold"><?= $jadwal['nama_kelas']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?= base_url('guru/absensi/tambah'); ?>" 
                                class="ml-4 inline-flex items-center px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium rounded-lg shadow-sm transition-all">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                Ganti
                            </a>
                        </div>
                    </div>

                    <input type="hidden" name="jadwal_mengajar_id" value="<?= $jadwal['id']; ?>">
                <?php else: ?>
                    <!-- Mode Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-question-circle mr-2 text-blue-500"></i>
                            Mode Input Absensi
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button type="button" id="modeOwnSchedule" 
                                    class="mode-btn active flex items-center justify-center px-6 py-4 border-2 border-blue-500 bg-blue-50 rounded-xl transition-all hover:shadow-md">
                                <div class="text-center">
                                    <i class="fas fa-chalkboard-teacher text-2xl text-blue-600 mb-2"></i>
                                    <p class="font-bold text-gray-800">Jadwal Saya Sendiri</p>
                                    <p class="text-xs text-gray-600 mt-1">Mengajar sesuai jadwal reguler</p>
                                </div>
                            </button>
                            <button type="button" id="modeSubstitute" 
                                    class="mode-btn flex items-center justify-center px-6 py-4 border-2 border-gray-300 bg-white rounded-xl transition-all hover:shadow-md hover:border-purple-300">
                                <div class="text-center">
                                    <i class="fas fa-user-plus text-2xl text-purple-600 mb-2"></i>
                                    <p class="font-bold text-gray-800">Guru Pengganti</p>
                                    <p class="text-xs text-gray-600 mt-1">Menggantikan guru lain</p>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Jadwal Selection Form -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-1 text-blue-500"></i>
                                Tanggal Absensi <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                id="tanggal"
                                name="tanggal"
                                value="<?= $tanggal; ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Jadwal akan muncul otomatis berdasarkan hari dari tanggal yang dipilih
                            </p>
                        </div>
                        <div>
                            <label for="jadwal_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clock mr-1 text-purple-500"></i>
                                <span id="jadwalLabel">Jadwal</span> <span class="text-red-500">*</span>
                            </label>
                            <select id="jadwal_id" name="jadwal_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih tanggal terlebih dahulu</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1" id="hariInfo">
                                <i class="fas fa-calendar-day mr-1"></i>
                                <span id="hariText">-</span>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Hidden field untuk hari (auto-detected) -->
                    <input type="hidden" id="hari" name="hari" value="">

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
                <!-- Absensi Details Section -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg mr-3">
                                <i class="fas fa-clipboard-list text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Detail Absensi</h3>
                        </div>
                        <div class="px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                            <span class="text-sm text-gray-600">Tanggal:</span>
                            <span class="font-bold text-blue-700 ml-2"><?= date('d/m/Y', strtotime($tanggal)); ?></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="pertemuan_ke" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-hashtag mr-2 text-indigo-500"></i>
                                Pertemuan Ke-
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="number"
                                id="pertemuan_ke"
                                name="pertemuan_ke"
                                value="<?= $pertemuanKe; ?>"
                                min="1"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                required>
                        </div>
                        <div>
                            <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                                Tanggal Absensi
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="date"
                                id="tanggal"
                                name="tanggal"
                                value="<?= $tanggal; ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                required>
                        </div>
                    </div>

                    <!-- Approved Izin Info -->
                    <?php if (!empty($approvedIzin)): ?>
                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-300 rounded-xl p-5 mb-6 shadow-sm">
                            <div class="flex items-start">
                                <div class="p-2 bg-blue-500 rounded-lg mr-3">
                                    <i class="fas fa-info-circle text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-blue-800 mb-2">
                                        Informasi Izin yang Disetujui
                                    </h4>
                                    <p class="text-sm text-blue-700 mb-3">Siswa berikut telah mengajukan izin dan disetujui:</p>
                                    <div class="space-y-2">
                                        <?php foreach ($approvedIzin as $izin): ?>
                                            <div class="flex items-center bg-white rounded-lg p-3 shadow-sm">
                                                <i class="fas fa-user-check text-blue-500 mr-3"></i>
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-800"><?= $izin['nama_lengkap']; ?></p>
                                                    <p class="text-xs text-gray-500">NIS: <?= $izin['nis']; ?></p>
                                                </div>
                                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                                    <?= ucfirst($izin['jenis_izin']); ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Students Table -->
                    <div class="bg-gray-50 rounded-xl p-1 mb-6">
                        <div class="bg-white rounded-xl shadow-md overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NIS</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Siswa</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status Kehadiran</th>
                                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="siswaTableBody">
                                        <!-- Will be populated by AJAX -->
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="p-4 bg-blue-100 rounded-full mb-3">
                                                        <i class="fas fa-spinner fa-spin text-blue-500 text-3xl"></i>
                                                    </div>
                                                    <p class="text-gray-600 font-medium">Memuat data siswa...</p>
                                                    <p class="text-gray-400 text-sm mt-1">Mohon tunggu sebentar</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-8 border-t-2 border-gray-200">
                    <a href="<?= base_url('guru/absensi'); ?>"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <button type="submit"
                            name="next_action"
                            value="list"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan Absensi
                        </button>
                        <button type="submit"
                            name="next_action"
                            value="jurnal"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                            <i class="fas fa-book mr-2"></i> Lanjut isi dokumentasi
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
    // Mode selection state
    let isSubstituteMode = false;

    // Handle mode selection
    document.getElementById('modeOwnSchedule').addEventListener('click', function() {
        isSubstituteMode = false;
        updateModeUI();
        // Reset jadwal selection
        document.getElementById('jadwal_id').innerHTML = '<option value="">Pilih Jadwal</option>';
        document.getElementById('hari').value = '';
    });

    document.getElementById('modeSubstitute').addEventListener('click', function() {
        isSubstituteMode = true;
        updateModeUI();
        // Reset jadwal selection
        document.getElementById('jadwal_id').innerHTML = '<option value="">Pilih Jadwal</option>';
        document.getElementById('hari').value = '';
    });

    function updateModeUI() {
        const ownBtn = document.getElementById('modeOwnSchedule');
        const subBtn = document.getElementById('modeSubstitute');
        const jadwalLabel = document.getElementById('jadwalLabel');

        if (isSubstituteMode) {
            // Substitute mode active
            ownBtn.classList.remove('border-blue-500', 'bg-blue-50');
            ownBtn.classList.add('border-gray-300', 'bg-white');
            
            subBtn.classList.remove('border-gray-300', 'bg-white');
            subBtn.classList.add('border-purple-500', 'bg-purple-50');
            
            jadwalLabel.innerHTML = '<i class="fas fa-exchange-alt mr-1 text-purple-500"></i> Jadwal yang Digantikan';
        } else {
            // Own schedule mode active
            ownBtn.classList.remove('border-gray-300', 'bg-white');
            ownBtn.classList.add('border-blue-500', 'bg-blue-50');
            
            subBtn.classList.remove('border-purple-500', 'bg-purple-50');
            subBtn.classList.add('border-gray-300', 'bg-white');
            
            jadwalLabel.textContent = 'Jadwal';
        }
    }

    // Function to get day name from date
    function getDayFromDate(dateString) {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const date = new Date(dateString);
        return days[date.getDay()];
    }

    // Function to load jadwal based on hari
    function loadJadwalByHari(hari) {
        const jadwalSelect = document.getElementById('jadwal_id');
        
        if (!hari) {
            jadwalSelect.innerHTML = '<option value="">Pilih tanggal terlebih dahulu</option>';
            return;
        }

        jadwalSelect.innerHTML = '<option value="">Memuat jadwal...</option>';

        const url = `<?= base_url('guru/absensi/getJadwalByHari'); ?>?hari=${hari}&substitute=${isSubstituteMode}`;

        fetch(url, {
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
                        if (data.isSubstitute && jadwal.nama_guru) {
                            // Show teacher name for substitute mode
                            options += `<option value="${jadwal.id}">${jadwal.nama_mapel} - ${jadwal.nama_kelas} (${waktu}) - Guru: ${jadwal.nama_guru}</option>`;
                        } else {
                            options += `<option value="${jadwal.id}">${jadwal.nama_mapel} - ${jadwal.nama_kelas} (${waktu})</option>`;
                        }
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
                    const noDataMsg = isSubstituteMode ? 'Tidak ada jadwal di hari ini' : 'Tidak ada jadwal untuk hari ini';
                    jadwalSelect.innerHTML = `<option value="">${noDataMsg}</option>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                jadwalSelect.innerHTML = '<option value="">Error loading data</option>';
            });
    }

    // Handle tanggal selection - auto detect hari and load jadwal
    document.getElementById('tanggal').addEventListener('change', function() {
        const tanggal = this.value;
        
        if (!tanggal) {
            document.getElementById('hari').value = '';
            document.getElementById('hariText').textContent = '-';
            document.getElementById('jadwal_id').innerHTML = '<option value="">Pilih tanggal terlebih dahulu</option>';
            return;
        }

        // Get day name from selected date
        const hari = getDayFromDate(tanggal);
        
        // Update hidden field and display
        document.getElementById('hari').value = hari;
        document.getElementById('hariText').textContent = hari;
        
        // Load jadwal for this day
        loadJadwalByHari(hari);
    });

    // Trigger on page load if tanggal already set
    window.addEventListener('DOMContentLoaded', function() {
        const tanggal = document.getElementById('tanggal').value;
        if (tanggal) {
            const hari = getDayFromDate(tanggal);
            document.getElementById('hari').value = hari;
            document.getElementById('hariText').textContent = hari;
            loadJadwalByHari(hari);
        }
    });
</script>
<?= $this->endSection() ?>