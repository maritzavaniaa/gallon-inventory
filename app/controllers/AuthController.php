<?php
require_once '../app/models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct($db) {
        $this->userModel = new User($db);
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $user_name = trim($_POST['user_name']);
            
            // Validasi
            if (empty($username) || empty($password) || empty($user_name)) {
                $error = "Semua field wajib diisi";
            } elseif (strlen($password) < 6) {
                $error = "Password minimal 6 karakter";
            } elseif ($this->userModel->usernameExists($username)) {
                $error = "Username sudah terdaftar";
            } else {
                // Register user
                if ($this->userModel->register($username, $password, $user_name)) {
                    $success = "Registrasi berhasil! Silakan login.";
                } else {
                    $error = "Registrasi gagal. Coba lagi.";
                }
            }
        }
        
        // Load view
        require_once '../app/views/auth/register.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            
            if (empty($username) || empty($password)) {
                $error = "Username dan password wajib diisi";
            } else {
                $user = $this->userModel->login($username, $password);
                if ($user) {
                    // Set session
                    $_SESSION['user_id'] = $user['id_user'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Redirect berdasarkan role
                    if ($user['role'] == 'admin') {
                        header('Location: ?url=dashboard');
                    } else {
                        header('Location: ?url=dashboard');
                    }
                    exit;
                } else {
                    $error = "Username atau password salah";
                }
            }
        }
        
        // Load view login
        require_once '../app/views/auth/login.php';
    }
    
}
?>
