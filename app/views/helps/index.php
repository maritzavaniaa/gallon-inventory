<?php 
$pageTitle = 'Panduan Pengguna';
ob_start(); 
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold mb-2" style="color: #1F4396;">Panduan Pengguna</h1>
    <p class="text-lg text-gray-700">Pelajari instruksi penggunaan sistem untuk Admin dan General User.</p>
</div>

<div class="bg-white rounded-md shadow-sm border-2 border-gray-200 overflow-hidden" style="height: calc(100vh - 200px); min-height: 500px;">
    <iframe 
        src="./assets/docs/BukuPanduan_InternGallon.pdf" 
        width="100%" 
        height="100%" 
        style="border: none;">
        <div class="p-10 text-center">
            <p class="text-xl mb-4">Browser Anda tidak mendukung tampilan PDF langsung.</p>
            <a href="assets/docs/user_manual.pdf" class="bg-blue-600 text-white px-6 py-2 rounded-md" target="_blank">
                Klik untuk Download PDF
            </a>
        </div>
    </iframe>
</div>

<?php 
$content = ob_get_clean(); 
include '../app/views/layouts/main.php'; 
?>