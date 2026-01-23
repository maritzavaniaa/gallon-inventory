<!DOCTYPE html>
<html>
<head>
    <title>Kelola User - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h1 class="text-3xl font-bold mb-4">Kelola User</h1>
            <a href="?url=dashboard" class="bg-gray-500 text-white px-4 py-2 rounded">← Kembali</a>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4">Daftar User</h2>
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border p-3 text-left">Username</th>
                            <th class="border p-3 text-left">Nama</th>
                            <th class="border p-3 text-left">Role</th>
                            <th class="border p-3 text-left">Status</th>
                            <th class="border p-3 text-left">Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="border p-3"><?= htmlspecialchars($user['username']) ?></td>
                            <td class="border p-3"><?= htmlspecialchars($user['user_name']) ?></td>
                            <td class="border p-3">
                                <span class="px-2 py-1 rounded text-xs 
                                    <?= $user['role'] == 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' ?>">
                                    <?= $user['role'] ?>
                                </span>
                            </td>
                            <td class="border p-3">
                                <?= $user['is_active'] ? '✅ Aktif' : '❌ Nonaktif' ?>
                            </td>
                            <td class="border p-3"><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
