<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-stethoscope text-indigo-600 mr-3"></i> <?= $pageTitle ?>
            </h2>
            <p class="text-gray-600 text-sm mt-1"><?= $pageDescription ?></p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <form action="<?= base_url('admin/guru/fix-multi-role') ?>" method="POST" onsubmit="return confirm('Jalankan perbaikan otomatis sinkronisasi data guru?')">
                <?= csrf_field() ?>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium flex items-center shadow-sm">
                    <i class="fas fa-magic mr-2"></i> Perbaiki Otomatis
                </button>
            </form>
            <a href="<?= base_url('admin/guru') ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">
                Kembali ke Data Guru
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Active Academic Year Banner -->
    <div class="mb-6 p-4 bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-xl flex items-center justify-between text-indigo-950">
        <div class="flex items-center space-x-3">
            <div class="p-2.5 bg-indigo-600 text-white rounded-lg">
                <i class="fas fa-calendar-alt text-lg"></i>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider font-semibold text-indigo-600">Tahun Ajaran Aktif System</p>
                <h4 class="text-lg font-bold text-indigo-900"><?= esc($summary['active_tahun_ajaran'] ?? get_active_tahun_ajaran()) ?></h4>
            </div>
        </div>
        <a href="<?= base_url('admin/pengaturan') ?>" class="text-xs bg-white hover:bg-indigo-50 border border-indigo-300 text-indigo-700 px-3 py-1.5 rounded-lg font-medium shadow-sm flex items-center">
            <i class="fas fa-cog mr-1.5"></i> Ubah TA di Pengaturan
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-5 text-white shadow-sm">
            <p class="text-xs uppercase tracking-wider text-blue-100 font-semibold">Total Data Guru</p>
            <h3 class="text-3xl font-extrabold mt-1"><?= $summary['total_guru'] ?? 0 ?></h3>
            <p class="text-xs text-blue-100 mt-2"><i class="fas fa-users mr-1"></i> Terdaftar di sistem</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl p-5 text-white shadow-sm">
            <p class="text-xs uppercase tracking-wider text-purple-100 font-semibold">Guru Multi-Role</p>
            <h3 class="text-3xl font-extrabold mt-1"><?= $summary['multi_role_count'] ?? 0 ?></h3>
            <p class="text-xs text-purple-100 mt-2"><i class="fas fa-user-tag mr-1"></i> Memiliki >1 role sekaligus</p>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl p-5 text-white shadow-sm">
            <p class="text-xs uppercase tracking-wider text-emerald-100 font-semibold">Status Valid (PASS)</p>
            <h3 class="text-3xl font-extrabold mt-1"><?= $summary['total_passed'] ?? 0 ?></h3>
            <p class="text-xs text-emerald-100 mt-2"><i class="fas fa-check-circle mr-1"></i> Atribut & Role Sinkron</p>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-5 text-white shadow-sm">
            <p class="text-xs uppercase tracking-wider text-amber-100 font-semibold">Peringatan (WARN)</p>
            <h3 class="text-3xl font-extrabold mt-1"><?= $summary['total_warnings'] ?? 0 ?></h3>
            <p class="text-xs text-amber-100 mt-2"><i class="fas fa-exclamation-triangle mr-1"></i> Perlu perhatian / fix</p>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="flex items-center justify-between border-b pb-4 mb-4">
        <div class="flex space-x-2">
            <button onclick="filterResults('all')" id="btnFilterAll" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 text-white">
                Semua Guru (<?= count($diagnostics) ?>)
            </button>
            <button onclick="filterResults('multi')" id="btnFilterMulti" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                Hanya Multi-Role (<?= $summary['multi_role_count'] ?? 0 ?>)
            </button>
            <button onclick="filterResults('warn')" id="btnFilterWarn" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                Peringatan (<?= $summary['total_warnings'] ?? 0 ?>)
            </button>
        </div>
        <a href="<?= base_url('admin/guru/test-multi-role?json=1') ?>" target="_blank" class="text-xs text-indigo-600 hover:underline font-medium flex items-center">
            <i class="fas fa-code mr-1"></i> Export JSON Raw Output
        </a>
    </div>

    <!-- Diagnostic List -->
    <div class="space-y-4">
        <?php foreach ($diagnostics as $index => $item): ?>
            <div class="diagnostic-card border rounded-xl p-4 transition-all hover:shadow-md <?= $item['status'] === 'PASS' ? 'border-gray-200 bg-gray-50/50' : 'border-amber-300 bg-amber-50/30' ?>"
                 data-multi="<?= $item['is_multi_role'] ? '1' : '0' ?>"
                 data-status="<?= strtolower($item['status']) ?>">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                    <div class="flex items-center space-x-3">
                        <span class="h-10 w-10 rounded-full flex items-center justify-center font-bold text-white shadow-sm <?= $item['status'] === 'PASS' ? 'bg-emerald-500' : 'bg-amber-500' ?>">
                            <?= $item['status'] === 'PASS' ? '<i class="fas fa-check"></i>' : '<i class="fas fa-exclamation"></i>' ?>
                        </span>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h4 class="font-bold text-gray-800 text-base"><?= esc($item['nama_lengkap']) ?></h4>
                                <?php if ($item['is_multi_role']): ?>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Multi-Role</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs text-gray-500 font-mono mt-0.5">NIP: <?= esc($item['nip']) ?> | User: @<?= esc($item['username']) ?></p>
                            
                            <!-- Academic Year Class Tag -->
                            <div class="mt-1.5 flex flex-wrap gap-1">
                                <?php if (!empty($item['kelas_aktif'])): ?>
                                    <span class="px-2 py-0.5 text-[11px] font-medium rounded bg-emerald-100 text-emerald-800 flex items-center">
                                        <i class="fas fa-chalkboard-teacher mr-1"></i> Wali Kelas TA Aktif: <?= esc($item['kelas_aktif']) ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($item['riwayat_kelas']) && empty($item['kelas_aktif'])): ?>
                                    <span class="px-2 py-0.5 text-[11px] font-medium rounded bg-gray-200 text-gray-700 flex items-center">
                                        <i class="fas fa-history mr-1"></i> Riwayat TA Lalu: <?= esc(implode(', ', $item['riwayat_kelas'])) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <!-- Badges Roles -->
                        <div class="flex flex-wrap gap-1">
                            <?php foreach ($item['roles'] as $r): ?>
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <?= esc($r) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <button onclick="toggleDetails(<?= $index ?>)" class="px-3 py-1.5 text-xs bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-medium text-gray-700 ml-2">
                            Detail Diagnosa <i class="fas fa-chevron-down ml-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Expanded Details -->
                <div id="details-<?= $index ?>" class="hidden mt-4 pt-4 border-t border-gray-200 text-xs space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Checks List -->
                        <div class="bg-white p-3 rounded-lg border border-gray-200">
                            <p class="font-bold text-gray-700 mb-2 border-b pb-1"><i class="fas fa-tasks text-indigo-500 mr-1"></i> Hasil Pengujian Konsistensi Data</p>
                            <ul class="space-y-1.5">
                                <?php foreach ($item['checks'] as $key => $check): ?>
                                    <li class="flex items-start">
                                        <span class="mr-2 font-bold <?= $check['status'] === 'OK' ? 'text-emerald-600' : 'text-amber-600' ?>">
                                            [<?= $check['status'] ?>]
                                        </span>
                                        <span class="text-gray-700"><?= esc($check['message']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Route Permissions -->
                        <div class="bg-white p-3 rounded-lg border border-gray-200">
                            <p class="font-bold text-gray-700 mb-2 border-b pb-1"><i class="fas fa-route text-indigo-500 mr-1"></i> Halaman/Route yang Dapat Diakses (TA Aktif)</p>
                            <div class="flex flex-wrap gap-1">
                                <?php foreach ($item['accessible_routes'] as $route): ?>
                                    <span class="px-2 py-1 bg-emerald-50 text-emerald-800 rounded font-mono text-xs border border-emerald-100">
                                        <?= esc($route) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Session Hydration Simulation -->
                    <div class="bg-slate-800 text-slate-200 p-3 rounded-lg font-mono text-xs overflow-x-auto">
                        <p class="text-emerald-400 font-bold mb-1">// Simulasi Data Session Ter-hidrasi untuk TA <?= esc($summary['active_tahun_ajaran'] ?? get_active_tahun_ajaran()) ?>:</p>
                        <pre><?= json_encode($item['session_hydrated'], JSON_PRETTY_PRINT) ?></pre>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function toggleDetails(index) {
        const el = document.getElementById('details-' + index);
        if (el.classList.contains('hidden')) {
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    }

    function filterResults(type) {
        const cards = document.querySelectorAll('.diagnostic-card');
        
        document.getElementById('btnFilterAll').className = "px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200";
        document.getElementById('btnFilterMulti').className = "px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200";
        document.getElementById('btnFilterWarn').className = "px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200";

        if (type === 'all') {
            document.getElementById('btnFilterAll').className = "px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 text-white";
        } else if (type === 'multi') {
            document.getElementById('btnFilterMulti').className = "px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 text-white";
        } else if (type === 'warn') {
            document.getElementById('btnFilterWarn').className = "px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 text-white";
        }

        cards.forEach(card => {
            if (type === 'all') {
                card.style.display = 'block';
            } else if (type === 'multi') {
                card.style.display = card.getAttribute('data-multi') === '1' ? 'block' : 'none';
            } else if (type === 'warn') {
                card.style.display = card.getAttribute('data-status') === 'warn' ? 'block' : 'none';
            }
        });
    }
</script>
<?= $this->endSection() ?>
