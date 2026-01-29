<?php 
$pageTitle = 'Data Penerima - Sistem Galon';
ob_start(); 

// --- LOGIC FILTER CONTENT (Slim Version) ---
ob_start(); ?>
    <div class="w-full">
        <?php 
        $label = 'Cari Penerima'; 
        $placeholder = 'Ketik Nama atau ID Share...'; 
        include '../app/views/components/search-bar.php'; 
        ?>
    </div>
<?php 
$searchContent = ob_get_clean(); 

// Baris 2: Dropdowns (Tipe, Status, Divisi)
ob_start(); ?>
    <?php 
        $label = 'Kategori'; $name = 'type'; 
        $options = ['karyawan' => 'Karyawan', 'non_karyawan' => 'Non-Karyawan'];
        include '../app/views/components/filter-dropdown.php'; 
    ?>
    <?php 
        $label = 'Status'; $name = 'status'; 
        $options = ['aktif' => 'Aktif', 'non_aktif' => 'Non-Aktif'];
        include '../app/views/components/filter-dropdown.php'; 
    ?>
    <?php 
        $label = 'Divisi'; $name = 'division'; 
        $options = [];
        foreach ($divisions as $div) { $options[$div['division']] = $div['division']; }
        include '../app/views/components/filter-dropdown.php'; 
    ?>
<?php 
$filterContent = ob_get_clean(); 
?>

<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold mb-2" style="color: #1F4396;">Data Penerima</h1>
        <p class="text-lg text-gray-700">Kelola data penerima, divisi, dan kuota galon</p>
    </div>
    
    <div class="flex-shrink-0">
        <a href="?url=subjects/create" 
           class="inline-flex items-center gap-3 text-white font-semibold text-md px-5 py-2 rounded-md shadow-md transition-all hover:shadow-xl"
           style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); min-height: 56px; white-space: nowrap;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Penerima Baru</span>
        </a>
    </div>
</div>

<?php include '../app/views/components/filter-container.php'; ?>

<?php if (isset($_GET['success'])): ?>
<div class="bg-green-50 border-2 border-green-500 text-gray-900 px-6 py-4 rounded-lg mb-6 text-lg">
    <strong class="font-bold text-green-700">Berhasil!</strong>
    <?php 
    $msg = [1 => 'Data penerima berhasil ditambahkan!', 2 => 'Data berhasil diperbarui!', 3 => 'Data berhasil dihapus!'];
    echo $msg[$_GET['success']] ?? 'Operasi berhasil!';
    ?>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="bg-red-50 border-2 border-red-500 text-gray-900 px-6 py-4 rounded-lg mb-6 text-lg">
    <strong class="font-bold text-red-700">Gagal!</strong>
    <?php if ($_GET['error'] == 'has_transaction'): ?>
        Data tidak bisa dihapus karena memiliki riwayat transaksi. Gunakan fitur "Non-aktifkan" saja.
    <?php else: ?>
        Terjadi kesalahan sistem. Silakan coba lagi.
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="bg-white rounded-md overflow-hidden border-2 border-gray-200 shadow-sm">
    <table class="w-full text-base">
        <thead>
            <tr style="background: linear-gradient(135deg, #1F4396 0%, #2E5BBE 100%);">
                <th class="py-3 px-5 text-left text-white font-semibold border-r border-white border-opacity-20" style="width: 12%;">ID SHARE</th>
                <th class="py-3 px-5 text-left text-white font-semibold border-r border-white border-opacity-20" style="width: 23%;">NAMA</th>
                <th class="py-3 px-5 text-left text-white font-semibold border-r border-white border-opacity-20" style="width: 15%;">DIVISI</th>
                <th class="py-3 px-5 text-center text-white font-semibold border-r border-white border-opacity-20" style="width: 15%;">STATUS</th>
                <th class="py-3 px-5 text-center text-white font-semibold border-r border-white border-opacity-20" style="width: 15%;">GRADE/KUOTA</th>
                <th class="py-3 px-5 text-center text-white font-semibold" style="width: 20%;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($subjects)): ?>
                <tr>
                    <td colspan="6" class="py-16 text-center text-gray-500 italic">Data tidak ditemukan...</td>
                </tr>
            <?php else: ?>
                <?php foreach ($subjects as $subject): ?>
                <tr class="border-b-2 border-gray-100 hover:bg-blue-50 transition-colors <?= $subject['status'] != 'aktif' ? 'bg-gray-50' : '' ?>">
                    <td class="py-4 px-5 font-mono font-bold text-blue-800 text-sm"><?= htmlspecialchars($subject['id_share']) ?></td>
                    
                    <!-- Nama Penerima & Tipe -->
                    <td class="py-4 px-5">
                        <div class="font-bold text-gray-900 mb-1"><?= htmlspecialchars($subject['name_subject']) ?></div>
                        <?php if ($subject['type_subject'] == 'karyawan'): ?>
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-600 border border-blue-200">
                                Karyawan
                            </span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-600 border border-purple-200">
                                Non-Karyawan
                            </span>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Divisi -->
                    <td class="py-4 px-5 text-gray-700 font-medium uppercase"><?= htmlspecialchars($subject['division'] ?: '-') ?></td>
                    
                    <!-- Status -->
                    <td class="py-4 px-5 text-center">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold border-2
                            <?= $subject['status'] == 'aktif' 
                                ? 'bg-green-100 text-green-800 border-green-200' 
                                : 'bg-red-100 text-red-800 border-red-200' ?>">
                            <?= strtoupper($subject['status']) ?>
                        </span>
                    </td>

                    <!-- Monthly Allowance / Grade -->
                    <td class="py-4 px-5 text-center">
                        <?php if ($subject['type_subject'] == 'karyawan'): ?>
                            <div class="inline-block bg-blue-50 text-blue-700 px-3 py-1 rounded border-2 border-blue-200 font-bold text-xs uppercase">
                                <?= htmlspecialchars($subject['name_grade'] ?: 'No Grade') ?>
                            </div>
                        <?php else: ?>
                            <div class="text-sm font-bold text-purple-700"><?= $subject['default_allowance'] ?> Galon</div>
                        <?php endif; ?>
                    </td>

                    <!-- Action Buttons -->
                    <td class="py-4 px-5">
                        <div class="flex justify-center gap-2">
                            <a href="?url=subjects/edit/<?= $subject['id_share'] ?>" 
                               class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold text-xs px-3 py-2 rounded shadow-sm transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                EDIT
                            </a>

                            <button type="button"
                                    onclick="confirmDelete('<?= $subject['id_share'] ?>', '<?= htmlspecialchars($subject['name_subject'], ENT_QUOTES) ?>')"
                                    class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white font-semibold text-xs px-3 py-2 rounded shadow-sm transition-all">
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

<?php 
if (isset($pagination) && $pagination['totalPages'] > 1):
    $baseUrl = '?url=subjects';
    $params = array_filter([
        'search' => $_GET['search'] ?? '',
        'type' => $_GET['type'] ?? '',
        'status' => $_GET['status'] ?? '',
        'division' => $_GET['division'] ?? ''
    ]);

    $currentPage = $pagination['currentPage'];
    $totalPages = $pagination['totalPages'];

    include '../app/views/components/pagination.php'; 
endif;
?>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50" onclick="if(event.target===this) closeDeleteModal()">
    <div class="bg-white rounded-md shadow-md p-10 max-w-lg w-full mx-4 border-t-8 border-red-600">
        <div class="text-center mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3 tracking-wider">KONFIRMASI PENGHAPUSAN</h2>
            <p class="text-md text-gray-800 mb-4 leading-relaxed">
                Apakah Anda <strong class="text-red-600">YAKIN</strong> ingin menghapus data penerima ini?
            </p>
            <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4 mb-4">
                <p class="text-xl font-bold text-gray-900" id="deleteName"></p>
                <p class="text-sm text-gray-600 font-mono" id="deleteId"></p>
            </div>
            <p class="text-md text-red-600 font-bold bg-red-100 py-2 rounded">
                Data akan dihapus PERMANEN dari sistem!
            </p>
        </div>
        <div class="flex flex-col gap-4">
            <a href="#" id="deleteConfirmLink" class="block bg-red-600 hover:bg-red-700 text-white font-bold text-md py-4 rounded-md text-center transition-all shadow-lg">
                YA, HAPUS SEKARANG
            </a>
            <button onclick="closeDeleteModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-900 font-bold text-md py-4 rounded-md transition-all">
                BATAL, JANGAN HAPUS
            </button>
        </div>
    </div>
</div>

<!-- Info Box -->
<div class="mt-8 bg-blue-50 border-2 border-blue-300 rounded-xl p-6">
    <div class="flex items-start gap-4">
        <svg class="w-8 h-8 text-blue-600 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
        <div>
            <p class="text-gray-900 font-bold text-lg mb-2">Manajemen Data Penerima:</p>
            <ul class="text-gray-800 text-base space-y-1 leading-relaxed">
                <li>• <strong>ID Share</strong> bersifat unik untuk setiap penerima.</li>
                <li>• Data penerima dengan status <strong>Non-aktif</strong> tidak muncul di transaksi harian.</li>
                <li>• Kuota galon karyawan otomatis mengikuti <strong>Grade</strong> yang dipilih.</li>
            </ul>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteName').textContent = name;
    document.getElementById('deleteId').textContent = 'ID Share: ' + id;
    document.getElementById('deleteConfirmLink').href = '?url=subjects/delete/' + id;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}
// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>