# Dependency Security Audit Report
**Date:** January 30, 2026  
**Project:** SIMACCA (Sistem Monitoring Absensi dan Catatan Cara Ajar)  
**Composer Version:** 2.9.3  
**PHP Version:** 8.2.12

---

## Executive Summary

Comprehensive dependency check reveals **1 critical security vulnerability** and **2 packages with available updates**.

### Security Status: ⚠️ **ACTION REQUIRED**

- 🔴 **1 Critical Security Vulnerability** in PHPUnit (CVE-2026-24765)
- 🟡 **2 Packages with Updates Available**
- ✅ **Core Framework (CodeIgniter 4.6.4) is Up-to-Date**

---

## 1. Security Vulnerabilities

### 🔴 CRITICAL: PHPUnit Unsafe Deserialization Vulnerability

**Package:** `phpunit/phpunit`  
**Current Version:** 10.5.60  
**Fixed Version:** 10.5.62  
**Severity:** HIGH  
**CVE:** CVE-2026-24765  
**Advisory ID:** PKSA-z3gr-8qht-p93v

#### Vulnerability Details

**Title:** PHPUnit Vulnerable to Unsafe Deserialization in PHPT Code Coverage Handling

**Affected Versions:**
- 12.0.0 - 12.5.7
- 11.0.0 - 11.5.49
- **10.0.0 - 10.5.61** ⚠️ (Your version: 10.5.60)
- 9.0.0 - 9.6.32
- < 8.5.52

**Description:**
PHPUnit has a vulnerability in its PHPT code coverage handling that allows unsafe deserialization. This could potentially allow attackers to execute arbitrary code during test execution.

**Impact on SIMACCA:**
- **Risk Level:** MEDIUM (Development/Test Environment Only)
- **Exploit Scenario:** Requires ability to modify test files
- **Production Impact:** None (PHPUnit is dev-dependency)

#### Recommended Action

**IMMEDIATE - Update PHPUnit:**

```bash
# Update to patched version
composer update phpunit/phpunit --with-dependencies

# Or update to latest stable
composer require --dev phpunit/phpunit "^10.5.62"
```

**Verification:**
```bash
composer show phpunit/phpunit
# Should show: versions : * 10.5.62 or higher
```

#### Patched Versions
- **10.5.62+** (Recommended for your project)
- 11.5.50+
- 12.5.8+
- 9.6.33+
- 8.5.52+

**Reference:**
- GitHub Advisory: https://github.com/advisories/GHSA-vvj3-c3rp-c85p
- CVE: https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2026-24765

---

## 2. Outdated Packages

### Direct Dependencies (composer.json)

#### 🟡 Medium Priority: PhpSpreadsheet

**Package:** `phpoffice/phpspreadsheet`  
**Current Version:** 5.3.0  
**Latest Version:** 5.4.0  
**Type:** Patch/Minor Release  
**Priority:** MEDIUM

**What's New in 5.4.0:**
- Bug fixes
- Performance improvements
- Security patches (if any)

**Recommendation:**
```bash
composer update phpoffice/phpspreadsheet
```

**Testing Required:**
- Excel import/export functionality
- Template generation (guru, siswa, jadwal)
- File format compatibility

**Impact:** LOW - Backward compatible update

---

#### 🟡 Low Priority: PHPUnit (After Security Fix)

**Package:** `phpunit/phpunit`  
**Current Version:** 10.5.60  
**Latest Stable:** 10.5.62 (Security fix)  
**Latest Major:** 11.5.50  
**Type:** Major release available  
**Priority:** LOW (for major upgrade)

**Options:**

1. **Security Update Only (Recommended):**
   ```bash
   composer update phpunit/phpunit
   # Updates to 10.5.62
   ```

2. **Major Version Upgrade (Optional):**
   ```bash
   composer require --dev phpunit/phpunit "^11.5"
   ```

**Note:** PHPUnit 11 requires PHP 8.2+ (you have 8.2.12 ✓)

**Breaking Changes in PHPUnit 11:**
- Deprecations removed from PHPUnit 10
- New assertion methods
- Changes to test runner output

**Recommendation:** 
- Apply security patch now (10.5.62)
- Consider PHPUnit 11 upgrade in next sprint (requires test updates)

---

### Transitive Dependencies (Indirect)

Multiple PHPUnit-related packages have major versions available, but these will update automatically when PHPUnit is upgraded.

**Affected Packages:**
- `phpunit/php-code-coverage` (10.1.16 → 11.0.12)
- `phpunit/php-file-iterator` (4.1.0 → 5.1.0)
- `phpunit/php-invoker` (4.0.0 → 5.0.1)
- `phpunit/php-text-template` (3.0.1 → 4.0.1)
- `phpunit/php-timer` (6.0.0 → 7.0.1)
- All `sebastian/*` packages (various versions)

**Action:** No direct action needed - will update when PHPUnit is upgraded.

---

## 3. Up-to-Date Packages ✅

### Core Framework

**Package:** `codeigniter4/framework`  
**Current Version:** 4.6.4  
**Status:** ✅ **UP TO DATE**

CodeIgniter 4.6.4 is the latest stable release. Well done!

---

### Production Dependencies

All production dependencies are current:

| Package | Version | Status |
|---------|---------|--------|
| `codeigniter4/framework` | 4.6.4 | ✅ Latest |
| `phpoffice/phpspreadsheet` | 5.3.0 | 🟡 5.4.0 available |

---

### Development Dependencies

| Package | Version | Latest | Status |
|---------|---------|--------|--------|
| `fakerphp/faker` | 1.24.1 | 1.24.1 | ✅ Latest |
| `mikey179/vfsstream` | 1.6.12 | 1.6.12 | ✅ Latest |
| `phpunit/phpunit` | 10.5.60 | 10.5.62 | 🔴 Security update |

---

## 4. PHP Version Compatibility

### Current Environment

**PHP Version:** 8.2.12  
**Required:** ^8.1  
**Status:** ✅ Compatible

### Package Compatibility Matrix

| Package | Min PHP | Max PHP | Your PHP |
|---------|---------|---------|----------|
| CodeIgniter 4.6.4 | 8.1 | 8.4 | 8.2.12 ✅ |
| PhpSpreadsheet 5.3 | 8.1 | 8.4 | 8.2.12 ✅ |
| PHPUnit 10.5 | 8.1 | 8.4 | 8.2.12 ✅ |
| PHPUnit 11.5 | 8.2 | 8.4 | 8.2.12 ✅ |

**All packages are compatible with your PHP version.**

---

## 5. Action Plan

### Priority 1: IMMEDIATE (Today)

#### 1. Fix PHPUnit Security Vulnerability
**Time Estimate:** 10 minutes

```bash
# Update PHPUnit to patched version
composer update phpunit/phpunit --with-dependencies

# Verify the update
composer show phpunit/phpunit | grep versions
```

**Expected Output:**
```
versions : * 10.5.62
```

**Testing:**
```bash
# Run existing tests to ensure compatibility
php vendor/bin/phpunit
```

---

### Priority 2: SHORT TERM (This Week)

#### 2. Update PhpSpreadsheet
**Time Estimate:** 30 minutes (including testing)

```bash
# Update to latest version
composer update phpoffice/phpspreadsheet

# Verify version
composer show phpoffice/phpspreadsheet
```

**Testing Checklist:**
- [ ] Test Excel import for Siswa
- [ ] Test Excel import for Guru  
- [ ] Test Excel import for Jadwal
- [ ] Test template downloads (all 3 types)
- [ ] Verify file format compatibility

**Rollback Plan:**
```bash
# If issues occur
composer require phpoffice/phpspreadsheet "5.3.0"
```

---

### Priority 3: MEDIUM TERM (Next Sprint)

#### 3. Consider PHPUnit 11 Upgrade
**Time Estimate:** 2-4 hours (test updates required)

**Benefits:**
- Latest features and improvements
- Better PHP 8.2+ support
- Performance improvements

**Steps:**
1. Review PHPUnit 11 migration guide
2. Update test suite for compatibility
3. Update CI/CD pipeline
4. Run full test suite

**Command:**
```bash
composer require --dev phpunit/phpunit "^11.5"
```

---

## 6. Dependency Management Best Practices

### Current State Assessment

✅ **Good Practices Observed:**
- Using semantic versioning (`^4.0`, `^8.1`)
- Separating dev and production dependencies
- Lock file committed (composer.lock present)
- Using latest stable CodeIgniter

⚠️ **Areas for Improvement:**
- No automated dependency checking
- Manual security audits
- Update schedule not documented

---

### Recommended Improvements

#### 1. Automated Dependency Checks

**Add to CI/CD Pipeline:**

```yaml
# .github/workflows/dependency-check.yml
name: Dependency Security Check

on:
  schedule:
    - cron: '0 0 * * 1'  # Weekly on Monday
  push:
    branches: [ main ]

jobs:
  security:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install dependencies
        run: composer install
      - name: Security Audit
        run: composer audit
      - name: Check for outdated packages
        run: composer outdated --direct
```

#### 2. Dependency Update Schedule

**Recommended Schedule:**

| Type | Frequency | Priority |
|------|-----------|----------|
| Security patches | Immediately | CRITICAL |
| Patch versions (x.x.X) | Monthly | HIGH |
| Minor versions (x.X.0) | Quarterly | MEDIUM |
| Major versions (X.0.0) | Annually | LOW |

#### 3. Update Testing Protocol

**Before any dependency update:**

1. ✅ Read changelog/release notes
2. ✅ Check breaking changes
3. ✅ Update in development environment
4. ✅ Run full test suite
5. ✅ Test critical user paths
6. ✅ Update staging environment
7. ✅ Run smoke tests
8. ✅ Deploy to production

---

## 7. Composer Configuration Review

### Current composer.json Configuration

```json
{
  "require": {
    "php": "^8.1",
    "codeigniter4/framework": "^4.0",
    "phpoffice/phpspreadsheet": "^5.3"
  },
  "require-dev": {
    "fakerphp/faker": "^1.9",
    "mikey179/vfsstream": "^1.6",
    "phpunit/phpunit": "^10.5.16"
  },
  "config": {
    "optimize-autoloader": true,
    "preferred-install": "dist",
    "sort-packages": true
  }
}
```

### Configuration Assessment: ✅ GOOD

**Strengths:**
- ✅ Semantic versioning used correctly
- ✅ `optimize-autoloader: true` (performance)
- ✅ `preferred-install: dist` (production ready)
- ✅ `sort-packages: true` (clean diffs)

**Recommendations:**

Add security-focused configuration:

```json
{
  "config": {
    "optimize-autoloader": true,
    "preferred-install": "dist",
    "sort-packages": true,
    "audit": {
      "abandoned": "report"
    },
    "allow-plugins": {
      "composer/installers": true
    }
  }
}
```

---

## 8. Security Monitoring Recommendations

### Tools to Consider

#### 1. Composer Audit (Built-in) ✅
```bash
# Already available
composer audit
```

#### 2. Roave Security Advisories
```bash
composer require --dev roave/security-advisories:dev-latest
```
Prevents installation of packages with known vulnerabilities.

#### 3. Local PHP Security Checker
```bash
# Install globally
composer global require enlightn/security-checker

# Run in project
security-checker security:check
```

#### 4. GitHub Dependabot (Recommended)

Create `.github/dependabot.yml`:

```yaml
version: 2
updates:
  - package-ecosystem: "composer"
    directory: "/"
    schedule:
      interval: "weekly"
    open-pull-requests-limit: 5
    labels:
      - "dependencies"
      - "security"
```

Benefits:
- Automatic PRs for updates
- Security vulnerability alerts
- Compatibility testing via CI

---

## 9. Immediate Action Summary

### Commands to Run Now

```bash
# 1. Fix security vulnerability
composer update phpunit/phpunit --with-dependencies

# 2. Update phpspreadsheet
composer update phpoffice/phpspreadsheet

# 3. Verify all updates
composer show --direct

# 4. Run tests
php vendor/bin/phpunit

# 5. Commit changes
git add composer.json composer.lock
git commit -m "Security: Update PHPUnit to 10.5.62 (CVE-2026-24765) and phpspreadsheet to 5.4.0"
```

**Total Time:** ~30 minutes

---

## 10. Long-term Dependency Strategy

### Quarterly Dependency Review

**Q1 2026 Actions (Next Quarter):**
- [ ] Review all dependencies for major updates
- [ ] Consider PHPUnit 11 upgrade
- [ ] Evaluate new CodeIgniter 4 features
- [ ] Update PHP to 8.3 (if not already)

### Version Pinning Strategy

**Current Approach:** Caret (^) versioning - ✅ Good

```json
"codeigniter4/framework": "^4.0"  // Allows 4.0 - 4.999
```

**Alternatives:**

1. **Tilde (~) for stricter control:**
   ```json
   "codeigniter4/framework": "~4.6.0"  // Allows 4.6.0 - 4.6.999
   ```

2. **Exact versions for stability:**
   ```json
   "codeigniter4/framework": "4.6.4"  // Locks to specific version
   ```

**Recommendation:** Keep current caret (^) approach for flexibility.

---

## 11. Testing Strategy Post-Update

### Test Checklist

After updating dependencies, verify:

#### Core Functionality
- [ ] Authentication (login/logout)
- [ ] User management (CRUD operations)
- [ ] Absensi creation and editing
- [ ] Import operations (Excel)
- [ ] Report generation
- [ ] Email sending
- [ ] File uploads

#### Integration Points
- [ ] Database connections
- [ ] Session handling
- [ ] CSRF protection
- [ ] File operations
- [ ] Email delivery

#### Browser Testing
- [ ] Chrome/Edge (Desktop)
- [ ] Mobile browsers
- [ ] Form submissions
- [ ] AJAX operations

---

## 12. Documentation Updates

### Update These Files

1. **README.md** - Update PHP/Composer requirements
2. **DEPLOYMENT_GUIDE.md** - Update dependency installation steps
3. **CHANGELOG.md** - Document dependency updates

```markdown
## [Unreleased]

### Security
- Updated PHPUnit from 10.5.60 to 10.5.62 (CVE-2026-24765)

### Dependencies
- Updated phpoffice/phpspreadsheet from 5.3.0 to 5.4.0
```

---

## 13. Risk Assessment

### Security Risk Matrix

| Vulnerability | Current Risk | After Update | Mitigation |
|---------------|-------------|--------------|------------|
| PHPUnit CVE-2026-24765 | MEDIUM | NONE | Update to 10.5.62 |
| Outdated PhpSpreadsheet | LOW | NONE | Update to 5.4.0 |

### Business Impact

**Before Updates:**
- Dev/Test environment potentially vulnerable
- Missing bug fixes and improvements
- Possible compatibility issues with newer PHP versions

**After Updates:**
- ✅ All known vulnerabilities patched
- ✅ Latest features and bug fixes
- ✅ Better PHP 8.2 compatibility

---

## 14. Conclusion

### Summary

**Current State:**
- 1 critical security vulnerability (PHPUnit)
- 2 packages with minor updates available
- Core framework up-to-date

**Required Actions:**
1. ✅ Update PHPUnit immediately (security fix)
2. ✅ Update PhpSpreadsheet this week
3. 🔵 Consider PHPUnit 11 next sprint

**Overall Security Posture:** GOOD after updates

### Next Steps

1. **Today:** Apply security patches
2. **This Week:** Update PhpSpreadsheet
3. **Next Sprint:** Plan PHPUnit 11 migration
4. **Next Quarter:** Full dependency review

---

## 15. Additional Resources

### Documentation Links

- **Composer Documentation:** https://getcomposer.org/doc/
- **CodeIgniter 4 Upgrade Guide:** https://codeigniter.com/user_guide/installation/upgrading.html
- **PHPUnit Migration Guide:** https://phpunit.de/migration.html
- **PhpSpreadsheet Changelog:** https://github.com/PHPOffice/PhpSpreadsheet/releases

### Security Resources

- **PHP Security Advisories:** https://github.com/FriendsOfPHP/security-advisories
- **CVE Database:** https://cve.mitre.org/
- **Packagist Security Advisories:** https://packagist.org/security-advisories

---

## Report Metadata

**Generated:** January 30, 2026  
**Auditor:** Automated Dependency Check  
**Next Audit:** Weekly (automated) / Quarterly (manual)  
**Report Version:** 1.0

---

*End of Dependency Security Audit Report*
