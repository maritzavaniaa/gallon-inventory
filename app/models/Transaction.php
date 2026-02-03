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
        if (!$this->config['allow_future_date'] && strtotime($tsxDate) > time()) {
            return ['valid' => false, 'message' => 'Tanggal tidak boleh di masa depan'];
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
                    SUM(CASE WHEN type_tsx = 'IN' THEN quantity ELSE 0 END) as stock_in,
                    SUM(CASE WHEN type_tsx = 'OUT' THEN quantity ELSE 0 END) as stock_out
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
     * Delete transaction (admin only)
     */
    public function delete($idTsx) {
        $sql = "DELETE FROM gallon_tsx WHERE id_tsx = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idTsx);
        return $stmt->execute();
    }
}
?>
