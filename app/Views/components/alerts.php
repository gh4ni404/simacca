<?php
/**
 * Reusable Alert Component
 * Menampilkan SATU flash message dengan prioritas tertinggi
 * 
 * Priority order: errors > error > warning > success_custom > success > info
 * 
 * Usage: <?= view('components/alerts') ?>
 * Or with parameter: <?= view('components/alerts', ['showAll' => true]) ?>
 */

// Default: only show highest priority message
// Use isset() to avoid undefined variable warning
$showAll = isset($showAll) ? $showAll : false;

// Render alerts with priority
echo render_alerts($showAll);
?>
