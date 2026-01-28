<?php 
$pageTitle = 'Tambah Grade - Inventaris Gallon';
ob_start(); 
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-md shadow-md p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-semibold text-gray-800 mb-2">Tambah Grade Baru</h1>
            <p class="text-lg text-gray-700">Buat grade baru untuk karyawan dengan kuota galon</p>
        </div>
        
        <form method="POST" action="?url=grades/store" class="space-y-6">
            <!-- Nama Grade -->
            <div>
                <label class="block text-lg font-semibold mb-3 text-gray-900">
                    Nama Grade<span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name_grade" 
                       required 
                       placeholder="Contoh: Staff, Manager, Director"
                       class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
            </div>
            
            <!-- Kuota Gallon -->
            <div>
                <label class="block text-lg font-semibold mb-3 text-gray-900">
                    Kuota Gallon per Bulan<span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="default_allowance" 
                       required 
                       min="0" 
                       placeholder="Contoh: 4"
                       class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-2">Jumlah gallon yang bisa diambil per bulan</p>
            </div>
            
            <div class="flex flex-col space-y-3 pt-6">
                <button type="submit" 
                        class="w-full bg-green-500 hover:bg-green-600 text-white text-lg font-semibold py-4 rounded-lg transition flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Simpan Grade
                </button>
                
                <a href="?url=grades" 
                class="w-full bg-gray-500 hover:bg-gray-600 text-white text-lg font-semibold py-4 rounded-lg text-center transition flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>
