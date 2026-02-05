<?php 
$pageTitle = 'Laporan Distribusi';
ob_start(); 

$hasFilters = !empty($_GET['search']) || !empty($_GET['division']) || !empty($_GET['date_start']) || !empty($_GET['date_end']);
?>

<!-- Page Header and Expprt Button -->
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold mb-2" style="color: #1F4396;">Laporan Distribusi</h1>
        <p class="text-lg text-gray-700">Riwayat pengambilan gallon oleh karyawan & non-karyawan.</p>
    </div>
    
    <div class="flex-shrink-0">
        <a href="?url=users/create" 
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
$label = 'Cari ID Share';
$placeholder = 'Masukkan ID Share...';
$isNumeric = true; 
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
        // Filter Division
        $label = 'Divisi';
        $name = 'division';
        $options = !empty($divisions) ? array_combine($divisions, $divisions) : [];
        include '../app/views/components/filter-dropdown.php'; 
        ?>
    </div>

    <div class="md:col-span-3">
        <?php 
        // Pagination
        $label = 'Tampilkan';
        $name = 'limit';
        $options = ['10' => '10 Data', '50' => '50 Data', '100' => '100 Data', '200' => '200 Data'];
        include '../app/views/components/filter-dropdown.php'; 
        ?>
    </div>
<?php $filterContent = ob_get_clean();

// 3. Render Container Filter
include '../app/views/components/filter-container.php';
?>

<div class="bg-white rounded-md overflow-hidden border-2 border-gray-200 shadow-sm mt-6">
    <table class="w-full text-base">
        <thead>
            <tr style="background: linear-gradient(135deg, #1F4396 0%, #2E5BBE 100%);">
                <th class="py-4 px-5 text-left text-white font-semibold border-r border-white border-opacity-20" style="width: 5%;">NO</th>
                <th class="py-4 px-5 text-left text-white font-semibold border-r border-white border-opacity-20" style="width: 12%;">TANGGAL</th>
                <th class="py-4 px-5 text-left text-white font-semibold border-r border-white border-opacity-20" style="width: 15%;">ID SHARE</th>
                <th class="py-4 px-5 text-left text-white font-semibold border-r border-white border-opacity-20" style="width: 25%;">PENERIMA</th>
                <th class="py-4 px-5 text-center text-white font-semibold border-r border-white border-opacity-20" style="width: 10%;">JUMLAH</th>
                <th class="py-4 px-5 text-center text-white font-semibold border-r border-white border-opacity-20" style="width: 13%;">ADMIN</th>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <th class="py-4 px-5 text-center text-white font-semibold" style="width: 20%;">AKSI</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reports)): ?>
                <tr>
                    <td colspan="<?= $_SESSION['role'] === 'admin' ? '7' : '6' ?>" class="py-16 text-center">
                        <p class="text-gray-600 text-xl font-semibold mb-2">Belum Ada Data Transaksi</p>
                        <p class="text-gray-500 text-lg">Data distribusi galon akan muncul di sini setelah diinput</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                $no = ($filters['page'] - 1) * $filters['limit'] + 1;
                foreach($reports as $r): 
                ?>
                <tr class="border-b-2 border-gray-100 hover:bg-blue-50 transition-colors">
                    <td class="py-4 px-5 text-sm text-gray-500 font-medium"><?= $no++ ?></td>
                    
                    <td class="py-4 px-5 text-sm text-gray-700"><?= date('d/m/Y', strtotime($r['date_tsx'])) ?></td>
                    
                    <td class="py-4 px-5 font-mono font-bold text-blue-800 text-sm italic"><?= htmlspecialchars($r['id_share']) ?></td>
                    
                    <td class="py-3 px-5">
                        <div class="flex flex-col">
                            <span class="font-bold text-sm text-gray-900 uppercase"><?= htmlspecialchars($r['name_subject']) ?></span>
                            <span class="text-[10px] font-bold text-blue-500 uppercase tracking-tighter italic">
                                <?= htmlspecialchars($r['division'] ?: 'No Division') ?>
                            </span>
                        </div>
                    </td>
                    
                    <td class="py-3 px-5 text-center">
                        <div class="inline-block bg-blue-50 text-blue-800 px-3 py-1 rounded-lg border-2 border-blue-100 font-black text-lg">
                            <?= $r['quantity'] ?>
                        </div>
                    </td>

                    <td class="py-3 px-5 text-center text-xs font-semibold text-gray-500 uppercase italic">
                        <?= htmlspecialchars($r['admin_name']) ?>
                    </td>

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                    <td class="py-3 px-5">
                        <div class="flex justify-center gap-2">
                            <a href="?url=transactions/edit/<?= $r['id_tsx'] ?>" 
                               class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold text-xs px-3 py-1.5 rounded shadow-sm transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                EDIT
                            </a>

                            <button type="button"
                                    onclick="confirmDeleteTsx('<?= $r['id_tsx'] ?>')"
                                    class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white font-semibold text-xs px-3 py-1.5 rounded shadow-sm transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                HAPUS
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

<?php 
$baseUrl = '?url=reports/out';
$params = $_GET; 
unset($params['url'], $params['page']); 
include '../app/views/components/pagination.php'; 
?>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>