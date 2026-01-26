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

// Panggil di awal setiap page
checkSessionExpiry();
?>
