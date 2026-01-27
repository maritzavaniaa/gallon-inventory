<?php 
$pageTitle = 'Tambah Karyawan - Sistem Galon';
ob_start(); 
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">➕ Tambah Karyawan/Non-Karyawan</h1>
        
        <!-- Error Alert -->
        <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?php if ($_GET['error'] == 'duplicate'): ?>
                ❌ ID Share sudah digunakan! Gunakan ID yang lain.
            <?php else: ?>
                ❌ Gagal menyimpan data. Silakan coba lagi.
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="?url=subjects/store" class="space-y-6">
            <!-- Type Selection -->
            <div>
                <label class="block text-lg font-semibold mb-3 text-gray-700">Tipe *</label>
                <select name="type_subject" 
                        id="type_subject" 
                        required 
                        onchange="toggleFields()"
                        class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
                    <option value="">-- Pilih Tipe --</option>
                    <option value="karyawan">Karyawan (Grade-based)</option>
                    <option value="non_karyawan">Non-Karyawan (Manual allowance)</option>
                </select>
            </div>
            
            <!-- ID Share -->
            <div>
                <label class="block text-lg font-semibold mb-3 text-gray-700">ID Share *</label>
                <input type="text" 
                       name="id_share" 
                       required 
                       placeholder="Contoh: EMP001, GUEST01"
                       style="text-transform: uppercase;"
                       class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-2">🔑 ID unik, tidak dapat diubah setelah dibuat</p>
            </div>
            
            <!-- Name -->
            <div>
                <label class="block text-lg font-semibold mb-3 text-gray-700">Nama Lengkap *</label>
                <input type="text" 
                       name="name_subject" 
                       required 
                       placeholder="Nama lengkap"
                       class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
            </div>
            
            <!-- Division -->
            <div>
                <label class="block text-lg font-semibold mb-3 text-gray-700">Divisi</label>
                <input type="text" 
                       name="division" 
                       placeholder="Contoh: Produksi, IT, Maintenance"
                       class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
            </div>
            
            <!-- Grade Field (Karyawan) -->
            <div id="grade_field" style="display:none;">
                <label class="block text-lg font-semibold mb-3 text-gray-700">Grade *</label>
                <select name="id_grade" 
                        id="grade_select"
                        class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
                    <option value="">-- Pilih Grade --</option>
                    <?php foreach ($grades as $grade): ?>
                        <option value="<?= $grade['id_grade'] ?>">
                            <?= htmlspecialchars($grade['name_grade']) ?> - <?= $grade['default_allowance'] ?> galon/bulan
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-sm text-gray-500 mt-2">💼 Kuota otomatis mengikuti grade</p>
            </div>
            
            <!-- Allowance Field (Non-Karyawan) -->
            <div id="allowance_field" style="display:none;">
                <label class="block text-lg font-semibold mb-3 text-gray-700">Kuota Galon per Bulan *</label>
                <input type="number" 
                       name="default_allowance" 
                       id="allowance_input"
                       min="0" 
                       placeholder="Contoh: 2"
                       class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-2">💧 Kuota manual untuk non-karyawan</p>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col space-y-3 pt-6 border-t-2">
                <button type="submit" 
                        class="w-full bg-green-500 hover:bg-green-600 text-white text-lg font-bold py-4 rounded-lg transition">
                    💾 Simpan Data
                </button>
                
                <a href="?url=subjects" 
                   class="w-full bg-gray-500 hover:bg-gray-600 text-white text-lg font-bold py-4 rounded-lg text-center transition">
                    ❌ Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleFields() {
    const type = document.getElementById('type_subject').value;
    const gradeField = document.getElementById('grade_field');
    const allowanceField = document.getElementById('allowance_field');
    const gradeSelect = document.getElementById('grade_select');
    const allowanceInput = document.getElementById('allowance_input');
    
    if (type === 'karyawan') {
        gradeField.style.display = 'block';
        allowanceField.style.display = 'none';
        gradeSelect.required = true;
        allowanceInput.required = false;
        allowanceInput.value = '';
    } else if (type === 'non_karyawan') {
        gradeField.style.display = 'none';
        allowanceField.style.display = 'block';
        gradeSelect.required = false;
        gradeSelect.value = '';
        allowanceInput.required = true;
    } else {
        gradeField.style.display = 'none';
        allowanceField.style.display = 'none';
        gradeSelect.required = false;
        allowanceInput.required = false;
    }
}
</script>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>
