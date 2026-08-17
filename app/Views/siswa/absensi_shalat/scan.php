<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Shalat - Scan QR Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
            min-height: 100vh;
        }
        #qr-reader {
            border: 3px solid #0d9488;
            border-radius: 1rem;
            overflow: hidden;
        }
        #qr-reader video {
            border-radius: 0.5rem;
        }
        .scan-region video {
            object-fit: cover;
            max-height: 70vh;
        }
        #qr-reader__scan_region {
            min-height: 250px;
        }
        #qr-reader__dashboard {
            display: none !important;
        }
        .pulse-green {
            animation: pulseGreen 2s infinite;
        }
        @keyframes pulseGreen {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            50% { box-shadow: 0 0 0 15px rgba(16, 185, 129, 0); }
        }
        .slide-up {
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Header Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden mb-4">
            <div class="bg-gradient-to-r from-teal-600 to-teal-700 text-white px-6 py-4 text-center">
                <i class="fas fa-mosque text-3xl mb-2"></i>
                <h1 class="text-xl font-bold">Absensi Shalat</h1>
                <p class="text-teal-100 text-sm">Scan QR Code untuk mencatat kehadiran</p>
            </div>
            <div class="px-6 py-3 bg-teal-50">
                <p class="text-sm text-center text-gray-600">
                    <i class="fas fa-user mr-1 text-teal-600"></i>
                    <strong><?= $siswa['nama_lengkap'] ?? 'Siswa' ?></strong>
                    <span class="text-gray-400 mx-1">|</span>
                    <span class="text-teal-700"><?= $siswa['nama_kelas'] ?? '' ?></span>
                </p>
            </div>
        </div>

        <!-- Scanner Card -->
        <div id="scanner-card" class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-4">
                <div id="qr-reader" class="w-full"></div>
            </div>

            <!-- Status Overlay -->
            <div id="status-overlay" class="hidden px-6 py-4 text-center">
                <div id="status-icon" class="text-5xl mb-3"></div>
                <p id="status-message" class="text-lg font-semibold"></p>
                <p id="status-sub" class="text-sm text-gray-500 mt-1"></p>
            </div>

            <!-- Manual Input Fallback -->
            <div class="px-6 pb-4">
                <div class="border-t pt-4">
                    <p class="text-xs text-gray-400 text-center mb-3">Atau masukkan token secara manual:</p>
                    <div class="flex gap-2">
                        <input type="text" id="manual-token" placeholder="Masukkan token dari QR Code"
                            class="flex-1 px-4 py-2 border-2 border-gray-200 rounded-xl text-sm focus:border-teal-500 focus:outline-none">
                        <button onclick="submitManualToken()" 
                            class="px-4 py-2 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors font-medium">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 mt-4 text-white text-sm">
            <h3 class="font-semibold mb-2"><i class="fas fa-info-circle mr-1"></i>Cara Penggunaan:</h3>
            <ol class="list-decimal list-inside space-y-1 text-teal-100">
                <li>Mintalah QR Code kepada guru piket</li>
                <li>Arahkan kamera ke QR Code</li>
                <li>Tunggu hingga muncul notifikasi keberhasilan</li>
            </ol>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center max-w-sm w-full slide-up">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 pulse-green">
                <i class="fas fa-check text-4xl text-green-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Berhasil!</h2>
            <p id="modal-message" class="text-gray-600 mb-1">Absensi shalat kamu sudah tercatat.</p>
            <p id="modal-time" class="text-sm text-gray-400 mb-6"></p>
            <button onclick="closeModal()" 
                class="w-full px-6 py-3 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors font-semibold">
                Tutup
            </button>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="error-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center max-w-sm w-full slide-up">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-times text-4xl text-red-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Gagal</h2>
            <p id="error-message" class="text-gray-600 mb-6"></p>
            <button onclick="closeErrorModal()" 
                class="w-full px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-semibold">
                Coba Lagi
            </button>
        </div>
    </div>

    <!-- QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        const csrfToken = '<?= csrf_hash() ?>';
        const csrfName = '<?= csrf_token() ?>';
        let html5QrCode = null;
        let isProcessing = false;

        // Extract token from URL if present
        const urlParams = new URLSearchParams(window.location.search);
        const preToken = urlParams.get('token');

        if (preToken) {
            document.getElementById('manual-token').value = preToken;
            // Auto-submit pre-filled token
            setTimeout(() => submitManualToken(), 1000);
        }

        // Start camera scanner
        function startScanner() {
            html5QrCode = new Html5Qrcode("qr-reader");

            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error("Camera start failed:", err);
                document.getElementById('qr-reader').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-camera text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 text-sm">Kamera tidak tersedia</p>
                        <p class="text-gray-400 text-xs">Gunakan input manual di bawah</p>
                    </div>
                `;
            });
        }

        function onScanSuccess(decodedText) {
            if (isProcessing) return;
            isProcessing = true;

            // Stop scanner
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().catch(console.error);
            }

            // Extract token from URL
            let token = decodedText;
            try {
                const url = new URL(decodedText);
                token = url.searchParams.get('token') || decodedText;
            } catch(e) {
                // Not a URL, use as-is
            }

            submitToken(token);
        }

        function onScanFailure(error) {
            // Scan failure is normal (no QR in frame), ignore
        }

        function submitManualToken() {
            const token = document.getElementById('manual-token').value.trim();
            if (!token) {
                showError('Masukkan token terlebih dahulu');
                return;
            }
            submitToken(token);
        }

        function submitToken(token) {
            showLoading();

            const formData = new FormData();
            formData.append('token', token);
            formData.append(csrfName, csrfToken);

            fetch('<?= base_url("/api/attendance/scan") ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                isProcessing = false;
                if (data.success) {
                    showSuccess(data.message, data.waktu);
                } else {
                    showError(data.message);
                    restartScanner();
                }
            })
            .catch(err => {
                isProcessing = false;
                console.error('Error:', err);
                showError('Terjadi kesalahan jaringan. Coba lagi.');
                restartScanner();
            });
        }

        function showLoading() {
            const overlay = document.getElementById('status-overlay');
            overlay.classList.remove('hidden');
            document.getElementById('status-icon').innerHTML = '<i class="fas fa-spinner fa-spin text-teal-600"></i>';
            document.getElementById('status-message').textContent = 'Memproses...';
            document.getElementById('status-message').className = 'text-lg font-semibold text-teal-600';
            document.getElementById('status-sub').textContent = '';
        }

        function showSuccess(message, waktu) {
            document.getElementById('status-overlay').classList.add('hidden');
            document.getElementById('modal-message').textContent = message;
            document.getElementById('modal-time').textContent = waktu ? 'Pukul ' + waktu : '';
            document.getElementById('success-modal').classList.remove('hidden');
        }

        function showError(message) {
            document.getElementById('status-overlay').classList.add('hidden');
            document.getElementById('error-message').textContent = message;
            document.getElementById('error-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('success-modal').classList.add('hidden');
            restartScanner();
        }

        function closeErrorModal() {
            document.getElementById('error-modal').classList.add('hidden');
            restartScanner();
        }

        function restartScanner() {
            setTimeout(() => {
                startScanner();
            }, 500);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            startScanner();
        });
    </script>
</body>
</html>
