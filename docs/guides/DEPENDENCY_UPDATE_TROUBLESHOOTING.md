# Dependency Update Troubleshooting Guide
**Complete troubleshooting reference for dependency updates**

---

## Quick Diagnostic Commands

```bash
# Check what's wrong with dependencies
composer diagnose

# Show detailed error info
composer update --verbose

# Clear all caches
composer clear-cache
rm -rf vendor/
composer install

# Verify composer.json syntax
composer validate

# Check PHP version
php --version

# Check required extensions
php -m | grep -E "mbstring|xml|curl|zip"
```

---

## Common Issues and Solutions

### Issue 1: "Your requirements could not be resolved"

**Symptoms:**
```
Your requirements could not be resolved to an installable set of packages.

Problem 1
  - Root composer.json requires phpunit/phpunit ^10.5.62 ...
```

**Causes:**
1. Version conflicts between packages
2. PHP version incompatibility
3. Missing PHP extensions
4. Platform requirements not met

**Solutions:**

#### Solution 1A: Check PHP Version
```bash
# Check current PHP version
php --version

# If too old, update PHP
# Ubuntu/Debian:
sudo apt-get update
sudo apt-get install php8.2

# Check composer's PHP requirement
composer show --platform
```

#### Solution 1B: Ignore Platform Requirements (Temporary)
```bash
# Only for testing - NOT for production!
composer update --ignore-platform-reqs

# Better: Set platform config
composer config platform.php 8.2.12
```

#### Solution 1C: Resolve Conflicts
```bash
# See why a package can't be installed
composer why-not phpunit/phpunit 10.5.62

# See what requires a package
composer why phpunit/phpunit

# Show dependency tree
composer depends phpunit/phpunit
```

---

### Issue 2: Tests Fail After Update

**Symptoms:**
```
PHPUnit 10.5.62 by Sebastian Bergmann

...F..

FAILURES!
Tests: 50, Assertions: 120, Failures: 1
```

**Diagnosis:**
```bash
# Run specific failing test
php vendor/bin/phpunit --filter=testMethodName

# Run with debug output
php vendor/bin/phpunit --testdox --verbose

# Check for deprecation warnings
php vendor/bin/phpunit 2>&1 | grep -i deprecat
```

**Common Causes:**

#### Cause 2A: Breaking Changes in PHPUnit
**Solution:**
```php
// Old PHPUnit 9 syntax
$this->expectException(Exception::class);

// PHPUnit 10 might require:
$this->expectException(\Exception::class);

// Check PHPUnit migration guide
// https://phpunit.de/migration.html
```

#### Cause 2B: Changed Method Signatures
**Solution:**
```bash
# Find changed signatures
git diff composer.lock | grep "phpunit"

# Review package changelog
composer show phpunit/phpunit --all | grep -A 20 "versions"
```

#### Cause 2C: Test Pollution
**Solution:**
```bash
# Run tests in random order
php vendor/bin/phpunit --order-rand

# Run tests in isolation
php vendor/bin/phpunit --process-isolation
```

---

### Issue 3: Excel Import/Export Broken

**Symptoms:**
- Excel files won't open
- Import returns errors
- Template download fails

**Diagnosis:**
```bash
# Check PhpSpreadsheet version
composer show phpoffice/phpspreadsheet

# Test Excel creation
php -r "
require 'vendor/autoload.php';
\$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
\$sheet = \$spreadsheet->getActiveSheet();
\$sheet->setCellValue('A1', 'Test');
\$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx(\$spreadsheet);
\$writer->save('test.xlsx');
echo 'Success';
"
```

**Solutions:**

#### Solution 3A: Missing PHP Extensions
```bash
# Check required extensions
php -m | grep -E "zip|xml|gd"

# Install missing extensions (Ubuntu/Debian)
sudo apt-get install php8.2-zip php8.2-xml php8.2-gd

# Restart web server
sudo systemctl restart apache2
# or
sudo systemctl restart php8.2-fpm
```

#### Solution 3B: Memory Limit
```bash
# Check memory limit
php -i | grep memory_limit

# Increase in php.ini
memory_limit = 512M

# Or in code (temporary)
ini_set('memory_limit', '512M');
```

#### Solution 3C: File Permissions
```bash
# Check writable directories
ls -la writable/uploads/
ls -la writable/cache/

# Fix permissions
chmod -R 775 writable/
chown -R www-data:www-data writable/
```

#### Solution 3D: PhpSpreadsheet Breaking Changes
```php
// Check changelog
// https://github.com/PHPOffice/PhpSpreadsheet/releases

// Common breaking change: Cell addressing
// Old (5.3.0)
$sheet->getCell('A1')->setValue('Test');

// New (5.4.0) - usually same, but check docs
$sheet->setCellValue('A1', 'Test');
```

---

### Issue 4: Composer Install Fails on Production

**Symptoms:**
```bash
composer install --no-dev
# Error: Package not found or timeout
```

**Solutions:**

#### Solution 4A: Network/Timeout Issues
```bash
# Increase timeout
export COMPOSER_PROCESS_TIMEOUT=600
composer install --no-dev

# Use different packagist mirror
composer config repo.packagist composer https://packagist.jp

# Clear cache and retry
composer clear-cache
composer install --no-dev
```

#### Solution 4B: Authentication Required
```bash
# If using private repositories
composer config --global --auth github-oauth.github.com YOUR_TOKEN

# Check configured authentication
composer config --global --list
```

#### Solution 4C: Composer Version Too Old
```bash
# Update composer itself
composer self-update

# Check version
composer --version
# Should be 2.x or higher
```

---

### Issue 5: "Class not found" After Update

**Symptoms:**
```
Fatal error: Class 'Vendor\Package\ClassName' not found
```

**Solutions:**

#### Solution 5A: Regenerate Autoloader
```bash
# Dump autoloader with optimization
composer dump-autoload --optimize

# In production
composer install --no-dev --optimize-autoloader

# Clear CodeIgniter cache
php spark cache:clear
```

#### Solution 5B: Namespace Changes
```bash
# Find where class moved
composer show vendor/package --all | grep -i "autoload"

# Update imports in your code
# Old:
use OldNamespace\ClassName;

# New:
use NewNamespace\ClassName;
```

#### Solution 5C: Class Renamed/Removed
```bash
# Check package changelog
composer show vendor/package --all

# Search for migration guide
# Visit package GitHub repository
```

---

### Issue 6: Security Audit Still Shows Vulnerabilities

**Symptoms:**
```bash
composer audit
# Still shows vulnerabilities after update
```

**Solutions:**

#### Solution 6A: Update Transitive Dependencies
```bash
# Update all dependencies (not just direct)
composer update --with-all-dependencies

# Force update specific package
composer update vendor/package --with-dependencies
```

#### Solution 6B: Check composer.lock is Updated
```bash
# Verify lock file has changes
git diff composer.lock

# If no changes, lock file wasn't updated
# Force update
rm composer.lock
composer install
```

#### Solution 6C: Cache Issues
```bash
# Clear composer cache
composer clear-cache

# Re-run audit
composer audit

# Run with verbose to see details
composer audit --format=json
```

---

### Issue 7: Dependabot PRs Not Creating

**Symptoms:**
- No PRs from Dependabot
- Configuration seems correct

**Solutions:**

#### Solution 7A: Check Dependabot Status
```bash
# Visit: https://github.com/YOUR_REPO/network/updates

# Check for errors in Dependabot logs
# Settings > Security > Dependabot alerts
```

#### Solution 7B: Validate Configuration
```yaml
# Validate dependabot.yml syntax
# Use: https://dependabot.com/docs/config-file/validator/

# Check file location
# Must be: .github/dependabot.yml
# NOT: .github/workflows/dependabot.yml
```

#### Solution 7C: Repository Permissions
```bash
# Ensure Dependabot has access
# Settings > Security > Enable Dependabot
# Settings > Actions > Allow GitHub Actions to create PRs
```

---

### Issue 8: Performance Degradation After Update

**Symptoms:**
- Slow page loads
- High memory usage
- Increased CPU usage

**Diagnosis:**
```bash
# Profile a page load
php -d xdebug.mode=profile spark serve

# Check memory usage
php -r "echo memory_get_peak_usage(true)/1024/1024 . ' MB';"

# Monitor logs for slow queries
tail -f writable/logs/log-$(date +%Y-%m-%d).php | grep -i "slow"
```

**Solutions:**

#### Solution 8A: Optimize Autoloader
```bash
# Use optimized autoloader in production
composer install --no-dev --optimize-autoloader --classmap-authoritative

# Enable opcache
# In php.ini:
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

#### Solution 8B: Check for N+1 Queries
```php
// Enable query log temporarily
\Config\Database::connect()->query('SET profiling = 1');

// Check slow queries
$queries = \Config\Database::connect()->query('SHOW PROFILES');
```

#### Solution 8C: Revert and Investigate
```bash
# If severe performance issue, rollback
git revert HEAD
composer install

# Compare performance metrics
# Identify which package caused issue
```

---

### Issue 9: Conflicts Between Dependencies

**Symptoms:**
```
Your requirements could not be resolved to an installable set of packages.

Package A requires B ^2.0
Package C requires B ^1.0
```

**Solutions:**

#### Solution 9A: Check Compatibility Matrix
```bash
# Show what requires each version
composer why-not vendor/package-b 2.0
composer why vendor/package-b

# Create compatibility matrix
composer show --tree
```

#### Solution 9B: Update Conflicting Package
```bash
# Update package C to version that supports B ^2.0
composer update vendor/package-c

# Or wait for package maintainer to update
# Check package repository for issues/roadmap
```

#### Solution 9C: Use Aliases (Advanced)
```json
// In composer.json (use with caution)
{
  "require": {
    "vendor/package-b": "2.0 as 1.0"
  }
}
```

---

### Issue 10: Git Merge Conflicts in composer.lock

**Symptoms:**
```bash
git merge feature-branch
# CONFLICT (content): Merge conflict in composer.lock
```

**Solutions:**

#### Solution 10A: Regenerate Lock File
```bash
# Take their version (or yours)
git checkout --theirs composer.lock
# or
git checkout --ours composer.lock

# Regenerate based on composer.json
composer update --lock

# Add and commit
git add composer.lock
git commit -m "resolve: Regenerate composer.lock"
```

#### Solution 10B: Manual Conflict Resolution (Not Recommended)
```bash
# Only if above doesn't work
# Edit composer.lock to resolve conflicts
# Then:
composer validate
composer install
```

---

## Rollback Procedures

### Quick Rollback (Emergency)

```bash
# Method 1: Git revert
git revert HEAD
composer install

# Method 2: Restore from backup
cp composer.json.backup composer.json
cp composer.lock.backup composer.lock
composer install

# Method 3: Checkout previous commit
git checkout HEAD~1 -- composer.json composer.lock
composer install
```

### Staged Rollback (With Verification)

```bash
# 1. Create rollback branch
git checkout -b rollback-dependency-update

# 2. Revert commits
git revert abc123def  # Commit hash of update

# 3. Test rollback
composer install
php vendor/bin/phpunit

# 4. If tests pass, merge
git checkout main
git merge rollback-dependency-update

# 5. Deploy
# ... your deployment process
```

---

## Prevention Checklist

### Before Updating

- [ ] Read package changelog
- [ ] Check for breaking changes
- [ ] Review migration guide
- [ ] Backup composer files
- [ ] Create git branch
- [ ] Run tests before update

### During Update

- [ ] Update one package at a time (if possible)
- [ ] Run tests after each update
- [ ] Check error logs
- [ ] Test critical functionality
- [ ] Document any issues

### After Update

- [ ] Run full test suite
- [ ] Test in staging environment
- [ ] Monitor production logs
- [ ] Update documentation
- [ ] Communicate changes to team

---

## Diagnostic Scripts

### Script 1: Dependency Health Check

```bash
#!/bin/bash
# dependency-health-check.sh

echo "=== Dependency Health Check ==="
echo ""

echo "1. Composer Version:"
composer --version
echo ""

echo "2. PHP Version:"
php --version
echo ""

echo "3. Required Extensions:"
for ext in mbstring xml curl zip mysqli; do
    if php -m | grep -q "^$ext$"; then
        echo "  ✓ $ext"
    else
        echo "  ✗ $ext (missing)"
    fi
done
echo ""

echo "4. Outdated Packages:"
composer outdated --direct
echo ""

echo "5. Security Vulnerabilities:"
composer audit
echo ""

echo "6. Disk Space:"
df -h .
echo ""

echo "7. Memory Limit:"
php -i | grep memory_limit
echo ""

echo "Health check complete!"
```

### Script 2: Update Validation

```bash
#!/bin/bash
# validate-update.sh

echo "=== Validating Dependency Update ==="
echo ""

# Validate composer.json
echo "1. Validating composer.json..."
composer validate --strict || exit 1

# Install dependencies
echo "2. Installing dependencies..."
composer install || exit 1

# Run tests
echo "3. Running tests..."
php vendor/bin/phpunit || exit 1

# Check security
echo "4. Security audit..."
composer audit || exit 1

# Test critical functionality
echo "5. Testing Excel functionality..."
php -r "
require 'vendor/autoload.php';
\$s = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
\$s->getActiveSheet()->setCellValue('A1', 'Test');
echo 'Excel: OK\n';
"

echo ""
echo "✅ All validation checks passed!"
```

---

## Getting Help

### Information to Collect

When asking for help, provide:

1. **Environment Info:**
```bash
php --version
composer --version
php -m  # PHP extensions
```

2. **Error Output:**
```bash
composer update --verbose 2>&1 | tee error.log
```

3. **Dependency Tree:**
```bash
composer show --tree > dependency-tree.txt
```

4. **Current Versions:**
```bash
composer show --direct
```

5. **Platform Info:**
```bash
composer show --platform
```

### Where to Get Help

1. **Composer Issues:**
   - https://github.com/composer/composer/issues
   - https://stackoverflow.com/questions/tagged/composer-php

2. **PHPUnit Issues:**
   - https://github.com/sebastianbergmann/phpunit/issues
   - PHPUnit migration guide

3. **PhpSpreadsheet Issues:**
   - https://github.com/PHPOffice/PhpSpreadsheet/issues
   - Documentation: https://phpspreadsheet.readthedocs.io/

4. **CodeIgniter Issues:**
   - https://forum.codeigniter.com/
   - https://github.com/codeigniter4/CodeIgniter4/issues

---

## Emergency Contact Template

```markdown
Subject: URGENT: Dependency Update Issue - Production Down

**Environment:**
- PHP: [version]
- Composer: [version]
- Server: [production/staging/development]

**Issue:**
[Brief description of problem]

**What Happened:**
[Steps that led to the issue]

**Error Messages:**
```
[Paste error output]
```

**What We've Tried:**
- [Action 1]
- [Action 2]

**Impact:**
- Users affected: [number/percentage]
- Services down: [list]
- Data loss: [yes/no]

**Immediate Need:**
[Rollback? Emergency fix? Support?]

**Attachments:**
- error.log
- composer.json
- composer.lock
```

---

**Last Updated:** January 30, 2026  
**Version:** 1.0  
**Maintainer:** Development Team
