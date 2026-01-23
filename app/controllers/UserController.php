<?php
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/models/User.php';

class UserController {
    private $userModel;
    
    public function __construct($db) {
        AuthMiddleware::checkAdmin();
        $this->userModel = new User($db);  // ← Pakai model aja, tidak perlu $conn sendiri
    }
    
    public function index() {
        $users = $this->userModel->getAllUsers();  // ← Pure model method
        require_once '../app/views/users/index.php';
    }
}
?>
