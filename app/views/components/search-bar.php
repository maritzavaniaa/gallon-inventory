<?php
/**
 * Reusable Search Bar Component
 * 
 * @param string $placeholder - Search placeholder text
 * @param string $name - Input name attribute (default: 'search')
 * @param string $value - Current search value (default: '')
 * @param string $action - Form action URL (default: current page)
 */

 $placeholder = $placeholder ?? 'Cari data...';
 $name = $name ?? 'search';
 $value = $_GET[$name] ?? '';
 $label = $label ?? 'Pencarian';
?>

<div class="flex flex-col w-full">
    <label class="text-md font-semibold text-gray-500 uppercase mb-1 ml-1"><?= htmlspecialchars($label) ?></label>
    
    <div class="flex items-center gap-2">
        <div class="relative flex-grow">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" 
                   name="<?= htmlspecialchars($name) ?>" 
                   value="<?= htmlspecialchars($value) ?>"
                   placeholder="<?= htmlspecialchars($placeholder) ?>"
                   class="block w-full pl-10 pr-3 py-2 border-2 border-gray-100 rounded-md bg-white text-sm font-medium text-gray-700 focus:border-[#1F4396] focus:ring-0 focus:outline-none transition-all"
                   autocomplete="off"
                   oninput="debounceSearch(this)">
        </div>

        <button type="submit" 
                style="background:#1F4396"
                onmouseover="this.style.background='#163172'"
                onmouseout="this.style.background='#1F4396'"
                class="flex items-center gap-2 px-6 py-2 text-white rounded-md shadow-md hover:shadow-lg transition-all active:scale-95 shrink-0">
            <span class="text-sm font-semibold uppercase tracking-wider">Cari</span>
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </button>
    </div>
</div>

<script>
let searchTimeout;

function debounceSearch(input) {
    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        input.closest('form').submit();
    }, 500);
}

// Opsional: Pindahkan kursor ke akhir teks setelah reload
const searchInput = document.getElementById('live-search-input');
if (searchInput && searchInput.value !== '') {
    searchInput.focus();
    const val = searchInput.value;
    searchInput.value = '';
    searchInput.value = val;
}
</script>