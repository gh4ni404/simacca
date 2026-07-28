<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Kegiatan PKL - <?= esc($siswa['nama_lengkap']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: white;
        }

        .container {
            max-width: 210mm;
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
        }

        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
            letter-spacing: 0.5px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 11pt;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px 5px;
            vertical-align: top;
        }

        .info-table td.label-cell {
            width: 180px;
        }

        .info-table td.separator-cell {
            width: 15px;
            text-align: center;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 8px 12px;
            text-align: left;
            font-size: 10pt;
            vertical-align: top;
        }

        table.data-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9.5pt;
        }

        table.data-table td.no {
            text-align: center;
            width: 40px;
        }

        table.data-table td.tgl {
            width: 160px;
        }

        table.data-table td.catatan {
            width: 220px;
        }

        table.data-table td.empty-cell {
            height: 40px;
        }

        table.data-table td ol {
            margin: 0;
            padding-left: 18px;
            list-style-type: decimal;
        }

        table.data-table td ol li {
            /* margin-bottom: 2px; */
            line-height: 1.4;
            padding-left: 4px;
        }

        .notes-section {
            margin-top: 15px;
            font-size: 9pt;
            font-style: italic;
            line-height: 1.5;
            color: #333;
        }

        .footer-signatures {
            margin-top: 40px;
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .footer-signatures td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 5px;
            font-size: 11pt;
        }

        .signature-space {
            height: 75px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .container {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Jurnal Kegiatan PKL</h1>
        </div>

        <table class="info-table">
            <tr>
                <td class="label-cell">Nama Peserta Didik</td>
                <td class="separator-cell">:</td>
                <td><strong><?= esc($siswa['nama_lengkap']) ?></strong></td>
            </tr>
            <tr>
                <td class="label-cell">Dunia Kerja Tempat PKL</td>
                <td class="separator-cell">:</td>
                <td><strong><?= esc($tempatPkl['nama_perusahaan'] ?? '-') ?></strong></td>
            </tr>
            <tr>
                <td class="label-cell">Nama Instruktur</td>
                <td class="separator-cell">:</td>
                <td><?= esc($instruktur['nama_lengkap'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label-cell">Nama Guru Mapel PKL</td>
                <td class="separator-cell">:</td>
                <td><?= esc($pembimbing['nama_guru'] ?? '-') ?></td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th class="no">No.</th>
                    <th class="tgl">Hari/Tanggal</th>
                    <th>Unit Kerja/Pekerjaan</th>
                    <th class="catatan">Catatan*</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                $bulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                $groupedJurnal = [];
                if (!empty($jurnalData)) {
                    foreach ($jurnalData as $row) {
                        $groupedJurnal[$row['tanggal']][] = $row;
                    }
                }

                $printedRows = 0;
                if (!empty($groupedJurnal)):
                    foreach ($groupedJurnal as $tanggal => $logs):
                        $printedRows++;
                        $d = new DateTime($tanggal);
                        $dayName = $hariIndo[$d->format('l')] ?? $d->format('l');
                        $dateStr = $dayName . ', ' . $d->format('d') . ' ' . $bulanIndo[(int) $d->format('m')] . ' ' . $d->format('Y');

                        // Combine activities
                        $activitiesHtml = '';
                        $catatanHtml = '';
                        if (count($logs) === 1) {
                            $activitiesHtml = esc($logs[0]['deskripsi']);
                            $catatanHtml = esc($logs[0]['catatan_instruktur'] ?? '');
                        } else {
                            $activitiesArray = [];
                            $catatanArray = [];

                            foreach ($logs as $log) {
                                if (!empty($log['deskripsi'])) {
                                    $activitiesArray[] = esc($log['deskripsi']);
                                }
                                if (!empty($log['catatan_instruktur'])) {
                                    $catatanArray[] = esc($log['catatan_instruktur']);
                                }
                            }

                            // membuat aktivitas dalam bentuk numbering
                            if (!empty($activitiesArray)) {
                                $activitiesHtml = '<ol>';
                                foreach ($activitiesArray as $act) {
                                    $activitiesHtml .= '<li>' . $act . '</li>';
                                }
                                $activitiesHtml .= '</ol>';
                            } else {
                                $activitiesHtml = '';
                            }

                            if (!empty($catatanArray)) {
                                $catatanHtml = '<ol>';
                                foreach ($catatanArray as $cat) {
                                    $catatanHtml .= '<li>' . $cat . '</li>';
                                }
                                $catatanHtml .= '</ol>';
                            } else {
                                $catatanHtml = '';
                            }
                        }
                        ?>
                        <tr>
                            <td class="no"><?= $printedRows ?></td>
                            <td class="tgl"><?= $dateStr ?></td>
                            <td><?= $activitiesHtml ?></td>
                            <td><?= $catatanHtml ?></td>
                        </tr>
                    <?php
                    endforeach;
                endif;

                // Pad to at least 8 rows to match the reference PDF layout
                $minRows = 8;
                for ($i = $printedRows + 1; $i <= $minRows; $i++):
                    ?>
                    <tr>
                        <td class="no"><?= $i ?></td>
                        <td class="tgl empty-cell"></td>
                        <td class="empty-cell"></td>
                        <td class="empty-cell"></td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <div class="notes-section">
            <p>Jurnal kegiatan disusun oleh peserta didik sebagai dokumen pekerjaan yang dilaksanakan.</p>
            <p>*) Catatan diberikan oleh pembimbing dunia kerja pada setiap kegiatan atau waktu tertentu.</p>
        </div>

        <table class="footer-signatures" style="display: none;">
            <tr>
                <td>
                    <p>Mengetahui,</p>
                    <p>Pembimbing Industri</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">
                        <?= esc($instruktur['nama_lengkap'] ?? '.......................................') ?></p>
                    <?php if (!empty($instruktur['telepon'])): ?>
                        <p style="font-size: 9pt; color: #555;">Telp: <?= esc($instruktur['telepon']) ?></p>
                    <?php endif; ?>
                </td>
                <td>
                    <p>&nbsp;</p>
                    <p>Pembimbing PKL</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">
                        <?= esc($pembimbing['nama_guru'] ?? '.......................................') ?></p>
                    <?php if (!empty($pembimbing['nip'])): ?>
                        <p style="font-size: 9pt; color: #555;">NIP. <?= esc($pembimbing['nip']) ?></p>
                    <?php endif; ?>
                </td>
                <td>
                    <p>
                        <?php
                        $currentDate = new DateTime();
                        echo esc($tempatPkl['kota'] ?? 'Kota') . ', ' . $currentDate->format('d') . ' ' . $bulanIndo[(int) $currentDate->format('m')] . ' ' . $currentDate->format('Y');
                        ?>
                    </p>
                    <p>Siswa</p>
                    <div class="signature-space"></div>
                    <p class="signature-name"><?= esc($siswa['nama_lengkap']) ?></p>
                    <p style="font-size: 9pt; color: #555;">NIS. <?= esc($siswa['nis']) ?></p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>