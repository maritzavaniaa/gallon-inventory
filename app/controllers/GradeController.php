<?php
require_once 'Controller.php';
require_once '../app/models/Grade.php';
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/helpers/functions.php';

class GradeController extends Controller{
    private $gradeModel;

    public function __construct($db) {
        parent::__construct($db);
        AuthMiddleware::checkAdmin();
        $this->gradeModel = new Grade($db);
    }

    public function index() {
        $filters = [
            'search' => $_GET['search'] ?? ''
        ];

        $grades = $this->gradeModel->getAll($filters);
        require_once '../app/views/grades/index.php';
    }
    
    public function store() {
        if ($_POST) {
            $name_grade = trim($_POST['name_grade']);
            $default_allowance = (int)$_POST['default_allowance'];
            
            if ($this->gradeModel->create($name_grade, $default_allowance)) {
                header('Location: ?url=grades&success=1');
            } else {
                header('Location: ?url=grades&error=1');
            }
        }
        exit;
    }

    public function create() {
        require_once '../app/views/grades/create.php';
    }
    
    public function update($id) {
        if ($_POST) {
            $name_grade = trim($_POST['name_grade']);
            $default_allowance = (int)$_POST['default_allowance'];
            
            if ($this->gradeModel->update($id, $name_grade, $default_allowance)) {
                header('Location: ?url=grades&success=2');
            } else {
                header('Location: ?url=grades&error=2');
            }
        }
        exit;
    }

    public function edit($id) {
        $grade = $this->gradeModel->findById($id);
        $grades = $this->gradeModel->getAll();
        
        // CEK apakah grade sedang digunakan oleh karyawan
        $sql = "SELECT COUNT(*) as total FROM subject WHERE id_grade = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $isInUse = $result['total'] > 0; // TRUE jika ada karyawan pakai grade ini
        
        require_once '../app/views/grades/edit.php';
    }
    
    
    public function delete($id) {
        // Cek apakah grade sedang digunakan
        $sql = "SELECT COUNT(*) as total FROM subject WHERE id_grade = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result['total'] > 0) {
            header('Location: ?url=grades&error=in_use');
            exit;
        }
        
        if ($this->gradeModel->delete($id)) {
            header('Location: ?url=grades&success=3');
        } else {
            header('Location: ?url=grades&error=3');
        }
        exit;
    }
}
?>
