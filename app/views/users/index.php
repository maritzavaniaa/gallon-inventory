<?php 
$pageTitle = 'Manajemen Akun - Sistem Galon';
ob_start(); 
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">👥 Manajemen Akun</h1>
    <p class="text-gray-600 mt-1">Kelola akun pengguna sistem</p>
</div>

<!-- Alert Messages -->
<?php if (isset($_GET['success'])): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    ✅ User berhasil ditambahkan!
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    ❌ Terjadi kesalahan. Silakan coba lagi.
</div>
<?php endif; ?>

<!-- Add User Form -->
<div class="bg-white rounded-xl shadow-lg p-8 mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">➕ Tambah User Baru</h2>
    
    <form method="POST" action="?url=users/store" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700">Username</label>
                <input type="text" 
                       name="username" 
                       required 
                       placeholder="Username"
                       class="w-full p-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-300">
            </div>
            
            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700">Password</label>
                <input type="password" 
                       name="password" 
                       required 
                       placeholder="Password"
                       class="w-full p-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-300">
            </div>
            
            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700">Role</label>
                <select name="role" 
                        required
                        class="w-full p-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-300">
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
        </div>
        
        <button type="submit" 
                class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition">
            💾 Tambah User
        </button>
    </form>
</div>

<!-- Users List -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-indigo-600 text-white">
            <tr>
                <th class="py-4 px-6 text-left text-lg font-semibold">No</th>
                <th class="py-4 px-6 text-left text-lg font-semibold">Username</th>
                <th class="py-4 px-6 text-left text-lg font-semibold">Role</th>
                <th class="py-4 px-6 text-left text-lg font-semibold">Dibuat</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="4" class="py-8 text-center text-gray-500">
                        Belum ada data user.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $index => $user): ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="py-4 px-6 text-lg"><?= $index + 1 ?></td>
                    <td class="py-4 px-6 text-lg font-semibold"><?= htmlspecialchars($user['username']) ?></td>
                    <td class="py-4 px-6 text-lg">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                            <?= ucfirst($user['role']) ?>
                        </span>
                    </td>
                    <td class="py-4 px-6 text-lg"><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>
