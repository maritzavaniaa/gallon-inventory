<?php 
$pageTitle = 'Tambah Grade - Sistem Galon';
ob_start(); 
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">➕ Tambah Grade Baru</h1>
        
        <form method="POST" action="?url=grades/store" class="space-y-6">
            <div>
                <label class="block text-lg font-semibold mb-3 text-gray-700">Nama Grade</label>
                <input type="text" 
                       name="name_grade" 
                       required 
                       placeholder="Contoh: Staff, Manager, Director"
                       class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-lg font-semibold mb-3 text-gray-700">Kuota Galon per Bulan</label>
                <input type="number" 
                       name="default_allowance" 
                       required 
                       min="0" 
                       placeholder="Contoh: 4"
                       class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-2">💧 Jumlah galon yang bisa diambil per bulan</p>
            </div>
            
            <div class="flex flex-col space-y-3 pt-6">
                <button type="submit" 
                        class="w-full bg-green-500 hover:bg-green-600 text-white text-lg font-bold py-4 rounded-lg transition">
                    💾 Simpan Grade
                </button>
                
                <a href="?url=grades" 
                   class="w-full bg-gray-500 hover:bg-gray-600 text-white text-lg font-bold py-4 rounded-lg text-center transition">
                    ❌ Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>
