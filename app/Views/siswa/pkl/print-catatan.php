<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Catatan Kegiatan PKL - <?= esc($siswa['nama_lengkap']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 20mm 15mm 20mm 15mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: white;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
        }

        .page {
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
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

        .section-box {
            border: 1px solid #000;
            margin-bottom: 15px;
            padding: 10px 12px;
            font-size: 10pt;
            background: #fff;
            page-break-inside: avoid;
        }

        .section-box.task-box {
            font-weight: bold;
            font-size: 11pt;
        }

        .section-header {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .planning-lines {
            margin-top: 8px;
        }

        .planning-line {
            height: 24px;
            border-bottom: 1px dotted #000;
            margin-bottom: 5px;
        }

        .footer-note {
            font-size: 9pt;
            font-style: italic;
            margin-top: 15px;
            color: #333;
        }

        .footer-signatures {
            margin-top: 30px;
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
        <?php
        $hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $bulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $totalTasks = count($tasksData);
        ?>

        <?php foreach ($tasksData as $idx => $item):
            $task = $item['task'];
            $progress = $item['progress'];
        ?>
        <div class="page">
            <div class="header">
                <h1>Catatan Kegiatan PKL</h1>
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

            <?php
            $jadwalPekerjaan = '-';
            if (!empty($progress)) {
                $firstDateStr = $progress[0]['tanggal'];
                $lastDateStr = $progress[count($progress) - 1]['tanggal'];

                $firstDate = new DateTime($firstDateStr);
                $lastDate = new DateTime($lastDateStr);

                $firstFormatted = ($hariIndo[$firstDate->format('l')] ?? $firstDate->format('l')) . ', ' . $firstDate->format('d') . ' ' . $bulanIndo[(int) $firstDate->format('m')] . ' ' . $firstDate->format('Y');
                $lastFormatted = ($hariIndo[$lastDate->format('l')] ?? $lastDate->format('l')) . ', ' . $lastDate->format('d') . ' ' . $bulanIndo[(int) $lastDate->format('m')] . ' ' . $lastDate->format('Y');

                if ($firstDateStr === $lastDateStr) {
                    $jadwalPekerjaan = $firstFormatted;
                } else {
                    $jadwalPekerjaan = $firstFormatted . ' s/d ' . $lastFormatted;
                }
            }
            ?>

            <div class="section-header">A. &nbsp; Nama Pekerjaan</div>
            <div class="section-box task-box">
                <?= esc($task['judul']) ?>
            </div>

            <div class="section-header">B. &nbsp; Perencanaan Kegiatan</div>
            <div class="section-box">
                <p><strong>Jadwal Kegiatan:</strong> <?= $jadwalPekerjaan ?>
                    <?= !empty($task['estimasi']) ? '(Estimasi: ' . esc($task['estimasi']) . ')' : '' ?></p>
                <p style="margin-top: 8px; font-weight: bold;">Dokumen Perencanaan & Persiapan:</p>
                <div class="planning-lines">
                    <div class="planning-line">1. </div>
                    <div class="planning-line">2. </div>
                    <div class="planning-line">3. </div>
                </div>
            </div>

            <div class="section-header">C. &nbsp; Pelaksanaan Kegiatan/Hasil</div>
            <div class="section-box">
                <p style="font-weight: bold; margin-bottom: 5px;">Uraian Proses Kerja:</p>
                <?php if (empty($progress)): ?>
                    <p style="font-style: italic; color: #666; margin-bottom: 10px;">Belum ada progress kegiatan.</p>
                <?php else: ?>
                    <ol style="margin-left: 20px; line-height: 1.6; margin-bottom: 10px;">
                        <?php foreach ($progress as $p): ?>
                            <li style="margin-bottom: 6px;"><?= esc($p['deskripsi']) ?></li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>

                <?php
                $photos = [];
                foreach ($progress as $p) {
                    if (!empty($p['foto'])) {
                        $photos[] = $p;
                    }
                }
                if (!empty($photos)):
                    ?>
                    <div style="margin-top: 15px; border-top: 1px dashed #ccc; padding-top: 10px;">
                        <p style="font-weight: bold; margin-bottom: 8px;">Foto Hasil:</p>
                        <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                            <?php foreach ($photos as $p):
                                $d = new DateTime($p['tanggal']);
                                $dateStr = $d->format('d/m/Y');
                                ?>
                                <div style="text-align: center;">
                                    <img src="<?= base_url('files/pkl-progress/' . $p['foto']) ?>"
                                        alt="Foto Progress <?= $dateStr ?>"
                                        style="max-width: 200px; max-height: 150px; border: 1px solid #ccc; padding: 4px; display: block; border-radius: 4px; object-fit: contain;">
                                    <span
                                        style="font-size: 8pt; color: #555; display: block; margin-top: 4px;"><?= $dateStr ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="section-header">D. &nbsp; Catatan Instruktur / Pembimbing</div>
            <div class="section-box">
                <?php
                $hasComments = false;
                foreach ($progress as $p) {
                    if (!empty($p['catatan_instruktur']) || !empty($p['catatan_pembimbing'])) {
                        $hasComments = true;
                        $d = new DateTime($p['tanggal']);
                        $dateStr = ($hariIndo[$d->format('l')] ?? $d->format('l')) . ', ' . $d->format('d/m/Y');
                        ?>
                        <div style="margin-bottom: 10px; line-height: 1.4;">
                            <strong style="text-decoration: underline;"><?= $dateStr ?></strong>
                            <?php if (!empty($p['catatan_instruktur'])): ?>
                                <p style="margin-top: 2px;"><strong>Instruktur Industri:</strong> <?= esc($p['catatan_instruktur']) ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($p['catatan_pembimbing'])): ?>
                                <p style="margin-top: 2px;"><strong>Guru Pembimbing:</strong> <?= esc($p['catatan_pembimbing']) ?></p>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                }
                if (!$hasComments):
                    ?>
                    <p style="font-style: italic; color: #666; height: 50px;">Belum ada catatan dari instruktur maupun
                        pembimbing.</p>
                <?php endif; ?>
            </div>

            <div class="footer-note">
                <p>(*Lembar ini dibuat untuk setiap tugas/pekerjaan yang dilaksanakan peserta didik)</p>
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
        <?php endforeach; ?>
    </div>
</body>

</html>
