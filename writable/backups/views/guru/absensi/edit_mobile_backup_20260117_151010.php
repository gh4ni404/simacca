<?= $this->extend('templates/mobile_layout') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 mb-4 shadow-lg">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                <i class="fas fa-edit text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold">Edit Absensi</h1>
                <p class="text-sm opacity-90">Perbarui data absensi siswa</p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= render_flash_message() ?>

    <div class="px-4">
        <!-- Absensi Info Card -->
        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-200 rounded-xl shadow-md p-4 mb-4">
            <div class="flex items-center mb-3">
                <div class="p-2 bg-blue-500 rounded-lg mr-2">
                    <i class="fas fa-info-circle text-white text-sm"></i>
                </div>
                <h2 class="text-base font-bold text-gray-800">Informasi Absensi</h2>
            </div>
            <div class="grid grid-cols-2 gap-2 mb-3">
                <div class="flex items-center bg-white rounded-lg p-2 shadow-sm">
                    <div class="p-1.5 bg-blue-100 rounded-lg mr-2">
                        <i class="fas fa-calendar-day text-blue-600 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tanggal</p>
                        <p class="text-xs font-bold text-gray-800"><?= date('d M Y', strtotime($absensi['tanggal'])) ?></p>
                    </div>
                </div>
                <div class="flex items-center bg-white rounded-lg p-2 shadow-sm">
                    <div class="p-1.5 bg-green-100 rounded-lg mr-2">
                        <i class="fas fa-book text-green-600 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Mapel</p>
                        <p class="text-xs font-bold text-gray-800"><?= esc($absensi['nama_mapel']) ?></p>
                    </div>
                </div>
                <div class="flex items-center bg-white rounded-lg p-2 shadow-sm">
                    <div class="p-1.5 bg-purple-100 rounded-lg mr-2">
                        <i class="fas fa-school text-purple-600 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Kelas</p>
                        <p class="text-xs font-bold text-gray-800"><?= esc($absensi['nama_kelas']) ?></p>
                    </div>
                </div>
                <div class="flex items-center bg-white rounded-lg p-2 shadow-sm">
                    <div class="p-1.5 bg-orange-100 rounded-lg mr-2">
                        <i class="fas fa-hashtag text-orange-600 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Pertemuan</p>
                        <p class="text-xs font-bold text-gray-800"><?= $absensi['pertemuan_ke'] ?></p>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($absensi['guru_pengganti_nama'])): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-2">
                <p class="text-xs text-yellow-800 flex items-center">
                    <i class="fas fa-user-tie mr-2"></i>
                    <span><strong>Guru Pengganti:</strong> <?= esc($absensi['guru_pengganti_nama']) ?></span>
                </p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Form Edit -->
        <form method="post" action="<?= base_url('guru/absensi/update/' . $absensi['id']); ?>" id="editAbsensiForm">
            <?= csrf_field() ?>
            
            <!-- Editable Fields Card -->
            <div class="bg-white rounded-xl shadow-md mb-4 overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-orange-50 to-red-50 border-b">
                    <h3 class="text-sm font-bold text-gray-800 flex items-center">
                        <i class="fas fa-edit text-orange-500 mr-2"></i>
                        Data yang Dapat Diubah
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <!-- Pertemuan Ke -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-hashtag mr-1 text-blue-500 text-xs"></i>
                            Pertemuan Ke <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="number"
                            class="w-full px-3 py-2 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            name="pertemuan_ke"
                            value="<?= old('pertemuan_ke', $absensi['pertemuan_ke']) ?>"
                            required
                            min="1">
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-calendar-alt mr-1 text-blue-500 text-xs"></i>
                            Tanggal <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="date"
                            class="w-full px-3 py-2 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            name="tanggal"
                            value="<?= old('tanggal', $absensi['tanggal']) ?>"
                            required>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Dapat diubah sesuai kebutuhan
                        </p>
                    </div>
                </div>
            </div>

            <!-- Progress Card -->
            <div class="bg-white rounded-xl shadow-md mb-4 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-700">Progress</span>
                    <span class="text-xs font-bold text-blue-600" id="progressText">0/0</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all" id="progressBar" style="width: 0%"></div>
                </div>
            </div>

            <!-- Quick Actions (Mobile Only) -->
            <div class="bg-white rounded-xl shadow-md p-3 mb-4">
                <p class="text-xs font-semibold text-gray-700 mb-2">Aksi Cepat:</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button"
                        onclick="setAllStatus('hadir')"
                        class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-lg active:scale-95 transition-all">
                        <i class="fas fa-check-circle mr-1"></i> Semua Hadir
                    </button>
                    <button type="button"
                        onclick="setAllStatus('izin')"
                        class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg active:scale-95 transition-all">
                        <i class="fas fa-file-alt mr-1"></i> Semua Izin
                    </button>
                    <button type="button"
                        onclick="setAllStatus('sakit')"
                        class="px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded-lg active:scale-95 transition-all">
                        <i class="fas fa-notes-medical mr-1"></i> Semua Sakit
                    </button>
                    <button type="button"
                        onclick="setAllStatus('alpa')"
                        class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg active:scale-95 transition-all">
                        <i class="fas fa-times-circle mr-1"></i> Semua Alpa
                    </button>
                </div>
            </div>

            <!-- Student List - Card Based -->
            <div class="space-y-3 mb-6">
                <?php if (empty($siswaList)): ?>
                    <?= empty_state(
                        'users', 
                        'Tidak Ada Siswa', 
                        'Belum ada data siswa untuk kelas ini',
                        '',
                        ''
                    ); ?>
                <?php else: ?>
                    <?php $no = 1; foreach ($siswaList as $siswa): ?>
                        <?php
                        // Get existing detail for this student
                        $existingDetail = null;
                        foreach ($absensiDetails as $detail) {
                            // Use loose comparison to handle type differences (string vs int)
                            if ((int)$detail['siswa_id'] == (int)$siswa['id']) {
                                $existingDetail = $detail;
                                break;
                            }
                        }
                        $currentStatus = $existingDetail ? strtolower(trim($existingDetail['status'])) : '';
                        $currentKeterangan = $existingDetail ? $existingDetail['keterangan'] : '';
                        
                        // Debug: Uncomment to see values
                        // echo "<!-- Siswa ID: {$siswa['id']}, Status: {$currentStatus} -->";
                        ?>
                        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                            <div class="p-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs mr-2">
                                        <?= $no++ ?>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900"><?= esc($siswa['nama_lengkap']) ?></p>
                                        <p class="text-xs text-gray-500">NIS: <?= esc($siswa['nis']) ?></p>
                                    </div>
                                </div>
                                <?php if ($currentStatus): ?>
                                <span class="status-badge-<?= $currentStatus ?> px-2 py-1 rounded-full text-xs font-semibold">
                                    <?= ucfirst($currentStatus) ?>
                                </span>
                                <?php endif; ?>
                            </div>

                            <div class="p-3">
                                <input type="hidden" name="siswa_id[]" value="<?= $siswa['id'] ?>">
                                
                                <!-- Status Radio Buttons -->
                                <div class="mb-2">
                                    <p class="text-xs font-semibold text-gray-700 mb-2">Status Kehadiran:</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <label class="flex items-center p-2 border-2 rounded-lg cursor-pointer hover:bg-green-50 transition-colors <?= $currentStatus == 'hadir' ? 'border-green-500 bg-green-50' : 'border-gray-200' ?>">
                                            <input type="radio"
                                                name="status[<?= $siswa['id'] ?>]"
                                                value="hadir"
                                                class="status-input hidden"
                                                data-siswa-id="<?= $siswa['id'] ?>"
                                                <?= $currentStatus == 'hadir' ? 'checked' : '' ?>
                                                onchange="updateProgress(); toggleKeterangan(<?= $siswa['id'] ?>)">
                                            <div class="flex items-center">
                                                <div class="status-indicator w-5 h-5 rounded-full border-2 mr-2 flex items-center justify-center <?= $currentStatus == 'hadir' ? 'border-green-500 bg-green-500' : 'border-gray-300' ?>">
                                                    <i class="fas fa-check text-white text-xs <?= $currentStatus == 'hadir' ? '' : 'hidden' ?>"></i>
                                                </div>
                                                <span class="text-xs font-semibold">Hadir</span>
                                            </div>
                                        </label>

                                        <label class="flex items-center p-2 border-2 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors <?= $currentStatus == 'izin' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' ?>">
                                            <input type="radio"
                                                name="status[<?= $siswa['id'] ?>]"
                                                value="izin"
                                                class="status-input hidden"
                                                data-siswa-id="<?= $siswa['id'] ?>"
                                                <?= $currentStatus == 'izin' ? 'checked' : '' ?>
                                                onchange="updateProgress(); toggleKeterangan(<?= $siswa['id'] ?>); updateVisualFeedback(this)">
                                            <div class="flex items-center">
                                                <div class="status-indicator w-5 h-5 rounded-full border-2 mr-2 flex items-center justify-center <?= $currentStatus == 'izin' ? 'border-blue-500 bg-blue-500' : 'border-gray-300' ?>">
                                                    <i class="fas fa-check text-white text-xs <?= $currentStatus == 'izin' ? '' : 'hidden' ?>"></i>
                                                </div>
                                                <span class="text-xs font-semibold">Izin</span>
                                            </div>
                                        </label>

                                        <label class="flex items-center p-2 border-2 rounded-lg cursor-pointer hover:bg-yellow-50 transition-colors <?= $currentStatus == 'sakit' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200' ?>">
                                            <input type="radio"
                                                name="status[<?= $siswa['id'] ?>]"
                                                value="sakit"
                                                class="status-input hidden"
                                                data-siswa-id="<?= $siswa['id'] ?>"
                                                <?= $currentStatus == 'sakit' ? 'checked' : '' ?>
                                                onchange="updateProgress(); toggleKeterangan(<?= $siswa['id'] ?>); updateVisualFeedback(this)">
                                            <div class="flex items-center">
                                                <div class="status-indicator w-5 h-5 rounded-full border-2 mr-2 flex items-center justify-center <?= $currentStatus == 'sakit' ? 'border-yellow-500 bg-yellow-500' : 'border-gray-300' ?>">
                                                    <i class="fas fa-check text-white text-xs <?= $currentStatus == 'sakit' ? '' : 'hidden' ?>"></i>
                                                </div>
                                                <span class="text-xs font-semibold">Sakit</span>
                                            </div>
                                        </label>

                                        <label class="flex items-center p-2 border-2 rounded-lg cursor-pointer hover:bg-red-50 transition-colors <?= $currentStatus == 'alpa' ? 'border-red-500 bg-red-50' : 'border-gray-200' ?>">
                                            <input type="radio"
                                                name="status[<?= $siswa['id'] ?>]"
                                                value="alpa"
                                                class="status-input hidden"
                                                data-siswa-id="<?= $siswa['id'] ?>"
                                                <?= $currentStatus == 'alpa' ? 'checked' : '' ?>
                                                onchange="updateProgress(); toggleKeterangan(<?= $siswa['id'] ?>); updateVisualFeedback(this)">
                                            <div class="flex items-center">
                                                <div class="status-indicator w-5 h-5 rounded-full border-2 mr-2 flex items-center justify-center <?= $currentStatus == 'alpa' ? 'border-red-500 bg-red-500' : 'border-gray-300' ?>">
                                                    <i class="fas fa-check text-white text-xs <?= $currentStatus == 'alpa' ? '' : 'hidden' ?>"></i>
                                                </div>
                                                <span class="text-xs font-semibold">Alpa</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Keterangan Field (shown for izin/sakit) -->
                                <div id="keterangan-<?= $siswa['id'] ?>" class="<?= in_array($currentStatus, ['izin', 'sakit']) ? '' : 'hidden' ?>">
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan:</label>
                                    <textarea name="keterangan[<?= $siswa['id'] ?>]"
                                        rows="2"
                                        class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Opsional: Tambahkan keterangan..."><?= esc($currentKeterangan) ?></textarea>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Submit Buttons -->
            <div class="sticky bottom-16 bg-white rounded-xl shadow-lg p-4 border-t-4 border-blue-500 mb-4">
                <div class="flex gap-2">
                    <a href="<?= base_url('guru/absensi'); ?>"
                        class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-lg text-center active:scale-95 transition-all">
                        <i class="fas fa-arrow-left mr-1"></i> Batal
                    </a>
                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-semibold rounded-lg active:scale-95 transition-all">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function setAllStatus(status) {
    const inputs = document.querySelectorAll('.status-input[value="' + status + '"]');
    inputs.forEach(input => {
        input.checked = true;
        const siswaId = input.getAttribute('data-siswa-id');
        toggleKeterangan(siswaId);
    });
    updateProgress();
}

function toggleKeterangan(siswaId) {
    const keteranganDiv = document.getElementById('keterangan-' + siswaId);
    const selectedStatus = document.querySelector('input[name="status[' + siswaId + ']"]:checked');
    
    if (selectedStatus && (selectedStatus.value === 'izin' || selectedStatus.value === 'sakit')) {
        keteranganDiv.classList.remove('hidden');
    } else {
        keteranganDiv.classList.add('hidden');
    }
}

function updateVisualFeedback(radioInput) {
    const siswaId = radioInput.getAttribute('data-siswa-id');
    const selectedValue = radioInput.value;
    
    // Get all labels for this student
    const allLabels = radioInput.closest('.grid').querySelectorAll('label');
    
    // Color map
    const colors = {
        'hadir': 'green',
        'izin': 'blue',
        'sakit': 'yellow',
        'alpa': 'red'
    };
    
    // Reset all labels and indicators
    allLabels.forEach(label => {
        const input = label.querySelector('input');
        const indicator = label.querySelector('.status-indicator');
        const checkIcon = indicator.querySelector('i');
        const status = input.value;
        const color = colors[status];
        
        if (input.checked) {
            // Selected state
            label.classList.remove('border-gray-200');
            label.classList.add(`border-${color}-500`, `bg-${color}-50`);
            
            indicator.classList.remove('border-gray-300');
            indicator.classList.add(`border-${color}-500`, `bg-${color}-500`);
            checkIcon.classList.remove('hidden');
        } else {
            // Unselected state
            label.classList.remove(`border-${color}-500`, `bg-${color}-50`);
            label.classList.add('border-gray-200');
            
            indicator.classList.remove(`border-${color}-500`, `bg-${color}-500`);
            indicator.classList.add('border-gray-300');
            checkIcon.classList.add('hidden');
        }
    });
}

function updateProgress() {
    const total = document.querySelectorAll('.status-input').length / 4; // 4 status per student
    const filled = document.querySelectorAll('.status-input:checked').length;
    const percentage = total > 0 ? Math.round((filled / total) * 100) : 0;
    
    document.getElementById('progressText').textContent = filled + '/' + total;
    document.getElementById('progressBar').style.width = percentage + '%';
}

// Form validation
document.getElementById('editAbsensiForm').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.status-input:checked').length;
    if (checked === 0) {
        e.preventDefault();
        alert('Mohon isi setidaknya satu status kehadiran siswa!');
        return false;
    }
    
    // Disable submit button
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
});

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    updateProgress();
    
    // Setup keterangan visibility for existing data
    document.querySelectorAll('.status-input:checked').forEach(input => {
        const siswaId = input.getAttribute('data-siswa-id');
        toggleKeterangan(siswaId);
    });
});
</script>

<style>
.status-badge-hadir { background-color: #dcfce7; color: #166534; }
.status-badge-izin { background-color: #dbeafe; color: #1e40af; }
.status-badge-sakit { background-color: #fef3c7; color: #92400e; }
.status-badge-alpa { background-color: #fee2e2; color: #991b1b; }
</style>

<?= $this->endSection() ?>
