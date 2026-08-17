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
    <div class="lg:col-span-2">
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
                                        <?php if (!empty($att['nama_guru_piket'])): ?>
                                            <p class="text-xs text-blue-500"><i class="fas fa-chalkboard-teacher mr-1"></i><?= esc($att['nama_guru_piket']) ?></p>
                                        <?php endif; ?>
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
        alert('Terjadi kesalahan. Silakan coba lagi.');
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
            alert(data.message || 'Gagal memulai sesi');
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

    if (active) {
        badge.className = 'px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700';
        badge.innerHTML = '<i class="fas fa-circle text-green-500 mr-1 animate-pulse"></i>Sesi Aktif';
        btnStart.classList.add('hidden');
        btnRefresh.classList.remove('hidden');
        btnStop.classList.remove('hidden');
    } else {
        badge.className = 'px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-600';
        badge.innerHTML = '<i class="fas fa-circle text-gray-400 mr-1"></i>Belum Aktif';
        btnStart.classList.remove('hidden');
        btnRefresh.classList.add('hidden');
        btnStop.classList.add('hidden');
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
                                ${att.nama_guru_piket ? '<p class="text-xs text-blue-500"><i class="fas fa-chalkboard-teacher mr-1"></i>' + att.nama_guru_piket + '</p>' : ''}
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

<?= $this->endSection() ?>
