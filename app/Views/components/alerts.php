<?php
/**
 * Reusable Alert Component
 * Menampilkan flash messages dengan styling yang konsisten
 */

if (session()->has('success')): ?>
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg shadow-md mb-4 flex items-start" 
     role="alert">
    <i class="fas fa-check-circle text-xl mr-3 mt-0.5"></i>
    <div class="flex-1">
        <p class="font-semibold">Berhasil!</p>
        <p class="text-sm"><?= session('success') ?></p>
    </div>
    <button onclick="this.parentElement.remove()" 
            class="text-green-700 hover:text-green-900 ml-4">
        <i class="fas fa-times"></i>
    </button>
</div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-md mb-4 flex items-start" 
     role="alert">
    <i class="fas fa-exclamation-circle text-xl mr-3 mt-0.5"></i>
    <div class="flex-1">
        <p class="font-semibold">Terjadi Kesalahan!</p>
        <p class="text-sm"><?= session('error') ?></p>
    </div>
    <button onclick="this.parentElement.remove()" 
            class="text-red-700 hover:text-red-900 ml-4">
        <i class="fas fa-times"></i>
    </button>
</div>
<?php endif; ?>

<?php if (session()->has('warning')): ?>
<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-r-lg shadow-md mb-4 flex items-start" 
     role="alert">
    <i class="fas fa-exclamation-triangle text-xl mr-3 mt-0.5"></i>
    <div class="flex-1">
        <p class="font-semibold">Perhatian!</p>
        <p class="text-sm"><?= session('warning') ?></p>
    </div>
    <button onclick="this.parentElement.remove()" 
            class="text-yellow-700 hover:text-yellow-900 ml-4">
        <i class="fas fa-times"></i>
    </button>
</div>
<?php endif; ?>

<?php if (session()->has('info')): ?>
<div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded-r-lg shadow-md mb-4 flex items-start" 
     role="alert">
    <i class="fas fa-info-circle text-xl mr-3 mt-0.5"></i>
    <div class="flex-1">
        <p class="font-semibold">Informasi</p>
        <p class="text-sm"><?= session('info') ?></p>
    </div>
    <button onclick="this.parentElement.remove()" 
            class="text-blue-700 hover:text-blue-900 ml-4">
        <i class="fas fa-times"></i>
    </button>
</div>
<?php endif; ?>

<?php if (session()->has('errors')): ?>
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-md mb-4" 
     role="alert">
    <div class="flex items-start">
        <i class="fas fa-exclamation-circle text-xl mr-3 mt-0.5"></i>
        <div class="flex-1">
            <p class="font-semibold mb-2">Terjadi beberapa kesalahan:</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" 
                class="text-red-700 hover:text-red-900 ml-4">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
<?php endif; ?>
