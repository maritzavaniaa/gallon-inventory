<?php
class User {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Get all users
    public function getAllUsers() {
        $sql = "SELECT * FROM app_user ORDER BY created_at DESC";
        $result = $this->db->query($sql);
        $users = [];
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Convert is_active (1/0) to status (aktif/non_aktif) for view
                $row['status'] = ($row['is_active'] == 1) ? 'aktif' : 'non_aktif';
                $users[] = $row;
            }
        }
        
        return $users;
    }
    
    // Get user by ID
    public function getUserById($id) {
        $sql = "SELECT * FROM app_user WHERE id_user = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user) {
            $user['status'] = ($user['is_active'] == 1) ? 'aktif' : 'non_aktif';
        }
        
        return $user;
    }
    
    // Get user by username
    public function getUserByUsername($username) {
        $sql = "SELECT * FROM app_user WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user) {
            $user['status'] = ($user['is_active'] == 1) ? 'aktif' : 'non_aktif';
        }
        
        return $user;
    }
    
    // Check if username exists
    public function usernameExists($username) {
        $user = $this->getUserByUsername($username);
        return !empty($user);
    }
    
    // Login method
    public function login($username, $password) {
        $sql = "SELECT * FROM app_user WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Check if user is active
            if ($user['is_active'] == 0) {
                return false; // User is inactive
            }
            return $user;
        }
        
        return false;
    }
    
    // Create user
    public function createUser($username, $password, $role) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $is_active = 1; // Default active
        $user_name = $username; // Use username as user_name default
        
        $sql = "INSERT INTO app_user (username, password_hash, user_name, role, is_active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssi", $username, $hashed_password, $user_name, $role, $is_active);
        
        return $stmt->execute();
    }
    
    // Register method (alias for createUser)
    public function register($username, $password, $user_name, $role) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $is_active = 1;
        
        $sql = "INSERT INTO app_user (username, password_hash, user_name, role, is_active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssi", $username, $hashed_password, $user_name, $role, $is_active);
        
        return $stmt->execute();
    }
    
    // Update user status
    public function updateStatus($id, $status) {
        // Convert status to is_active (aktif = 1, non_aktif = 0)
        $is_active = ($status == 'aktif') ? 1 : 0;
        
        $sql = "UPDATE app_user SET is_active = ? WHERE id_user = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $is_active, $id);
        
        return $stmt->execute();
    }
}
?>
