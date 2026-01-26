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
                    if ($user) {
                        $_SESSION['user_id'] = $user['id_user'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['last_activity'] = time();
                        
                        header('Location: ?url=dashboard');
                        exit;
                    } else {
                        $error = "Username atau password salah";
                    }
                }
            }
        }
        require_once '../app/views/auth/login.php';
    }
    
    public function storeUser() {
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = "Request tidak valid (CSRF Token Invalid)";
            header('Location: ?url=users');
            exit;
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $user_name = trim($_POST['user_name'] ?? '');
        
        if (empty($username) || empty($password) || empty($user_name)) {
            $_SESSION['error'] = "Semua field wajib diisi";
        } elseif (strlen($password) < 6) {
            $_SESSION['error'] = "Password minimal 6 karakter";
        } elseif ($this->userModel->usernameExists($username)) {
            $_SESSION['error'] = "Username sudah ada";
        } else {
            if ($this->userModel->register($username, $password, $user_name, 'general_user')) {
                $_SESSION['success'] = "General User berhasil dibuat!";
            } else {
                $_SESSION['error'] = "Gagal membuat user";
            }
        }
        
        header('Location: ?url=users');
        exit;
    }
    
}
?>
