<div class="mb-4">
    <label class="block text-sm font-semibold text-gray-700 mb-2"><?= $label ?></label>
    <input type="<?= $type ?? 'text' ?>" 
           name="<?= $name ?>" 
           value="<?= $value ?? '' ?>"
           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
           <?= $required ?? '' ?>
           placeholder="<?= $placeholder ?? '' ?>">
</div>