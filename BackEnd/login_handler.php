<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /Volley_app/FrontEnd/login.php');
    exit;
}


$username = trim($_POST['username'] ?? '');
$password = $_POST['password']      ?? '';

if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Παρακαλώ συμπληρώστε όλα τα πεδία.';
    header('Location: /Volley_app/FrontEnd/login.php');
    exit;
}


$stmt = mysqli_prepare($conn,
    "SELECT id, first_name, last_name, username, password, role, status
     FROM users WHERE username = ?"
);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$user) {
    $_SESSION['login_error'] = 'Λάθος username ή password.';
    header('Location: /Volley_app/FrontEnd/login.php');
    exit;
}

if ($user['status'] === 'pending') {
    $_SESSION['login_error'] = 'Ο λογαριασμός σας δεν έχει ενεργοποιηθεί ακόμα. Αναμένετε επικύρωση από τον διαχειριστή.';
    header('Location: /Volley_app/FrontEnd/login.php');
    exit;
}

if (!password_verify($password, $user['password'])) {
    $_SESSION['login_error'] = 'Λάθος username ή password.';
    header('Location: /Volley_app/FrontEnd/login.php');
    exit;
}

$_SESSION['logged_in']  = true;
$_SESSION['user_id']    = $user['id'];
$_SESSION['username']   = $user['username'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['role']       = $user['role'];

mysqli_close($conn);

switch ($user['role']) {
    case 'admin':
        header('Location: /Volley_app/FrontEnd/admin_panel.php');
        break;
    case 'club_admin':
    case 'referee':
    default:
        header('Location: /Volley_app/FrontEnd/index.php');
        break;
}
exit;
?>