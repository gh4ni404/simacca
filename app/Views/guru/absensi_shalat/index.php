<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-mosque text-green-600 mr-2"></i>Absensi Shalat
            </h2>
            <p class="text-gray-600">QR Code akan di-refresh otomatis setiap 15 detik</p>
        </div>
        <div class="flex items-center gap-2">
            <span id="status-badge" class="px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-600">
                <i class="fas fa-circle text-gray-400 mr-1"></i>Belum Aktif
            </span>
        </div>
    </div>
</div>

<?= view('components/alerts') ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- QR Code Section -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-qrcode mr-2"></i>QR Code Absensi Shalat
                </h3>
            </div>
            <div class="p-6">
                <!-- QR Code Display -->
                <div class="flex flex-col items-center justify-center py-8">
                    <div id="qr-container" class="bg-white p-4 rounded-2xl shadow-lg border-2 border-gray-200 mb-4">
                        <div id="qr-placeholder" class="w-64 h-64 flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <i class="fas fa-qrcode text-6xl mb-4"></i>
                                <p class="text-lg font-medium">Tekan tombol di bawah untuk memulai</p>
                            </div>
                        </div>
                        <div id="qr-code" class="hidden">
                            <canvas id="qr-canvas"></canvas>
                        </div>
                    </div>

                    <!-- Countdown Timer -->
                    <div id="countdown-container" class="hidden mt-4 text-center">
                        <div class="inline-flex items-center gap-2 bg-orange-100 text-orange-800 px-4 py-2 rounded-full">
                            <i class="fas fa-clock"></i>
                            <span class="font-mono text-lg font-bold" id="countdown">15</span>
                            <span>detik lagi</span>
                        </div>
                    </div>

                    <!-- Token Info -->
                    <div id="token-info" class="mt-4 text-center hidden">
                        <p class="text-sm text-gray-500 mb-1">Token Aktif:</p>
                        <code id="token-display" class="bg-gray-100 px-3 py-1 rounded text-sm font-mono"></code>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center mt-4">
                    <button id="btn-start" onclick="startSession()" 
                        class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors font-semibold shadow-lg">
                        <i class="fas fa-play mr-2"></i>Mulai Sesi
                    </button>
                    <button id="btn-refresh" onclick="refreshToken()" 
                        class="hidden px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-semibold">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh Manual
                    </button>
                    <button id="btn-stop" onclick="stopSession()" 
                        class="hidden px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-semibold">
                        <i class="fas fa-stop mr-2"></i>Hentikan Sesi
                    </button>
                </div>
            </div>
        </div>

        <!-- Manual Attendance Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-user-check mr-2"></i>Absensi Manual Siswa
                </h3>
            </div>
            <div class="p-6">
                <!-- Manual Attendance Container -->
                <div id="manual-absensi-container" class="hidden">
                    <p class="text-gray-600 mb-4">Gunakan fitur ini jika siswa tidak membawa HP/tidak dapat melakukan scan QRIS.</p>
                    <form id="form-manual-absen" onsubmit="submitManualAbsen(event)">
                        <div class="mb-4">
                            <label for="siswa-select" class="block text-sm font-medium text-gray-700 mb-2">Pilih Siswa</label>
                            <select id="siswa-select" name="siswa_id" class="w-full text-gray-800" required>
                                <option value=""></option>
                            </select>
                        </div>
                        <button type="submit" id="btn-manual-submit"
                            class="w-full px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors font-semibold shadow-lg">
                            <i class="fas fa-check mr-2"></i>Catat Kehadiran
                        </button>
                    </form>
                </div>
                
                <!-- Placeholder when session is inactive -->
                <div id="manual-absensi-placeholder" class="text-center py-6 text-gray-400">
                    <i class="fas fa-info-circle text-4xl mb-2 text-gray-300"></i>
                    <p class="text-lg font-medium">Mulai sesi shalat terlebih dahulu untuk melakukan absensi manual</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats & Attendance Sidebar -->
    <div class="space-y-6">
        <!-- Live Stats Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-chart-line mr-2"></i>Real-time
                </h3>
            </div>
            <div class="p-6">
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-1">Total Hadir Hari Ini</p>
                    <p id="total-hadir" class="text-5xl font-bold text-green-600">0</p>
                </div>
            </div>
        </div>

        <!-- Today's Sessions -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-history mr-2"></i>Sesi Hari Ini
                </h3>
            </div>
            <div class="p-4">
                <div id="sessions-list" class="space-y-2 max-h-64 overflow-y-auto">
                    <?php if (empty($todaySessions)): ?>
                        <p class="text-gray-400 text-center py-4 text-sm">Belum ada sesi</p>
                    <?php else: ?>
                        <?php foreach ($todaySessions as $session): ?>
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg text-sm">
                                <div>
                                    <span class="font-mono text-xs"><?= substr($session['token'], 0, 8) ?>...</span>
                                    <span class="text-gray-400 mx-1">|</span>
                                    <span class="text-green-600 font-semibold"><?= $session['total_hadir'] ?> hadir</span>
                                    <span class="text-gray-400 mx-1">|</span>
                                    <span class="text-xs text-gray-500"><?= esc($session['nama_guru'] ?? '-') ?></span>
                                </div>
                                <span class="text-xs text-gray-400"><?= date('H:i', strtotime($session['created_at'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Live Attendance List -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 text-white px-6 py-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-users mr-2"></i>Daftar Hadir
                </h3>
            </div>
            <div class="p-4">
                <div id="attendance-list" class="space-y-2 max-h-64 overflow-y-auto">
                    <?php if (empty($todayAttendance)): ?>
                        <p class="text-gray-400 text-center py-4 text-sm">Belum ada yang hadir</p>
                    <?php else: ?>
                        <?php foreach ($todayAttendance as $att): ?>
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg text-sm attendance-item">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <div>
                                        <p class="font-medium"><?= $att['nama_lengkap'] ?></p>
                                        <p class="text-xs text-gray-400"><?= $att['nis'] ?> - <?= $att['nama_kelas'] ?? '' ?></p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400"><?= date('H:i:s', strtotime($att['waktu_absen'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>

<script>
let refreshInterval = null;
let countdownInterval = null;
let countdownValue = 15;
let isSessionActive = false;
let csrfName = '<?= csrf_token() ?>';
let csrfHash = '<?= csrf_hash() ?>';

async function refreshCsrfToken() {
    const res = await fetch('<?= base_url("csrf-token") ?>', { credentials: 'same-origin' });
    const data = await res.json();
    csrfName = data.tokenName;
    csrfHash = data.tokenValue;
}

async function postRequest(url, callback) {
    try {
        await refreshCsrfToken();
        const formData = new FormData();
        formData.append(csrfName, csrfHash);
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        const data = await res.json();
        callback(data);
    } catch (err) {
        console.error('Error:', err);
        Swal.fire({
            title: 'Terjadi Kesalahan',
            text: 'Terjadi kesalahan pada server. Silakan coba lagi.',
            icon: 'error',
            confirmButtonColor: '#10b981'
        });
    }
}

function startSession() {
    postRequest('<?= base_url("/guru/absensi-shalat/generate-token") ?>', data => {
        if (data.success) {
            isSessionActive = true;
            showQRCode(data.token, data.scan_url);
            startAutoRefresh();
            updateUI(true);
            refreshStats();
        } else {
            Swal.fire({
                title: 'Gagal',
                text: data.message || 'Gagal memulai sesi.',
                icon: 'error',
                confirmButtonColor: '#10b981'
            });
        }
    });
}

function refreshToken() {
    postRequest('<?= base_url("/guru/absensi-shalat/generate-token") ?>', data => {
        if (data.success) {
            showQRCode(data.token, data.scan_url);
            resetCountdown();
            refreshStats();
        }
    });
}

function stopSession() {
    postRequest('<?= base_url("/guru/absensi-shalat/stop-session") ?>', data => {
        if (data.success) {
            isSessionActive = false;
            clearInterval(refreshInterval);
            clearInterval(countdownInterval);
            updateUI(false);
            document.getElementById('qr-placeholder').classList.remove('hidden');
            document.getElementById('qr-code').classList.add('hidden');
            document.getElementById('countdown-container').classList.add('hidden');
            document.getElementById('token-info').classList.add('hidden');
            refreshStats();
        }
    });
}

function showQRCode(token, scanUrl) {
    const canvas = document.getElementById('qr-canvas');
    const qrPlaceholder = document.getElementById('qr-placeholder');
    const qrCode = document.getElementById('qr-code');
    const tokenDisplay = document.getElementById('token-display');
    const tokenInfo = document.getElementById('token-info');
    const countdownContainer = document.getElementById('countdown-container');

    QRCode.toCanvas(canvas, scanUrl, {
        width: 256,
        margin: 2,
        color: {
            dark: '#000000',
            light: '#ffffff'
        }
    }, function(error) {
        if (error) {
            console.error(error);
            return;
        }
        qrPlaceholder.classList.add('hidden');
        qrCode.classList.remove('hidden');
        tokenDisplay.textContent = token;
        tokenInfo.classList.remove('hidden');
        countdownContainer.classList.remove('hidden');
    });
}

function startAutoRefresh() {
    clearInterval(refreshInterval);
    resetCountdown();

    // Auto refresh every 15 seconds
    refreshInterval = setInterval(() => {
        refreshToken();
    }, 15000);
}

function resetCountdown() {
    clearInterval(countdownInterval);
    countdownValue = 15;
    document.getElementById('countdown').textContent = countdownValue;

    countdownInterval = setInterval(() => {
        countdownValue--;
        document.getElementById('countdown').textContent = countdownValue;

        if (countdownValue <= 0) {
            countdownValue = 15;
        }
    }, 1000);
}

function updateUI(active) {
    const badge = document.getElementById('status-badge');
    const btnStart = document.getElementById('btn-start');
    const btnRefresh = document.getElementById('btn-refresh');
    const btnStop = document.getElementById('btn-stop');
    
    const manualContainer = document.getElementById('manual-absensi-container');
    const manualPlaceholder = document.getElementById('manual-absensi-placeholder');

    if (active) {
        badge.className = 'px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700';
        badge.innerHTML = '<i class="fas fa-circle text-green-500 mr-1 animate-pulse"></i>Sesi Aktif';
        btnStart.classList.add('hidden');
        btnRefresh.classList.remove('hidden');
        btnStop.classList.remove('hidden');
        
        if (manualContainer) manualContainer.classList.remove('hidden');
        if (manualPlaceholder) manualPlaceholder.classList.add('hidden');
        initSelect2();
    } else {
        badge.className = 'px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-600';
        badge.innerHTML = '<i class="fas fa-circle text-gray-400 mr-1"></i>Belum Aktif';
        btnStart.classList.remove('hidden');
        btnRefresh.classList.add('hidden');
        btnStop.classList.add('hidden');
        
        if (manualContainer) manualContainer.classList.add('hidden');
        if (manualPlaceholder) manualPlaceholder.classList.remove('hidden');
    }
}

function refreshStats() {
    fetch('<?= base_url("/guru/absensi-shalat/stats") ?>', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('total-hadir').textContent = data.total_hadir;

            // Update attendance list
            const attList = document.getElementById('attendance-list');
            if (data.attendance && data.attendance.length > 0) {
                attList.innerHTML = '';
                data.attendance.forEach(att => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center justify-between p-2 bg-gray-50 rounded-lg text-sm';
                    div.innerHTML = `
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <div>
                                <p class="font-medium">${att.nama_lengkap}</p>
                                <p class="text-xs text-gray-400">${att.nis} - ${att.nama_kelas || ''}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">${att.waktu_absen ? att.waktu_absen.substring(11, 19) : ''}</span>
                    `;
                    attList.appendChild(div);
                });
            }

            // Update sessions list
            if (data.sessions && data.sessions.length > 0) {
                const sessList = document.getElementById('sessions-list');
                sessList.innerHTML = '';
                data.sessions.forEach(sess => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center justify-between p-2 bg-gray-50 rounded-lg text-sm';
                    div.innerHTML = `
                        <div>
                            <span class="font-mono text-xs">${sess.token.substring(0, 8)}...</span>
                            <span class="text-gray-400 mx-1">|</span>
                            <span class="text-green-600 font-semibold">${sess.total_hadir} hadir</span>
                            <span class="text-gray-400 mx-1">|</span>
                            <span class="text-xs text-gray-500">${sess.nama_guru || '-'}</span>
                        </div>
                        <span class="text-xs text-gray-400">${new Date(sess.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}</span>
                    `;
                    sessList.appendChild(div);
                });
            }
        }
    });
}

// Poll stats every 5 seconds for real-time updates
setInterval(refreshStats, 5000);

// Initial stats load
refreshStats();

let select2Initialized = false;

function initSelect2() {
    if (select2Initialized) return;
    
    $('#siswa-select').select2({
        placeholder: 'Cari Nama atau NIS Siswa...',
        minimumInputLength: 2,
        width: '100%',
        ajax: {
            url: '<?= base_url("/guru/absensi-shalat/search-siswa") ?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            },
            cache: true
        }
    });
    
    select2Initialized = true;
}

async function submitManualAbsen(event) {
    event.preventDefault();
    
    const siswaSelect = document.getElementById('siswa-select');
    const siswaId = siswaSelect.value;
    
    if (!siswaId) {
        Swal.fire({
            title: 'Peringatan',
            text: 'Harap pilih siswa terlebih dahulu.',
            icon: 'warning',
            confirmButtonColor: '#10b981'
        });
        return;
    }
    
    const btnSubmit = document.getElementById('btn-manual-submit');
    const originalText = btnSubmit.innerHTML;
    
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    
    try {
        await refreshCsrfToken();
        const formData = new FormData();
        formData.append(csrfName, csrfHash);
        formData.append('siswa_id', siswaId);
        
        const res = await fetch('<?= base_url("/guru/absensi-shalat/absen-manual") ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        
        const data = await res.json();
        
        if (data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: data.message,
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            });
            $('#siswa-select').val(null).trigger('change');
            refreshStats();
        } else {
            Swal.fire({
                title: 'Gagal',
                text: data.message || 'Gagal merekam absensi manual.',
                icon: 'warning',
                confirmButtonColor: '#10b981'
            });
        }
    } catch (err) {
        console.error('Error:', err);
        Swal.fire({
            title: 'Terjadi Kesalahan',
            text: 'Terjadi kesalahan saat memproses absensi manual.',
            icon: 'error',
            confirmButtonColor: '#10b981'
        });
    } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = originalText;
    }
}

// Auto-restore session on page refresh
document.addEventListener('DOMContentLoaded', function() {
    const activeSession = <?= $activeSession ? json_encode($activeSession) : 'null' ?>;
    if (activeSession) {
        isSessionActive = true;
        const scanUrl = '<?= base_url("/scan") ?>?token=' + activeSession.token;
        showQRCode(activeSession.token, scanUrl);
        startAutoRefresh();
        updateUI(true);
        refreshStats();
    }
});
</script>

<!-- Custom Select2 Styles for premium feel -->
<style>
.select2-container--default .select2-selection--single {
    border-color: #e2e8f0;
    border-radius: 0.75rem;
    height: 3rem;
    display: flex;
    align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 2.8rem;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #4a5568;
    padding-left: 1rem;
}
.select2-dropdown {
    border-color: #e2e8f0;
    border-radius: 0.75rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>

<!-- Select2 & SweetAlert2 CSS & JS dependencies -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?= $this->endSection() ?>
