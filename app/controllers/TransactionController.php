<?php
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/models/Transaction.php';
require_once '../app/models/MonthlyAllowance.php';

class TransactionController {
    private $db;
    private $transactionModel;
    private $allowanceModel;
    
    public function __construct($db) {
        // FR-AUTH-02: Memastikan user sudah login (Semua role boleh akses)
        AuthMiddleware::checkLog(); 
        
        $this->db = $db;
        $this->transactionModel = new Transaction($db);
        $this->allowanceModel = new MonthlyAllowance($db);
    }
    
    public function index() {
        // FR-RPT-01: Menampilkan stok gudang di dashboard/index
        $stock = $this->transactionModel->getCurrentStock();
        $stats_today = $this->transactionModel->getStatsToday();
        require_once '../app/views/transactions/index.php';
    }

    // FR-TSX-01: Input Transaksi Keluar
    public function storeOut() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ambil data dari POST
            $data = [
                'id_share' => $_POST['id_share'],
                'quantity' => $_POST['quantity'],
                'tsx_date'      => $_POST['tsx_date'],
                'notes' => $_POST['notes'] ?? '',
                'reference_doc' => $_POST['reference_doc'] ?? null
            ];

            // Panggil model (Model kamu sudah punya method createOut)
            $result = $this->transactionModel->createOut($data);

            $_SESSION['flash'] = $result;
            header('Location: ?url=transactions');
            exit;
        }
    }

    // FR-TSX-02: Input Transaksi Masuk
    public function storeIn() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['recorded_by'] = $_SESSION['user_id'];

            $result = $this->transactionModel->createIn($data);
            $_SESSION['flash'] = $result;
            header('Location: ?url=transactions');
            exit;
        }
    }

    // FR-TSX-01: Ajax Search untuk mencari ID Share/Nama & Sisa Kuota
    public function searchSubject() {
        if (ob_get_length()) ob_clean(); 
    
        header('Content-Type: application/json');
        
        try {
            $keyword = $_GET['q'] ?? '';
            if (empty($keyword)) {
                echo json_encode(['success' => false, 'message' => 'Keyword kosong']);
                return;
            }
    
            // 2. Pastikan memanggil method dari model yang benar
            $result = $this->allowanceModel->searchWithQuota($keyword);
            
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}