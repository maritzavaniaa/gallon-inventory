<?php
class Transaction {
    private $conn;
    private $config;
    
    public function __construct($db) {
        $this->conn = $db;
        $this->config = include '../app/helpers/business-rule.php';
    }
    
    /**
     * Create Transaction OUT (distribusi galon ke subject)
     * Translation dari: create_tsx_out()
     */
    public function createOut($data) {
        $idShare = $data['id_share'];
        $quantity = (int)$data['quantity'];
        $tsxDate = $data['tsx_date'] ?? date('Y-m-d');
        $referenceDoc = $data['reference_doc'] ?? null;
        $notes = !empty($data['notes']) ? $data['notes'] : null;

        $recordedBy = $_SESSION['user_id'] ?? null;
        if (!$recordedBy) {
            return [
                'success' => false, 
                'message' => 'Sesi berakhir. Silakan login kembali untuk mencatat transaksi.'];
        }
        
        // Validation
        $validation = $this->validateOut($idShare, $quantity, $tsxDate);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }
        
        // Get current allowance info
        require_once '../app/models/MonthlyAllowance.php';
        $allowanceModel = new MonthlyAllowance($this->conn);
        $allowance_month = date('Y-m-01', strtotime($tsxDate));
        $allowanceInfo = $allowanceModel->getSubjectAllowance($idShare, $allowance_month);
        $newAllowanceInfo = $allowanceModel->getSubjectAllowance($idShare, $allowance_month);
        
        // Check quota (based on config)
        if ($this->config['quota_enforcement'] == 'block') {
            if ($allowanceInfo['remaining'] < $quantity) {
                return [
                    'success' => false,
                    'message' => "Jatah tidak cukup. Sisa: {$allowanceInfo['remaining']}, Diminta: {$quantity}",
                    'allowance_info' => $allowanceInfo
                ];
            }
        }
        
        // Insert transaction
        $this->conn->begin_transaction();
        
        try {
            $sql = "INSERT INTO gallon_tsx 
                (id_share, date_tsx, month_tsx, type_tsx, quantity, reference_doc, notes, recorded_by, created_at) 
                VALUES (?, ?, ?, 'OUT', ?, ?, ?, ?, NOW())";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssissi", 
                $idShare, 
                $tsxDate, 
                $allowance_month, 
                $quantity, 
                $referenceDoc, 
                $notes, 
                $recordedBy
            );
            
            $stmt->execute();
            $idTsx = $this->conn->insert_id;
            
            $this->conn->commit();
            
            // Recalculate allowance info after insert
            $newAllowanceInfo = $allowanceModel->getSubjectAllowance($idShare, $allowance_month);
            
            return [
                'success' => true,
                'id_tsx' => $idTsx,
                'allowance' => $newAllowanceInfo['allowance'],
                'used' => $newAllowanceInfo['used'],
                'remaining' => $newAllowanceInfo['remaining']
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Create Transaction IN (FR-TSX-02)
     */
    public function createIn($data) {
        $quantity = (int)$data['quantity'];
        $tsxDate = $data['tsx_date'] ?? date('Y-m-d');
        $referenceDoc = !empty($data['reference_doc']) ? $data['reference_doc'] : null;
        $notes = $data['notes'] ?? null;
        $monthTsx = date('Y-m-01', strtotime($tsxDate));

        $recordedBy = $_SESSION['user_id'] ?? null;
        if (!$recordedBy) {
            return ['success' => false, 'message' => 'User tidak terautentikasi'];
        }
        
        if ($quantity <= 0) {
            return ['success' => false, 'message' => 'Quantity harus lebih dari 0'];
        }
        
        // Query disesuaikan dengan skema: id_share untuk IN biasanya NULL atau 'SUPPLIER'
        $sql = "INSERT INTO gallon_tsx 
            (id_share, date_tsx, month_tsx, type_tsx, quantity, reference_doc, notes, recorded_by, created_at) 
            VALUES (NULL, ?, ?, 'IN', ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        // Binding: s (date), s (month), i (qty), s (ref), s (notes), i (user)
        $stmt->bind_param("ssissi", $tsxDate, $monthTsx, $quantity, $referenceDoc, $notes, $recordedBy);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'transaction_id' => $this->conn->insert_id,
                'quantity' => $quantity
            ];
        }
        
        return ['success' => false, 'message' => 'Gagal menyimpan transaksi masuk'];
    }
    
    /**
     * Validate transaction OUT
     */
    private function validateOut($idShare, $quantity, $tsxDate) {
        // 1. Check subject exists & active
        $sql = "SELECT status FROM subject WHERE id_share = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $idShare);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if (!$result) {
            return ['valid' => false, 'message' => 'Subject tidak ditemukan'];
        }
        
        if ($result['status'] != 'aktif') {
            return ['valid' => false, 'message' => 'Subject tidak aktif'];
        }
        
        // 2. Check quantity
        if ($quantity <= 0 && !$this->config['allow_negative_qty']) {
            return ['valid' => false, 'message' => 'Quantity harus lebih dari 0'];
        }
        
        // 3. Check date
        $today = date('Y-m-d');
        if (!$this->config['allow_future_date'] && $tsxDate > $today) {
            return ['valid' => false, 'message' => 'Tanggal tidak boleh di masa depan (Maksimal hari ini).'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Get all transactions with pagination & filters
     */
    public function getAllPaginated($filters = [], $offset = 0, $limit = 50) {
        $sql = "SELECT 
                    t.id_tsx,
                    t.id_share,
                    t.tsx_date,
                    t.tsx_type,
                    t.quantity,
                    t.reference_doc,
                    t.notes,
                    t.created_at,
                    s.name_subject,
                    s.division,
                    s.type_subject,
                    u.username as recorded_by_name
                FROM gallon_tsx t
                LEFT JOIN subject s ON t.id_share = s.id_share
                LEFT JOIN app_user u ON t.recorded_by = u.id_user
                WHERE 1=1";
        
        $params = [];
        $types = "";
        
        // Filter by type (IN/OUT)
        if (!empty($filters['tsx_type'])) {
            $sql .= " AND t.type_tsx = ?";
            $params[] = $filters['tsx_type'];
            $types .= "s";
        }
        
        // Filter by subject (search)
        if (!empty($filters['search'])) {
            $sql .= " AND (s.name_subject LIKE ? OR t.id_share LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }
        
        // Filter by date range
        if (!empty($filters['date_from'])) {
            $sql .= " AND t.date_tsx >= ?";
            $params[] = $filters['date_from'];
            $types .= "s";
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND t.date_tsx <= ?";
            $params[] = $filters['date_to'];
            $types .= "s";
        }
        
        // Filter by division
        if (!empty($filters['division'])) {
            $sql .= " AND s.division = ?";
            $params[] = $filters['division'];
            $types .= "s";
        }
        
        $sql .= " ORDER BY t.tsx_date DESC, t.created_at DESC LIMIT ? OFFSET ?";
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
    
    /**
     * Count total (for pagination)
     */
    public function countAll($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM gallon_tsx t
                LEFT JOIN subject s ON t.id_share = s.id_share
                WHERE 1=1";
        
        $params = [];
        $types = "";
        
        // Apply same filters as getAllPaginated
        if (!empty($filters['tsx_type'])) {
            $sql .= " AND t.tsx_type = ?";
            $params[] = $filters['tsx_type'];
            $types .= "s";
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (s.name_subject LIKE ? OR t.id_share LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND t.tsx_date >= ?";
            $params[] = $filters['date_from'];
            $types .= "s";
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND t.tsx_date <= ?";
            $params[] = $filters['date_to'];
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
        $result = $stmt->get_result()->fetch_assoc();
        return (int)$result['total'];
    }
    

    /**
     * Get Current Stock (FR-RPT-01)
     */
    public function getCurrentStock() {
        $sql = "SELECT 
                    SUM(CASE WHEN type_tsx = 'IN' AND deleted_at IS NULL THEN quantity ELSE 0 END) as stock_in,
                    SUM(CASE WHEN type_tsx = 'OUT' AND deleted_at IS NULL THEN quantity ELSE 0 END) as stock_out
                FROM gallon_tsx";
        
        $result = $this->conn->query($sql)->fetch_assoc();
        
        $in = (int)$result['stock_in'];
        $out = (int)$result['stock_out'];
        
        return [
            'stock_in' => $in,
            'stock_out' => $out,
            'remaining' => ($in - $out)
        ];
    }

    /**
     * Get Daily Stats for Transaction Page
     */
    public function getStatsToday() {
        $today = date('Y-m-d');
        $sql = "SELECT 
                    SUM(CASE WHEN type_tsx = 'IN' THEN quantity ELSE 0 END) as total_in,
                    SUM(CASE WHEN type_tsx = 'OUT' THEN quantity ELSE 0 END) as total_out
                FROM gallon_tsx 
                WHERE date_tsx = ? 
                AND deleted_at IS NULL"; 
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return [
            'in' => (int)($result['total_in'] ?? 0),
            'out' => (int)($result['total_out'] ?? 0)
        ];
    }
    
    /**
     * Delete transaction (admin only)
     */
    public function delete($idTsx) {
        $sql = "DELETE FROM gallon_tsx WHERE id_tsx = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idTsx);
        return $stmt->execute();
    }

    public function getDashboardStats($range) {
        $currentWhere = "WHERE deleted_at IS NULL";
        $prevWhere = "WHERE deleted_at IS NULL";
        
        // Logika penentuan range sekarang vs sebelumnya
        if ($range === 'week') {
            $currentWhere .= " AND YEARWEEK(date_tsx, 1) = YEARWEEK(CURDATE(), 1)";
            $prevWhere .= " AND YEARWEEK(date_tsx, 1) = YEARWEEK(CURDATE(), 1) - 1";
        } elseif ($range === 'month') {
            $currentWhere .= " AND MONTH(date_tsx) = MONTH(CURDATE()) AND YEAR(date_tsx) = YEAR(CURDATE())";
            $prevWhere .= " AND MONTH(date_tsx) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(date_tsx) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
        } elseif ($range === 'year') {
            $currentWhere .= " AND YEAR(date_tsx) = YEAR(CURDATE())";
            $prevWhere .= " AND YEAR(date_tsx) = YEAR(CURDATE()) - 1";
        }
    
        // Query data periode ini
        $sqlCurr = "SELECT 
                    SUM(CASE WHEN type_tsx = 'OUT' THEN quantity ELSE 0 END) as out_curr,
                    SUM(CASE WHEN type_tsx = 'IN' THEN quantity ELSE 0 END) as in_curr
                    FROM gallon_tsx $currentWhere";
        
        // Query data periode lalu
        $sqlPrev = "SELECT 
                    SUM(CASE WHEN type_tsx = 'OUT' THEN quantity ELSE 0 END) as out_prev,
                    SUM(CASE WHEN type_tsx = 'IN' THEN quantity ELSE 0 END) as in_prev
                    FROM gallon_tsx $prevWhere";
    
        $curr = $this->conn->query($sqlCurr)->fetch_assoc();
        $prev = $this->conn->query($sqlPrev)->fetch_assoc();
    
        // Fungsi hitung persen: ((skrg - lalu) / lalu) * 100
        $calcPct = fn($c, $p) => $p > 0 ? round((($c - $p) / $p) * 100) : ($c > 0 ? 100 : 0);
    
        return [
            'total_out' => $curr['out_curr'] ?? 0,
            'total_in'  => $curr['in_curr'] ?? 0,
            'pct_out'   => $calcPct($curr['out_curr'], $prev['out_prev']),
            'pct_in'    => $calcPct($curr['in_curr'], $prev['in_prev'])
        ];
    }
    
    public function getChartData($range) {
        $labels = [];
        $dataOut = [];
        $dataIn = [];
    
        if ($range === 'week') {
            // Label: Senin - Minggu
            $labels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            // Mengambil data 7 hari terakhir (dimulai dari Senin minggu ini)
            $sql = "SELECT 
                        WEEKDAY(date_tsx) as day_idx,
                        SUM(CASE WHEN type_tsx = 'OUT' THEN quantity ELSE 0 END) as total_out,
                        SUM(CASE WHEN type_tsx = 'IN' THEN quantity ELSE 0 END) as total_in
                    FROM gallon_tsx 
                    WHERE YEARWEEK(date_tsx, 1) = YEARWEEK(CURDATE(), 1) 
                    AND deleted_at IS NULL
                    GROUP BY day_idx";
            
            $result = $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
            $mapped = array_fill(0, 7, ['out' => 0, 'in' => 0]);
            foreach ($result as $row) {
                $mapped[$row['day_idx']] = ['out' => (int)$row['total_out'], 'in' => (int)$row['total_in']];
            }
            $dataOut = array_column($mapped, 'out');
            $dataIn = array_column($mapped, 'in');
    
        } elseif ($range === 'month') {
            // Label: Minggu 1 - Minggu 5
            $labels = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Minggu 5'];
            $sql = "SELECT 
                        FLOOR((DAY(date_tsx) - 1) / 7) as week_idx,
                        SUM(CASE WHEN type_tsx = 'OUT' THEN quantity ELSE 0 END) as total_out,
                        SUM(CASE WHEN type_tsx = 'IN' THEN quantity ELSE 0 END) as total_in
                    FROM gallon_tsx 
                    WHERE MONTH(date_tsx) = MONTH(CURDATE()) AND YEAR(date_tsx) = YEAR(CURDATE())
                    AND deleted_at IS NULL
                    GROUP BY week_idx";
            
            $result = $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
            $mapped = array_fill(0, 5, ['out' => 0, 'in' => 0]);
            foreach ($result as $row) {
                if($row['week_idx'] < 5) {
                    $mapped[$row['week_idx']] = ['out' => (int)$row['total_out'], 'in' => (int)$row['total_in']];
                }
            }
            $dataOut = array_column($mapped, 'out');
            $dataIn = array_column($mapped, 'in');
    
        } elseif ($range === 'year') {
            // Label: Jan - Des
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $sql = "SELECT 
                        MONTH(date_tsx) - 1 as month_idx,
                        SUM(CASE WHEN type_tsx = 'OUT' THEN quantity ELSE 0 END) as total_out,
                        SUM(CASE WHEN type_tsx = 'IN' THEN quantity ELSE 0 END) as total_in
                    FROM gallon_tsx 
                    WHERE YEAR(date_tsx) = YEAR(CURDATE())
                    AND deleted_at IS NULL
                    GROUP BY month_idx";
            
            $result = $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
            $mapped = array_fill(0, 12, ['out' => 0, 'in' => 0]);
            foreach ($result as $row) {
                $mapped[$row['month_idx']] = ['out' => (int)$row['total_out'], 'in' => (int)$row['total_in']];
            }
            $dataOut = array_column($mapped, 'out');
            $dataIn = array_column($mapped, 'in');
        }
    
        return [
            'labels' => $labels,
            'out' => $dataOut,
            'in' => $dataIn
        ];
    }
}
?>
