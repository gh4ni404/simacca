<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-user-check text-blue-600 mr-2"></i>Monitoring Absensi Guru
            </h2>
            <p class="text-gray-600">Real-time monitoring kehadiran guru hari ini</p>
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <i class="fas fa-sync-alt mr-2" id="refresh-icon"></i>
                <span>Auto-refresh: <span id="countdown" class="font-semibold text-blue-600">30</span>s</span>
                <button id="toggle-refresh" class="ml-3 px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-xs transition" onclick="toggleAutoRefresh()">
                    <i class="fas fa-pause"></i> Pause
                </button>
            </div>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="<?= base_url('admin/absensi-guru/laporan') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-chart-bar mr-2"></i> Laporan
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Summary Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6" id="summary-cards">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-blue-600 uppercase font-semibold">Total Guru</p>
                    <p class="text-2xl font-bold text-blue-800" id="total-guru"><?= $summary['total_guru'] ?? 0 ?></p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <i class="fas fa-sign-in-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-green-600 uppercase font-semibold">Sudah Check-In</p>
                    <p class="text-2xl font-bold text-green-800" id="sudah-checkin"><?= $summary['sudah_checkin'] ?? 0 ?></p>
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-yellow-600 uppercase font-semibold">Belum Check-In</p>
                    <p class="text-2xl font-bold text-yellow-800" id="belum-checkin"><?= $summary['belum_checkin'] ?? 0 ?></p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <i class="fas fa-sign-out-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-purple-600 uppercase font-semibold">Sudah Check-Out</p>
                    <p class="text-2xl font-bold text-purple-800" id="sudah-checkout"><?= $summary['sudah_checkout'] ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6" id="status-distribution">
        <div class="bg-white border-2 border-green-200 rounded-lg p-3 text-center">
            <p class="text-sm text-green-600 font-semibold mb-1">Hadir</p>
            <p class="text-2xl font-bold text-green-700"><?= $summary['hadir'] ?? 0 ?></p>
        </div>
        <div class="bg-white border-2 border-yellow-200 rounded-lg p-3 text-center">
            <p class="text-sm text-yellow-600 font-semibold mb-1">Terlambat</p>
            <p class="text-2xl font-bold text-yellow-700"><?= $summary['terlambat'] ?? 0 ?></p>
        </div>
        <div class="bg-white border-2 border-blue-200 rounded-lg p-3 text-center">
            <p class="text-sm text-blue-600 font-semibold mb-1">Izin</p>
            <p class="text-2xl font-bold text-blue-700"><?= $summary['izin'] ?? 0 ?></p>
        </div>
        <div class="bg-white border-2 border-indigo-200 rounded-lg p-3 text-center">
            <p class="text-sm text-indigo-600 font-semibold mb-1">Sakit</p>
            <p class="text-2xl font-bold text-indigo-700"><?= $summary['sakit'] ?? 0 ?></p>
        </div>
        <div class="bg-white border-2 border-red-200 rounded-lg p-3 text-center">
            <p class="text-sm text-red-600 font-semibold mb-1">Alpha</p>
            <p class="text-2xl font-bold text-red-700"><?= $summary['alpha'] ?? 0 ?></p>
        </div>
        <div class="bg-white border-2 border-gray-200 rounded-lg p-3 text-center">
            <p class="text-sm text-gray-600 font-semibold mb-1">Belum</p>
            <p class="text-2xl font-bold text-gray-700"><?= $summary['belum_absen'] ?? 0 ?></p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="tanggal" value="<?= $filters['tanggal'] ?? date('Y-m-d') ?>"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Guru</label>
                <select name="guru_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Guru</option>
                    <?php foreach ($guruList as $guru): ?>
                        <option value="<?= $guru['id'] ?>" <?= ($filters['guru_id'] ?? '') == $guru['id'] ? 'selected' : '' ?>>
                            <?= esc($guru['nama_lengkap']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="hadir" <?= ($filters['status'] ?? '') == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                    <option value="terlambat" <?= ($filters['status'] ?? '') == 'terlambat' ? 'selected' : '' ?>>Terlambat</option>
                    <option value="izin" <?= ($filters['status'] ?? '') == 'izin' ? 'selected' : '' ?>>Izin</option>
                    <option value="sakit" <?= ($filters['status'] ?? '') == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                    <option value="alpha" <?= ($filters['status'] ?? '') == 'alpha' ? 'selected' : '' ?>>Alpha</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <?php if (!empty($filters['tanggal']) || !empty($filters['guru_id']) || !empty($filters['status'])): ?>
                    <a href="<?= base_url('admin/absensi-guru') ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Guru</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="absensi-table">
                <?php if (!empty($absensiList)): ?>
                    <?php foreach ($absensiList as $index => $absensi): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $index + 1 ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= esc($absensi['nama_guru']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if ($absensi['check_in']): ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <?= date('H:i', strtotime($absensi['check_in'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if ($absensi['check_out']): ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?= date('H:i', strtotime($absensi['check_out'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900" data-field="keterangan" data-id="<?= $absensi['id'] ?>">
                                <span class="keterangan-display"><?= esc($absensi['keterangan_masuk'] ?? '-') ?></span>
                                <textarea class="keterangan-edit hidden w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2"><?= esc($absensi['keterangan_masuk'] ?? '') ?></textarea>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center" data-field="status" data-id="<?= $absensi['id'] ?>">
                                <?php
                                $badgeColors = [
                                    'hadir' => 'bg-green-100 text-green-800 border-green-200',
                                    'terlambat' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'izin' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'sakit' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                    'alpha' => 'bg-red-100 text-red-800 border-red-200'
                                ];
                                $colorClass = $badgeColors[$absensi['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                ?>
                                <span class="status-display px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?= $colorClass ?>">
                                    <?= ucfirst($absensi['status']) ?>
                                </span>
                                <select class="status-edit hidden border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="hadir" <?= $absensi['status'] == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                    <option value="terlambat" <?= $absensi['status'] == 'terlambat' ? 'selected' : '' ?>>Terlambat</option>
                                    <option value="izin" <?= $absensi['status'] == 'izin' ? 'selected' : '' ?>>Izin</option>
                                    <option value="sakit" <?= $absensi['status'] == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                                    <option value="alpha" <?= $absensi['status'] == 'alpha' ? 'selected' : '' ?>>Alpha</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?= base_url('admin/absensi-guru/detail/' . $absensi['guru_id']) ?>" 
                                       class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn-edit inline-flex items-center px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition"
                                            data-id="<?= $absensi['id'] ?>"
                                            onclick="toggleEditMode(<?= $absensi['id'] ?>)"
                                            title="Edit Status & Keterangan">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn-save hidden inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg transition"
                                            data-id="<?= $absensi['id'] ?>"
                                            onclick="saveInlineEdit(<?= $absensi['id'] ?>)"
                                            title="Simpan">
                                        <i class="fas fa-save"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn-cancel hidden inline-flex items-center px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition"
                                            data-id="<?= $absensi['id'] ?>"
                                            onclick="cancelEditMode(<?= $absensi['id'] ?>)"
                                            title="Batal">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Tidak ada data absensi</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
let refreshInterval;
let refreshEnabled = true;
let countdown = 30;

function toggleAutoRefresh() {
    refreshEnabled = !refreshEnabled;
    const btn = document.getElementById('toggle-refresh');
    
    if (refreshEnabled) {
        btn.innerHTML = '<i class="fas fa-pause"></i> Pause';
        startAutoRefresh();
    } else {
        btn.innerHTML = '<i class="fas fa-play"></i> Resume';
        clearInterval(refreshInterval);
    }
}

function startAutoRefresh() {
    countdown = 30;
    refreshInterval = setInterval(() => {
        if (refreshEnabled) {
            countdown--;
            document.getElementById('countdown').textContent = countdown;
            
            if (countdown <= 0) {
                refreshData();
                countdown = 30;
            }
        }
    }, 1000);
}

function refreshData() {
    const icon = document.getElementById('refresh-icon');
    icon.classList.add('fa-spin');
    
    // Get current filter values
    const urlParams = new URLSearchParams(window.location.search);
    const filterParams = {
        tanggal: urlParams.get('tanggal') || '',
        guru_id: urlParams.get('guru_id') || '',
        status: urlParams.get('status') || '',
        ajax: '1'
    };
    
    // Build query string
    const queryString = Object.keys(filterParams)
        .filter(key => filterParams[key])
        .map(key => `${key}=${encodeURIComponent(filterParams[key])}`)
        .join('&');
    
    fetch(`<?= base_url('admin/absensi-guru') ?>?${queryString}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateSummaryCards(data.summary);
            updateStatusDistribution(data.summary);
            updateTable(data.absensiList);
        }
        icon.classList.remove('fa-spin');
    })
    .catch(error => {
        console.error('Auto-refresh error:', error);
        icon.classList.remove('fa-spin');
    });
}

function updateSummaryCards(summary) {
    document.getElementById('total-guru').textContent = summary.total_guru || 0;
    document.getElementById('sudah-checkin').textContent = summary.sudah_checkin || 0;
    document.getElementById('belum-checkin').textContent = summary.belum_checkin || 0;
    document.getElementById('sudah-checkout').textContent = summary.sudah_checkout || 0;
}

function updateStatusDistribution(summary) {
    const container = document.getElementById('status-distribution');
    if (!container) return;
    
    container.innerHTML = `
        <div class="bg-white border-2 border-green-200 rounded-lg p-3 text-center">
            <p class="text-sm text-green-600 font-semibold mb-1">Hadir</p>
            <p class="text-2xl font-bold text-green-700">${summary.hadir || 0}</p>
        </div>
        <div class="bg-white border-2 border-yellow-200 rounded-lg p-3 text-center">
            <p class="text-sm text-yellow-600 font-semibold mb-1">Terlambat</p>
            <p class="text-2xl font-bold text-yellow-700">${summary.terlambat || 0}</p>
        </div>
        <div class="bg-white border-2 border-blue-200 rounded-lg p-3 text-center">
            <p class="text-sm text-blue-600 font-semibold mb-1">Izin</p>
            <p class="text-2xl font-bold text-blue-700">${summary.izin || 0}</p>
        </div>
        <div class="bg-white border-2 border-indigo-200 rounded-lg p-3 text-center">
            <p class="text-sm text-indigo-600 font-semibold mb-1">Sakit</p>
            <p class="text-2xl font-bold text-indigo-700">${summary.sakit || 0}</p>
        </div>
        <div class="bg-white border-2 border-red-200 rounded-lg p-3 text-center">
            <p class="text-sm text-red-600 font-semibold mb-1">Alpha</p>
            <p class="text-2xl font-bold text-red-700">${summary.alpha || 0}</p>
        </div>
        <div class="bg-white border-2 border-gray-200 rounded-lg p-3 text-center">
            <p class="text-sm text-gray-600 font-semibold mb-1">Belum</p>
            <p class="text-2xl font-bold text-gray-700">${summary.belum_absen || 0}</p>
        </div>
    `;
}

// Helper function to get badge color classes
function getBadgeClass(status) {
    const badgeColors = {
        'hadir': 'bg-green-100 text-green-800 border-green-200',
        'terlambat': 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'izin': 'bg-blue-100 text-blue-800 border-blue-200',
        'sakit': 'bg-indigo-100 text-indigo-800 border-indigo-200',
        'alpha': 'bg-red-100 text-red-800 border-red-200'
    };
    return badgeColors[status] || 'bg-gray-100 text-gray-800 border-gray-200';
}

function updateTable(absensiList) {
    const tbody = document.getElementById('absensi-table');
    if (!tbody) return;
    
    if (!absensiList || absensiList.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p>Tidak ada data absensi</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = absensiList.map((absensi, index) => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${absensi.nama_guru || '-'}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                ${absensi.check_in ? 
                    `<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        ${absensi.check_in}
                    </span>` : 
                    '<span class="text-gray-400">-</span>'
                }
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                ${absensi.check_out ? 
                    `<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        ${absensi.check_out}
                    </span>` : 
                    '<span class="text-gray-400">-</span>'
                }
            </td>
            <td class="px-6 py-4 text-sm text-gray-900" data-field="keterangan" data-id="${absensi.id}">
                <span class="keterangan-display">${absensi.keterangan || '-'}</span>
                <textarea class="keterangan-edit hidden w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2">${absensi.keterangan || ''}</textarea>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center" data-field="status" data-id="${absensi.id}">
                <span class="status-display px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border ${getBadgeClass(absensi.status)}">
                    ${absensi.status ? absensi.status.charAt(0).toUpperCase() + absensi.status.slice(1) : '-'}
                </span>
                <select class="status-edit hidden border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="hadir" ${absensi.status === 'hadir' ? 'selected' : ''}>Hadir</option>
                    <option value="terlambat" ${absensi.status === 'terlambat' ? 'selected' : ''}>Terlambat</option>
                    <option value="izin" ${absensi.status === 'izin' ? 'selected' : ''}>Izin</option>
                    <option value="sakit" ${absensi.status === 'sakit' ? 'selected' : ''}>Sakit</option>
                    <option value="alpha" ${absensi.status === 'alpha' ? 'selected' : ''}>Alpha</option>
                </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                <div class="flex items-center justify-center gap-2">
                    <a href="<?= base_url('admin/absensi-guru/detail/') ?>${absensi.guru_id}" 
                       class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition"
                       title="Lihat Detail">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button type="button" 
                            class="btn-edit inline-flex items-center px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition"
                            data-id="${absensi.id}"
                            onclick="toggleEditMode(${absensi.id})"
                            title="Edit Status & Keterangan">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" 
                            class="btn-save hidden inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg transition"
                            data-id="${absensi.id}"
                            onclick="saveInlineEdit(${absensi.id})"
                            title="Simpan">
                        <i class="fas fa-save"></i>
                    </button>
                    <button type="button" 
                            class="btn-cancel hidden inline-flex items-center px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition"
                            data-id="${absensi.id}"
                            onclick="cancelEditMode(${absensi.id})"
                            title="Batal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Inline editing functions
let originalValues = {};

function toggleEditMode(id) {
    const row = document.querySelector(`tr:has([data-id="${id}"])`);
    if (!row) return;
    
    // Store original values
    const statusCell = row.querySelector('[data-field="status"]');
    const keteranganCell = row.querySelector('[data-field="keterangan"]');
    
    originalValues[id] = {
        status: statusCell.querySelector('.status-edit').value,
        keterangan: keteranganCell.querySelector('.keterangan-edit').value
    };
    
    // Toggle display/edit elements
    statusCell.querySelector('.status-display').classList.add('hidden');
    statusCell.querySelector('.status-edit').classList.remove('hidden');
    
    keteranganCell.querySelector('.keterangan-display').classList.add('hidden');
    keteranganCell.querySelector('.keterangan-edit').classList.remove('hidden');
    
    // Toggle buttons
    row.querySelector(`.btn-edit[data-id="${id}"]`).classList.add('hidden');
    row.querySelector(`.btn-save[data-id="${id}"]`).classList.remove('hidden');
    row.querySelector(`.btn-cancel[data-id="${id}"]`).classList.remove('hidden');
    
    // Highlight row
    row.classList.add('bg-blue-50', 'border-2', 'border-blue-300');
}

function cancelEditMode(id) {
    const row = document.querySelector(`tr:has([data-id="${id}"])`);
    if (!row) return;
    
    const statusCell = row.querySelector('[data-field="status"]');
    const keteranganCell = row.querySelector('[data-field="keterangan"]');
    
    // Restore original values
    if (originalValues[id]) {
        statusCell.querySelector('.status-edit').value = originalValues[id].status;
        keteranganCell.querySelector('.keterangan-edit').value = originalValues[id].keterangan;
    }
    
    // Toggle display/edit elements
    statusCell.querySelector('.status-display').classList.remove('hidden');
    statusCell.querySelector('.status-edit').classList.add('hidden');
    
    keteranganCell.querySelector('.keterangan-display').classList.remove('hidden');
    keteranganCell.querySelector('.keterangan-edit').classList.add('hidden');
    
    // Toggle buttons
    row.querySelector(`.btn-edit[data-id="${id}"]`).classList.remove('hidden');
    row.querySelector(`.btn-save[data-id="${id}"]`).classList.add('hidden');
    row.querySelector(`.btn-cancel[data-id="${id}"]`).classList.add('hidden');
    
    // Remove highlight
    row.classList.remove('bg-blue-50', 'border-2', 'border-blue-300');
    
    // Clear stored values
    delete originalValues[id];
}

function saveInlineEdit(id) {
    const row = document.querySelector(`tr:has([data-id="${id}"])`);
    if (!row) return;
    
    const statusCell = row.querySelector('[data-field="status"]');
    const keteranganCell = row.querySelector('[data-field="keterangan"]');
    
    const newStatus = statusCell.querySelector('.status-edit').value;
    const newKeterangan = keteranganCell.querySelector('.keterangan-edit').value;
    
    // Disable save button and show loading
    const saveBtn = row.querySelector(`.btn-save[data-id="${id}"]`);
    const originalHTML = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    saveBtn.disabled = true;
    
    // Create form data
    const formData = new FormData();
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    formData.append('absensi_id', id);
    formData.append('status', newStatus);
    formData.append('keterangan', newKeterangan);
    
    fetch('<?= base_url('admin/absensi-guru/update-status') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success toast
            showToast('Status dan keterangan berhasil diupdate', 'success');
            
            // Exit edit mode
            cancelEditMode(id);
            
            // Refresh data to update display
            refreshData();
        } else {
            showToast('Gagal update: ' + (data.message || 'Terjadi kesalahan'), 'error');
            saveBtn.innerHTML = originalHTML;
            saveBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat menyimpan data', 'error');
        saveBtn.innerHTML = originalHTML;
        saveBtn.disabled = false;
    });
}

// Show toast notification
function showToast(message, type = 'success') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 ${colors[type] || colors.success} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center`;
    toast.style.transform = 'translateX(400px)';
    toast.innerHTML = `<i class="fas fa-${icons[type] || icons.success} mr-2"></i>${message}`;
    document.body.appendChild(toast);
    
    // Slide in animation
    setTimeout(() => {
        toast.style.transition = 'transform 0.3s ease-out';
        toast.style.transform = 'translateX(0)';
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Start auto-refresh on page load
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
});
</script>

<?= $this->endSection() ?>
