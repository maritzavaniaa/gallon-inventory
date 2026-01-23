<?php
require_once '../app/models/Subject.php';
require_once '../app/middleware/AuthMiddleware.php';

class SubjectController {
    private $subjectModel;
    
    public function __construct($db) {
        AuthMiddleware::checkAdmin();
        $this->subjectModel = new Subject($db);
    }
    
    public function index() {
        $subjects = $this->subjectModel->getAll();
        require_once '../app/views/subjects/index.php';
    }
    
    public function create() {
        require_once '../app/views/subjects/create.php';
    }
    
    public function store() {
        if ($_POST) {
            $data = [
                'id_share' => $_POST['id_share'],
                'type_subject' => $_POST['type_subject'],
                'name_subject' => $_POST['name_subject'],
                'division' => $_POST['division'] ?? '',
                'status' => $_POST['status'],
                'inactive_note' => $_POST['inactive_note'] ?? '',
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
        require_once '../app/views/subjects/edit.php';
    }
    
    public function update($id_share) {
        if ($_POST) {
            $data = $_POST;
            $data['id_share'] = $id_share;
            
            if ($this->subjectModel->update($data)) {
                header('Location: ?url=subjects&success=2');
            } else {
                header('Location: ?url=subjects&error=2');
            }
        }
        exit;
    }
    
    public function destroy($id_share) {
        if ($this->subjectModel->delete($id_share)) {
            header('Location: ?url=subjects&success=3');
        } else {
            header('Location: ?url=subjects&error=3');
        }
        exit;
    }
}
?>
