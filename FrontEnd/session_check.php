<?php
// session_check.php
// Κάνε include αυτό στην ΑΡΧΗ κάθε προστατευμένης σελίδας
// ΣΗΜΑΝΤΙΚΟ: session_start() πρέπει να έχει κληθεί πριν το include

// ── Ελέγχει αν ο χρήστης είναι logged in ────────────────────────────────────
function isLoggedIn() {
    return !empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// ── Αν δεν είναι logged in → redirect στο login ──────────────────────────────
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['login_error'] = 'Πρέπει να συνδεθείτε για να δείτε αυτή τη σελίδα.';
        header('Location: login.php');
        exit;
    }
}

// ── Αν δεν έχει τον σωστό ρόλο → redirect στο index ─────────────────────────
function requireRole($requiredRole) {
    requireLogin(); // πρώτα: είναι logged in;
    if ($_SESSION['role'] !== $requiredRole) {
        header('Location: index.php');
        exit;
    }
}

// ── Βοηθητική: επιστρέφει τον ρόλο του τρέχοντα χρήστη (ή null) ─────────────
function getCurrentRole() {
    return $_SESSION['role'] ?? null;
}
?>