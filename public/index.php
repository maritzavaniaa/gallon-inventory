<?php
// Session config 8 jam
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);
session_set_cookie_params(28800);

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

session_start();
require_once '../app/helpers/functions.php';
require_once '../app/config/database.php'; 

// Simple router berdasarkan $_GET['url']
$url = $_GET['url'] ?? 'dashboard';

if ($url !== 'login') {
    checkSessionExpiry();
}

switch ($url) {
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
    case 'users/store':
        require_once '../app/controllers/AuthController.php';
        $controller = new AuthController($conn);
        $controller->storeUser();  // Method baru
        break;
    case 'subjects':
        require_once '../app/controllers/SubjectController.php';
        $controller = new SubjectController($conn);
        $controller->index();
        break;
    case 'subjects/create':
        require_once '../app/controllers/SubjectController.php';
        $controller = new SubjectController($conn);
        $controller->create();
        break;
    case 'subjects/store':
        require_once '../app/controllers/SubjectController.php';
        $controller = new SubjectController($conn);
        $controller->store();
        break;
    case 'grades':
        require_once '../app/controllers/GradeController.php';
        $controller = new GradeController($conn);
        $controller->index();
        break;
    case 'grades/store':
        require_once '../app/controllers/GradeController.php';
        $controller = new GradeController($conn);
        $controller->store();
        break;


    default:
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?url=login');
            exit;
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Dashboard</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; background: #f3f4f6; }
                .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto; }
                .btn { display: inline-block; padding: 12px 24px; margin: 8px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: all 0.3s; }
                .btn-blue { background: #3b82f6; color: white; }
                .btn-blue:hover { background: #2563eb; }
                .btn-red { background: #ef4444; color: white; }
                .btn-red:hover { background: #dc2626; }
                .btn-green { background: #10b981; color: white; }
                .btn-green:hover { background: #059669; }
                h1 { color: #1f2937; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class="card">
                <h1>🏆 Dashboard Galon Inventory</h1>
                <p><strong>Selamat datang:</strong> <?= htmlspecialchars($_SESSION['username']) ?></p>
                <p><strong>Role:</strong> <?= ucfirst($_SESSION['role']) ?></p>
                
                <?php if ($_SESSION['role'] == 'admin'): ?>
                <div style="margin: 30px 0;">
                    <h2 style="color: #374151; margin-bottom: 20px;">Admin Panel</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                        <a href="?url=users" class="btn btn-blue">👥 Kelola User</a>
                        <a href="?url=grades" style="background: #f59e0b; color: white;" class="btn">🏅 Kelola Grade</a>
                        <a href="?url=subjects" class="btn btn-green">👨‍💼 Kelola Karyawan</a>
                    </div>
                </div>
                <?php endif; ?>

                
                <a href="?url=logout" class="btn btn-red">🚪 Logout</a>
            </div>
        </body>
        </html>
        <?php 
        exit; 
} 
?>