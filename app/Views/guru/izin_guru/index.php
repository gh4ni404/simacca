<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?= $pageTitle ?></h1>
                <p class="mt-1 text-sm text-gray-500"><?= $pageDescription ?></p>
            </div>
            <div class="mt-4 md:mt-0">
                <?= button_link('primary', 'Ajukan Izin Baru', 'plus', base_url('guru/izin-guru/create'), ['class' => 'shadow']) ?>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Info Card -->
    <?= info_card('info-circle', 'Informasi Pengajuan Izin', '
        <ul class="mt-2 text-sm text-blue-800 space-y-1">
            <li>&bull; Ajukan izin minimal 1 hari sebelumnya (untuk izin terencana)</li>
            <li>&bull; Upload surat keterangan dokter untuk izin sakit lebih dari 2 hari</li>
            <li>&bull; Status pengajuan akan diproses oleh Wakakur</li>
            <li>&bull; Anda akan menerima notifikasi ketika izin disetujui/ditolak</li>
        </ul>
    ', 'blue') ?>

    <!-- Statistics Cards -->
    <?php
    $totalPending = count(array_filter($izinList, fn($i) => $i['status'] === 'pending'));
    $totalDisetujui = count(array_filter($izinList, fn($i) => $i['status'] === 'disetujui'));
    $totalDitolak = count(array_filter($izinList, fn($i) => $i['status'] === 'ditolak'));
    ?>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <?= stat_card('Total Pengajuan', count($izinList), 'file-alt', 'gray') ?>
        <?= stat_card('Menunggu', $totalPending, 'clock', 'yellow') ?>
        <?= stat_card('Disetujui', $totalDisetujui, 'check-circle', 'green') ?>
        <?= stat_card('Ditolak', $totalDitolak, 'times-circle', 'red') ?>
    </div>

    <!-- Izin List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Riwayat Pengajuan Izin</h3>
        </div>

        <?php if (empty($izinList)): ?>
            <?= empty_state('inbox', 'Belum Ada Pengajuan Izin', 'Anda belum pernah mengajukan izin', 'Ajukan Izin Sekarang', base_url('guru/izin-guru/create')) ?>
        <?php else: ?>
            <div class="overflow-x-auto">
                <?= table_start() ?>
                    <?= table_header(['Tanggal', 'Jenis Izin', 'Alasan', 'Status', 'Diproses Oleh', 'Aksi']) ?>
                    <?php foreach ($izinList as $izin): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= date('d M Y', strtotime($izin['tanggal_mulai'])) ?></div>
                                <div class="text-xs text-gray-500">s/d <?= date('d M Y', strtotime($izin['tanggal_selesai'])) ?></div>
                                <?php $days = (strtotime($izin['tanggal_selesai']) - strtotime($izin['tanggal_mulai'])) / 86400 + 1; ?>
                                <div class="text-xs text-gray-500">(<?= $days ?> hari)</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $jenisColors = ['sakit' => 'yellow', 'cuti' => 'purple', 'dinas_luar' => 'blue'];
                                $jenisColor = $jenisColors[$izin['jenis_izin']] ?? 'gray';
                                ?>
                                <?= badge(ucfirst(str_replace('_', ' ', $izin['jenis_izin'])), $jenisColor) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 line-clamp-2">
                                    <?= esc(substr($izin['alasan'], 0, 100)) ?>
                                    <?= strlen($izin['alasan']) > 100 ? '...' : '' ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?= status_badge($izin['status']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= $izin['approver_name'] ?? '-' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <?= button_link('info', 'Detail', 'eye', base_url('guru/izin-guru/show/' . $izin['id']), ['class' => 'text-xs px-3 py-1']) ?>
                                <?php if ($izin['status'] === 'pending'): ?>
                                    <?= button('danger', 'Hapus', 'trash', ['type' => 'button', 'onclick' => 'confirmDelete(' . $izin['id'] . ')', 'class' => 'text-xs px-3 py-1']) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?= table_end() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<?= confirm_modal('deleteModal', 'Hapus Pengajuan Izin?', 'Apakah Anda yakin ingin menghapus pengajuan izin ini? Tindakan ini tidak dapat dibatalkan.', 'Hapus', 'Batal') ?>

<script>
function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = '<?= base_url('guru/izin-guru/delete') ?>/' + id;
    openModal('deleteModal');
}

document.addEventListener('confirmed', function(e) {
    if (e.detail.modalId === 'deleteModal') {
        document.getElementById('deleteForm').submit();
    }
});
</script>

<?= modal_scripts() ?>

<?= $this->endSection() ?>
