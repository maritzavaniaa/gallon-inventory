<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Sistem Galon' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <?php include '../app/views/layouts/sidebar.php'; ?>
    
    <!-- Main Content -->
    <main class="ml-64 p-8">
        <?= $content ?? '' ?>
    </main>
</body>
</html>
