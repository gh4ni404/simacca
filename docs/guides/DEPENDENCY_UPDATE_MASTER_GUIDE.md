# Dependency Update Master Guide
**Complete reference for all dependency update approaches**

---

## 📚 Guide Overview

This master guide helps you choose and execute the right dependency update strategy for SIMACCA.

### Available Guides

| Guide | Best For | Time | Risk | Difficulty |
|-------|----------|------|------|------------|
| **[Option A: Quick Fix](DEPENDENCY_UPDATE_GUIDE_OPTION_A.md)** | Urgent updates, small teams | 30-45 min | Low | Easy |
| **[Option B: Staged Approach](DEPENDENCY_UPDATE_GUIDE_OPTION_B.md)** | Production systems, large teams | 3-5 days | Very Low | Medium |
| **[Option C: Full Automation](DEPENDENCY_UPDATE_GUIDE_OPTION_C.md)** | Modern CI/CD, long-term | 4-6 hours setup | Low | Advanced |
| **[Troubleshooting](DEPENDENCY_UPDATE_TROUBLESHOOTING.md)** | When things go wrong | As needed | - | - |

---

## 🎯 Quick Decision Tree

```
START: Do you need to update dependencies?
│
├─→ YES, URGENT SECURITY FIX
│   └─→ Use OPTION A (Quick Fix)
│       Time: 30-45 minutes
│       Guide: DEPENDENCY_UPDATE_GUIDE_OPTION_A.md
│
├─→ YES, but NOT urgent
│   │
│   ├─→ Is this a PRODUCTION system?
│   │   └─→ YES → Use OPTION B (Staged Approach)
│   │       Time: 3-5 days
│   │       Guide: DEPENDENCY_UPDATE_GUIDE_OPTION_B.md
│   │
│   └─→ Is this a DEV/STAGING system?
│       └─→ Use OPTION A (Quick Fix)
│           Time: 30-45 minutes
│           Guide: DEPENDENCY_UPDATE_GUIDE_OPTION_A.md
│
└─→ Want AUTOMATED dependency management
    └─→ Use OPTION C (Full Automation)
        Time: 4-6 hours initial setup
        Guide: DEPENDENCY_UPDATE_GUIDE_OPTION_C.md
```

---

## 📋 Current Dependency Status

### Critical Update Required

**Package:** `phpunit/phpunit`  
**Current:** 10.5.60  
**Required:** 10.5.62+  
**Reason:** CVE-2026-24765 (Security vulnerability)  
**Severity:** HIGH  
**Impact:** Development/test environments only  
**Action:** Update immediately

### Recommended Updates

**Package:** `phpoffice/phpspreadsheet`  
**Current:** 5.3.0  
**Latest:** 5.4.0  
**Reason:** Bug fixes, improvements  
**Severity:** LOW  
**Impact:** Excel import/export functionality  
**Action:** Update this week

### Up-to-Date

✅ `codeigniter4/framework` - 4.6.4 (latest)  
✅ `fakerphp/faker` - 1.24.1 (latest)  
✅ `mikey179/vfsstream` - 1.6.12 (latest)

---

## 🚀 Quick Start by Scenario

### Scenario 1: "Security alert just came in!"

**Recommended:** Option A (Quick Fix)

```bash
# Apply fix immediately
composer update phpunit/phpunit --with-dependencies
php vendor/bin/phpunit
git commit -m "security: Update PHPUnit (CVE-2026-24765)"
git push
```

**Time:** 10 minutes  
**Full Guide:** [Option A](DEPENDENCY_UPDATE_GUIDE_OPTION_A.md)

---

### Scenario 2: "We run a hospital/bank/critical system"

**Recommended:** Option B (Staged Approach)

**Timeline:**
- **Day 1:** Apply security fix to dev/staging
- **Day 2-3:** Extended testing and monitoring
- **Day 4:** Apply additional updates
- **Day 5:** Production deployment

**Full Guide:** [Option B](DEPENDENCY_UPDATE_GUIDE_OPTION_B.md)

---

### Scenario 3: "Set it and forget it"

**Recommended:** Option C (Full Automation)

**Setup:**
1. Configure GitHub Dependabot (1 hour)
2. Set up CI/CD security checks (2 hours)
3. Add Roave Security Advisories (15 minutes)
4. Configure notifications (1 hour)

**Benefits:**
- Automatic security patches
- Weekly dependency checks
- Team notifications
- No manual work

**Full Guide:** [Option C](DEPENDENCY_UPDATE_GUIDE_OPTION_C.md)

---

## 📊 Comparison Matrix

### Time Investment

| Approach | Initial | Per Update | Annual Total |
|----------|---------|------------|--------------|
| Option A | 0 hours | 45 min | ~6 hours |
| Option B | 0 hours | 3-5 days | ~20 days |
| Option C | 4-6 hours | 0 min (auto) | ~6 hours |

### Risk Assessment

| Approach | Development | Staging | Production |
|----------|-------------|---------|------------|
| Option A | ⚠️ Medium | ⚠️ Medium | 🔴 High |
| Option B | ✅ Low | ✅ Low | ✅ Very Low |
| Option C | ✅ Low | ✅ Low | ✅ Low |

### Team Size Recommendation

| Team Size | Recommended Approach |
|-----------|---------------------|
| 1-2 developers | Option A or C |
| 3-5 developers | Option B or C |
| 6+ developers | Option C (automation essential) |

---

## 🎓 Best Practices

### Before Any Update

1. **Read the changelog** - Know what's changing
2. **Check breaking changes** - Avoid surprises
3. **Backup everything** - Git tag, database, code
4. **Create a branch** - Never update directly on main
5. **Test first** - Dev → Staging → Production

### During Updates

1. **Update one at a time** - Easier to troubleshoot
2. **Run tests frequently** - Catch issues early
3. **Document issues** - Help future you
4. **Monitor logs** - Watch for errors
5. **Communicate progress** - Keep team informed

### After Updates

1. **Extended monitoring** - 24-48 hours minimum
2. **Update documentation** - CHANGELOG, README
3. **Share learnings** - Help the team
4. **Plan next review** - Don't forget about dependencies

---

## 🛠️ Essential Commands Reference

### Check for Updates

```bash
# Security vulnerabilities
composer audit

# Outdated packages (direct dependencies)
composer outdated --direct

# All outdated packages
composer outdated

# Specific package info
composer show phpunit/phpunit
```

### Update Packages

```bash
# Update specific package
composer update vendor/package

# Update with dependencies
composer update vendor/package --with-dependencies

# Update all packages
composer update

# Update lock file only (no package changes)
composer update --lock
```

### Verify Updates

```bash
# Validate composer.json
composer validate --strict

# Check installed versions
composer show --direct

# Run security audit
composer audit

# Run tests
php vendor/bin/phpunit
```

### Troubleshooting

```bash
# Clear composer cache
composer clear-cache

# Diagnose issues
composer diagnose

# See why package can't be installed
composer why-not vendor/package version

# Show dependency tree
composer show --tree
```

---

## 📞 When Things Go Wrong

### Quick Fixes

1. **Clear cache and retry:**
   ```bash
   composer clear-cache
   composer install
   ```

2. **Regenerate autoloader:**
   ```bash
   composer dump-autoload --optimize
   ```

3. **Rollback:**
   ```bash
   git checkout HEAD~1 -- composer.json composer.lock
   composer install
   ```

### Need More Help?

👉 **[Complete Troubleshooting Guide](DEPENDENCY_UPDATE_TROUBLESHOOTING.md)**

Covers:
- 10 most common issues and solutions
- Diagnostic scripts
- Rollback procedures
- Emergency contacts

---

## 📝 Implementation Checklists

### Option A Checklist

- [ ] Read [Option A Guide](DEPENDENCY_UPDATE_GUIDE_OPTION_A.md)
- [ ] Backup composer files
- [ ] Update PHPUnit (security fix)
- [ ] Run tests
- [ ] Update PhpSpreadsheet
- [ ] Test Excel functionality
- [ ] Commit changes
- [ ] Deploy to production
- [ ] Monitor for 24 hours

**Estimated Time:** 30-45 minutes

---

### Option B Checklist

#### Day 0: Preparation
- [ ] Read [Option B Guide](DEPENDENCY_UPDATE_GUIDE_OPTION_B.md)
- [ ] Create working branch
- [ ] Document current state
- [ ] Notify stakeholders
- [ ] Create backups

#### Day 1: Security Fix
- [ ] Apply PHPUnit update
- [ ] Run automated tests
- [ ] Manual testing
- [ ] Deploy to staging
- [ ] Set up monitoring

#### Day 2-3: Testing Period
- [ ] Monitor staging environment
- [ ] Run extensive tests
- [ ] Collect user feedback
- [ ] Document issues
- [ ] Make go/no-go decision

#### Day 4: Additional Updates
- [ ] Apply PhpSpreadsheet update
- [ ] Test Excel functionality
- [ ] Regression testing
- [ ] Deploy to staging

#### Day 5: Production
- [ ] Pre-deployment checks
- [ ] Merge to main
- [ ] Deploy to production
- [ ] Immediate verification
- [ ] Extended monitoring
- [ ] Send completion notification

**Estimated Time:** 5 days (spread over time)

---

### Option C Checklist

#### Part 1: GitHub Dependabot (1 hour)
- [ ] Read [Option C Guide](DEPENDENCY_UPDATE_GUIDE_OPTION_C.md)
- [ ] Create `.github/dependabot.yml`
- [ ] Configure auto-merge workflow
- [ ] Test Dependabot PRs
- [ ] Document configuration

#### Part 2: CI/CD Pipeline (2 hours)
- [ ] Create security audit workflow
- [ ] Create dependency check workflow
- [ ] Set up automated testing
- [ ] Test workflows
- [ ] Monitor workflow runs

#### Part 3: Security Advisories (15 min)
- [ ] Install Roave Security Advisories
- [ ] Test blocking of vulnerable packages
- [ ] Commit changes

#### Part 4: Notifications (1 hour)
- [ ] Configure Slack notifications
- [ ] Set up email alerts
- [ ] Add GitHub secrets
- [ ] Test notification delivery

#### Part 5: Verification (30 min)
- [ ] Run all workflows manually
- [ ] Verify Dependabot creates PRs
- [ ] Check notifications work
- [ ] Document for team

**Estimated Time:** 4-6 hours initial setup

---

## 🔄 Maintenance Schedule

### Weekly

- [ ] Review Dependabot PRs (Option C)
- [ ] Check security audit results
- [ ] Monitor production logs

### Monthly

- [ ] Review outdated packages
- [ ] Apply patch updates
- [ ] Update documentation

### Quarterly

- [ ] Full dependency review
- [ ] Consider minor version updates
- [ ] Review and update automation
- [ ] Team retrospective on update process

### Annually

- [ ] Consider major version updates
- [ ] Review update strategy
- [ ] Update guides and documentation
- [ ] PHP version upgrade planning

---

## 📚 Additional Resources

### Documentation

- **Dependency Audit Report:** `docs/audit/DEPENDENCY_SECURITY_AUDIT_2026-01-30.md`
- **Code Quality Report:** `docs/audit/CODE_QUALITY_OPTIMIZATION_REPORT_2026-01-30.md`
- **Composer Documentation:** https://getcomposer.org/doc/
- **PHPUnit Documentation:** https://phpunit.de/
- **PhpSpreadsheet Docs:** https://phpspreadsheet.readthedocs.io/

### Related Guides

- Deployment Guide: `docs/guides/DEPLOYMENT_GUIDE.md`
- Security Guide: `docs/audit/SECURITY_AUDIT_REPORT_2026-01-30.md`
- Performance Guide: `docs/guides/PERFORMANCE_OPTIMIZATION_GUIDE.md`

---

## 🎯 Success Criteria

You've successfully updated dependencies when:

✅ **Technical**
- [ ] `composer audit` shows no vulnerabilities
- [ ] All tests passing
- [ ] Excel import/export working
- [ ] No performance degradation
- [ ] Error logs clean

✅ **Process**
- [ ] Changes committed to git
- [ ] Documentation updated
- [ ] Team informed
- [ ] Monitoring in place

✅ **Business**
- [ ] No user complaints
- [ ] No downtime (or planned)
- [ ] All features working
- [ ] Stakeholders satisfied

---

## 🚦 Traffic Light System

Use this simple system to assess update urgency:

### 🔴 RED - Update Immediately (Option A)
- Critical security vulnerability (CVE)
- Actively exploited vulnerability
- Production system at risk
- Data breach possibility

**Timeline:** Same day  
**Approach:** Option A (Quick Fix)

---

### 🟡 YELLOW - Update Soon (Option A or B)
- Security vulnerability (low severity)
- Important bug fixes
- Compatibility issues
- End-of-life warnings

**Timeline:** This week  
**Approach:** Option A (small teams) or Option B (production)

---

### 🟢 GREEN - Update When Convenient (Any Option)
- Feature additions
- Performance improvements
- Minor bug fixes
- Documentation updates

**Timeline:** This month  
**Approach:** Option B (staged) or Option C (automated)

---

## 📧 Team Communication Templates

### Update Announcement

```markdown
Subject: Scheduled Dependency Updates - [Date]

Team,

We will be updating dependencies for SIMACCA:

**Updates:**
- PHPUnit: 10.5.60 → 10.5.62 (Security fix)
- PhpSpreadsheet: 5.3.0 → 5.4.0 (Bug fixes)

**Timeline:**
- [Date/Time]: Apply updates
- [Date/Time]: Testing period
- [Date/Time]: Production deployment

**Expected Impact:**
- Downtime: None
- Testing required: [Specify]
- User impact: None

**What You Need to Do:**
- Developers: Pull latest changes after deployment
- QA: Test [specific features]
- Users: No action required

**Questions?** Reply to this email or Slack #dev-team

Thanks,
[Your Name]
```

### Update Completion

```markdown
Subject: ✅ Dependency Updates Complete

Team,

Dependency updates have been successfully deployed.

**What Was Updated:**
- PHPUnit: 10.5.60 → 10.5.62 ✓
- PhpSpreadsheet: 5.3.0 → 5.4.0 ✓

**Results:**
- All tests passing ✓
- Security audit clean ✓
- Production stable ✓
- No issues reported ✓

**What's Fixed:**
- Security vulnerability (CVE-2026-24765)
- Excel processing improvements

**Action Items:**
- Developers: `git pull && composer install`
- No other action required

Thanks for your support!
[Your Name]
```

---

## 🔗 Quick Links

### For Developers
- [Option A - Quick Fix](DEPENDENCY_UPDATE_GUIDE_OPTION_A.md)
- [Troubleshooting Guide](DEPENDENCY_UPDATE_TROUBLESHOOTING.md)
- [Security Audit Report](../audit/DEPENDENCY_SECURITY_AUDIT_2026-01-30.md)

### For Team Leads
- [Option B - Staged Approach](DEPENDENCY_UPDATE_GUIDE_OPTION_B.md)
- [Code Quality Report](../audit/CODE_QUALITY_OPTIMIZATION_REPORT_2026-01-30.md)

### For DevOps
- [Option C - Full Automation](DEPENDENCY_UPDATE_GUIDE_OPTION_C.md)
- [CI/CD Configuration Examples](DEPENDENCY_UPDATE_GUIDE_OPTION_C.md#part-2-cicd-security-pipeline)

---

## 📊 Metrics to Track

### Technical Metrics
- Time to apply updates
- Test pass rate
- Rollback frequency
- Vulnerability response time

### Process Metrics
- Update frequency
- Team adherence to process
- Documentation quality
- Communication effectiveness

### Business Metrics
- Downtime during updates
- User-reported issues
- Security posture
- Technical debt reduction

---

## 🎓 Training Resources

### For New Team Members

**Week 1:**
- Read this master guide
- Review current dependency status
- Run `composer outdated` and understand output

**Week 2:**
- Practice with Option A on dev environment
- Review recent update history
- Understand rollback procedures

**Week 3:**
- Shadow experienced team member during update
- Document learnings
- Ask questions

### For Experienced Developers

**Quarterly:**
- Review new Composer features
- Update automation workflows
- Share best practices with team
- Improve documentation

---

## 📅 Update History Template

Keep track of updates:

```markdown
# Dependency Update History

## 2026-01-30 - Security Update
**Updated By:** [Name]
**Approach:** Option A (Quick Fix)
**Packages:**
- phpunit/phpunit: 10.5.60 → 10.5.62
- phpoffice/phpspreadsheet: 5.3.0 → 5.4.0

**Reason:** CVE-2026-24765 security fix
**Issues:** None
**Downtime:** 0 minutes
**Notes:** All tests passed, no issues reported

---

## [Date] - [Update Type]
**Updated By:** 
**Approach:** 
**Packages:**
- 

**Reason:** 
**Issues:** 
**Downtime:** 
**Notes:** 
```

---

## 🏆 Best Practices Summary

### Do's ✅
- ✅ Read changelogs before updating
- ✅ Test in non-production first
- ✅ Update regularly (don't let them pile up)
- ✅ Keep dependencies up-to-date
- ✅ Document everything
- ✅ Communicate with team
- ✅ Monitor after updates
- ✅ Automate where possible

### Don'ts ❌
- ❌ Update directly on production
- ❌ Skip testing
- ❌ Update multiple packages without testing
- ❌ Ignore security vulnerabilities
- ❌ Forget to backup
- ❌ Update without reading changelog
- ❌ Deploy on Friday afternoon
- ❌ Ignore team communication

---

## 🎯 Next Steps

Choose your path:

1. **Need to update NOW?**
   → [Quick Fix Guide (Option A)](DEPENDENCY_UPDATE_GUIDE_OPTION_A.md)

2. **Want to be careful?**
   → [Staged Approach Guide (Option B)](DEPENDENCY_UPDATE_GUIDE_OPTION_B.md)

3. **Want to automate?**
   → [Automation Guide (Option C)](DEPENDENCY_UPDATE_GUIDE_OPTION_C.md)

4. **Having problems?**
   → [Troubleshooting Guide](DEPENDENCY_UPDATE_TROUBLESHOOTING.md)

---

**Last Updated:** January 30, 2026  
**Version:** 1.0  
**Maintainer:** Development Team  
**Next Review:** April 30, 2026

---

*"Keeping dependencies updated is like maintaining a car - regular small updates are better than waiting for a breakdown."*
