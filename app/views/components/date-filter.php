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
         <label class="text-md font-semibold text-gray-500 uppercase mb-1 ml-1">
             <?= htmlspecialchars($labelStart) ?>
         </label>
         <div class="relative">
             <input type="date" 
                    name="<?= htmlspecialchars($nameStart) ?>" 
                    value="<?= htmlspecialchars($valueStart) ?>"
                    class="block w-full px-3 py-2 border-2 border-gray-100 rounded-md bg-white text-sm font-medium text-gray-700 focus:border-[#1F4396] focus:outline-none transition-all cursor-pointer"
                    onchange="this.form.submit()">
         </div>
     </div>
 
     <div class="flex flex-col">
         <label class="text-md font-semibold text-gray-500 uppercase mb-1 ml-1">
             <?= htmlspecialchars($labelEnd) ?>
         </label>
         <div class="relative">
             <input type="date" 
                    name="<?= htmlspecialchars($nameEnd) ?>" 
                    value="<?= htmlspecialchars($valueEnd) ?>"
                    class="block w-full px-3 py-2 border-2 border-gray-100 rounded-md bg-white text-sm font-medium text-gray-700 focus:border-[#1F4396] focus:outline-none transition-all cursor-pointer"
                    onchange="this.form.submit()">
         </div>
     </div>
 </div>