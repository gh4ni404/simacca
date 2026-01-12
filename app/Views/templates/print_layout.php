<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - SIMACCA</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Print-specific styles -->
    <style>
        @media print {
            body {
                margin: 0;
                padding: 20px;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-after: always;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            thead {
                display: table-header-group;
            }
            
            tfoot {
                display: table-footer-group;
            }
        }
        
        @page {
            size: <?= $this->renderSection('page_size') ?: 'A4' ?>;
            margin: 1cm;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .kop-surat h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
        }
        
        .kop-surat p {
            margin: 2px 0;
            font-size: 10pt;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 45%;
        }
        
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>

<body>
    
    <!-- Print Controls (hidden when printing) -->
    <div class="no-print fixed top-4 right-4 space-x-2 z-50">
        <button onclick="window.print()" 
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow">
            <i class="fas fa-print mr-2"></i>Cetak
        </button>
        <button onclick="window.close()" 
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded shadow">
            <i class="fas fa-times mr-2"></i>Tutup
        </button>
    </div>
    
    <!-- Main Content -->
    <div class="container mx-auto p-8">
        <?= $this->renderSection('content') ?>
    </div>
    
    <!-- Scripts -->
    <script>
        // Auto print on load (optional, can be disabled)
        <?php if ($auto_print ?? false): ?>
        window.onload = function() {
            window.print();
        };
        <?php endif; ?>
    </script>
    
    <?= $this->renderSection('scripts') ?>
    
</body>

</html>
