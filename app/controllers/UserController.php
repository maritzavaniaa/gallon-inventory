<?php
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/models/User.php';

class UserController {
    private $userModel;
    
    public function __construct($db) {
        AuthMiddleware::checkAdmin();
        $this->userModel = new User($db);
    }
    
    public function index() {
        $users = $this->userModel->getAllUsers();
        require_once '../app/views/users/index.php';
    }
    
    public function create() {
        require_once '../app/views/users/create.php';
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $role = trim($_POST['role'] ?? '');
            
            // Validate
            if (empty($username) || empty($password) || empty($role)) {
                header('Location: ?url=users/create&error=empty');
                exit;
            }
            
            if (strlen($password) < 6) {
                header('Location: ?url=users/create&error=password_short');
                exit;
            }
            
            // Check duplicate username
            if ($this->userModel->usernameExists($username)) {
                header('Location: ?url=users/create&error=duplicate');
                exit;
            }
            
            // Create user (default status: aktif)
            if ($this->userModel->createUser($username, $password, $role)) {
                header('Location: ?url=users&success=1');
            } else {
                header('Location: ?url=users/create&error=1');
            }
            exit;
        }
    }
    
    public function toggleStatus($id) {
        // Prevent toggling own status
        if ($id == $_SESSION['user_id']) {
            header('Location: ?url=users&error=self_toggle');
            exit;
        }
        
        // Verify admin password
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $admin_password = $_POST['admin_password'] ?? '';
            
            if (empty($admin_password)) {
                header('Location: ?url=users&error=no_password');
                exit;
            }
            
            // Get current admin user
            $admin = $this->userModel->getUserById($_SESSION['user_id']);
            
            // Verify password
            if (!password_verify($admin_password, $admin['password_hash'])) {
                header('Location: ?url=users&error=wrong_password');
                exit;
            }
            
            // Password correct, proceed with toggle
            $user = $this->userModel->getUserById($id);
            
            if (!$user) {
                header('Location: ?url=users&error=not_found');
                exit;
            }
            
            // Toggle status
            $newStatus = ($user['status'] == 'aktif') ? 'non_aktif' : 'aktif';
            
            if ($this->userModel->updateStatus($id, $newStatus)) {
                header('Location: ?url=users&success=2');
            } else {
                header('Location: ?url=users&error=1');
            }
            exit;
        }
        
        // If not POST, redirect
        header('Location: ?url=users');
        exit;
    }
}
?>
