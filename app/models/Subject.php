<?php
class Subject {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAll($filters = []) {
        // Gunakan JOIN yang benar sesuai nama tabelmu (subject & grade_employee)
        $query = "SELECT s.*, g.name_grade 
                  FROM subject s 
                  LEFT JOIN grade_employee g ON s.id_grade = g.id_grade 
                  WHERE 1=1";
        
        // 1. Filter Search (Nama atau ID)
        if (!empty($filters['search'])) {
            $s = $this->conn->real_escape_string($filters['search']);
            $query .= " AND (s.name_subject LIKE '%$s%' OR s.id_share LIKE '%$s%')";
        }
        
        // 2. Filter Tipe (karyawan/non_karyawan)
        if (!empty($filters['type'])) {
            $t = $this->conn->real_escape_string($filters['type']);
            $query .= " AND s.type_subject = '$t'";
        }
        
        // 3. Filter Status (aktif/non_aktif)
        if (!empty($filters['status'])) {
            $st = $this->conn->real_escape_string($filters['status']);
            $query .= " AND s.status = '$st'";
        }

        // 4. Filter Division
        if (!empty($filters['division'])) {
            $d = $this->conn->real_escape_string($filters['division']);
            $query .= " AND s.division = '$d'";
        }
    
        $query .= " ORDER BY s.name_subject ASC";
        
        $result = $this->conn->query($query);
        if (!$result) return []; // Kembalikan array kosong jika query gagal
        
        return $result->fetch_all(MYSQLI_ASSOC);
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

    public function countAll($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM subject s WHERE 1=1";
        $params = [];
        $types = "";
        
        if (!empty($filters['search'])) {
            $sql .= " AND s.id_share LIKE ?";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $types .= "s"; 
        }
        
        if (!empty($filters['type'])) {
            $sql .= " AND s.type_subject = ?";
            $params[] = $filters['type'];
            $types .= "s";
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND s.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        if (!empty($filters['division'])) {
            $sql .= " AND s.division = ?";
            $params[] = $filters['division'];
            $types .= "s";
        }
        
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return (int)$row['total'];
    }

    public function getUniqueDivisions() {
        $sql = "SELECT DISTINCT division FROM subject 
                WHERE division IS NOT NULL AND division != '' 
                ORDER BY division ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllPaginated($filters = [], $offset = 0, $limit = 50) {
        $sql = "SELECT s.*, g.name_grade, g.default_allowance as grade_allowance 
                FROM subject s 
                LEFT JOIN grade_employee g ON s.id_grade = g.id_grade 
                WHERE 1=1";
        
        $params = [];
        $types = "";
        
        if (!empty($filters['search'])) {
            $sql .= " AND s.id_share LIKE ?";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $types .= "s"; 
        }
        
        if (!empty($filters['type'])) {
            $sql .= " AND s.type_subject = ?";
            $params[] = $filters['type'];
            $types .= "s";
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND s.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        if (!empty($filters['division'])) {
            $sql .= " AND s.division = ?";
            $params[] = $filters['division'];
            $types .= "s";
        }
        
        $sql .= " ORDER BY s.name_subject ASC LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;
        $types .= "ii";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'aktif' THEN 1 ELSE 0 END) as aktif,
                    SUM(CASE WHEN status = 'non_aktif' THEN 1 ELSE 0 END) as off
                FROM subject";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
}
?> 
