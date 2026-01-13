#!/usr/bin/env php
<?php
/**
 * Production Deployment Script
 * 
 * Run this script to prepare application for production deployment
 * Usage: php deploy.php
 */

// Colors for CLI output
define('COLOR_RESET', "\033[0m");
define('COLOR_RED', "\033[31m");
define('COLOR_GREEN', "\033[32m");
define('COLOR_YELLOW', "\033[33m");
define('COLOR_CYAN', "\033[36m");

function output($message, $color = COLOR_RESET) {
    echo $color . $message . COLOR_RESET . PHP_EOL;
}

function printHeader($message) {
    echo PHP_EOL;
    output('═══════════════════════════════════════════════════', COLOR_CYAN);
    output('  ' . $message, COLOR_YELLOW);
    output('═══════════════════════════════════════════════════', COLOR_CYAN);
    echo PHP_EOL;
}

function checkmark($message) {
    output('✓ ' . $message, COLOR_GREEN);
}

function error($message) {
    output('✗ ' . $message, COLOR_RED);
}

function warning($message) {
    output('⚠ ' . $message, COLOR_YELLOW);
}

function prompt($question) {
    echo COLOR_YELLOW . $question . ' (y/n): ' . COLOR_RESET;
    $handle = fopen('php://stdin', 'r');
    $line = fgets($handle);
    fclose($handle);
    return strtolower(trim($line)) === 'y';
}

// Start deployment
printHeader('SIMACCA Production Deployment Checker');

output('Target: simacca.smkn8bone.sch.id', COLOR_CYAN);
output('Environment: PRODUCTION', COLOR_CYAN);
echo PHP_EOL;

warning('This script will check if your application is ready for production deployment.');
echo PHP_EOL;

if (!prompt('Continue?')) {
    output('Deployment cancelled.', COLOR_YELLOW);
    exit(0);
}

$errors = [];
$warnings = [];
$checks = 0;

// Check 1: .env file exists
printHeader('Checking Configuration Files');
$checks++;
if (file_exists(__DIR__ . '/.env')) {
    checkmark('.env file exists');
    
    // Read .env content
    $envContent = file_get_contents(__DIR__ . '/.env');
    
    // Check environment setting
    if (preg_match('/CI_ENVIRONMENT\s*=\s*production/i', $envContent)) {
        checkmark('CI_ENVIRONMENT set to production');
    } else {
        error('CI_ENVIRONMENT is NOT set to production');
        $errors[] = 'Update .env: CI_ENVIRONMENT = production';
    }
    
    // Check baseURL
    if (preg_match('/app\.baseURL.*simacca\.smkn8bone\.sch\.id/i', $envContent)) {
        checkmark('app.baseURL configured correctly');
    } else {
        error('app.baseURL not set to production domain');
        $errors[] = 'Update .env: app.baseURL = \'https://simacca.smkn8bone.sch.id/\'';
    }
    
    // Check encryption key
    if (preg_match('/encryption\.key\s*=\s*[^\s]+/i', $envContent) && 
        !preg_match('/encryption\.key\s*=\s*$/i', $envContent)) {
        checkmark('Encryption key is set');
    } else {
        error('Encryption key is NOT set');
        $errors[] = 'Generate encryption key: php spark key:generate';
    }
    
    // Check database password
    if (preg_match('/database\.default\.password\s*=\s*$/i', $envContent)) {
        warning('Database password is empty (might be okay for localhost)');
        $warnings[] = 'Consider setting a database password for security';
    } else {
        checkmark('Database password is set');
    }
    
} else {
    error('.env file NOT found');
    $errors[] = 'Copy .env.production to .env and configure it';
}

// Check 2: Writable directories
printHeader('Checking Directory Permissions');
$checks++;
$writableDirs = ['writable', 'writable/cache', 'writable/logs', 'writable/session', 'writable/uploads'];
foreach ($writableDirs as $dir) {
    if (is_writable(__DIR__ . '/' . $dir)) {
        checkmark($dir . ' is writable');
    } else {
        error($dir . ' is NOT writable');
        $errors[] = 'Make ' . $dir . ' writable: chmod 755 ' . $dir;
    }
}

// Check 3: Public directory
printHeader('Checking Public Directory');
$checks++;
if (file_exists(__DIR__ . '/simacca_public/index.php')) {
    checkmark('simacca_public/index.php exists');
} else {
    error('simacca_public/index.php NOT found');
    $errors[] = 'Ensure public directory is properly set up';
}

if (file_exists(__DIR__ . '/simacca_public/.htaccess')) {
    checkmark('simacca_public/.htaccess exists');
} else {
    warning('simacca_public/.htaccess NOT found (might cause routing issues)');
    $warnings[] = 'Create .htaccess in public directory for Apache';
}

// Check 4: Database config
printHeader('Checking Database Configuration');
$checks++;
require_once __DIR__ . '/app/Config/Database.php';
$dbConfig = new \Config\Database();
if ($dbConfig->default['hostname'] && $dbConfig->default['database']) {
    checkmark('Database configuration found');
} else {
    error('Database configuration incomplete');
    $errors[] = 'Configure database settings in .env';
}

// Check 5: Security settings
printHeader('Checking Security Configuration');
$checks++;
if (file_exists(__DIR__ . '/.gitignore')) {
    $gitignore = file_get_contents(__DIR__ . '/.gitignore');
    if (strpos($gitignore, '.env') !== false) {
        checkmark('.env is in .gitignore');
    } else {
        error('.env is NOT in .gitignore');
        $errors[] = 'Add .env to .gitignore to prevent committing secrets';
    }
} else {
    warning('.gitignore not found');
}

// Check 6: Commands available
printHeader('Checking Maintenance Commands');
$checks++;
if (file_exists(__DIR__ . '/app/Commands/SessionCleanup.php')) {
    checkmark('SessionCleanup command available');
} else {
    warning('SessionCleanup command not found');
}

if (file_exists(__DIR__ . '/app/Commands/CacheClear.php')) {
    checkmark('CacheClear command available');
} else {
    warning('CacheClear command not found');
}

if (file_exists(__DIR__ . '/app/Commands/KeyGenerate.php')) {
    checkmark('KeyGenerate command available');
} else {
    warning('KeyGenerate command not found');
}

// Summary
printHeader('Deployment Readiness Summary');
output('Checks completed: ' . $checks, COLOR_CYAN);
output('Errors found: ' . count($errors), count($errors) > 0 ? COLOR_RED : COLOR_GREEN);
output('Warnings: ' . count($warnings), count($warnings) > 0 ? COLOR_YELLOW : COLOR_GREEN);
echo PHP_EOL;

if (count($errors) > 0) {
    output('CRITICAL ISSUES TO FIX:', COLOR_RED);
    foreach ($errors as $i => $error) {
        output(($i + 1) . '. ' . $error, COLOR_RED);
    }
    echo PHP_EOL;
}

if (count($warnings) > 0) {
    output('WARNINGS (Recommended to fix):', COLOR_YELLOW);
    foreach ($warnings as $i => $warning) {
        output(($i + 1) . '. ' . $warning, COLOR_YELLOW);
    }
    echo PHP_EOL;
}

// Final verdict
printHeader('Final Verdict');
if (count($errors) === 0) {
    checkmark('Application is READY for production deployment!');
    echo PHP_EOL;
    output('Next steps:', COLOR_CYAN);
    output('1. Upload files to server', COLOR_CYAN);
    output('2. Run: php spark migrate', COLOR_CYAN);
    output('3. Setup cron: 0 2 * * * php spark session:cleanup', COLOR_CYAN);
    output('4. Test application thoroughly', COLOR_CYAN);
    output('5. Monitor logs in writable/logs/', COLOR_CYAN);
} else {
    error('Application is NOT ready for deployment!');
    output('Fix all critical issues above before deploying.', COLOR_RED);
    exit(1);
}

echo PHP_EOL;
output('═══════════════════════════════════════════════════', COLOR_CYAN);
echo PHP_EOL;
