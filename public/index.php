<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();
require_once '../app/config/database.php'; // Cuma require, tidak ada echo

// Simple router berdasarkan $_GET['url']
$url = $_GET['url'] ?? 'dashboard';

switch ($url) {
    // case 'register':
        // ... sama
        // break;
    case 'login':
        require_once '../app/controllers/AuthController.php';
        $controller = new AuthController($conn);
        $controller->login();
        break;
     case 'logout':
        session_destroy();
        header('Location: ?url=login');
        exit;
        break;    
    case 'users':
        require_once '../app/controllers/UserController.php';
        $controller = new UserController($conn);
        $controller->index();
        break;
        
    default:
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?url=login');
            exit;
        }
        
        // Dashboard sederhana
        echo "<div class='container mx-auto p-6'>";
        echo "<h1 class='text-3xl font-bold mb-4'>Dashboard - {$_SESSION['username']}</h1>";
        echo "<p>Role: <strong>{$_SESSION['role']}</strong></p>";
        
        if ($_SESSION['role'] == 'admin') {
            echo "<h2 class='text-xl mt-6'>Admin Menu:</h2>";
            echo "<ul class='list-disc list-inside mt-2'>";
            echo "<li><a href='?url=users' class='text-blue-500 hover:underline'>Kelola User</a></li>";
            echo "<li><a href='?url=subjects' class='text-blue-500 hover:underline'>Kelola Karyawan</a></li>";
            echo "</ul>";
        }
        
        echo "<a href='?url=logout' class='bg-red-500 text-white px-4 py-2 rounded mt-4 inline-block'>Logout</a>";
        echo "</div>";
        break;
    
    
}

?>
