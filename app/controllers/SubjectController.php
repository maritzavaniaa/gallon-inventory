<?php
require_once 'Controller.php';
require_once '../app/models/Subject.php';
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/helpers/functions.php';

class SubjectController extends Controller{
    private $subjectModel;
    
    public function __construct($db) {
        // 3. Panggil constructor bapaknya agar $this->db terisi
        parent::__construct($db); 
        
        AuthMiddleware::checkAdmin();
        $this->subjectModel = new Subject($db);
    }
    
    public function index() {
        AuthMiddleware::checkAdmin();
    
        // 1. Ambil LIMIT dari dropdown URL (lewat fungsi Bapaknya)
        $perPage = $this->getLimit(50); 
    
        // 2. Siapkan filter
        $filters = [
            'search'   => $_GET['search'] ?? '',
            'type'     => $_GET['type'] ?? '',
            'status'   => $_GET['status'] ?? '',
            'division' => $_GET['division'] ?? '',
        ];
    
        // 3. Pagination Logic
        $totalRecords = $this->subjectModel->countAll($filters);
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // PENTING: Kirim $perPage ke sini!
        $pagination = getPagination($totalRecords, $currentPage, $perPage);
        
        // 4. Ambil data dengan LIMIT dan OFFSET yang sudah dihitung
        // Gunakan $pagination['limit'] karena isinya sudah pasti angka dari $perPage
        $subjects = $this->subjectModel->getAllPaginated(
            $filters, 
            $pagination['offset'], 
            $pagination['limit']
        );
        
        // Ambil list divisi "Resmi" dari helpers/functions.php
        $officialDivisions = function_exists('getDivisionList') ? getDivisionList() : [];
        
        // Ambil divisi yang sudah ada di database (Unique)
        $dbDivisions = $this->subjectModel->getUniqueDivisions();
        $existingList = array_column($dbDivisions, 'division');
        
        // Gabungkan keduanya, hilangkan duplikat, dan urutkan A-Z
        $allDivisions = array_unique(array_merge($officialDivisions, $existingList));
        sort($allDivisions);

        $divisions = $this->subjectModel->getUniqueDivisions();
        $stats = $this->subjectModel->getStats();
        
        include '../app/views/subjects/index.php';
    }  
    
    public function create() {
        require_once '../app/models/Grade.php';
        $gradeModel = new Grade($this->db);
        $grades = $gradeModel->getAll();
        require_once '../app/views/subjects/create.php';
    }
    
    public function store() {
        if ($_POST) {
            // Validasi ID Share unik
            if ($this->subjectModel->checkIdExists($_POST['id_share'])) {
                header('Location: ?url=subjects/create&error=duplicate');
                exit;
            }
            
            $data = [
                'id_share' => strtoupper(trim($_POST['id_share'])),
                'type_subject' => $_POST['type_subject'],
                'name_subject' => trim($_POST['name_subject']),
                'division' => strtoupper(trim($_POST['division'] ?? '')),
                'status' => 'aktif', // Default aktif
                'inactive_note' => '',
                'id_grade' => $_POST['id_grade'] ?? null,
                'default_allowance' => $_POST['default_allowance'] ?? null
            ];
            
            if ($this->subjectModel->create($data)) {
                header('Location: ?url=subjects&success=1');
            } else {
                header('Location: ?url=subjects/create&error=1');
            }
        }
        exit;
    }
    
    public function edit($id_share) {
        $subject = $this->subjectModel->findById($id_share);
        if (!$subject) {
            header('Location: ?url=subjects&error=notfound');
            exit;
        }
        
        // Load grades untuk dropdown
        require_once '../app/models/Grade.php';
        $gradeModel = new Grade($this->db);
        $grades = $gradeModel->getAll();
        
        // Cek apakah punya transaksi
        $hasTransaction = $this->subjectModel->checkHasTransaction($id_share);
        
        require_once '../app/views/subjects/edit.php';
    }
    
    public function update($id_share) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $existing = $this->subjectModel->findById($id_share);
            
            $data = [
                'id_share' => $id_share,
                'type_subject' => $_POST['type_subject'] ?? $existing['type_subject'],
                'name_subject' => trim($_POST['name_subject']),
                'division' => strtoupper(trim($_POST['division'] ?? '')),
                'status' => $_POST['status'],
                'inactive_note' => trim($_POST['inactive_note'] ?? ''),
                'id_grade' => $_POST['id_grade'] ?? null,
                'default_allowance' => $_POST['default_allowance'] ?? null
            ];
            
            if ($this->subjectModel->update($data)) {
                header('Location: ?url=subjects&success=2');
            } else {
                header('Location: ?url=subjects&error=2');
            }
            exit;
        }
    }
    
    public function delete($id_share) {
        // Cek apakah punya transaksi
        if ($this->subjectModel->checkHasTransaction($id_share)) {
            header('Location: ?url=subjects&error=has_transaction');
            exit;
        }
        
        if ($this->subjectModel->delete($id_share)) {
            header('Location: ?url=subjects&success=3');
        } else {
            header('Location: ?url=subjects&error=3');
        }
        exit;
    }

    public function activate_allowance($id_share) {
        $subject = $this->subjectModel->findById($id_share);
        if (!$subject) {
            header('Location: ?url=subjects&error=notfound');
            exit;
        }
    
        $amount = ($subject['type_subject'] == 'karyawan') 
                   ? $subject['grade_allowance'] 
                   : $subject['default_allowance'];
    
        $currentMonth = date('Y-m-01');
        
        // Insert ke monthly_allowance
        $sql = "INSERT INTO monthly_allowance (id_share, allowance_month, amount, source, created_at) 
                VALUES (?, ?, ?, 'manual', NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $id_share, $currentMonth, $amount);
        
        if ($stmt->execute()) {
            header('Location: ?url=subjects&success=4'); // success=4: Jatah diaktifkan
        } else {
            header('Location: ?url=subjects&error=failed');
        }
        exit;
    }

    public function getUniqueDivisions() {
        $sql = "SELECT DISTINCT division 
                FROM subject 
                WHERE division IS NOT NULL 
                AND division != '' 
                ORDER BY division ASC";
        
        $result = $this->db->query($sql);
        
        if (!$result) {
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    

    
}


?>
