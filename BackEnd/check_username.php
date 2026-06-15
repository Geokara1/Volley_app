<?php
header('Content-Type: application/json'); 
require_once 'db.php';

$username = isset($_POST['username']) ? trim($_POST['username']) : '';

if (empty($username)) {
    echo json_encode(['available' => false]);
    exit;
}
require_once 'db.php';

/** @var mysqli $conn */  
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {

    echo json_encode(['available' => false]);
} else {

    echo json_encode(['available' => true]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>