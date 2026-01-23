<?php
require_once '../app/models/User.php';
require_once '../app/middleware/AuthMiddleware.php';

class UserController {
    private $userModel;
    
    public function __construct($db) {
        AuthMiddleware::checkAdmin(); // Hanya admin
        $this->userModel = new User($db);
    }
    
    public function index() {
        // Ambil semua user
        $sql = "SELECT id_user, username, user_name, role, is_active, created_at 
                FROM app_user ORDER BY created_at DESC";
        $result = $this->conn->query($sql);
        $users = $result->fetch_all(MYSQLI_ASSOC);
        
        require_once '../app/views/users/index.php';
    }
}
?>
