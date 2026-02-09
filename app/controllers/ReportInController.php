<?php
require_once '../app/controllers/Controller.php';

class ReportInController extends Controller {
    public function index() {
        // Atur limit & pagination mirip report out
        $limitRaw = $_GET['limit'] ?? 10;
        $limit = (empty($limitRaw) || $limitRaw === 'all') ? 500 : (int)$limitRaw;
        
        $filters = [
            'search'     => $_GET['search'] ?? '',
            'date_start' => $_GET['date_start'] ?? '',
            'date_end'   => $_GET['date_end'] ?? '',
            'limit'      => $limit,
            'page'       => isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1
        ];

        $offset = ($filters['page'] - 1) * $filters['limit'];

        require_once '../app/models/ReportIn.php';
        require_once '../app/models/Transaction.php';
        
        $modelIn = new ReportIn($this->db);
        $modelTsx = new Transaction($this->db);

        // Ambil data laporan
        $reports = $modelIn->getInReports($filters, $offset);
        $totalData = $modelIn->countInReports($filters);
        $totalPages = ceil($totalData / $filters['limit']);

        // Persiapkan Summary Cards (Warna Hijau/Emerald untuk In)
        $modelIn = new ReportIn($this->db);
        $modelTsx = new Transaction($this->db);

        $totalInFiltered = $modelIn->getInSummary($filters);

        $stock = $modelTsx->getCurrentStock();

        $cards = [
            [
                'label' => 'Stok Internal',
                'value' => $stock['remaining'],
                'unit'  => 'Gallons',
                'color' => 'text-blue-800',
                'subtext' => 'Total stok saat ini'
            ],
            [
                'label' => 'Total Masuk Periode Ini', 
                'value' => $totalInFiltered,
                'unit'  => 'Gallons',
                'color' => 'text-emerald-600',
                'subtext' => 'Berdasarkan filter tanggal'
            ]
        ];

        require_once '../app/views/reports/in/index.php';
    }

    public function export() {
        $filters = [
            'search'     => $_GET['search'] ?? '',
            'date_start' => $_GET['date_start'] ?? '',
            'date_end'   => $_GET['date_end'] ?? '',
            'limit'      => 10000, 
            'page'       => 1
        ];
    
        require_once '../app/models/ReportIn.php';
        $model = new ReportIn($this->db);
        
        $reports = $model->getInReports($filters, 0, 10000);
    
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Laporan_Intern_Masuk_'.date('Ymd').'.csv');
    
        $output = fopen('php://output', 'w');

        fputcsv($output, ['NO', 'TANGGAL', 'REF. DOKUMEN IM', 'JUMLAH MASUK', 'CATATAN', 'ADMIN']);
    
        $no = 1;
        foreach ($reports as $r) {
            fputcsv($output, [
                $no++,
                date('d/m/Y', strtotime($r['date_tsx'])),
                $r['reference_doc'] ?: '-',
                $r['quantity'],
                $r['notes'] ?: '-',
                $r['admin_name']
            ]);
        }
    
        fclose($output);
        exit;
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputPassword = $_POST['admin_password'] ?? '';
            $adminId = $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;
            
            if (!$adminId) {
                header('Location: ?url=login');
                exit;
            }

            // 1. Ambil password admin dari DB
            $stmt = $this->db->prepare("SELECT password_hash FROM app_user WHERE id_user = ?");
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
    
            // 2. Verifikasi
            if ($user && password_verify($inputPassword, $user['password_hash'])) {
                require_once '../app/models/ReportIn.php';
                $model = new ReportIn($this->db);
                
                if ($model->softDelete($id)) {
                    header('Location: ?url=reports/in&success=3');
                } else {
                    header('Location: ?url=reports/in&error=system');
                }
            } else {
                header('Location: ?url=reports/in&error=wrong_password');
            }
            exit;
        }
        header('Location: ?url=reports/in');
        exit;
    }
}