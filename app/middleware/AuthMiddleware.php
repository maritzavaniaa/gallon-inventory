<?php
class AuthMiddleware {
    public static function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?url=login');
            exit;
        }
    }
    
    public static function checkAdmin() {
        self::checkAuth();
        if ($_SESSION['role'] != 'admin') {
            die("Access denied. Admin only.");
        }
    }

    public static function checkLog() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?url=login');
            exit;
        }
    }
}
?>
