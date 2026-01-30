# 🛠️ Service Layer Implementation Guide - SIMACCA

**Version:** 1.0  
**Date:** 2026-01-30  
**Purpose:** Step-by-step implementation instructions  
**Audience:** Developers implementing the service layer pattern

---

## 📋 Table of Contents

- [Quick Start](#quick-start)
- [Day 1: Infrastructure Setup](#day-1-infrastructure-setup)
- [Day 2-3: GuruService](#day-2-3-guruservice)
- [Day 4: ImportExportService](#day-4-importexportservice)
- [Day 5-6: AbsensiService](#day-5-6-absensiservice)
- [Testing Guide](#testing-guide)
- [Troubleshooting](#troubleshooting)

---

## 🚀 Quick Start

### Prerequisites

Before starting, ensure you have:
- [ ] SIMACCA codebase cloned
- [ ] Development environment running
- [ ] Database migrations up to date
- [ ] PHPUnit installed for testing
- [ ] Text editor/IDE ready

### Setup Checklist

```bash
# 1. Create services directory
mkdir -p app/Services

# 2. Verify PHPUnit works
vendor/bin/phpunit --version

# 3. Create test directory for services
mkdir -p tests/Services

# 4. Backup current code
git checkout -b feature/service-layer-refactoring
git commit -m "Starting service layer refactoring"
```

---

## 📅 Day 1: Infrastructure Setup

**Time Required:** 4-6 hours  
**Goal:** Create foundation for all services

### Step 1.1: Create BaseService Class (1 hour)

**File:** `app/Services/BaseService.php`

```php
<?php

namespace App\Services;

use CodeIgniter\Database\ConnectionInterface;

/**
 * Base Service Class
 * 
 * Provides common functionality for all services:
 * - Database transaction management
 * - Logging
 * - Error handling
 */
abstract class BaseService
{
    /**
     * Database connection
     * @var ConnectionInterface
     */
    protected $db;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    
    /**
     * Execute callback within database transaction
     * 
     * Automatically handles commit/rollback based on success/failure
     * 
     * @param callable $callback Function to execute in transaction
     * @return mixed Result from callback
     * @throws \Exception If transaction fails
     * 
     * @example
     * $result = $this->transaction(function() {
     *     // Your database operations here
     *     return $someValue;
     * });
     */
    protected function transaction(callable $callback)
    {
        $this->db->transStart();
        
        try {
            $result = $callback();
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaction failed - database error occurred');
            }
            
            return $result;
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            $this->log('error', 'Transaction failed: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Log service operation
     * 
     * @param string $level Log level (info, warning, error, etc.)
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        $serviceName = basename(str_replace('\\', '/', get_class($this)));
        $fullMessage = "[{$serviceName}] {$message}";
        
        log_message($level, $fullMessage, $context);
    }
    
    /**
     * Validate required fields in data array
     * 
     * @param array $data Data to validate
     * @param array $required Required field names
     * @return void
     * @throws \InvalidArgumentException If required field missing
     */
    protected function validateRequired(array $data, array $required): void
    {
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === null || $data[$field] === '') {
                throw new \InvalidArgumentException("Required field missing: {$field}");
            }
        }
    }
}
```

**Test Your Work:**

```bash
# Create test file
touch tests/Services/BaseServiceTest.php
```

---

### Step 1.2: Create BaseService Test (1 hour)

**File:** `tests/Services/BaseServiceTest.php`

```php
<?php

namespace Tests\Services;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class BaseServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    
    protected $testService;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create anonymous test service
        $this->testService = new class extends \App\Services\BaseService {
            public function testTransaction(callable $callback) {
                return $this->transaction($callback);
            }
            
            public function testValidateRequired(array $data, array $required) {
                return $this->validateRequired($data, $required);
            }
        };
    }
    
    public function testTransactionCommitsOnSuccess()
    {
        $result = $this->testService->testTransaction(function() {
            return 'success';
        });
        
        $this->assertEquals('success', $result);
    }
    
    public function testTransactionRollsBackOnException()
    {
        $this->expectException(\Exception::class);
        
        $this->testService->testTransaction(function() {
            throw new \Exception('Test exception');
        });
    }
    
    public function testValidateRequiredThrowsExceptionForMissingField()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Required field missing: name');
        
        $this->testService->testValidateRequired(
            ['email' => 'test@example.com'],
            ['name', 'email']
        );
    }
    
    public function testValidateRequiredPassesWithAllFields()
    {
        // Should not throw exception
        $this->testService->testValidateRequired(
            ['name' => 'Test', 'email' => 'test@example.com'],
            ['name', 'email']
        );
        
        $this->assertTrue(true);
    }
}
```

**Run Tests:**

```bash
vendor/bin/phpunit tests/Services/BaseServiceTest.php
```

**Expected Output:**
```
PHPUnit 9.x
....                                                                4 / 4 (100%)

Time: 00:00.123, Memory: 10.00 MB

OK (4 tests, 5 assertions)
```

---

### Step 1.3: Create Service Provider (Optional but Recommended) (1 hour)

**File:** `app/Libraries/ServiceProvider.php`

```php
<?php

namespace App\Libraries;

/**
 * Service Provider
 * 
 * Manages service instances (singleton pattern)
 * Useful for dependency injection and testing
 */
class ServiceProvider
{
    /**
     * Service instances
     * @var array
     */
    private static $instances = [];
    
    /**
     * Get service instance
     * 
     * Returns existing instance or creates new one
     * 
     * @param string $serviceClass Fully qualified service class name
     * @return object Service instance
     * 
     * @example
     * $guruService = ServiceProvider::get(\App\Services\GuruService::class);
     */
    public static function get(string $serviceClass)
    {
        if (!isset(self::$instances[$serviceClass])) {
            if (!class_exists($serviceClass)) {
                throw new \RuntimeException("Service class not found: {$serviceClass}");
            }
            
            self::$instances[$serviceClass] = new $serviceClass();
        }
        
        return self::$instances[$serviceClass];
    }
    
    /**
     * Set service instance (useful for testing with mocks)
     * 
     * @param string $serviceClass Service class name
     * @param object $instance Service instance or mock
     * @return void
     */
    public static function set(string $serviceClass, $instance): void
    {
        self::$instances[$serviceClass] = $instance;
    }
    
    /**
     * Clear service instance(s)
     * 
     * @param string|null $serviceClass Specific service or null for all
     * @return void
     */
    public static function reset(?string $serviceClass = null): void
    {
        if ($serviceClass) {
            unset(self::$instances[$serviceClass]);
        } else {
            self::$instances = [];
        }
    }
    
    /**
     * Check if service instance exists
     * 
     * @param string $serviceClass Service class name
     * @return bool
     */
    public static function has(string $serviceClass): bool
    {
        return isset(self::$instances[$serviceClass]);
    }
}
```

---

### Step 1.4: Document Service Pattern (1 hour)

**File:** `app/Services/README.md`

```markdown
# Services Directory

This directory contains business logic services that implement the Service Layer Pattern.

## Structure

- `BaseService.php` - Base class for all services
- Individual service files (e.g., `GuruService.php`, `AbsensiService.php`)

## Usage

### Creating a Service

```php
class MyService extends BaseService
{
    public function doSomething(array $data)
    {
        return $this->transaction(function() use ($data) {
            // Business logic here
            return $result;
        });
    }
}
```

### Using in Controller

```php
class MyController extends BaseController
{
    private $myService;
    
    public function __construct()
    {
        $this->myService = new MyService();
    }
    
    public function action()
    {
        try {
            $result = $this->myService->doSomething($data);
            return redirect()->with('success', 'Operation successful');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
```

## Guidelines

- Services contain business logic only
- Controllers handle HTTP requests/responses
- Models handle database queries
- Use transactions for multi-step operations
- Throw exceptions for error handling
```

---

### Step 1.5: Verify Setup (30 minutes)

**Verification Checklist:**

```bash
# 1. Check directory structure
ls -la app/Services/
# Should show: BaseService.php, README.md

ls -la app/Libraries/
# Should show: ServiceProvider.php

ls -la tests/Services/
# Should show: BaseServiceTest.php

# 2. Run tests
vendor/bin/phpunit tests/Services/BaseServiceTest.php
# Should pass all 4 tests

# 3. Check PHP syntax
php -l app/Services/BaseService.php
php -l app/Libraries/ServiceProvider.php
# Both should output: No syntax errors detected

# 4. Commit your work
git add app/Services/ app/Libraries/ServiceProvider.php tests/Services/
git commit -m "Day 1: Created service layer infrastructure"
```

---

## ✅ Day 1 Completion Checklist

- [ ] `app/Services/BaseService.php` created
- [ ] `app/Libraries/ServiceProvider.php` created
- [ ] `tests/Services/BaseServiceTest.php` created
- [ ] `app/Services/README.md` created
- [ ] All tests passing (4/4)
- [ ] Code committed to git
- [ ] No syntax errors

**If all checked:** ✅ Ready for Day 2!

---


## ?? Day 2-3: GuruService Implementation

**Time Required:** 12-16 hours  
**Goal:** Extract guru management business logic into service

### Overview

GuruService handles:
- Creating guru with user account
- Updating guru information
- Managing wali kelas assignments
- Deleting guru and cascading cleanup
- Transaction coordination between User and Guru models

---

### Step 2.1: Create GuruService Class (2 hours)

**File:** `app/Services/GuruService.php`

```php
<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\KelasModel;

/**
 * Guru Service
 * 
 * Handles guru management business logic including:
 * - User account creation/management
 * - Guru record management
 * - Wali kelas assignments
 */
class GuruService extends BaseService
{
    /**
     * Model instances
     */
    private UserModel \;
    private GuruModel \;
    private KelasModel \;
    
    /**
     * Constructor
     * 
     * Initialize required models
     */
    public function __construct()
    {
        parent::__construct();
        
        \->userModel = new UserModel();
        \->guruModel = new GuruModel();
        \->kelasModel = new KelasModel();
    }
    
    /**
     * Create new guru with user account
     * 
     * This method:
     * 1. Creates user account
     * 2. Creates guru record
     * 3. Assigns wali kelas if applicable
     * 
     * All operations run in a transaction.
     * 
     * @param array \ Guru and user data
     *   Required: username, password, role, nip, nama_lengkap, jenis_kelamin
     *   Optional: email, mata_pelajaran_id, is_wali_kelas, kelas_id
     * 
     * @return int Guru ID
     * @throws \Exception If validation fails or operation fails
     * 
     * @example
     * \ = \->createGuru([
     *     'username' => 'guru.test',
     *     'password' => 'password123',
     *     'role' => 'guru_mapel',
     *     'email' => 'guru@example.com',
     *     'nip' => '1234567890',
     *     'nama_lengkap' => 'Nama Guru',
     *     'jenis_kelamin' => 'L',
     *     'mata_pelajaran_id' => 1,
     *     'is_wali_kelas' => 1,
     *     'kelas_id' => 5
     * ]);
     */
    public function createGuru(array \): int
    {
        // Validate required fields
        \->validateRequired(\, [
            'username', 'password', 'role', 'nip', 
            'nama_lengkap', 'jenis_kelamin'
        ]);
        
        return \->transaction(function() use (\) {
            // 1. Check for duplicate NIP
            if (\->isDuplicateNip(\['nip'])) {
                throw new \Exception('NIP sudah terdaftar');
            }
            
            // 2. Check for duplicate username
            if (\->isDuplicateUsername(\['username'])) {
                throw new \Exception('Username sudah digunakan');
            }
            
            // 3. Create user account
            \ = \->createUserAccount([
                'username' => \['username'],
                'password' => \['password'],
                'role' => \['role'],
                'email' => \['email'] ?? null
            ]);
            
            // 4. Create guru record
            \ = [
                'user_id' => \,
                'nip' => \['nip'],
                'nama_lengkap' => \['nama_lengkap'],
                'jenis_kelamin' => \['jenis_kelamin'],
                'mata_pelajaran_id' => \['mata_pelajaran_id'] ?? null,
                'is_wali_kelas' => \['is_wali_kelas'] ?? 0,
                'kelas_id' => \['kelas_id'] ?? null,
                'is_active' => 1
            ];
            
            \ = \->guruModel->insert(\);
            
            if (!\) {
                throw new \Exception('Gagal membuat data guru');
            }
            
            // 5. Assign wali kelas if applicable
            if ((\['is_wali_kelas'] ?? 0) && (\['kelas_id'] ?? null)) {
                \->assignWaliKelas(\, \['kelas_id']);
            }
            
            \->log('info', 'Guru created successfully', [
                'guru_id' => \,
                'nip' => \['nip'],
                'nama' => \['nama_lengkap']
            ]);
            
            return \;
        });
    }
    
    /**
     * Update existing guru
     * 
     * Updates both user account and guru record.
     * Handles wali kelas assignment changes.
     * 
     * @param int \ Guru ID to update
     * @param array \ Updated data
     * @return bool Success status
     * @throws \Exception If guru not found or update fails
     */
    public function updateGuru(int \, array \): bool
    {
        return \->transaction(function() use (\, \) {
            // 1. Get existing guru
            \ = \->guruModel->find(\);
            
            if (!\) {
                throw new \Exception('Guru tidak ditemukan');
            }
            
            // 2. Check for duplicate NIP (excluding current guru)
            if (isset(\['nip']) && \['nip'] !== \['nip']) {
                if (\->isDuplicateNip(\['nip'], \)) {
                    throw new \Exception('NIP sudah terdaftar');
                }
            }
            
            // 3. Update user account if needed
            if (isset(\['username']) || isset(\['email']) || 
                isset(\['password']) || isset(\['role'])) {
                
                \->updateUserAccount(\['user_id'], [
                    'username' => \['username'] ?? null,
                    'email' => \['email'] ?? null,
                    'password' => \['password'] ?? null,
                    'role' => \['role'] ?? null
                ]);
            }
            
            // 4. Update guru data
            \ = [
                'nip' => \['nip'] ?? \['nip'],
                'nama_lengkap' => \['nama_lengkap'] ?? \['nama_lengkap'],
                'jenis_kelamin' => \['jenis_kelamin'] ?? \['jenis_kelamin'],
                'mata_pelajaran_id' => \['mata_pelajaran_id'] ?? \['mata_pelajaran_id'],
                'is_wali_kelas' => \['is_wali_kelas'] ?? \['is_wali_kelas'],
                'kelas_id' => \['kelas_id'] ?? \['kelas_id']
            ];
            
            \->guruModel->update(\, \);
            
            // 5. Handle wali kelas changes
            \->handleWaliKelasChange(\, \, \);
            
            \->log('info', 'Guru updated successfully', [
                'guru_id' => \,
                'changes' => array_keys(\)
            ]);
            
            return true;
        });
    }
    
    /**
     * Delete guru (soft delete)
     * 
     * Deletes guru record and associated user account.
     * Removes wali kelas assignment if applicable.
     * 
     * @param int \ Guru ID to delete
     * @return bool Success status
     * @throws \Exception If guru not found
     */
    public function deleteGuru(int \): bool
    {
        return \->transaction(function() use (\) {
            \ = \->guruModel->find(\);
            
            if (!\) {
                throw new \Exception('Guru tidak ditemukan');
            }
            
            // Remove wali kelas assignment
            if (\['is_wali_kelas'] && \['kelas_id']) {
                \->removeWaliKelas(\, \['kelas_id']);
            }
            
            // Delete guru record
            \->guruModel->delete(\);
            
            // Delete user account
            \->userModel->delete(\['user_id']);
            
            \->log('info', 'Guru deleted successfully', [
                'guru_id' => \,
                'nip' => \['nip']
            ]);
            
            return true;
        });
    }
    
    /**
     * Get guru by ID with related data
     * 
     * @param int \ Guru ID
     * @return array|null Guru data with user and related info
     */
    public function getGuruById(int \): ?array
    {
        return \->guruModel
            ->select('guru.*, users.username, users.email, users.role, 
                     mata_pelajaran.nama_mapel, kelas.nama_kelas')
            ->join('users', 'users.id = guru.user_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left')
            ->join('kelas', 'kelas.id = guru.kelas_id', 'left')
            ->find(\);
    }
    
    // ========================================================================
    // PRIVATE HELPER METHODS
    // ========================================================================
    
    /**
     * Check if NIP already exists
     */
    private function isDuplicateNip(string \, ?int \ = null): bool
    {
        \ = \->guruModel->where('nip', \);
        
        if (\) {
            \->where('id !=', \);
        }
        
        return \->countAllResults() > 0;
    }
    
    /**
     * Check if username already exists
     */
    private function isDuplicateUsername(string \, ?int \ = null): bool
    {
        \ = \->userModel->where('username', \);
        
        if (\) {
            \->where('id !=', \);
        }
        
        return \->countAllResults() > 0;
    }
    
    /**
     * Create user account
     */
    private function createUserAccount(array \): int
    {
        \ = \->userModel->insert(\);
        
        if (!\) {
            throw new \Exception('Gagal membuat akun user');
        }
        
        return \;
    }
    
    /**
     * Update user account
     */
    private function updateUserAccount(int \, array \): void
    {
        // Remove null values
        \ = array_filter(\, fn(\) => \ !== null);
        
        if (!empty(\)) {
            \->userModel->update(\, \);
        }
    }
    
    /**
     * Assign guru as wali kelas
     */
    private function assignWaliKelas(int \, int \): void
    {
        \->kelasModel->update(\, ['wali_kelas_id' => \]);
    }
    
    /**
     * Remove wali kelas assignment
     */
    private function removeWaliKelas(int \, int \): void
    {
        \->kelasModel->update(\, ['wali_kelas_id' => null]);
    }
    
    /**
     * Handle wali kelas assignment changes
     */
    private function handleWaliKelasChange(int \, array \, array \): void
    {
        \ = \['is_wali_kelas'];
        \ = \['is_wali_kelas'];
        \ = \['kelas_id'];
        \ = \['kelas_id'];
        
        // Remove old assignment
        if (\ && \) {
            \->removeWaliKelas(\, \);
        }
        
        // Add new assignment
        if (\ && \) {
            \->assignWaliKelas(\, \);
        }
    }
}
```

---

### Step 2.2: Create GuruService Tests (3 hours)

**File:** `tests/Services/GuruServiceTest.php`

```php
<?php

namespace Tests\Services;

use App\Services\GuruService;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\KelasModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class GuruServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    
    protected GuruService \;
    protected UserModel \;
    protected GuruModel \;
    protected KelasModel \;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        \->guruService = new GuruService();
        \->userModel = new UserModel();
        \->guruModel = new GuruModel();
        \->kelasModel = new KelasModel();
    }
    
    public function testCreateGuruSuccessfully()
    {
        \ = [
            'username' => 'test.guru',
            'password' => 'password123',
            'role' => 'guru_mapel',
            'email' => 'test@example.com',
            'nip' => '1234567890',
            'nama_lengkap' => 'Test Guru',
            'jenis_kelamin' => 'L',
            'mata_pelajaran_id' => 1
        ];
        
        \ = \->guruService->createGuru(\);
        
        // Assertions
        \->assertIsInt(\);
        \->assertGreaterThan(0, \);
        
        // Verify guru was created
        \ = \->guruModel->find(\);
        \->assertNotNull(\);
        \->assertEquals(\['nip'], \['nip']);
        \->assertEquals(\['nama_lengkap'], \['nama_lengkap']);
        
        // Verify user was created
        \ = \->userModel->find(\['user_id']);
        \->assertNotNull(\);
        \->assertEquals(\['username'], \['username']);
    }
    
    public function testCreateGuruWithWaliKelasAssignment()
    {
        // Create test kelas first
        \ = \->kelasModel->insert([
            'nama_kelas' => 'X-1',
            'tingkat' => 10,
            'jurusan' => 'IPA'
        ]);
        
        \ = [
            'username' => 'wali.kelas',
            'password' => 'password123',
            'role' => 'wali_kelas',
            'nip' => '9876543210',
            'nama_lengkap' => 'Wali Kelas Test',
            'jenis_kelamin' => 'P',
            'is_wali_kelas' => 1,
            'kelas_id' => \
        ];
        
        \ = \->guruService->createGuru(\);
        
        // Verify wali kelas was assigned
        \ = \->kelasModel->find(\);
        \->assertEquals(\, \['wali_kelas_id']);
    }
    
    public function testCreateGuruWithDuplicateNipThrowsException()
    {
        \->expectException(\Exception::class);
        \->expectExceptionMessage('NIP sudah terdaftar');
        
        \ = [
            'username' => 'guru1',
            'password' => 'password123',
            'role' => 'guru_mapel',
            'nip' => '1111111111',
            'nama_lengkap' => 'Guru 1',
            'jenis_kelamin' => 'L'
        ];
        
        // Create first guru
        \->guruService->createGuru(\);
        
        // Try to create duplicate
        \['username'] = 'guru2'; // Different username
        \->guruService->createGuru(\); // Same NIP
    }
    
    public function testUpdateGuruSuccessfully()
    {
        // Create guru first
        \ = \->guruService->createGuru([
            'username' => 'update.test',
            'password' => 'password123',
            'role' => 'guru_mapel',
            'nip' => '5555555555',
            'nama_lengkap' => 'Original Name',
            'jenis_kelamin' => 'L'
        ]);
        
        // Update guru
        \ = \->guruService->updateGuru(\, [
            'nama_lengkap' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
        
        \->assertTrue(\);
        
        // Verify update
        \ = \->guruModel->find(\);
        \->assertEquals('Updated Name', \['nama_lengkap']);
        
        \ = \->userModel->find(\['user_id']);
        \->assertEquals('updated@example.com', \['email']);
    }
    
    public function testDeleteGuruSuccessfully()
    {
        // Create guru
        \ = \->guruService->createGuru([
            'username' => 'delete.test',
            'password' => 'password123',
            'role' => 'guru_mapel',
            'nip' => '7777777777',
            'nama_lengkap' => 'Delete Test',
            'jenis_kelamin' => 'L'
        ]);
        
        \ = \->guruModel->find(\);
        \ = \['user_id'];
        
        // Delete guru
        \ = \->guruService->deleteGuru(\);
        
        \->assertTrue(\);
        
        // Verify deletion
        \->assertNull(\->guruModel->find(\));
        \->assertNull(\->userModel->find(\));
    }
}
```

**Run Tests:**

```bash
vendor/bin/phpunit tests/Services/GuruServiceTest.php
```

---

### Step 2.3: Refactor GuruController (3 hours)

**File:** `app/Controllers/Admin/GuruController.php`

**BEFORE (store method - 70 lines):**
```php
public function store()
{
    // 70 lines of validation, transaction, user creation, guru creation, 
    // wali kelas assignment, error handling, etc.
}
```

**AFTER (store method - 15 lines):**
```php
public function store()
{
    // Validate input
    if (!\->validate(\->getValidationRules())) {
        return redirect()->back()
            ->withInput()
            ->with('errors', \->validator->getErrors());
    }
    
    try {
        // Call service
        \ = \->guruService->createGuru(\->request->getPost());
        
        return redirect()->to('/admin/guru')
            ->with('success', 'Data guru berhasil ditambahkan');
            
    } catch (\Exception \) {
        return redirect()->back()
            ->withInput()
            ->with('error', \->getMessage());
    }
}
```

**Complete Refactored Controller:**

```php
<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\GuruService;
use App\Models\GuruModel;
use App\Models\MataPelajaranModel;
use App\Models\KelasModel;

class GuruController extends BaseController
{
    private GuruService \;
    private GuruModel \;
    private MataPelajaranModel \;
    private KelasModel \;
    
    public function __construct()
    {
        \->guruService = new GuruService();
        \->guruModel = new GuruModel();
        \->mataPelajaranModel = new MataPelajaranModel();
        \->kelasModel = new KelasModel();
    }
    
    public function index()
    {
        \ = [
            'title' => 'Data Guru',
            'guru' => \->guruModel->getGuruWithDetails()
        ];
        
        return view('admin/guru/index', \);
    }
    
    public function create()
    {
        \ = [
            'title' => 'Tambah Guru',
            'mapel' => \->mataPelajaranModel->findAll(),
            'kelas' => \->kelasModel->getAvailableKelas()
        ];
        
        return view('admin/guru/create', \);
    }
    
    public function store()
    {
        if (!\->validate(\->getValidationRules())) {
            return redirect()->back()->withInput()
                ->with('errors', \->validator->getErrors());
        }
        
        try {
            \ = \->guruService->createGuru(\->request->getPost());
            
            return redirect()->to('/admin/guru')
                ->with('success', 'Data guru berhasil ditambahkan');
                
        } catch (\Exception \) {
            return redirect()->back()->withInput()
                ->with('error', \->getMessage());
        }
    }
    
    public function edit(\)
    {
        \ = \->guruService->getGuruById(\);
        
        if (!\) {
            return redirect()->to('/admin/guru')
                ->with('error', 'Guru tidak ditemukan');
        }
        
        \ = [
            'title' => 'Edit Guru',
            'guru' => \,
            'mapel' => \->mataPelajaranModel->findAll(),
            'kelas' => \->kelasModel->getAvailableKelas()
        ];
        
        return view('admin/guru/edit', \);
    }
    
    public function update(\)
    {
        if (!\->validate(\->getValidationRules(\))) {
            return redirect()->back()->withInput()
                ->with('errors', \->validator->getErrors());
        }
        
        try {
            \->guruService->updateGuru(\, \->request->getPost());
            
            return redirect()->to('/admin/guru')
                ->with('success', 'Data guru berhasil diupdate');
                
        } catch (\Exception \) {
            return redirect()->back()->withInput()
                ->with('error', \->getMessage());
        }
    }
    
    public function delete(\)
    {
        try {
            \->guruService->deleteGuru(\);
            
            return redirect()->to('/admin/guru')
                ->with('success', 'Data guru berhasil dihapus');
                
        } catch (\Exception \) {
            return redirect()->back()
                ->with('error', \->getMessage());
        }
    }
    
    private function getValidationRules(\ = null): array
    {
        \ = 'required|numeric|exact_length[10]';
        \ = 'required|min_length[5]|max_length[50]';
        
        if (\) {
            \ .= \"|is_unique[guru.nip,id,\]\";
            \ .= \"|is_unique[users.username,id,\]\";
        } else {
            \ .= '|is_unique[guru.nip]';
            \ .= '|is_unique[users.username]';
        }
        
        return [
            'username' => \,
            'password' => \ ? 'permit_empty|min_length[6]' : 'required|min_length[6]',
            'role' => 'required|in_list[guru_mapel,wali_kelas,wakakur]',
            'email' => 'permit_empty|valid_email',
            'nip' => \,
            'nama_lengkap' => 'required|min_length[3]|max_length[100]',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'mata_pelajaran_id' => 'permit_empty|numeric',
            'is_wali_kelas' => 'permit_empty|in_list[0,1]',
            'kelas_id' => 'permit_empty|numeric'
        ];
    }
}
```

**Code Reduction:**
- Before: ~800 lines
- After: ~150 lines
- **Reduction: 81%**

---

### Step 2.4: Integration Testing (2 hours)

Test the refactored controller:

```bash
# 1. Start development server
php spark serve

# 2. Test manually in browser
# - Create new guru
# - Edit guru
# - Delete guru
# - Test wali kelas assignment
# - Test validation errors

# 3. Check logs
tail -f writable/logs/log-\2026-01-30.log
```

---

## ? Day 2-3 Completion Checklist

- [ ] `GuruService.php` created and tested
- [ ] `GuruServiceTest.php` passing (6+ tests)
- [ ] `GuruController.php` refactored
- [ ] Manual testing completed
- [ ] Code reduction: ~650 lines removed
- [ ] All CRUD operations working
- [ ] Wali kelas assignment working
- [ ] Error handling working
- [ ] Logging implemented
- [ ] Code committed

**If all checked:** ? Ready for Day 4!


## ?? Day 4: ImportExportService Implementation

**Time Required:** 6-8 hours  
**Goal:** Create reusable Excel import/export functionality

### Quick Implementation

**File:** `app/Services/ImportExportService.php`

```php
<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ImportExportService extends BaseService
{
    public function createExcelExport(
        array \, 
        array \, 
        string \ = 'Export',
        array \ = []
    ): Spreadsheet
    {
        \ = new Spreadsheet();
        \ = \->getActiveSheet();
        \->setTitle(\);
        
        // Set headers (row 1)
        \ = 'A';
        foreach (\ as \) {
            \->setCellValue(\ . '1', \);
            \->getStyle(\ . '1')->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE0E0E0']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            \->getColumnDimension(\)->setAutoSize(true);
            \++;
        }
        
        // Fill data (starting from row 2)
        \ = 2;
        foreach (\ as \) {
            \ = 'A';
            foreach (\ as \ => \) {
                \ = isset(\[\]) 
                    ? \[\](\) 
                    : \;
                \->setCellValue(\ . \, \);
                \++;
            }
            \++;
        }
        
        return \;
    }
    
    public function downloadExcel(Spreadsheet \, string \): void
    {
        \ = new Xlsx(\);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . \ . '"');
        header('Cache-Control: max-age=0');
        
        \->save('php://output');
        exit();
    }
    
    public function parseExcelImport(string \, bool \ = true): array
    {
        \ = IOFactory::load(\);
        \ = \->getActiveSheet();
        \ = \->toArray();
        
        if (\) {
            array_shift(\);
        }
        
        return array_filter(\, fn(\) => !empty(array_filter(\)));
    }
}
```

### Usage Example

```php
// In GuruController::export()
public function export()
{
    \ = \->guruModel->getAllGuru();
    \ = new ImportExportService();
    
    \ = \->createExcelExport(
        data: \,
        headers: ['NO', 'NIP', 'NAMA', 'JENIS KELAMIN', 'MAPEL', 'STATUS'],
        sheetTitle: 'Data Guru',
        formatters: [
            'jenis_kelamin' => fn(\) => \ == 'L' ? 'Laki-laki' : 'Perempuan',
            'is_active' => fn(\) => \ ? 'Aktif' : 'Nonaktif'
        ]
    );
    
    \->downloadExcel(\, 'data-guru-' . date('Y-m-d') . '.xlsx');
}
```

**Impact:** Reduces 60 lines per export ? 10 lines (eliminates 150+ lines across 3 controllers)

---

## ?? Day 5-6: AbsensiService Implementation

**Time Required:** 12-16 hours  
**Goal:** Extract complex attendance logic

### Key Methods

```php
class AbsensiService extends BaseService
{
    public function createAbsensi(array \, array \): int
    {
        return \->transaction(function() use (\, \) {
            // 1. Validate jadwal exists
            // 2. Check for duplicates
            // 3. Create absensi header
            // 4. Create detail records for each student
            return \;
        });
    }
    
    public function updateAbsensi(int \, array \, array \): bool
    {
        return \->transaction(function() use (\, \, \) {
            // 1. Update header
            // 2. Delete old details
            // 3. Create new details
            return true;
        });
    }
    
    public function getAbsensiSummary(int \): array
    {
        // Return: ['total' => 30, 'hadir' => 28, 'sakit' => 1, 'izin' => 0, 'alpa' => 1]
    }
}
```

**Impact:** 1100 ? 400 lines (64% reduction)

---

## ?? Testing Guide

### Running Tests

```bash
# Run all service tests
vendor/bin/phpunit tests/Services/

# Run specific test
vendor/bin/phpunit tests/Services/GuruServiceTest.php

# Run with coverage
vendor/bin/phpunit --coverage-html coverage/ tests/Services/

# Run specific test method
vendor/bin/phpunit --filter testCreateGuruSuccessfully tests/Services/GuruServiceTest.php
```

### Writing Good Tests

```php
public function testServiceMethodName()
{
    // 1. ARRANGE: Set up test data
    \ = ['field' => 'value'];
    
    // 2. ACT: Execute the method
    \ = \->service->method(\);
    
    // 3. ASSERT: Verify expectations
    \->assertEquals('expected', \);
    \->assertNotNull(\);
}
```

### Test Coverage Goals

- **Services:** 80%+ coverage
- **Critical methods:** 100% coverage
- **Edge cases:** All handled

---

## ?? Troubleshooting

### Common Issues

#### Issue: "Class not found"
```bash
# Solution: Update composer autoload
composer dump-autoload
```

#### Issue: "Transaction failed"
```bash
# Check logs
tail -f writable/logs/log-\2026-01-30.log

# Enable query debugging
\->query()->getLastQuery();
```

#### Issue: Tests failing
```bash
# Reset test database
php spark migrate:refresh --all
php spark db:seed TestSeeder
```

---

## ?? Progress Tracking

### Daily Checklist

**Day 1:**
- [ ] BaseService created
- [ ] ServiceProvider created
- [ ] Tests passing
- [ ] Documentation complete

**Day 2-3:**
- [ ] GuruService created
- [ ] GuruServiceTest complete (6+ tests passing)
- [ ] GuruController refactored
- [ ] Manual testing passed
- [ ] 650 lines reduced

**Day 4:**
- [ ] ImportExportService created
- [ ] Export methods working
- [ ] Import methods working
- [ ] Integrated into 3+ controllers
- [ ] 150+ lines eliminated

**Day 5-6:**
- [ ] AbsensiService created
- [ ] AbsensiServiceTest complete
- [ ] AbsensiController refactored
- [ ] Complex logic extracted
- [ ] 700 lines reduced

---

## ? Quick Reference Checklist

### Before Starting Each Service

- [ ] Review service plan document
- [ ] Check models used by service
- [ ] Create service file
- [ ] Create test file
- [ ] Commit to git branch

### During Development

- [ ] Write service method
- [ ] Write test for method
- [ ] Run test (should pass)
- [ ] Add logging
- [ ] Handle edge cases

### After Completion

- [ ] All tests passing
- [ ] Controller refactored
- [ ] Manual testing done
- [ ] Code reviewed
- [ ] Documentation updated
- [ ] Committed to git

---

## ?? Code Templates

### Service Template

```php
<?php

namespace App\Services;

use App\Models\YourModel;

class YourService extends BaseService
{
    private YourModel \;
    
    public function __construct()
    {
        parent::__construct();
        \->yourModel = new YourModel();
    }
    
    public function yourMethod(array \): mixed
    {
        \->validateRequired(\, ['required_field']);
        
        return \->transaction(function() use (\) {
            // Your business logic here
            
            \->log('info', 'Operation completed', ['data' => \]);
            
            return \;
        });
    }
}
```

### Test Template

```php
<?php

namespace Tests\Services;

use App\Services\YourService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class YourServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    
    protected YourService \;
    
    protected function setUp(): void
    {
        parent::setUp();
        \->yourService = new YourService();
    }
    
    public function testYourMethodSuccessfully()
    {
        // Arrange
        \ = ['field' => 'value'];
        
        // Act
        \ = \->yourService->yourMethod(\);
        
        // Assert
        \->assertNotNull(\);
    }
}
```

### Controller Template

```php
public function action()
{
    if (!\->validate(\->getValidationRules())) {
        return redirect()->back()->withInput()
            ->with('errors', \->validator->getErrors());
    }
    
    try {
        \ = \->yourService->yourMethod(\->request->getPost());
        
        return redirect()->to('/your/route')
            ->with('success', 'Operation successful');
            
    } catch (\Exception \) {
        return redirect()->back()->withInput()
            ->with('error', \->getMessage());
    }
}
```

---

## ?? Success Metrics

### Expected Results After Implementation

| Metric | Target | How to Measure |
|--------|--------|----------------|
| **Code Reduction** | 70%+ | Line count before/after |
| **Test Coverage** | 80%+ | PHPUnit coverage report |
| **Test Passing** | 100% | All tests green |
| **Duplication** | <50 lines | Code analysis tools |
| **Maintainability** | Index >70 | PHPMetrics |

### Verification Commands

```bash
# Line count
find app/Controllers -name "*.php" -exec wc -l {} + | tail -n 1

# Test coverage
vendor/bin/phpunit --coverage-text

# Code quality
./vendor/bin/phpmetrics --report-html=metrics/ app/
```

---

## ?? Support

### Getting Help

1. **Review Documentation**
   - This implementation guide
   - Service layer refactoring plan
   - CodeIgniter 4 documentation

2. **Check Examples**
   - GuruService (complete example)
   - ImportExportService (reusable utility)
   - Test cases (testing patterns)

3. **Common Patterns**
   - Transaction usage: Wrap multi-step operations
   - Error handling: Throw exceptions with clear messages
   - Logging: Log important operations
   - Validation: Use validateRequired() helper

### Best Practices Reminder

**DO:**
- ? Use transactions for multi-step operations
- ? Throw exceptions with descriptive messages
- ? Log important operations
- ? Write tests for all public methods
- ? Keep methods focused (single responsibility)

**DON'T:**
- ? Access request/response in services
- ? Return HTML/JSON from services
- ? Mix business logic with presentation
- ? Forget to handle edge cases
- ? Skip writing tests

---

## ?? Completion

### After Completing All Services

1. **Run Full Test Suite**
   ```bash
   vendor/bin/phpunit
   ```

2. **Generate Coverage Report**
   ```bash
   vendor/bin/phpunit --coverage-html coverage/
   open coverage/index.html
   ```

3. **Code Quality Check**
   ```bash
   ./vendor/bin/phpstan analyse app/Services
   ```

4. **Update Documentation**
   - Mark completed items in refactoring plan
   - Document any deviations
   - Note lessons learned

5. **Team Review**
   - Code review session
   - Demo to team
   - Training materials

---

## ?? Notes

### Tips for Success

1. **Start Small** - Begin with GuruService (best example)
2. **Test Early** - Write tests as you develop
3. **Commit Often** - Small, focused commits
4. **Ask Questions** - Don't hesitate to clarify
5. **Review Code** - Pair programming helps

### Common Pitfalls to Avoid

- Don't skip testing
- Don't forget to refactor controllers
- Don't over-engineer (KISS principle)
- Don't ignore edge cases
- Don't forget error handling

---

**Guide Version:** 1.0  
**Last Updated:** 2026-01-30  
**Status:** ? READY TO USE

**Happy Coding! ??**

