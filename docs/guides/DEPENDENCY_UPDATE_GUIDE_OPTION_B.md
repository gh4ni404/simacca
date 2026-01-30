# Dependency Update Guide - Option B: Staged Approach
**Approach:** Apply updates in phases with thorough testing  
**Time Required:** 3-5 days (spread over time)  
**Difficulty:** Medium  
**Risk Level:** Very Low (safest approach)

---

## Overview

This guide provides a **cautious, staged approach** to updating dependencies with extensive testing between each phase. Best for:
- Production-critical systems
- Large teams requiring approval workflows
- Risk-averse environments
- Applications with complex business logic

---

## Timeline Overview

```
Day 1: Security Fix (PHPUnit)
├─ Morning: Apply update
├─ Afternoon: Test & monitor
└─ Evening: Review results

Day 2-3: Testing Period
├─ Monitor production
├─ Review error logs
└─ User feedback collection

Day 4: PhpSpreadsheet Update
├─ Morning: Apply update
├─ Afternoon: Test Excel functionality
└─ Evening: Staging deployment

Day 5: Production Deployment
├─ Morning: Final testing
├─ Afternoon: Production deploy
└─ Evening: Monitoring
```

---

## Phase 1: Preparation (Day 0)

### Step 1.1: Environment Setup

**Create Working Branch:**
```bash
# Create feature branch
git checkout -b security/dependency-updates-jan-2026

# Verify branch
git branch --show-current
```

**Document Current State:**
```bash
# Save current versions
composer show --direct > dependency-versions-before.txt

# Save test baseline
php vendor/bin/phpunit > test-results-before.txt 2>&1

# Commit baseline
git add dependency-versions-before.txt test-results-before.txt
git commit -m "docs: Document dependency baseline before updates"
```

### Step 1.2: Stakeholder Communication

**Send Update Notification:**

```markdown
Subject: Scheduled Dependency Updates - Security Fix Required

Team,

We have identified a security vulnerability that requires dependency updates:

**Security Issue:**
- Package: PHPUnit (dev dependency)
- CVE: CVE-2026-24765
- Severity: HIGH
- Impact: Development/test environments only
- Production risk: NONE

**Update Schedule:**
- Day 1 (Monday): PHPUnit security fix
- Day 2-3 (Tue-Wed): Testing & monitoring
- Day 4 (Thursday): PhpSpreadsheet update
- Day 5 (Friday): Production deployment

**Testing Plan:**
- Automated tests will run after each update
- Manual testing of Excel import/export
- Staging deployment before production

**Point of Contact:** [Your Name]
**Questions:** Reply to this email or Slack #dev-team

Thanks,
[Your Name]
```

### Step 1.3: Backup Strategy

**Create Multiple Backup Points:**
```bash
# 1. Tag current production state
git tag -a v1.0-pre-update -m "Before dependency updates Jan 2026"
git push origin v1.0-pre-update

# 2. Backup database (production)
# Run on production server
mysqldump -u username -p simacca_db > backup-$(date +%Y%m%d).sql
# Or use your backup tool

# 3. Backup composer files
cp composer.json composer.json.backup-$(date +%Y%m%d)
cp composer.lock composer.lock.backup-$(date +%Y%m%d)
```

**✅ Checkpoint:** Preparation complete, backups in place

---

## Phase 2: Security Fix - PHPUnit (Day 1)

### Step 2.1: Morning - Apply Update (9:00 AM)

**Update PHPUnit:**
```bash
# Switch to working branch
git checkout security/dependency-updates-jan-2026

# Update PHPUnit only
composer update phpunit/phpunit --with-dependencies

# Verify version
composer show phpunit/phpunit | grep versions
# Expected: * 10.5.62
```

### Step 2.2: Automated Testing (9:15 AM)

**Run Full Test Suite:**
```bash
# Run tests with detailed output
php vendor/bin/phpunit --testdox

# Save results
php vendor/bin/phpunit > test-results-after-phpunit.txt 2>&1

# Compare with baseline
diff test-results-before.txt test-results-after-phpunit.txt
```

**Success Criteria:**
- [ ] All tests pass
- [ ] No new failures
- [ ] Test count unchanged
- [ ] No deprecation warnings

### Step 2.3: Manual Testing (10:00 AM)

**Test Development Environment:**
```bash
# Start dev server
php spark serve

# Open browser to http://localhost:8080
```

**Test Checklist:**
- [ ] Login as admin
- [ ] Login as guru
- [ ] Login as siswa
- [ ] Navigate all main pages
- [ ] Create test absensi
- [ ] Run any dev tools that use tests

**Document Results:**
```bash
# Create test report
cat > test-report-phpunit-day1.md << EOF
# PHPUnit Update Test Report - Day 1

## Test Environment
- Date: $(date)
- PHP Version: $(php --version | head -n 1)
- PHPUnit Version: $(composer show phpunit/phpunit | grep versions)

## Automated Tests
- Status: PASS/FAIL
- Total Tests: ___
- Failures: ___
- Errors: ___

## Manual Tests
- Login: PASS/FAIL
- Navigation: PASS/FAIL
- Core Functions: PASS/FAIL

## Issues Found
- None / [List issues]

## Recommendation
- Proceed to staging: YES/NO
EOF
```

### Step 2.4: Code Review (11:00 AM)

**Create Pull Request:**
```bash
# Commit changes
git add composer.json composer.lock test-report-phpunit-day1.md
git commit -m "security: Update PHPUnit to 10.5.62 (CVE-2026-24765)

- Fixes CVE-2026-24765 (unsafe deserialization)
- All tests passing
- Manual testing complete

Test report: test-report-phpunit-day1.md"

# Push to remote
git push origin security/dependency-updates-jan-2026
```

**Create PR with Template:**
```markdown
## Update Type
- [x] Security Fix
- [ ] Feature Update
- [ ] Bug Fix

## Description
Updates PHPUnit from 10.5.60 to 10.5.62 to fix CVE-2026-24765 
(unsafe deserialization vulnerability).

## Risk Assessment
- **Severity:** HIGH (CVE rating)
- **Project Risk:** LOW (dev dependency only)
- **Production Impact:** NONE

## Testing Completed
- [x] All automated tests passing
- [x] Manual testing in dev environment
- [x] No breaking changes detected

## Deployment Plan
- Day 1: Dev environment (complete)
- Day 2-3: Monitoring & extended testing
- Day 4: Staging deployment

## Rollback Plan
Git tag available: v1.0-pre-update

## Review Checklist
- [ ] Code reviewed
- [ ] Tests passing
- [ ] Documentation updated
- [ ] Security advisory verified fixed
```

### Step 2.5: Afternoon - Staging Deployment (2:00 PM)

**Deploy to Staging:**
```bash
# SSH to staging server
ssh user@staging.example.com

# Navigate to project
cd /var/www/simacca

# Pull changes
git fetch origin
git checkout security/dependency-updates-jan-2026
git pull origin security/dependency-updates-jan-2026

# Install dependencies
composer install --optimize-autoloader

# Clear cache
php spark cache:clear

# Verify installation
composer show phpunit/phpunit
```

**Run Tests on Staging:**
```bash
# On staging server
php vendor/bin/phpunit

# Check for errors
tail -f writable/logs/log-$(date +%Y-%m-%d).php
```

### Step 2.6: Monitoring Setup (3:00 PM)

**Set Up Monitoring:**
```bash
# Create monitoring script
cat > scripts/monitor-staging.sh << 'EOF'
#!/bin/bash
echo "=== Staging Monitoring - $(date) ==="
echo ""
echo "Error Log (last 50 lines):"
tail -n 50 writable/logs/log-$(date +%Y-%m-%d).php | grep -i error
echo ""
echo "Response Time Check:"
curl -o /dev/null -s -w "Time: %{time_total}s\nHTTP Code: %{http_code}\n" https://staging.example.com
echo ""
echo "Memory Usage:"
free -h
EOF

chmod +x scripts/monitor-staging.sh

# Run every hour
# Add to cron: 0 * * * * /path/to/scripts/monitor-staging.sh >> monitor-log.txt
```

**✅ Checkpoint:** PHPUnit updated, deployed to staging, monitoring active

---

## Phase 3: Extended Testing (Day 2-3)

### Day 2: Intensive Monitoring

**Morning Check (9:00 AM):**
```bash
# Review overnight logs
./scripts/monitor-staging.sh

# Check for any errors
grep -i "error\|warning\|exception" writable/logs/log-*.php | tail -n 100

# Verify no test failures
php vendor/bin/phpunit
```

**Test Scenarios:**
```markdown
## Day 2 Testing Checklist

### Automated Testing
- [ ] Run full test suite 3x throughout the day
- [ ] Monitor for flaky tests
- [ ] Check memory usage during tests

### Integration Testing
- [ ] Test all user workflows
- [ ] Verify all imports work
- [ ] Check report generation
- [ ] Test email sending

### Performance Testing
- [ ] Page load times
- [ ] Database query performance
- [ ] Memory consumption
- [ ] CPU usage patterns
```

**Afternoon Review (2:00 PM):**
```bash
# Generate test summary
cat > day2-testing-summary.md << EOF
# Day 2 Testing Summary

## Automated Tests
- Runs: 3
- Total Tests: ___
- Pass Rate: ___%
- Issues: None / [List]

## Manual Testing
- Workflows Tested: ___
- Issues Found: ___
- Severity: Low/Medium/High

## Performance
- Avg Response Time: ___ms
- Memory Usage: ___MB
- Errors: ___

## Recommendation
Continue to Day 3: YES/NO
EOF
```

### Day 3: User Acceptance Testing

**Coordinate Testing:**
```markdown
To: Dev Team, QA Team
Subject: UAT for PHPUnit Update - Day 3

Team,

PHPUnit security update has been on staging for 2 days. 
Please conduct user acceptance testing today.

**Staging URL:** https://staging.example.com
**Test Accounts:** [Provide credentials]

**Focus Areas:**
1. Login and authentication
2. Data entry and validation
3. Report generation
4. Import/export operations

**Report Issues:**
Create a ticket or reply to this email with:
- What you were doing
- What happened
- Expected behavior
- Screenshots if applicable

**Deadline:** Today 5:00 PM

Thanks!
```

**End of Day 3 Decision:**
```bash
# Review all feedback
cat > day3-decision.md << EOF
# Go/No-Go Decision - End of Day 3

## Test Results Summary
- Automated Tests: PASS / FAIL
- Manual Tests: PASS / FAIL  
- UAT Feedback: Positive / Issues Found
- Performance: Acceptable / Needs Review

## Issues Log
Total Issues: ___
- Critical: ___
- High: ___
- Medium: ___
- Low: ___

## Decision
- [x] Proceed with PhpSpreadsheet update (Day 4)
- [ ] Extend testing period
- [ ] Rollback and investigate

## Sign-off
- Tech Lead: ________________
- QA Lead: ________________
- Date: ________________
EOF
```

**✅ Checkpoint:** Extended testing complete, ready for next update

---

## Phase 4: PhpSpreadsheet Update (Day 4)

### Step 4.1: Morning - Apply Update (9:00 AM)

**Update PhpSpreadsheet:**
```bash
# Ensure on working branch
git checkout security/dependency-updates-jan-2026
git pull origin security/dependency-updates-jan-2026

# Update package
composer update phpoffice/phpspreadsheet

# Verify version
composer show phpoffice/phpspreadsheet
# Expected: 5.4.0
```

### Step 4.2: Excel Functionality Testing (9:30 AM)

**Prepare Test Data:**
```bash
# Create test directory
mkdir -p tests/fixtures/excel-test-day4

# Download templates
curl http://localhost:8080/admin/siswa/download-template -o tests/fixtures/template-siswa.xlsx
curl http://localhost:8080/admin/guru/download-template -o tests/fixtures/template-guru.xlsx
curl http://localhost:8080/admin/jadwal/download-template -o tests/fixtures/template-jadwal.xlsx
```

**Test Import - Siswa:**
```markdown
### Siswa Import Test

1. **Download Template**
   - Navigate to: Admin > Siswa > Import
   - Click "Download Template"
   - Result: [ ] PASS [ ] FAIL
   - Notes: _______________

2. **Fill Template**
   - Add 5 test students
   - Include validation edge cases:
     * Valid data
     * Missing required fields
     * Invalid email format
     * Duplicate NIS
   - Result: [ ] Complete

3. **Upload Template**
   - Upload filled template
   - Result: [ ] PASS [ ] FAIL
   - Students Created: ___
   - Errors Shown: ___

4. **Verify Import**
   - Check students in database
   - Verify data accuracy
   - Result: [ ] PASS [ ] FAIL
```

**Test Import - Guru:**
```markdown
### Guru Import Test
[Same structure as Siswa test]
```

**Test Import - Jadwal:**
```markdown
### Jadwal Import Test
[Same structure as Siswa test]
```

### Step 4.3: Export Testing (11:00 AM)

**Test Excel Exports:**
```markdown
### Excel Export Tests

1. **Export Student List**
   - Navigate to: Admin > Siswa
   - Click export button
   - Result: [ ] PASS [ ] FAIL
   - File opens in Excel: [ ] PASS [ ] FAIL

2. **Export Attendance Report**
   - Navigate to: Admin > Laporan
   - Generate report for date range
   - Export to Excel
   - Result: [ ] PASS [ ] FAIL
   - Data accuracy: [ ] PASS [ ] FAIL

3. **Export Teacher Report**
   - Navigate to: Guru > Laporan
   - Export report
   - Result: [ ] PASS [ ] FAIL
```

### Step 4.4: Regression Testing (1:00 PM)

```bash
# Run full test suite again
php vendor/bin/phpunit --testdox

# Run specific Excel-related tests (if you have them)
php vendor/bin/phpunit --filter Excel

# Document results
php vendor/bin/phpunit > test-results-after-phpspreadsheet.txt 2>&1
```

### Step 4.5: Commit and PR Update (2:00 PM)

```bash
# Commit PhpSpreadsheet update
git add composer.json composer.lock
git add tests/fixtures/  # Include test data
git commit -m "chore: Update phpoffice/phpspreadsheet to 5.4.0

- Updates from 5.3.0 to 5.4.0
- All Excel import/export tests passing
- Template generation verified
- No breaking changes

Test report: excel-test-results-day4.txt"

# Push to remote
git push origin security/dependency-updates-jan-2026
```

**Update PR:**
```markdown
## Update - Day 4

### Changes
- [x] PHPUnit 10.5.62 (Day 1)
- [x] PhpSpreadsheet 5.4.0 (Day 4)

### Testing Status
- PHPUnit: 3 days of testing complete ✅
- PhpSpreadsheet: Initial testing complete ✅

### Test Results
- Automated tests: All passing
- Excel import: Siswa ✅, Guru ✅, Jadwal ✅
- Excel export: Reports ✅, Lists ✅
- Template generation: All templates ✅

### Next Steps
- Deploy to staging (today afternoon)
- Extended monitoring (overnight)
- Production deployment (Day 5 morning)
```

### Step 4.6: Staging Deployment (3:00 PM)

```bash
# Deploy to staging
ssh user@staging.example.com
cd /var/www/simacca
git pull origin security/dependency-updates-jan-2026
composer install --optimize-autoloader
php spark cache:clear

# Verify both updates
composer show | grep -E "phpunit|phpspreadsheet"

# Test Excel functionality on staging
# - Download templates
# - Import test data
# - Export reports
```

**✅ Checkpoint:** All updates applied, staging deployment complete

---

## Phase 5: Production Deployment (Day 5)

### Step 5.1: Pre-Deployment (8:00 AM)

**Final Checks:**
```bash
# 1. Verify PR approved
# 2. All tests passing
php vendor/bin/phpunit

# 3. Security audit clean
composer audit

# 4. No outstanding issues
git status

# 5. Staging stable overnight
./scripts/monitor-staging.sh
```

**Pre-Deployment Meeting:**
```markdown
## Go-Live Checklist

### Technical Readiness
- [ ] All tests passing
- [ ] Staging stable for 12+ hours
- [ ] No critical issues reported
- [ ] Rollback plan documented
- [ ] Database backup completed

### Team Readiness
- [ ] Team notified of deployment
- [ ] On-call engineer assigned
- [ ] Rollback contact identified
- [ ] Communication plan ready

### Business Readiness
- [ ] Deployment window confirmed
- [ ] Stakeholders notified
- [ ] Low-traffic time selected
- [ ] Maintenance window (if needed)

### Sign-off
- [ ] Tech Lead approval
- [ ] QA approval
- [ ] Product Owner approval (if required)
```

### Step 5.2: Merge to Main (9:00 AM)

```bash
# Merge PR
# Via GitHub UI or command line:
git checkout main
git pull origin main
git merge --no-ff security/dependency-updates-jan-2026
git push origin main

# Tag release
git tag -a v1.0.1 -m "Security and dependency updates

- PHPUnit 10.5.62 (CVE-2026-24765 fix)
- PhpSpreadsheet 5.4.0

Full changelog in CHANGELOG.md"

git push origin v1.0.1
```

### Step 5.3: Production Deployment (10:00 AM)

**Deployment Steps:**
```bash
# 1. Enable maintenance mode (if needed)
ssh user@production.example.com
cd /var/www/simacca
php spark down

# 2. Backup current state
mysqldump -u user -p simacca_db > backup-pre-update-$(date +%Y%m%d-%H%M).sql
tar -czf code-backup-$(date +%Y%m%d-%H%M).tar.gz .

# 3. Deploy code
git fetch origin
git checkout main
git pull origin main

# 4. Update dependencies (IMPORTANT: Use --no-dev for production)
composer install --no-dev --optimize-autoloader --no-scripts

# 5. Clear all caches
php spark cache:clear
php spark optimize:clear  # If using route caching

# 6. Re-enable site
php spark up

# 7. Verify deployment
composer show | grep -E "phpunit|phpspreadsheet"
```

### Step 5.4: Immediate Verification (10:15 AM)

**Smoke Tests:**
```bash
# 1. Check site loads
curl -I https://simacca.example.com
# Expected: HTTP 200

# 2. Test login
# Via browser: https://simacca.example.com/login

# 3. Check critical paths
# - Dashboard loads
# - Can view data
# - No error pages
```

**Monitor Logs:**
```bash
# Watch for errors in real-time
tail -f writable/logs/log-$(date +%Y-%m-%d).php

# Check for any exceptions
grep -i "exception\|fatal\|error" writable/logs/log-$(date +%Y-%m-%d).php
```

### Step 5.5: Extended Monitoring (10:30 AM - End of Day)

**Monitoring Schedule:**
```markdown
## Day 5 Monitoring Schedule

### 10:30 AM - First Hour Critical
- [ ] Check logs every 15 minutes
- [ ] Monitor error rate
- [ ] Watch for user reports
- [ ] Check site performance

### 12:00 PM - Lunch Review
- [ ] Review morning metrics
- [ ] Check for any issues
- [ ] Update stakeholders

### 3:00 PM - Afternoon Check
- [ ] Test Excel imports
- [ ] Verify reports generate
- [ ] Check user activity

### 5:00 PM - End of Day Review
- [ ] Full metric review
- [ ] Document any issues
- [ ] Update team on status
- [ ] Plan next day monitoring
```

**Metrics to Track:**
```bash
# Create monitoring dashboard data
cat > production-metrics-day5.json << EOF
{
  "deployment_time": "$(date -Iseconds)",
  "response_times": {
    "homepage": "___ms",
    "login": "___ms",
    "dashboard": "___ms"
  },
  "errors": {
    "count": 0,
    "critical": 0,
    "warnings": 0
  },
  "users": {
    "active": ___,
    "issues_reported": 0
  }
}
EOF
```

### Step 5.6: Post-Deployment Communication (6:00 PM)

**Send Success Notification:**
```markdown
Subject: ✅ Dependency Updates Deployed Successfully

Team,

The scheduled dependency updates have been successfully deployed to production.

**Updates Applied:**
- PHPUnit 10.5.62 (security fix for CVE-2026-24765)
- PhpSpreadsheet 5.4.0

**Deployment Timeline:**
- Started: 10:00 AM
- Completed: 10:15 AM
- Downtime: None
- Issues: None

**Monitoring Results:**
- Error rate: Normal
- Performance: Stable
- User reports: No issues

**What's Fixed:**
- Security vulnerability in test framework (dev environment)
- Bug fixes and improvements in Excel processing

**Testing Completed:**
- 5 days of staged testing
- All automated tests passing
- Excel import/export verified
- Production smoke tests complete

**Next Steps:**
- Continued monitoring over weekend
- Regular dependency review scheduled for Q2

Thank you all for your support during this update cycle!

[Your Name]
```

**✅ Checkpoint:** Production deployment complete and stable!

---

## Phase 6: Post-Deployment (Day 6-7)

### Day 6: Weekend Monitoring

**Automated Monitoring:**
```bash
# Set up weekend monitoring cron
# Add to crontab:
0 */4 * * * /path/to/scripts/monitor-production.sh >> /path/to/logs/weekend-monitor.log 2>&1
```

**Weekend Checklist:**
```markdown
## Weekend Monitoring - Day 6-7

### Every 4 Hours
- [ ] Check error logs
- [ ] Verify site accessibility
- [ ] Monitor response times
- [ ] Review user activity

### If Issues Found
1. Assess severity
2. Check if related to updates
3. Decide: Monitor vs Fix vs Rollback
4. Contact team if critical
```

### Day 7: Week Review

**Generate Summary Report:**
```bash
cat > weekly-update-summary.md << EOF
# Dependency Update Summary - Week of $(date +%Y-%m-%d)

## Updates Applied
1. PHPUnit: 10.5.60 → 10.5.62 (Security)
2. PhpSpreadsheet: 5.3.0 → 5.4.0

## Timeline
- Day 0: Preparation
- Day 1: PHPUnit update
- Day 2-3: Extended testing
- Day 4: PhpSpreadsheet update
- Day 5: Production deployment
- Day 6-7: Post-deployment monitoring

## Metrics
- Total downtime: 0 minutes
- Tests run: ___ times
- Issues found: ___
- Issues resolved: ___
- User impact: None

## Lessons Learned
- What went well: ___
- What could improve: ___
- Process changes: ___

## Next Actions
- [ ] Update runbook with learnings
- [ ] Schedule next dependency review
- [ ] Archive test data
- [ ] Close related tickets
EOF
```

**✅ Checkpoint:** Update cycle complete!

---

## Rollback Procedures

### When to Rollback

**Critical Issues:**
- Site completely down
- Data corruption detected
- Security breach
- Critical functionality broken

**Rollback Decision Matrix:**

| Issue Severity | Response Time | Action |
|---------------|---------------|--------|
| Site down | Immediate | Rollback |
| Critical function broken | 30 minutes | Assess → Rollback if unfixable |
| Minor issues | 2 hours | Try fixing first |
| Performance degradation | 4 hours | Monitor → Rollback if worsens |

### Quick Rollback (Emergency)

```bash
# 1. Switch to backup tag
ssh user@production.example.com
cd /var/www/simacca
git checkout v1.0-pre-update

# 2. Restore dependencies
composer install --no-dev --optimize-autoloader

# 3. Clear cache
php spark cache:clear

# 4. Verify rollback
composer show | grep -E "phpunit|phpspreadsheet"
# Should show old versions

# 5. Monitor
tail -f writable/logs/log-$(date +%Y-%m-%d).php
```

### Controlled Rollback (Planned)

```bash
# 1. Enable maintenance mode
php spark down

# 2. Restore database if needed
mysql -u user -p simacca_db < backup-pre-update-YYYYMMDD.sql

# 3. Restore code
git checkout v1.0-pre-update
composer install --no-dev --optimize-autoloader

# 4. Test in maintenance mode
# Run smoke tests

# 5. Re-enable site
php spark up

# 6. Notify team
# Send rollback notification
```

---

## Success Metrics

### Key Performance Indicators

**Technical Metrics:**
- [ ] Zero unplanned downtime
- [ ] All tests passing
- [ ] Security audit clean
- [ ] Performance unchanged or better
- [ ] Error rate unchanged or lower

**Process Metrics:**
- [ ] Timeline met (5-day schedule)
- [ ] All testing phases completed
- [ ] No rollbacks required
- [ ] Documentation updated
- [ ] Team informed throughout

**Business Metrics:**
- [ ] No user complaints
- [ ] No data issues
- [ ] No functionality regressions
- [ ] Stakeholder satisfaction

---

## Final Checklist

### Technical Completion
- [ ] PHPUnit updated to 10.5.62
- [ ] PhpSpreadsheet updated to 5.4.0
- [ ] All tests passing
- [ ] Security audit clean
- [ ] Production stable for 48+ hours

### Documentation
- [ ] CHANGELOG.md updated
- [ ] This guide updated with learnings
- [ ] Test reports archived
- [ ] Metrics documented

### Team Communication
- [ ] All stakeholders notified
- [ ] Success metrics shared
- [ ] Lessons learned documented
- [ ] Next review scheduled

### Administrative
- [ ] Close related tickets/issues
- [ ] Update project tracker
- [ ] Archive backup files
- [ ] Schedule next dependency review

---

## Appendix: Scripts and Templates

### A. Daily Monitoring Script

```bash
#!/bin/bash
# monitor-daily.sh

DATE=$(date +%Y-%m-%d)
TIME=$(date +%H:%M:%S)

echo "=== Daily Monitoring Report - $DATE $TIME ===" | tee -a monitor-daily.log

# Check error logs
echo "\n--- Error Count ---" | tee -a monitor-daily.log
grep -c "ERROR" writable/logs/log-$DATE.php 2>/dev/null | tee -a monitor-daily.log

# Check site response
echo "\n--- Site Health ---" | tee -a monitor-daily.log
curl -o /dev/null -s -w "HTTP: %{http_code}\nTime: %{time_total}s\n" https://simacca.example.com | tee -a monitor-daily.log

# Check dependencies
echo "\n--- Dependency Versions ---" | tee -a monitor-daily.log
composer show | grep -E "phpunit|phpspreadsheet" | tee -a monitor-daily.log

# Send notification if errors
ERROR_COUNT=$(grep -c "ERROR" writable/logs/log-$DATE.php 2>/dev/null)
if [ $ERROR_COUNT -gt 10 ]; then
    echo "WARNING: High error count detected!" | mail -s "SIMACCA Alert" admin@example.com
fi
```

### B. Test Report Template

```markdown
# Test Report - [Phase Name] - [Date]

## Environment
- Date: 
- Time: 
- PHP Version: 
- Server: Development/Staging/Production

## Updates Applied
- [ ] PHPUnit 10.5.60 → 10.5.62
- [ ] PhpSpreadsheet 5.3.0 → 5.4.0

## Automated Tests
- Total Tests: ___
- Passed: ___
- Failed: ___
- Skipped: ___
- Execution Time: ___ seconds

## Manual Tests
| Test Case | Expected | Actual | Status |
|-----------|----------|--------|--------|
| Login | Success | | PASS/FAIL |
| Import Siswa | Data imported | | PASS/FAIL |
| Export Report | File downloads | | PASS/FAIL |

## Issues Found
1. [Issue description]
   - Severity: Critical/High/Medium/Low
   - Reproducible: Yes/No
   - Related to update: Yes/No/Unknown

## Performance
- Page Load Time: ___ms
- API Response: ___ms
- Memory Usage: ___MB

## Recommendation
- [ ] Proceed to next phase
- [ ] Extend testing period
- [ ] Fix issues first
- [ ] Rollback required

## Sign-off
Tester: _______________
Date: _______________
```

---

## Support and Questions

**Internal Resources:**
- Dependency Audit Report: `docs/audit/DEPENDENCY_SECURITY_AUDIT_2026-01-30.md`
- Quick Fix Guide: `docs/guides/DEPENDENCY_UPDATE_GUIDE_OPTION_A.md`
- Project README: `README.md`

**External Resources:**
- PHPUnit Changelog: https://github.com/sebastianbergmann/phpunit/releases
- PhpSpreadsheet Releases: https://github.com/PHPOffice/PhpSpreadsheet/releases
- Composer Documentation: https://getcomposer.org/doc/

**Emergency Contacts:**
- Tech Lead: _______________
- DevOps: _______________
- On-Call: _______________

---

**Last Updated:** January 30, 2026  
**Version:** 1.0  
**Tested On:** SIMACCA v1.0, PHP 8.2.12, Composer 2.9.3  
**Recommended For:** Production systems, risk-averse environments
