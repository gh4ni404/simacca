<?= $this->extend(get_device_layout()) ?>

<?= $this->section('styles') ?>
<style>
    .table-responsive { overflow-x: auto; }
    .toggle-checkbox:checked { background-color: #6366f1; border-color: #6366f1; }
    .toggle-checkbox:checked + .toggle-label { background-color: #6366f1; }
    .toggle-checkbox:checked + .toggle-label::after { transform: translateX(100%); }
    .toggle-checkbox { position: absolute; opacity: 0; width: 0; height: 0; }
    .toggle-label { position: relative; display: inline-block; width: 36px; height: 20px; background-color: #d1d5db; border-radius: 9999px; cursor: pointer; transition: background-color 0.2s; }
    .toggle-label::after { content: ''; position: absolute; top: 2px; left: 2px; width: 16px; height: 16px; background-color: white; border-radius: 50%; transition: transform 0.2s; }
    .kategori-item { transition: all 0.2s ease; }
    .kategori-item:hover { background-color: #f5f3ff; }
    .kategori-item.loading { opacity: 0.5; pointer-events: none; }
    .pulse-dot { animation: pulse 1s infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-project-diagram mr-2 text-indigo-600"></i>Mapping Kategori PKL
        </h1>
        <p class="text-gray-600 mt-1">Tentukan kategori pekerjaan yang berlaku untuk setiap tempat PKL</p>
    </div>

    <?= view('components/alerts') ?>

    <!-- Pilih Tempat PKL -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-building text-indigo-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800">Pilih Tempat PKL</h3>
                <p class="text-sm text-gray-500">Pilih perusahaan untuk mengelola kategori yang berlaku</p>
            </div>
        </div>

        <form method="GET" action="<?= base_url('admin/kategori-pkl-mapping'); ?>" id="filterForm">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <select name="tempat_pkl_id" id="tempatSelect"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            onchange="this.form.submit()">
                        <option value="">-- Pilih Tempat PKL --</option>
                        <?php foreach ($allTempatPkl as $tp): ?>
                        <option value="<?= $tp['id']; ?>" <?= $selectedTempatId == $tp['id'] ? 'selected' : '' ?>>
                            <?= esc($tp['nama_perusahaan']); ?>
                            <?= !empty($tp['kota']) ? '(' . esc($tp['kota']) . ')' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($selectedTempatId): ?>
                <a href="<?= base_url('admin/kategori-pkl-mapping'); ?>"
                   class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors">
                    <i class="fas fa-undo mr-2"></i>Reset
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if ($selectedTempatId): ?>
    <!-- Kategori Checklist -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-tags text-purple-600"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800">Kategori Tersedia</h3>
                <p class="text-sm text-gray-500">Centang kategori yang berlaku untuk tempat PKL ini</p>
            </div>
            <div id="loadingIndicator" class="ml-auto hidden">
                <span class="inline-flex items-center text-sm text-indigo-600">
                    <i class="fas fa-circle-notch fa-spin mr-2"></i>Memproses...
                </span>
            </div>
        </div>

        <?php if (empty($allKategori)): ?>
        <div class="p-8 text-center">
            <i class="fas fa-tags text-3xl text-gray-300 mb-2"></i>
            <p class="text-gray-500 text-sm">Belum ada kategori. <a href="<?= base_url('admin/pkl-categories'); ?>" class="text-indigo-600 hover:underline">Tambah kategori dulu</a></p>
        </div>
        <?php else: ?>
        <div id="kategoriList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <?php foreach ($allKategori as $kat): ?>
            <?php $isChecked = in_array($kat['id'], $mappedKategoriIds); ?>
            <div class="kategori-item flex items-center justify-between p-4 rounded-xl border <?= $isChecked ? 'border-indigo-200 bg-indigo-50' : 'border-gray-200 bg-white' ?>"
                 id="kategori-<?= $kat['id']; ?>">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center <?= $isChecked ? 'bg-indigo-100' : 'bg-gray-100' ?>">
                        <i class="fas fa-tag <?= $isChecked ? 'text-indigo-600' : 'text-gray-400' ?>"></i>
                    </div>
                    <span class="font-medium text-gray-800 text-sm"><?= esc($kat['nama']); ?></span>
                </div>
                <div class="relative">
                    <input type="checkbox" id="toggle-<?= $kat['id']; ?>" class="toggle-checkbox"
                           <?= $isChecked ? 'checked' : '' ?>
                           onchange="toggleKategori(<?= $selectedTempatId; ?>, <?= $kat['id']; ?>, this)">
                    <label for="toggle-<?= $kat['id']; ?>" class="toggle-label"></label>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Info Box -->
    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 flex items-start gap-3">
        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="fas fa-info-circle text-indigo-600 text-sm"></i>
        </div>
        <div class="text-sm text-indigo-800">
            <p><strong>Catatan:</strong> Kategori yang di-mapping akan menjadi opsi yang tersedia saat instruktur atau pembimbing membuat task template untuk tempat PKL ini.</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabel Ringkasan -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800"><i class="fas fa-table mr-2 text-indigo-600"></i>Ringkasan Mapping</h3>
        </div>
        <div class="table-responsive">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Perusahaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kota</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Kategori</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($mappingSummary)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-project-diagram text-3xl text-gray-300 mb-2 block"></i>
                            <p>Belum ada mapping kategori</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php $no = 1; ?>
                    <?php foreach ($mappingSummary as $row): ?>
                    <tr class="hover:bg-gray-50 <?= (isset($row['tempat_pkl_id']) && $selectedTempatId == $row['tempat_pkl_id']) ? 'bg-indigo-50' : '' ?>">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($row['nama_perusahaan'] ?? '-'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= esc($row['kota'] ?? '-'); ?></td>
                        <td class="px-6 py-4 text-center">
                            <?php $count = $row['jumlah_kategori'] ?? 0; ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $count > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                                <?= $count; ?> kategori
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="<?= base_url('admin/kategori-pkl-mapping?tempat_pkl_id=' . ($row['tempat_pkl_id'] ?? '')); ?>"
                               class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 text-xs font-medium transition-colors">
                                <i class="fas fa-edit mr-1"></i>Kelola
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-4 right-4 z-[9999] hidden">
    <div class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-sm font-medium" id="toastContent">
        <i id="toastIcon" class="fas"></i>
        <span id="toastMessage"></span>
    </div>
</div>

<script>
let currentCsrfName = '<?= csrf_token() ?>';
let currentCsrfHash = '<?= csrf_hash() ?>';

async function getFreshCsrfToken() {
    try {
        const res = await fetch('<?= base_url('csrf-token'); ?>', { credentials: 'same-origin' });
        const data = await res.json();
        currentCsrfName = data.tokenName;
        currentCsrfHash = data.tokenValue;
        document.querySelectorAll('input[name="<?= csrf_token() ?>"]').forEach(el => el.value = currentCsrfHash);
    } catch (e) {
        // fallback: keep current token
    }
}

async function toggleKategori(tempatPklId, kategoriId, checkbox) {
    const item = document.getElementById('kategori-' + kategoriId);
    const icon = item.querySelector('.fa-tag');
    const bgDiv = item.querySelector('.w-9');

    item.classList.add('loading');
    checkbox.disabled = true;

    await getFreshCsrfToken();

    const formData = new FormData();
    formData.append('tempat_pkl_id', tempatPklId);
    formData.append('kategori_id', kategoriId);
    formData.append(currentCsrfName, currentCsrfHash);

    fetch('<?= base_url('admin/kategori-pkl-mapping/simpan'); ?>', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const isAdded = data.status === 'added';

            if (isAdded) {
                item.classList.remove('border-gray-200', 'bg-white');
                item.classList.add('border-indigo-200', 'bg-indigo-50');
                bgDiv.classList.remove('bg-gray-100');
                bgDiv.classList.add('bg-indigo-100');
                icon.classList.remove('text-gray-400');
                icon.classList.add('text-indigo-600');
            } else {
                item.classList.remove('border-indigo-200', 'bg-indigo-50');
                item.classList.add('border-gray-200', 'bg-white');
                bgDiv.classList.remove('bg-indigo-100');
                bgDiv.classList.add('bg-gray-100');
                icon.classList.remove('text-indigo-600');
                icon.classList.add('text-gray-400');
            }

            showToast(isAdded ? 'Kategori ditambahkan' : 'Kategori dihapus', isAdded ? 'success' : 'info');
            refreshSummary();
        } else {
            checkbox.checked = !checkbox.checked;
            showToast(data.message || 'Gagal memproses', 'error');
        }
    })
    .catch(() => {
        checkbox.checked = !checkbox.checked;
        showToast('Terjadi kesalahan jaringan', 'error');
    })
    .finally(() => {
        item.classList.remove('loading');
        checkbox.disabled = false;
    });
}

function refreshSummary() {
    fetch('<?= base_url('admin/kategori-pkl-mapping/summary'); ?>')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tbody = document.querySelector('table tbody');
            const summary = data.summary;

            if (summary.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500"><i class="fas fa-project-diagram text-3xl text-gray-300 mb-2 block"></i><p>Belum ada mapping kategori</p></td></tr>';
                return;
            }

            let html = '';
            summary.forEach((row, idx) => {
                const count = parseInt(row.jumlah_kategori) || 0;
                const badgeClass = count > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500';
                const activeClass = '<?= $selectedTempatId ?>' == row.tempat_pkl_id ? 'bg-indigo-50' : '';
                html += '<tr class="hover:bg-gray-50 ' + activeClass + '">';
                html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + (idx + 1) + '</td>';
                html += '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' + (row.nama_perusahaan || '-') + '</td>';
                html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + (row.kota || '-') + '</td>';
                html += '<td class="px-6 py-4 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' + badgeClass + '">' + count + ' kategori</span></td>';
                html += '<td class="px-6 py-4 text-center"><a href="<?= base_url('admin/kategori-pkl-mapping'); ?>?tempat_pkl_id=' + row.tempat_pkl_id + '" class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 text-xs font-medium transition-colors"><i class="fas fa-edit mr-1"></i>Kelola</a></td>';
                html += '</tr>';
            });

            tbody.innerHTML = html;
        }
    });
}

function showToast(message, type) {
    const toast = document.getElementById('toast');
    const content = document.getElementById('toastContent');
    const icon = document.getElementById('toastIcon');
    const msg = document.getElementById('toastMessage');

    const colors = {
        success: 'bg-green-600 text-white',
        error: 'bg-red-600 text-white',
        info: 'bg-indigo-600 text-white',
    };

    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        info: 'fa-info-circle',
    };

    content.className = 'flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-sm font-medium ' + (colors[type] || colors.info);
    icon.className = 'fas ' + (icons[type] || icons.info);
    msg.textContent = message;

    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3000);
}
</script>
<?= $this->endSection() ?>
