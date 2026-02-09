<?php 
$pageTitle = 'Dashboard';
$range = $_GET['range'] ?? 'week';
ob_start(); 
?>

<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold mb-2" style="color: #1F4396;">Dashboard</h1>
        <p class="text-lg text-gray-700">Ringkasan stok dan aktivitas galon periode ini.</p>
    </div>

    <div class="flex bg-gray-200 p-1 rounded-lg shadow-inner">
        <?php 
        $tabs = ['week' => 'Minggu Ini', 'month' => 'Bulan Ini', 'year' => 'Tahun Ini'];
        foreach($tabs as $key => $lbl): 
        ?>
            <a href="?url=dashboard&range=<?= $key ?>" 
               class="px-5 py-2 rounded-md text-sm font-bold transition-all <?= $range === $key ? 'bg-white shadow text-blue-700' : 'text-gray-500 hover:text-gray-700' ?>">
               <?= $lbl ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php 
$comparisonLabel = [
    'week'  => 'MINGGU',
    'month' => 'BULAN',
    'year'  => 'TAHUN'
];
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    <!-- Stok Gudang -->
    <div class="bg-white rounded-md border border-gray-100 shadow-sm px-6 py-4 relative overflow-hidden group hover:shadow-md transition-shadow duration-300">

        <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-md font-bold text-gray-400 uppercase tracking-widest">
                    Stok Gudang
                </h3>
                <span class="flex h-2 w-2 rounded-full bg-blue-500"></span>
            </div>

            <div class="flex items-end justify-between">
                <div class="flex flex-col">
                    <span class="text-5xl font-black text-gray-900 tracking-tighter">
                        <?= number_format($stock['remaining']) ?>
                    </span>
                    <p class="text-sm font-medium text-gray-400 mt-1">
                        Galon tersedia
                    </p>
                </div>

                <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600 rounded-2xl shadow-inner border border-blue-400/50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Galon Keluar -->
    <div class="bg-white rounded-md border border-gray-100 shadow-sm px-6 py-4 relative overflow-hidden group hover:shadow-md transition-all duration-300">

        <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-md font-bold text-gray-400 uppercase tracking-widest">
                    Galon Keluar
                </h3>
                <span class="flex h-2 w-2 rounded-full bg-red-500"></span>
            </div>

            <div class="flex items-end justify-between">
                <div class="flex flex-col">
                    <span class="text-5xl font-black text-gray-900 tracking-tighter">
                        <?= number_format($stats['total_out']) ?>
                    </span>
                    <div class="flex items-center gap-2 mt-1">
                        <?php $pOut = $stats['pct_out']; ?>
                        <span class="flex items-center text-xs font-bold <?= $pOut >= 0 ? 'text-red-500' : 'text-green-500' ?>">
                            <svg class="w-3 h-3 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="<?= $pOut >= 0 ? 'M10 3a1 1 0 01.707.293l7 7a1 1 0 01-1.414 1.414L11 6.414V17a1 1 0 11-2 0V6.414L3.707 11.707a1 1 0 01-1.414-1.414l7-7A1 1 0 0110 3z' : 'M10 17a1 1 0 01-.707-.293l-7-7a1 1 0 011.414-1.414L9 13.586V3a1 1 0 112 0v10.586l5.293-5.293a1 1 0 111.414 1.414l-7 7A1 1 0 0110 17z' ?>"/>
                            </svg>
                            <?= abs($pOut) ?>%
                        </span>
                        <span class="text-[10px] text-gray-400 font-medium tracking-tight">
                            vs <?= $comparisonLabel[$range] ?> lalu
                        </span>
                    </div>
                </div>

                <div class="p-4 bg-gradient-to-br from-red-50 to-orange-100 text-red-600 rounded-2xl shadow-inner border border-red-400/50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Galon Masuk -->
    <div class="bg-white rounded-md border border-gray-100 shadow-sm px-6 py-4 relative overflow-hidden group hover:shadow-md transition-all duration-300">

        <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-md font-bold text-gray-400 uppercase tracking-widest">
                    Galon Masuk
                </h3>
                <span class="flex h-2 w-2 rounded-full bg-green-500"></span>
            </div>

            <div class="flex items-end justify-between">
                <div class="flex flex-col">
                    <span class="text-5xl font-black text-gray-900 tracking-tighter">
                        <?= number_format($stats['total_in']) ?>
                    </span>
                    <div class="flex items-center gap-2 mt-1">
                        <?php $pIn = $stats['pct_in']; ?>
                        <span class="flex items-center text-xs font-bold <?= $pIn >= 0 ? 'text-green-500' : 'text-red-500' ?>">
                            <svg class="w-3 h-3 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="<?= $pIn >= 0 ? 'M10 3a1 1 0 01.707.293l7 7a1 1 0 01-1.414 1.414L11 6.414V17a1 1 0 11-2 0V6.414L3.707 11.707a1 1 0 01-1.414-1.414l7-7A1 1 0 0110 3z' : 'M10 17a1 1 0 01-.707-.293l-7-7a1 1 0 011.414-1.414L9 13.586V3a1 1 0 112 0v10.586l5.293-5.293a1 1 0 111.414 1.414l-7 7A1 1 0 0110 17z' ?>"/>
                            </svg>
                            <?= abs($pIn) ?>%
                        </span>
                        <span class="text-[10px] text-gray-400 font-medium tracking-tight">
                            vs <?= $comparisonLabel[$range] ?> lalu
                        </span>
                    </div>
                </div>

                <div class="p-4 bg-gradient-to-br from-green-50 to-teal-100 text-green-600 rounded-2xl shadow-inner border border-green-400/50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="bg-white p-6 rounded-md shadow-sm border-2 border-gray-100">
    <div class="flex items-center justify-between mb-8">
        <h3 class="text-xl font-bold text-gray-800">Perbandingan Aktivitas Galon</h3>
        <div class="flex gap-4 text-xs font-bold">
            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-500 rounded-full"></span> KELUAR</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-400 rounded-full"></span> MASUK</span>
        </div>
    </div>
    <div class="relative" style="height: 400px;">
        <canvas id="mainChart"></canvas>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('mainChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartData['labels']) ?>,
            datasets: [
                {
                    label: 'Keluar',
                    data: <?= json_encode($chartData['out']) ?>,
                    backgroundColor: '#3b82f6', 
                    borderRadius: 6,
                    barPercentage: 0.6
                },
                {
                    label: 'Masuk',
                    data: <?= json_encode($chartData['in']) ?>,
                    backgroundColor: '#34d399', 
                    borderRadius: 6,
                    barPercentage: 0.6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { strokeDashArray: 5, color: '#f3f4f6' }
                },
                x: { grid: { display: false } }
            }
        }
    });
</script>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>