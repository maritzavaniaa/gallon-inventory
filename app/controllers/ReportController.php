<?php
// require_once '../app/middleware/AuthMiddleware.php';

class ReportController {
    private $db;
    
    public function __construct($db) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?url=login');
            exit;
        }
        $this->db = $db;
    }
    
    public function index() {
        require_once '../app/views/reports/index.php';
    }
}
?>
