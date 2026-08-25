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
            line-height: 1.3;
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
        .summary-box {
            margin-bottom: 15px;
            font-size: 9pt;
            border: 1px solid #ccc;
            padding: 6px 10px;
            background-color: #fcfcfc;
        }
        .teacher-section {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }
        .teacher-header {
            background-color: #e6f0fa;
            border: 1px solid #000;
            border-bottom: none;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 9.5pt;
            display: flex;
            justify-content: space-between;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 8.5pt;
            vertical-align: middle;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .signature-section {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .sig-box {
            text-align: center;
            width: 220px;
            font-size: 9.5pt;
        }
        .sig-space {
            height: 60px;
        }
        .btn-print {
            position: fixed;
            top: 15px;
            right: 15px;
            padding: 8px 16px;
            background: #0284c7;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 999;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Print Button -->
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            🖨️ Cetak / Print Dokumen
        </button>
    </div>

    <!-- Kop Surat -->
    <div class="kop-surat">
        <h2>PEMERINTAH PROVINSI / YAYASAN PENDIDIKAN</h2>
        <h1><?= esc($sekolahInfo['nama_sekolah'] ?? 'SMK NEGERI CONTOH') ?></h1>
        <p><?= esc($sekolahInfo['alamat'] ?? 'Jl. Pendidikan No. 1, Kota / Kabupaten') ?> | Telp: <?= esc($sekolahInfo['no_telepon'] ?? '-') ?> | Email: <?= esc($sekolahInfo['email'] ?? 'info@sekolah.sch.id') ?></p>
        <p>Website: <?= esc($sekolahInfo['website'] ?? 'www.sekolah.sch.id') ?></p>
    </div>

    <!-- Document Title -->
    <div class="doc-title">
        <h3>SURAT KEPUTUSAN / REKAPITULASI PEMBAGIAN GURU WALI</h3>
        <p>Tahun Ajaran: <strong><?= esc($tahunAjaran) ?></strong></p>
    </div>

    <!-- Summary Box -->
    <div class="summary-box">
        <strong>Ringkasan Penugasan:</strong> Total Siswa: <?= number_format($stats['total_siswa'] ?? 0) ?> | Siswa Terpetakan: <?= number_format($stats['total_assigned'] ?? 0) ?> (<?= $stats['percentage_assigned'] ?? 0 ?>%) | Siswa Belum Ada Wali: <?= number_format($stats['total_unassigned'] ?? 0) ?> | Total Guru Pembimbing: <?= number_format($stats['total_guru_wali'] ?? 0) ?> Guru
    </div>

    <!-- List per Guru Wali -->
    <?php if (empty($guruWaliList)): ?>
        <p class="text-center" style="padding: 30px 0; color: #666;">Belum ada data penugasan Guru Wali pada tahun ajaran ini.</p>
    <?php else: ?>
        <?php foreach ($guruWaliList as $gw): ?>
            <div class="teacher-section">
                <div class="teacher-header">
                    <span>Guru Wali: <strong><?= esc($gw['nama_lengkap']) ?></strong> (NIP: <?= esc($gw['nip'] ?: '-') ?> <?= $gw['nama_mapel'] ? '• Mapel: ' . esc($gw['nama_mapel']) : '' ?>)</span>
                    <span>Jumlah Siswa Binaan: <strong><?= count($gw['siswa']) ?> Siswa</strong></span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 18%;">NIS</th>
                            <th style="width: 32%;">Nama Siswa</th>
                            <th style="width: 8%;">L/P</th>
                            <th style="width: 18%;">Kelas & Jurusan</th>
                            <th style="width: 19%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($gw['siswa'])): ?>
                            <tr>
                                <td colspan="6" class="text-center" style="color: #777;">Belum ada siswa yang ditugaskan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($gw['siswa'] as $i => $s): ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td class="text-center"><?= esc($s['nis']) ?></td>
                                    <td><strong><?= esc($s['nama_siswa']) ?></strong></td>
                                    <td class="text-center"><?= esc($s['jenis_kelamin']) ?></td>
                                    <td><?= esc($s['nama_kelas'] ?? '-') ?> <?= !empty($s['jurusan']) ? '(' . esc($s['jurusan']) . ')' : '' ?></td>
                                    <td><?= esc($s['keterangan'] ?: '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Signature Block -->
    <div class="signature-section">
        <div class="sig-box">
            <p>Mengetahui,<br>Wakil Kepala Bidang Kurikulum</p>
            <div class="sig-space"></div>
            <p><strong>_________________________</strong><br>NIP. -</p>
        </div>
        <div class="sig-box">
            <p>Ditetapkan di: <?= esc($sekolahInfo['kota'] ?? 'Tempat') ?><br>Pada tanggal: <?= date('d F Y') ?></p>
            <p>Kepala Sekolah,</p>
            <div class="sig-space"></div>
            <p><strong><?= esc($sekolahInfo['nama_kepala_sekolah'] ?? 'Kepala Sekolah') ?></strong><br>NIP. <?= esc($sekolahInfo['nip_kepala_sekolah'] ?? '-') ?></p>
        </div>
    </div>

</body>
</html>
