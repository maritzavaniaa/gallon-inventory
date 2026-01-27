<?php 
$pageTitle = 'Kelola Grade - Sistem Galon';
ob_start(); 
?>

<!-- Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">🏅 Kelola Grade</h1>
    <p class="text-gray-600 mt-1">Manajemen grade karyawan dan kuota galon</p>
</div>

<!-- Alert Messages -->
<?php if (isset($_GET['success'])): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    <?php if ($_GET['success'] == '1'): ?>
        ✅ Grade berhasil ditambahkan!
    <?php elseif ($_GET['success'] == '2'): ?>
        ✅ Grade berhasil diperbarui!
    <?php elseif ($_GET['success'] == '3'): ?>
        ✅ Grade berhasil dihapus!
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    <?php if ($_GET['error'] == 'in_use'): ?>
        ❌ Tidak dapat menghapus! Grade ini sedang digunakan oleh karyawan. Hapus/ubah grade karyawan terlebih dahulu.
    <?php else: ?>
        ❌ Terjadi kesalahan. Silakan coba lagi.
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Action Button -->
<div class="mb-6 flex justify-end">
    <a href="?url=grades/create" 
       class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition">
        + Tambah Grade Baru
    </a>
</div>

<!-- Grades Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-indigo-600 text-white">
            <tr>
                <th class="py-4 px-6 text-left text-lg font-semibold">No</th>
                <th class="py-4 px-6 text-left text-lg font-semibold">Nama Grade</th>
                <th class="py-4 px-6 text-left text-lg font-semibold">Kuota Galon/Bulan</th>
                <th class="py-4 px-6 text-center text-lg font-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($grades)): ?>
                <tr>
                    <td colspan="4" class="py-8 text-center text-gray-500">
                        Belum ada data grade. Klik "Tambah Grade Baru" untuk mulai.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($grades as $index => $grade): ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="py-4 px-6 text-lg"><?= $index + 1 ?></td>
                    <td class="py-4 px-6 text-lg font-semibold"><?= htmlspecialchars($grade['name_grade']) ?></td>
                    <td class="py-4 px-6 text-lg"><?= $grade['default_allowance'] ?> galon</td>
                    <td class="py-4 px-6 text-center">
                        <a href="?url=grades/edit/<?= $grade['id_grade'] ?>" 
                           class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition inline-block">
                            ✏️ Edit
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>
