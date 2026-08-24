<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style id="print-dynamic-style">
        @page {
            size: A4 landscape;
            margin: 10mm 12mm;
        }
    </style>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #000;
            background: #f1f5f9;
            padding: 0;
            margin: 0;
        }

        /* Top Toolbar Controls */
        .print-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #1e293b;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
        }

        .toolbar-left, .toolbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .toolbar-divider {
            width: 1px;
            height: 24px;
            background: #475569;
        }

        .tool-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tool-group label {
            font-size: 12px;
            color: #cbd5e1;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-group-toggle {
            display: inline-flex;
            background: #334155;
            border-radius: 8px;
            padding: 2px;
        }

        .btn-toggle {
            background: transparent;
            color: #cbd5e1;
            border: none;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-toggle:hover {
            color: #fff;
        }

        .btn-toggle.active {
            background: #4f46e5;
            color: #fff;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.4);
        }

        .form-select-sm {
            background: #334155;
            color: #fff;
            border: 1px solid #475569;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            outline: none;
        }

        .toggle-checkbox-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            color: #e2e8f0;
            font-size: 12px;
            user-select: none;
        }

        .toggle-checkbox-label input {
            cursor: pointer;
            accent-color: #4f46e5;
        }

        .btn-tool {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
            transition: all 0.15s;
        }

        .btn-back {
            background: #475569;
            color: #fff;
        }

        .btn-back:hover {
            background: #64748b;
        }

        .btn-print-main {
            background: #4f46e5;
            color: #fff;
            box-shadow: 0 3px 10px rgba(79, 70, 229, 0.4);
        }

        .btn-print-main:hover {
            background: #4338ca;
        }

        /* Sheet / Paper Presentation */
        .sheet-container {
            padding: 80px 20px 40px 20px;
            display: flex;
            justify-content: center;
        }

        .page-sheet {
            background: #fff;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            padding: 20mm 15mm;
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .page-sheet.landscape {
            width: 297mm;
            min-height: 210mm;
        }

        .page-sheet.portrait {
            width: 210mm;
            min-height: 297mm;
        }

        /* Header Kop Surat */
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 10px;
        }

        .logo {
            width: 55px;
            height: 55px;
            flex-shrink: 0;
        }

        .logo img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        .header-text {
            text-align: center;
            flex: 1;
        }

        .header-text h3 {
            font-size: 11pt;
            font-weight: bold;
            line-height: 1.15;
            text-transform: uppercase;
        }

        .header-text h2 {
            font-size: 13pt;
            font-weight: bold;
            line-height: 1.2;
            margin: 1px 0;
        }

        .header-text p {
            font-size: 8.5pt;
            line-height: 1.2;
        }

        /* Document Title */
        .document-title {
            text-align: center;
            margin: 10px 0 12px 0;
        }

        .document-title h2 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .document-title p {
            font-size: 9pt;
            color: #333;
            margin-top: 2px;
        }

        /* Teacher Meta Info */
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }

        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .meta-table .label {
            width: 130px;
            font-weight: bold;
        }

        .meta-table .colon {
            width: 15px;
            text-align: center;
        }

        /* Table Laporan */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
        }

        table.data-table th, 
        table.data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }

        table.data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .thumb-photo {
            max-width: 60px;
            max-height: 60px;
            border-radius: 4px;
            border: 1px solid #ccc;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 240px;
            font-size: 9.5pt;
        }

        .signature-box .title {
            margin-bottom: 60px;
            line-height: 1.25;
        }

        .signature-box .name {
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-box .nip {
            margin-top: 2px;
            font-size: 9pt;
        }

        .hide-photos .col-photo {
            display: none !important;
        }

        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .sheet-container {
                padding: 0 !important;
                display: block !important;
            }
            .page-sheet {
                box-shadow: none !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: auto !important;
            }
            table.data-table th {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Top Interactive Toolbar -->
    <div class="print-toolbar no-print">
        <div class="toolbar-left">
            <a href="<?= base_url('guru/jurnal-piket') ?>" class="btn-tool btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <div class="toolbar-divider"></div>
            
            <!-- Orientation Toggle -->
            <div class="tool-group">
                <label><i class="fas fa-compass"></i> Orientasi:</label>
                <div class="btn-group-toggle">
                    <button type="button" id="btn-landscape" class="btn-toggle active" onclick="setOrientation('landscape')">
                        <i class="fas fa-image"></i> Landscape
                    </button>
                    <button type="button" id="btn-portrait" class="btn-toggle" onclick="setOrientation('portrait')">
                        <i class="fas fa-file-alt"></i> Portrait
                    </button>
                </div>
            </div>

            <!-- Paper Size Selector -->
            <div class="tool-group">
                <label><i class="fas fa-scroll"></i> Kertas:</label>
                <select id="paper-size-select" class="form-select-sm" onchange="setPaperSize(this.value)">
                    <option value="A4" selected>A4 (210 x 297 mm)</option>
                    <option value="legal">F4 / Folio (215 x 330 mm)</option>
                    <option value="letter">Letter</option>
                </select>
            </div>

            <div class="toolbar-divider"></div>

            <!-- Toggle Foto -->
            <div class="tool-group">
                <label class="toggle-checkbox-label">
                    <input type="checkbox" id="toggle-photo" checked onchange="togglePhotos(this.checked)">
                    <span>Tampilkan Foto</span>
                </label>
            </div>
        </div>

        <div class="toolbar-right">
            <button onclick="window.print()" class="btn-tool btn-print-main">
                <i class="fas fa-print"></i> Cetak Laporan (Ctrl+P)
            </button>
        </div>
    </div>

    <div class="sheet-container">
        <div id="page-sheet" class="page-sheet landscape">
            <!-- Header Kop Surat -->
            <div class="header">
                <div class="header-content">
                    <div class="logo">
                        <img src="<?= base_url('assets/images/provinsi.png') ?>" alt="Logo Provinsi" onerror="this.style.display='none'">
                    </div>
                    <div class="header-text">
                        <h3>PEMERINTAH PROVINSI SULAWESI SELATAN</h3>
                        <h3>DINAS PENDIDIKAN</h3>
                        <h3>CABANG DINAS PENDIDIKAN WILAYAH III</h3>
                        <h2>UPT SMKN 8 BONE</h2>
                        <p><em>Alamat: Jln. Poros Bone – Sengkang Welado Kec. Ajangale Kode Pos 92755</em></p>
                        <p><em>Email: smkn8bone@gmail.com &bull; Website: smkn8bone.sch.id</em></p>
                    </div>
                    <div class="logo">
                        <img src="<?= base_url('assets/images/sekolah.png') ?>" alt="Logo Sekolah" onerror="this.style.display='none'">
                    </div>
                </div>
            </div>

            <!-- Document Title -->
            <div class="document-title">
                <h2>LAPORAN REKAPITULASI JURNAL PIKET GURU</h2>
                <p>
                    Tahun Ajaran: <?= esc($tahunAjaran) ?>
                    <?php if (!empty($startDate) && !empty($endDate)): ?>
                        | Periode: <?= date('d/m/Y', strtotime($startDate)) ?> s.d. <?= date('d/m/Y', strtotime($endDate)) ?>
                    <?php elseif (!empty($startDate)): ?>
                        | Mulai Tanggal: <?= date('d/m/Y', strtotime($startDate)) ?>
                    <?php elseif (!empty($endDate)): ?>
                        | Sampai Tanggal: <?= date('d/m/Y', strtotime($endDate)) ?>
                    <?php endif; ?>
                </p>
            </div>

            <!-- Teacher Meta Table -->
            <table class="meta-table">
                <tr>
                    <td class="label">Nama Guru Piket</td>
                    <td class="colon">:</td>
                    <td><strong><?= esc($guru['nama_lengkap']) ?></strong></td>
                    <td class="label" style="width: 110px;">Tanggal Cetak</td>
                    <td class="colon">:</td>
                    <td style="width: 150px;"><?= date('d F Y') ?></td>
                </tr>
                <tr>
                    <td class="label">NIP</td>
                    <td class="colon">:</td>
                    <td><?= esc($guru['nip'] ?: '-') ?></td>
                    <td class="label">Total Jurnal</td>
                    <td class="colon">:</td>
                    <td><?= count($jurnalList) ?> kegiatan</td>
                </tr>
            </table>

            <!-- Table Data -->
            <table class="data-table" id="jurnal-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 13%;">Hari & Tanggal</th>
                        <th style="width: 24%;">Rincian Panduan Tugas</th>
                        <th style="width: 32%;">Uraian Pelaksanaan Kegiatan</th>
                        <th style="width: 19%;">Catatan Kejadian Khusus</th>
                        <th style="width: 8%;" class="col-photo">Foto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jurnalList)): ?>
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 20px; color: #666;">
                                Tidak ada catatan jurnal piket pada periode yang dipilih.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($jurnalList as $row): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td>
                                    <strong><?= esc(ucfirst(date_to_indo($row['tanggal']))) ?></strong><br>
                                    <span><?= date('d/m/Y', strtotime($row['tanggal'])) ?></span><br>
                                    <small style="color:#555;">Sem. <?= esc(ucfirst($row['semester'] ?? '')) ?></small>
                                </td>
                                <td>
                                    <?= !empty($row['rincian_tugas']) ? nl2br(esc($row['rincian_tugas'])) : '<em style="color:#888;">-</em>' ?>
                                </td>
                                <td>
                                    <?= nl2br(esc($row['deskripsi'])) ?>
                                </td>
                                <td>
                                    <?= !empty($row['catatan']) ? nl2br(esc($row['catatan'])) : '<em style="color:#888;">-</em>' ?>
                                </td>
                                <td class="text-center col-photo">
                                    <?php if (!empty($row['foto_dokumentasi'])): ?>
                                        <?php 
                                        $fotos = explode(',', $row['foto_dokumentasi']);
                                        $printed = false;
                                        foreach ($fotos as $f):
                                            $f = trim($f);
                                            if (file_exists(WRITEPATH . 'uploads/jurnal_piket/' . $f)):
                                                $printed = true;
                                        ?>
                                                <img src="<?= base_url('files/jurnal-piket/' . $f) ?>" alt="Foto" class="thumb-photo" style="margin: 2px; max-width: 40px; max-height: 40px; object-fit: cover;">
                                        <?php 
                                            endif;
                                        endforeach;
                                        if (!$printed):
                                        ?>
                                            <span style="font-size: 8pt; color: #059669;">Ada Foto</span>
                                        <?php 
                                        endif;
                                        ?>
                                    <?php else: ?>
                                        <span style="color: #999;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="title">
                        Mengetahui,<br>
                        Kepala UPT SMKN 8 Bone
                    </div>
                    <div class="name">_______________________</div>
                    <div class="nip">NIP. ........................................</div>
                </div>

                <div class="signature-box">
                    <div class="title">
                        Welado, <?= date('d F Y') ?><br>
                        Guru Piket Pelaksana
                    </div>
                    <div class="name"><?= esc($guru['nama_lengkap']) ?></div>
                    <div class="nip">NIP. <?= esc($guru['nip'] ?: '........................................') ?></div>
                </div>
            </div>
        </div>
    </div>

    <script>
    let currentOrientation = 'landscape';
    let currentPaperSize = 'A4';

    function updatePageStyles() {
        const dynamicStyle = document.getElementById('print-dynamic-style');
        dynamicStyle.innerHTML = `@page { size: ${currentPaperSize} ${currentOrientation}; margin: 10mm 12mm; }`;
        
        const sheet = document.getElementById('page-sheet');
        sheet.className = `page-sheet ${currentOrientation}`;
    }

    function setOrientation(orientation) {
        currentOrientation = orientation;
        document.getElementById('btn-landscape').classList.toggle('active', orientation === 'landscape');
        document.getElementById('btn-portrait').classList.toggle('active', orientation === 'portrait');
        updatePageStyles();
    }

    function setPaperSize(size) {
        currentPaperSize = size;
        updatePageStyles();
    }

    function togglePhotos(show) {
        const table = document.getElementById('jurnal-table');
        if (show) {
            table.classList.remove('hide-photos');
        } else {
            table.classList.add('hide-photos');
        }
    }
    </script>
</body>
</html>
