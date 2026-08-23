<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            font-size: 9pt;
        }
        .meta-info {
            margin-bottom: 15px;
            font-size: 9pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer-sig {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .sig-box {
            text-align: center;
            width: 200px;
        }
        .sig-space {
            height: 50px;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #0284c7; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Cetak PDF / Print
        </button>
    </div>

    <div class="header">
        <h2>LAPORAN JURNAL PIKET GURU</h2>
        <p>Tahun Ajaran <?= esc($tahunAjaran) ?> | Periode: <?= date('d/m/Y', strtotime($startDate)) ?> s.d. <?= date('d/m/Y', strtotime($endDate)) ?></p>
        <?php if (!empty($selectedGuru)): ?>
            <p><strong>Guru: <?= esc($selectedGuru['nama_lengkap']) ?> (NIP: <?= esc($selectedGuru['nip'] ?: '-') ?>)</strong></p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 12%;">Tanggal / Hari</th>
                <th style="width: 18%;">Nama Guru Piket</th>
                <th style="width: 38%;">Uraian Kegiatan Piket</th>
                <th style="width: 28%;">Catatan / Kejadian Khusus</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($jurnalList)): ?>
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data jurnal piket untuk periode ini.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($jurnalList as $j): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td>
                            <strong><?= date('d/m/Y', strtotime($j['tanggal'])) ?></strong><br>
                            <small><?= esc(ucfirst(date_to_indo($j['tanggal']))) ?></small>
                        </td>
                        <td>
                            <strong><?= esc($j['nama_lengkap']) ?></strong><br>
                            <small>NIP: <?= esc($j['nip'] ?: '-') ?></small>
                        </td>
                        <td><?= nl2br(esc($j['deskripsi'])) ?></td>
                        <td><?= !empty($j['catatan']) ? nl2br(esc($j['catatan'])) : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer-sig">
        <div class="sig-box">
            <p>Mengetahui,</p>
            <p>Kepala Sekolah</p>
            <div class="sig-space"></div>
            <p>_______________________</p>
        </div>
        <div class="sig-box">
            <p><?= date('d F Y') ?></p>
            <p>Koordinator Guru Piket</p>
            <div class="sig-space"></div>
            <p>_______________________</p>
        </div>
    </div>
</body>
</html>
