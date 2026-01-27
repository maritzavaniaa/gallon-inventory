<?php 
$pageTitle = 'Tambah Grade - Sistem Galon';
ob_start(); 
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Edit Grade</h1>          
        <form method="POST" action="?url=grades/update/<?= $grade['id_grade'] ?>" class="space-y-6">
            <div>
                <label class="block text-lg font-semibold mb-3 text-gray-700">Nama Grade</label>
                <input type="text" name="name_grade" value="<?= htmlspecialchars($grade['name_grade']) ?>" 
                        required 
                        class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-lg font-semibold mb-3 text-gray-700">Kuota Galon per Bulan</label>
                <input type="number" name="default_allowance" value="<?= $grade['default_allowance'] ?>" 
                        required min="0" 
                        class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
            </div>
            
            <div class="flex flex-col space-y-3 pt-6">
                <button type="submit" 
                        class="w-full bg-green-500 hover:bg-green-600 text-white text-lg font-bold py-4 rounded-lg transition">
                    💾 Simpan Perubahan
                </button>
                
                <a href="?url=grades" 
                    class="w-full bg-gray-500 hover:bg-gray-600 text-white text-lg font-bold py-4 rounded-lg text-center transition">
                    ❌ Batal
                </a>
                
                <?php if (!$isInUse): ?>
                    <!-- JIKA TIDAK DIGUNAKAN: tombol merah aktif -->
                    <a href="?url=grades/delete/<?= $grade['id_grade'] ?>" 
                    onclick="return confirm('Yakin hapus grade ini?')" 
                    class="w-full bg-red-500 hover:bg-red-600 text-white text-lg font-bold py-4 rounded-lg text-center transition">
                        🗑️ Hapus Grade
                    </a>
                <?php else: ?>
                    <!-- JIKA DIGUNAKAN: tombol abu disabled -->
                    <div class="w-full bg-gray-300 text-gray-600 text-lg font-bold py-4 rounded-lg text-center cursor-not-allowed">
                        🔒 Hapus Tidak Tersedia (Sedang Digunakan)
                    </div>
                <?php endif; ?>

                <a href="?url=grades" 
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white text-lg font-bold py-4 rounded-lg text-center transition">
                    🏠 Kembali ke Daftar Grade
                </a>
            </div>
        </form>
    </div>
</div>


<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>
