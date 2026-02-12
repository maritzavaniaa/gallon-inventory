<?php 
$pageTitle = 'Laporan Distribusi';
ob_start(); 

$hasFilters = !empty($_GET['search']) || !empty($_GET['division']) || !empty($_GET['date_start']) || !empty($_GET['date_end']);
?>

<!-- Page Header and Expprt Button -->
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold mb-2" style="color: #1F4396;">Laporan Intern Gallon</h1>
        <p class="text-lg text-gray-700">Riwayat masuk intern gallon.</p>
    </div>

    <?php 
    $exportParams = $_GET;
    unset($exportParams['url']); 
    ?>
    
    <div class="flex-shrink-0">
        <a href="?url=reports/in/export&<?= http_build_query($exportParams) ?>" 
        class="inline-flex items-center gap-3 text-white font-semibold text-md px-5 py-2 rounded-md shadow-md transition-all hover:shadow-xl"
        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); min-height: 56px; white-space: nowrap;">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Export CSV</span>
        </a>
    </div>
</div>

<?php
// 1. Search Bar
ob_start();
$label = 'Cari Referensi';
$placeholder = 'Masukkan No. DO';
$isNumeric = false;
include '../app/views/components/search-bar.php';
$searchContent = ob_get_clean();

ob_start(); ?>
    <div class="md:col-span-6">
        <?php 
        // Filter Period
        $nameStart = 'date_start';
        $nameEnd = 'date_end';
        include '../app/views/components/date-filter.php'; 
        ?>
    </div>

    <div class="md:col-span-3">
        <?php 
        // Pagination
        $label = 'Tampilkan';
        $name = 'limit';
        $showAll = false;
        $options = ['10' => '10 Data', '50' => '50 Data', '100' => '100 Data', '200' => '200 Data'];
        include '../app/views/components/filter-dropdown.php'; 
        ?>
    </div>
<?php 
$filterContent = ob_get_clean();

// 3. Render Container Filter
include '../app/views/components/filter-container.php';

// 4. Render Summary Cards
include '../app/views/components/summary-cards.php';
?>

<?php if (isset($_GET['success']) && $_GET['success'] == '3'): ?>
<div class="bg-green-50 border-2 border-green-500 text-gray-900 px-6 py-4 rounded-lg mb-6 text-lg">
    <strong class="font-bold text-green-700">Berhasil!</strong>
    Transaksi telah dibatalkan dan stok galon telah dikembalikan.
</div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] == 'wrong_password'): ?>
<div class="bg-red-50 border-2 border-red-500 text-gray-900 px-6 py-4 rounded-lg mb-6 text-lg">
    <strong class="font-bold text-red-700">Gagal!</strong>
    Password admin salah. Transaksi tidak dapat dibatalkan.
</div>
<?php endif; ?>

<div class="bg-white rounded-md overflow-hidden border-2 border-gray-200 shadow-sm mt-6">
    <table class="w-full text-base">
        <thead>
            <tr style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <th class="py-4 px-3 text-left text-white font-semibold border-r border-white border-opacity-20" style="width: 5%;">NO</th>
                <th class="py-4 px-4 text-left text-white font-semibold border-r border-white border-opacity-20" style="width: 15%;">TANGGAL</th>
                <th class="py-4 px-4 text-left text-white font-semibold border-r border-white border-opacity-20" style="width: 25%;">REF. DOKUMEN IM</th>
                <th class="py-4 px-3 text-center text-white font-semibold border-r border-white border-opacity-20" style="width: 10%;">MASUK</th>
                <th class="py-4 px-4 text-left text-white font-semibold border-r border-white border-opacity-20" style="width: 25%;">CATATAN</th>
                <th class="py-4 px-4 text-center text-white font-semibold" style="width: 15%;">ADMIN</th>
                
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <th class="py-4 px-4 text-center text-white font-semibold" style="width: 10%;">AKSI</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reports)): ?>
                <tr>
                    <td colspan="<?= $_SESSION['role'] === 'admin' ? '7' : '6' ?>" class="py-10 text-center text-gray-500 font-medium italic">
                        Data tidak ditemukan.
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                $no = ($filters['page'] - 1) * $filters['limit'] + 1;
                foreach($reports as $r): 
                ?>
                <tr class="border-b-2 border-gray-100 hover:bg-green-50 transition-colors">
                    <td class="py-3 px-3 text-sm text-gray-500 font-medium text-center"><?= $no++ ?></td>
                    <td class="py-3 px-4 text-sm text-gray-700"><?= date('d/m/y', strtotime($r['date_tsx'])) ?></td>
                    <td class="py-3 px-4 font-bold text-sm text-gray-900 uppercase"><?= htmlspecialchars($r['reference_doc'] ?: '-') ?></td>
                    <td class="py-3 px-3 text-center">
                        <span class="text-green-700 px-2 py-0.5 text-lg font-bold italic">+ <?= $r['quantity'] ?></span>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-600 italic"><?= htmlspecialchars($r['notes'] ?: '-') ?></td>
                    <td class="py-3 px-4 text-center text-xs font-semibold text-gray-400 uppercase italic">
                        <?= htmlspecialchars($r['admin_name']) ?>
                    </td>

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                    <td class="py-3 px-4">
                        <div class="flex justify-center">
                            <button type="button"
                                    onclick='confirmDeleteIn(<?= json_encode($r["id_tsx"]) ?>, <?= json_encode($r["reference_doc"]) ?>, <?= json_encode($r["quantity"]) ?>, <?= json_encode(date("d/m/Y", strtotime($r["date_tsx"]))) ?>)'
                                    class="p-2 bg-red-100 text-red-600 hover:bg-red-500 hover:text-white rounded-lg transition-all shadow-sm"
                                    title="Batalkan Transaksi">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?> 
        </tbody>
    </table>
</div>

<!-- Delete confirmation modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-70 hidden flex items-center justify-center z-[9999]" onclick="if(event.target===this) closeDeleteModal()">
    <div class="bg-white rounded-md shadow-md p-10 max-w-xl w-full mx-4">
        <form id="deleteForm" method="POST" action="">
            <div class="text-center mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-3 uppercase">KONFIRMASI PEMBATALAN MASUK</h2>
                <p class="text-md text-gray-800 mb-4 leading-relaxed">
                    Apakah Anda <strong class="text-red-600">YAKIN</strong> ingin membatalkan transaksi masuk ini?
                </p>
                <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-4 mb-6 text-center">
                    <p class="text-xs text-yellow-700 font-bold uppercase tracking-widest mb-1">Detail Transaksi</p>
                    <p class="text-xl font-bold text-gray-900" id="delRef"></p>
                    <p class="text-base text-gray-700 border-t border-yellow-200 mt-2 pt-2" id="delInfo"></p>
                </div>
                <input type="password" name="admin_password" required placeholder="Masukkan Password Admin" class="w-full px-4 py-4 border-2 border-gray-200 rounded-md focus:border-red-500 focus:outline-none text-center text-lg shadow-inner bg-gray-50 mb-6">
            </div>
            <div class="flex flex-col gap-4">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-5 rounded-md shadow-lg uppercase">YA, BATALKAN STOK MASUK</button>
                <button type="button" onclick="closeDeleteModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-900 font-bold py-5 rounded-md uppercase">KEMBALI</button>
            </div>
        </form>
    </div>
</div>

<script>
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    let timeout = null;
    searchInput.addEventListener('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            searchInput.closest('form').submit();
        }, 800); 
    });
}

function confirmDeleteIn(id, ref, qty, date) {
    console.log("ID:", id, "Ref:", ref); 
    const refText = ref ? ref : '-';
    document.getElementById('delRef').textContent = "Ref: " + refText;
    document.getElementById('delInfo').textContent = qty + ' Galon Masuk - ' + date;

    document.getElementById('deleteForm').action = '?url=reports/in/delete/' + id;

    const modal = document.getElementById('deleteModal');
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>

<?php 
$baseUrl = '?url=reports/in';
$params = $_GET; 
unset($params['url'], $params['page']); 
include '../app/views/components/pagination.php'; 
?>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>