<?php
/**
 * Reusable Pagination Component
 * 
 * @param int    $currentPage   Current page number
 * @param int    $totalPages    Total number of pages
 * @param string $baseUrl       Base URL without page parameter (e.g., '?url=subjects')
 * @param array  $params        Additional GET parameters to preserve (e.g., ['search' => 'john', 'filter' => 'aktif'])
 * @param int    $maxLinks      Maximum number of page links to show (default: 5)
 */

$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$baseUrl = $baseUrl ?? '';
$params = $params ?? [];
$maxLinks = $maxLinks ?? 5;

// Build query string from params
$queryString = '';
if (!empty($params)) {
    $queryString = '&' . http_build_query($params);
}

// Calculate page range to display
$halfMax = floor($maxLinks / 2);
$startPage = max(1, $currentPage - $halfMax);
$endPage = min($totalPages, $currentPage + $halfMax);

// Adjust if at the beginning or end
if ($currentPage <= $halfMax) {
    $endPage = min($totalPages, $maxLinks);
}
if ($currentPage > $totalPages - $halfMax) {
    $startPage = max(1, $totalPages - $maxLinks + 1);
}

// Don't show pagination if only 1 page
if ($totalPages <= 1) {
    return;
}
?>

<div class="flex items-center justify-between bg-white border-2 border-gray-200 rounded-lg p-4 mt-6">
    <!-- Info -->
    <div class="text-sm text-gray-700">
        Halaman <span class="font-semibold text-gray-900"><?= $currentPage ?></span> 
        dari <span class="font-semibold text-gray-900"><?= $totalPages ?></span>
    </div>
    
    <!-- Navigation -->
    <div class="flex items-center gap-2">
        <!-- First Page -->
        <?php if ($currentPage > 1): ?>
            <a href="<?= $baseUrl ?>&page=1<?= $queryString ?>" 
               class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
                </svg>
            </a>
        <?php else: ?>
            <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
                </svg>
            </span>
        <?php endif; ?>
        
        <!-- Previous Page -->
        <?php if ($currentPage > 1): ?>
            <a href="<?= $baseUrl ?>&page=<?= $currentPage - 1 ?><?= $queryString ?>" 
               class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
        <?php else: ?>
            <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </span>
        <?php endif; ?>
        
        <!-- Page Numbers -->
        <div class="hidden md:flex items-center gap-2">
            <?php if ($startPage > 1): ?>
                <span class="px-3 py-2 text-sm text-gray-500">...</span>
            <?php endif; ?>
            
            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <?php if ($i == $currentPage): ?>
                    <span class="px-4 py-2 text-sm font-bold text-white bg-gradient-to-r from-[#1F4396] to-[#2E5BBE] rounded-lg">
                        <?= $i ?>
                    </span>
                <?php else: ?>
                    <a href="<?= $baseUrl ?>&page=<?= $i ?><?= $queryString ?>" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <?= $i ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($endPage < $totalPages): ?>
                <span class="px-3 py-2 text-sm text-gray-500">...</span>
            <?php endif; ?>
        </div>
        
        <!-- Mobile Page Indicator -->
        <div class="md:hidden px-3 py-2 text-sm font-medium text-gray-700">
            <?= $currentPage ?> / <?= $totalPages ?>
        </div>
        
        <!-- Next Page -->
        <?php if ($currentPage < $totalPages): ?>
            <a href="<?= $baseUrl ?>&page=<?= $currentPage + 1 ?><?= $queryString ?>" 
               class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        <?php else: ?>
            <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </span>
        <?php endif; ?>
        
        <!-- Last Page -->
        <?php if ($currentPage < $totalPages): ?>
            <a href="<?= $baseUrl ?>&page=<?= $totalPages ?><?= $queryString ?>" 
               class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        <?php else: ?>
            <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
                </svg>
            </span>
        <?php endif; ?>
    </div>
</div>