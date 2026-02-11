<?php
/**
 * Reusable Filter Container
 * Wraps filters with consistent styling and reset button
 * 
 * @param string $action - Form action URL
 * @param bool $hasFilters - Whether active filters exist
 */

$action = $action ?? '';
$hasFilters = $hasFilters ?? false;
?>

<form method="GET" action="<?= htmlspecialchars($action) ?>" class="mb-6">
    <?php if (isset($_GET['url'])): ?>
        <input type="hidden" name="url" value="<?= htmlspecialchars($_GET['url'])  ?>">
    <?php endif; ?>
    
    <div class="bg-white rounded-md shadow-md border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-[#1F4396] flex items-center gap-2 uppercase tracking-tight">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Pencarian & Filter
            </h3>
            
            <a href="?url=<?= htmlspecialchars($_GET['url'] ?? 'subjects') ?>" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-md font-semibold text-sm uppercase tracking-widest transition-all duration-200 
               <?= $hasFilters 
                   ? 'bg-red-50 text-red-600 border-2 border-red-100 hover:bg-red-600 hover:text-white hover:border-red-600 shadow-sm' 
                   : 'bg-gray-50 text-gray-400 border-2 border-gray-100 pointer-events-none' ?>">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Bersihkan Filter
            </a>
        </div>
        
        <div class="mb-5">
            <?= $searchContent ?? '' ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <?= $filterContent ?? '' ?>
        </div>
    </div>
</form>

