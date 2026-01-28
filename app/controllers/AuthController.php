<?php
require_once '../app/models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct($db) {
        $this->userModel = new User($db);
    }
    
    // public function register() {
    //     // Hanya admin yang bisa akses register
    //     if ($_SESSION['role'] != 'admin') {
    //         header('Location: ?url=dashboard');
    //         exit;
    //     }
        
    //     // Sisanya sama, tapi role fixed jadi 'general_user'
    //     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //         // ... validasi sama ...

    //         $username = trim($_POST['username'] ?? '');
    //         $password = $_POST['password'] ?? '';
    //         $user_name = trim($_POST['user_name'] ?? '');
            
    //         if ($this->userModel->register($username, $password, $user_name, 'general_user')) {  // Fixed role
    //             $success = "General User berhasil dibuat!";
    //         }
    //         // ...
    //     }
        
    //     require_once '../app/views/auth/register.php';
    // }
    

    public function login() {
        $error = '';
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                $error = "Request tidak valid";
            } else {
                $username = trim($_POST['username']);
                $password = $_POST['password'];
                
                if (empty($username) || empty($password)) {
                    $error = "Username dan password wajib diisi";
                } else {
                    $user = $this->userModel->login($username, $password);
                    
                    if ($user === false) {
                        // Check specifically if account is inactive
                        $checkUser = $this->userModel->getUserByUsername($username);
                        if ($checkUser && isset($checkUser['status']) && $checkUser['status'] == 'non_aktif') {
                            $error = "Akun Anda telah dinonaktifkan. Hubungi administrator.";
                        } else {
                            $error = "Username atau password salah";
                        }
                    } else {
                        // Login success
                        $_SESSION['user_id'] = $user['id_user'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['last_activity'] = time();
                        
                        header('Location: ?url=dashboard');
                        exit;
                    }
                }
            }
        }
        require_once '../app/views/auth/login.php';
    }
    
    
    public function storeUser() {
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            header('Location: ?url=users/create&error=csrf');
            exit;
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = trim($_POST['role'] ?? '');
        
        if (empty($username) || empty($password) || empty($role)) {
            header('Location: ?url=users/create&error=empty');
            exit;
        } 
        
        if (strlen($password) < 6) {
            header('Location: ?url=users/create&error=password_short');
            exit;
        } 
        
        if ($this->userModel->usernameExists($username)) {
            header('Location: ?url=users/create&error=duplicate');
            exit;
        }
        
        if ($this->userModel->createUser($username, $password, $role)) {
            header('Location: ?url=users&success=1');
        } else {
            header('Location: ?url=users/create&error=1');
        }
        exit;
    }
    
    
}
?>
