# ðŸ—ï¸ REFACTORING PLAN - Phase 1: Foundation
**SIMACCA - Code Quality & Architecture Improvement**

---

## ðŸ“‹ Phase 1 Overview

**Duration:** 3 weeks (15 working days)  
**Goal:** Establish architectural foundation with Service Layer & Repository Pattern  
**Team Size:** 2-3 developers  
**Risk Level:** MEDIUM  

**Success Criteria:**
- âœ… 3 core services implemented (Guru, Siswa, Absensi)
- âœ… 4 repositories with interfaces
- âœ… Top 5 long methods refactored
- âœ… Controllers reduced by 30% (258 â†’ 180 lines avg)
- âœ… All changes tested and documented

---

## ðŸŽ¯ Sprint Breakdown

### **Week 1: Service Layer Foundation**
**Focus:** Create core service classes and infrastructure

**Days 1-2:** Setup & Core Services Infrastructure  
**Days 3-5:** Implement GuruService (Pilot)

### **Week 2: Service Layer Expansion**
**Focus:** Implement remaining core services

**Days 6-7:** Implement SiswaService  
**Days 8-10:** Implement AbsensiService

### **Week 3: Repository Pattern & Refactoring**
**Focus:** Add data abstraction layer & refactor long methods

**Days 11-12:** Implement Repository Pattern  
**Days 13-14:** Refactor Top 5 Long Methods  
**Day 15:** Testing, Documentation & Review

---

## ðŸ“¦ TICKET DETAILS

This section provides complete specifications for all 14 tickets in Phase 1.

---


### Ticket #1: Create Service Base Structure
**Type:** Task  
**Priority:** Critical  
**Estimate:** 4 hours  
**Dependencies:** None  

**Description:**
Create the foundation for service layer pattern with base class and configuration.

**Acceptance Criteria:**
- [ ] Create `app/Services/` directory
- [ ] Create `BaseService.php` with common methods
- [ ] Add service auto-loading to `Config/Autoload.php`
- [ ] Create `Config/Services.php` service container entries
- [ ] Documentation in `docs/architecture/SERVICE_LAYER.md`

**Implementation Details:**
```php
// app/Services/BaseService.php
namespace App\Services;

abstract class BaseService {
    protected function validateData(array $data, array $rules): array {
        $validation = \Config\Services::validation();
        
        if (!$validation->setRules($rules)->run($data)) {
            throw new \InvalidArgumentException(
                json_encode($validation->getErrors())
            );
        }
        
        return $data;
    }
    
    protected function logActivity(string $action, array $context): void {
        log_message('info', "Service Activity: {$action}", $context);
    }
    
    protected function handleException(\Exception $e): void {
        log_message('error', $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        throw $e;
    }
}
```

**Files to Create:**
- `app/Services/BaseService.php`
- `docs/architecture/SERVICE_LAYER.md`

**Files to Modify:**
- `app/Config/Autoload.php` (add Services namespace)
- `app/Config/Services.php` (add service definitions)

---

### Ticket #2: Create GuruService (Pilot Implementation)
**Type:** Feature  
**Priority:** Critical  
**Estimate:** 12 hours  
**Dependencies:** Ticket #1, #3  

**Description:**
Implement GuruService as pilot to establish pattern for other services. Extract business logic from GuruController.

**Acceptance Criteria:**
- [ ] Create `GuruService` class with all business logic
- [ ] Extract methods: `create()`, `update()`, `delete()`, `import()`
- [ ] Handle password generation
- [ ] Handle email sending
- [ ] Handle Excel import validation
- [ ] Refactor GuruController to use service
- [ ] Unit tests for GuruService (60% coverage)
- [ ] Integration tests for controller

**Implementation Details:**

```php
// app/Services/GuruService.php
namespace App\Services;

use App\Models\GuruModel;
use App\Models\UserModel;

class GuruService extends BaseService {
    protected $guruModel;
    protected $userModel;
    protected $emailService;
    
    public function __construct(
        GuruModel $guruModel,
        UserModel $userModel,
        EmailService $emailService
    ) {
        $this->guruModel = $guruModel;
        $this->userModel = $userModel;
        $this->emailService = $emailService;
    }
    
    /**
     * Create new guru with user account
     */
    public function create(array $data): array {
        try {
            // Validate
            $validated = $this->validateData($data, [
                'nip' => 'required|is_unique[guru.nip]',
                'nama_lengkap' => 'required',
                'email' => 'required|valid_email|is_unique[users.email]'
            ]);
            
            // Generate password
            $password = $this->generatePassword();
            
            // Create user account
            $userId = $this->userModel->insert([
                'username' => $validated['nip'],
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $validated['role'] ?? 'guru_mapel',
                'email' => $validated['email']
            ]);
            
            if (!$userId) {
                throw new \Exception('Failed to create user account');
            }
            
            // Create guru record
            $guruId = $this->guruModel->insert([
                'user_id' => $userId,
                'nip' => $validated['nip'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'] ?? null,
                'mata_pelajaran_id' => $validated['mata_pelajaran_id'] ?? null
            ]);
            
            if (!$guruId) {
                // Rollback user
                $this->userModel->delete($userId);
                throw new \Exception('Failed to create guru record');
            }
            
            // Send welcome email
            $this->emailService->sendWelcomeEmail(
                $validated['email'],
                $validated['nama_lengkap'],
                $password
            );
            
            $this->logActivity('guru_created', [
                'guru_id' => $guruId,
                'user_id' => $userId,
                'nip' => $validated['nip']
            ]);
            
            return [
                'guru_id' => $guruId,
                'user_id' => $userId,
                'password' => $password
            ];
            
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
    
    /**
     * Update guru data
     */
    public function update(int $guruId, array $data): bool {
        try {
            $guru = $this->guruModel->find($guruId);
            
            if (!$guru) {
                throw new \Exception('Guru tidak ditemukan');
            }
            
            // Validate
            $validated = $this->validateData($data, [
                'nama_lengkap' => 'required',
                'email' => "required|valid_email|is_unique[users.email,id,{$guru['user_id']}]"
            ]);
            
            // Update guru record
            $updated = $this->guruModel->update($guruId, [
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'] ?? $guru['no_hp'],
                'mata_pelajaran_id' => $validated['mata_pelajaran_id'] ?? $guru['mata_pelajaran_id']
            ]);
            
            if (!$updated) {
                throw new \Exception('Failed to update guru');
            }
            
            // Update user record if email changed
            if ($validated['email'] !== $guru['email']) {
                $this->userModel->update($guru['user_id'], [
                    'email' => $validated['email']
                ]);
                
                // Send notification
                $this->emailService->sendEmailChangedNotification(
                    $validated['email'],
                    $validated['nama_lengkap']
                );
            }
            
            // Update password if provided
            if (!empty($validated['password'])) {
                $this->userModel->update($guru['user_id'], [
                    'password' => password_hash($validated['password'], PASSWORD_DEFAULT)
                ]);
                
                $this->emailService->sendPasswordChanged(
                    $validated['email'],
                    $validated['nama_lengkap'],
                    $validated['password']
                );
            }
            
            $this->logActivity('guru_updated', ['guru_id' => $guruId]);
            
            return true;
            
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
    
    /**
     * Delete guru
     */
    public function delete(int $guruId): bool {
        try {
            $guru = $this->guruModel->find($guruId);
            
            if (!$guru) {
                throw new \Exception('Guru tidak ditemukan');
            }
            
            // Check if guru has dependencies
            // (jadwal, absensi, jurnal)
            
            // Delete guru
            $deleted = $this->guruModel->delete($guruId);
            
            if (!$deleted) {
                throw new \Exception('Failed to delete guru');
            }
            
            // Delete user account
            $this->userModel->delete($guru['user_id']);
            
            $this->logActivity('guru_deleted', ['guru_id' => $guruId]);
            
            return true;
            
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
    
    /**
     * Import guru from Excel
     */
    public function importFromExcel(string $filePath): array {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            $results = [
                'success' => 0,
                'failed' => 0,
                'errors' => []
            ];
            
            // Skip header row
            array_shift($rows);
            
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                
                try {
                    // Parse row data
                    $data = [
                        'nip' => trim($row[0]),
                        'nama_lengkap' => trim($row[1]),
                        'email' => trim($row[2]),
                        'no_hp' => trim($row[3] ?? ''),
                        'role' => trim($row[4] ?? 'guru_mapel')
                    ];
                    
                    // Skip empty rows
                    if (empty($data['nip']) && empty($data['nama_lengkap'])) {
                        continue;
                    }
                    
                    // Create guru
                    $this->create($data);
                    $results['success']++;
                    
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'row' => $rowNumber,
                        'data' => $row,
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            $this->logActivity('guru_import_completed', $results);
            
            return $results;
            
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
    
    // Private helper methods
    private function generatePassword(): string {
        return bin2hex(random_bytes(4));
    }
}
```

**Refactored Controller:**
```php
// app/Controllers/Admin/GuruController.php (AFTER refactoring)
namespace App\Controllers\Admin;

use App\Services\GuruService;

class GuruController extends BaseController {
    protected $guruService;
    
    public function __construct() {
        $this->guruService = service('guruService');
    }
    
    // BEFORE: 134 lines
    // AFTER: ~20 lines
    public function store() {
        try {
            $result = $this->guruService->create($this->request->getPost());
            
            return $this->successRedirect(
                'Guru berhasil ditambahkan',
                '/admin/guru'
            );
        } catch (\Exception $e) {
            return $this->errorRedirect($e->getMessage());
        }
    }
    
    // BEFORE: 136 lines
    // AFTER: ~25 lines
    public function update($id) {
        try {
            $this->guruService->update($id, $this->request->getPost());
            
            return $this->successRedirect(
                'Guru berhasil diperbarui',
                '/admin/guru'
            );
        } catch (\Exception $e) {
            return $this->errorRedirect($e->getMessage());
        }
    }
}
```

**Files to Create:**
- `app/Services/GuruService.php`
- `tests/unit/Services/GuruServiceTest.php`

**Files to Modify:**
- `app/Controllers/Admin/GuruController.php`
- `app/Config/Services.php`

**Testing Checklist:**
- [ ] Test create guru with valid data
- [ ] Test create guru with duplicate NIP
- [ ] Test password generation
- [ ] Test email sending
- [ ] Test update guru data
- [ ] Test import Excel (valid file)
- [ ] Test import Excel (invalid data)

---


### Ticket #3: Create EmailService
**Type:** Feature  
**Priority:** High  
**Estimate:** 6 hours  
**Dependencies:** Ticket #1  

**Description:**
Extract email sending logic into dedicated EmailService. Currently scattered across controllers with inline helper calls.

**Acceptance Criteria:**
- [ ] Create `EmailService` class
- [ ] Extract methods: `sendWelcome()`, `sendPasswordReset()`, `sendNotification()`
- [ ] Consolidate email templates
- [ ] Add email queueing (optional)
- [ ] Error handling and logging
- [ ] Unit tests (70% coverage)

**Implementation:**
```php
// app/Services/EmailService.php
namespace App\Services;

class EmailService extends BaseService {
    public function sendWelcomeEmail(
        string $email, 
        string $name, 
        string $password
    ): bool {
        helper('email');
        
        try {
            $result = send_welcome_email(
                $email, 
                $name, 
                $password, 
                session()->get('role')
            );
            
            $this->logActivity('email_sent', [
                'type' => 'welcome',
                'recipient' => $email
            ]);
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Failed to send welcome email: ' . $e->getMessage());
            return false;
        }
    }
    
    public function sendPasswordChanged(
        string $email,
        string $name,
        string $newPassword
    ): bool {
        helper('email');
        
        try {
            $result = send_password_changed_email($email, $name, $newPassword);
            
            $this->logActivity('email_sent', [
                'type' => 'password_changed',
                'recipient' => $email
            ]);
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Failed to send password changed email: ' . $e->getMessage());
            return false;
        }
    }
    
    public function sendPasswordResetLink(
        string $email,
        string $token
    ): bool {
        helper('email');
        
        try {
            $resetUrl = base_url('auth/reset-password/' . $token);
            $result = send_password_reset_email($email, $resetUrl);
            
            $this->logActivity('email_sent', [
                'type' => 'password_reset',
                'recipient' => $email
            ]);
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Failed to send password reset email: ' . $e->getMessage());
            return false;
        }
    }
}
```

**Files to Create:**
- `app/Services/EmailService.php`
- `tests/unit/Services/EmailServiceTest.php`

---

### Ticket #4: Create SiswaService
**Type:** Feature  
**Priority:** High  
**Estimate:** 10 hours  
**Dependencies:** Ticket #1, #3  

**Description:**
Extract Siswa business logic from SiswaController. Similar pattern to GuruService.

**Acceptance Criteria:**
- [ ] Create `SiswaService` class
- [ ] Extract methods: `create()`, `update()`, `delete()`, `import()`
- [ ] Handle kelas auto-creation during import
- [ ] Handle user account creation
- [ ] Integrate with EmailService
- [ ] Refactor SiswaController
- [ ] Unit tests (60% coverage)

**Key Methods:**
```php
public function create(array $data): array
public function update(int $siswaId, array $data): bool
public function delete(int $siswaId): bool
public function importFromExcel(string $filePath): array
public function moveToClass(int $siswaId, int $newKelasId): bool
```

**Special Logic:**
- Auto-create kelas if not exists during import (with validation)
- Smart kelas name parsing (X-RPL, XI-TKJ, XII-MM)
- Batch processing for large imports (100+ records)

**Files to Create:**
- `app/Services/SiswaService.php`
- `tests/unit/Services/SiswaServiceTest.php`

**Files to Modify:**
- `app/Controllers/Admin/SiswaController.php`

---

### Ticket #5: Create AbsensiService
**Type:** Feature  
**Priority:** High  
**Estimate:** 14 hours  
**Dependencies:** Ticket #1  

**Description:**
Extract Absensi business logic - most complex service due to multiple related entities (Absensi, AbsensiDetail, JurnalKBM).

**Acceptance Criteria:**
- [ ] Create `AbsensiService` class
- [ ] Extract methods: `create()`, `update()`, `delete()`, `checkEditable()``
- [ ] Handle absensi detail bulk operations
- [ ] Handle guru pengganti logic
- [ ] Integrate with izin siswa
- [ ] Refactor AbsensiController
- [ ] Unit tests (60% coverage)

**Key Methods:**
```php
public function create(array $absensiData, array $siswaDetails): array
public function update(int $absensiId, array $data): bool
public function delete(int $absensiId): bool
public function isEditable(array $absensi): bool
public function getByGuru(int $guruId, array $filters = []): array
public function recordSubstituteTeacher(int $absensiId, int $guruPenggantiId): bool
```

**Complex Logic:**
- Dual ownership (creator OR schedule owner can edit)
- 24-hour edit window (or unlocked by admin)
- Bulk update absensi details (update existing, insert new)
- Automatic izin siswa detection and application
- Guru pengganti mode logic

**Files to Create:**
- `app/Services/AbsensiService.php`
- `tests/unit/Services/AbsensiServiceTest.php`

**Files to Modify:**
- `app/Controllers/Guru/AbsensiController.php`
- `app/Controllers/Admin/AbsensiController.php`

---

### Ticket #6: Create Repository Interfaces
**Type:** Task  
**Priority:** High  
**Estimate:** 4 hours  
**Dependencies:** None  

**Description:**
Define repository interfaces to establish contracts for data access layer.

**Acceptance Criteria:**
- [ ] Create `app/Repositories/Contracts/` directory
- [ ] Create `GuruRepositoryInterface`
- [ ] Create `SiswaRepositoryInterface`
- [ ] Create `AbsensiRepositoryInterface`
- [ ] Create `BaseRepositoryInterface`
- [ ] Documentation

**Implementation:**
```php
// app/Repositories/Contracts/GuruRepositoryInterface.php
namespace App\Repositories\Contracts;

interface GuruRepositoryInterface {
    public function find(int $id): ?array;
    public function findAll(): array;
    public function findActive(): array;
    public function findByNip(string $nip): ?array;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getWithUser(int $id): ?array;
}

// app/Repositories/Contracts/BaseRepositoryInterface.php
namespace App\Repositories\Contracts;

interface BaseRepositoryInterface {
    public function find(int $id): ?array;
    public function findAll(): array;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
```

**Files to Create:**
- `app/Repositories/Contracts/BaseRepositoryInterface.php`
- `app/Repositories/Contracts/GuruRepositoryInterface.php`
- `app/Repositories/Contracts/SiswaRepositoryInterface.php`
- `app/Repositories/Contracts/AbsensiRepositoryInterface.php`

---

### Ticket #7: Implement GuruRepository
**Type:** Feature  
**Priority:** High  
**Estimate:** 6 hours  
**Dependencies:** Ticket #6  

**Description:**
Implement concrete GuruRepository with all data access logic.

**Acceptance Criteria:**
- [ ] Create `GuruRepository` implementing interface
- [ ] Move all query logic from GuruModel
- [ ] Add caching layer (optional)
- [ ] Refactor GuruService to use repository
- [ ] Unit tests (70% coverage)

**Implementation:**
```php
// app/Repositories/GuruRepository.php
namespace App\Repositories;

use App\Models\GuruModel;
use App\Models\UserModel;
use App\Repositories\Contracts\GuruRepositoryInterface;

class GuruRepository implements GuruRepositoryInterface {
    protected $guruModel;
    protected $userModel;
    
    public function __construct(
        GuruModel $guruModel,
        UserModel $userModel
    ) {
        $this->guruModel = $guruModel;
        $this->userModel = $userModel;
    }
    
    public function find(int $id): ?array {
        return $this->guruModel->find($id);
    }
    
    public function findActive(): array {
        return $this->guruModel
            ->where('is_active', 1)
            ->findAll();
    }
    
    public function getWithUser(int $id): ?array {
        $guru = $this->find($id);
        
        if (!$guru) {
            return null;
        }
        
        $guru['user'] = $this->userModel->find($guru['user_id']);
        
        return $guru;
    }
    
    public function findByNip(string $nip): ?array {
        return $this->guruModel
            ->where('nip', $nip)
            ->first();
    }
    
    public function create(array $data): int {
        return $this->guruModel->insert($data);
    }
    
    public function update(int $id, array $data): bool {
        return $this->guruModel->update($id, $data);
    }
    
    public function delete(int $id): bool {
        return $this->guruModel->delete($id);
    }
}
```

**Files to Create:**
- `app/Repositories/GuruRepository.php`
- `tests/unit/Repositories/GuruRepositoryTest.php`

**Files to Modify:**
- `app/Services/GuruService.php` (inject repository)
- `app/Config/Services.php` (bind interface to concrete class)

---

### Ticket #8: Implement SiswaRepository
**Type:** Feature  
**Priority:** High  
**Estimate:** 6 hours  
**Dependencies:** Ticket #6  

**Description:**
Implement SiswaRepository with kelas relationship handling.

**Key Methods:**
```php
public function findByKelas(int $kelasId): array
public function findWithKelas(int $siswaId): ?array
public function countByKelas(int $kelasId): int
public function moveToKelas(int $siswaId, int $newKelasId): bool
```

**Files to Create:**
- `app/Repositories/SiswaRepository.php`
- `tests/unit/Repositories/SiswaRepositoryTest.php`

---

### Ticket #9: Implement AbsensiRepository
**Type:** Feature  
**Priority:** High  
**Estimate:** 8 hours  
**Dependencies:** Ticket #6  

**Description:**
Implement AbsensiRepository - most complex due to joins and aggregations.

**Key Methods:**
```php
public function findByGuru(int $guruId, array $filters): array
public function findByKelas(int $kelasId, array $filters): array
public function findWithDetails(int $absensiId): ?array
public function getStatistics(array $filters): array
```

**Files to Create:**
- `app/Repositories/AbsensiRepository.php`
- `tests/unit/Repositories/AbsensiRepositoryTest.php`

---


### Ticket #10: Refactor JadwalController::downloadTemplate()
**Type:** Refactoring  
**Priority:** High  
**Estimate:** 8 hours  
**Dependencies:** None  

**Description:**
Refactor 297-line method into ExcelTemplateGenerator service class.

**Current State:**
- 297 lines in single method
- Excel generation mixed with controller logic
- Hard to test, hard to maintain
- Duplicate code with other template generators

**Target State:**
- Controller method: ~10 lines
- Dedicated `ExcelTemplateGenerator` class
- Reusable across different templates
- Fully tested

**Implementation:**
```php
// BEFORE: app/Controllers/Admin/JadwalController.php
public function downloadTemplate() {
    // 297 lines of Excel generation
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // ... 250+ lines
}

// AFTER: app/Controllers/Admin/JadwalController.php
public function downloadTemplate() {
    $generator = new JadwalTemplateGenerator(
        model(GuruModel::class),
        model(KelasModel::class),
        model(MataPelajaranModel::class)
    );
    
    $filePath = $generator->generate();
    
    return $this->response->download($filePath, null)->setFileName('template-import-jadwal.xlsx');
}

// NEW: app/Services/Excel/JadwalTemplateGenerator.php
namespace App\Services\Excel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class JadwalTemplateGenerator {
    protected $guruModel;
    protected $kelasModel;
    protected $mapelModel;
    
    public function __construct(...) {
        // Inject dependencies
    }
    
    public function generate(): string {
        $spreadsheet = $this->createSpreadsheet();
        $this->addDataSheet($spreadsheet);
        $this->addInstructionSheet($spreadsheet);
        $this->addReferenceSheets($spreadsheet);
        $this->applyFormatting($spreadsheet);
        
        return $this->save($spreadsheet);
    }
    
    private function createSpreadsheet(): Spreadsheet {
        // Create base spreadsheet
    }
    
    private function addDataSheet(Spreadsheet $spreadsheet): void {
        // Add main data entry sheet (20 lines)
    }
    
    private function addInstructionSheet(Spreadsheet $spreadsheet): void {
        // Add instructions sheet (30 lines)
    }
    
    private function addReferenceSheets(Spreadsheet $spreadsheet): void {
        // Add guru, kelas, mapel reference sheets (40 lines)
    }
    
    private function applyFormatting(Spreadsheet $spreadsheet): void {
        // Apply styles, dropdowns, validation (50 lines)
    }
    
    private function save(Spreadsheet $spreadsheet): string {
        // Save to temp file and return path (10 lines)
    }
}
```

**Breakdown:**
- Create `ExcelTemplateGenerator` base class (2 hours)
- Create `JadwalTemplateGenerator` extending base (3 hours)
- Refactor controller to use generator (1 hour)
- Write unit tests (2 hours)

**Files to Create:**
- `app/Services/Excel/ExcelTemplateGenerator.php` (base class)
- `app/Services/Excel/JadwalTemplateGenerator.php`
- `tests/unit/Services/Excel/JadwalTemplateGeneratorTest.php`

**Files to Modify:**
- `app/Controllers/Admin/JadwalController.php`

**Testing:**
- [ ] Generated file has correct structure
- [ ] All reference sheets populated
- [ ] Dropdowns working correctly
- [ ] Data validation rules applied
- [ ] Instructions clear and complete

---

### Ticket #11: Refactor JadwalController::processImport()
**Type:** Refactoring  
**Priority:** High  
**Estimate:** 10 hours  
**Dependencies:** Ticket #10  

**Description:**
Refactor 231-line import method into `JadwalImportService`.

**Current Issues:**
- 231 lines in single method
- Complex validation logic
- Error handling scattered
- Conflict checking inline

**Target:**
- Controller: ~15 lines
- Service handles all business logic
- Clear error reporting
- Reusable conflict detection

**Implementation:**
```php
// AFTER: Controller
public function processImport() {
    $file = $this->request->getFile('file_excel');
    
    if (!$file->isValid()) {
        return $this->errorRedirect('File tidak valid');
    }
    
    try {
        $service = new JadwalImportService(
            model(JadwalMengajarModel::class),
            model(GuruModel::class),
            model(KelasModel::class),
            model(MataPelajaranModel::class)
        );
        
        $result = $service->import($file);
        
        return $this->successRedirect(
            "Import berhasil: {$result['success']} data, {$result['failed']} gagal"
        );
    } catch (\Exception $e) {
        return $this->errorRedirect($e->getMessage());
    }
}

// NEW: app/Services/Import/JadwalImportService.php
class JadwalImportService extends BaseImportService {
    public function import($file): array {
        $rows = $this->parseExcelFile($file);
        $validated = $this->validateRows($rows);
        $conflicts = $this->checkConflicts($validated);
        
        return $this->processRows($validated, $conflicts);
    }
    
    private function validateRows(array $rows): array {
        // Validation logic (50 lines)
    }
    
    private function checkConflicts(array $rows): array {
        // Conflict detection (60 lines)
    }
    
    private function processRows(array $rows, array $conflicts): array {
        // Insert/update logic with transaction (40 lines)
    }
}
```

**Files to Create:**
- `app/Services/Import/BaseImportService.php`
- `app/Services/Import/JadwalImportService.php`
- `tests/unit/Services/Import/JadwalImportServiceTest.php`

**Files to Modify:**
- `app/Controllers/Admin/JadwalController.php`

---

### Ticket #12: Refactor ProfileController::update()
**Type:** Refactoring  
**Priority:** Medium  
**Estimate:** 8 hours  
**Dependencies:** Ticket #3 (EmailService)  

**Description:**
Refactor 215-line update method - handles password change, email change, profile photo.

**Current Issues:**
- Multiple responsibilities in one method
- Email sending inline
- File handling mixed with business logic
- Complex conditional logic

**Implementation:**
```php
// AFTER: Controller
public function update() {
    $data = $this->request->getPost();
    
    try {
        $service = service('profileService');
        
        if (isset($data['current_password'])) {
            $result = $service->changePassword(
                session()->get('user_id'),
                $data
            );
        } elseif (isset($data['email'])) {
            $result = $service->changeEmail(
                session()->get('user_id'),
                $data['email']
            );
        } else {
            $result = $service->updateProfile(
                session()->get('user_id'),
                $data
            );
        }
        
        return $this->successRedirect('Profil berhasil diperbarui');
    } catch (\Exception $e) {
        return $this->errorRedirect($e->getMessage());
    }
}

// NEW: app/Services/ProfileService.php
class ProfileService extends BaseService {
    protected $userModel;
    protected $guruModel;
    protected $siswaModel;
    protected $emailService;
    
    public function changePassword(int $userId, array $data): bool {
        // Validate current password (15 lines)
        // Hash new password (5 lines)
        // Update database (5 lines)
        // Send email notification (5 lines)
        // Log activity (5 lines)
    }
    
    public function changeEmail(int $userId, string $newEmail): bool {
        // Validate email format (10 lines)
        // Check uniqueness (5 lines)
        // Update database (5 lines)
        // Send notification to both emails (10 lines)
        // Log activity (5 lines)
    }
    
    public function updateProfile(int $userId, array $data): bool {
        // Update user table (10 lines)
        // Update guru/siswa table based on role (20 lines)
        // Log activity (5 lines)
    }
    
    public function uploadPhoto(int $userId, $file): string {
        // Validate file (10 lines)
        // Optimize image (5 lines)
        // Save to disk (10 lines)
        // Update database (5 lines)
        // Delete old photo (5 lines)
    }
}
```

**Files to Create:**
- `app/Services/ProfileService.php`
- `tests/unit/Services/ProfileServiceTest.php`

**Files to Modify:**
- `app/Controllers/ProfileController.php`

---

### Ticket #13: Refactor JurnalController::update()
**Type:** Refactoring  
**Priority:** Medium  
**Estimate:** 8 hours  
**Dependencies:** None  

**Description:**
Refactor 206-line update method - handles jurnal data + photo upload.

**Implementation:**
```php
// NEW: app/Services/JurnalService.php
class JurnalService extends BaseService {
    protected $jurnalModel;
    protected $fileService;
    
    public function update(int $jurnalId, array $data, $photo = null): bool {
        $jurnal = $this->jurnalModel->find($jurnalId);
        
        if (!$jurnal) {
            throw new \Exception('Jurnal tidak ditemukan');
        }
        
        // Handle photo upload if provided
        if ($photo && $photo->isValid()) {
            $data['foto_dokumentasi'] = $this->fileService->uploadJurnalPhoto(
                $photo,
                $jurnal['foto_dokumentasi'] // old photo to delete
            );
        }
        
        // Update jurnal record
        $this->jurnalModel->update($jurnalId, $data);
        
        $this->logActivity('jurnal_updated', ['jurnal_id' => $jurnalId]);
        
        return true;
    }
}

// NEW: app/Services/FileService.php
class FileService extends BaseService {
    public function uploadJurnalPhoto($file, string $oldPhoto = null): string {
        // Validate file
        // Optimize image  
        // Save to uploads
        // Delete old photo
        // Return new filename
    }
}
```

**Files to Create:**
- `app/Services/JurnalService.php`
- `app/Services/FileService.php`
- `tests/unit/Services/JurnalServiceTest.php`

**Files to Modify:**
- `app/Controllers/Guru/JurnalController.php`

---

### Ticket #14: Refactor AbsensiController::update()
**Type:** Refactoring  
**Priority:** Medium  
**Estimate:** 10 hours  
**Dependencies:** Ticket #5 (AbsensiService)  

**Description:**
Refactor 172-line update method - bulk update absensi details with complex logic.

**Current Issues:**
- Bulk update logic (update existing, insert new)
- Izin siswa detection and application
- Transaction handling
- Complex validation

**Implementation:**
```php
// Already covered in Ticket #5 (AbsensiService)
// This ticket focuses on testing and edge cases

public function bulkUpdateDetails(int $absensiId, array $details): array {
    $db = \Config\Database::connect();
    $db->transStart();
    
    try {
        $updated = 0;
        $inserted = 0;
        
        foreach ($details as $detail) {
            if (isset($detail['id']) && $detail['id']) {
                // Update existing
                $this->absensiDetailModel->update($detail['id'], $detail);
                $updated++;
            } else {
                // Insert new
                $this->absensiDetailModel->insert([
                    'absensi_id' => $absensiId,
                    'siswa_id' => $detail['siswa_id'],
                    'status' => $detail['status'],
                    'keterangan' => $detail['keterangan'] ?? null
                ]);
                $inserted++;
            }
        }
        
        $db->transComplete();
        
        return [
            'success' => true,
            'updated' => $updated,
            'inserted' => $inserted
        ];
    } catch (\Exception $e) {
        $db->transRollback();
        throw $e;
    }
}
```

**Focus:**
- Extract bulk update to service
- Add comprehensive tests
- Handle edge cases (concurrent updates, partial failures)

**Files to Modify:**
- `app/Services/AbsensiService.php` (add bulkUpdateDetails method)
- `app/Controllers/Guru/AbsensiController.php`

---


## ?? DEPENDENCY GRAPH & TIMELINE

### Visual Dependency Map

```
Critical Path (Must do first):
+-------------------------------------------------------------+
¦ Ticket #1 (Service Base) -----> Ticket #2 (GuruService)    ¦
¦                             ¦                                ¦
¦ Ticket #6 (Repo Interface) ----> Ticket #7 (GuruRepo)      ¦
+-------------------------------------------------------------+

Week 1 Flow:
Day 1-2:  #1 (Service Base) + #6 (Repo Interfaces)
Day 2:    #3 (EmailService)
Day 3-4:  #2 (GuruService) - depends on #1, #3
Day 4-5:  #7 (GuruRepository) - depends on #6
Day 5:    Integration & Testing

Week 2 Flow:
Day 6-7:  #4 (SiswaService) + #8 (SiswaRepo)
Day 8-10: #5 (AbsensiService) + #9 (AbsensiRepo)

Week 3 Flow:
Day 11-12: #10, #11 (Jadwal refactoring)
Day 13-14: #12, #13, #14 (Profile, Jurnal, Absensi refactoring)
Day 15:    Final testing & documentation
```

### Ticket Dependencies Table

| Ticket | Title | Depends On | Can Start After |
|--------|-------|------------|-----------------|
| #1 | Service Base | None | Day 1 |
| #6 | Repo Interfaces | None | Day 1 |
| #3 | EmailService | #1 | Day 2 |
| #2 | GuruService | #1, #3 | Day 3 |
| #7 | GuruRepository | #6 | Day 4 |
| #4 | SiswaService | #1, #3 | Day 6 |
| #8 | SiswaRepository | #6 | Day 6 |
| #5 | AbsensiService | #1 | Day 8 |
| #9 | AbsensiRepository | #6 | Day 8 |
| #10 | JadwalTemplate | None | Day 11 |
| #11 | JadwalImport | #10 | Day 12 |
| #12 | ProfileService | #3 | Day 13 |
| #13 | JurnalService | None | Day 13 |
| #14 | AbsensiUpdate | #5 | Day 14 |

---

## ?? DETAILED TIMELINE

### Week 1: Service Layer Foundation (Days 1-5)

**Day 1 (Monday) - 8 hours**
- [ ] Morning (4h): Ticket #1 - Service Base Structure
  - Create folder structure
  - Implement BaseService class
  - Configure autoloading
  - Write documentation
- [ ] Afternoon (4h): Ticket #6 - Repository Interfaces
  - Create interface folder
  - Define all 4 interfaces
  - Document contracts

**Day 2 (Tuesday) - 8 hours**
- [ ] Morning (4h): Finish Ticket #6
  - Review interfaces
  - Add PHPDoc comments
- [ ] Afternoon (4h): Ticket #3 - EmailService (Start)
  - Create EmailService class
  - Implement basic methods
  - Test email sending

**Day 3 (Wednesday) - 8 hours**
- [ ] Morning (2h): Finish Ticket #3 - EmailService
  - Complete all methods
  - Write unit tests
- [ ] Afternoon (6h): Ticket #2 - GuruService (Start)
  - Create GuruService class
  - Implement create() method
  - Implement update() method

**Day 4 (Thursday) - 8 hours**
- [ ] Morning (4h): Continue Ticket #2 - GuruService
  - Implement delete() method
  - Implement import() method
  - Write unit tests
- [ ] Afternoon (4h): Ticket #7 - GuruRepository
  - Create GuruRepository class
  - Implement all interface methods
  - Write unit tests

**Day 5 (Friday) - 8 hours**
- [ ] Morning (2h): Finish Ticket #2 & #7
  - Integration testing
  - Fix any issues
- [ ] Afternoon (4h): Refactor GuruController
  - Update to use GuruService
  - Remove business logic
  - Update routes
- [ ] Evening (2h): Week 1 Demo & Retrospective
  - Demo GuruService to team
  - Collect feedback
  - Plan Week 2

**Week 1 Deliverables:**
- ? Service base infrastructure
- ? Repository pattern foundation
- ? GuruService fully functional (pilot)
- ? GuruController refactored
- ? EmailService functional

---

### Week 2: Service Layer Expansion (Days 6-10)

**Day 6 (Monday) - 8 hours**
- [ ] Morning (4h): Ticket #4 - SiswaService (Start)
  - Create SiswaService class
  - Implement create() method
- [ ] Afternoon (4h): Ticket #8 - SiswaRepository
  - Create SiswaRepository class
  - Implement interface methods

**Day 7 (Tuesday) - 8 hours**
- [ ] Morning (4h): Continue Ticket #4 - SiswaService
  - Implement update(), delete()
  - Implement import with kelas auto-create
- [ ] Afternoon (4h): Finish Ticket #8 + Integration
  - Complete repository
  - Integration testing
  - Refactor SiswaController

**Day 8 (Wednesday) - 8 hours**
- [ ] All Day (8h): Ticket #5 - AbsensiService (Start)
  - Create AbsensiService class
  - Implement create() method
  - Handle dual ownership logic
  - Handle guru pengganti logic

**Day 9 (Thursday) - 8 hours**
- [ ] Morning (4h): Continue Ticket #5 - AbsensiService
  - Implement update(), delete()
  - Implement isEditable() logic
  - Write unit tests
- [ ] Afternoon (4h): Ticket #9 - AbsensiRepository
  - Create AbsensiRepository
  - Complex queries with joins
  - Implement statistics methods

**Day 10 (Friday) - 8 hours**
- [ ] Morning (2h): Finish Ticket #9
  - Complete all methods
  - Write unit tests
- [ ] Afternoon (4h): Integration & Refactoring
  - Integrate with AbsensiService
  - Refactor AbsensiController
  - Update routes
- [ ] Evening (2h): Week 2 Demo & Retrospective
  - Demo all 3 core services
  - Discuss any issues
  - Plan Week 3

**Week 2 Deliverables:**
- ? SiswaService fully functional
- ? AbsensiService fully functional
- ? All 3 core repositories implemented
- ? Controllers refactored to use services

---

### Week 3: Repository Integration & Long Methods (Days 11-15)

**Day 11 (Monday) - 8 hours**
- [ ] Morning (4h): Ticket #10 - JadwalTemplateGenerator
  - Create base Excel generator
  - Create JadwalTemplateGenerator
  - Refactor controller
- [ ] Afternoon (4h): Ticket #13 - JurnalService
  - Create JurnalService class
  - Create FileService class
  - Refactor JurnalController

**Day 12 (Tuesday) - 8 hours**
- [ ] Morning (4h): Finish Ticket #10
  - Write tests
  - Verify Excel generation
- [ ] Afternoon (4h): Ticket #11 - JadwalImportService
  - Create BaseImportService
  - Create JadwalImportService
  - Implement validation logic

**Day 13 (Wednesday) - 8 hours**
- [ ] Morning (4h): Finish Ticket #11
  - Implement conflict detection
  - Write tests
  - Refactor controller
- [ ] Afternoon (4h): Ticket #12 - ProfileService
  - Create ProfileService class
  - Implement password change
  - Implement email change
  - Implement profile update

**Day 14 (Thursday) - 8 hours**
- [ ] Morning (4h): Finish Ticket #12
  - Complete all methods
  - Write tests
  - Refactor ProfileController
- [ ] Afternoon (4h): Ticket #14 - AbsensiUpdate
  - Add bulk update to AbsensiService
  - Implement transaction handling
  - Write comprehensive tests

**Day 15 (Friday) - 8 hours**
- [ ] Morning (3h): Final Integration Testing
  - Test all services together
  - Test all refactored controllers
  - Performance benchmarking
- [ ] Midday (2h): Documentation Update
  - Update SERVICE_LAYER.md
  - Update REPOSITORY_PATTERN.md
  - Create migration guide
- [ ] Afternoon (2h): Phase 1 Final Review
  - Code review with team
  - Performance metrics comparison
  - Create completion report
- [ ] Evening (1h): Phase 1 Demo to Stakeholders
  - Present achievements
  - Show metrics improvements
  - Discuss Phase 2 timeline

**Week 3 Deliverables:**
- ? All 5 long methods refactored
- ? Complete service layer implemented
- ? Repository pattern across main domains
- ? Documentation updated

---

## ?? PROGRESS TRACKING

### Daily Metrics to Track

**Code Metrics:**
- Lines of code in controllers (target: -30%)
- Number of methods >100 lines (target: 0)
- Service class count (target: 6+)
- Repository class count (target: 4+)

**Quality Metrics:**
- Test coverage (target: 60%)
- Code duplication (target: -50%)
- Cyclomatic complexity (target: Medium)

**Velocity Metrics:**
- Story points completed per day
- Blockers encountered
- Technical debt added/removed

### Weekly Checkpoints

**Week 1 Success Criteria:**
- [ ] GuruService operational
- [ ] GuruRepository implemented
- [ ] GuruController refactored
- [ ] All tests passing
- [ ] No regressions

**Week 2 Success Criteria:**
- [ ] SiswaService operational
- [ ] AbsensiService operational
- [ ] All repositories implemented
- [ ] Controllers refactored
- [ ] Test coverage >50%

**Week 3 Success Criteria:**
- [ ] All long methods refactored
- [ ] Documentation complete
- [ ] Test coverage >60%
- [ ] Performance acceptable
- [ ] Team trained

---

## ?? TESTING STRATEGY

### Unit Testing

**Coverage Target:** 60% for services, 70% for repositories

**Test Structure Example:**
```php
// tests/unit/Services/GuruServiceTest.php
namespace Tests\Unit\Services;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\GuruService;

class GuruServiceTest extends CIUnitTestCase {
    protected $guruService;
    protected $guruModel;
    protected $userModel;
    protected $emailService;
    
    protected function setUp(): void {
        parent::setUp();
        
        // Mock dependencies
        $this->guruModel = $this->createMock(GuruModel::class);
        $this->userModel = $this->createMock(UserModel::class);
        $this->emailService = $this->createMock(EmailService::class);
        
        $this->guruService = new GuruService(
            $this->guruModel,
            $this->userModel,
            $this->emailService
        );
    }
    
    public function testCreateGuruSuccessfully() {
        // Arrange
        $data = [
            'nip' => '123456',
            'nama_lengkap' => 'Test Guru',
            'email' => 'test@test.com'
        ];
        
        $this->userModel->method('insert')->willReturn(1);
        $this->guruModel->method('insert')->willReturn(1);
        $this->emailService->method('sendWelcomeEmail')->willReturn(true);
        
        // Act
        $result = $this->guruService->create($data);
        
        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('guru_id', $result);
        $this->assertArrayHasKey('user_id', $result);
        $this->assertArrayHasKey('password', $result);
    }
    
    public function testCreateGuruWithDuplicateNip() {
        // Arrange
        $data = [
            'nip' => '123456',
            'nama_lengkap' => 'Test',
            'email' => 'test@test.com'
        ];
        
        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->guruService->create($data);
    }
    
    public function testUpdateGuruSuccessfully() {
        // Test implementation
    }
    
    public function testDeleteGuruSuccessfully() {
        // Test implementation
    }
    
    public function testImportFromExcel() {
        // Test implementation
    }
}
```

### Integration Testing

**Coverage Target:** All refactored controllers

**Test Structure Example:**
```php
// tests/integration/Controllers/GuruControllerTest.php
namespace Tests\Integration\Controllers;

use CodeIgniter\Test\FeatureTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class GuruControllerTest extends FeatureTestCase {
    use DatabaseTestTrait;
    
    protected function setUp(): void {
        parent::setUp();
        $this->db = db_connect();
        $this->db->transStart();
    }
    
    protected function tearDown(): void {
        $this->db->transRollback();
        parent::tearDown();
    }
    
    public function testStoreGuruSuccessfully() {
        // Setup auth session
        $this->withSession([
            'role' => 'admin',
            'isLoggedIn' => true,
            'user_id' => 1
        ]);
        
        // Call endpoint
        $result = $this->post('/admin/guru/store', [
            'nip' => '123456',
            'nama_lengkap' => 'Test Guru',
            'email' => 'test@test.com',
            'no_hp' => '08123456789'
        ]);
        
        // Assert
        $result->assertStatus(302);
        $result->assertRedirect('/admin/guru');
        $result->assertSessionHas('success');
        
        // Verify database
        $this->seeInDatabase('guru', ['nip' => '123456']);
        $this->seeInDatabase('users', ['email' => 'test@test.com']);
    }
    
    public function testStoreGuruWithInvalidData() {
        $this->withSession(['role' => 'admin', 'isLoggedIn' => true]);
        
        $result = $this->post('/admin/guru/store', [
            'nip' => '', // Invalid
            'nama_lengkap' => '',
            'email' => 'invalid-email'
        ]);
        
        $result->assertStatus(302);
        $result->assertSessionHas('error');
    }
}
```

---

## ? CODE REVIEW CHECKLIST

### For All Tickets

**Architecture:**
- [ ] Follows service layer pattern correctly
- [ ] Dependencies injected via constructor
- [ ] No business logic in controllers
- [ ] Repository interface used (not model directly)
- [ ] Clear separation of concerns

**Code Quality:**
- [ ] No methods >50 lines
- [ ] Clear method names (self-documenting)
- [ ] Proper error handling with try-catch
- [ ] Type hints on all parameters and returns
- [ ] PSR-12 coding standards followed
- [ ] No code duplication

**Testing:**
- [ ] Unit tests for all public methods
- [ ] Edge cases covered
- [ ] Happy path tested
- [ ] Error scenarios tested
- [ ] Integration tests for controllers
- [ ] Test coverage meets target (60-70%)

**Documentation:**
- [ ] PHPDoc comments on classes
- [ ] PHPDoc comments on public methods
- [ ] Complex logic explained
- [ ] README updated if needed
- [ ] CHANGELOG.md updated

**Performance:**
- [ ] No N+1 query problems
- [ ] Batch operations where possible
- [ ] Caching considered
- [ ] No memory leaks
- [ ] Performance acceptable vs baseline

**Security:**
- [ ] Input validation
- [ ] Output escaping
- [ ] SQL injection prevention
- [ ] File upload validation
- [ ] Authentication checks
- [ ] Authorization checks

---

## ?? KNOWLEDGE TRANSFER

### Training Sessions

**Session 1: Service Layer Pattern (2 hours)**
- What is service layer?
- Benefits and use cases
- Live demo with GuruService
- Hands-on: Create simple service
- Q&A

**Session 2: Repository Pattern (1.5 hours)**
- What is repository pattern?
- Interface-based design
- Mocking for tests
- Live demo with GuruRepository
- Q&A

**Session 3: Hands-on Workshop (3 hours)**
- Pair programming on small feature
- Implementing service from scratch
- Writing tests
- Code review practice
- Best practices discussion

### Resources

**Documentation:**
- `docs/architecture/SERVICE_LAYER.md`
- `docs/architecture/REPOSITORY_PATTERN.md`
- `docs/guides/REFACTORING_GUIDE.md`

**External Resources:**
- [Martin Fowler - Service Layer](https://martinfowler.com/eaaCatalog/serviceLayer.html)
- [Repository Pattern in PHP](https://designpatternsphp.readthedocs.io/)
- [CodeIgniter 4 Services](https://codeigniter.com/user_guide/concepts/services.html)

---


## ?? RISK MITIGATION

### Risk 1: Breaking Changes
**Probability:** MEDIUM  
**Impact:** HIGH  

**Mitigation:**
- Comprehensive test suite before refactoring
- Feature flags for gradual rollout
- Backup/rollback plan
- Staging environment testing
- Incremental deployment

**Action Plan if Occurs:**
1. Rollback to previous version immediately
2. Analyze root cause
3. Fix issues in isolated branch
4. Deploy fix after thorough testing
5. Post-mortem review

---

### Risk 2: Timeline Slippage
**Probability:** MEDIUM  
**Impact:** MEDIUM  

**Mitigation:**
- Buffer time in estimates (20%)
- Daily standup to catch blockers early
- Can pause after Week 2 (core services done)
- Prioritize critical path tickets
- Pair programming for complex tasks

**Action Plan if Occurs:**
1. Assess remaining work vs priority
2. Extend Phase 1 by 1 week if needed
3. Move non-critical tickets to Phase 2
4. Add resources if critical deadline
5. Communicate with stakeholders

---

### Risk 3: Team Resistance to New Patterns
**Probability:** LOW  
**Impact:** MEDIUM  

**Mitigation:**
- Involve team in planning phase
- Pair programming sessions
- Show benefits with pilot (GuruService)
- Regular knowledge sharing
- Celebrate quick wins

**Action Plan if Occurs:**
1. Schedule additional training sessions
2. Increase pairing time
3. Provide more code examples
4. Adjust complexity if needed
5. Address concerns openly

---

### Risk 4: Performance Regression
**Probability:** LOW  
**Impact:** MEDIUM  

**Mitigation:**
- Benchmark before/after each ticket
- Load testing on staging environment
- Optimize service instantiation
- Implement caching where needed
- Monitor production metrics

**Action Plan if Occurs:**
1. Profile slow endpoints
2. Add caching layer
3. Optimize database queries
4. Consider lazy loading
5. Rollback if critical

---

## ?? DEVELOPER SETUP GUIDE

### Prerequisites

**Tools Required:**
- PHP 8.0+
- Composer
- PHPUnit
- Git
- IDE with PHP support (VS Code, PhpStorm)

**Knowledge Required:**
- CodeIgniter 4 fundamentals
- Service layer pattern
- Repository pattern
- Dependency injection
- Unit testing basics

### Getting Started

**Step 1: Create Feature Branch**
```bash
git checkout develop
git pull origin develop
git checkout -b feature/phase1-service-layer
```

**Step 2: Setup Service Directory**
```bash
mkdir -p app/Services
mkdir -p app/Repositories/Contracts
mkdir -p tests/unit/Services
mkdir -p tests/unit/Repositories
```

**Step 3: Run Existing Tests**
```bash
./vendor/bin/phpunit
# All tests should pass before starting
```

**Step 4: Start with Ticket #1**
- Read ticket description carefully
- Review acceptance criteria
- Check dependencies
- Create task branch if working with team

**Step 5: Development Workflow**
1. Write failing test first (TDD)
2. Implement minimum code to pass
3. Refactor for quality
4. Run all tests
5. Commit with clear message
6. Push and create PR

---

## ?? DELIVERABLES SUMMARY

### Code Deliverables

**Services (6 classes):**
- `app/Services/BaseService.php`
- `app/Services/GuruService.php`
- `app/Services/SiswaService.php`
- `app/Services/AbsensiService.php`
- `app/Services/EmailService.php`
- `app/Services/ProfileService.php`
- `app/Services/JurnalService.php`
- `app/Services/FileService.php`

**Repositories (4 classes + interfaces):**
- `app/Repositories/Contracts/BaseRepositoryInterface.php`
- `app/Repositories/Contracts/GuruRepositoryInterface.php`
- `app/Repositories/Contracts/SiswaRepositoryInterface.php`
- `app/Repositories/Contracts/AbsensiRepositoryInterface.php`
- `app/Repositories/GuruRepository.php`
- `app/Repositories/SiswaRepository.php`
- `app/Repositories/AbsensiRepository.php`

**Excel Services (2 classes):**
- `app/Services/Excel/ExcelTemplateGenerator.php`
- `app/Services/Excel/JadwalTemplateGenerator.php`

**Import Services (2 classes):**
- `app/Services/Import/BaseImportService.php`
- `app/Services/Import/JadwalImportService.php`

**Refactored Controllers (6 files):**
- `app/Controllers/Admin/GuruController.php`
- `app/Controllers/Admin/SiswaController.php`
- `app/Controllers/Guru/AbsensiController.php`
- `app/Controllers/Admin/JadwalController.php`
- `app/Controllers/ProfileController.php`
- `app/Controllers/Guru/JurnalController.php`

**Tests (14+ files):**
- Unit tests for all services
- Unit tests for all repositories
- Integration tests for controllers

### Documentation Deliverables

**Architecture Documentation:**
- `docs/architecture/SERVICE_LAYER.md`
- `docs/architecture/REPOSITORY_PATTERN.md`
- `docs/guides/REFACTORING_GUIDE.md`

**Updated Files:**
- `CHANGELOG.md` - Phase 1 completion notes
- `TODO.md` - Mark Phase 1 as complete
- `README.md` - Update architecture section

---

## ?? SUCCESS METRICS

### Quantitative Metrics

| Metric | Before | Target | Success Criteria |
|--------|--------|--------|------------------|
| **Avg Controller Lines** | 258 | 180 | <200 lines |
| **Long Methods (>100)** | 16 | 0 | 0 methods |
| **Code Duplication** | 235 | 120 | <150 instances |
| **Service Classes** | 0 | 6 | 6+ classes |
| **Repository Classes** | 0 | 3 | 3+ classes |
| **Test Coverage** | 5% | 60% | >55% |
| **SOLID Score** | 65/100 | 85/100 | >80/100 |

### Qualitative Metrics

**Code Quality:**
- ? Clear separation of concerns
- ? Single Responsibility Principle followed
- ? Dependency Injection implemented
- ? Testable code (mocked dependencies)

**Developer Experience:**
- ? Faster feature development
- ? Easier debugging
- ? Better code organization
- ? Reduced onboarding time

**Maintainability:**
- ? Reduced technical debt
- ? Easier to extend
- ? Better documentation
- ? Consistent patterns

---

## ? PHASE 1 COMPLETION CHECKLIST

### Code Completion

- [ ] All 14 tickets completed
- [ ] All services implemented and tested
- [ ] All repositories implemented and tested
- [ ] All controllers refactored
- [ ] All long methods refactored
- [ ] No methods >100 lines
- [ ] Code duplication reduced by 50%+

### Testing Completion

- [ ] Unit test coverage >60%
- [ ] All unit tests passing
- [ ] Integration tests passing
- [ ] Manual testing completed
- [ ] No regressions found
- [ ] Performance acceptable

### Documentation Completion

- [ ] SERVICE_LAYER.md created
- [ ] REPOSITORY_PATTERN.md created
- [ ] REFACTORING_GUIDE.md created
- [ ] CHANGELOG.md updated
- [ ] TODO.md updated
- [ ] Code comments added

### Deployment Completion

- [ ] Staging deployment successful
- [ ] Production deployment plan ready
- [ ] Rollback plan documented
- [ ] Team training completed
- [ ] Stakeholder demo completed

---

## ?? LESSONS LEARNED TEMPLATE

### What Went Well

*To be filled after Phase 1 completion*

- Example: Pilot approach with GuruService was effective
- Example: Pair programming accelerated learning
- Example: Test coverage exceeded expectations

### What Could Be Improved

*To be filled after Phase 1 completion*

- Example: Timeline was tight, needed more buffer
- Example: More upfront training would help
- Example: Better communication on blockers

### Key Takeaways

*To be filled after Phase 1 completion*

- Example: Service layer pattern significantly improved code quality
- Example: Repository pattern made testing much easier
- Example: Daily standups were essential for coordination

### Recommendations for Phase 2

*To be filled after Phase 1 completion*

- Add 20% buffer to estimates
- More frequent code reviews
- Automated testing in CI/CD
- Consider pair programming for complex tasks

---

## ?? NEXT STEPS AFTER PHASE 1

### Immediate Actions (Week 4)

1. **Deploy to Production**
   - Create deployment plan
   - Schedule maintenance window
   - Execute deployment
   - Monitor for issues

2. **Retrospective Meeting**
   - Gather team feedback
   - Document lessons learned
   - Celebrate achievements
   - Plan improvements

3. **Stakeholder Communication**
   - Present completion report
   - Show metrics improvements
   - Discuss ROI
   - Plan Phase 2

### Phase 2 Planning

**Potential Focus Areas:**
- Complete pagination implementation (3 controllers remaining)
- Implement notification system
- Add PDF export functionality
- Expand test coverage to 80%
- Refactor remaining views to use templates
- Implement breadcrumb navigation

**Estimated Timeline:** 2-3 weeks

---

## ?? SUPPORT & QUESTIONS

### For Technical Questions

**Review Resources:**
1. This refactoring plan document
2. Code examples and technical notes
3. External resources (Martin Fowler, etc.)
4. CI4 documentation

**Contact Points:**
- Team Lead: For architecture decisions
- Senior Developer: For implementation help
- QA Lead: For testing strategies

### For Blockers

**Escalation Path:**
1. Try to resolve with pair programming
2. Ask in team chat/standup
3. Escalate to team lead if blocked >2 hours
4. Document blocker in daily standup

---

## ?? FINAL NOTES

### Critical Success Factors

1. **Communication** - Daily standups, clear blockers
2. **Testing** - Write tests first, maintain coverage
3. **Incremental** - Small commits, frequent merges
4. **Collaboration** - Pair programming, code reviews
5. **Focus** - Stay on critical path, avoid scope creep

### Remember

- **Quality over speed** - Better to do it right than fast
- **Test everything** - Untested code is legacy code
- **Ask for help** - No question is too small
- **Document as you go** - Future you will thank you
- **Celebrate wins** - Each ticket completed is progress

---

## ?? APPENDIX

### A. Ticket Summary Table

| # | Title | Priority | Estimate | Type | Status |
|---|-------|----------|----------|------|--------|
| 1 | Service Base Structure | Critical | 4h | Task | Pending |
| 2 | GuruService (Pilot) | Critical | 12h | Feature | Pending |
| 3 | EmailService | High | 6h | Feature | Pending |
| 4 | SiswaService | High | 10h | Feature | Pending |
| 5 | AbsensiService | High | 14h | Feature | Pending |
| 6 | Repository Interfaces | High | 4h | Task | Pending |
| 7 | GuruRepository | High | 6h | Feature | Pending |
| 8 | SiswaRepository | High | 6h | Feature | Pending |
| 9 | AbsensiRepository | High | 8h | Feature | Pending |
| 10 | JadwalTemplate Refactor | High | 8h | Refactor | Pending |
| 11 | JadwalImport Refactor | High | 10h | Refactor | Pending |
| 12 | ProfileService Refactor | Medium | 8h | Refactor | Pending |
| 13 | JurnalService Refactor | Medium | 8h | Refactor | Pending |
| 14 | AbsensiUpdate Refactor | Medium | 10h | Refactor | Pending |

**Total Estimated Hours:** 114 hours  
**With Buffer (20%):** 137 hours  
**Team of 2:** ~17 days (3.5 weeks)  
**Team of 3:** ~11 days (2.2 weeks)

### B. File Structure After Phase 1

```
app/
+-- Controllers/
¦   +-- (Refactored, thin controllers)
+-- Services/
¦   +-- BaseService.php
¦   +-- GuruService.php
¦   +-- SiswaService.php
¦   +-- AbsensiService.php
¦   +-- EmailService.php
¦   +-- ProfileService.php
¦   +-- JurnalService.php
¦   +-- FileService.php
¦   +-- Excel/
¦   ¦   +-- ExcelTemplateGenerator.php
¦   ¦   +-- JadwalTemplateGenerator.php
¦   +-- Import/
¦       +-- BaseImportService.php
¦       +-- JadwalImportService.php
+-- Repositories/
¦   +-- Contracts/
¦   ¦   +-- BaseRepositoryInterface.php
¦   ¦   +-- GuruRepositoryInterface.php
¦   ¦   +-- SiswaRepositoryInterface.php
¦   ¦   +-- AbsensiRepositoryInterface.php
¦   +-- GuruRepository.php
¦   +-- SiswaRepository.php
¦   +-- AbsensiRepository.php
+-- Models/
    +-- (Simplified, data access only)

tests/
+-- unit/
¦   +-- Services/
¦   ¦   +-- GuruServiceTest.php
¦   ¦   +-- SiswaServiceTest.php
¦   ¦   +-- AbsensiServiceTest.php
¦   ¦   +-- EmailServiceTest.php
¦   +-- Repositories/
¦       +-- GuruRepositoryTest.php
¦       +-- SiswaRepositoryTest.php
¦       +-- AbsensiRepositoryTest.php
+-- integration/
    +-- Controllers/
        +-- GuruControllerTest.php
        +-- SiswaControllerTest.php
        +-- AbsensiControllerTest.php
```

---

**END OF REFACTORING PLAN - PHASE 1**

---

**Document Version:** 1.0  
**Created:** 2026-01-30  
**Last Updated:** 2026-01-30  
**Status:** Ready for Implementation  

**Next Action:** Begin Day 1 - Task 1 (Create Service Base Structure)

Good luck with Phase 1 refactoring! ??

---

