<?php 
$pageTitle = 'Tambah Penerima - Inventaris Gallon';
ob_start(); 
?>


<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-md shadow-md p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-semibold text-gray-800 mb-2">Tambah Penerima Baru</h1>
            <p class="text-lg text-gray-700">Daftarkan karyawan atau non-karyawan sebagai penerima galon</p>
        </div>
        
        <!-- Error Alert -->
        <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-50 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <span>
                <?php if ($_GET['error'] == 'duplicate'): ?>
                    ID Share sudah digunakan! Gunakan ID yang lain.
                <?php else: ?>
                    Gagal menyimpan data. Silakan coba lagi.
                <?php endif; ?>
            </span>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="?url=subjects/store" class="space-y-6">
            <!-- Type Selection -->
            <div>
                <label class="block text-md font-semibold mb-2 text-gray-900">
                    Tipe<span class="text-red-500">*</span>
                </label>
                <select name="type_subject" 
                        id="type_subject" 
                        required 
                        onchange="toggleFields()"
                        class="w-full p-2 text-md border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
                    <option value="">-- Pilih Tipe --</option>
                    <option value="karyawan">Karyawan (Grade-based)</option>
                    <option value="non_karyawan">Non-Karyawan (Manual allowance)</option>
                </select>
                <p class="text-sm text-gray-500 mt-2">Pilih tipe berdasarkan status kepegawaian</p>
            </div>
            
            <!-- ID Share -->
            <div>
                <label class="block text-md font-semibold mb-2 text-gray-900">
                    ID Share<span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="id_share" 
                       required 
                       placeholder="Contoh: EMP001, GUEST01"
                       style="text-transform: uppercase;"
                       class="w-full p-2 text-md border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-2">ID unik, tidak dapat diubah setelah dibuat</p>
            </div>
            
            <!-- Name -->
            <div>
                <label class="block text-md font-semibold mb-2 text-gray-900">
                    Nama Lengkap<span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name_subject" 
                       required 
                       placeholder="Nama lengkap"
                       class="w-full p-2 text-md border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
            </div>
            
            <!-- Division -->
            <div>
                <label class="block text-md font-semibold mb-2 text-gray-900">
                    Divisi
                </label>
                <div id="division_karyawan_wrapper">
                    <select name="division_select" 
                            id="division_select"
                            class="w-full p-2 text-md border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
                        <option value="">-- Pilih Divisi --</option>
                        <?php foreach (getDivisionList() as $div): ?>
                            <option value="<?= $div ?>"><?= $div ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="division_non_karyawan_wrapper" style="display:none;">
                    <input type="text" 
                        name="division_text" 
                        id="division_text"
                        placeholder="Ketik divisi bebas..."
                        style="text-transform: uppercase;"
                        class="w-full p-2 text-md border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
                </div>

                <input type="hidden" name="division" id="division_final">
                
                <p class="text-sm text-gray-500 mt-2">Untuk karyawan silakan pilih list, untuk non-karyawan bisa isi bebas.</p>
            </div>
            
            <!-- Grade Field (Karyawan) -->
            <div id="grade_field" style="display:none;">
                <label class="block text-md font-semibold mb-2 text-gray-900">
                    Grade<span class="text-red-500">*</span>
                </label>
                <select name="id_grade" 
                        id="grade_select"
                        class="w-full p-2 text-md border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
                    <option value="">-- Pilih Grade --</option>
                    <?php foreach ($grades as $grade): ?>
                        <option value="<?= $grade['id_grade'] ?>">
                            <?= htmlspecialchars($grade['name_grade']) ?> - <?= $grade['default_allowance'] ?> galon/bulan
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-sm text-gray-500 mt-2">Kuota otomatis mengikuti grade yang dipilih</p>
            </div>
            
            <!-- Allowance Field (Non-Karyawan) -->
            <div id="allowance_field" style="display:none;">
                <label class="block text-md font-semibold mb-2 text-gray-900">
                    Kuota Galon per Bulan<span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="default_allowance" 
                       id="allowance_input"
                       min="0" 
                       placeholder="Contoh: 2"
                       class="w-full p-2 text-md border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-2">Tentukan kuota manual untuk non-karyawan</p>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col space-y-3 pt-6">
                <button type="submit" 
                        class="w-full bg-green-500 hover:bg-green-600 text-white text-lg font-semibold py-4 rounded-lg transition flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Simpan Data
                </button>
                
                <a href="?url=subjects" 
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


<script>
function toggleFields() {
    const type = document.getElementById('type_subject').value;
    const gradeField = document.getElementById('grade_field');
    const allowanceField = document.getElementById('allowance_field');
    const gradeSelect = document.getElementById('grade_select');
    const allowanceInput = document.getElementById('allowance_input');
    const divKaryawan = document.getElementById('division_karyawan_wrapper');
    const divNon = document.getElementById('division_non_karyawan_wrapper');
    
    if (type === 'karyawan') {
        // Tampilkan Grade & Divisi Dropdown
        gradeField.style.display = 'block';
        allowanceField.style.display = 'none';
        divKaryawan.style.display = 'block';
        divNon.style.display = 'none';

        gradeSelect.required = true;
        allowanceInput.required = false;
        allowanceInput.value = '';
    } else if (type === 'non_karyawan') {
        gradeField.style.display = 'none';
        allowanceField.style.display = 'block';
        divKaryawan.style.display = 'none';
        divNon.style.display = 'block';

        gradeSelect.required = false;
        gradeSelect.value = '';
        allowanceInput.required = true;
    } else {
        gradeField.style.display = 'none';
        allowanceField.style.display = 'none';
        divKaryawan.style.display = 'block'; // Default tampilan
        divNon.style.display = 'none';
    }
}

document.querySelector('form').addEventListener('submit', function(e) {
    const type = document.getElementById('type_subject').value;
    const finalInput = document.getElementById('division_final');
    
    if (type === 'karyawan') {
        finalInput.value = document.getElementById('division_select').value;
    } else {
        finalInput.value = document.getElementById('division_text').value;
    }
});
</script>


<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>