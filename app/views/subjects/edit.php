<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-6 py-8 max-w-3xl">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">✏️ Edit Data</h1>
            <p class="text-xl text-gray-600 mb-8"><?= htmlspecialchars($subject['name_subject']) ?></p>
            
            <?php if ($hasTransaction ?? false): ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 mb-6">
                <p class="font-semibold">⚠️ Data ini memiliki riwayat transaksi</p>
                <p class="text-sm mt-1">ID Share tidak dapat dihapus. Gunakan status "Non-aktif" jika sudah tidak aktif.</p>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="?url=subjects/update/<?= $subject['id_share'] ?>" class="space-y-6">
                <!-- ID Share (Read-only) -->
                <div class="bg-gray-100 p-4 rounded-lg">
                    <label class="block text-lg font-semibold mb-2">ID Share</label>
                    <p class="text-2xl font-mono font-bold text-gray-700"><?= htmlspecialchars($subject['id_share']) ?></p>
                    <p class="text-sm text-gray-500 mt-1">⚠️ ID tidak dapat diubah</p>
                </div>
                
                <!-- Tipe (Read-only) -->
                <div class="bg-gray-100 p-4 rounded-lg">
                    <label class="block text-lg font-semibold mb-2">Tipe</label>
                    <p class="text-xl font-semibold">
                        <span class="px-4 py-2 rounded-full <?= $subject['type_subject'] == 'karyawan' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' ?>">
                            <?= ucfirst($subject['type_subject']) ?>
                        </span>
                    </p>
                    <p class="text-sm text-gray-500 mt-1">Tipe tidak dapat diubah setelah dibuat</p>
                    <input type="hidden" name="type_subject" value="<?= $subject['type_subject'] ?>">
                </div>
                
                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-lg font-semibold mb-3">Nama Lengkap *</label>
                    <input type="text" name="name_subject" value="<?= htmlspecialchars($subject['name_subject']) ?>" required 
                           class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300">
                </div>
                
                <!-- Divisi -->
                <div>
                    <label class="block text-lg font-semibold mb-3">Divisi</label>
                    <input type="text" name="division" value="<?= htmlspecialchars($subject['division'] ?? '') ?>" 
                           class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300">
                </div>
                
                <!-- Grade (Karyawan) atau Kuota (Non-Karyawan) -->
                <?php if ($subject['type_subject'] == 'karyawan'): ?>
                <div>
                    <label class="block text-lg font-semibold mb-3">Grade *</label>
                    <select name="id_grade" required
                            class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300">
                        <option value="">-- Pilih Grade --</option>
                        <?php foreach ($grades as $grade): ?>
                            <option value="<?= $grade['id_grade'] ?>" <?= $subject['id_grade'] == $grade['id_grade'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($grade['name_grade']) ?> - <?= $grade['default_allowance'] ?> galon/bulan
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="default_allowance" value="">
                </div>
                <?php else: ?>
                <div>
                    <label class="block text-lg font-semibold mb-3">Kuota Galon per Bulan *</label>
                    <input type="number" name="default_allowance" value="<?= $subject['default_allowance'] ?>" min="0" required
                           class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300">
                    <input type="hidden" name="id_grade" value="">
                </div>
                <?php endif; ?>
                
                <!-- Status -->
                <div>
                    <label class="block text-lg font-semibold mb-3">Status *</label>
                    <select name="status" id="status" required
                            class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300"
                            onchange="toggleInactiveNote()">
                        <option value="aktif" <?= $subject['status'] == 'aktif' ? 'selected' : '' ?>>✅ Aktif</option>
                        <option value="non_aktif" <?= $subject['status'] == 'non_aktif' ? 'selected' : '' ?>>❌ Non-aktif</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-2">
                        Status "Non-aktif" akan menonaktifkan kuota, namun riwayat transaksi tetap tersimpan
                    </p>
                </div>
                
                <!-- Alasan Non-aktif (conditional) -->
                <div id="inactive_note_field" style="display:<?= $subject['status'] == 'non_aktif' ? 'block' : 'none' ?>;">
                    <label class="block text-lg font-semibold mb-3">Alasan Non-aktif</label>
                    <textarea name="inactive_note" rows="3" 
                              placeholder="Contoh: Pengunduran diri, Pensiun, Meninggal dunia" 
                              class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300"><?= htmlspecialchars($subject['inactive_note'] ?? '') ?></textarea>
                    <p class="text-sm text-gray-500 mt-2">Alasan valid: Pengunduran diri, Pensiun, Meninggal dunia</p>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col space-y-3 pt-6 border-t-2">
                    <button type="submit" 
                            class="w-full bg-green-500 hover:bg-green-600 text-white text-xl font-bold py-4 rounded-lg transition">
                        💾 Simpan Perubahan
                    </button>
                    
                    <a href="?url=subjects" 
                       class="w-full bg-blue-500 hover:bg-blue-600 text-white text-xl font-bold py-4 rounded-lg text-center transition">
                        🏠 Kembali ke Daftar
                    </a>
                    
                    <?php if (!$hasTransaction): ?>
                    <a href="?url=subjects/delete/<?= $subject['id_share'] ?>" 
                       onclick="return confirm('⚠️ YAKIN HAPUS PERMANEN?\n\nData: <?= htmlspecialchars($subject['name_subject']) ?>\n\nData akan dihapus selamanya!')" 
                       class="w-full bg-red-500 hover:bg-red-600 text-white text-xl font-bold py-4 rounded-lg text-center transition">
                        🗑️ Hapus Data Permanen
                    </a>
                    <?php else: ?>
                    <div class="w-full bg-gray-300 text-gray-600 text-xl font-bold py-4 rounded-lg text-center">
                        🔒 Hapus Tidak Tersedia (Ada Riwayat Transaksi)
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function toggleInactiveNote() {
            const status = document.getElementById('status').value;
            const field = document.getElementById('inactive_note_field');
            field.style.display = status === 'non_aktif' ? 'block' : 'none';
        }
    </script>
</body>
</html>
