# Dependency Update Guide - Option C: Full Automation Setup
**Approach:** Automate dependency management and security monitoring  
**Time Required:** 4-6 hours initial setup, then automated  
**Difficulty:** Advanced  
**Risk Level:** Low (with proper configuration)

---

## Overview

This guide provides a **complete automation setup** for dependency management. Best for:
- Modern development teams using CI/CD
- Projects hosted on GitHub/GitLab
- Teams wanting proactive security alerts
- Long-term maintenance efficiency

---

## What You'll Set Up

### Automated Systems
1. **GitHub Dependabot** - Automatic dependency updates
2. **Composer Audit** - Security vulnerability scanning
3. **CI/CD Pipeline** - Automated testing on updates
4. **Roave Security Advisories** - Prevent vulnerable installs
5. **Notification System** - Slack/Email alerts
6. **Scheduled Reviews** - Quarterly automated checks

### Benefits
- ✅ Automatic security patches
- ✅ Proactive vulnerability detection
- ✅ Reduced manual maintenance
- ✅ Always up-to-date dependencies
- ✅ Team-wide visibility

---

## Part 1: GitHub Dependabot Setup

### Step 1.1: Create Dependabot Configuration

**Create file:** `.github/dependabot.yml`

```yaml
# Dependabot configuration for SIMACCA
version: 2
updates:
  # Composer dependencies
  - package-ecosystem: "composer"
    directory: "/"
    schedule:
      interval: "weekly"
      day: "monday"
      time: "09:00"
      timezone: "Asia/Jakarta"
    
    # PR limits and behavior
    open-pull-requests-limit: 5
    
    # Automatically merge security patches
    # (requires additional GitHub Actions setup)
    labels:
      - "dependencies"
      - "composer"
      - "automated"
    
    # Reviewers for dependency PRs
    reviewers:
      - "tech-lead"
      - "senior-dev"
    
    # Assignees
    assignees:
      - "devops-team"
    
    # Commit message prefix
    commit-message:
      prefix: "chore(deps)"
      prefix-development: "chore(dev-deps)"
      include: "scope"
    
    # Version update strategy
    # - auto = determine automatically
    # - increase = only newer versions
    # - widen = increase allowed version range
    versioning-strategy: increase
    
    # Group dependency updates
    groups:
      # Group all patch updates
      patch-updates:
        patterns:
          - "*"
        update-types:
          - "patch"
      
      # Group CodeIgniter framework separately
      codeigniter:
        patterns:
          - "codeigniter4/*"
        update-types:
          - "minor"
          - "patch"
      
      # Group dev dependencies
      development:
        dependency-type: "development"
        update-types:
          - "minor"
          - "patch"
    
    # Ignore specific updates
    ignore:
      # Don't update to major versions automatically
      - dependency-name: "*"
        update-types: ["version-update:semver-major"]
      
      # Specific package to ignore (example)
      # - dependency-name: "vendor/package"
      #   versions: ["2.x"]

  # GitHub Actions dependencies (if you use them)
  - package-ecosystem: "github-actions"
    directory: "/"
    schedule:
      interval: "monthly"
    labels:
      - "dependencies"
      - "github-actions"
```

### Step 1.2: Configure Auto-Merge for Security Updates

**Create file:** `.github/workflows/auto-merge-dependabot.yml`

```yaml
name: Auto-merge Dependabot Security Updates

on:
  pull_request:
    types: [opened, synchronize, reopened]

permissions:
  contents: write
  pull-requests: write

jobs:
  auto-merge:
    runs-on: ubuntu-latest
    if: github.actor == 'dependabot[bot]'
    
    steps:
      - name: Dependabot metadata
        id: metadata
        uses: dependabot/fetch-metadata@v1
        with:
          github-token: "${{ secrets.GITHUB_TOKEN }}"
      
      - name: Auto-merge security patches
        if: steps.metadata.outputs.update-type == 'version-update:semver-patch'
        run: gh pr merge --auto --squash "$PR_URL"
        env:
          PR_URL: ${{ github.event.pull_request.html_url }}
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
      
      - name: Comment on PR
        if: steps.metadata.outputs.update-type == 'version-update:semver-minor'
        uses: actions/github-script@v6
        with:
          script: |
            github.rest.issues.createComment({
              issue_number: context.issue.number,
              owner: context.repo.owner,
              repo: context.repo.repo,
              body: '⚠️ Minor version update detected. Please review before merging.'
            })
```

### Step 1.3: Commit and Enable

```bash
# Create the files
mkdir -p .github/workflows
# (files created above)

# Commit
git add .github/dependabot.yml
git add .github/workflows/auto-merge-dependabot.yml
git commit -m "ci: Add Dependabot automation

- Weekly dependency checks
- Auto-merge security patches
- Grouped updates by type
- PR assignment and labeling"

# Push
git push origin main

# Dependabot will start automatically!
```

**✅ Checkpoint:** Dependabot now watches for updates

---

## Part 2: CI/CD Security Pipeline

### Step 2.1: Create Security Check Workflow

**Create file:** `.github/workflows/security-audit.yml`

```yaml
name: Security Audit

on:
  # Run on every push to main
  push:
    branches: [ main, develop ]
  
  # Run on all PRs
  pull_request:
    branches: [ main, develop ]
  
  # Weekly scheduled scan
  schedule:
    - cron: '0 0 * * 1'  # Monday at midnight
  
  # Manual trigger
  workflow_dispatch:

jobs:
  security-audit:
    runs-on: ubuntu-latest
    name: Security Vulnerability Scan
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, xml, curl, mysqli
          coverage: none
      
      - name: Validate composer.json
        run: composer validate --strict
      
      - name: Cache Composer packages
        uses: actions/cache@v3
        with:
          path: vendor
          key: ${{ runner.os }}-php-${{ hashFiles('**/composer.lock') }}
          restore-keys: |
            ${{ runner.os }}-php-
      
      - name: Install dependencies
        run: composer install --prefer-dist --no-progress
      
      - name: Run Composer Audit
        id: audit
        run: |
          composer audit --format=json > audit-results.json
          cat audit-results.json
        continue-on-error: true
      
      - name: Check for vulnerabilities
        run: |
          if [ -s audit-results.json ]; then
            VULN_COUNT=$(jq '.advisories | length' audit-results.json)
            if [ "$VULN_COUNT" -gt 0 ]; then
              echo "❌ Found $VULN_COUNT security vulnerabilities!"
              jq -r '.advisories[] | "Package: \(.packageName)\nCVE: \(.cve)\nSeverity: \(.severity)"' audit-results.json
              exit 1
            fi
          fi
          echo "✅ No vulnerabilities found!"
      
      - name: Upload audit results
        if: always()
        uses: actions/upload-artifact@v3
        with:
          name: security-audit-results
          path: audit-results.json
      
      - name: Comment PR with results
        if: github.event_name == 'pull_request' && failure()
        uses: actions/github-script@v6
        with:
          script: |
            const fs = require('fs');
            const audit = JSON.parse(fs.readFileSync('audit-results.json', 'utf8'));
            const vulnCount = Object.keys(audit.advisories).length;
            
            const comment = `## 🚨 Security Audit Failed
            
            Found ${vulnCount} security vulnerabilities in dependencies.
            
            Please review and update affected packages before merging.
            
            See workflow artifacts for detailed report.`;
            
            github.rest.issues.createComment({
              issue_number: context.issue.number,
              owner: context.repo.owner,
              repo: context.repo.repo,
              body: comment
            });
```

### Step 2.2: Create Dependency Check Workflow

**Create file:** `.github/workflows/dependency-check.yml`

```yaml
name: Dependency Check

on:
  schedule:
    - cron: '0 9 * * 1'  # Every Monday at 9 AM
  workflow_dispatch:

jobs:
  check-outdated:
    runs-on: ubuntu-latest
    name: Check for Outdated Dependencies
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      
      - name: Install dependencies
        run: composer install --no-progress
      
      - name: Check for outdated packages
        id: outdated
        run: |
          composer outdated --direct --format=json > outdated.json
          cat outdated.json
        continue-on-error: true
      
      - name: Generate report
        run: |
          echo "# Dependency Status Report" > report.md
          echo "Generated: $(date)" >> report.md
          echo "" >> report.md
          
          if [ -s outdated.json ]; then
            echo "## Outdated Packages" >> report.md
            jq -r '.installed[] | "- **\(.name)**: \(.version) → \(.latest)"' outdated.json >> report.md
          else
            echo "✅ All dependencies up to date!" >> report.md
          fi
      
      - name: Create Issue if Outdated
        if: steps.outdated.outcome == 'success'
        uses: actions/github-script@v6
        with:
          script: |
            const fs = require('fs');
            const report = fs.readFileSync('report.md', 'utf8');
            
            // Check if similar issue exists
            const issues = await github.rest.issues.listForRepo({
              owner: context.repo.owner,
              repo: context.repo.repo,
              state: 'open',
              labels: 'dependencies,automated'
            });
            
            const existingIssue = issues.data.find(issue => 
              issue.title.includes('Weekly Dependency Check')
            );
            
            if (existingIssue) {
              // Update existing issue
              await github.rest.issues.createComment({
                owner: context.repo.owner,
                repo: context.repo.repo,
                issue_number: existingIssue.number,
                body: `## Update: ${new Date().toLocaleDateString()}\n\n${report}`
              });
            } else {
              // Create new issue
              await github.rest.issues.create({
                owner: context.repo.owner,
                repo: context.repo.repo,
                title: `Weekly Dependency Check - ${new Date().toLocaleDateString()}`,
                body: report,
                labels: ['dependencies', 'automated', 'maintenance']
              });
            }
```

---

## Part 3: Roave Security Advisories

### Step 3.1: Install Roave Security Advisories

```bash
# This package will prevent installation of packages with known vulnerabilities
composer require --dev roave/security-advisories:dev-latest
```

**What this does:**
- Conflicts with any package version that has known security vulnerabilities
- Updates automatically when you run `composer update`
- Prevents accidental installation of vulnerable packages

### Step 3.2: Test It Works

```bash
# Try to install a vulnerable package (should fail)
composer require phpunit/phpunit "9.0.0"

# Expected error:
# roave/security-advisories prevents installation of vulnerable packages
```

### Step 3.3: Commit

```bash
git add composer.json composer.lock
git commit -m "security: Add Roave Security Advisories

Prevents installation of packages with known vulnerabilities"
git push origin main
```

**✅ Checkpoint:** Automatic vulnerability prevention active

---

## Part 4: Notification System

### Step 4.1: Slack Notifications

**Create file:** `.github/workflows/notify-slack.yml`

```yaml
name: Slack Notifications

on:
  workflow_run:
    workflows: ["Security Audit"]
    types: [completed]

jobs:
  notify:
    runs-on: ubuntu-latest
    if: ${{ github.event.workflow_run.conclusion == 'failure' }}
    
    steps:
      - name: Send Slack notification
        uses: slackapi/slack-github-action@v1
        with:
          payload: |
            {
              "text": "🚨 Security Audit Failed",
              "blocks": [
                {
                  "type": "header",
                  "text": {
                    "type": "plain_text",
                    "text": "🚨 Security Audit Failed"
                  }
                },
                {
                  "type": "section",
                  "text": {
                    "type": "mrkdwn",
                    "text": "*Repository:* ${{ github.repository }}\n*Branch:* ${{ github.ref_name }}\n*Workflow:* Security Audit"
                  }
                },
                {
                  "type": "actions",
                  "elements": [
                    {
                      "type": "button",
                      "text": {
                        "type": "plain_text",
                        "text": "View Workflow"
                      },
                      "url": "${{ github.event.workflow_run.html_url }}"
                    }
                  ]
                }
              ]
            }
        env:
          SLACK_WEBHOOK_URL: ${{ secrets.SLACK_WEBHOOK_URL }}
          SLACK_WEBHOOK_TYPE: INCOMING_WEBHOOK
```

### Step 4.2: Email Notifications

**Add to existing workflow:**

```yaml
      - name: Send email notification
        if: failure()
        uses: dawidd6/action-send-mail@v3
        with:
          server_address: smtp.gmail.com
          server_port: 465
          username: ${{ secrets.EMAIL_USERNAME }}
          password: ${{ secrets.EMAIL_PASSWORD }}
          subject: "🚨 SIMACCA Security Alert"
          to: dev-team@example.com
          from: ci-bot@example.com
          body: |
            Security audit detected vulnerabilities in SIMACCA.
            
            Please review: ${{ github.server_url }}/${{ github.repository }}/actions/runs/${{ github.run_id }}
```

### Step 4.3: Configure Secrets

```bash
# Add secrets in GitHub Settings > Secrets and variables > Actions

# Required secrets:
# - SLACK_WEBHOOK_URL
# - EMAIL_USERNAME
# - EMAIL_PASSWORD
```

**✅ Checkpoint:** Team notifications configured

---

## Part 5: Automated Testing on Updates

### Step 5.1: Create Comprehensive Test Workflow

**Create file:** `.github/workflows/test-dependencies.yml`

```yaml
name: Test Dependencies

on:
  pull_request:
    paths:
      - 'composer.json'
      - 'composer.lock'

jobs:
  test:
    runs-on: ubuntu-latest
    name: Test Dependency Updates
    
    strategy:
      matrix:
        php-version: ['8.1', '8.2']
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v3
      
      - name: Setup PHP ${{ matrix.php-version }}
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php-version }}
          extensions: mbstring, xml, curl, mysqli
          coverage: xdebug
      
      - name: Install dependencies
        run: composer install --prefer-dist --no-progress
      
      - name: Run PHPUnit tests
        run: vendor/bin/phpunit --coverage-text
      
      - name: Test Excel functionality
        run: |
          php spark test:excel-import
          php spark test:excel-export
        continue-on-error: true
```

**✅ Checkpoint:** Automated testing on all dependency changes

---

I'll continue with the remaining parts in the next response to keep it manageable.

Would you like me to continue with:
- Part 6: Monitoring Dashboard
- Part 7: Quarterly Review Automation  
- Part 8: Complete Setup Verification
- Final configuration and troubleshooting?