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

<?php
// 1. Cek apakah ada filter aktif
$hasFilters = !empty($_GET['search']) || !empty($_GET['role']) || !empty($_GET['status']);

// 2. Siapkan konten Search Bar
ob_start();
    $label = 'Cari User';
    $placeholder = 'Cari berdasarkan username...';
    $isNumeric = false;
    include '../app/views/components/search-bar.php';
$searchContent = ob_get_clean();

// 3. Siapkan konten Filter Dropdown
ob_start(); ?>
<div class="md:col-span-3">
    <?php  
        // Dropdown Role
        $label = 'Hak Akses';
        $name = 'role';
        $options = ['admin' => 'Administrator', 'general_user' => 'General User'];
        include '../app/views/components/filter-dropdown.php';
    ?>
</div>

<div class="md:col-span-3">
    <?php  
        // Dropdown Status
        $label = 'Status Akun';
        $name = 'status';
        $options = ['aktif' => 'Aktif', 'non_aktif' => 'Non-aktif'];
        include '../app/views/components/filter-dropdown.php';
    ?>
</div>

<?php
$filterContent = ob_get_clean(); 
include '../app/views/components/filter-container.php';
?>

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
                <th class="py-4 px-5 text-left text-white font-semibold text-md border-r border-white border-opacity-20" style="width: 6%;">
                    NO
                </th>
                <th class="py-4 px-5 text-left text-white font-semibold text-md border-r border-white border-opacity-20" style="width: 24%;">
                    USERNAME
                </th>
                <th class="py-4 px-5 text-center text-white font-semibold text-md border-r border-white border-opacity-20" style="width: 15%;">
                    ROLE
                </th>
                <th class="py-4 px-5 text-center text-white font-semibold text-md border-r border-white border-opacity-20" style="width: 15%;">
                    STATUS
                </th>
                <th class="py-4 px-5 text-center text-white font-semibold text-md border-r border-white border-opacity-20" style="width: 15%;">
                    DIBUAT
                </th>
                <th class="py-4 px-5 text-center text-white font-semibold text-md" style="width: 25%;">
                    KONTROL AKSES
                </th>
            </tr>
        </thead>
        
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" class="py-16 text-center">
                        <p class="text-gray-600 text-xl font-semibold mb-2">Belum Ada Data User</p>
                        <p class="text-gray-500 text-lg">Klik tombol "Tambah User Baru" untuk memulai</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $index => $user): ?>
                <tr class="border-b-2 border-gray-100 hover:bg-blue-50 transition-colors <?= $user['status'] == 'non_aktif' ? 'bg-red-50' : '' ?>">
                    <!-- Nomor -->
                    <td class="py-3 px-5 text-gray-900 font-semibold text-sm">
                        <?= $index + 1 ?>
                    </td>
                    
                    <!-- Username -->
                    <td class="py-3 px-5">
                        <span class="text-gray-900 font-semibold text-sm">
                            <?= htmlspecialchars($user['username']) ?>
                        </span>
                        <?php if ($user['id_user'] == $_SESSION['user_id']): ?>
                            <span class="ml-2 text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-semibold">Anda</span>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Role -->
                    <td class="py-3 px-5 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border-2
                            <?= $user['role'] == 'admin' 
                                ? 'bg-blue-100 text-blue-800 border-blue-200' 
                                : 'bg-purple-100 text-purple-800 border-purple-200' ?>">
                            
                            <?php if ($user['role'] == 'admin'): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21a3.745 3.745 0 01-3.068-1.593 3.745 3.745 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.745 3.745 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                </svg>
                            <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            <?php endif; ?>
                            
                            <?= strtoupper($user['role']) ?>
                        </span>
                    </td>
                    
                    <!-- Status -->
                    <td class="py-3 px-5 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border-2 transition-all 
                            <?= $user['status'] == 'aktif' 
                                ? 'bg-green-100 text-green-800 border-green-200' 
                                : 'bg-red-100 text-red-800 border-red-200' ?>">
                            
                            <span class="w-2 h-2 rounded-full <?= $user['status'] == 'aktif' ? 'bg-green-500' : 'bg-red-500' ?>"></span>
                            
                            <?= strtoupper($user['status']) ?>
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
                                        class="group inline-flex items-center gap-2 px-4 py-1.5 rounded-md font-bold text-xs uppercase tracking-wider transition-all shadow-sm border-2 
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
