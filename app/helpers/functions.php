<?php
function checkSessionExpiry() {
    // Cek apakah session sudah 8 jam
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity'] > 28800)) {
        session_unset();
        session_destroy();
        header('Location: ?url=login&expired=1');
        exit;
    }
    
    // Update last activity
    $_SESSION['last_activity'] = time();
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? '',
            'role' => $_SESSION['role'] ?? ''
        ];
    }
    return null;
}

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Calculate pagination data
 * 
 * @param int $totalRecords Total number of records
 * @param int $currentPage  Current page number
 * @param int $perPage      Records per page
 * @return array ['offset' => int, 'limit' => int, 'totalPages' => int, 'currentPage' => int]
 */
function getPagination($totalRecords, $currentPage = 1, $perPage = 50) {
    $currentPage = max(1, (int)$currentPage);
    $totalPages = ceil($totalRecords / $perPage);
    $currentPage = min($currentPage, max(1, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'offset' => $offset,
        'limit' => $perPage,
        'totalPages' => $totalPages,
        'currentPage' => $currentPage,
        'totalRecords' => $totalRecords,
        'perPage' => $perPage
    ];
}

?>
