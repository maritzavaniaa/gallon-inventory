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

    <?php 
    $exportParams = $_GET;
    unset($exportParams['url']); 
    ?>
    
    <div class="flex-shrink-0">
        <a href="?url=reports/out/export&<?= http_build_query($exportParams) ?>" 
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

<div class="bg-white rounded-md overflow-hidden border-2 border-gray-200 shadow-sm mt-6">
    <table class="w-full text-base">
        <thead>
            <tr style="background: linear-gradient(135deg, #1F4396 0%, #2E5BBE 100%);">
                <th class="py-4 px-3 text-left text-white font-semibold border-r border-white border-opacity-20" style="width: 5%;">NO</th>
                <th class="py-4 px-4 text-left text-white font-semibold border-r border-white border-opacity-20" style="width: 10%;">TANGGAL</th>
                <th class="py-4 px-4 text-left text-white font-semibold border-r border-white border-opacity-20" style="width: 20%;">PENERIMA</th>
                <th class="py-4 px-3 text-center text-white font-semibold border-r border-white border-opacity-20" style="width: 8%;">GRADE</th>
                <th class="py-4 px-3 text-center text-white font-semibold border-r border-white border-opacity-20" style="width: 8%;">KUOTA</th>
                <th class="py-4 px-3 text-center text-white font-semibold border-r border-white border-opacity-20" style="width: 8%;">AMBIL</th>
                <th class="py-4 px-4 text-center text-white font-semibold border-r border-white border-opacity-20" style="width: 12%;">ADMIN</th>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <th class="py-4 px-4 text-center text-white font-semibold" style="width: 10%;">AKSI</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reports)): ?>
                <?php else: ?>
                <?php 
                $no = ($filters['page'] - 1) * $filters['limit'] + 1;
                foreach($reports as $r): 
                ?>
                <tr class="border-b-2 border-gray-100 hover:bg-blue-50 transition-colors">
                    <td class="py-3 px-3 text-sm text-gray-500 font-medium text-center"><?= $no++ ?></td>
                    <td class="py-3 px-4 text-sm text-gray-700"><?= date('d/m/y', strtotime($r['date_tsx'])) ?></td>
                    <td class="py-3 px-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-sm text-gray-900 uppercase leading-tight"><?= htmlspecialchars($r['name_subject']) ?></span>
                            <span class="text-xs font-semibold text-blue-500 uppercase tracking-tighter italic">
                                ID: <?= htmlspecialchars($r['id_share']) ?> | <?= htmlspecialchars($r['division'] ?: 'No Div') ?>
                            </span>
                        </div>
                    </td>
                    <td class="py-3 px-3 text-center text-lg font-semibold text-gray-600"><?= htmlspecialchars($r['name_grade'] ?? '-') ?></td>
                    <td class="py-3 px-3 text-center text-lg font-semibold text-gray-600"><?= htmlspecialchars($r['amount'] ?? '-') ?></td>
                    
                    <td class="py-3 px-3 text-center">
                        <span class="text-blue-800 px-2 py-0.5 text-lg font-bold italic">
                            <?= $r['quantity'] ?>
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center text-xs font-semibold text-gray-400 uppercase italic">
                        <?= htmlspecialchars($r['admin_name']) ?>
                    </td>

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                    <td class="py-3 px-4">
                        <div class="flex justify-center gap-1.5">
                            <a href="?url=transactions/edit/<?= $r['id_tsx'] ?>" 
                               class="p-2 bg-yellow-100 text-yellow-600 hover:bg-yellow-500 hover:text-white rounded-lg transition-all shadow-sm"
                               title="Edit Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <button type="button"
                                    onclick="confirmDeleteTsx('<?= $r['id_tsx'] ?>')"
                                    class="p-2 bg-red-100 text-red-600 hover:bg-red-500 hover:text-white rounded-lg transition-all shadow-sm"
                                    title="Hapus Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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