<?= $this->extend('templates/mobile_layout') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header Section with Back Button and Profile -->
    <div class="bg-white border-b sticky top-0 z-10">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center gap-3 flex-1">
                <a href="<?= base_url('guru/absensi') ?>" class="text-gray-700 hover:text-gray-900">
                    <i class="fas fa-chevron-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Edit Absensi</h1>
                    <p class="text-xs text-gray-500"><?= esc($absensi['nama_mapel']) ?> • Pertemuan <?= esc($absensi['pertemuan_ke']) ?></p>
                </div>
            </div>
            <?php if (session()->get('profile_photo')): ?>
                <img src="<?= base_url('profile-photo/' . session()->get('profile_photo')) ?>" 
                     alt="Profile" 
                     class="w-10 h-10 rounded-full object-cover border-2 border-gray-200">
            <?php else: ?>
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                    <?= strtoupper(substr(session()->get('nama'), 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= render_flash_message() ?>

    <div class="p-4">
        <!-- Info Card -->
        <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-4 mb-4 shadow-sm">
            <!-- Date & Time -->
            <div class="flex items-start gap-3 mb-3">
                <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Hari & Tanggal</p>
                    <p class="text-sm font-bold text-gray-900"><?= strftime('%A, %d %b %Y', strtotime($absensi['tanggal'])) ?></p>
                </div>
            </div>

            <!-- Subject -->
            <div class="flex items-start gap-3 mb-3">
                <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Mata Pelajaran</p>
                    <p class="text-sm font-bold text-gray-900"><?= esc($absensi['nama_mapel']) ?></p>
                </div>
            </div>

            <!-- Class -->
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-pink-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Kelas</p>
                    <p class="text-sm font-bold text-gray-900"><?= esc($absensi['nama_kelas']) ?> (<?= count($siswaList) ?> Siswa)</p>
                </div>
            </div>
        </div>

        <!-- Meeting Info -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-gray-900">Pertemuan ke-<?= esc($absensi['pertemuan_ke']) ?></h2>
            <div class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-clock text-sm"></i>
                <span class="text-sm font-medium"><?= esc($absensi['jam_mulai']) ?> - <?= esc($absensi['jam_selesai']) ?></span>
            </div>
        </div>

        <form action="<?= base_url('guru/absensi/update/' . $absensi['id']) ?>" method="post" id="absensiForm">
            <?= csrf_field() ?>

            <!-- Quick Action Buttons -->
            <div class="mb-4">
                <p class="text-sm font-bold text-gray-700 mb-2">AKSI CEPAT</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" 
                            onclick="setAllStatus('hadir')" 
                            class="flex items-center justify-center gap-2 py-3 px-4 bg-green-50 border-2 border-green-200 rounded-xl text-green-700 font-medium text-sm hover:bg-green-100 transition-all">
                        <i class="fas fa-check-circle"></i>
                        <span>Hadir Semua</span>
                    </button>
                    <button type="button" 
                            onclick="setAllStatus('izin')" 
                            class="flex items-center justify-center gap-2 py-3 px-4 bg-blue-50 border-2 border-blue-200 rounded-xl text-blue-700 font-medium text-sm hover:bg-blue-100 transition-all">
                        <i class="fas fa-info-circle"></i>
                        <span>Izin Semua</span>
                    </button>
                    <button type="button" 
                            onclick="setAllStatus('sakit')" 
                            class="flex items-center justify-center gap-2 py-3 px-4 bg-orange-50 border-2 border-orange-200 rounded-xl text-orange-700 font-medium text-sm hover:bg-orange-100 transition-all">
                        <i class="fas fa-thermometer text-sm"></i>
                        <span>Sakit Semua</span>
                    </button>
                    <button type="button" 
                            onclick="setAllStatus('alpa')" 
                            class="flex items-center justify-center gap-2 py-3 px-4 bg-red-50 border-2 border-red-200 rounded-xl text-red-700 font-medium text-sm hover:bg-red-100 transition-all">
                        <i class="fas fa-times-circle"></i>
                        <span>Alpa Semua</span>
                    </button>
                </div>
            </div>

            <!-- Students List -->
            <div class="space-y-3">
                <?php foreach ($siswaList as $siswa): ?>
                    <?php
                    // Get existing detail for this student
                    $existingDetail = null;
                    foreach ($absensiDetails as $detail) {
                        if ((int)$detail['siswa_id'] == (int)$siswa['id']) {
                            $existingDetail = $detail;
                            break;
                        }
                    }
                    $currentStatus = $existingDetail ? strtolower(trim($existingDetail['status'])) : '';
                    $currentKeterangan = $existingDetail ? $existingDetail['keterangan'] : '';
                    ?>

                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                        <!-- Student Header -->
                        <div class="flex items-center justify-between p-4 bg-gray-50">
                            <div class="flex items-center gap-3">
                                <?php if (!empty($siswa['foto'])): ?>
                                    <img src="<?= base_url('writable/uploads/' . $siswa['foto']) ?>" 
                                         alt="<?= esc($siswa['nama_lengkap']) ?>" 
                                         class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm">
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold shadow-sm">
                                        <?= strtoupper(substr($siswa['nama_lengkap'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <p class="text-sm font-bold text-gray-900"><?= esc($siswa['nama_lengkap']) ?></p>
                                    <p class="text-xs text-gray-500">NIS: <?= esc($siswa['nis']) ?></p>
                                </div>
                            </div>
                            <button type="button" class="text-gray-400 hover:text-gray-600 p-2">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>

                        <!-- Status Buttons -->
                        <div class="p-4">
                            <input type="hidden" name="siswa_id[]" value="<?= $siswa['id'] ?>">
                            
                            <div class="grid grid-cols-4 gap-2">
                                <!-- Hadir -->
                                <label class="status-btn-wrapper">
                                    <input type="radio"
                                           name="status[<?= $siswa['id'] ?>]"
                                           value="hadir"
                                           class="status-input hidden"
                                           data-siswa-id="<?= $siswa['id'] ?>"
                                           <?= $currentStatus == 'hadir' ? 'checked' : '' ?>
                                           onchange="updateStatusUI(<?= $siswa['id'] ?>, 'hadir'); toggleKeterangan(<?= $siswa['id'] ?>)">
                                    <div class="status-btn <?= $currentStatus == 'hadir' ? 'active-hadir' : '' ?> flex flex-col items-center justify-center py-3 rounded-xl border-2 cursor-pointer transition-all hover:scale-105">
                                        <i class="fas fa-check text-lg mb-1"></i>
                                        <span class="text-xs font-semibold">Hadir</span>
                                    </div>
                                </label>

                                <!-- Izin -->
                                <label class="status-btn-wrapper">
                                    <input type="radio"
                                           name="status[<?= $siswa['id'] ?>]"
                                           value="izin"
                                           class="status-input hidden"
                                           data-siswa-id="<?= $siswa['id'] ?>"
                                           <?= $currentStatus == 'izin' ? 'checked' : '' ?>
                                           onchange="updateStatusUI(<?= $siswa['id'] ?>, 'izin'); toggleKeterangan(<?= $siswa['id'] ?>)">
                                    <div class="status-btn <?= $currentStatus == 'izin' ? 'active-izin' : '' ?> flex flex-col items-center justify-center py-3 rounded-xl border-2 cursor-pointer transition-all hover:scale-105">
                                        <i class="fas fa-file-alt text-lg mb-1"></i>
                                        <span class="text-xs font-semibold">Izin</span>
                                    </div>
                                </label>

                                <!-- Sakit -->
                                <label class="status-btn-wrapper">
                                    <input type="radio"
                                           name="status[<?= $siswa['id'] ?>]"
                                           value="sakit"
                                           class="status-input hidden"
                                           data-siswa-id="<?= $siswa['id'] ?>"
                                           <?= $currentStatus == 'sakit' ? 'checked' : '' ?>
                                           onchange="updateStatusUI(<?= $siswa['id'] ?>, 'sakit'); toggleKeterangan(<?= $siswa['id'] ?>)">
                                    <div class="status-btn <?= $currentStatus == 'sakit' ? 'active-sakit' : '' ?> flex flex-col items-center justify-center py-3 rounded-xl border-2 cursor-pointer transition-all hover:scale-105">
                                        <i class="fas fa-thermometer text-lg mb-1"></i>
                                        <span class="text-xs font-semibold">Sakit</span>
                                    </div>
                                </label>

                                <!-- Alpa -->
                                <label class="status-btn-wrapper">
                                    <input type="radio"
                                           name="status[<?= $siswa['id'] ?>]"
                                           value="alpa"
                                           class="status-input hidden"
                                           data-siswa-id="<?= $siswa['id'] ?>"
                                           <?= $currentStatus == 'alpa' ? 'checked' : '' ?>
                                           onchange="updateStatusUI(<?= $siswa['id'] ?>, 'alpa'); toggleKeterangan(<?= $siswa['id'] ?>)">
                                    <div class="status-btn <?= $currentStatus == 'alpa' ? 'active-alpa' : '' ?> flex flex-col items-center justify-center py-3 rounded-xl border-2 cursor-pointer transition-all hover:scale-105">
                                        <i class="fas fa-times text-lg mb-1"></i>
                                        <span class="text-xs font-semibold">Alpa</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Keterangan Field (Hidden by default) -->
                            <div id="keterangan-<?= $siswa['id'] ?>" class="mt-3 <?= ($currentStatus == 'izin' || $currentStatus == 'sakit') ? '' : 'hidden' ?>">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan:</label>
                                <textarea name="keterangan[<?= $siswa['id'] ?>]"
                                          rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Masukkan keterangan..."><?= esc($currentKeterangan) ?></textarea>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 sticky bottom-20 z-10">
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold py-4 px-6 rounded-2xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>

            <!-- Optional: Link to Journal -->
            <div class="mt-4">
                <a href="<?= base_url('guru/absensi') ?>" 
                   class="w-full block text-center bg-white border-2 border-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-2xl hover:bg-gray-50 transition-all">
                    Kembali ke Daftar Absensi
                </a>
            </div>
        </form>
    </div>
</div>

<style>
/* Status Button Styles */
.status-btn {
    border-color: #e5e7eb;
    color: #6b7280;
    background-color: #ffffff;
}

.status-btn.active-hadir {
    border-color: #10b981;
    background-color: #10b981;
    color: #ffffff;
}

.status-btn.active-izin {
    border-color: #3b82f6;
    background-color: #3b82f6;
    color: #ffffff;
}

.status-btn.active-sakit {
    border-color: #f97316;
    background-color: #f97316;
    color: #ffffff;
}

.status-btn.active-alpa {
    border-color: #ef4444;
    background-color: #ef4444;
    color: #ffffff;
}

.status-btn:hover {
    border-color: #9ca3af;
}
</style>

<script>
// Toggle keterangan field based on status
function toggleKeterangan(siswaId) {
    const keteranganDiv = document.getElementById('keterangan-' + siswaId);
    const selectedStatus = document.querySelector('input[name="status[' + siswaId + ']"]:checked');
    
    if (selectedStatus && (selectedStatus.value === 'izin' || selectedStatus.value === 'sakit')) {
        keteranganDiv.classList.remove('hidden');
    } else {
        keteranganDiv.classList.add('hidden');
    }
}

// Update status UI when radio button changes
function updateStatusUI(siswaId, status) {
    const wrapper = document.querySelector(`input[name="status[${siswaId}]"][value="${status}"]`).closest('.status-btn-wrapper');
    const allWrappers = document.querySelectorAll(`input[name="status[${siswaId}]"]`);
    
    // Remove all active classes
    allWrappers.forEach(input => {
        const btn = input.nextElementSibling;
        btn.classList.remove('active-hadir', 'active-izin', 'active-sakit', 'active-alpa');
    });
    
    // Add active class to selected button
    const btn = wrapper.querySelector('.status-btn');
    btn.classList.add(`active-${status}`);
}

// Set all students to specific status
function setAllStatus(status) {
    const allRadios = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
    
    allRadios.forEach(radio => {
        radio.checked = true;
        const siswaId = radio.getAttribute('data-siswa-id');
        updateStatusUI(siswaId, status);
        toggleKeterangan(siswaId);
    });
    
    // Show feedback
    showToast(`Semua siswa diset sebagai ${status.toUpperCase()}`);
}

// Simple toast notification
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize keterangan visibility
    document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
        const siswaId = radio.getAttribute('data-siswa-id');
        toggleKeterangan(siswaId);
    });
});
</script>

<?= $this->endSection() ?>
