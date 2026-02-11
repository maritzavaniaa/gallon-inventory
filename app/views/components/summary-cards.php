<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <?php foreach ($cards as $card): ?>
    <div class="bg-white p-6 rounded-md border-2 border-gray-100 shadow-sm transition-all hover:shadow-md">
        <p class="text-md font-semibold text-gray-500 mb-1 uppercase tracking-wider">
            <?= htmlspecialchars($card['label']) ?>
        </p>
        <div class="flex items-end gap-2">
            <span class="text-4xl font-black <?= $card['color'] ?? 'text-gray-900' ?>">
                <?= number_format($card['value'], 0, ',', '.') ?>
            </span>
            <?php if (isset($card['unit'])): ?>
                <span class="text-gray-400 font-bold mb-1"><?= $card['unit'] ?></span>
            <?php endif; ?>
        </div>
        <?php if (isset($card['subtext'])): ?>
            <p class="text-xs font-medium mt-2 text-gray-400 italic">
                <?= htmlspecialchars($card['subtext']) ?>
            </p>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>