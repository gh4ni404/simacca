<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .table-row-hover { transition: all 0.2s ease; }
    .table-row-hover:hover { background-color: #f8fafc; transform: translateX(4px); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl shadow-lg">
                <i class="fas fa-clipboard-check text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Rekap Absensi PKL</span>
                </h1>
                <p class="text-gray-600 mt-1 text-sm">
                    <i class="fas fa-info-circle mr-1"></i> Riwayat kehadiran PKL Anda
                </p>
            </div>
        </div>
        <div>
            <button onclick="openRekapCetakModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fas fa-print"></i> Cetak Rekap Bulanan
            </button>
        </div>
    </div>

    <?= render_flash_message() ?>

    <!-- Stat Cards -->
    <?php
    $totalHari = $statistik['total_hari'] ?? 0;
    $totalHadir = $statistik['hadir'] ?? 0;
    $totalIzin = $statistik['izin'] ?? 0;
    $totalSakit = $statistik['sakit'] ?? 0;
    $totalAlpa = $statistik['alpa'] ?? 0;
    $persenKehadiran = $statistik['persen_kehadiran'] ?? 0;
    ?>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Hari</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $totalHari ?></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Hadir</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $totalHadir ?></p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Izin</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $totalIzin ?></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-file-alt text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Sakit</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $totalSakit ?></p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-medkit text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Alpa</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $totalAlpa ?></p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-user-times text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-emerald-500 col-span-2 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Persentase Kehadiran</p>
                    <p class="text-2xl font-bold <?= $persenKehadiran >= 80 ? 'text-green-600' : ($persenKehadiran >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $persenKehadiran ?>%</p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-full">
                    <i class="fas fa-chart-line text-emerald-600"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full transition-all duration-300"
                         style="width: <?= $persenKehadiran ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rekap Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-list mr-3"></i> Rekap Absensi PKL
                    </h2>
                    <p class="text-blue-100 mt-1">Daftar kehadiran selama masa PKL</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl">
                    <p class="text-sm opacity-90">Total Rekapan</p>
                    <p class="text-3xl font-bold"><?= count($rekap) ?></p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <?php if (empty($rekap)): ?>
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 mb-6">
                    <i class="fas fa-clipboard-list text-5xl text-blue-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">Belum Ada Data Absensi PKL</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">Rekap absensi PKL Anda akan muncul di sini setelah guru pembimbing melakukan pencatatan kehadiran.</p>
            </div>
            <?php else: ?>
                <?php foreach ($groupedByMonth as $month => $items): ?>
                <div class="mb-6 last:mb-0">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="p-2 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg">
                            <i class="fas fa-calendar-alt text-white text-sm"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800"><?= $month ?></h3>
                        <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full"><?= count($items['items']) ?> hari</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Perusahaan</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pembimbing</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $no = 1;
                                $statusClasses = [
                                    'hadir'  => 'bg-green-100 text-green-800',
                                    'izin'   => 'bg-blue-100 text-blue-800',
                                    'sakit'  => 'bg-yellow-100 text-yellow-800',
                                    'alpa'   => 'bg-red-100 text-red-800',
                                    'libur'  => 'bg-purple-100 text-purple-800',
                                ];
                                foreach ($items['items'] as $item):
                                ?>
                                <tr class="table-row-hover">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900"><?= $no++ ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                                <i class="fas fa-calendar-day text-blue-600"></i>
                                            </div>
                                            <div>
                                                <?php
                                                $fmt = new IntlDateFormatter('id_ID', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
                                                $date = new DateTime($item['tanggal']);
                                                ?>
                                                <div class="text-sm font-bold text-gray-900"><?= $fmt->format($date) ?></div>
                                                <div class="text-xs text-gray-500"><?= date('d/m/Y', strtotime($item['tanggal'])) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900"><?= esc($item['nama_perusahaan'] ?? '-') ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900"><?= esc($item['nama_pembimbing'] ?? '-') ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $statusClasses[$item['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                            <i class="fas <?= $item['status'] === 'hadir' ? 'fa-check-circle' : ($item['status'] === 'izin' ? 'fa-file-alt' : ($item['status'] === 'sakit' ? 'fa-medkit' : ($item['status'] === 'alpa' ? 'fa-user-times' : ($item['status'] === 'libur' ? 'fa-umbrella-beach' : 'fa-id-badge')))) ?> mr-1"></i>
                                            <?= ucfirst($item['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?= esc($item['keterangan'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Cetak Rekap -->
<div id="modalCetakRekap"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
    onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
        <div class="px-5 pt-5 pb-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-900">Pilih Bulan</h3>
                <button onclick="document.getElementById('modalCetakRekap').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                <?= get_jurnal_pkl_start_date() ? date('d M Y', strtotime(get_jurnal_pkl_start_date())) . ' – ' . (get_jurnal_pkl_end_date() ? date('d M Y', strtotime(get_jurnal_pkl_end_date())) : '...') : 'Belum diatur' ?>
            </p>
        </div>
        <div id="weekListRekap" class="p-4 space-y-2 max-h-80 overflow-y-auto"></div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
var REKAP_CETAK_BASE_URL = '<?= base_url('siswa/absensi-pkl/cetak-rekap') ?>';
var REKAP_START_DATE = '<?= get_jurnal_pkl_start_date() ?>';
var REKAP_END_DATE = '<?= get_jurnal_pkl_end_date() ?>';

function openRekapCetakModal() {
    document.getElementById('modalCetakRekap').classList.remove('hidden');
    renderRekapMonthList();
}

function buildRekapCetakUrl(monthStr) {
    var url = REKAP_CETAK_BASE_URL + '/' + monthStr;
    printRekapCetak(url);
}

function printRekapCetak(url) {
    document.getElementById('modalCetakRekap').classList.add('hidden');
    var iframe = document.getElementById('printFrameRekap');
    if (!iframe) {
        iframe = document.createElement('iframe');
        iframe.id = 'printFrameRekap';
        iframe.style.cssText = 'position:fixed;top:0;left:0;width:0;height:0;border:none;opacity:0';
        document.body.appendChild(iframe);
    }
    iframe.onload = function () {
        iframe.onload = null;
        setTimeout(function () {
            try {
                var win = iframe.contentWindow;
                win.print();
            } catch (e) {
                window.open(url, '_blank');
            }
        }, 300);
    };
    iframe.src = url;
}

function renderRekapMonthList() {
    var container = document.getElementById('weekListRekap');
    if (!container) return;

    if (!REKAP_START_DATE) {
        container.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">Belum ada pengaturan tanggal PKL</p>';
        return;
    }

    var start = new Date(REKAP_START_DATE + 'T00:00:00');
    var today = new Date(new Date().toISOString().split('T')[0] + 'T00:00:00');
    var end = REKAP_END_DATE ? new Date(REKAP_END_DATE + 'T00:00:00') : new Date(start);
    if (end < start) end = new Date(start);

    var months = [];
    var current = new Date(start.getFullYear(), start.getMonth(), 1);
    var lastMonth = new Date(end.getFullYear(), end.getMonth(), 1);

    while (current <= lastMonth) {
        var monthStart = new Date(current.getFullYear(), current.getMonth(), 1);
        var monthEnd = new Date(current.getFullYear(), current.getMonth() + 1, 0);

        if (monthStart < start) monthStart = new Date(start);
        if (monthEnd > end) monthEnd = new Date(end);

        months.push({
            str: current.getFullYear() + '-' + String(current.getMonth() + 1).padStart(2, '0'),
            label: current.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }),
            start: monthStart,
            end: monthEnd
        });

        current.setMonth(current.getMonth() + 1);
    }

    var html = '';
    months.forEach(function(month) {
        var isCurrentMonth = (today.getMonth() === month.start.getMonth() && today.getFullYear() === month.start.getFullYear());

        html += '<a href="javascript:void(0)" onclick="buildRekapCetakUrl(\'' + month.str + '\')" ' +
            'class="block p-3 rounded-xl border transition-all cursor-pointer hover:border-blue-500/50 hover:bg-gray-50 ' +
            (isCurrentMonth ? 'border-blue-500 bg-blue-50' : 'border-gray-200') + '">' +
            '<div class="flex items-center justify-between">' +
            '<div>' +
            '<p class="text-sm font-semibold text-gray-800">' + month.label + '</p>' +
            '<p class="text-xs text-gray-500">' + month.start.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) + ' – ' + month.end.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) + '</p>' +
            '</div>' +
            (isCurrentMonth
                ? '<span class="text-[10px] font-bold text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">Bulan Ini</span>'
                : '<i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>') +
            '</div>' +
            '</a>';
    });

    container.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.table-row-hover');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(10px)';
        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 30);
    });
});
</script>
<?= $this->endSection() ?>
