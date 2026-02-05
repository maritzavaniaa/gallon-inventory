<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventaris Gallon</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .bg-brand {
            background: linear-gradient(135deg, #1F4396 0%, #2E5BBE 100%);
        }
        .btn-brand {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }
        .btn-brand:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>

<body class="bg-brand min-h-screen flex items-center justify-center">
    <div class="bg-white p-10 rounded-2xl shadow-2xl max-w-md w-full mx-4 border border-white border-opacity-20">
        
        <div class="text-center mb-10">
            <div class="flex justify-center">
                <img src="./assets/images/logo.svg" alt="Logo" class="h-24 w-auto">
            </div>
            <h2 class="text-2xl font-extrabold text-[#1F4396] uppercase tracking-tight">Sistem Intern Gallon</h2>
            <p class="text-gray-500 text-sm mt-1">Plant Pandaan</p>
        </div>
        
        <?php if (isset($error) && $error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6 text-sm flex items-center gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['expired'])): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md mb-6 text-sm flex items-center gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span>Sesi berakhir. Silakan login kembali.</span>
            </div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="on">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="mb-5">
                <label for="username" class="block text-[#1F4396] text-sm font-semibold uppercase tracking-widest mb-2 ml-1">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <input type="text" id="username" name="username" autocomplete="username" 
                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-100 rounded-xl focus:outline-none focus:border-[#1F4396] focus:ring-0 transition-all text-gray-700" 
                        placeholder="Masukkan username" required>
                </div>
            </div>
            
            <div class="mb-8">
                <label for="password" class="block text-[#1F4396] text-sm font-semibold uppercase tracking-widest mb-2 ml-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input type="password" id="password" name="password" autocomplete="current-password" 
                        class="w-full pl-10 pr-4 py-3 border-2 border-gray-100 rounded-xl focus:outline-none focus:border-[#1F4396] focus:ring-0 transition-all text-gray-700" 
                        placeholder="••••••••" required>
                </div>
            </div>
            
            <button type="submit" class="w-full btn-brand text-white font-semibold py-4 px-4 rounded-lg shadow-lg uppercase tracking-widest text-md">
                Masuk Ke Sistem
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-xs text-gray-400 font-medium tracking-tight">&copy; <?= date('Y') ?> Gallon Inventory System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>