# 🚀 Performance Optimization Guide - SIMACCA

> **Last Updated:** 2026-01-30  
> **Version:** 1.0  
> **Baseline Analysis Date:** 2026-01-30

## 📋 Table of Contents

- [Overview](#overview)
- [Current Performance Baseline](#current-performance-baseline)
- [Quick Wins (Week 1)](#quick-wins-week-1)
- [Medium Impact (Week 2)](#medium-impact-week-2)
- [Long-term Optimizations (Week 3+)](#long-term-optimizations-week-3)
- [Testing & Verification](#testing--verification)
- [Troubleshooting](#troubleshooting)

---

## 📊 Overview

This guide provides step-by-step instructions for optimizing SIMACCA performance. Optimizations are prioritized by impact vs. effort ratio.

### Performance Goals

- **Dashboard Load Time:** 40-60% faster
- **Query Execution:** 10-20% faster  
- **Page Size:** 200-300KB smaller
- **Overall Performance:** 70-100% improvement

### Implementation Timeline

- **Week 1:** Quick Wins (1-2 hours) → 50-70% boost
- **Week 2:** Medium Impact (3-4 hours) → +30-40% improvement
- **Week 3+:** Long-term (optional) → +20-30% improvement

---

## 📈 Current Performance Baseline

**Analysis Date:** 2026-01-30

### Database Metrics
- **Tables:** 6 core tables
- **Total Rows:** 4,043 records
  - absensi: 188 rows
  - absensi_detail: 3,466 rows
  - guru: 25 rows
  - jadwal_mengajar: 182 rows
  - jurnal_kbm: 179 rows
  - izin_siswa: 5 rows
- **Migrations:** 20 applied successfully
- **Connection:** MySQLi, localhost:3306

### Application Metrics
- **Session Storage:** File-based, 8-hour expiration
- **Caching:** Minimal (manual cache()->clean() only)
- **Assets:** CDN-based (Tailwind play CDN, FontAwesome)
- **Uploads:** 139 files, 169MB total

### Current Strengths ✅
- Optimized queries (no N+1 problems)
- Proper JOINs and relationships
- Good session configuration
- Clean asset structure

### Identified Issues ⚠️
- No result caching
- No persistent DB connections
- Non-production CDN usage
- OPcache not configured
- Limited query caching

---

## ⚡ Quick Wins (Week 1)

**Total Time:** 1-2 hours  
**Expected Improvement:** 50-70% performance boost

---

### 🎯 Optimization #1: Enable Query Result Caching

**Impact:** HIGH | **Effort:** LOW | **Time:** 30 minutes

#### Problem
Dashboard queries run on every page load, recalculating statistics repeatedly.

#### Solution
Implement CodeIgniter's built-in caching for frequently accessed data.

#### Implementation Steps

**Step 1: Cache Dashboard Statistics**

Edit `app/Controllers/Guru/DashboardController.php`:

```php
private function getGuruStats($guruId)
{
    $cacheKey = "guru_stats_{$guruId}_" . date('Y-m-d-H');
    
    return cache()->remember($cacheKey, 900, function() use ($guruId) {
        $today = date('Y-m-d');
        $currentMonth = date('m');
        $currentYear = date('Y');

        // ... existing stats calculation code ...
        
        return [
            'total_jadwal' => $totalJadwal,
            'absensi_bulan_ini' => $absensiBulanIni['total_pertemuan'] ?? 0,
            'jurnal_bulan_ini' => $jurnalBulanIni['total'] ?? 0,
            'total_kelas' => $totalKelas['total'] ?? 0,
            'absensi_hari_ini' => $absensiHariIni
        ];
    });
}
```

**Step 2: Cache Jadwal Data**

```php
private function getJadwalHariIni($guruId)
{
    $hariIni = $this->getHariIni(); // extract hari logic
    $cacheKey = "jadwal_hari_ini_{$guruId}_{$hariIni}";
    
    return cache()->remember($cacheKey, 3600, function() use ($guruId, $hariIni) {
        return $this->jadwalModel->select('jadwal_mengajar.*, mata_pelajaran.nama_mapel, kelas.nama_kelas, kelas.tingkat')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('guru_id', $guruId)
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai', 'ASC')
            ->findAll();
    });
}
```

**Step 3: Cache Dropdown Data in Models**

Edit `app/Models/MataPelajaranModel.php`:

```php
public function getAllCached($ttl = 3600)
{
    return cache()->remember('mata_pelajaran_all', $ttl, function() {
        return $this->orderBy('nama_mapel', 'ASC')->findAll();
    });
}
```

Edit `app/Models/KelasModel.php`:

```php
public function getAllCached($ttl = 3600)
{
    return cache()->remember('kelas_all', $ttl, function() {
        return $this->orderBy('tingkat', 'ASC')
            ->orderBy('nama_kelas', 'ASC')
            ->findAll();
    });
}
```

**Step 4: Invalidate Cache on Data Changes**

Add to controllers when creating/updating/deleting:

```php
// In MataPelajaranController after save/delete
cache()->delete('mata_pelajaran_all');

// In KelasController after save/delete
cache()->delete('kelas_all');

// In JadwalController after save/delete
$guruId = $data['guru_id'];
cache()->delete("jadwal_hari_ini_{$guruId}_*"); // requires wildcard support
```

**Step 5: Update Controllers to Use Cached Methods**

```php
// Instead of:
$mapelList = $this->mataPelajaranModel->findAll();

// Use:
$mapelList = $this->mataPelajaranModel->getAllCached();
```

#### Testing
```bash
# Test dashboard load time
# Before: Time the page load
# After: Time should be 40-60% faster on subsequent loads
```

#### Expected Results
- Dashboard loads: **40-60% faster**
- Reduced database queries: **~70% reduction**
- Cache TTL: 15 minutes for stats, 1 hour for dropdowns

---

### 🔗 Optimization #2: Enable Database Connection Pooling

**Impact:** MEDIUM | **Effort:** LOW | **Time:** 15 minutes

#### Problem
Application creates new database connections for each request, adding overhead.

#### Solution
Enable persistent connections (connection pooling) in CodeIgniter.

#### Implementation Steps

**Step 1: Edit Database Configuration**

File: `app/Config/Database.php`

Find line 154 and change:

```php
// BEFORE
'pConnect'    => false,

// AFTER
'pConnect'    => true,
```

**Step 2: Verify Configuration**

Ensure these related settings are correct:

```php
public array $default = [
    'DSN'         => '',
    'hostname'    => env('database.default.hostname', 'localhost'),
    'username'    => env('database.default.username', 'root'),
    'password'    => env('database.default.password', ''),
    'database'    => env('database.default.database', 'simacca_database'),
    'DBDriver'    => 'MySQLi',
    'DBPrefix'    => '',
    'pConnect'    => true,  // ← CHANGED
    'DBDebug'     => true,
    'charset'     => 'utf8mb4',
    'DBCollat'    => 'utf8mb4_general_ci',
    'swapPre'     => '',
    'encrypt'     => false,
    'compress'    => false,
    'strictOn'    => false,
    'failover'    => [],
    'port'        => 3306,
    'numberNative' => false,
];
```

**Step 3: Restart Application**

```bash
# Clear any existing connections
php spark cache:clear

# If using PHP-FPM, restart it
sudo service php8.2-fpm restart
```

#### Testing
```bash
# Check MySQL connections
mysql -u root -p -e "SHOW PROCESSLIST;"

# Before: Multiple new connections per request
# After: Persistent connections reused
```

#### Expected Results
- Query execution: **10-20% faster**
- Reduced connection overhead
- Better resource utilization

#### Important Notes
⚠️ **Monitor connection limits:** Ensure MySQL `max_connections` is adequate
⚠️ **Development vs Production:** Keep `pConnect = false` in development for easier debugging

---

### 📦 Optimization #3: Optimize CDN Asset Loading

**Impact:** MEDIUM | **Effort:** LOW | **Time:** 20 minutes

#### Problem
- TailwindCSS loaded via play CDN (not for production)
- FontAwesome (132KB) loaded on every page
- Flatpickr CSS loaded even when not used
- No integrity/crossorigin attributes

#### Solution
Use production builds and conditional loading for better performance.

#### Implementation Steps

**Step 1: Replace Tailwind Play CDN**

Option A - Use Tailwind CDN Production Build:

Edit `app/Views/templates/main_layout.php` (line 7):

```php
<!-- BEFORE -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- AFTER -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet" 
      integrity="sha384-..." crossorigin="anonymous">
```

Option B - Build Your Own (Recommended for production):

```bash
# Install Tailwind
npm install -D tailwindcss

# Create config
npx tailwindcss init

# Build CSS
npx tailwindcss -i ./input.css -o ./public/assets/css/tailwind.css --minify
```

Then in layout:
```php
<link href="<?= base_url('assets/css/tailwind.css') ?>" rel="stylesheet">
```

**Step 2: Conditionally Load FontAwesome**

Create a helper function in `app/Helpers/component_helper.php`:

```php
if (!function_exists('load_fontawesome')) {
    function load_fontawesome($force = false) {
        if ($force || !isset($_SESSION['fontawesome_loaded'])) {
            $_SESSION['fontawesome_loaded'] = true;
            return '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
                    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
                    crossorigin="anonymous" referrerpolicy="no-referrer">';
        }
        return '';
    }
}
```

In layout:
```php
<!-- Only load if needed -->
<?= load_fontawesome(true) ?>
```

**Step 3: Conditionally Load Flatpickr**

In `main_layout.php`, move Flatpickr to a section:

```php
<?php if (isset($needsDatePicker) && $needsDatePicker): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
<?php endif; ?>
```

In controllers that need it:
```php
$data['needsDatePicker'] = true;
```

**Step 4: Add Integrity Attributes**

Add SRI (Subresource Integrity) to all CDN links:

```php
<link rel="stylesheet" 
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
      crossorigin="anonymous" 
      referrerpolicy="no-referrer">
```

#### Testing
```bash
# Check page size before and after
curl -s -w '%{size_download}\n' -o /dev/null https://your-site.com/guru/dashboard

# Check network tab in browser DevTools
# Before: ~400KB total assets
# After: ~150KB total assets
```

#### Expected Results
- Page size: **200-300KB smaller**
- Faster initial load
- Better security (SRI)
- Reduced bandwidth usage

---


## ?? Medium Impact (Week 2)

**Total Time:** 3-4 hours  
**Expected Improvement:** Additional 30-40% boost

---

### ?? Optimization #4: Enable OPcache with Preloading

**Impact:** HIGH | **Effort:** MEDIUM | **Time:** 45 minutes

#### Problem
PHP files are compiled on every request, wasting CPU cycles.

#### Solution
Enable OPcache and configure preloading to cache compiled PHP code.

#### Implementation Steps

**Step 1: Check if OPcache is Installed**

```bash
php -i | grep opcache

# Or check info
php -r "phpinfo();" | grep opcache
```

**Step 2: Configure php.ini**

Edit `php.ini` (usually at `/etc/php/8.2/fpm/php.ini` or `/etc/php/8.2/cli/php.ini`):

```ini
[opcache]
; Enable OPcache
opcache.enable=1
opcache.enable_cli=1

; Memory settings
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000

; Revalidation settings
opcache.revalidate_freq=2
opcache.validate_timestamps=1

; Preloading (PHP 7.4+)
opcache.preload=/path/to/your/simacca/preload.php
opcache.preload_user=www-data
```

**Step 3: Update preload.php**

Edit `preload.php` in project root:

```php
<?php

use CodeIgniter\Boot;
use Config\Paths;

require __DIR__ . '/app/Config/Paths.php';

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

class preload
{
    private array \ = [
        [
            'include' => __DIR__ . '/vendor/codeigniter4/framework/system',
            'exclude' => [
                '/system/Database/OCI8/',
                '/system/Database/Postgre/',
                '/system/Database/SQLite3/',
                '/system/Database/SQLSRV/',
                '/system/Database/Seeder.php',
                '/system/Test/',
                '/system/CLI/',
                '/system/Commands/',
                '/system/Publisher/',
                '/system/ComposerScripts.php',
                '/system/Config/Routes.php',
                '/system/Language/',
                '/system/bootstrap.php',
                '/system/util_bootstrap.php',
                '/system/rewrite.php',
                '/Views/',
                '/system/ThirdParty/',
            ],
        ],
        // Add your app files
        [
            'include' => __DIR__ . '/app',
            'exclude' => [
                '/app/Views/',
                '/app/Config/Routes.php',
                '/app/Database/',
                '/app/Commands/',
            ],
        ],
    ];

    public function __construct()
    {
        \->loadAutoloader();
    }

    private function loadAutoloader(): void
    {
        \ = new Paths();
        require rtrim(\->systemDirectory, '\\\/ ') . DIRECTORY_SEPARATOR . 'Boot.php';
        Boot::preload(\);
    }

    public function load(): void
    {
        foreach (\->paths as \) {
            \ = new RecursiveDirectoryIterator(\['include']);
            \  = new RecursiveIteratorIterator(\);
            \  = new RegexIterator(
                \,
                '/.+((?<!Test)+\.php\$)/i',
                RecursiveRegexIterator::GET_MATCH,
            );

            foreach (\ as \ => \) {
                foreach (\['exclude'] as \) {
                    if (str_contains(\[0], \)) {
                        continue 2;
                    }
                }

                require_once \[0];
            }
        }
    }
}

(new preload())->load();
```

**Step 4: Restart PHP-FPM**

```bash
sudo service php8.2-fpm restart

# Or for Apache module
sudo service apache2 restart
```

**Step 5: Verify OPcache is Working**

Create `public/opcache-status.php`:

```php
<?php
\ = opcache_get_status();
echo "<pre>";
print_r(\);
echo "</pre>";

// Security: Delete this file after checking!
```

Visit: `https://your-site.com/opcache-status.php`

#### Testing

```bash
# Test page load time
time curl -s https://your-site.com/guru/dashboard > /dev/null

# Before: ~500ms
# After: ~250ms (50% faster)
```

#### Expected Results
- PHP execution: **30-50% faster**
- Reduced CPU usage
- Better scalability

#### Important Notes
?? **Clear OPcache after code changes:** `php spark cache:clear` won't clear OPcache
?? **Use cautiously in development:** Set `opcache.validate_timestamps=1` for dev

---

### ?? Optimization #5: Implement Database Query Caching

**Impact:** MEDIUM | **Effort:** MEDIUM | **Time:** 1 hour

#### Problem
Dropdown data and rarely-changed lists are queried repeatedly.

#### Solution
Cache query results for data that changes infrequently.

#### Implementation Steps

**Step 1: Create Cached Model Methods**

Add to `app/Models/GuruModel.php`:

```php
public function getGuruListCached(\ = 3600)
{
    return cache()->remember('guru_list_dropdown', \, function() {
        return \->select('id, nama_lengkap, nip')
            ->where('is_active', 1)
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
    });
}
```

Add to `app/Models/SiswaModel.php`:

```php
public function getSiswaByKelasCached(\, \ = 1800)
{
    return cache()->remember("siswa_kelas_{\}", \, function() use (\) {
        return \->where('kelas_id', \)
            ->where('is_active', 1)
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
    });
}
```

Add to `app/Models/JadwalMengajarModel.php`:

```php
public function getJadwalBySemesterCached(\, \, \, \ = 86400)
{
    \ = "jadwal_semester_{\}_{\}_{\}";
    
    return cache()->remember(\, \, function() use (\, \, \) {
        return \->select('jadwal_mengajar.*, mata_pelajaran.nama_mapel, kelas.nama_kelas')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('guru_id', \)
            ->where('semester', \)
            ->where('tahun_ajaran', \)
            ->orderBy('hari', 'ASC')
            ->orderBy('jam_mulai', 'ASC')
            ->findAll();
    });
}
```

**Step 2: Create Cache Invalidation Helper**

Create `app/Helpers/cache_helper.php`:

```php
<?php

if (!function_exists('invalidate_model_cache')) {
    function invalidate_model_cache(string \, mixed \ = null): void
    {
        \ = [
            'guru' => ['guru_list_dropdown'],
            'mata_pelajaran' => ['mata_pelajaran_all'],
            'kelas' => ['kelas_all'],
            'siswa' => \ ? ["siswa_kelas_*"] : [],
            'jadwal' => \ ? ["jadwal_semester_*", "jadwal_hari_ini_*"] : [],
        ];

        if (isset(\[\])) {
            foreach (\[\] as \) {
                // Note: CI4 doesn't support wildcard delete by default
                // You may need to track cache keys or use Redis
                cache()->delete(\);
            }
        }
    }
}

if (!function_exists('clear_all_model_caches')) {
    function clear_all_model_caches(): void
    {
        \ = [
            'guru_list_dropdown',
            'mata_pelajaran_all',
            'kelas_all',
            // Add pattern-based cache clearing if using Redis
        ];

        foreach (\ as \) {
            cache()->delete(\);
        }
    }
}
```

**Step 3: Update Autoload**

Edit `app/Config/Autoload.php`:

```php
public \ = ['auth', 'component', 'email', 'image', 'security', 'cache'];
```

**Step 4: Update Controllers**

In `app/Controllers/Admin/GuruController.php`:

```php
public function store()
{
    // ... validation and save code ...
    
    if (\->guruModel->save(\)) {
        invalidate_model_cache('guru');
        return redirect()->to('/admin/guru')->with('success', 'Data guru berhasil ditambahkan');
    }
}

public function update(\)
{
    // ... validation and update code ...
    
    if (\->guruModel->update(\, \)) {
        invalidate_model_cache('guru');
        return redirect()->to('/admin/guru')->with('success', 'Data guru berhasil diupdate');
    }
}
```

**Step 5: Use Cached Methods in Controllers**

```php
// Instead of:
\ = \->guruModel->orderBy('nama_lengkap', 'ASC')->findAll();

// Use:
\ = \->guruModel->getGuruListCached();
```

#### Testing

```bash
# Check cache files
ls -lh writable/cache/

# Test form load time
# Before: ~300ms
# After: ~100ms (70% faster on subsequent loads)
```

#### Expected Results
- Form loads: **50-70% faster**
- Reduced database load
- Better response times

---

### ??? Optimization #6: Optimize Image Uploads

**Impact:** MEDIUM | **Effort:** MEDIUM | **Time:** 1 hour

#### Problem
169MB of uploads with potential unoptimized images.

#### Solution
Enhance image optimization system and add lazy loading.

#### Implementation Steps

**Step 1: Verify Image Helper is Used**

Check `app/Helpers/image_helper.php` exists and has optimization functions.

**Step 2: Add Lazy Loading to Views**

Edit image display in views (e.g., `app/Views/guru/jurnal/index.php`):

```php
<!-- BEFORE -->
<img src="<?= base_url('files/jurnal/' . \['foto']) ?>" alt="Foto Jurnal">

<!-- AFTER -->
<img src="<?= base_url('files/jurnal/' . \['foto']) ?>" 
     alt="Foto Jurnal"
     loading="lazy"
     class="w-full h-auto">
```

**Step 3: Add Thumbnail Generation**

Add to `app/Helpers/image_helper.php`:

```php
if (!function_exists('create_thumbnail')) {
    function create_thumbnail(string \, int \ = 300, int \ = 300): string|false
    {
        try {
            \ = \Config\Services::image();
            \ = pathinfo(\);
            \ = \['dirname'] . '/thumb_' . \['basename'];

            \->withFile(\)
                ->fit(\, \, 'center')
                ->save(\, 80);

            return \;
        } catch (\Exception \) {
            log_message('error', 'Thumbnail creation failed: ' . \->getMessage());
            return false;
        }
    }
}
```

**Step 4: Generate Thumbnails on Upload**

Update upload controllers:

```php
// After successful upload
\ = 'writable/uploads/jurnal/' . \;
create_thumbnail(\, 300, 300);
```

**Step 5: Use Thumbnails in List Views**

```php
<!-- For lists/grids -->
<img src="<?= base_url('files/jurnal/thumb_' . \['foto']) ?>" 
     alt="Foto Jurnal Thumbnail"
     loading="lazy">

<!-- Full size on detail/lightbox -->
<a href="<?= base_url('files/jurnal/' . \['foto']) ?>" data-lightbox="jurnal">
    <img src="<?= base_url('files/jurnal/thumb_' . \['foto']) ?>" loading="lazy">
</a>
```

**Step 6: Add Bulk Image Optimization Script**

Create `app/Commands/OptimizeImages.php`:

```php
<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class OptimizeImages extends BaseCommand
{
    protected \       = 'Maintenance';
    protected \        = 'images:optimize';
    protected \ = 'Optimize all uploaded images';

    public function run(array \)
    {
        \ = WRITEPATH . 'uploads/';
        \ = ['jurnal', 'izin', 'profile'];
        
        CLI::write('Starting image optimization...', 'yellow');
        
        \ = 0;
        \ = 0;
        
        foreach (\ as \) {
            \ = \ . \;
            
            if (!is_dir(\)) {
                continue;
            }
            
            \ = glob(\ . '/*.{jpg,jpeg,png}', GLOB_BRACE);
            
            foreach (\ as \) {
                \ = filesize(\);
                
                // Optimize image
                \ = \Config\Services::image();
                \->withFile(\)
                    ->save(\, 85);
                
                \ = filesize(\);
                \ = \ - \;
                
                if (\ > 0) {
                    \++;
                    \ += \;
                    CLI::write("Optimized: " . basename(\) . " (saved " . format_bytes(\) . ")", 'green');
                }
            }
        }
        
        CLI::write("\nOptimization complete!", 'green');
        CLI::write("Files optimized: \", 'yellow');
        CLI::write("Space saved: " . format_bytes(\), 'yellow');
    }
}
```

**Step 7: Run Bulk Optimization**

```bash
php spark images:optimize
```

#### Testing

```bash
# Check image sizes
du -sh writable/uploads/*

# Before: 169MB
# After: ~80-100MB (40-60% reduction)
```

#### Expected Results
- Image sizes: **40-60% smaller**
- Faster page loads
- Reduced bandwidth
- Better mobile experience

---


## ?? Long-term Optimizations (Week 3+)

**Total Time:** Variable (1-3 days)  
**Expected Improvement:** Scalability + 20-30% additional boost

---

### ?? Optimization #7: Database Indexing Review

**Impact:** MEDIUM | **Effort:** HIGH | **Time:** 2-3 hours

#### Problem
Missing composite indexes on frequently joined/filtered columns.

#### Solution
Add strategic indexes to improve query performance.

#### Implementation Steps

**Step 1: Analyze Current Indexes**

```sql
-- Check existing indexes
SHOW INDEXES FROM absensi;
SHOW INDEXES FROM absensi_detail;
SHOW INDEXES FROM jadwal_mengajar;
SHOW INDEXES FROM jurnal_kbm;
```

**Step 2: Create Composite Indexes**

Create `app/Database/Migrations/2026-01-31-000000_AddPerformanceIndexes.php`:

```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // Composite index for absensi queries
        \->db->query('CREATE INDEX idx_absensi_jadwal_tanggal 
                          ON absensi(jadwal_mengajar_id, tanggal)');
        
        // Index for absensi_detail filtering
        \->db->query('CREATE INDEX idx_absensi_detail_status 
                          ON absensi_detail(absensi_id, status)');
        
        // Index for jurnal queries
        \->db->query('CREATE INDEX idx_jurnal_absensi 
                          ON jurnal_kbm(absensi_id)');
        
        // Index for jadwal queries by guru and hari
        \->db->query('CREATE INDEX idx_jadwal_guru_hari 
                          ON jadwal_mengajar(guru_id, hari)');
        
        // Index for siswa by kelas
        \->db->query('CREATE INDEX idx_siswa_kelas_active 
                          ON siswa(kelas_id, is_active)');
    }

    public function down()
    {
        \->db->query('DROP INDEX idx_absensi_jadwal_tanggal ON absensi');
        \->db->query('DROP INDEX idx_absensi_detail_status ON absensi_detail');
        \->db->query('DROP INDEX idx_jurnal_absensi ON jurnal_kbm');
        \->db->query('DROP INDEX idx_jadwal_guru_hari ON jadwal_mengajar');
        \->db->query('DROP INDEX idx_siswa_kelas_active ON siswa');
    }
}
```

**Step 3: Run Migration**

```bash
php spark migrate
```

**Step 4: Analyze Query Performance**

```sql
-- Enable slow query log
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;

-- After some time, check slow queries
SELECT * FROM mysql.slow_log ORDER BY query_time DESC LIMIT 10;
```

#### Expected Results
- Query speed: **20-40% faster** for complex joins
- Better performance with large datasets
- Reduced table scans

---

### ?? Optimization #8: Page Caching Implementation

**Impact:** LOW-MEDIUM | **Effort:** HIGH | **Time:** 2-3 hours

#### Problem
Static/semi-static pages regenerated on every request.

#### Solution
Implement page caching for appropriate pages.

#### Implementation Steps

**Step 1: Enable Page Caching in Config**

Edit `app/Config/Cache.php`:

```php
public \ = false; // or true if needed
```

**Step 2: Add Caching to Print Layouts**

In `app/Controllers/Guru/LaporanController.php`:

```php
public function print(\)
{
    // Cache print pages for 1 hour
    \->cachePage(3600);
    
    // ... rest of code ...
}
```

**Step 3: Add Caching to Static Reports**

```php
public function statistik()
{
    // Cache monthly stats for 6 hours
    if (!\->request->getGet('refresh')) {
        \->cachePage(21600);
    }
    
    // ... rest of code ...
}
```

**Step 4: Create Cache Invalidation Method**

```php
protected function invalidatePageCache(string \): void
{
    \ = WRITEPATH . 'cache/' . md5(\);
    if (file_exists(\)) {
        unlink(\);
    }
}
```

#### Expected Results
- Static page loads: **80-90% faster**
- Reduced server load
- Better scalability

---

### ?? Optimization #9: Redis/Memcached Upgrade

**Impact:** HIGH | **Effort:** HIGH | **Time:** 1 day

#### Problem
File-based caching is slow and not suitable for multi-server setups.

#### Solution
Implement Redis for caching and session storage.

#### Implementation Steps

**Step 1: Install Redis**

```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install redis-server php-redis

# Start Redis
sudo service redis-server start

# Test connection
redis-cli ping
# Should return: PONG
```

**Step 2: Configure Cache Handler**

Edit `app/Config/Cache.php`:

```php
public string \ = 'redis';

public array \ = [
    'host'     => '127.0.0.1',
    'password' => null,
    'port'     => 6379,
    'timeout'  => 0,
    'database' => 0,
];

public string \ = 'simacca_';
```

**Step 3: Configure Session Storage**

Edit `app/Config/Session.php`:

```php
use CodeIgniter\Session\Handlers\RedisHandler;

public string \ = RedisHandler::class;

// Session will now use Redis
```

**Step 4: Test Redis Connection**

Create temporary test file:

```php
<?php
\ = \Config\Services::cache();
\->save('test_key', 'test_value', 60);
\ = \->get('test_key');
echo \; // Should output: test_value
```

**Step 5: Monitor Redis**

```bash
# Monitor Redis commands
redis-cli monitor

# Check memory usage
redis-cli info memory

# Check keys
redis-cli keys "simacca_*"
```

#### Expected Results
- Cache operations: **10x faster**
- Session handling: **5x faster**
- Multi-server compatibility
- Better memory management

---

## ? Testing & Verification

### Performance Testing Checklist

- [ ] **Dashboard Load Time**
  - Measure with browser DevTools (Network tab)
  - Before: ~X ms
  - After: ~Y ms
  - Improvement: Z%

- [ ] **Database Query Count**
  - Enable Query Profiler
  - Count queries per page load
  - Target: 70% reduction

- [ ] **Page Size**
  - Check Network tab total size
  - Before: ~X KB
  - After: ~Y KB
  - Improvement: Z KB saved

- [ ] **Memory Usage**
  - Check PHP memory_get_peak_usage()
  - Should remain stable or decrease

### Testing Tools

**1. Browser DevTools**
```
F12 ? Network tab ? Reload page
- Check total load time
- Check resource sizes
- Check number of requests
```

**2. CodeIgniter Profiler**
```php
// Enable in controller
\->benchmark->start('test');
// ... code ...
\->benchmark->stop('test');
echo \->benchmark->getElapsedTime('test');
```

**3. MySQL Query Profiling**
```sql
SET profiling = 1;
-- Run your query
SELECT * FROM absensi WHERE jadwal_mengajar_id = 1;
SHOW PROFILES;
SHOW PROFILE FOR QUERY 1;
```

**4. Load Testing**
```bash
# Using Apache Bench
ab -n 100 -c 10 http://your-site.com/guru/dashboard

# Before optimizations
# After optimizations
# Compare Requests per second
```

---

## ?? Troubleshooting

### Common Issues

#### Issue: Cache Not Working

**Symptoms:** No performance improvement after enabling cache

**Solutions:**
```bash
# Check cache directory permissions
chmod 755 writable/cache/
chown -R www-data:www-data writable/cache/

# Clear old cache
php spark cache:clear

# Check cache handler
php spark cache:info
```

#### Issue: OPcache Not Loading

**Symptoms:** `opcache_get_status()` returns false

**Solutions:**
```bash
# Check if enabled
php -i | grep opcache.enable

# Check preload errors
tail -f /var/log/php8.2-fpm.log

# Verify preload path is correct
php -r "echo opcache_get_status()['preload_statistics'];"
```

#### Issue: Persistent Connections Exhausted

**Symptoms:** "Too many connections" error

**Solutions:**
```sql
-- Check current connections
SHOW PROCESSLIST;

-- Increase max connections
SET GLOBAL max_connections = 200;

-- Or reduce persistent connections by setting timeout
SET GLOBAL wait_timeout = 600;
```

#### Issue: Redis Connection Failed

**Symptoms:** Cache falls back to file handler

**Solutions:**
```bash
# Check Redis is running
sudo service redis-server status

# Test connection
redis-cli ping

# Check PHP extension
php -m | grep redis

# Check logs
sudo tail -f /var/log/redis/redis-server.log
```

---

## ?? Performance Metrics Tracking

### Baseline Metrics (Before Optimization)

| Metric | Value |
|--------|-------|
| Dashboard Load Time | ~500ms |
| Database Queries/Page | ~15-20 |
| Page Size | ~400KB |
| Memory Usage | ~10MB |
| Cache Hit Rate | 0% |

### Target Metrics (After All Optimizations)

| Metric | Target | Improvement |
|--------|--------|-------------|
| Dashboard Load Time | ~150ms | 70% faster |
| Database Queries/Page | ~3-5 | 75% reduction |
| Page Size | ~150KB | 62% smaller |
| Memory Usage | ~8MB | 20% less |
| Cache Hit Rate | 80%+ | 8 |

### Monitoring Commands

```bash
# Monitor cache effectiveness
php spark cache:info

# Monitor database performance
mysql -u root -p -e "SHOW STATUS LIKE 'Threads%';"

# Monitor PHP-FPM status
curl http://localhost/status?full

# Monitor Redis stats
redis-cli info stats
```

---

## ?? Summary & Next Steps

### What We've Accomplished

? **Quick Wins (Week 1)**
- Query result caching ? 40-60% faster dashboard
- Persistent DB connections ? 10-20% faster queries
- Optimized CDN loading ? 200-300KB saved

? **Medium Impact (Week 2)**
- OPcache with preloading ? 30-50% faster PHP
- Database query caching ? 50-70% faster forms
- Image optimization ? 40-60% smaller files

? **Long-term (Week 3+)**
- Database indexing ? 20-40% faster complex queries
- Page caching ? 80-90% faster static pages
- Redis upgrade ? 10x faster cache, 5x faster sessions

### Total Expected Improvement

?? **Overall Performance: 70-100% faster**

### Maintenance Tasks

**Daily:**
- Monitor cache hit rates
- Check error logs

**Weekly:**
- Review slow query log
- Clear old cache if needed
- Check disk space (cache/uploads)

**Monthly:**
- Analyze performance metrics
- Review and adjust cache TTLs
- Update OPcache settings if needed

### Additional Resources

- [CodeIgniter Caching Documentation](https://codeigniter.com/user_guide/libraries/caching.html)
- [PHP OPcache Best Practices](https://www.php.net/manual/en/opcache.configuration.php)
- [MySQL Index Optimization](https://dev.mysql.com/doc/refman/8.0/en/optimization-indexes.html)
- [Redis Best Practices](https://redis.io/topics/optimization)

---

## ?? Support

If you encounter issues during optimization:

1. Check the **Troubleshooting** section above
2. Review application logs: `writable/logs/`
3. Test on staging environment first
4. Document performance metrics before and after
5. Keep backups before major changes

**Remember:** Always test optimizations in a development/staging environment before deploying to production!

---

**Guide Version:** 1.0  
**Last Updated:** 2026-01-30  
**Author:** Performance Optimization Analysis Team

