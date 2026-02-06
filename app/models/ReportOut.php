<?php
class ReportOut {
    private $conn;
    public function __construct($db) { 
        $this->conn = $db; 
    }

    public function getOutReports($filters, $offset) {
        $sql = "SELECT t.*, s.name_subject, s.division, s.type_subject, 
                g.name_grade,
                ma.amount,
                u.user_name as admin_name
        FROM gallon_tsx t
        JOIN subject s ON t.id_share = s.id_share
        JOIN app_user u ON t.recorded_by = u.id_user
        LEFT JOIN grade_employee g ON s.id_grade = g.id_grade
        -- Join ke monthly_allowance berdasarkan ID dan bulan/tahun yang sama
        LEFT JOIN monthly_allowance ma ON t.id_share = ma.id_share 
            AND DATE_FORMAT(t.month_tsx, '%Y-%m') = DATE_FORMAT(ma.allowance_month, '%Y-%m')
        WHERE t.type_tsx = 'OUT'";
    
        $params = [];
        $types = "";
    
        // Filter-filter (Tetap sama seperti kode kamu sebelumnya)
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
        if (!empty($filters['division'])) {
            $sql .= " AND s.division = ?";
            $params[] = $filters['division'];
            $types .= "s";
        }
        if (!empty($filters['search'])) {
            $sql .= " AND t.id_share LIKE ?";
            $params[] = "%" . $filters['search'] . "%";
            $types .= "s";
        }
    
        $sql .= " ORDER BY t.date_tsx DESC, t.created_at DESC LIMIT ? OFFSET ?";
        $params[] = (int)$filters['limit'];
        $params[] = (int)$offset;
        $types .= "ii";
        
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
    
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function countOutReports($filters) {
        $sql = "SELECT COUNT(*) as total 
                FROM gallon_tsx t
                JOIN subject s ON t.id_share = s.id_share
                WHERE t.type_tsx = 'OUT'";
    
        $params = [];
        $types = "";
  
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
        if (!empty($filters['division'])) {
            $sql .= " AND s.division = ?";
            $params[] = $filters['division'];
            $types .= "s";
        }
        if (!empty($filters['search'])) {
            $sql .= " AND t.id_share LIKE ?";
            $params[] = "%" . $filters['search'] . "%";
            $types .= "s";
        }
    
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function getDistinctDivisions() {
        $sql = "SELECT DISTINCT division FROM subject WHERE division IS NOT NULL AND division != '' ORDER BY division ASC";
        $result = $this->conn->query($sql);
        $divisions = [];
        while ($row = $result->fetch_assoc()) {
            $divisions[] = $row['division'];
        }
        return $divisions;
    }

    public function getOutSummary($filters) {
        $sql = "SELECT 
                    SUM(t.quantity) as total_qty,
                    COUNT(DISTINCT t.id_share) as total_penerima
                FROM gallon_tsx t
                JOIN subject s ON t.id_share = s.id_share
                LEFT JOIN monthly_allowance ma ON t.id_share = ma.id_share 
                     AND DATE_FORMAT(t.month_tsx, '%Y-%m') = DATE_FORMAT(ma.allowance_month, '%Y-%m')
                WHERE t.type_tsx = 'OUT'";
    
        $params = [];
        $types = "";

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
        if (!empty($filters['division'])) {
            $sql .= " AND s.division = ?";
            $params[] = $filters['division'];
            $types .= "s";
        }
        if (!empty($filters['search'])) {
            $sql .= " AND t.id_share LIKE ?";
            $params[] = "%" . $filters['search'] . "%";
            $types .= "s";
        }
    
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
    
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
