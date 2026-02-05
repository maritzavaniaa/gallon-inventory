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

    public function export() {
        $filters = [
            'search'     => $_GET['search'] ?? '',
            'date_start' => $_GET['date_start'] ?? '',
            'date_end'   => $_GET['date_end'] ?? '',
            'division'   => $_GET['division'] ?? '',
            'limit'      => 10000, // Ambil semua data tanpa batas limit halaman
            'page'       => 1
        ];
    
        require_once '../app/models/ReportOut.php';
        $model = new ReportOut($this->db);
        $reports = $model->getOutReports($filters, 0);
    
        // Set header agar browser mendownload file CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Laporan_Distribusi_'.date('Ymd').'.csv');
    
        // Buka "file" di output stream
        $output = fopen('php://output', 'w');

        fputcsv($output, ['NO', 'TANGGAL', 'ID SHARE', 'NAMA', 'DIVISI', 'GRADE', 'KUOTA', 'JUMLAH AMBIL']);
    
        $no = 1;
        foreach ($reports as $r) {
            fputcsv($output, [
                $no++,
                date('d/m/Y', strtotime($r['date_tsx'])),
                $r['id_share'],
                $r['name_subject'],
                $r['division'] ?: '-',
                ($r['type_subject'] === 'karyawan') ? ($r['name_grade'] ?? '-') : '-',
                $r['amount'] ?? 0, // Mengambil amount dari monthly_allowance
                $r['quantity']
            ]);
        }
    
        fclose($output);
        exit;
    }
}
?>