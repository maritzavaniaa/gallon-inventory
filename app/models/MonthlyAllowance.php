<?php
class MonthlyAllowance {
    private $conn;
    private $config;
    
    public function __construct($db) {
        $this->conn = $db;
        $this->config = include '../app/helpers/business-rule.php';
    }
    
    /**
     * Generate monthly allowance untuk semua active subjects
     * Translation dari: generate_monthly_allowance()
     */
    public function generate($targetMonth) {
        // Format: 'YYYY-MM-01'
        $allowance_month = date('Y-m-01', strtotime($targetMonth));
        
        // Check if already generated
        if ($this->isGenerated($allowance_month)) {
            return [
                'success' => false,
                'message' => "Allowance untuk bulan {$allowance_month} sudah di-generate"
            ];
        }
        
        // Start transaction
        $this->conn->begin_transaction();
        
        try {
            $count = 0;
            
            // Get all active subjects with their allowance
            $sql = "SELECT 
                        s.id_share,
                        s.type_subject,
                        s.default_allowance,
                        g.default_allowance as grade_allowance
                    FROM subject s
                    LEFT JOIN grade_employee g ON s.id_grade = g.id_grade
                    WHERE s.status = 'aktif'";
            
            $result = $this->conn->query($sql);
            
            while ($subject = $result->fetch_assoc()) {
                // Determine allowance amount & source
                if ($subject['type_subject'] == 'karyawan') {
                    $amount = $subject['grade_allowance'];
                    $source = 'grade';
                } else {
                    $amount = $subject['default_allowance'];
                    $source = 'manual';
                }
                
                // Insert into monthly_allowance (create table if not exists)
                $insertSql = "INSERT INTO monthly_allowance 
                             (id_share, allowance_month, amount, source, created_at) 
                             VALUES (?, ?, ?, ?, NOW())";
                
                $stmt = $this->conn->prepare($insertSql);
                $stmt->bind_param("ssis", 
                    $subject['id_share'], 
                    $allowance_month, 
                    $amount, 
                    $source
                );
                $stmt->execute();
                
                $count++;
            }
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'allowance_month' => $allowance_month,
                'generated_count' => $count
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
     * Check if allowance sudah di-generate untuk bulan tertentu
     */
    public function isGenerated($allowance_month) {
        $sql = "SELECT COUNT(*) as total FROM monthly_allowance WHERE allowance_month = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $allowance_month);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] > 0;
    }
    
    /**
     * Get allowance info untuk subject tertentu
     * Translation dari: get_subject_allowance_info()
     */
    public function getSubjectAllowance($idShare, $allowance_month = null) {
        $allowance_month = $allowance_month ?: date('Y-m-01');
        
        // Get allowance
        $sql = "SELECT amount FROM monthly_allowance 
                WHERE id_share = ? AND allowance_month = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $idShare, $allowance_month);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if (!$result) {
            return [
                'allowance_month' => $allowance_month,
                'allowance' => 0,
                'used' => 0,
                'remaining' => 0,
                'message' => 'Allowance belum di-generate'
            ];
        }
        
        $allowance = $result['amount'];
        
        // Calculate used
        $usedSql = "SELECT COALESCE(SUM(quantity), 0) as used 
            FROM gallon_tsx 
            WHERE id_share = ? 
            AND type_tsx = 'OUT' 
            AND month_tsx = ?";
        
        $stmt2 = $this->conn->prepare($usedSql);
        $stmt2->bind_param("ss", $idShare, $allowance_month);
        $stmt2->execute();
        $usedResult = $stmt2->get_result()->fetch_assoc();
        $used = $usedResult['used'];
        
        return [
            'allowance_month' => $allowance_month,
            'allowance' => (int)$allowance,
            'used' => (int)$used,
            'remaining' => (int)($allowance - $used),
            'percentage_used' => $allowance > 0 ? round(($used / $allowance) * 100, 1) : 0
        ];
    }
    
    /**
     * Get monthly summary (all subjects)
     * Translation dari: get_monthly_summary()
     */
    public function getMonthlySummary($allowance_month = null) {
        $allowance_month = $allowance_month ?: date('Y-m-01');
        
        // Total allowance distributed
        $sql = "SELECT 
                    COALESCE(SUM(amount), 0) as total_allowance,
                    COUNT(*) as total_subjects
                FROM monthly_allowance
                WHERE allowance_month = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $allowance_month);
        $stmt->execute();
        $allowanceData = $stmt->get_result()->fetch_assoc();
        
        // Total used
        $usedSql = "SELECT 
        COALESCE(SUM(quantity), 0) as total_used,
        COUNT(DISTINCT id_share) as subjects_with_tsx
            FROM gallon_tsx
            WHERE type_tsx = 'OUT'
            AND month_tsx = ?"; // Menggunakan kolom month_tsx kamu

        $stmt2 = $this->conn->prepare($usedSql);
        $stmt2->bind_param("s", $allowance_month);
        $stmt2->execute();
        $usedData = $stmt2->get_result()->fetch_assoc();
        
        return [
            'allowance_month' => $allowance_month,
            'total_subjects' => (int)$allowanceData['total_subjects'],
            'subjects_with_transactions' => (int)$usedData['subjects_with_tsx'],
            'total_allowance' => (int)$allowanceData['total_allowance'],
            'total_used' => (int)$usedData['total_used'],
            'remaining_allowance' => (int)($allowanceData['total_allowance'] - $usedData['total_used'])
        ];
    }
    
    /**
     * Manual reset allowance (Admin only)
     */
    public function resetMonth($allowance_month) {
        $sql = "DELETE FROM monthly_allowance WHERE allowance_month = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $allowance_month);
        return $stmt->execute();
    }

    /**
     * Search subjects and their current quota (FR-TSX-01)
     * Digunakan untuk live search di form transaksi OUT
     */
    public function searchWithQuota($keyword, $allowance_month = null) {
        $allowance_month = $allowance_month ?: date('Y-m-01');
        $searchTerm = "%" . trim($keyword) . "%";
    
        // Mencari orangnya dulu di tabel subject
        $sql = "SELECT id_share, name_subject, division 
                FROM subject 
                WHERE (id_share LIKE ? OR name_subject LIKE ?) 
                AND status = 'aktif'";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            // DI SINI KITA AMBIL KUOTA ASLINYA (10, 13, dsb)
            $info = $this->getSubjectAllowance($row['id_share'], $allowance_month);
            
            $data[] = [
                'id_share'  => $row['id_share'],
                'name'      => $row['name_subject'],
                'division'  => $row['division'],
                'allowance' => $info['allowance'], // Ini jatah asli (misal: 13)
                'used'      => $info['used'],      // Yang sudah dipakai
                'remaining' => $info['remaining']  // Sisa yang boleh diambil
            ];
        }
    
        return [
            'success' => !empty($data),
            'data' => $data
        ];
    }
}
?>
