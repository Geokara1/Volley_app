<?php
// login_handler.php
// Δέχεται POST από login.php, ελέγχει credentials, δημιουργεί session

session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// ─── 1. ΠΑΙΡΝΟΥΜΕ ΤΑ ΔΕΔΟΜΕΝΑ ────────────────────────────────────────────────
$username = trim($_POST['username'] ?? '');
$password = $_POST['password']      ?? '';

if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Παρακαλώ συμπληρώστε όλα τα πεδία.';
    header('Location: login.php');
    exit;
}

// ─── 2. ΑΝΑΖΗΤΗΣΗ USER ΣΤΗ ΒΑΣΗs ─────────────────────────────────────────────
$stmt = mysqli_prepare($conn,
    "SELECT id, first_name, last_name, username, password, role, status
     FROM users WHERE username = ?"
);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// ─── 3. ΕΛΕΓΧΟΣ ΥΠΑΡΞΗΣ ──────────────────────────────────────────────────────
// Ίδιο μήνυμα για "δεν βρέθηκε" ΚΑΙ "λάθος password" — δεν αποκαλύπτουμε αν υπάρχει το username
if (!$user) {
    $_SESSION['login_error'] = 'Λάθος username ή password.';
    header('Location: login.php');
    exit;
}

// ─── 4. ΕΛΕΓΧΟΣ STATUS ───────────────────────────────────────────────────────
// Ο χρήστης έχει εγγραφεί αλλά δεν έχει ενεργοποιηθεί ακόμα από τον admin
if ($user['status'] === 'inactive') {
    $_SESSION['login_error'] = 'Ο λογαριασμός σας δεν έχει ενεργοποιηθεί ακόμα. Αναμένετε επικύρωση από τον διαχειριστή.';
    header('Location: login.php');
    exit;
}

// ─── 5. ΕΛΕΓΧΟΣ PASSWORD ─────────────────────────────────────────────────────
// password_verify: συγκρίνει το plaintext με το hash της βάσης
if (!password_verify($password, $user['password'])) {
    $_SESSION['login_error'] = 'Λάθος username ή password.';
    header('Location: login.php');
    exit;
}

// ─── 6. ΔΗΜΙΟΥΡΓΙΑ SESSION ───────────────────────────────────────────────────
// Αποθηκεύουμε μόνο ό,τι χρειαζόμαστε — ΟΧΙ το password hash
$_SESSION['logged_in']  = true;
$_SESSION['user_id']    = $user['id'];
$_SESSION['username']   = $user['username'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['role']       = $user['role'];

mysqli_close($conn);

// ─── 7. REDIRECT ΑΝΑΛΟΓΑ ΜΕ ΡΟΛΟ ─────────────────────────────────────────────
switch ($user['role']) {
    case 'admin':
        header('Location: admin_panel.php');
        break;
    case 'club_admin':
    case 'referee':
    default:
        header('Location: index.php');
        break;
}
exit;
?>