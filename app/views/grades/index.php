<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Grade</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-6 py-8 max-w-6xl">
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Kelola Grade Karyawan</h1>
                <a href="?url=dashboard" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">← Dashboard</a>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                Data berhasil disimpan!
            </div>
            <?php endif; ?>
            
            <!-- Form Tambah Grade -->
            <div class="bg-blue-50 border-2 border-blue-200 p-6 rounded-lg mb-6">
                <h2 class="text-xl font-semibold mb-4 text-blue-800">Tambah Grade Baru</h2>
                <form method="POST" action="?url=grades/store" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Nama Grade</label>
                        <input type="text" name="name_grade" required placeholder="Contoh: Staff, Manager" 
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Kuota Galon/Bulan</label>
                        <input type="number" name="default_allowance" required min="0" placeholder="Contoh: 4" 
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold">
                            Tambah Grade
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Tabel Grade -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border p-4 text-left font-semibold">ID</th>
                            <th class="border p-4 text-left font-semibold">Nama Grade</th>
                            <th class="border p-4 text-left font-semibold">Kuota Galon/Bulan</th>
                            <th class="border p-4 text-left font-semibold">Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $grade): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="border p-4"><?= $grade['id_grade'] ?></td>
                            <td class="border p-4 font-semibold"><?= htmlspecialchars($grade['name_grade']) ?></td>
                            <td class="border p-4 text-blue-600 font-bold"><?= $grade['default_allowance'] ?> galon</td>
                            <td class="border p-4"><?= date('d/m/Y', strtotime($grade['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
