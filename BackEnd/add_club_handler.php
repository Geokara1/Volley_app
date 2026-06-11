<?php
// BackEnd/add_club_handler.php
session_start();
require_once 'db.php';           // ίδιος φάκελος (BackEnd/)
require_once 'session_check.php'; // ίδιος φάκελος (BackEnd/)
requireRole('club_admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /Volley_app/FrontEnd/add_club.php');
    exit;
}

// ─── 1. ΕΛΕΓΧΟΣ: ο club_admin έχει ήδη σύλλογο; ────────────────────────────
$adminId = $_SESSION['user_id'];

$checkStmt = mysqli_prepare($conn, "SELECT id FROM team_profile WHERE admin_id = ?");
mysqli_stmt_bind_param($checkStmt, "i", $adminId);
mysqli_stmt_execute($checkStmt);
mysqli_stmt_store_result($checkStmt);

if (mysqli_stmt_num_rows($checkStmt) > 0) {
    $_SESSION['club_error'] = 'Έχεις ήδη καταχωρήσει έναν σύλλογο.';
    mysqli_stmt_close($checkStmt);
    header('Location: /Volley_app/FrontEnd/add_club.php');
    exit;
}
mysqli_stmt_close($checkStmt);

// ─── 2. SANITIZE CLUB INFO ───────────────────────────────────────────────────
$teamName         = trim($_POST['teamName']         ?? '');
$teamSite         = trim($_POST['teamSite']         ?? '');
$teamVideo        = trim($_POST['teamVideo']         ?? '');
$coachName        = trim($_POST['coachName']         ?? '');
$trainerName      = trim($_POST['trainerName']       ?? '');
$physioName       = trim($_POST['physioName']        ?? '');
$caretakerName    = trim($_POST['caretakerName']     ?? '');
$statisticianName = trim($_POST['statisticianName']  ?? '');

if (empty($teamName) || empty($coachName)) {
    $_SESSION['club_error'] = 'Το όνομα ομάδας και ο προπονητής είναι υποχρεωτικά.';
    header('Location: /Volley_app/FrontEnd/add_club.php');
    exit;
}

// ─── 3. FILE UPLOADS ─────────────────────────────────────────────────────────
// Αποθηκεύονται στο BackEnd/uploads/ (ίδιος φάκελος με τον handler)
$uploadDir    = __DIR__ . '/uploads/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$logoPath     = '';
$photoPath    = '';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Upload logo
if (isset($_FILES['teamLogo']) && $_FILES['teamLogo']['error'] === 0) {
    if (!in_array($_FILES['teamLogo']['type'], $allowedTypes)) {
        $_SESSION['club_error'] = 'Μόνο εικόνες επιτρέπονται για το λογότυπο (jpg, png, gif).';
        header('Location: /Volley_app/FrontEnd/add_club.php');
        exit;
    }
    $ext      = pathinfo($_FILES['teamLogo']['name'], PATHINFO_EXTENSION);
    $filename = 'logo_' . $adminId . '_' . time() . '.' . $ext;
    move_uploaded_file($_FILES['teamLogo']['tmp_name'], $uploadDir . $filename);
    $logoPath = 'uploads/' . $filename; // αποθηκεύεται έτσι στη DB
}

// Upload photo
if (isset($_FILES['teamPhoto']) && $_FILES['teamPhoto']['error'] === 0) {
    if (!in_array($_FILES['teamPhoto']['type'], $allowedTypes)) {
        $_SESSION['club_error'] = 'Μόνο εικόνες επιτρέπονται για τη φωτογραφία (jpg, png, gif).';
        header('Location: /Volley_app/FrontEnd/add_club.php');
        exit;
    }
    $ext      = pathinfo($_FILES['teamPhoto']['name'], PATHINFO_EXTENSION);
    $filename = 'photo_' . $adminId . '_' . time() . '.' . $ext;
    move_uploaded_file($_FILES['teamPhoto']['tmp_name'], $uploadDir . $filename);
    $photoPath = 'uploads/' . $filename;
}

// ─── 4. INSERT team_profile ──────────────────────────────────────────────────
$insertClub = mysqli_prepare($conn,
    "INSERT INTO team_profile
        (team_name, team_site, team_logo, team_photo, team_video,
         coach_name, trainer_name, physio_name, caretaker_name,
         statistician_name, admin_id)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param($insertClub, "ssssssssssi",
    $teamName, $teamSite, $logoPath, $photoPath, $teamVideo,
    $coachName, $trainerName, $physioName, $caretakerName,
    $statisticianName, $adminId
);

if (!mysqli_stmt_execute($insertClub)) {
    $_SESSION['club_error'] = 'Σφάλμα κατά την αποθήκευση του συλλόγου.';
    header('Location: /Volley_app/FrontEnd/add_club.php');
    exit;
}

$teamId = mysqli_insert_id($conn);
mysqli_stmt_close($insertClub);

// ─── 5. INSERT 12 PLAYERS ────────────────────────────────────────────────────
$insertPlayer = mysqli_prepare($conn,
    "INSERT INTO player (team_id, jersey_number, full_name, position, height, date_of_birth)
     VALUES (?, ?, ?, ?, ?, ?)"
);

for ($i = 1; $i <= 12; $i++) {
    $jerseyNum = intval($_POST["p{$i}_num"]      ?? 0);
    $fullName  = trim($_POST["p{$i}_name"]       ?? '');
    $position  = trim($_POST["p{$i}_pos"]        ?? '');
    $height    = floatval($_POST["p{$i}_height"] ?? 0);
    $dob       = trim($_POST["p{$i}_dob"]        ?? '');

    if (empty($fullName) || empty($position)) continue;

    mysqli_stmt_bind_param($insertPlayer, "iissds",
        $teamId, $jerseyNum, $fullName, $position, $height, $dob
    );
    mysqli_stmt_execute($insertPlayer);
}
mysqli_stmt_close($insertPlayer);
mysqli_close($conn);

// ─── 6. ΕΠΙΤΥΧΙΑ ─────────────────────────────────────────────────────────────
$_SESSION['club_success'] = 'Ο σύλλογος "' . htmlspecialchars($teamName) . '" καταχωρήθηκε επιτυχώς!';
header('Location: /Volley_app/FrontEnd/clubs.php');
exit;
?>