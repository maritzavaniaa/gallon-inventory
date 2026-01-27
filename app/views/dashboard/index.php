<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Galon</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include '../app/views/layouts/sidebar.php'; ?>
    
    <main class="ml-64 p-8">
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <h1 class="text-6xl font-bold text-gray-800 mb-4">🏠 Dashboard</h1>
            <p class="text-2xl text-gray-600">Sistem Manajemen Stok Galon</p>
            
            <div class="mt-8 text-gray-500">
                <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></strong>!</p>
                <p class="mt-2">Gunakan menu di samping untuk navigasi.</p>
            </div>
        </div>
    </main>
</body>
</html>
