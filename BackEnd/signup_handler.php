<?php
// signup_handler.php
// Δέχεται το POST από το sign_up.php, ελέγχει, αποθηκεύει στη DB

session_start();
require_once 'db.php';

// Αν κάποιος μπει απευθείας στο URL (GET), πήγαινέ τον πίσω
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /Volley_app/FrontEnd/sign_up.php');
    exit;
}

// ─── 1. ΠΑΙΡΝΟΥΜΕ & ΚΑΘΑΡΙΖΟΥΜΕ ΤΑ ΔΕΔΟΜΕΝΑ ────────────────────────────────
$firstName   = trim($_POST['firstName']        ?? '');
$lastName    = trim($_POST['lastName']         ?? '');
$role        = trim($_POST['role']             ?? '');
$phone       = trim($_POST['phone']            ?? '');
$email       = trim($_POST['email']            ?? '');
$username    = trim($_POST['username']         ?? '');
$password    = $_POST['password']              ?? '';
$confirmPass = $_POST['confirmPassword']       ?? '';

// ─── 2. SERVER-SIDE VALIDATION ───────────────────────────────────────────────
// Backup του JS validation — ο server ΠΑΝΤΑ ελέγχει ξανά
$errors = [];

if (empty($firstName) || preg_match('/\d/', $firstName)) {
    $errors[] = 'Μη έγκυρο όνομα.';
}
if (empty($lastName) || preg_match('/\d/', $lastName)) {
    $errors[] = 'Μη έγκυρο επίθετο.';
}
if (!in_array($role, ['club_admin', 'referee'])) {
    $errors[] = 'Μη έγκυρος ρόλος.';
}
if (!preg_match('/^\d{10}$/', $phone)) {
    $errors[] = 'Το τηλέφωνο πρέπει να έχει ακριβώς 10 ψηφία.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Μη έγκυρη διεύθυνση email.';
}
if (empty($username) || preg_match('/\s/', $username)) {
    $errors[] = 'Το username δεν μπορεί να περιέχει κενά.';
}
if (strlen($password) < 5 || !preg_match('/[!@#$%^&*()\-_=+\[\]{};:\'",.<>?\/\\|`~]/', $password)) {
    $errors[] = 'Ο κωδικός χρειάζεται τουλάχιστον 5 χαρακτήρες και 1 σύμβολο.';
}
if ($password !== $confirmPass) {
    $errors[] = 'Οι κωδικοί δεν ταιριάζουν.';
}

// Αν υπάρχουν errors, πήγαινε πίσω στο sign_up με τα μηνύματα
if (!empty($errors)) {
    $_SESSION['signup_errors'] = $errors;
    header('Location: /Volley_app/FrontEnd/sign_up.php');
    exit;
}

// ─── 3. ΕΛΕΓΧΟΣ DUPLICATE USERNAME ──────────────────────────────────────────
// Prepared statement: ασφαλές από SQL injection
$checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($checkStmt, "s", $username);
mysqli_stmt_execute($checkStmt);
mysqli_stmt_store_result($checkStmt);

if (mysqli_stmt_num_rows($checkStmt) > 0) {
    $_SESSION['signup_errors'] = ['Το username χρησιμοποιείται ήδη.'];
    mysqli_stmt_close($checkStmt);
    header('Location: /Volley_app/FrontEnd/sign_up.php');
    exit;
}
mysqli_stmt_close($checkStmt);

// ─── 4. HASH PASSWORD & INSERT ──────────────────────────────────────────────
// password_hash: ΠΟΤΕ δεν αποθηκεύουμε plaintext password στη βάση
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$insertStmt = mysqli_prepare($conn,
    "INSERT INTO users (first_name, last_name, role, phone, email, username, password, status)
     VALUES (?, ?, ?, ?, ?, ?, ?, 'inactive')"
    // status = 'inactive': ο admin πρέπει να ενεργοποιήσει τον χρήστη
);
mysqli_stmt_bind_param($insertStmt, "sssssss",
    $firstName,
    $lastName,
    $role,
    $phone,
    $email,
    $username,
    $hashedPassword
);

if (mysqli_stmt_execute($insertStmt)) {
    // ΕΠΙΤΥΧΙΑ — πήγαινε στο login με μήνυμα
    $_SESSION['signup_success'] = 'Η εγγραφή ολοκληρώθηκε! Αναμένετε ενεργοποίηση από τον διαχειριστή.';
    mysqli_stmt_close($insertStmt);
    mysqli_close($conn);
    header('Location: /Volley_app/FrontEnd/login.php');
    exit;
} else {
    // ΑΠΟΤΥΧΙΑ
    $_SESSION['signup_errors'] = ['Σφάλμα κατά την εγγραφή. Προσπαθήστε ξανά.'];
    mysqli_stmt_close($insertStmt);
    mysqli_close($conn);
    header('Location: /Volley_app/FrontEnd/sign_up.php');
    exit;
}
?>