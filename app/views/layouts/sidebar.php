<?php
// Get current page
$currentUrl = $_GET['url'] ?? 'dashboard';
$isGrades = strpos($currentUrl, 'grade') !== false;
$isSubjects = strpos($currentUrl, 'subject') !== false;
$isDashboard = $currentUrl === 'dashboard';
$isUsers = strpos($currentUrl, 'user') !== false;
$isTransactions = strpos($currentUrl, 'transaction') !== false;
$isReports = strpos($currentUrl, 'report') !== false;
?>

<aside class="w-64 min-h-screen flex flex-col fixed left-0 top-0" style="background: linear-gradient(180deg, #1F4396 0%, #152F6B 100%);">
    <!-- Header -->
    <div class="p-6 border-b border-white border-opacity-20">
        <div class="flex items-center gap-3 mb-2">
            <img src="./assets/images/logo.svg" alt="Logo Aqua" class="w-12 h-12 object-contain">
            <div>
                <h1 class="text-[17px] font-semibold text-white">Inventaris Gallon</h1>
                <p class="text-sm text-blue-200">Plant Pandaan</p>
            </div>
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 py-6 space-y-2">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
             <!-- Dashboard -->
            <a href="?url=dashboard" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all <?= $isDashboard ? 'bg-white bg-opacity-20 shadow-lg' : 'hover:bg-white hover:bg-opacity-10' ?>"
               style="color: <?= $isDashboard ? '#ffffff' : '#E8EFFF' ?>;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span class="font-semibold">Dashboard</span>
            </a>
        <?php endif; ?>

        <!-- Transaksi Galon -->
        <a href="?url=transactions" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all <?= $isTransactions ? 'bg-white bg-opacity-20 shadow-lg' : 'hover:bg-white hover:bg-opacity-10' ?>"
           style="color: <?= $isTransactions ? '#ffffff' : '#E8EFFF' ?>;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            <span class="font-semibold">Transaksi Galon</span>
        </a>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <!-- Data Master (Dropdown) -->
            <div class="space-y-1">
            <button onclick="toggleMasterMenu()" 
                    id="masterMenuButton"
                    class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg transition-all <?= ($isGrades || $isSubjects) ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10' ?>"
                    style="color: <?= ($isGrades || $isSubjects) ? '#ffffff' : '#E8EFFF' ?>;">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                    <span class="font-semibold">Data Master</span>
                </div>
                <svg id="masterMenuArrow" 
                    class="w-4 h-4 transform transition-transform duration-300 ease-in-out <?= ($isGrades || $isSubjects) ? 'rotate-180' : '' ?>" 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
                
                <!-- Submenu -->
                <div id="masterMenu" class="ml-8 space-y-1 <?= ($isGrades || $isSubjects) ? '' : 'hidden' ?>">
                    <a href="?url=subjects" 
                    class="block px-4 py-2 rounded-lg transition-all <?= $isSubjects ? 'bg-white bg-opacity-20 font-semibold' : 'hover:bg-white hover:bg-opacity-10' ?>"
                    style="color: <?= $isSubjects ? '#ffffff' : '#E8EFFF' ?>;">
                        Data Penerima
                    </a>
                    <a href="?url=grades" 
                    class="block px-4 py-2 rounded-lg transition-all <?= $isGrades ? 'bg-white bg-opacity-20 font-semibold' : 'hover:bg-white hover:bg-opacity-10' ?>"
                    style="color: <?= $isGrades ? '#ffffff' : '#E8EFFF' ?>;">
                        Grade Karyawan
                    </a>
                </div>
            </div>
            
            <!-- Laporan -->
            <a href="?url=reports" 
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all <?= $isReports ? 'bg-white bg-opacity-20 shadow-lg' : 'hover:bg-white hover:bg-opacity-10' ?>"
            style="color: <?= $isReports ? '#ffffff' : '#E8EFFF' ?>;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="font-semibold">Laporan</span>
            </a>
            
            <!-- Manajemen Akun -->
            <a href="?url=users" 
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all <?= $isUsers ? 'bg-white bg-opacity-20 shadow-lg' : 'hover:bg-white hover:bg-opacity-10' ?>"
            style="color: <?= $isUsers ? '#ffffff' : '#E8EFFF' ?>;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="font-semibold">Manajemen Akun</span>
            </a>
        <?php endif; ?>
    </nav>
    
    <!-- User Info & Logout -->
    <div class="p-4 border-t border-white border-opacity-20">
        <div class="mb-3 px-4">
            <p class="text-sm text-blue-200">Login sebagai:</p>
            <p class="font-semibold text-white"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></p>
        </div>
        <a href="?url=logout" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all bg-red-500 bg-opacity-80 hover:bg-red-600"
           style="color: #ffffff;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="font-semibold">Logout</span>
        </a>
    </div>
</aside>

<!-- JavaScript for Dropdown -->
<script>
function toggleMasterMenu() {
    const menu = document.getElementById('masterMenu');
    const arrow = document.getElementById('masterMenuArrow');
    menu.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}
</script>