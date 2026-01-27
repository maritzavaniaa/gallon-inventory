<?php 
$pageTitle = 'Laporan - Sistem Galon';
ob_start(); 
?>

<div class="bg-white rounded-xl shadow-lg p-12 text-center">
    <div class="mb-6">
        <span class="text-8xl">📊</span>
    </div>
    <h1 class="text-5xl font-bold text-gray-800 mb-4">Laporan</h1>
    <p class="text-xl text-gray-600">Halaman untuk melihat laporan penggunaan galon</p>
    
    <div class="mt-8 p-6 bg-purple-50 rounded-lg inline-block">
        <p class="text-gray-700 font-semibold">🚧 Under Development</p>
        <p class="text-sm text-gray-600 mt-2">Fitur ini akan segera hadir!</p>
    </div>
</div>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>
