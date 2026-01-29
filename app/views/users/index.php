<?php 
$pageTitle = 'Manajemen Akun - Sistem Galon';
ob_start(); 
?>

<!-- Page Header dengan Button di Samping -->
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold mb-2" style="color: #1F4396;">Manajemen Akun</h1>
        <p class="text-lg text-gray-700">Kelola akun pengguna sistem dan status aktif</p>
    </div>
    
    <div class="flex-shrink-0">
        <a href="?url=users/create" 
           class="inline-flex items-center gap-3 text-white font-semibold text-md px-5 py-2 rounded-md shadow-md transition-all hover:shadow-xl"
           style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); min-height: 56px; white-space: nowrap;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah User Baru</span>
        </a>
    </div>
</div>

<!-- Alert Messages -->
<?php if (isset($_GET['success'])): ?>
<div class="bg-green-50 border-2 border-green-500 text-gray-900 px-6 py-4 rounded-lg mb-6 text-lg">
    <strong class="font-bold text-green-700">Berhasil!</strong>
    <?php if ($_GET['success'] == '1'): ?>
        User baru berhasil ditambahkan ke sistem!
    <?php elseif ($_GET['success'] == '2'): ?>
        Status user berhasil diubah!
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="bg-red-50 border-2 border-red-500 text-gray-900 px-6 py-4 rounded-lg mb-6 text-lg">
    <strong class="font-bold text-red-700">Gagal!</strong>
    <?php if ($_GET['error'] == 'wrong_password'): ?>
        Password salah! Aksi dibatalkan.
    <?php elseif ($_GET['error'] == 'self_toggle'): ?>
        Anda tidak dapat mengubah status akun Anda sendiri!
    <?php elseif ($_GET['error'] == 'not_found'): ?>
        User tidak ditemukan.
    <?php else: ?>
        Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Users Table -->
<div class="bg-white rounded-md overflow-hidden border-2 border-gray-200">
    <table class="w-full">
        <thead>
            <tr style="background: linear-gradient(135deg, #1F4396 0%, #2E5BBE 100%);">
                <th class="py-3 px-5 text-left text-white font-semibold text-md border-r border-white border-opacity-20" style="width: 6%;">
                    No
                </th>
                <th class="py-3 px-5 text-left text-white font-semibold text-md border-r border-white border-opacity-20" style="width: 24%;">
                    Username
                </th>
                <th class="py-3 px-5 text-center text-white font-semibold text-md border-r border-white border-opacity-20" style="width: 15%;">
                    Role
                </th>
                <th class="py-3 px-5 text-center text-white font-semibold text-md border-r border-white border-opacity-20" style="width: 15%;">
                    Status
                </th>
                <th class="py-3 px-5 text-center text-white font-semibold text-md border-r border-white border-opacity-20" style="width: 15%;">
                    Dibuat
                </th>
                <th class="py-3 px-5 text-center text-white font-semibold text-md" style="width: 25%;">
                    Kontrol Akses
                </th>
            </tr>
        </thead>
        
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" class="py-16 text-center">
                        <div class="text-gray-400 text-7xl mb-4">👥</div>
                        <p class="text-gray-600 text-xl font-semibold mb-2">Belum Ada Data User</p>
                        <p class="text-gray-500 text-lg">Klik tombol "Tambah User Baru" untuk memulai</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $index => $user): ?>
                <tr class="border-b-2 border-gray-100 hover:bg-blue-50 transition-colors <?= $user['status'] == 'non_aktif' ? 'bg-red-50' : '' ?>">
                    <!-- Nomor -->
                    <td class="py-3 px-5 text-gray-900 font-semibold text-base">
                        <?= $index + 1 ?>
                    </td>
                    
                    <!-- Username -->
                    <td class="py-3 px-5">
                        <span class="text-gray-900 font-semibold text-base">
                            <?= htmlspecialchars($user['username']) ?>
                        </span>
                        <?php if ($user['id_user'] == $_SESSION['user_id']): ?>
                            <span class="ml-2 text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-semibold">Anda</span>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Role -->
                    <td class="py-3 px-5 flex items-center justify-center">
                        <span class="inline-flex items-center gap-1.5 <?= $user['role'] == 'admin' ? 'bg-blue-50 border-blue-200' : 'bg-purple-50 border-purple-200' ?> px-3 py-1.5 rounded-md border-2">
                            <svg class="w-4 h-4 <?= $user['role'] == 'admin' ? 'text-blue-600' : 'text-purple-600' ?>" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            <span class="<?= $user['role'] == 'admin' ? 'text-blue-900' : 'text-purple-900' ?> font-semibold text-sm">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </span>
                    </td>
                    
                    <!-- Status -->
                    <td class="py-3 px-5 text-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border-2 transition-all 
                            <?= $user['status'] == 'aktif' 
                                ? 'bg-green-50 border-green-200 text-green-700' 
                                : 'bg-red-50 border-red-200 text-red-700' ?>">
                            
                            <?php if ($user['status'] == 'aktif'): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            <?php endif; ?>

                            <span class="font-bold text-sm uppercase tracking-wide">
                                <?= $user['status'] ?>
                            </span>
                        </span>
                    </td>
                    
                    <!-- Tanggal Dibuat -->
                    <td class="py-3 px-5 text-center">
                        <span class="text-gray-700 font-medium text-sm">
                            <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                        </span>
                    </td>
                    
                    <!-- Action Button -->
                    <td class="py-3 px-5">
                        <div class="flex justify-center">
                            <?php if ($user['id_user'] != $_SESSION['user_id']): ?>
                                <button type="button"
                                        onclick="showPasswordModal(<?= $user['id_user'] ?>, '<?= htmlspecialchars($user['username'], ENT_QUOTES) ?>', '<?= $user['status'] ?>')"
                                        class="group inline-flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-wider transition-all shadow-sm border-2 
                                        <?= $user['status'] == 'aktif' 
                                            ? 'bg-white border-red-500 text-red-600 hover:bg-red-500 hover:text-white' 
                                            : 'bg-white border-green-500 text-green-600 hover:bg-green-500 hover:text-white' ?>"
                                        style="min-width: 140px; justify-content: center;">
                                    
                                    <?php if ($user['status'] == 'aktif'): ?>
                                        <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        <span>Nonaktifkan</span>
                                    <?php else: ?>
                                        <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                        <span>Aktifkan User</span>
                                    <?php endif; ?>
                                </button>
                            <?php else: ?>
                                <div class="flex items-center gap-2 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <span class="text-xs font-semibold">Akun Anda</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Password Confirmation Modal -->
<div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50" onclick="if(event.target===this) closePasswordModal()">
    <div class="bg-white rounded-md shadow-md p-10 max-w-lg w-full mx-4">
        <div class="text-center mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-3">KONFIRMASI PASSWORD</h2>
            <p class="text-md text-gray-800 mb-4 leading-relaxed">
                Masukkan password Anda untuk <strong id="modalAction">mengubah status</strong> user ini
            </p>
            <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-4 mb-4">
                <p class="text-lg font-semibold text-gray-900" id="modalUserName"></p>
                <p class="text-base text-gray-700" id="modalStatusInfo"></p>
            </div>
        </div>
        
        <form method="POST" action="" id="toggleStatusForm">
            <div class="mb-6">
                <label class="block text-md font-semibold mb-2 text-gray-900">Password Anda</label>
                <input type="password" 
                       name="admin_password" 
                       id="adminPassword"
                       required 
                       placeholder="Masukkan password untuk konfirmasi"
                       class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-300 focus:border-blue-500">
            </div>
            
            <div class="flex flex-col gap-4">
                <button type="submit" 
                        class="block text-white font-semibold text-md py-5 rounded-md text-center transition-all shadow-lg hover:opacity-90"
                        style="background: #1F4396;">
                    KONFIRMASI & UBAH STATUS
                </button>
                <button type="button"
                        onclick="closePasswordModal()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold text-md py-5 rounded-md transition-all">
                    BATAL
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showPasswordModal(userId, username, currentStatus) {
    const newStatus = currentStatus === 'aktif' ? 'non_aktif' : 'aktif';
    const action = currentStatus === 'aktif' ? 'menonaktifkan' : 'mengaktifkan';

    document.getElementById('modalAction').textContent = action;
    document.getElementById('modalUserName').textContent = 'Username: ' + username;
    document.getElementById('modalStatusInfo').textContent = 'Status saat ini: ' + (currentStatus === 'aktif' ? 'Aktif' : 'Non-aktif');
    
    // Set form action
    document.getElementById('toggleStatusForm').action = '?url=users/toggle/' + userId;
    
    // Clear password field
    document.getElementById('adminPassword').value = '';
    
    // Show modal
    document.getElementById('passwordModal').classList.remove('hidden');
    
    // Focus password field
    setTimeout(() => {
        document.getElementById('adminPassword').focus();
    }, 100);
}

function closePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
}

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePasswordModal();
});
</script>

<!-- Info Box -->
<div class="mt-8 bg-blue-50 border-2 border-blue-300 rounded-xl p-6">
    <div class="flex items-start gap-4">
        <svg class="w-8 h-8 text-blue-600 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
        <div>
            <p class="text-gray-900 font-bold text-lg mb-2">Informasi Penting:</p>
            <ul class="text-gray-800 text-base space-y-1 leading-relaxed">
                <li>• User dengan status <strong>Aktif</strong> dapat login ke sistem</li>
                <li>• User dengan status <strong>Non-aktif</strong> tidak dapat login</li>
                <li>• Anda tidak dapat mengubah status akun Anda sendiri</li>
                <li>• Setiap perubahan status memerlukan konfirmasi password</li>
            </ul>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include '../app/views/layouts/main.php';
?>
