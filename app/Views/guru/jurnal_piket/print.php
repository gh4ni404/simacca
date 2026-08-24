<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style id="print-dynamic-style">
        @page {
            size: A4 portrait;
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

        /* List-based Journal Item Mockup styles */
        .journal-item {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .journal-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
        }
        .journal-num {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #10b981;
            color: #fff;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11pt;
            border: 2px solid #047857;
        }
        .journal-text {
            font-size: 11pt;
            font-weight: bold;
            line-height: 1.4;
            color: #000;
        }
        .journal-photos {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-left: 36px;
            margin-top: 8px;
            max-width: 650px;
        }
        .journal-photo-wrapper {
            border: 1px solid #ddd;
            padding: 6px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .journal-photo-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            display: block;
        }
        .hide-photos .journal-photos {
            display: none !important;
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
                    <button type="button" id="btn-landscape" class="btn-toggle" onclick="setOrientation('landscape')">
                        <i class="fas fa-image"></i> Landscape
                    </button>
                    <button type="button" id="btn-portrait" class="btn-toggle active" onclick="setOrientation('portrait')">
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
        <div id="page-sheet" class="page-sheet portrait">
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

            <!-- Jurnal Items List -->
            <div id="jurnal-items" style="margin-top: 15px; margin-bottom: 25px;">
                <?php if (empty($jurnalList)): ?>
                    <p style="text-align: center; padding: 20px; color: #666; font-style: italic;">
                        Tidak ada catatan jurnal piket pada periode yang dipilih.
                    </p>
                <?php else: ?>
                    <?php $no = 1; foreach ($jurnalList as $row): ?>
                        <div class="journal-item">
                            <div class="journal-header">
                                <div class="journal-num"><?= $no++ ?></div>
                                <div class="journal-text">
                                    <div style="font-size: 9.5pt; color: #555; font-weight: normal; margin-bottom: 4px;">
                                        <i class="fas fa-calendar-alt" style="margin-right: 4px; color: #10b981;"></i>
                                        <?= esc(ucfirst(date_to_indo($row['tanggal']))) ?>
                                    </div>
                                    <?= nl2br(esc($row['deskripsi'])) ?>
                                </div>
                            </div>
                            
                            <!-- Foto Dokumentasi -->
                            <?php if (!empty($row['foto_dokumentasi'])): ?>
                                <?php 
                                $fotos = explode(',', $row['foto_dokumentasi']);
                                $validFotos = [];
                                foreach ($fotos as $f) {
                                    $f = trim($f);
                                    if (file_exists(WRITEPATH . 'uploads/jurnal_piket/' . $f)) {
                                        $validFotos[] = $f;
                                    }
                                }
                                if (!empty($validFotos)):
                                ?>
                                    <div class="journal-photos">
                                        <?php foreach ($validFotos as $vf): ?>
                                            <div class="journal-photo-wrapper">
                                                <img src="<?= base_url('files/jurnal-piket/' . $vf) ?>" class="journal-photo-img" alt="Foto Dokumentasi">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

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
    let currentOrientation = 'portrait';
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
        const container = document.getElementById('jurnal-items');
        if (show) {
            container.classList.remove('hide-photos');
        } else {
            container.classList.add('hide-photos');
        }
    }
    </script>
</body>
</html>
