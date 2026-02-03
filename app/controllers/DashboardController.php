<?php
require_once '../app/middleware/AuthMiddleware.php';

class DashboardController {
    private $db;
    
    public function __construct($db) {
        AuthMiddleware::checkAdmin();
        $this->db = $db;
    }
    
    public function index() {
        if ($_SESSION['role'] !== 'admin') {
            header('Location: ?url=transactions');
            exit;
        }

        require_once '../app/views/dashboard/index.php';
    }
}
?>
