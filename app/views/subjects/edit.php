<?php 
$pageTitle = 'Edit Penerima - Inventaris Gallon';
ob_start(); 
?>


<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-md shadow-md p-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-semibold text-gray-800 mb-2">Edit Data Penerima</h1>
        </div>
        
        <!-- Warning if has transaction -->
        <?php if ($hasTransaction ?? false): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <div>
                <p class="font-semibold text-gray-900">Data ini memiliki riwayat transaksi</p>
                <p class="text-sm mt-1 text-gray-700">ID Share tidak dapat dihapus. Gunakan status "Non-aktif" jika sudah tidak aktif.</p>
            </div>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="?url=subjects/update/<?= $subject['id_share'] ?>" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- ID Share -->
                <div>
                    <label class="block text-md font-semibold mb-2 text-gray-900">ID Share</label>
                    <p class="text-xl font-mono font-bold py-2"><?= htmlspecialchars($subject['id_share']) ?></p>
                    <p class="text-sm text-gray-500">ID tidak dapat diubah</p>
                </div>
                 
                <!-- Tipe -->
                <div>
                    <label class="block text-md font-semibold mb-2 text-gray-900">Tipe</label>
                    <p class="my-2">
                        <span class="px-4 py-1.5 text-md font-semibold rounded-full inline-block <?= $subject['type_subject'] == 'karyawan' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' ?>">
                            <?= ucfirst($subject['type_subject']) ?>
                        </span>
                    </p>
                    <p class="text-sm text-gray-500">Tipe tidak dapat diubah</p>
                </div>
            </div>

            <!-- Nama Lengkap -->
            <div>
                <label class="block text-md font-semibold mb-2 text-gray-900">
                    Nama Lengkap<span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name_subject" 
                       value="<?= htmlspecialchars($subject['name_subject']) ?>" 
                       required 
                       class="w-full p-2 text-md border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
            </div>
            
            <!-- Divisi -->
            <div>
                <label class="block text-md font-semibold mb-2 text-gray-900">Divisi</label>
                <input type="text" 
                       name="division" 
                       value="<?= htmlspecialchars($subject['division'] ?? '') ?>" 
                       placeholder="Contoh: Produksi, IT, Maintenance"
                       class="w-full p-2 text-md uppercase border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
            </div>
            
            <!-- Grade (Karyawan) atau Kuota (Non-Karyawan) -->
            <?php if ($subject['type_subject'] == 'karyawan'): ?>
            <div>
                <label class="block text-md font-semibold mb-2 text-gray-900">
                    Grade<span class="text-red-500">*</span>
                </label>
                <select name="id_grade" 
                        required
                        class="w-full p-2 text-md border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
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
                <label class="block text-md font-semibold mb-2 text-gray-900">
                    Kuota Galon per Bulan<span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       name="default_allowance" 
                       value="<?= $subject['default_allowance'] ?>" 
                       min="0" 
                       required
                       class="w-full p-2 text-md border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
                <input type="hidden" name="id_grade" value="">
            </div>
            <?php endif; ?>
            
            <!-- Status -->
            <div>
                <label class="block text-md font-semibold mb-2 text-gray-900">
                    Status<span class="text-red-500">*</span>
                </label>
                <select name="status" 
                        id="status" 
                        required
                        class="w-full p-2 text-md border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500"
                        onchange="toggleInactiveNote()">
                    <option value="aktif" <?= $subject['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="non_aktif" <?= $subject['status'] == 'non_aktif' ? 'selected' : '' ?>>Non-aktif</option>
                </select>
                <p class="text-sm text-gray-500 mt-2">
                    Status "Non-aktif" akan menonaktifkan kuota, namun riwayat transaksi tetap tersimpan
                </p>
            </div>
            
            <!-- Alasan Non-aktif (conditional) -->
            <div id="inactive_note_field" style="display:<?= $subject['status'] == 'non_aktif' ? 'block' : 'none' ?>;">
                <label class="block text-md font-semibold mb-2 text-gray-900">Alasan Non-aktif</label>
                <textarea name="inactive_note" 
                          rows="2" 
                          placeholder="Contoh: Pengunduran diri, Pensiun, Meninggal dunia" 
                          class="w-full p-2 text-md border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500"><?= htmlspecialchars($subject['inactive_note'] ?? '') ?></textarea>
                <p class="text-sm text-gray-500 mt-2">Alasan valid: Pengunduran diri, Pensiun, Meninggal dunia</p>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col space-y-3 pt-6">
                <!-- Simpan -->
                <button type="submit" 
                        class="w-full bg-green-500 hover:bg-green-600 text-white text-lg font-semibold py-4 rounded-lg transition flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Simpan Perubahan
                </button>
                
                <!-- Batal -->
                <a href="?url=subjects" 
                   class="w-full bg-gray-500 hover:bg-gray-600 text-white text-lg font-semibold py-4 rounded-lg text-center transition flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Batal
                </a>
                
                <!-- Hapus / Disabled -->
                <?php if (!$hasTransaction): ?>
                    <button type="button"
                            onclick="confirmDelete()"
                            class="w-full bg-red-500 hover:bg-red-600 text-white text-lg font-semibold py-4 rounded-lg transition flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        Hapus Data
                    </button>
                <?php else: ?>
                    <div class="w-full bg-gray-300 text-gray-600 text-lg font-semibold py-4 rounded-lg text-center cursor-not-allowed flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                        Tidak Dapat Dihapus (Ada Riwayat Transaksi)
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50" onclick="if(event.target===this) closeDeleteModal()">
    <div class="bg-white rounded-md shadow-md p-10 max-w-lg w-full mx-4">
        <div class="text-center mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">KONFIRMASI PENGHAPUSAN</h2>
            <p class="text-md text-gray-800 mb-4 leading-relaxed">
                Apakah Anda <strong class="text-red-600">YAKIN</strong> ingin menghapus data penerima ini?
            </p>
            <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-4 mb-4">
                <p class="text-lg font-semibold text-gray-900">Nama: <?= htmlspecialchars($subject['name_subject']) ?></p>
                <p class="text-base text-gray-700">ID Share: <?= htmlspecialchars($subject['id_share']) ?></p>
                <p class="text-base text-gray-700">Tipe: <?= ucfirst($subject['type_subject']) ?></p>
            </div>
            <p class="text-md text-red-600 font-bold">
                Data ini akan dihapus PERMANEN dan tidak dapat dikembalikan!
            </p>
        </div>
        
        <div class="flex flex-col gap-4">
            <a href="?url=subjects/delete/<?= $subject['id_share'] ?>" 
               id="deleteConfirmLink"
               class="block bg-red-600 hover:bg-red-700 text-white font-semibold text-md py-5 rounded-md text-center transition-all shadow-lg">
                YA, HAPUS SEKARANG
            </a>
            <button type="button"
                    onclick="closeDeleteModal()"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold text-md py-5 rounded-md transition-all">
                BATAL, JANGAN HAPUS
            </button>
        </div>
    </div>
</div>


<script>
function toggleInactiveNote() {
    const status = document.getElementById('status').value;
    const field = document.getElementById('inactive_note_field');
    field.style.display = status === 'non_aktif' ? 'block' : 'none';
}

function confirmDelete() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>


<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>