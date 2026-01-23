<?php
class Subject {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAll() {
        $sql = "SELECT s.*, g.name_grade 
                FROM subject s 
                LEFT JOIN grade_employee g ON s.id_grade = g.id_grade 
                ORDER BY s.name_subject";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public function create($data) {
        $sql = "INSERT INTO subject (id_share, type_subject, name_subject, division, status, inactive_note, id_grade, default_allowance, updated_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssssiii", 
            $data['id_share'], $data['type_subject'], $data['name_subject'], 
            $data['division'], $data['status'], $data['inactive_note'],
            $data['id_grade'] ?? null, $data['default_allowance'] ?? null, $_SESSION['user_id']
        );
        return $stmt->execute();
    }
    
    public function findById($id_share) {
        $sql = "SELECT s.*, g.name_grade 
                FROM subject s 
                LEFT JOIN grade_employee g ON s.id_grade = g.id_grade 
                WHERE s.id_share = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $id_share);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function update($data) {
        $sql = "UPDATE subject SET type_subject=?, name_subject=?, division=?, status=?, inactive_note=?, 
                id_grade=?, default_allowance=?, updated_by=? WHERE id_share=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssiiis", 
            $data['type_subject'], $data['name_subject'], $data['division'], 
            $data['status'], $data['inactive_note'],
            $data['id_grade'] ?? null, $data['default_allowance'] ?? null, 
            $_SESSION['user_id'], $data['id_share']
        );
        return $stmt->execute();
    }
    
    public function delete($id_share) {
        $sql = "DELETE FROM subject WHERE id_share = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $id_share);
        return $stmt->execute();
    }
}
?>
