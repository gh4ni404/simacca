<?= $this->extend('templates/desktop_layout') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Edit Absensi
                    </span>
                </h1>
                <p class="text-base text-gray-600 flex items-center mt-1">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Perbarui data absensi siswa dengan mudah dan cepat
                </p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= render_flash_message() ?>

    <!-- Absensi Info Card -->
    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-300 rounded-2xl shadow-lg p-6 mb-8">
        <div class="flex items-center mb-4">
            <div class="p-2 bg-blue-500 rounded-lg mr-3">
                <i class="fas fa-info-circle text-white"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-800">Informasi Absensi</h2>
        </div>
        <div class="grid grid-cols-4 gap-4">
            <div class="flex items-center bg-white rounded-lg p-4 shadow-sm">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <i class="fas fa-calendar-day text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Tanggal</p>
                    <p class="text-sm font-bold text-gray-800"><?= date('d F Y', strtotime($absensi['tanggal'])) ?></p>
                </div>
            </div>
            <div class="flex items-center bg-white rounded-lg p-4 shadow-sm">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                    <i class="fas fa-book text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Mata Pelajaran</p>
                    <p class="text-sm font-bold text-gray-800"><?= esc($absensi['nama_mapel']) ?></p>
                </div>
            </div>
            <div class="flex items-center bg-white rounded-lg p-4 shadow-sm">
                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                    <i class="fas fa-school text-purple-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Kelas</p>
                    <p class="text-sm font-bold text-gray-800"><?= esc($absensi['nama_kelas']) ?></p>
                </div>
            </div>
            <div class="flex items-center bg-white rounded-lg p-4 shadow-sm">
                <div class="p-2 bg-orange-100 rounded-lg mr-3">
                    <i class="fas fa-hashtag text-orange-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Pertemuan Ke</p>
                    <p class="text-sm font-bold text-gray-800"><?= $absensi['pertemuan_ke'] ?></p>
                </div>
            </div>
        </div>
        
        <?php if (!empty($absensi['guru_pengganti_nama'])): ?>
        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
            <p class="text-sm text-yellow-800 flex items-center">
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
        <div class="bg-white rounded-2xl shadow-lg mb-6 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-red-50 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-edit text-orange-500 mr-2"></i>
                    Data yang Dapat Diubah
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Pertemuan Ke -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-hashtag mr-2 text-blue-500"></i>
                            Pertemuan Ke <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="number"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                            name="pertemuan_ke"
                            value="<?= old('pertemuan_ke', $absensi['pertemuan_ke']) ?>"
                            required
                            min="1">
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            Tanggal <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="date"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                            name="tanggal"
                            value="<?= old('tanggal', $absensi['tanggal']) ?>"
                            required>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tanggal dapat diubah sesuai kebutuhan
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Attendance Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-500 rounded-lg mr-3">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Daftar Kehadiran Siswa</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600 mr-2">Progress:</span>
                        <span class="text-sm font-bold text-blue-600" id="progressText">0/0</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2 ml-2">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all" id="progressBar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-gray-700 mr-2">Aksi Cepat:</span>
                    <button type="button"
                        onclick="setAllStatus('hadir')"
                        class="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                        <i class="fas fa-check-circle mr-1"></i> Semua Hadir
                    </button>
                    <button type="button"
                        onclick="setAllStatus('izin')"
                        class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                        <i class="fas fa-file-alt mr-1"></i> Semua Izin
                    </button>
                    <button type="button"
                        onclick="setAllStatus('sakit')"
                        class="inline-flex items-center px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                        <i class="fas fa-notes-medical mr-1"></i> Semua Sakit
                    </button>
                    <button type="button"
                        onclick="setAllStatus('alpa')"
                        class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                        <i class="fas fa-times-circle mr-1"></i> Semua Alpa
                    </button>
                </div>
            </div>

            <?php if (empty($siswaList)): ?>
                <div class="p-16">
                    <?= empty_state(
                        'users', 
                        'Tidak Ada Siswa', 
                        'Belum ada data siswa untuk kelas ini',
                        '',
                        ''
                    ); ?>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">NIS</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Siswa</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Hadir</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Izin</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Sakit</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Alpa</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
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
                                <tr class="hover:bg-blue-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-gray-700"><?= $no++ ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600"><?= esc($siswa['nis']) ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xs mr-3">
                                                <?= strtoupper(substr($siswa['nama_lengkap'], 0, 1)) ?>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-900"><?= esc($siswa['nama_lengkap']) ?></span>
                                        </div>
                                        <input type="hidden" name="siswa_id[]" value="<?= $siswa['id'] ?>">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio"
                                                name="status[<?= $siswa['id'] ?>]"
                                                value="hadir"
                                                class="status-input w-5 h-5 text-green-600 focus:ring-green-500"
                                                data-siswa-id="<?= $siswa['id'] ?>"
                                                <?= $currentStatus == 'hadir' ? 'checked' : '' ?>
                                                onchange="updateProgress(); toggleKeterangan(<?= $siswa['id'] ?>)">
                                        </label>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio"
                                                name="status[<?= $siswa['id'] ?>]"
                                                value="izin"
                                                class="status-input w-5 h-5 text-blue-600 focus:ring-blue-500"
                                                data-siswa-id="<?= $siswa['id'] ?>"
                                                <?= $currentStatus == 'izin' ? 'checked' : '' ?>
                                                onchange="updateProgress(); toggleKeterangan(<?= $siswa['id'] ?>)">
                                        </label>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio"
                                                name="status[<?= $siswa['id'] ?>]"
                                                value="sakit"
                                                class="status-input w-5 h-5 text-yellow-600 focus:ring-yellow-500"
                                                data-siswa-id="<?= $siswa['id'] ?>"
                                                <?= $currentStatus == 'sakit' ? 'checked' : '' ?>
                                                onchange="updateProgress(); toggleKeterangan(<?= $siswa['id'] ?>)">
                                        </label>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio"
                                                name="status[<?= $siswa['id'] ?>]"
                                                value="alpa"
                                                class="status-input w-5 h-5 text-red-600 focus:ring-red-500"
                                                data-siswa-id="<?= $siswa['id'] ?>"
                                                <?= $currentStatus == 'alpa' ? 'checked' : '' ?>
                                                onchange="updateProgress(); toggleKeterangan(<?= $siswa['id'] ?>)">
                                        </label>
                                    </td>
                                    <td class="px-6 py-4">
                                        <textarea name="keterangan[<?= $siswa['id'] ?>]"
                                            id="keterangan-<?= $siswa['id'] ?>"
                                            rows="1"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none <?= in_array($currentStatus, ['izin', 'sakit']) ? '' : 'hidden' ?>"
                                            placeholder="Keterangan (opsional)..."><?= esc($currentKeterangan) ?></textarea>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Submit Buttons -->
        <div class="mt-6 bg-white rounded-2xl shadow-lg p-6 border-t-4 border-blue-500">
            <div class="flex items-center justify-between">
                <a href="<?= base_url('guru/absensi'); ?>"
                    class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                <button type="submit"
                    class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
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
    const keteranganField = document.getElementById('keterangan-' + siswaId);
    const selectedStatus = document.querySelector('input[name="status[' + siswaId + ']"]:checked');
    
    if (selectedStatus && (selectedStatus.value === 'izin' || selectedStatus.value === 'sakit')) {
        keteranganField.classList.remove('hidden');
    } else {
        keteranganField.classList.add('hidden');
        keteranganField.value = ''; // Clear value when hidden
    }
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
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
    
    // Re-enable after timeout (in case of error)
    setTimeout(function() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Simpan Perubahan';
    }, 5000);
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateProgress();
    
    // Setup keterangan visibility for existing data
    document.querySelectorAll('.status-input:checked').forEach(input => {
        const siswaId = input.getAttribute('data-siswa-id');
        toggleKeterangan(siswaId);
    });
});
</script>
<?= $this->endSection() ?>
