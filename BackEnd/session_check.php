<?php
function isLoggedIn() {
    return !empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['login_error'] = 'Πρέπει να συνδεθείτε για να δείτε αυτή τη σελίδα.';
        header('Location: login.php');
        exit;
    }
}

function requireRole($requiredRole) {
    requireLogin(); 
    if ($_SESSION['role'] !== $requiredRole) {
        header('Location: index.php');
        exit;
    }
}

function getCurrentRole() {
    return $_SESSION['role'] ?? null;
}
?>