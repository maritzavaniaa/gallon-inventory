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
        // Untuk non-karyawan, id_grade harus NULL (bukan string kosong)
        $id_grade = ($data['type_subject'] == 'karyawan' && !empty($data['id_grade'])) 
                    ? (int)$data['id_grade'] 
                    : null;
        
        // Untuk karyawan, default_allowance NULL (ikut grade)
        $default_allowance = ($data['type_subject'] == 'non_karyawan' && !empty($data['default_allowance'])) 
                             ? (int)$data['default_allowance'] 
                             : null;
        
        $sql = "INSERT INTO subject (id_share, type_subject, name_subject, division, status, inactive_note, id_grade, default_allowance, updated_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        
        $id_share = $data['id_share'];
        $type_subject = $data['type_subject'];
        $name_subject = $data['name_subject'];
        $division = $data['division'];
        $status = $data['status'];
        $inactive_note = $data['inactive_note'];
        $updated_by = $_SESSION['user_id'];
        
        $stmt->bind_param("ssssssiii", 
            $id_share, $type_subject, $name_subject, 
            $division, $status, $inactive_note,
            $id_grade, $default_allowance, $updated_by
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
        // Handle NULL untuk id_grade dan default_allowance
        $id_grade = ($data['type_subject'] == 'karyawan' && !empty($data['id_grade'])) 
                    ? (int)$data['id_grade'] 
                    : null;
        
        $default_allowance = ($data['type_subject'] == 'non_karyawan' && !empty($data['default_allowance'])) 
                             ? (int)$data['default_allowance'] 
                             : null;
        
        $sql = "UPDATE subject SET type_subject=?, name_subject=?, division=?, status=?, inactive_note=?, 
                id_grade=?, default_allowance=?, updated_by=? WHERE id_share=?";
        $stmt = $this->conn->prepare($sql);
        
        // Assign semua variables
        $type_subject = $data['type_subject'];
        $name_subject = $data['name_subject'];
        $division = $data['division'];
        $status = $data['status'];
        $inactive_note = $data['inactive_note'];
        $updated_by = $_SESSION['user_id'];
        $id_share = $data['id_share'];
        
        $stmt->bind_param("sssssiiis", 
            $type_subject, $name_subject, $division, 
            $status, $inactive_note,
            $id_grade, $default_allowance, 
            $updated_by, $id_share
        );
        
        return $stmt->execute();
    }
    
    
    public function delete($id_share) {
        $sql = "DELETE FROM subject WHERE id_share = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $id_share);
        return $stmt->execute();
    }

    public function checkHasTransaction($id_share) {
        $sql = "SELECT COUNT(*) as total FROM gallon_tsx WHERE id_share = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $id_share);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }
    
    public function checkIdExists($id_share) {
        $sql = "SELECT COUNT(*) as total FROM subject WHERE id_share = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $id_share);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }
    
}
?> 
