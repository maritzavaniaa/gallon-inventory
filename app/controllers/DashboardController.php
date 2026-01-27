<?php
require_once '../app/middleware/AuthMiddleware.php';

class DashboardController {
    private $db;
    
    public function __construct($db) {
        AuthMiddleware::checkAdmin();
        $this->db = $db;
    }
    
    public function index() {
        // Langsung load view tanpa query stats
        require_once '../app/views/dashboard/index.php';
    }
}
?>
