<?php 
$pageTitle = 'Kelola Grade - Inventaris Gallon';
ob_start(); 
?>

<!-- Page Header dengan Button di Samping -->
<div class="mb-8 flex items-center justify-between">
    <!-- Left: Title & Description -->
    <div>
        <h1 class="text-3xl font-bold mb-2" style="color: #1F4396;">Kelola Grade</h1>
        <p class="text-lg text-gray-700">Atur grade dan kuota galon untuk karyawan</p>
    </div>
    
    <!-- Right: Action Button -->
    <div class="flex-shrink-0">
        <a href="?url=grades/create" 
           class="inline-flex items-center gap-3 text-white font-semibold text-md px-5 py-2 rounded-md shadow-md transition-all hover:shadow-xl"
           style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); min-height: 56px; white-space: nowrap;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Grade Baru</span>
        </a>
    </div>
</div>

<?php
// 1. Tentukan apakah ada filter aktif (untuk tombol Bersihkan)
$hasFilters = !empty($_GET['search']);

// 2. Siapkan konten Search Bar
ob_start();
    $label = 'Cari Grade';
    $placeholder = 'Masukkan nama grade...';
    include '../app/views/components/search-bar.php';
$searchContent = ob_get_clean();

// 3. Konten Filter (Kosongkan saja karena Grade cuma butuh Search)
$filterContent = ''; 

// 4. Render Container
include '../app/views/components/filter-container.php';
?>

<!-- Alert Messages -->
<?php if (isset($_GET['success'])): ?>
<div class="bg-green-50 border-2 border-green-500 text-gray-900 px-6 py-4 rounded-lg mb-6 text-lg">
<strong class="font-bold text-green-700">Berhasil!</strong>
    <?php if ($_GET['success'] == '1'): ?>
        Grade baru berhasil ditambahkan ke sistem!
    <?php elseif ($_GET['success'] == '2'): ?>
        Data Grade berhasil diperbarui!
    <?php elseif ($_GET['success'] == '3'): ?>
        Grade berhasil dihapus dari sistem!
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="bg-red-50 border-2 border-red-500 text-gray-900 px-6 py-4 rounded-lg mb-6 text-lg">
<strong class="font-bold text-red-700">Gagal!</strong>
    <?php if ($_GET['error'] == 'in_use'): ?>
        Grade tidak dapat dihapus karena masih digunakan oleh karyawan. Silakan ubah grade karyawan terlebih dahulu.
    <?php else: ?>
        Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Grades Table -->
<div class="bg-white rounded-md overflow-hidden border-2 border-gray-200">
    <table class="w-full">
        <!-- Table Header -->
        <thead>
            <tr style="background: linear-gradient(135deg, #1F4396 0%, #2E5BBE 100%);">
                <th class="py-4 px-5 text-left text-white font-semibold text-md border-r border-white border-opacity-20" style="width: 10%;">
                    NO
                </th>
                <th class="py-4 px-5 text-left text-white font-semibold text-md border-r border-white border-opacity-20" style="width: 35%;">
                    NAMA GRADE
                </th>
                <th class="py-4 px-5 text-center text-white font-semibold text-md border-r border-white border-opacity-20" style="width: 25%;">
                    KUOTA GALLON / BULAN
                </th>
                <th class="py-4 px-5 text-center text-white font-semibold text-md" style="width: 30%;">
                    AKSI
                </th>
            </tr>
        </thead>
        
        <!-- Table Body -->
        <tbody>
            <?php if (empty($grades)): ?>
                <tr>
                    <td colspan="4" class="py-16 text-center">
                        <p class="text-gray-600 text-xl font-semibold mb-2">Belum Ada Data Grade</p>
                        <p class="text-gray-500 text-lg">Klik tombol "Tambah Grade Baru" untuk memulai</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($grades as $index => $grade): ?>
                <tr class="border-b-2 border-gray-100 hover:bg-blue-50 transition-colors">
                    <!-- Nomor -->
                    <td class="py-3 px-5 text-gray-900 font-semibold text-base">
                        <?= $index + 1 ?>
                    </td>
                    
                    <!-- Nama Grade -->
                    <td class="py-3 px-5">
                        <span class="text-gray-900 font-semibold text-base">
                            <?= htmlspecialchars($grade['name_grade']) ?>
                        </span>
                    </td>
                    
                    <!-- Kuota -->
                    <td class="py-3 px-5 flex items-center justify-center">
                        <div class="inline-flex items-center gap-1.5 bg-blue-50 px-2.5 py-1 rounded-md border-2 border-blue-200">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-900 font-semibold text-sm"><?= $grade['default_allowance'] ?></span>
                            <span class="text-gray-700 font-medium text-sm">gallon</span>
                        </div>
                    </td>
                    
                    <!-- Action Buttons -->
                    <td class="py-4 px-5">
                        <div class="flex justify-center gap-2">
                            <a href="?url=grades/edit/<?= $grade['id_grade'] ?>" 
                            class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold text-xs px-3 py-1.5 rounded shadow-sm transition-all"
                            title="Edit Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                EDIT
                            </a>
                            
                            <button type="button"
                                    onclick="confirmDelete(<?= $grade['id_grade'] ?>, '<?= htmlspecialchars($grade['name_grade'], ENT_QUOTES) ?>', <?= $grade['default_allowance'] ?>)"
                                    class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white font-semibold text-xs px-3 py-1.5 rounded shadow-sm transition-all"
                                    title="Hapus Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                HAPUS
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50" onclick="if(event.target===this) closeDeleteModal()">
    <div class="bg-white rounded-md shadow-md p-10 max-w-lg w-full mx-4">
        <div class="text-center mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">KONFIRMASI PENGHAPUSAN</h2>
            <p class="text-md text-gray-800 mb-4 leading-relaxed">
                Apakah Anda <strong class="text-red-600">YAKIN</strong> ingin menghapus grade ini?
            </p>
            <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-4 mb-4">
                <p class="text-lg font-semibold text-gray-900" id="deleteGradeName"></p>
                <p class="text-base text-gray-700" id="deleteGradeAllowance"></p>
            </div>
            <p class="text-md text-red-600 font-bold">
                Data ini akan dihapus PERMANEN dan tidak dapat dikembalikan!
            </p>
        </div>
        
        <div class="flex flex-col gap-4">
            <a href="#" 
               id="deleteConfirmLink"
               class="block bg-red-600 hover:bg-red-700 text-white font-semibold text-md py-5 rounded-md text-center transition-all shadow-lg">
                YA, HAPUS SEKARANG
            </a>
            <button type="button"
                    onclick="closeDeleteModal()"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold text-md py-5 rounded-md transition-all">
                BATAL, JANGAN HAPUS
            </button>
        </div>
    </div>
</div>

<script>
function confirmDelete(gradeId, gradeName, allowance) {
    document.getElementById('deleteGradeName').textContent = 'Grade: ' + gradeName;
    document.getElementById('deleteGradeAllowance').textContent = 'Kuota: ' + allowance + ' galon/bulan';
    document.getElementById('deleteConfirmLink').href = '?url=grades/delete/' + gradeId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>

<!-- Info Box -->
<div class="mt-8 bg-blue-50 border-2 border-blue-300 rounded-xl p-6">
    <div class="flex items-start gap-4">
        <svg class="w-8 h-8 text-blue-600 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
        <div>
            <p class="text-gray-900 font-bold text-lg mb-2">Informasi Penting:</p>
            <ul class="text-gray-800 text-base space-y-1 leading-relaxed">
                <li>• Grade menentukan kuota galon bulanan untuk setiap karyawan</li>
                <li>• Grade yang SUDAH digunakan tidak dapat dihapus, hanya bisa diedit</li>
                <li>• Perubahan grade akan otomatis mempengaruhi kuota karyawan terkait di bulan berikutnya</li>
            </ul>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>
