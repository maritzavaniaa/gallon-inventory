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
        
        <!-- FORM TAMBAH GENERAL USER -->
        <div class="bg-blue-50 border-2 border-blue-200 p-6 rounded-lg mb-6">
            <h2 class="text-xl font-semibold mb-4 text-blue-800">Tambah General User</h2>
            <form method="POST" action="?url=users/store" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Username</label>
                    <input type="text" name="username" required 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Nama Lengkap</label>
                    <input type="text" name="user_name" required 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Password</label>
                    <input type="password" name="password" required minlength="6"
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold">
                        Tambah User
                    </button>
                </div>
            </form>
            <p class="text-xs text-gray-500 mt-2">Role otomatis: <strong>general_user</strong></p>
        </div>
        
        <!-- DAFTAR USER -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Daftar User</h2>
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <!-- Table sama seperti sebelumnya -->
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
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td class="border p-3"><?= $user['is_active'] ? '✅ Aktif' : '❌ Nonaktif' ?></td>
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
