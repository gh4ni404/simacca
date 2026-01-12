<?php
/**
 * Script untuk clear cache CodeIgniter
 * Upload file ini ke root folder dan akses via browser
 * Kemudian hapus file ini setelah selesai
 */

// Clear writable/cache
$cacheDir = __DIR__ . '/writable/cache';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== 'index.html') {
            unlink($file);
            echo "Deleted: " . basename($file) . "<br>";
        }
    }
    echo "<br><strong>Cache cleared!</strong><br>";
} else {
    echo "Cache directory not found!<br>";
}

// Clear writable/debugbar
$debugbarDir = __DIR__ . '/writable/debugbar';
if (is_dir($debugbarDir)) {
    $files = glob($debugbarDir . '/*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== 'index.html') {
            unlink($file);
            echo "Deleted debugbar: " . basename($file) . "<br>";
        }
    }
}

// Clear writable/session (optional - ini akan logout semua user)
// Uncomment jika perlu
/*
$sessionDir = __DIR__ . '/writable/session';
if (is_dir($sessionDir)) {
    $files = glob($sessionDir . '/*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== 'index.html') {
            unlink($file);
            echo "Deleted session: " . basename($file) . "<br>";
        }
    }
}
*/

echo "<br><strong>Done!</strong><br>";
echo "<br><a href='" . dirname($_SERVER['PHP_SELF']) . "'>Go to Home</a><br>";
echo "<br><strong style='color: red;'>IMPORTANT: Delete this file (clear_cache.php) after use!</strong>";
?>
