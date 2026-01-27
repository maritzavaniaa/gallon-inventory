<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Karyawan & Non-Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white p-6 rounded-lg shadow-md mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Kelola Karyawan & Non-Karyawan</h1>
                <p class="text-gray-600 mt-2">Data penerima kuota galon bulanan</p>
            </div>
            <div class="flex gap-3">
                <a href="?url=dashboard" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 font-semibold">
                    ← Dashboard
                </a>
                <a href="?url=subjects/create" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 font-semibold">
                    + Tambah Baru
                </a>
            </div>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php if ($_GET['success'] == 1): ?>
                    ✅ Data berhasil ditambahkan!
                <?php elseif ($_GET['success'] == 2): ?>
                    ✅ Data berhasil diperbarui!
                <?php elseif ($_GET['success'] == 3): ?>
                    ✅ Data berhasil dihapus!
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php if ($_GET['error'] == 'has_transaction'): ?>
                    ❌ Tidak dapat menghapus! Data ini memiliki riwayat transaksi. Ubah status menjadi "Non-aktif" sebagai gantinya.
                <?php elseif ($_GET['error'] == 'notfound'): ?>
                    ❌ Data tidak ditemukan!
                <?php else: ?>
                    ❌ Terjadi kesalahan. Silakan coba lagi.
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="p-4 text-left font-semibold">ID Share</th>
                            <th class="p-4 text-left font-semibold">Nama</th>
                            <th class="p-4 text-left font-semibold">Tipe</th>
                            <th class="p-4 text-left font-semibold">Divisi</th>
                            <th class="p-4 text-left font-semibold">Status</th>
                            <th class="p-4 text-left font-semibold">Grade/Kuota</th>
                            <th class="p-4 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($subjects)): ?>
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500">
                                Belum ada data. Klik "Tambah Baru" untuk memulai.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($subjects as $subject): ?>
                        <tr class="border-t hover:bg-gray-50 <?= $subject['status'] == 'non_aktif' ? 'bg-red-50' : '' ?>">
                            <td class="p-4 font-mono font-semibold"><?= htmlspecialchars($subject['id_share']) ?></td>
                            <td class="p-4 font-semibold"><?= htmlspecialchars($subject['name_subject']) ?></td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    <?= $subject['type_subject'] == 'karyawan' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' ?>">
                                    <?= ucfirst($subject['type_subject']) ?>
                                </span>
                            </td>
                            <td class="p-4"><?= htmlspecialchars($subject['division'] ?: '-') ?></td>
                            <td class="p-4">
                                <?php if ($subject['status'] == 'aktif'): ?>
                                    <span class="text-green-600 font-semibold">✅ Aktif</span>
                                <?php else: ?>
                                    <span class="text-red-600 font-semibold">❌ Non-aktif</span>
                                    <?php if ($subject['inactive_note']): ?>
                                        <p class="text-xs text-gray-600 mt-1"><?= htmlspecialchars($subject['inactive_note']) ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <?php if ($subject['type_subject'] == 'karyawan'): ?>
                                    <span class="text-blue-600 font-semibold"><?= htmlspecialchars($subject['name_grade'] ?: 'Belum set') ?></span>
                                <?php else: ?>
                                    <span class="text-purple-600 font-semibold"><?= $subject['default_allowance'] ?> galon/bulan</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center">
                                <a href="?url=subjects/edit/<?= $subject['id_share'] ?>" 
                                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-semibold mr-2">
                                    ✏️ Edit
                                </a>
                                <a href="?url=subjects/delete/<?= $subject['id_share'] ?>" 
                                   onclick="return confirm('Yakin hapus data <?= htmlspecialchars($subject['name_subject']) ?>?\n\nPeringatan: Jika data ini punya riwayat transaksi, tidak dapat dihapus!')" 
                                   class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold">
                                    🗑️ Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
