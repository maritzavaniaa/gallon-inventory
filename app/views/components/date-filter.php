<?php
/**
 * Reusable Date Range Filter Component
 * 
 * @param string $labelStart - Start date label
 * @param string $labelEnd - End date label
 * @param string $nameStart - Start date input name (default: 'date_start')
 * @param string $nameEnd - End date input name (default: 'date_end')
 * @param string $valueStart - Current start date value
 * @param string $valueEnd - Current end date value
 */

$labelStart = $labelStart ?? 'Dari Tanggal';
$labelEnd = $labelEnd ?? 'Sampai Tanggal';
$nameStart = $nameStart ?? 'date_start';
$nameEnd = $nameEnd ?? 'date_end';
$valueStart = $valueStart ?? ($_GET[$nameStart] ?? '');
$valueEnd = $valueEnd ?? ($_GET[$nameEnd] ?? '');
?>

<div class="grid grid-cols-2 gap-4">
    <div class="flex flex-col">
        <label class="text-sm font-semibold text-gray-700 mb-1.5"><?= htmlspecialchars($labelStart) ?></label>
        <input type="date" 
               name="<?= htmlspecialchars($nameStart) ?>" 
               value="<?= htmlspecialchars($valueStart) ?>"
               class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1F4396] focus:border-transparent transition duration-200"
               onchange="this.form.submit()">
    </div>
    <div class="flex flex-col">
        <label class="text-sm font-semibold text-gray-700 mb-1.5"><?= htmlspecialchars($labelEnd) ?></label>
        <input type="date" 
               name="<?= htmlspecialchars($nameEnd) ?>" 
               value="<?= htmlspecialchars($valueEnd) ?>"
               class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1F4396] focus:border-transparent transition duration-200"
               onchange="this.form.submit()">
    </div>
</div>
