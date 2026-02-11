<?php

require_once '../app/controllers/Controller.php';

class ReportOutController extends Controller {
    public function index() {
        $limitRaw = $_GET['limit'] ?? 10;
        $limit = (empty($limitRaw) || $limitRaw === 'all') ? 500 : (int)$limitRaw;

        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

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

        // Summary cards components
        $summaryData = $model->getOutSummary($filters);

        $totalQty = $summaryData['total_qty'] ?? 0;
        $totalPenerima = $summaryData['total_penerima'] ?? 0;
        
        $rataRata = ($totalPenerima > 0) ? ($totalQty / $totalPenerima) : 0;
        
        $cards = [
            [
                'label' => 'Total Gallon Keluar',
                'value' => $totalQty,
                'unit'  => 'Gallons',
                'color' => 'text-red-600',
                'subtext' => 'Total pengambilan periode ini'
            ],
            [
                'label' => 'Total Penerima',
                'value' => $totalPenerima,
                'unit'  => 'Orang',
                'color' => 'text-blue-600',
                'subtext' => 'Jumlah individu unik yang mengambil'
            ],
            [
                'label' => 'Rata-rata Pengambilan',
                'value' => $rataRata,
                'unit'  => 'Gallon/Orang',
                'color' => 'text-green-600',
                'subtext' => 'Rasio gallon per individu'
            ]
        ];

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
                $r['amount'] ?? 0, 
                $r['quantity']
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
    
            // 1. Ambil password asli (hashed) dari database
            $stmt = $this->db->prepare("SELECT password_hash FROM app_user WHERE id_user = ?");
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
    
            // 2. Verifikasi Password
            if ($user && password_verify($inputPassword, $user['password_hash'])) {
                require_once '../app/models/ReportOut.php';
                $model = new ReportOut($this->db);
                
                if ($model->softDelete($id)) {
                    // Berhasil: Redirect dengan kode success (3 = Berhasil Batal)
                    header('Location: ?url=reports/out&success=3');
                } else {
                    header('Location: ?url=reports/out&error=system');
                }
            } else {
                // Password Salah
                header('Location: ?url=reports/out&error=wrong_password');
            }
            exit;
        }
        
        // Jika diakses via GET (langsung ke URL), lempar balik ke laporan
        header('Location: ?url=reports/out');
        exit;
    }
}
?>