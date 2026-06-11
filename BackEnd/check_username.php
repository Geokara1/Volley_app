<?php
// check_username.php
// Καλείται από το AJAX στο signup_validation.js
// Ελέγχει αν το username υπάρχει ήδη στη βάση

header('Content-Type: application/json'); // η απάντηση είναι JSON
require_once 'db.php';

$username = isset($_POST['username']) ? trim($_POST['username']) : '';

if (empty($username)) {
    echo json_encode(['available' => false]);
    exit;
}
require_once 'db.php';

/** @var mysqli $conn */  // ← λέει στο VS Code "εμπιστέψου με, $conn είναι mysqli"
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
// Ασφαλής query με prepared statement
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    // Username υπάρχει ήδη
    echo json_encode(['available' => false]);
} else {
    // Username είναι διαθέσιμο
    echo json_encode(['available' => true]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>