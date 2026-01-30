<?php

class Controller {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 1. Ambil Limit Pagination Secara Otomatis
    protected function getLimit($default = 50) {
        $allowed = [1, 2, 3, 5, 10, 25, 50, 100, 200, 500];
        return (isset($_GET['per_page']) && in_array($_GET['per_page'], $allowed)) 
               ? (int)$_GET['per_page'] 
               : $default;
    }

    // 2. Fungsi Redirect yang Lebih Bersih
    protected function redirect($url) {
        header("Location: ?url=" . $url);
        exit;
    }

    // 3. Helper untuk Flash Messages (Alert)
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    // 4. View Loader (Opsional - Jika ingin mempersingkat include)
    protected function view($path, $data = []) {
        extract($data); // Mengubah array ['subjects' => $data] jadi variabel $subjects
        include "../app/views/" . $path . ".php";
    }
}