<?php

require_once '../app/controllers/Controller.php';

class ReportOutController extends Controller {
    public function index() {
        $limitRaw = $_GET['limit'] ?? 10;
        $limit = (empty($limitRaw) || $limitRaw === 'all') ? 500 : (int)$limitRaw;

        $filters = [
            'search'     => $_GET['search'] ?? '',
            'date_start' => $_GET['date_start'] ?? '',
            'date_end'   => $_GET['date_end'] ?? '',
            'division'   => $_GET['division'] ?? '',
            'limit'      => $limit,
            'page'       => isset($_GET['page']) ? (int)$_GET['page'] : 1
        ];

        $offset = ($filters['page'] - 1) * $filters['limit'];

        require_once '../app/models/ReportOut.php';
        $model = new ReportOut($this->db);
        
        $reports = $model->getOutReports($filters, $offset);
        $totalData = $model->countOutReports($filters);
        $divisions = $model->getDistinctDivisions();
        
        $totalPages = ceil($totalData / $filters['limit']);

        require_once '../app/views/reports/out/index.php';
    }
}
?>