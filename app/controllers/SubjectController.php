<?php
require_once '../app/models/Subject.php';
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/helpers/functions.php';

class SubjectController {
    private $subjectModel;
    private $db;
    
    public function __construct($db) {
        AuthMiddleware::checkAdmin();
        $this->db = $db;
        $this->subjectModel = new Subject($db);
    }
    
    public function index() {
        // 1. Auth check (Gunakan AuthMiddleware yang sudah ada saja)
        AuthMiddleware::checkAdmin();
        
        // 2. Get filters
        $filters = [
            'search'   => $_GET['search'] ?? '',
            'type'     => $_GET['type'] ?? '',
            'status'   => $_GET['status'] ?? '',
            'division' => $_GET['division'] ?? ''
        ];
    
        $hasFilters = !empty($filters['search']) || !empty($filters['type']) || 
                      !empty($filters['status']) || !empty($filters['division']);
    
        // 3. Pagination setup
        $perPage = 1;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) $currentPage = 1;
        
        $offset = ($currentPage - 1) * $perPage;
        
        // 4. Get data (Panggil method dari Model)
        $totalRecords = $this->subjectModel->countAll($filters);
        
        // Pastikan fungsi getPagination() tersedia di functions.php
        $pagination = getPagination($totalRecords, $currentPage, $perPage);
        
        // Ambil data yang sudah dipagination
        $subjects = $this->subjectModel->getAllPaginated($filters, $pagination['offset'], $pagination['limit']);
        
        // Error tadi karena ini: harusnya panggil dari model
        $divisions = $this->subjectModel->getUniqueDivisions();
        
        // 5. Load view
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
                'division' => trim($_POST['division'] ?? ''),
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
        if ($_POST) {
            $data = [
                'id_share' => $id_share,
                'type_subject' => $_POST['type_subject'],
                'name_subject' => trim($_POST['name_subject']),
                'division' => trim($_POST['division'] ?? ''),
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
        }
        exit;
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
