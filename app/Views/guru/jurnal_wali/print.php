<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.2cm 1.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #000;
            margin: 0;
            padding: 10px;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
            position: relative;
        }
        .kop-surat h2 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-surat h1 {
            margin: 2px 0;
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-surat p {
            margin: 1px 0;
            font-size: 8.5pt;
        }
        .doc-title {
            text-align: center;
            margin: 15px 0 12px 0;
        }
        .doc-title h3 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .doc-title p {
            margin: 3px 0 0 0;
            font-size: 10pt;
        }
        .info-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9.5pt;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2.5px 4px;
            vertical-align: top;
        }
        .info-table .label {
            width: 18%;
            font-weight: bold;
        }
        .info-table .sep {
            width: 2%;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 15px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            font-size: 8.5pt;
            vertical-align: top;
        }
        table.data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            font-size: 10pt;
        }
        .signature-space {
            height: 65px;
        }
        .no-print {
            position: fixed;
            top: 15px;
            right: 15px;
            background: #1e293b;
            color: #fff;
            padding: 8px 16px;
            border-radius: 8px;
            font-family: sans-serif;
            font-size: 12px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            border: none;
            display: flex;
            align-items: center;
            gap: 6px;
            z-index: 999;
        }
        .no-print:hover {
            background: #0f172a;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print">
        🖨️ Cetak Jurnal
    </button>

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <h2>PEMERINTAH DAERAH PROVINSI</h2>
        <h1><?= esc($sekolahInfo['nama_sekolah'] ?? 'SMK NEGERI 1 SIMACCA') ?></h1>
        <p><?= esc($sekolahInfo['alamat'] ?? 'Jl. Pendidikan No. 1, Kota') ?> | Telp: <?= esc($sekolahInfo['telepon'] ?? '-') ?> | Email: <?= esc($sekolahInfo['email'] ?? '-') ?></p>
        <p>Website: <?= esc($sekolahInfo['website'] ?? 'https://simacca.sch.id') ?></p>
    </div>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title">
        <h3>JURNAL BIMBINGAN GURU WALI</h3>
        <p>Laporan Rekam Jejak Bimbingan Personal Peserta Didik</p>
    </div>

    <!-- PROFIL GURU WALI & FILTER -->
    <table class="info-table">
        <tr>
            <td class="label">Nama Guru Wali</td>
            <td class="sep">:</td>
            <td><strong><?= esc($guru['nama_lengkap']) ?></strong></td>
            <td class="label">Total Siswa Binaan</td>
            <td class="sep">:</td>
            <td><?= count($siswaBinaan) ?> Orang</td>
        </tr>
        <tr>
            <td class="label">NIP</td>
            <td class="sep">:</td>
            <td><?= esc($guru['nip'] ?: '-') ?></td>
            <td class="label">Siswa Difilter</td>
            <td class="sep">:</td>
            <td><?= !empty($selectedSiswa) ? esc($selectedSiswa['nama_lengkap'] . ' (' . ($selectedSiswa['nama_kelas'] ?? '-') . ')') : 'Semua Siswa Binaan' ?></td>
        </tr>
    </table>

    <!-- TABEL DATA JURNAL -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 22%;">Nama Siswa (NIS / Kelas)</th>
                <th style="width: 14%;">Jenis Bimbingan</th>
                <th style="width: 26%;">Catatan / Observasi</th>
                <th style="width: 22%;">Tindak Lanjut / Solusi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($jurnalList)): ?>
                <tr>
                    <td colspan="6" class="text-center" style="padding: 15px;">
                        <em>Tidak ada riwayat bimbingan pada filter yang dipilih.</em>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($jurnalList as $idx => $j): ?>
                    <tr>
                        <td class="text-center"><?= $idx + 1 ?></td>
                        <td class="text-center font-mono"><?= date('d/m/Y', strtotime($j['tanggal'])) ?></td>
                        <td>
                            <strong><?= esc($j['nama_siswa']) ?></strong><br>
                            <span style="font-size: 7.5pt; color: #555;">NIS: <?= esc($j['nis']) ?> • <?= esc($j['nama_kelas'] ?? '-') ?></span>
                        </td>
                        <td class="text-center">
                            <?= esc($j['jenis_bimbingan']) ?>
                        </td>
                        <td><?= nl2br(esc($j['catatan'])) ?></td>
                        <td><?= nl2br(esc($j['tindak_lanjut'] ?: '-')) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <div class="signature-space"></div>
            <p>
                <strong><u><?= esc($sekolahInfo['kepala_sekolah'] ?? 'Kepala Sekolah, M.Pd') ?></u></strong><br>
                NIP. <?= esc($sekolahInfo['nip_kepala_sekolah'] ?? '-') ?>
            </p>
        </div>
        <div class="signature-box">
            <p><?= esc($sekolahInfo['kota'] ?? 'Kota') ?>, <?= date('d F Y') ?><br>Guru Wali,</p>
            <div class="signature-space"></div>
            <p>
                <strong><u><?= esc($guru['nama_lengkap']) ?></u></strong><br>
                NIP. <?= esc($guru['nip'] ?: '-') ?>
            </p>
        </div>
    </div>

</body>
</html>
