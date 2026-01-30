# 🏗️ Service Layer Refactoring Plan - SIMACCA

**Date:** 2026-01-30  
**Priority:** HIGH  
**Estimated Duration:** 2-3 weeks  
**Phase:** Planning Complete → Ready for Implementation

---

## 📋 Executive Summary

### Current Architecture Problems

**1. Fat Controllers (Bloated)**
- `GuruController`: 800+ lines with complex business logic
- `AbsensiController`: 1100+ lines of mixed concerns
- `SiswaController`: Import/export + CRUD + business rules

**2. Code Duplication**
- User account creation logic repeated in 3+ controllers
- Excel import/export patterns duplicated
- Transaction handling copy-pasted everywhere

**3. Poor Testability**
- Business logic tightly coupled to HTTP layer
- Cannot unit test business rules independently
- Mock dependencies difficult

**4. Maintenance Challenges**
- Changes require modifying multiple files
- Business rules scattered across codebase
- Risk of inconsistent behavior

### Solution: Service Layer Pattern

Extract business logic into dedicated service classes that:
- ✅ Handle business rules and workflows
- ✅ Coordinate between multiple models
- ✅ Provide reusable, testable components
- ✅ Maintain single responsibility principle

---

## 🎯 Goals & Benefits

### Primary Goals
1. **Separate Concerns** - HTTP, business logic, data access
2. **Improve Testability** - Unit test business logic independently
3. **Reduce Duplication** - Reusable service methods
4. **Enhance Maintainability** - Centralize business rules

### Expected Benefits
- **Code Reduction:** 30-40% less code in controllers
- **Testability:** 80%+ business logic unit-testable
- **Maintenance:** Single point of change for business rules
- **Reusability:** Services usable from multiple contexts (CLI, API, Web)

---

## 🏛️ Proposed Architecture

### Layer Structure

```
┌─────────────────────────────────────────┐
│         HTTP Layer (Controllers)        │  ← Handles HTTP requests/responses
│  - Validation                           │
│  - Session management                   │
│  - Response formatting                  │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│       Business Layer (Services)         │  ← Business logic & workflows
│  - Business rules                       │
│  - Complex operations                   │
│  - Transaction coordination             │
│  - Cross-model operations               │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│       Data Layer (Models)               │  ← Database queries only
│  - CRUD operations                      │
│  - Query building                       │
│  - Data validation                      │
└─────────────────────────────────────────┘
```

### Folder Structure

```
app/
├── Controllers/           ← Thin controllers (HTTP only)
│   ├── Admin/
│   ├── Guru/
│   └── ...
│
├── Services/             ← NEW: Business logic layer
│   ├── BaseService.php
│   ├── GuruService.php
│   ├── AbsensiService.php
│   ├── SiswaService.php
│   ├── JadwalService.php
│   ├── ImportExportService.php
│   └── NotificationService.php
│
├── Models/               ← Data access only
│   ├── UserModel.php
│   ├── GuruModel.php
│   └── ...
│
└── Libraries/            ← Utilities (optional)
    ├── ExcelHandler.php
    └── ValidationHelper.php
```

---

## 📝 Implementation Plan

### Phase 1: Foundation (Week 1 - Days 1-2)

**Goal:** Create base infrastructure

#### Day 1: Base Service Class

**Create:** `app/Services/BaseService.php`

```php
<?php

namespace App\Services;

use CodeIgniter\Database\BaseBuilder;

abstract class BaseService
{
    protected $db;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    
    /**
     * Execute operation in database transaction
     */
    protected function transaction(callable $callback)
    {
        $this->db->transStart();
        
        try {
            $result = $callback();
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return $result;
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }
    
    /**
     * Log service operation
     */
    protected function log(string $level, string $message, array $context = [])
    {
        log_message($level, '[' . get_class($this) . '] ' . $message, $context);
    }
}
```

#### Day 2: Service Provider (Optional but Recommended)

**Create:** `app/Libraries/ServiceProvider.php`

```php
<?php

namespace App\Libraries;

class ServiceProvider
{
    private static $instances = [];
    
    /**
     * Get service instance (singleton)
     */
    public static function get(string $serviceClass)
    {
        if (!isset(self::$instances[$serviceClass])) {
            self::$instances[$serviceClass] = new $serviceClass();
        }
        
        return self::$instances[$serviceClass];
    }
    
    /**
     * Clear service instance (for testing)
     */
    public static function reset(string $serviceClass = null)
    {
        if ($serviceClass) {
            unset(self::$instances[$serviceClass]);
        } else {
            self::$instances = [];
        }
    }
}
```

---

### Phase 2: High Priority Services (Week 1 - Days 3-5)

#### Service 1: GuruService 🎯 HIGH PRIORITY

**Why First?**
- Most complex business logic (user + guru creation)
- Demonstrates full pattern implementation
- High reusability potential

**Create:** `app/Services/GuruService.php`

**Responsibilities:**
```php
<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\KelasModel;

class GuruService extends BaseService
{
    private $userModel;
    private $guruModel;
    private $kelasModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
    }
    
    /**
     * Create new guru with user account
     * 
     * @param array $data Guru and user data
     * @return int Guru ID
     * @throws \Exception
     */
    public function createGuru(array $data): int
    {
        return $this->transaction(function() use ($data) {
            // 1. Create user account
            $userId = $this->createUserAccount([
                'username' => $data['username'],
                'password' => $data['password'],
                'role' => $data['role'],
                'email' => $data['email'] ?? null
            ]);
            
            // 2. Create guru record
            $guruId = $this->guruModel->insert([
                'user_id' => $userId,
                'nip' => $data['nip'],
                'nama_lengkap' => $data['nama_lengkap'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'mata_pelajaran_id' => $data['mata_pelajaran_id'] ?? null,
                'is_wali_kelas' => $data['is_wali_kelas'] ?? 0,
                'kelas_id' => $data['kelas_id'] ?? null
            ]);
            
            // 3. Assign wali kelas if applicable
            if ($data['is_wali_kelas'] ?? false && $data['kelas_id'] ?? null) {
                $this->assignWaliKelas($guruId, $data['kelas_id']);
            }
            
            $this->log('info', "Guru created successfully", ['guru_id' => $guruId]);
            
            return $guruId;
        });
    }
    
    /**
     * Update existing guru
     */
    public function updateGuru(int $guruId, array $data): bool
    {
        return $this->transaction(function() use ($guruId, $data) {
            $guru = $this->guruModel->find($guruId);
            
            if (!$guru) {
                throw new \Exception('Guru not found');
            }
            
            // Update user account
            if (isset($data['username']) || isset($data['email']) || isset($data['password'])) {
                $this->updateUserAccount($guru['user_id'], [
                    'username' => $data['username'] ?? null,
                    'email' => $data['email'] ?? null,
                    'password' => $data['password'] ?? null,
                    'role' => $data['role'] ?? null
                ]);
            }
            
            // Update guru data
            $this->guruModel->update($guruId, [
                'nip' => $data['nip'],
                'nama_lengkap' => $data['nama_lengkap'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'mata_pelajaran_id' => $data['mata_pelajaran_id'] ?? null,
                'is_wali_kelas' => $data['is_wali_kelas'] ?? 0,
                'kelas_id' => $data['kelas_id'] ?? null
            ]);
            
            // Handle wali kelas changes
            $this->handleWaliKelasChange($guruId, $guru, $data);
            
            return true;
        });
    }
    
    /**
     * Delete guru (soft delete user, cascade to guru)
     */
    public function deleteGuru(int $guruId): bool
    {
        return $this->transaction(function() use ($guruId) {
            $guru = $this->guruModel->find($guruId);
            
            if (!$guru) {
                throw new \Exception('Guru not found');
            }
            
            // Remove wali kelas assignment
            if ($guru['is_wali_kelas'] && $guru['kelas_id']) {
                $this->removeWaliKelas($guruId, $guru['kelas_id']);
            }
            
            // Delete guru record
            $this->guruModel->delete($guruId);
            
            // Delete user account
            $this->userModel->delete($guru['user_id']);
            
            return true;
        });
    }
    
    /**
     * Private helper methods
     */
    private function createUserAccount(array $userData): int
    {
        return $this->userModel->insert($userData);
    }
    
    private function updateUserAccount(int $userId, array $userData)
    {
        $updateData = array_filter($userData, fn($v) => $v !== null);
        
        if (!empty($updateData)) {
            $this->userModel->update($userId, $updateData);
        }
    }
    
    private function assignWaliKelas(int $guruId, int $kelasId)
    {
        $this->kelasModel->update($kelasId, ['wali_kelas_id' => $guruId]);
    }
    
    private function removeWaliKelas(int $guruId, int $kelasId)
    {
        $this->kelasModel->update($kelasId, ['wali_kelas_id' => null]);
    }
    
    private function handleWaliKelasChange(int $guruId, array $oldData, array $newData)
    {
        $wasWaliKelas = $oldData['is_wali_kelas'];
        $isWaliKelas = $newData['is_wali_kelas'] ?? false;
        
        // Remove old assignment
        if ($wasWaliKelas && $oldData['kelas_id']) {
            $this->removeWaliKelas($guruId, $oldData['kelas_id']);
        }
        
        // Add new assignment
        if ($isWaliKelas && ($newData['kelas_id'] ?? null)) {
            $this->assignWaliKelas($guruId, $newData['kelas_id']);
        }
    }
}
```

**Controller Before (800 lines) → After (150 lines):**

```php
// AFTER: Slim controller
class GuruController extends BaseController
{
    private $guruService;
    
    public function __construct()
    {
        $this->guruService = new GuruService();
    }
    
    public function store()
    {
        // 1. Validate input
        if (!$this->validate($this->validationRules())) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        try {
            // 2. Call service (business logic)
            $guruId = $this->guruService->createGuru(
                $this->request->getPost()
            );
            
            // 3. Return response
            return redirect()->to('/admin/guru')
                ->with('success', 'Guru berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
```

**Benefits:**
- ✅ Controller reduced from 800 → 150 lines
- ✅ Business logic testable independently
- ✅ Reusable from API, CLI, or other contexts

---

#### Service 2: ImportExportService 🎯 HIGH PRIORITY

**Why Important?**
- Excel logic duplicated in 3+ controllers
- High reusability
- Complex operations

**Create:** `app/Services/ImportExportService.php`

```php
<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportExportService extends BaseService
{
    /**
     * Export data to Excel
     * 
     * @param array $data Data to export
     * @param array $headers Column headers
     * @param string $sheetTitle Sheet title
     * @param array $formatters Optional column formatters
     * @return Spreadsheet
     */
    public function createExcelExport(
        array $data, 
        array $headers, 
        string $sheetTitle = 'Export',
        array $formatters = []
    ): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($sheetTitle);
        
        // Set headers
        $this->setHeaders($sheet, $headers);
        
        // Fill data
        $this->fillData($sheet, $data, $formatters);
        
        // Apply styling
        $this->applyDefaultStyling($sheet, count($headers));
        
        return $spreadsheet;
    }
    
    /**
     * Parse Excel file to array
     * 
     * @param string $filePath Path to Excel file
     * @param bool $skipHeader Skip first row
     * @return array
     */
    public function parseExcelImport(
        string $filePath, 
        bool $skipHeader = true
    ): array
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        
        if ($skipHeader) {
            array_shift($rows);
        }
        
        // Remove empty rows
        return array_filter($rows, fn($row) => !empty(array_filter($row)));
    }
    
    /**
     * Download Excel file
     */
    public function downloadExcel(
        Spreadsheet $spreadsheet, 
        string $filename
    ): void
    {
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }
    
    /**
     * Create import template
     */
    public function createImportTemplate(
        array $headers,
        array $exampleData = [],
        string $sheetTitle = 'Import Template'
    ): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($sheetTitle);
        
        // Set headers with instructions
        $this->setHeaders($sheet, $headers);
        
        // Add example data
        if (!empty($exampleData)) {
            $sheet->fromArray($exampleData, null, 'A2');
        }
        
        // Apply template styling
        $this->applyTemplateStyling($sheet, count($headers));
        
        return $spreadsheet;
    }
    
    // Private helper methods...
    private function setHeaders($sheet, array $headers)
    {
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }
    }
    
    private function fillData($sheet, array $data, array $formatters)
    {
        $row = 2;
        foreach ($data as $item) {
            $column = 'A';
            foreach ($item as $key => $value) {
                $formatted = isset($formatters[$key]) 
                    ? $formatters[$key]($value) 
                    : $value;
                $sheet->setCellValue($column . $row, $formatted);
                $column++;
            }
            $row++;
        }
    }
    
    private function applyDefaultStyling($sheet, int $columnCount)
    {
        // Header styling
        $headerRange = 'A1:' . chr(64 + $columnCount) . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE0E0E0']
            ]
        ]);
        
        // Auto size columns
        foreach (range('A', chr(64 + $columnCount)) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    
    private function applyTemplateStyling($sheet, int $columnCount)
    {
        $this->applyDefaultStyling($sheet, $columnCount);
        
        // Freeze header row
        $sheet->freezePane('A2');
    }
}
```

**Usage in Controller:**

```php
// AFTER: Using ImportExportService
public function export()
{
    $guru = $this->guruModel->getAllGuru();
    
    $importExportService = new ImportExportService();
    
    $spreadsheet = $importExportService->createExcelExport(
        data: $guru,
        headers: ['NO', 'NIP', 'NAMA', 'JENIS KELAMIN', 'MAPEL', 'ROLE', 'STATUS'],
        sheetTitle: 'Data Guru',
        formatters: [
            'jenis_kelamin' => fn($v) => $v == 'L' ? 'Laki-laki' : 'Perempuan',
            'is_active' => fn($v) => $v ? 'Aktif' : 'Nonaktif'
        ]
    );
    
    $importExportService->downloadExcel(
        $spreadsheet, 
        'data-guru-' . date('Y-m-d') . '.xlsx'
    );
}
```

**Benefits:**
- ✅ Eliminates 200+ lines of duplicated code
- ✅ Reusable across all export features
- ✅ Consistent Excel formatting
- ✅ Easy to extend with new features

---

#### Service 3: AbsensiService 🎯 HIGH PRIORITY

**Why Critical?**
- Most complex controller (1100+ lines)
- Core business functionality
- Multiple model coordination

**Create:** `app/Services/AbsensiService.php`

```php
<?php

namespace App\Services;

use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\JadwalMengajarModel;
use App\Models\SiswaModel;

class AbsensiService extends BaseService
{
    private $absensiModel;
    private $detailModel;
    private $jadwalModel;
    private $siswaModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->absensiModel = new AbsensiModel();
        $this->detailModel = new AbsensiDetailModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->siswaModel = new SiswaModel();
    }
    
    /**
     * Create absensi with details
     * 
     * @param array $absensiData Absensi header data
     * @param array $siswaData Student attendance data
     * @return int Absensi ID
     */
    public function createAbsensi(array $absensiData, array $siswaData): int
    {
        return $this->transaction(function() use ($absensiData, $siswaData) {
            // Validate jadwal exists
            $jadwal = $this->jadwalModel->find($absensiData['jadwal_mengajar_id']);
            if (!$jadwal) {
                throw new \Exception('Jadwal tidak ditemukan');
            }
            
            // Check for duplicates
            if ($this->isDuplicateAbsensi($absensiData)) {
                throw new \Exception('Absensi sudah ada untuk jadwal dan tanggal ini');
            }
            
            // Create absensi header
            $absensiId = $this->absensiModel->insert($absensiData);
            
            // Create absensi details
            $this->createAbsensiDetails($absensiId, $siswaData);
            
            $this->log('info', "Absensi created", [
                'absensi_id' => $absensiId,
                'total_siswa' => count($siswaData)
            ]);
            
            return $absensiId;
        });
    }
    
    /**
     * Update absensi with details
     */
    public function updateAbsensi(
        int $absensiId, 
        array $absensiData, 
        array $siswaData
    ): bool
    {
        return $this->transaction(function() use ($absensiId, $absensiData, $siswaData) {
            // Update header
            $this->absensiModel->update($absensiId, $absensiData);
            
            // Update details
            $this->updateAbsensiDetails($absensiId, $siswaData);
            
            return true;
        });
    }
    
    /**
     * Get absensi summary statistics
     */
    public function getAbsensiSummary(int $absensiId): array
    {
        $details = $this->detailModel
            ->where('absensi_id', $absensiId)
            ->findAll();
        
        $summary = [
            'total' => count($details),
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0
        ];
        
        foreach ($details as $detail) {
            $summary[strtolower($detail['status'])]++;
        }
        
        return $summary;
    }
    
    // Private helpers...
    private function isDuplicateAbsensi(array $data): bool
    {
        return $this->absensiModel
            ->where('jadwal_mengajar_id', $data['jadwal_mengajar_id'])
            ->where('tanggal', $data['tanggal'])
            ->countAllResults() > 0;
    }
    
    private function createAbsensiDetails(int $absensiId, array $siswaData)
    {
        foreach ($siswaData as $siswaId => $status) {
            $this->detailModel->insert([
                'absensi_id' => $absensiId,
                'siswa_id' => $siswaId,
                'status' => $status,
                'keterangan' => null
            ]);
        }
    }
    
    private function updateAbsensiDetails(int $absensiId, array $siswaData)
    {
        // Delete existing details
        $this->detailModel->where('absensi_id', $absensiId)->delete();
        
        // Recreate details
        $this->createAbsensiDetails($absensiId, $siswaData);
    }
}
```

---

### Phase 3: Medium Priority Services (Week 2)

#### Service 4: SiswaService
- Student management operations
- Bulk operations
- Kelas assignments

#### Service 5: JadwalService  
- Schedule management
- Conflict detection
- Teacher/class scheduling

#### Service 6: JurnalService
- Journal operations
- Photo uploads
- Report generation

---

### Phase 4: Low Priority Services (Week 3)

#### Service 7: LaporanService
- Report aggregation
- Statistical calculations
- PDF/Excel generation coordination

#### Service 8: NotificationService
- Email notifications
- Event-driven notifications
- Template management

---

## 🧪 Testing Strategy

### Unit Testing Services

**Example: GuruService Test**

```php
<?php

namespace Tests\Services;

use App\Services\GuruService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class GuruServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    
    protected $guruService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->guruService = new GuruService();
    }
    
    public function testCreateGuruSuccessfully()
    {
        $data = [
            'username' => 'test.guru',
            'password' => 'password123',
            'role' => 'guru_mapel',
            'email' => 'test@example.com',
            'nip' => '1234567890',
            'nama_lengkap' => 'Test Guru',
            'jenis_kelamin' => 'L'
        ];
        
        $guruId = $this->guruService->createGuru($data);
        
        $this->assertIsInt($guruId);
        $this->assertGreaterThan(0, $guruId);
        
        // Verify guru was created
        $guruModel = new \App\Models\GuruModel();
        $guru = $guruModel->find($guruId);
        
        $this->assertEquals($data['nip'], $guru['nip']);
        $this->assertEquals($data['nama_lengkap'], $guru['nama_lengkap']);
    }
    
    public function testCreateGuruWithDuplicateNipThrowsException()
    {
        $this->expectException(\Exception::class);
        
        // Create first guru
        $data = [...]; // guru data
        $this->guruService->createGuru($data);
        
        // Try to create duplicate
        $this->guruService->createGuru($data);
    }
}
```

---

## 📦 Migration Strategy

### Approach: Incremental Refactoring

**Option 1: Gradual Migration (RECOMMENDED)**
1. Create service classes
2. Keep old controller methods
3. Add new service-based methods
4. Gradually replace old with new
5. Remove old methods when all references updated

**Option 2: Big Bang (NOT RECOMMENDED)**
- Refactor all at once
- High risk
- Long testing period

### Migration Steps

**Step 1: Create Service (Week 1)**
```php
// New service
class GuruService { ... }
```

**Step 2: Add Service-Based Method (Week 1)**
```php
// Controller
public function storeNew() // New method
{
    $guruId = $this->guruService->createGuru(...);
    // ...
}

public function store() // Keep old method
{
    // Old implementation
}
```

**Step 3: Test in Parallel (Week 1-2)**
- Test new implementation
- Keep old as fallback

**Step 4: Switch Routes (Week 2)**
```php
// Routes.php
$routes->post('guru/store', 'GuruController::storeNew'); // New
```

**Step 5: Remove Old Code (Week 3)**
```php
// Delete old store() method
// Rename storeNew() to store()
```

---

## ✅ Success Criteria

### Metrics

**Code Quality:**
- [ ] Controllers reduced by 30-40%
- [ ] Code duplication reduced by 50%+
- [ ] Business logic in services (not controllers)

**Testing:**
- [ ] 80%+ service code coverage
- [ ] All business rules unit tested
- [ ] Integration tests passing

**Maintainability:**
- [ ] Single responsibility per class
- [ ] Clear separation of concerns
- [ ] Easy to add new features

**Performance:**
- [ ] No performance degradation
- [ ] Transaction handling optimized
- [ ] Database queries efficient

---

## 🚧 Risks & Mitigation

### Risk 1: Breaking Existing Functionality
**Mitigation:**
- Incremental migration
- Comprehensive testing
- Keep old code as fallback
- Gradual rollout

### Risk 2: Learning Curve
**Mitigation:**
- Document pattern clearly
- Code examples provided
- Pair programming sessions
- Code reviews

### Risk 3: Over-Engineering
**Mitigation:**
- Start with high-value services
- Don't refactor what works
- Pragmatic approach
- Measure benefits

---

## 📚 Resources & References

### CodeIgniter 4 Best Practices
- [Service Classes](https://codeigniter4.github.io/userguide/concepts/structure.html)
- [Testing](https://codeigniter4.github.io/userguide/testing/index.html)

### Design Patterns
- Service Layer Pattern
- Repository Pattern (optional future enhancement)
- Dependency Injection

### Similar Projects
- Laravel Service Pattern
- Symfony Service Container
- Clean Architecture principles

---

## 📞 Support & Questions

**Questions?**
- Review this plan
- Check code examples
- Consult with team lead

**Remember:**
- Start small (GuruService first)
- Test thoroughly
- Migrate incrementally
- Measure improvements

---

**Document Version:** 1.0  
**Status:** Ready for Implementation  
**Next Step:** Create BaseService and GuruService (Week 1, Days 1-2)