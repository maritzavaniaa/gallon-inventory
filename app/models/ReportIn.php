<?php
class ReportIn {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getInReports($filters = [], $offset = 0, $limit = 10) {
        $sql = "SELECT t.*, u.user_name as admin_name 
                FROM gallon_tsx t 
                LEFT JOIN app_user u ON t.recorded_by = u.id_user 
                WHERE t.type_tsx = 'IN' AND t.deleted_at IS NULL";
    
        $params = [];
        $types = "";
    
        // Tambahkan Filter Search (No Referensi)
        if (!empty($filters['search'])) {
            $sql .= " AND t.reference_doc LIKE ?";
            $params[] = "%" . $filters['search'] . "%";
            $types .= "s";
        }
    
        if (!empty($filters['date_start'])) {
            $sql .= " AND t.date_tsx >= ?";
            $params[] = $filters['date_start'];
            $types .= "s";
        }
    
        if (!empty($filters['date_end'])) {
            $sql .= " AND t.date_tsx <= ?";
            $params[] = $filters['date_end'];
            $types .= "s";
        }
    
        $sql .= " ORDER BY t.date_tsx DESC, t.created_at DESC LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;
        $types .= "ii";
    
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function countInReports($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM gallon_tsx WHERE type_tsx = 'IN' AND deleted_at IS NULL";
        if (!empty($filters['date_start'])) $sql .= " AND date_tsx >= '{$filters['date_start']}'";
        if (!empty($filters['date_end'])) $sql .= " AND date_tsx <= '{$filters['date_end']}'";
        
        return $this->db->query($sql)->fetch_assoc()['total'];
    }

    public function getInSummary($filters) {
        $sql = "SELECT SUM(quantity) as total_qty 
                FROM gallon_tsx 
                WHERE type_tsx = 'IN' AND deleted_at IS NULL";
    
        $params = [];
        $types = "";
    
        if (!empty($filters['date_start'])) {
            $sql .= " AND date_tsx >= ?";
            $params[] = $filters['date_start'];
            $types .= "s";
        }
        if (!empty($filters['date_end'])) {
            $sql .= " AND date_tsx <= ?";
            $params[] = $filters['date_end'];
            $types .= "s";
        }
    
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total_qty'] ?? 0;
    }

    public function softDelete($id) {
        $sql = "UPDATE gallon_tsx SET deleted_at = NOW() WHERE id_tsx = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }
}