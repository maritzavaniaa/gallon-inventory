<?php
class Grade {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT * FROM grade_employee WHERE 1=1";
        $params = [];
        $types = "";
    
        if (!empty($filters['search'])) {
            $sql .= " AND name_grade LIKE ?";
            $params[] = "%" . $filters['search'] . "%";
            $types .= "s";
        }
    
        $sql .= " ORDER BY name_grade ASC";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function create($name_grade, $default_allowance) {
        $sql = "INSERT INTO grade_employee (name_grade, default_allowance, updated_by) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $name_grade, $default_allowance, $_SESSION['user_id']);
        return $stmt->execute();
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM grade_employee WHERE id_grade = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function update($id, $name_grade, $default_allowance) {
        $sql = "UPDATE grade_employee SET name_grade=?, default_allowance=?, updated_by=? WHERE id_grade=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("siii", $name_grade, $default_allowance, $_SESSION['user_id'], $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM grade_employee WHERE id_grade = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
}
?>
