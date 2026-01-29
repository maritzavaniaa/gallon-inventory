<?php
/**
 * Reusable Filter Dropdown Component
 * 
 * @param string $label - Filter label
 * @param string $name - Select name attribute
 * @param array $options - Array of ['value' => 'label'] options
 * @param string $selected - Currently selected value
 * @param bool $showAll - Show "Semua" option (default: true)
 */

 $displayLabel = $label ?? 'Filter';
 $name = $name ?? 'filter';
 $options = $options ?? [];
 $selected = $_GET[$name] ?? ''; 
 $showAll = $showAll ?? true;
 ?>
 
 <div class="flex flex-col relative">
    <label class="text-md font-semibold text-gray-500 uppercase mb-1 ml-1"><?= htmlspecialchars($displayLabel) ?></label>
    
    <div class="relative">
        <select name="<?= htmlspecialchars($name) ?>" 
                class="block w-full pl-3 pr-10 py-2 border-2 border-gray-100 rounded-md bg-white text-sm font-medium text-gray-700 focus:border-[#1F4396] focus:outline-none transition-all cursor-pointer appearance-none"
                onchange="this.form.submit()">
            
            <?php if ($showAll): ?>
                <option value="">Semua</option>
            <?php endif; ?>

            <?php foreach ($options as $value => $optionLabel): ?>
                <option value="<?= htmlspecialchars($value) ?>" 
                    <?= (string)$selected === (string)$value ? 'selected' : '' ?>>
                    <?= htmlspecialchars($optionLabel) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
</div>
