<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-6 py-8 max-w-3xl">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">➕ Tambah Karyawan/Non-Karyawan Baru</h1>
            
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
                <div>
                    <label class="block text-lg font-semibold mb-3">Tipe *</label>
                    <select name="type_subject" id="type_subject" required 
                            class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300"
                            onchange="toggleFields()">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="karyawan">Karyawan (Grade-based)</option>
                        <option value="non_karyawan">Non-Karyawan (Manual allowance)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-lg font-semibold mb-3">ID Share *</label>
                    <input type="text" name="id_share" required placeholder="Contoh: EMP001, GUEST01" 
                           class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300"
                           style="text-transform: uppercase;">
                    <p class="text-sm text-gray-500 mt-2">⚠️ ID unik, tidak dapat diubah setelah dibuat</p>
                </div>
                
                <div>
                    <label class="block text-lg font-semibold mb-3">Nama Lengkap *</label>
                    <input type="text" name="name_subject" required placeholder="Nama lengkap" 
                           class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300">
                </div>
                
                <div>
                    <label class="block text-lg font-semibold mb-3">Divisi</label>
                    <input type="text" name="division" placeholder="Contoh: Produksi, IT, Maintenance" 
                           class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300">
                </div>
                
                <div id="grade_field" style="display:none;">
                    <label class="block text-lg font-semibold mb-3">Grade (Karyawan) *</label>
                    <select name="id_grade" 
                            class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300">
                        <option value="">-- Pilih Grade --</option>
                        <?php
                        require_once '../app/models/Grade.php';
                        $gradeModel = new Grade($GLOBALS['conn']);
                        $grades = $gradeModel->getAll();
                        foreach ($grades as $grade): ?>
                            <option value="<?= $grade['id_grade'] ?>">
                                <?= htmlspecialchars($grade['name_grade']) ?> - <?= $grade['default_allowance'] ?> galon/bulan
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-sm text-gray-500 mt-2">Kuota otomatis mengikuti grade</p>
                </div>
                
                <div id="allowance_field" style="display:none;">
                    <label class="block text-lg font-semibold mb-3">Kuota Galon per Bulan (Non-Karyawan) *</label>
                    <input type="number" name="default_allowance" min="0" placeholder="Contoh: 2" 
                           class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300">
                    <p class="text-sm text-gray-500 mt-2">Kuota manual untuk non-karyawan</p>
                </div>
                
                <div class="flex flex-col space-y-3 pt-6 border-t-2">
                    <button type="submit" 
                            class="w-full bg-green-500 hover:bg-green-600 text-white text-xl font-bold py-4 rounded-lg transition">
                        💾 Simpan Data
                    </button>
                    
                    <a href="?url=subjects" 
                       class="w-full bg-gray-500 hover:bg-gray-600 text-white text-xl font-bold py-4 rounded-lg text-center transition">
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
            
            if (type === 'karyawan') {
                gradeField.style.display = 'block';
                allowanceField.style.display = 'none';
                gradeField.querySelector('select').required = true;
                allowanceField.querySelector('input').required = false;
                allowanceField.querySelector('input').value = '';
            } else if (type === 'non_karyawan') {
                gradeField.style.display = 'none';
                allowanceField.style.display = 'block';
                gradeField.querySelector('select').required = false;
                gradeField.querySelector('select').value = '';
                allowanceField.querySelector('input').required = true;
            } else {
                gradeField.style.display = 'none';
                allowanceField.style.display = 'none';
            }
        }
    </script>
</body>
</html>
