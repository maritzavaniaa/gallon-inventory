<?php
class User {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function register($username, $password, $user_name, $role = 'general_user') {
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO app_user (username, password_hash, user_name, role) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $password_hash, $user_name, $role);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    public function usernameExists($username) {
        $sql = "SELECT id_user FROM app_user WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Tambah method ini di class User
    public function login($username, $password) {
        $sql = "SELECT id_user, username, password_hash, role FROM app_user WHERE username = ? AND is_active = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password_hash'])) {
                return $user; // return user data kalau login sukses
            }
        }
        return false;
    }

    public function getAllUsers() {
        $sql = "SELECT id_user, username, user_name, role, is_active, created_at 
                FROM app_user ORDER BY created_at DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    

}
?>
