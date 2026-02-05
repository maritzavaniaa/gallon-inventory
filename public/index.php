<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session config 8 jam
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);
session_set_cookie_params(28800);

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Timezone
date_default_timezone_set('Asia/Jakarta');

session_start();
require_once '../app/helpers/functions.php';
require_once '../app/config/database.php'; 

$database = new Database();
$db = $database->getConnection();

// Simple router
$url = $_GET['url'] ?? 'dashboard';

if ($url !== 'login') {
    checkSessionExpiry();
}

switch ($url) {
    case 'login':
        require_once '../app/controllers/AuthController.php';
        $controller = new AuthController($db);
        $controller->login();
        break;
        
    case 'logout':
        session_destroy();
        header('Location: ?url=login');
        exit;

    case 'dashboard':
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'general_user') {
            header('Location: ?url=transactions');
            exit;
        }

        require_once '../app/controllers/DashboardController.php';
        $controller = new DashboardController($db);
        $controller->index();
        break;
        
    case 'users':
        require_once '../app/controllers/UserController.php';
        $controller = new UserController($db);
        $controller->index();
        break;
    
    case 'users/create':
        require_once '../app/controllers/UserController.php';
        $controller = new UserController($db);
        $controller->create();
        break;
        
    case 'users/store':
        require_once '../app/controllers/UserController.php';
        $controller = new UserController($db);
        $controller->store();
        break;
        
        
    case 'subjects':
        require_once '../app/controllers/SubjectController.php';
        $controller = new SubjectController($db);
        $controller->index();
        break;
        
    case 'subjects/create':
        require_once '../app/controllers/SubjectController.php';
        $controller = new SubjectController($db);
        $controller->create();
        break;
        
    case 'subjects/store':
        require_once '../app/controllers/SubjectController.php';
        $controller = new SubjectController($db);
        $controller->store();
        break;
        
    case 'grades':
        require_once '../app/controllers/GradeController.php';
        $controller = new GradeController($db);
        $controller->index();
        break;
        
    case 'grades/store':
        require_once '../app/controllers/GradeController.php';
        $controller = new GradeController($db);
        $controller->store();
        break;

    case 'grades/create':
        require_once '../app/controllers/GradeController.php';
        $controller = new GradeController($db);
        $controller->create();
        break;

    case 'transactions':
        require_once '../app/controllers/TransactionController.php';
        $controller = new TransactionController($db);
        $controller->index();
        break;

    case 'transactions/search':
        require_once '../app/controllers/TransactionController.php';
        $controller = new TransactionController($db);
        $controller->searchSubject();
        break;

    case 'transactions/store-out':
        require_once '../app/controllers/TransactionController.php';
        $controller = new TransactionController($db);
        $controller->storeOut();
        break;

    case 'transactions/store-in':
        require_once '../app/controllers/TransactionController.php';
        $controller = new TransactionController($db);
        $controller->storeIn();
        break;
        
    case 'reports/out':
        require_once '../app/models/ReportOut.php'; 
        require_once '../app/controllers/ReportOutController.php'; 
        $controller = new ReportOutController($db); 
        $controller->index(); 
        break;
        
    case 'reports/out/export':
        require_once '../app/controllers/ReportOutController.php';
        $controller = new ReportOutController($db);
        $controller->export();
        break;
    
    // case 'reports/in':
    //     require_once '../app/models/ReportIn.php'; 
    //     require_once '../app/controllers/ReportInController.php';
    //     $controller = new ReportInController($db);
    //     $controller->index();
    //     break;
    
    case 'reports':
        header('Location: ?url=reports/out');
        break;

    default:
        // Dynamic routing untuk edit/update/delete dengan ID
        if (strpos($url, 'grades/edit/') === 0) {
            $id = (int)str_replace('grades/edit/', '', $url);
            require_once '../app/controllers/GradeController.php';
            $controller = new GradeController($db);
            $controller->edit($id);
            
        } elseif (strpos($url, 'grades/update/') === 0) {
            $id = (int)str_replace('grades/update/', '', $url);
            require_once '../app/controllers/GradeController.php';
            $controller = new GradeController($db);
            $controller->update($id);
            
        } elseif (strpos($url, 'grades/delete/') === 0) {
            $id = (int)str_replace('grades/delete/', '', $url);
            require_once '../app/controllers/GradeController.php';
            $controller = new GradeController($db);
            $controller->delete($id);
            
        } elseif (strpos($url, 'subjects/edit/') === 0) {
            $id_share = str_replace('subjects/edit/', '', $url);
            require_once '../app/controllers/SubjectController.php';
            $controller = new SubjectController($db);
            $controller->edit($id_share);
            
        } elseif (strpos($url, 'subjects/update/') === 0) {
            $id_share = str_replace('subjects/update/', '', $url);
            require_once '../app/controllers/SubjectController.php';
            $controller = new SubjectController($db);
            $controller->update($id_share);
            
        } elseif (strpos($url, 'subjects/delete/') === 0) {
            $id_share = str_replace('subjects/delete/', '', $url);
            require_once '../app/controllers/SubjectController.php';
            $controller = new SubjectController($db);
            $controller->delete($id_share);

        } elseif (strpos($url, 'users/toggle/') === 0) {
            $id = (int)str_replace('users/toggle/', '', $url);
            require_once '../app/controllers/UserController.php';
            $controller = new UserController($db);
            $controller->toggleStatus($id);            
            
        } else {
            // Dashboard & Default Access
            if (!isset($_SESSION['user_id'])) {
                header('Location: ?url=login');
                exit;
            }

            // CEK ROLE DISINI
            if ($_SESSION['role'] === 'general_user') {
                // General User dipaksa ke transaksi
                require_once '../app/controllers/TransactionController.php';
                $controller = new TransactionController($db);
                $controller->index();
            } else {
                // Admin baru boleh ke dashboard
                require_once '../app/controllers/DashboardController.php';
                $controller = new DashboardController($db);
                $controller->index();
            }
        }
}
?>
