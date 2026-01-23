<!DOCTYPE html>
<html>
<head>
    <title>Kelola Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white p-6 rounded-lg shadow-md mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold">Kelola Karyawan & Non-Karyawan</h1>
            <a href="?url=subjects/create" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                + Tambah Baru
            </a>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                Berhasil disimpan!
            </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-4 text-left font-semibold">ID Share</th>
                        <th class="p-4 text-left font-semibold">Nama</th>
                        <th class="p-4 text-left font-semibold">Tipe</th>
                        <th class="p-4 text-left font-semibold">Divisi</th>
                        <th class="p-4 text-left font-semibold">Status</th>
                        <th class="p-4 text-left font-semibold">Grade</th>
                        <th class="p-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $subject): ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-4 font-mono"><?= $subject['id_share'] ?></td>
                        <td class="p-4 font-semibold"><?= htmlspecialchars($subject['name_subject']) ?></td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-xs 
                                <?= $subject['type_subject'] == 'karyawan' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' ?>">
                                <?= $subject['type_subject'] ?>
                            </span>
                        </td>
                        <td class="p-4"><?= htmlspecialchars($subject['division'] ?: '-') ?></td>
                        <td class="p-4">
                            <?= $subject['status'] == 'aktif' ? '✅ Aktif' : '❌ Nonaktif' ?>
                        </td>
                        <td class="p-4"><?= htmlspecialchars($subject['name_grade'] ?: '-') ?></td>
                        <td class="p-4 text-right space-x-2">
                            <a href="?url=subjects/edit/<?= $subject['id_share'] ?>" class="text-blue-500 hover:underline">Edit</a>
                            <a href="?url=subjects/delete/<?= $subject['id_share'] ?>" 
                               onclick="return confirm('Yakin hapus?')" 
                               class="text-red-500 hover:underline">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="?url=dashboard" class="block mt-6 text-blue-500 hover:underline">← Kembali Dashboard</a>
    </div>
</body>
</html>
