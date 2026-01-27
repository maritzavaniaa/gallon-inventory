<?php
require_once '../app/middleware/AuthMiddleware.php';

class ReportController {
    private $db;
    
    public function __construct($db) {
        AuthMiddleware::checkAdmin();
        $this->db = $db;
    }
    
    public function index() {
        require_once '../app/views/reports/index.php';
    }
}
?>
