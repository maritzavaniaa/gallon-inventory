<?php
require_once '../app/models/Grade.php';
require_once '../app/middleware/AuthMiddleware.php';

class GradeController {
    private $gradeModel;
    
    public function __construct($db) {
        AuthMiddleware::checkAdmin();
        $this->gradeModel = new Grade($db);
    }
    
    public function index() {
        $grades = $this->gradeModel->getAll();
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
}
?>
