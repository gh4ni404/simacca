# Dependency Update Guide - Option A: Quick Fix
**Approach:** Apply all updates immediately (fastest)  
**Time Required:** 30-45 minutes  
**Difficulty:** Easy  
**Risk Level:** Low

---

## Overview

This guide provides a **fast-track approach** to updating all dependencies in one session. Best for:
- Urgent security fixes
- Small teams with quick deployment cycles
- Development/staging environments

---

## Prerequisites

### Before You Start

**Check Your Environment:**
```bash
# Verify Composer is installed
composer --version
# Expected: 2.9.3 or higher

# Verify PHP version
php --version
# Expected: 8.2.12 or higher

# Check current working directory
pwd
# Should be in project root
```

**Backup Your Current State:**
```bash
# Create a backup branch
git checkout -b backup-before-dependency-update

# Or create a backup of composer files
cp composer.json composer.json.backup
cp composer.lock composer.lock.backup
```

---

## Step 1: Security Fix - PHPUnit (10 minutes)

### 1.1 Update PHPUnit

```bash
# Update to patched version
composer update phpunit/phpunit --with-dependencies
```

**Expected Output:**
```
Loading composer repositories with package information
Updating dependencies
Lock file operations: 0 installs, 15 updates, 0 removals
  - Upgrading phpunit/phpunit (10.5.60 => 10.5.62)
  - Upgrading phpunit/php-code-coverage (10.1.16 => 10.1.17)
  ...
Writing lock file
Installing dependencies from lock file
Nothing to install, update or remove
Generating optimized autoload files
```

### 1.2 Verify the Update

```bash
# Check installed version
composer show phpunit/phpunit | grep versions

# Expected output:
# versions : * 10.5.62
```

### 1.3 Test PHPUnit Works

```bash
# Run your test suite
php vendor/bin/phpunit

# Or if you have a test script in composer.json
composer test
```

**Expected:** All existing tests should pass. If tests fail, see troubleshooting section.

### 1.4 Verify Security Fix

```bash
# Run security audit again
composer audit

# Expected: No vulnerabilities found
```

**✅ Checkpoint:** PHPUnit security vulnerability is now fixed!

---

## Step 2: Update PhpSpreadsheet (15 minutes)

### 2.1 Update the Package

```bash
# Update to latest version
composer update phpoffice/phpspreadsheet
```

**Expected Output:**
```
Loading composer repositories with package information
Updating dependencies
Lock file operations: 0 installs, 1 update, 0 removals
  - Upgrading phpoffice/phpspreadsheet (5.3.0 => 5.4.0)
Writing lock file
Installing dependencies from lock file
Package operations: 0 installs, 1 update, 0 removals
  - Downloading phpoffice/phpspreadsheet (5.4.0)
  - Upgrading phpoffice/phpspreadsheet (5.3.0 => 5.4.0): Extracting archive
Generating optimized autoload files
```

### 2.2 Verify the Update

```bash
composer show phpoffice/phpspreadsheet

# Look for: versions : * 5.4.0
```

### 2.3 Test Excel Functionality

Run these tests manually or create test scripts:

#### Test 1: Import Siswa
```bash
# 1. Navigate to Admin > Siswa > Import
# 2. Download template
# 3. Fill with test data
# 4. Upload and verify import
```

**Verification:**
- [ ] Template downloads successfully
- [ ] File uploads without errors
- [ ] Data imports correctly
- [ ] Validation works

#### Test 2: Import Guru
```bash
# 1. Navigate to Admin > Guru > Import
# 2. Download template
# 3. Fill with test data
# 4. Upload and verify import
```

#### Test 3: Import Jadwal
```bash
# 1. Navigate to Admin > Jadwal > Import
# 2. Download template
# 3. Fill with test data
# 4. Upload and verify import
```

**✅ Checkpoint:** PhpSpreadsheet is updated and tested!

---

## Step 3: Final Verification (10 minutes)

### 3.1 Review All Updates

```bash
# Show all direct dependencies with their versions
composer show --direct
```

**Expected Output:**
```
codeigniter4/framework   4.6.4   ✓ Latest
fakerphp/faker          1.24.1  ✓ Latest
mikey179/vfsstream      1.6.12  ✓ Latest
phpoffice/phpspreadsheet 5.4.0  ✓ Updated
phpunit/phpunit         10.5.62 ✓ Security patched
```

### 3.2 Run Full Test Suite

```bash
# Run all tests
php vendor/bin/phpunit

# Check for any failures
echo $?
# Should output: 0 (success)
```

### 3.3 Check for Outdated Packages

```bash
# Verify no more updates needed
composer outdated --direct
```

**Expected:** No red items (security issues), possibly yellow items (major versions).

### 3.4 Final Security Audit

```bash
composer audit
```

**Expected:** "No security vulnerability advisories found"

**✅ Checkpoint:** All updates applied and verified!

---

## Step 4: Commit Changes (5 minutes)

### 4.1 Review Changes

```bash
# See what changed
git diff composer.json
git diff composer.lock
```

### 4.2 Stage Changes

```bash
git add composer.json composer.lock
```

### 4.3 Commit with Descriptive Message

```bash
git commit -m "Security: Update dependencies (CVE-2026-24765)

- Update phpunit/phpunit from 10.5.60 to 10.5.62 (security fix)
- Update phpoffice/phpspreadsheet from 5.3.0 to 5.4.0
- All tests passing
- Security audit clean

Fixes: CVE-2026-24765 (PHPUnit unsafe deserialization)"
```

### 4.4 Push to Remote

```bash
# Push to your branch
git push origin main
# Or your current branch
git push origin $(git branch --show-current)
```

**✅ Checkpoint:** Changes committed and pushed!

---

## Step 5: Deploy to Staging (Optional - 10 minutes)

### 5.1 Deploy Updated Code

```bash
# Using your deployment method (example with git)
ssh user@staging-server
cd /path/to/project
git pull origin main
composer install --no-dev --optimize-autoloader
```

### 5.2 Run Smoke Tests on Staging

**Critical Path Testing:**
- [ ] Login works
- [ ] Dashboard loads
- [ ] Create new absensi
- [ ] Import siswa/guru/jadwal
- [ ] Generate report
- [ ] Download Excel file

### 5.3 Monitor Logs

```bash
# Check for any errors
tail -f /path/to/logs/log-*.php
# Or
tail -f writable/logs/log-*.php
```

**✅ Checkpoint:** Staging deployment successful!

---

## Step 6: Deploy to Production (When Ready)

### 6.1 Schedule Deployment

**Recommended Timing:**
- During low-traffic hours
- Have rollback plan ready
- Team member available for monitoring

### 6.2 Pre-Deployment Checklist

- [ ] All tests passing
- [ ] Staging verified for 24+ hours
- [ ] Database backup created
- [ ] Code backup available
- [ ] Rollback procedure documented

### 6.3 Deploy

```bash
# Your production deployment process
# Example:
ssh user@production-server
cd /path/to/project
git pull origin main
composer install --no-dev --optimize-autoloader --no-scripts
php spark cache:clear
```

### 6.4 Post-Deployment Verification

**Immediate Checks (0-5 minutes):**
- [ ] Site loads
- [ ] Login works
- [ ] No error pages

**Short-term Monitoring (5-30 minutes):**
- [ ] Check error logs
- [ ] Monitor application performance
- [ ] Test core functionality

**Extended Monitoring (1-24 hours):**
- [ ] User-reported issues
- [ ] Error rate monitoring
- [ ] Performance metrics

**✅ Checkpoint:** Production deployment complete!

---

## Completion Checklist

### Updates Applied
- [x] PHPUnit updated to 10.5.62 (security fix)
- [x] PhpSpreadsheet updated to 5.4.0
- [x] All dependencies verified
- [x] Security audit clean

### Testing Completed
- [x] Unit tests passing
- [x] Excel import/export tested
- [x] Manual functionality tests
- [x] Staging deployment verified

### Documentation Updated
- [ ] CHANGELOG.md updated
- [ ] README.md dependencies checked
- [ ] Deployment notes documented

### Deployment
- [ ] Committed to version control
- [ ] Deployed to staging
- [ ] Deployed to production
- [ ] Monitoring in place

---

## Troubleshooting

### Issue: Composer Update Fails

**Error:** "Your requirements could not be resolved..."

**Solution:**
```bash
# Clear composer cache
composer clear-cache

# Try update again
composer update phpunit/phpunit --with-dependencies

# If still failing, check PHP version
php --version
```

### Issue: Tests Fail After Update

**Symptom:** Previously passing tests now fail

**Solution:**
```bash
# Rollback to previous version
git checkout composer.json composer.lock
composer install

# Check what changed
composer show phpunit/phpunit --all | grep changes

# Report issue and wait for fix
```

### Issue: Excel Import Broken

**Symptom:** Excel files won't import/export

**Solution:**
```bash
# Rollback phpspreadsheet only
composer require phpoffice/phpspreadsheet "5.3.0"

# Test again
# If works, report issue to phpspreadsheet project
```

### Issue: Permission Denied

**Error:** Cannot write to composer.lock

**Solution:**
```bash
# Fix permissions
chmod 644 composer.json composer.lock
chmod 755 vendor/

# Try again
composer update
```

---

## Rollback Procedure

### Quick Rollback (Git)

```bash
# Restore from backup branch
git checkout backup-before-dependency-update -- composer.json composer.lock

# Reinstall dependencies
composer install

# Verify rollback
composer show --direct
```

### Manual Rollback

```bash
# Restore backup files
cp composer.json.backup composer.json
cp composer.lock.backup composer.lock

# Reinstall old versions
composer install

# Clear cache
php spark cache:clear
```

---

## Time Tracking

**Actual Time Spent:**
- Step 1 (PHPUnit): _____ minutes
- Step 2 (PhpSpreadsheet): _____ minutes
- Step 3 (Verification): _____ minutes
- Step 4 (Commit): _____ minutes
- Step 5 (Staging): _____ minutes
- Step 6 (Production): _____ minutes

**Total Time:** _____ minutes

**Expected:** 30-45 minutes  
**Variance:** _____ minutes

---

## Success Criteria

✅ **You're done when:**
1. `composer audit` shows no vulnerabilities
2. All tests pass
3. Excel import/export works
4. Changes committed to git
5. Deployed to production (if applicable)

---

## Next Steps

After completing Option A:

1. **Schedule Next Review:** Add calendar reminder for quarterly dependency check
2. **Document Issues:** Note any problems encountered
3. **Update Runbook:** Add any new learnings to this guide
4. **Consider Automation:** See Option C guide for automated dependency management

---

## Support

**If you encounter issues:**
1. Check troubleshooting section above
2. Review error logs in `writable/logs/`
3. Consult Composer documentation
4. Check package-specific issue trackers

**Useful Commands:**
```bash
composer diagnose         # Check Composer setup
composer why-not pkg/name # Check why package can't be installed
composer why pkg/name     # Check why package is installed
composer outdated         # List all outdated packages
```

---

**Last Updated:** January 30, 2026  
**Version:** 1.0  
**Tested On:** SIMACCA v1.0, PHP 8.2.12, Composer 2.9.3
