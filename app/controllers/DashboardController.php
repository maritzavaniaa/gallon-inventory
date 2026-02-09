<?php
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/models/Transaction.php';

class DashboardController {
    private $db;
    
    public function __construct($db) {
        AuthMiddleware::checkAdmin();
        $this->db = $db;
    }
    
    public function index() {
        $model = new Transaction($this->db);
        $range = $_GET['range'] ?? 'week'; // Default minggu ini
        $model = new Transaction($this->db);
        
        $stock = $model->getCurrentStock();
        $stats = $model->getDashboardStats($range); // Data Masuk & Keluar
        $chartData = $model->getChartData($range); // Data Keluar & Masuk sesuai range
    
        require_once '../app/views/dashboard/index.php';
    }
}