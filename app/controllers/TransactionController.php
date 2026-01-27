<?php
require_once '../app/middleware/AuthMiddleware.php';

class TransactionController {
    private $db;
    
    public function __construct($db) {
        AuthMiddleware::checkAdmin();
        $this->db = $db;
    }
    
    public function index() {
        require_once '../app/views/transactions/index.php';
    }
}
?>
